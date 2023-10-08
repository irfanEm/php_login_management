<?php

require_once __DIR__ . "/../vendor/autoload.php";

use Irfanm\Belajar\PHP\MVC\App\Router;
use Irfanm\Belajar\PHP\MVC\Controller\HomeController;

Router::add('GET', '/', HomeController::class, 'index', []);

Router::run();