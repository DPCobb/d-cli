<?php
namespace App\Core;

use App\Interfaces\Container_Interface;
use App\Core\Command_Environment;
use App\Core\Config_Container;

class Command_Container implements Container_Interface
{
    /**
     * Command_Environment
     *
     * @var Command_Environment
     */
    public Command_Environment $Environment;

    /**
     * Config
     *
     * @var Config_Container
     */
    public Config_Container $Config;

    /**
     * Constructor
     *
     * @param array               $config
     * @param Command_Environment $Command_Environment
     */
    public function __construct(array $config = [], Command_Environment $Command_Environment)
    {
        $this->Config = new Config_Container();
        $this->Config->load($config);
        $this->Environment = $Command_Environment;
    }

    /**
     * Set a value into the Command_Container instance
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return void
     */
    public function set(string $key, $value): void
    {
        $this->Environment->$key = $value;
        return;
    }

    /**
     * Get a value from the Command_Container instance, supports dot notation
     *
     * @param string $key
     *
     * @return mixed
     */
    public function get(string $key)
    {
        return $this->Environment->{$key};
    }

    /**
     * Check if the Command_Container instance has a value
     *
     * @param string $key
     *
     * @return boolean
     */
    public function has(string $key): bool
    {
        return !empty($this->Environment->{$key});
    }

    /**
     * Check if the Command_Container intance has a specific flag
     *
     * @param string $key
     *
     * @return boolean
     */
    public function hasFlag(string $key): bool
    {
        if (empty($this->Environment->flags)) {
            return false;
        }

        return in_array($key, $this->Environment->flags);
    }
}
