# Solsken Framework
MVC Framework to bootstrap PHP projects, including Javascript framework for frontend workload

## Usage
Add the following snippet to your composer.json:
```
"require": {
    ...
    "hmusche/solsken": "master@dev",
    ...
},
"repositories": [
    ...
    {
          "url": "git@github.com:hmusche/solsken.git",
          "type": "vcs"
    }
    ...
]
```
and run ```composer update```.

A basic index.php to use the framework could look like this:
```
<?php

require "vendor/autoload.php";

$app = new Solsken\Application(Solsken\Util::fileMerge('config'));
$app->run();
```

This reads all files in the config directory and applies them as config. A sample config file will be added soon.

## Examples
To see this in action, see my [travelblog project](https://github.com/hmusche/travelblog), currently in use at https://no-fly-zone.de.
