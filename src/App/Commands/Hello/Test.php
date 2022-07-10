<?php
namespace App\Commands\Hello;

use App\Interfaces\Command_Handler_Interface;
use App\Core\Command_Container;
use App\Core\Event_Handler;
use App\Commands\Hello\Hello_Event;
use App\IO\Output;

class Test implements Command_Handler_Interface
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
     * arguments
     *
     * The arguments this command accepts
     *
     * @var array
     */
    public array $arguments = ['name', 'age'];

    /**
     * required_arguments
     *
     * Any arguments this command requires to be set
     *
     * @var array
     */
    public array $required_arguments = [];

    public Command_Container $Command_Container;
    public Event_Handler $Event_Handler;

    public function __construct(Event_Handler $Event_Handler)
    {
        $this->Event_Handler = $Event_Handler;
        $this->Event_Handler->subscribe('hello-world', new Hello_Event);
        $this->Event_Handler->subscribe('goodbye-world', new Hello_Event);
        $this->Event_Handler->subscribe('goodbye-world', new Hello_Event, 'test');
    }

    public function handle()
    {
        Output::message('Hello World TEST' . $this->name);
        $this->Event_Handler->dispatch('hello-world');
    }

    public function test()
    {
        Output::message('Hello World TEST' . $this->name);
    }
}
