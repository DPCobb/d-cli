<?php

namespace App\Core;

use App\Interfaces\Command_Container_Interface;
use App\IO\Output;
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

    public function alias(array $alias, string $action)
    {
        foreach ($alias as $value) {
            $this->set($value, $action);
        }
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

            // passed a class @ method ex: App\Commands\Hello\Test@helloWorld
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

        if (strpos($command, '-')) {
            $parts = explode('-', $command);
            foreach ($parts as &$v) {
                $v = ucwords($v);
            }

            $command = implode('_', $parts);
        }

        $command_class = "Default_Handler";
        
        if ($this->Command_Container->has('sub_command')) {
            $command_class = ucwords($this->Command_Container->get('sub_command'));

            if (strpos($command_class, '-')) {
                $parts = explode('-', $command_class);
                foreach ($parts as &$v) {
                    $v = ucwords($v);
                }
    
                $command_class = implode('_', $parts);
            }
        }

        $namespace = sprintf("App\Commands\%s\%s", $command, $command_class);

        $className = $namespace;

        if (class_exists($className)) {
            $c = new $className();
            $c->handle();
            return;
        }

        Output::error("Command Not Found!");
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
