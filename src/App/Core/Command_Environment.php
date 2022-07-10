<?php
namespace App\Core;

use App\Core\Base\Environment;

class Command_Environment extends Environment
{
    /**
     * command
     *
     * The command to process
     *
     * @var string
     */
    public string $command;

    /**
     * sub_command
     *
     * The sub command to process
     *
     * @var string
     */
    public string $sub_command;

    /**
     * arguments
     *
     * Any arguments passed
     *
     * @var array
     */
    public array $arguments = [];

    /**
     * flags
     *
     * Any passed flags
     *
     * @var array
     */
    public array $flags = [];
}
