<?php

namespace App\IO\Themes;

use App\Interfaces\Printer_Interface;

class Alt_Printer implements Printer_Interface
{
    public function getThemeSettings() : array
    {
        return [
            'error' => '0;37;41',
            'warning' => '0;37',
            'alert' => '0;31',
            'message' => '0;37',
            'info' => '1;31',
            'success' => '0;32',
            'banner' => '1;37;45'
        ];
    }
}
