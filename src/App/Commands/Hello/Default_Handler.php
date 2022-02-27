<?php

namespace App\Commands\Hello;

use App\Interfaces\Command_Handler_Interface;

class Default_Handler implements Command_Handler_Interface
{
    public function handle()
    {
        echo "hello command";
    }
}
