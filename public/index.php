<?php

require_once __DIR__ . "/../vendor/autoload.php";

use Irfanm\Belajar\PHP\MVC\App\Router;
use Irfanm\Belajar\PHP\MVC\Config\Database;
use Irfanm\Belajar\PHP\MVC\Controller\HomeController;
use Irfanm\Belajar\PHP\MVC\Controller\UserController;

Database::getConnection('prod');

// HomeController
Router::add('GET', '/', HomeController::class, 'index', []);

// UserController
Router::add('GET', '/users/register', UserController::class, 'register',[]);
Router::add('POST', '/users/register', UserController::class, 'postRegister',[]);
Router::add('GET', '/users/login', UserController::class, 'login',[]);
Router::add('POST', '/users/login', UserController::class, 'postLogin',[]);

Router::run();