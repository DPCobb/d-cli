<?php

namespace dcli\App\IO;

use dcli\App\Interfaces\Printer_Interface;
use dcli\App\IO\Themes\Default_Printer;

class Output
{
    public ?Printer_Interface $printer;
    public array $default_printer;

    public function __construct(?Printer_Interface $printer = null)
    {
        $this->printer = $printer;
        $default_printer = new Default_Printer;
        $this->default_printer = $default_printer->getThemeSettings();
    }

    public function output(string $message, string $color)
    {
        echo sprintf("\e[%sm%s\e[0m\n", $color, $message);
    }


    public function __call($method, $args)
    {
        $color = $this->printer[$method] ?? $this->default_printer[$method];
        return $this->output($args[0], $color);
    }

    public function __callStatic($method, $args)
    {
        $out = new Output;

        return $out->output($args[0], $out->default_printer[$method]);
    }
}
