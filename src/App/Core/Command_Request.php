<?php
namespace App\Core;

use App\Core\Command_Environment;

class Command_Request
{
    /**
     * args
     *
     * passed in from argv
     *
     * @var array
     */
    public array $args;

    /**
     * request_data
     *
     * processed args
     *
     * @var array
     */
    public array $request_data;

    /**
     * constructor
     *
     * @param array $args
     */
    public function __construct(array $args = [])
    {
        $this->args = $args;
    }

    /**
     * Process CLI input
     *
     * @return Command_Environment
     */
    public function process(): Command_Environment
    {
        return $this->getCommand()->getSubCommand()->processRequest();
    }

    /**
     * Gets the main command passed
     *
     * @return Command_Request
     */
    public function getCommand(): Command_Request
    {
        $this->request_data['command'] = empty($this->args[1]) ? null : $this->args[1];
        return $this;
    }

    /**
     * Gets the sub command if any is present
     *
     * @return Command_Request
     */
    public function getSubCommand(): Command_Request
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

    /**
     * Process arguments and return the environment
     *
     * @return Command_Environment
     */
    public function processRequest(): Command_Environment
    {
        $args = $this->args;
        unset($args[0], $args[1]);

        if (!empty($this->request_data['sub_command'])) {
            unset($args[2]);
        }

        $this->processArguments(array_values($args));

        return $this->returnCommandEnvironment();
    }

    /**
     * Processes arguments and flags and stores them for the Command_Environment
     *
     * @param  array $args
     * @return void
     */
    public function processArguments(array $args): void
    {
        foreach ($args as $k => $v) {
            $v_clean = str_replace('-', '', $v);
            if (preg_match("/^--\w+/", $v) > 0) {
                $next = isset($args[$k + 1]) ? $args[$k + 1] : null;
                if (!is_null($next) && (preg_match("/^-{1,2}\w+/", $next) <= 0)) {
                    $this->request_data['arguments'][$v_clean] = $this->findUntilNextArg($args, $k + 1);
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

    /**
     * Builds out an argument value until the next flag/argument is found or input ends
     * This is to capture argument values that are not in quotes
     *
     * @param  array   $data
     * @param  integer $key
     * @return string
     */
    public function findUntilNextArg(array $data, int $key): string
    {
        $k      = $key++;
        $next   = $data[$k];
        $output = [];
        while (preg_match("/^-{1,2}\w+/", $next) <= 0) {
            $output[] = $next;
            $k++;
            if (empty($data[$k])) {
                break;
            }
            $next = $data[$k];
        }
        return implode(' ', $output);
    }

    /**
     * Builds the Command_Environment
     *
     * @return Command_Environment
     */
    public function returnCommandEnvironment(): Command_Environment
    {
        $Command_Environment = new Command_Environment;
        $Command_Environment->load($this->request_data);
        return $Command_Environment;
    }
}
