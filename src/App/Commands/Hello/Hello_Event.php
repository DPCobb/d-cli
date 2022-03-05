<?php

namespace App\Commands\Hello;

use App\Interfaces\Event_Provider_Interface;
use App\IO\Output;

class Hello_Event implements Event_Provider_Interface
{
    /**
     * Process the Goodbye event
     *
     * @return void
     */
    public function goodbyeWorld()
    {
        Output::banner('Goodbye!');
    }

    /**
     * Method also fired during goodbye-world event
     *
     * @param string $event
     *
     * @return void
     */
    public function test(string $event)
    {
        Output::success('Sent to test from ' . $event);
    }

    /**
     * Handles routing the event action
     *
     * @param string $event_name
     *
     * @return void
     */
    public function run(string $event_name)
    {
        if (empty($event_name)) {
            return;
        }
        // Parse the event name to a method name
        $parts = explode('-', $event_name);
        foreach ($parts as $k => &$v) {
            if ($k === 0) {
                continue;
            }
            $v = ucwords($v);
        }
        $event_name_parsed = implode($parts, '');

        // If we have a method call it
        if (method_exists($this, $event_name_parsed)) {
            call_user_func([$this, $event_name_parsed]);
            return;
        }
        
        // If not process it here...
        Output::banner($event_name);
        return;
    }
}
