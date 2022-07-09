<?php
namespace App\Core;

use App\Core\Command_Environment;

class Command_Request
{
    public array $args;
    public array $request_data;

    public function __construct(array $args = [])
    {
        $this->args = $args;
    }

    public function process()
    {
        return $this->getCommand()->getSubCommand()->processRequest();
    }

    public function getCommand()
    {
        $this->request_data['command'] = empty($this->args[1]) ? null : $this->args[1];
        return $this;
    }

    public function getSubCommand()
    {
        $sub_command = empty($this->args[2]) ? null : $this->args[2];

        if (is_null($sub_command)) {
            return $this;
        }

        // check if this is a argument (--)
        if (strpos($sub_command, '--') !== false) {
            return $this;
        }

        if (strpos($sub_command, '-') !== false) {
            return $this;
        }

        $this->request_data['sub_command'] = $sub_command;
        return $this;
    }

    public function processRequest()
    {
        $args = $this->args;
        unset($args[0], $args[1]);

        if (!empty($this->request_data['sub_command'])) {
            unset($args[2]);
        }

        $this->processArguments(array_values($args));

        return $this->returnCommandEnvironment();
    }

    public function processArguments(array $args)
    {
        foreach ($args as $k => $v) {
            $v_clean = str_replace('-', '', $v);
            if (preg_match("/^--\w+/", $v) > 0) {
                $next = isset($args[$k + 1]) ? $args[$k + 1] : null;
                if (!is_null($next) && (preg_match("/^-{1,2}\w+/", $next) <= 0)) {
                    $this->request_data['arguments'][$v_clean] = $next;
                } else {
                    $this->request_data['flags'][] = $v_clean;
                }
                continue;
            }

            if (preg_match("/^-\w+/", $v) > 0) {
                $this->request_data['flags'][] = $v_clean;
            }
        }
    }

    public function returnCommandEnvironment()
    {
        $Command_Environment = new Command_Environment;
        $Command_Environment->load($this->request_data);
        return $Command_Environment;
    }
}
