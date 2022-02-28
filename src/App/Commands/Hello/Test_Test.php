<?php

namespace App\Commands\Hello;

use App\Core\Application;

class Test_Test
{
    public function handle()
    {
        echo "TEST HELLO COMMAND";
        $cc = Application::read()->get('params.name');

        //$name = $cc->get('params.name');

        echo $cc;
    }

    public function test()
    {
        echo "Who dis?\n";
        $name = readline();
        echo "HELLO {$name} from TEST TEST";
    }
}
