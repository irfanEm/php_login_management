<?php

require_once __DIR__ . "/../vendor/autoload.php";

use Irfanm\Belajar\PHP\MVC\App\Router;
use Irfanm\Belajar\PHP\MVC\Controller\HomeController;
use Irfanm\Belajar\PHP\MVC\Controller\ProductController;
use Irfanm\Belajar\PHP\MVC\Middleware\AuthMiddleware;

Router::add('GET', '/products/([0-9a-zA-Z]*)/categories/([0-9a-zA-Z]*)', ProductController::class, 'categories');
Router::add('GET', '/', HomeController::class, 'index');
Router::add('GET', '/hello', HomeController::class, 'hello', [AuthMiddleware::class]);
Router::add('GET', '/world', HomeController::class, 'world', [AuthMiddleware::class]);
Router::add('GET', '/about', HomeController::class, 'about');

Router::run();