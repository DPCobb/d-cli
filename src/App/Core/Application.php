<?php

namespace dcli\App\Core;

use dcli\App\Interfaces\Command_Container_Interface;
use Error;

class Application
{
    /**
     * command_path
     *
     * @var string
     */
    public string $command_path;
    public Command_Container_Interface $Command_Container;
    public static ?Application $instance = null;

    public function __construct(Command_Container_Interface $Command_Container)
    {
        $this->Command_Container = $Command_Container;
        $this->command_path = "App/Commands";
        $this->commands = [];
    }

    public static function load(Command_Container_Interface $Command_Container)
    {
        if (is_null(self::$instance)) {
            self::$instance = new Application($Command_Container);
        }

        return self::$instance;
    }

    public static function read()
    {
        return self::$instance ?? null;
    }

    public static function getCommandContainer(): Command_Container_Interface
    {
        if (is_null(self::$instance)) {
            throw new Error('Application is not yet instantiated.');
        }

        return self::$instance->Command_Container;
    }

    public function set(string $command_name, $action)
    {
        $this->commands[$command_name] = $action;
    }

    public function run()
    {
        $command = $this->Command_Container->get('command');

        if (!empty($this->commands[$command])) {
            $action = $this->commands[$command];

            // passed a function in
            if (is_callable($action)) {
                call_user_func($action);
                return;
            }

            // passed a class @ method ex: dcli\App\Commands\Hello\Test@helloWorld
            if (strpos($action, '@') !== false) {
                $parts = explode('@', $action);
                $c = new $parts[0];
                if (method_exists($c, $parts[1])) {
                    call_user_func([$c, $parts[1]]);
                }
                return;
            }
        }

        // not explicitly set, find in commands dir
        $this->findCommand();
    }

    public function findCommand()
    {
        $command = ucwords($this->Command_Container->get('command'));

        $command_class = "Default_Handler";
        
        if ($this->Command_Container->has('sub_command')) {
            $command_class = ucwords($this->Command_Container->get('sub_command'));
        }

        $namespace = sprintf("App\Commands\%s\%s", $command, $command_class);

        $className = $namespace;

        $file = $this->command_path . "/" . $command . "/" . $command_class . ".php";

        if (file_exists($file)) {
            require_once $file;
            $c = new $className();
            $c->handle();
            return;
        }

        echo "Command Not Found!";
        return;
    }

    public function __get($arg)
    {
        if (property_exists($this->Command_Container, $arg)) {
            return $this->Command_Container->$arg;
        }

        return null;
    }

    public function __call($arg, $params)
    {
        if (method_exists($this->Command_Container, $arg)) {
            return call_user_func_array([$this->Command_Container, $arg], $params);
        }

        return null;
    }
}
