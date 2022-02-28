<?php

namespace App\IO;

use App\Interfaces\Printer_Interface;
use App\IO\Themes\Default_Printer;

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

    public static function line()
    {
        echo "\n";
    }

    public function __call($method, $args)
    {
        $color = $this->printer[$method] ?? $this->default_printer[$method];

        $message = $args[0];
        
        if ($method === 'banner') {
            $message = "     {$args[0]}     ";
        }

        return $this->output($message, $color);
    }

    public static function __callStatic($method, $args)
    {
        $out = new Output;

        $message = $args[0];

        if ($method === 'banner') {
            $message = "     {$args[0]}     ";
        }

        return $out->output($message, $out->default_printer[$method]);
    }
}
