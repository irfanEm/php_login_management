<?php

require_once __DIR__ . "/../vendor/autoload.php";

use Irfanm\Belajar\PHP\MVC\App\Router;

Router::add('GET', '/', 'Controller', 'index');
Router::add('GET', '/hello', 'HelloController', 'hello');
Router::add('GET', '/world', 'WorldController', 'world');

Router::run();