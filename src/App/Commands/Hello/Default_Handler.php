<?php
namespace App\Commands\Hello;

use App\Interfaces\Command_Handler_Interface;
use App\Core\Command_Container;
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
    public array $required_arguments = ['name'];

    public Command_Container $Command_Container;

    public function __construct(Command_Container $Command_Container)
    {
        $this->ev = new Event_Handler;
        $this->ev->subscribe('hello-world', new Hello_Event);
        $this->ev->subscribe('goodbye-world', new Hello_Event);
        $this->ev->subscribe('goodbye-world', new Hello_Event, 'test');
    }

    public function handle()
    {
        Output::message('Hello World');
        if (!$this->d && !$this->dryRun) {
            $this->ev->dispatch('hello-world');
            $this->ev->dispatch('goodbye-world');
        }
    }

    public function world()
    {
        echo 'World Hello';
    }
}
