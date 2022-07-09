<?php

namespace App\Core\Base;

class Environment
{
    public function load(array $data)
    {
        foreach ($data as $key => $data) {
            if (array_key_exists($key, get_class_vars(get_class($this)))) {
                $this->{$key} = $data;
            }
        }
    }
}
