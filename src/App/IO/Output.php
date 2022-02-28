<?php

namespace App\IO;

use App\Interfaces\Printer_Interface;
use App\IO\Themes\Default_Printer;

class Output
{
    /**
     * printer
     *
     * @var Printer_Interface|null
     */
    public ?Printer_Interface $printer;

    /**
     * default_printer
     *
     * @var array
     */
    public array $default_printer;

    public function __construct(?Printer_Interface $printer = null)
    {
        // printer sets the color values
        $this->printer = $printer;
        $default_printer = new Default_Printer;
        $this->default_printer = $default_printer->getThemeSettings();
    }

    /**
     * Format output message
     *
     * @param string $message
     * @param string $color
     *
     * @return void
     */
    public function output(string $message, string $color): void
    {
        echo sprintf("\e[%sm%s\e[0m\n", $color, $message);
        return;
    }

    /**
     * Just a new line
     *
     * @return void
     */
    public static function line():void
    {
        echo "\n";
        return;
    }

    /**
     * Magic method to process output
     *
     * @param string $method
     * @param array $args
     *
     * @return void
     */
    public function __call(string $method, array $args): void
    {
        $color = $this->printer[$method] ?? $this->default_printer[$method];

        $message = $args[0];
        
        if ($method === 'banner') {
            $message = "     {$args[0]}     ";
        }

        $this->output($message, $color);
        return;
    }

    /**
     * Magic method so you can call these outputs statically
     *
     * @param string $method
     * @param array $args
     *
     * @return void
     */
    public static function __callStatic(string $method, array $args): void
    {
        $out = new Output;

        $message = $args[0];

        if ($method === 'banner') {
            $message = "     {$args[0]}     ";
        }

        $out->output($message, $out->default_printer[$method]);

        return;
    }
}
