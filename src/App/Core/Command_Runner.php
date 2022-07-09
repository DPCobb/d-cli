<?php
namespace App\Core;

use App\Core\Command_Container;
use App\Interfaces\Command_Handler_Interface;
use ReflectionClass;
use Exception;

class Command_Runner
{
    /**
     * Construct
     *
     * @param Command_Container $Command_Container
     * @param string $class_name
     */
    public function __construct(Command_Container $Command_Container, string $class_name)
    {
        $this->class_name = $class_name;
        $this->Command_Container = $Command_Container;
    }

    /**
     * Attempt to run our command
     *
     * @param string $method
     * @return void
     * @throws Exception
     */
    public function run(string $method = ''): void {
        $item = new ReflectionClass($this->class_name);

        // Get flags and arguments for processing
        $flags = $item->hasProperty('flags') ? $this->Command_Container->Environment->flags : null;
        $arguments = $item->hasProperty('arguments') ? $this->Command_Container->Environment->arguments : null;
        $arguments_present = array_keys($arguments);

        $constructor = $item->getConstructor();
        $constructor_params = $constructor->getParameters();

        $params = []; // eventually we should dependency inject other requirements

        // inject Command_Container into constructor if needed
        foreach($constructor_params as $param) {
            $param_name = $param->getClass()->name;
            if ($param_name === "App\\Core\\Command_Container") {
                $params[$param->name] = $this->Command_Container;
            }
        }

        // create instance
        if (empty($params)) {
            $instance = $item->newInstance();
        } else {
            $instance = $item->newInstanceArgs($params);
        }

        // Make sure we know what this is
        if (!$instance instanceof Command_Handler_Interface) {
            throw new Exception("Class is not an instance of Command_Handler_Interface for command {$this->Command_Container->Environment->command}");
        }

        // Get flags and arguments from instance
        $allowed_flags = $instance->flags ?? [];
        $required_arguments = $instance->required_arguments ?? [];
        
        // Validate
        $this->validateFlagsAreAllowed($flags, $allowed_flags);
        $this->validateRequiredArguments($required_arguments, $arguments_present);

        // Set values
        $instance = $this->setFlags($flags, $allowed_flags, $instance);
        $instance = $this->setArguments($arguments_present, $arguments, $instance);

        // inject the command container if property is set
        if ($item->hasProperty('Command_Container')) {
            $instance->Command_Container = $this->Command_Container;
        }

        // Handle
        if (empty($method)) {
            $instance->handle();
            return;
        }

        if ($item->hasMethod($method)) {
            call_user_func([$instance, $method]);
            return;
        }

        throw new Exception("Unknown handler passed for command {$this->Command_Container->Environment->command} " . print_r($this->Command_Container, true));

    }

    /**
     * Validates all of the passed flags are allowed
     *
     * @param array $flags
     * @param array $allowed_flags
     * @return void
     * @throws Exception
     */
    public function validateFlagsAreAllowed(array $flags, array $allowed_flags):void {
        // Throw exception if we have unknown flags passed
        if (!is_null($flags)) {
            $processed_flags = array_diff($flags, $allowed_flags);
            if (!empty($processed_flags)) {
                throw new Exception("Unknown flags passed to command {$this->Command_Container->Environment->command}: " . implode(',', $processed_flags));
            }
        }
    }

    /**
     * Validate all the required arguments are present
     *
     * @param array $required_arguments
     * @param array $arguments_present
     * @return void
     * @throws Exception
     */
    public function validateRequiredArguments(array $required_arguments, array $arguments_present):void {
        // Throw exception if we are missing required arguments
        if (!empty($required_arguments)) {
            $processed_arguments = array_diff($required_arguments, $arguments_present);
            if (!empty($processed_arguments)) {
                throw new Exception("Missing required arguments for command {$this->Command_Container->Environment->command}: " . implode(',', $processed_arguments));
            }
        }
    }

    /**
     * Set our flag values into our command handler
     *
     * @param array $flags
     * @param array $allowed_flags
     * @param Command_Handler_Interface $instance
     * @return Command_Handler_Interface
     */
    public function setFlags(array $flags, array $allowed_flags, Command_Handler_Interface $instance): Command_Handler_Interface {
        $false_flags = array_diff($allowed_flags, $flags);
        foreach ($flags as $flag) {
            $instance->{$flag} = true;
        }

        foreach ($false_flags as $flag) {
            $instance->{$flag} = false;
        }

        return $instance;
    }

    /**
     * Sets our arguments into our handler
     *
     * @param array $arguments_present
     * @param array $arguments
     * @param Command_Handler_Interface $instance
     * @return Command_Handler_Interface
     */
    public function setArguments(array $arguments_present, array $arguments, Command_Handler_Interface $instance): Command_Handler_Interface {

        $missing_arguments = array_diff($instance->arguments, $arguments_present);
        
        foreach($arguments as $argument => $value) {
            $instance->{$argument} = $value;
        }

        foreach ($missing_arguments as $argument) {
            $instance->{$argument} = null;
        }

        return $instance;
    }
}