# D-CLI

A Basic PHP CLI App Framework. This project is dependency free and meant to be a lightweight starting point for PHP CLI applications.

***These docs are a work in progress as this project is still in development.***

[Full Documentation](https://github.com/DPCobb/d-cli/wiki) (For Beta Version)

![GitHub tag (latest by date)](https://img.shields.io/github/v/tag/dpcobb/d-cli?style=plastic) ![GitHub](https://img.shields.io/github/license/dpcobb/d-cli?style=plastic)

## Basic Setup

Install with: ```composer create-project danc0/dcli``` or download the latest package.

After creating your main file (ex: ``src/dcli``) ensure you make that file executable and optionally add it to your path so you can call your application from anywhere. You will also want to create a Composer autoload file.

The most basic setup uses anonymous functions to process commands.

```php
#!/usr/bin/php
<?php

if (php_sapi_name() !== 'cli') {
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';

use App\Core\Application; // Main Application
use App\Core\Config; // Process the config
use App\Core\Command_Request; // Process the Request
use App\Core\Command_Container; // Stores the config and request environment
use App\IO\Output;

// Get any user set config values
$config = Config::load(__DIR__ . '/App/config.ini')->get();

// Load the request into the Command_Container
$Command_Request   = new Command_Request($argv);
$Command_Container = new Command_Container($config, $Command_Request->process());

// Load the Application
$app = Application::load($Command_Container);

// Set the commands
$app->set('hello-world', function () {
    Output::message('Hello World');
});

// Run the Application
$app->run();

```

This is the most basic possible usage and does not take advantage of sub commands.


## Passing Class@method to Call

This method also does not make use of sub commands but allows you to move your logic into a class.

```php
#!/usr/bin/php
<?php

if (php_sapi_name() !== 'cli') {
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';

use App\Core\Application; // Main Application
use App\Core\Config; // Process the config
use App\Core\Command_Request; // Process the Request
use App\Core\Command_Container; // Stores the config and request environment
use App\IO\Output;
use App\Commands\Hello\Test;

// Get any user set config values
$config = Config::load(__DIR__ . '/App/config.ini')->get();

// Load the request into the Command_Container
$Command_Request   = new Command_Request($argv);
$Command_Container = new Command_Container($config, $Command_Request->process());

// Load the Application
$app = Application::load($Command_Container);

// Set the commands
$app->set('hello-world', Test::class . '@test');

// Run the Application
$app->run();
```

This will call the ```test``` method in the ```Test``` class.

## File Structure Controlled Commands

This method makes use of sub commands. To use the advanced structure a specific project layout is required. Your classes that will handle the commands must be placed in the ```src/App/Commands``` directory. The command should be a sub folder. For example, the command ```hello``` should be placed in ```src/App/Commands/Hello```. Within that directory a file named ```Default_Handler.php``` should be created. This, and all classes in the ```src/App/Commands``` directory should implement ```Command_Handler_Interface```. This is the file that will be called on the command ```hello```.

To use a sub command create an additional file with that sub-commands name. For example, ```hello test``` would require you to make a file named ```Test.php``` with a class of ```Test``` in ```src/App/Commands/Hello```. This file will be called to process the sub command.

This requires a minimal setup in the main file.

```php
#!/usr/bin/php
<?php

if (php_sapi_name() !== 'cli') {
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';

use App\Core\Application; // Main Application
use App\Core\Config; // Process the config
use App\Core\Command_Request; // Process the Request
use App\Core\Command_Container; // Stores the config and request environment

// Get any user set config values
$config = Config::load(__DIR__ . '/App/config.ini')->get();

// Load the request into the Command_Container
$Command_Request   = new Command_Request($argv);
$Command_Container = new Command_Container($config, $Command_Request->process());

// Load the Application
$app = Application::load($Command_Container);

// Run the Application
$app->run();
```
The ```Application``` class handles the logic and routing of the calls.

## Passing Arguments

Arguments are treated as key value pairs passed in the the key starting with the `--` annotation. Arguments can be passed into the command in the following way:

```bash
dcli hello --name "John Smith"
# OR

dcli hello --name John Smith
```
The command parser will return `John Smith` for the `name` argument for either of these approaches.

The command runner will automatically set these variables within your class when it resolves the handler. For example, if you have a `Default_Handler` with the argument `name` available the command runner will add the value of `name` to `$this->name` within your class.

## Passing Flags

Flags are considered booleans as can be passed with either `-` or `--` annotation.

The command runner will automatically set these variables within your class when it resolves the handler. For example, if you have a `Default_Handler` with the flag `v` available the command runner will set `$this->v` to true or false depending on if it was passed or not.

## Command_Container

The Command_Container holds information about your Config and your Request Environment. To tell the Command Runner to load this into your handler class you can do one of two things. First you can declare a variable in your class `public Command_Container $Command_Container` this will be set AFTER the class is instantiated so you will not have access in the constructor, but will in the other methods of your class. If you need this available in your constructor function add `Command_Container $Command_Container` as a variable that needs to be passed into the class.

## Event_Handler Global

The main Application Event_Handler can be injected into your handler classes using the same two techniques outlined above for the `Command_Container`