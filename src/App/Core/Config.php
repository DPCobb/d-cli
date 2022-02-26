<?php

namespace dcli\App\Core;

class Config
{
    public string $path;
    public array $config;
    public static $instance;

    public function set($path)
    {
        if (file_exists($path)) {
            $this->config = parse_ini_file($path, true);
            $this->config['is_unit'] = $this->areWeUnit();

            $this->config = $this->setBools($this->config);
        }
    }

    public function setBools(array &$config)
    {
        foreach ($config as $k => &$v) {
            if (is_array($v)) {
                $v = $this->setBools($v, true);
                continue;
            }

            $value = strtolower($v);

            if (is_bool($value) || $value === 'false' || $value === 'true') {
                $v = $value === 'true' ? true : false;
            }
        }

        return $config;
    }

    public function get():array
    {
        return $this->config;
    }

    public static function load(string $path)
    {
        if (is_null(self::$instance)) {
            self::$instance = new Config;
        }

        self::$instance->set($path);

        return self::$instance;
    }

    public function areWeUnit() : bool
    {
        if (! defined('PHPUNIT_COMPOSER_INSTALL') && ! defined('__PHPUNIT_PHAR__')) {
            return false;
        }

        return true;
    }
}
