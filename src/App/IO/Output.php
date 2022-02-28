<?php

namespace App\IO;

use App\Interfaces\Printer_Interface;
use App\IO\Themes\Default_Printer;

class Output
{
    /**
     * printer
     *
     * @var array|null
     */
    public ?array $printer;

    /**
     * default_printer
     *
     * @var array
     */
    public array $default_printer;

    public static ?Output $instance = null;

    public function __construct(?Printer_Interface $printer = null)
    {
        // printer sets the color values
        $this->printer = null;
        if (!is_null($printer)) {
            $this->printer = $printer->getThemeSettings();
        }
        $default_printer = new Default_Printer;
        $this->default_printer = $default_printer->getThemeSettings();
    }

    public static function load(Printer_Interface $printer)
    {
        if (is_null(static::$instance)) {
            static::$instance = new Output($printer);
        }

        return static::$instance;
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
     * Cannot currently use themes
     *
     * @param string $method
     * @param array $args
     *
     * @return void
     */
    public static function __callStatic(string $method, array $args): void
    {
        if (!is_null(static::$instance)) {
            static::$instance->$method($args[0]);
            return;
        }

        $out = new Output;

        $message = $args[0];

        if ($method === 'banner') {
            $message = "     {$args[0]}     ";
        }

        $out->output($message, $out->default_printer[$method]);

        return;
    }
}
