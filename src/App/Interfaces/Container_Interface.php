<?php

namespace App\Interfaces;

interface Container_Interface
{
    public function set(string $key, $value);
    public function get(string $key);
    public function has(string $key): bool;
}
