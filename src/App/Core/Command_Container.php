<?php

namespace App\Core;

use App\Interfaces\Command_Container_Interface;
use App\Utility\Cleaner;
use App\Utility\Arr;
use stdClass;

class Command_Container implements Command_Container_Interface
{
    public function __construct(array $config = [], array $args)
    {
        $this->config = new stdClass();
        
        foreach ($config as $k => $v) {
            $this->config->$k = $v;
        }

        $this->instance = [];

        $this->processArgs($args);
    }

    public function processArgs(array $args)
    {
        $this->instance['command'] = Cleaner::clean($args[1]);

        if (!empty($args[2]) && strpos($args[2], '--') === false && strpos($args[2], '=') === false) {
            $this->instance['sub_command'] = Cleaner::clean($args[2]);
        }

        foreach ($args as $k => $v) {
            if (strpos($v, "--") !== false) {
                $this->instance['flags'][] = str_replace('--', '', $v);
            }

            if (strpos($v, "=") !== false) {
                $params = explode('=', $v);
                $this->instance['params'][$params[0]] = $params[1];
            }

            if (strpos($v, '-') !== false) {
                $parts = explode('-', $v);

                if ($parts[0] !== '') {
                    continue;
                }

                $flags = str_split($parts[1]);
                foreach ($flags as $flag) {
                    $this->instance['flags'][] = $flag;
                }
            }
        }
    }

    public function set(string $key, $value)
    {
        $this->instance[$key] = $value;
    }

    public function get(string $key)
    {
        return Arr::get($key, $this->instance);
    }

    public function has(string $key): bool
    {
        return !empty(Arr::get($key, $this->instance));
    }

    public function hasFlag(string $key): bool
    {
        if (empty($this->instance['flags'])) {
            return false;
        }
        return in_array($key, $this->instance['flags']);
    }
}
