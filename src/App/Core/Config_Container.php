<?php

namespace App\Core;

use App\Interfaces\Container_Interface;

class Config_Container implements Container_Interface
{
    public array $config;
    public function set(string $key, $value)
    {
        $this->{$key} = $value;
    }
    
    public function get(string $key)
    {
        return $this->{$key} ?? null;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->config) || isset($this->{$key});
    }

    public function load(array $config)
    {
        $this->config = $config;
        foreach ($this->config as $k => $v) {
            $this->{$k} = $v;
        }
    }
}
