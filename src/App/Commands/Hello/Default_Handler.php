<?php
namespace App\Commands\Hello;

use App\Interfaces\Command_Handler_Interface;
use App\Core\Event_Handler;
use App\Commands\Hello\Hello_Event;
use App\IO\Output;

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

    public function __construct()
    {
        $this->ev = new Event_Handler;
        $this->ev->subscribe('hello-world', new Hello_Event);
        $this->ev->subscribe('goodbye-world', new Hello_Event);
        $this->ev->subscribe('goodbye-world', new Hello_Event, 'test');
    }

    public function handle()
    {
        Output::message('Hello World');
        $this->ev->dispatch('hello-world');
        $this->ev->dispatch('goodbye-world');
    }

    public function world()
    {
        echo 'World Hello';
    }
}
