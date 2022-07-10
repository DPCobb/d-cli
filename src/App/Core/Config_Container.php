<?php
namespace App\Core;

use App\Interfaces\Container_Interface;

class Config_Container implements Container_Interface
{
    /**
     * config
     *
     * @var array
     */
    public array $config;

    /**
     * Set a value
     *
     * @param  string $key
     * @param  mixed  $value
     * @return void
     */
    public function set(string $key, $value): void
    {
        $this->{$key} = $value;
    }

    /**
     * Get a value
     *
     * @param  string $key
     * @return mixed
     */
    public function get(string $key)
    {
        return $this->{$key} ?? null;
    }

    /**
     * Check if Container has a value
     *
     * @param  string  $key
     * @return boolean
     */
    public function has(string $key): bool
    {
        return array_key_exists($key, $this->config) || isset($this->{$key});
    }

    /**
     * Load the container from a config array
     *
     * @param  array $config
     * @return void
     */
    public function load(array $config): void
    {
        $this->config = $config;
        foreach ($this->config as $k => $v) {
            $this->{$k} = $v;
        }
    }
}
