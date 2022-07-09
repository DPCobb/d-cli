<?php

namespace App\Core;

use App\Core\Base\Environment;

class Command_Environment extends Environment
{
    public string $command;
    public string $sub_command;
    public array $arguments;
    public array $flags;
}
