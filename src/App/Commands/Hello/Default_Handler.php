<?php

namespace App\Commands\Hello;

use App\Interfaces\Command_Handler_Interface;

class Default_Handler implements Command_Handler_Interface
{
    /**
     * flags
     *
     * These are the flags allowed to be used with this command
     *
     * @var array
     */
    public array $flags = ['dryRun', 'd'];

    /**
     * parameters
     *
     * The parameters this command accepts
     *
     * @var array
     */
    public array $parameters = ['name'];

    /**
     * required_parameters
     *
     * Any parameters this command requires to be set
     *
     * @var array
     */
    public array $required_parameters = ['name'];

    public function handle()
    {
        echo "Hello World";
    }

    public function world()
    {
        echo "World Hello";
    }
}
