# D-CLI

A Basic PHP CLI App Framework. This project is dependency free and meant to be a lightweight starting point for PHP CLI applications.

A work in progress demo project can be found [here](https://github.com/DPCobb/dbver).

***These docs are a work in progress as this project is still in development.***

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

require_once __DIR__ . "/vendor/autoload.php";

use App\Core\Application;
use App\Core\Config;
use App\Core\Command_Container;

// Get any user set config values
$config = Config::load(__DIR__ . "/App/config.ini")->get();
$command_container = new Command_Container($config, $argv);

$app = Application::load($command_container);


$app->set('hello-world', function () {
    echo "Hello World";
});

$app->run();

```

This is the most basic possible usage and does not take advantage of sub commands.

### Getting Params and Flags

You can access params (```foo=bar```) and flags (```--baz```) through the ```Application``` class or through the ```Command_Container```.

```php
// Get through Application

$app->get('params');
$app->get('flags');

// Get a specific param
$app->get('params.foo');

// Get from Command Container

$command_container->get('params');
$command_container->get('flags');

// Get a specific param
$command_container->get('params.foo');

// From inside a class
$cc = Application::getCommandContainer();

$cc->get('params');

```

Params must be passed as ```name=value``` and flags are passed with double dashes ```--foo```.

## Passing Class@method to Call

This method also does not make use of sub commands but allows you to move your logic into a class.

```php
#!/usr/bin/php
<?php

if (php_sapi_name() !== 'cli') {
    exit;
}

require_once __DIR__ . "/vendor/autoload.php";

use App\Core\Application;
use App\Core\Config;
use App\Core\Command_Container;

// Get any user set config values
$config = Config::load(__DIR__ . "/App/config.ini")->get();
$command_container = new Command_Container($config, $argv);

$app = Application::load($command_container);


$app->set('hello-world', 'App\Commands\Hello\Test@test');


$app->run()
```

This will call the ```test``` method in the ```Test``` class.

## Advanced Structure

This method makes use of sub commands. To use the advanced structure a specific project layout is required. Your classes that will handle the commands must be placed in the ```src/App/Commands``` directory. The command should be a sub folder. For example, the command ```hello``` should be placed in ```src/App/Commands/Hello```. Within that directory a file named ```Default_Handler.php``` should be created. This, and all classes in the ```src/App/Commands``` directory should implement ```Command_Handler_Interface```. This is the file that will be called on the command ```hello```.

To use a sub command create an additional file with that sub-commands name. For example, ```hello test``` would require you to make a file named ```Test.php``` with a class of ```Test``` in ```src/App/Commands/Hello```. This file will be called to process the sub command.

This requires a minimal setup in the main file.

```php
#!/usr/bin/php
<?php

if (php_sapi_name() !== 'cli') {
    exit;
}

require_once __DIR__ . "/vendor/autoload.php";

use App\Core\Application;
use App\Core\Config;
use App\Core\Command_Container;

// Get any user set config values
$config = Config::load(__DIR__ . "/App/config.ini")->get();
$command_container = new Command_Container($config, $argv);

$app = Application::load($command_container);

$app->run();
```
The ```Application``` class handles the logic and routing of the calls.

### Adding Command Alias's

You can also add alias's for commands or alternate commands by simply adding the following code before the ```$app->run``` call.

```php
$app->alias(['hw', 'hello-world', 'world', 'say-hi'], "App\Commands\Hello\Default_Handler@handle");
```

The commands above would trigger the same code execution as using the ```hello``` command.

```bash
# Actual Command
dcli hello

# Alias Commands
dcli hw
dcli hello-world
dcli world
dcli say-hi
```

Alias's come in handy to offer shortcuts for calling sub-commands of a command. For example, if ```hello``` had a subcommand of ```world``` you could create a alias ```hw``` that targets the ```World::handle``` execution for ```dcli hello world```.

## Additional Classes

The ```src/App/IO``` directory includes classes to help with Input/Output.

### Output Class

Used to send colored output to the console. Colors are set in ```App/IO/Themes/Default_Printer.php``` and can be overwritten with a custom file. The keys you use are the keys you will call to process that color text. For example, ```Output::success``` would process the message using the success color.

Additionally, the Output class can parse a ```.txt``` file for output. A simple syntax is used in the file.

```bash
# Color code text
<[MESSAGE TYPE]> Some Message <end>
ex:
<success>Some Message<end>

# Add A Variable:
<info>{{[VARIABLE NAME]}}<end>
ex:
<info>{{version}}<end>
```

This function can be called either dynamically with ```Output::file``` or using the class method ```parseFile```. Both of these require the file path to be passed as the first argument and an optional array for variables ```[variable => value]```

```php
Output::file('/foo/bar/baz.txt', ['version'=>'1.0.0-beta']);
```

File parsing is helpful when outputting large sections of text, like help pages.