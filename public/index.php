<?php

require_once __DIR__ . "/../vendor/autoload.php";

use Irfanm\Belajar\PHP\MVC\App\Router;
use Irfanm\Belajar\PHP\MVC\Config\Database;
use Irfanm\Belajar\PHP\MVC\Controller\HomeController;
use Irfanm\Belajar\PHP\MVC\Controller\UserController;
use Irfanm\Belajar\PHP\MVC\Middleware\MustNotLoginMiddleware;
use Irfanm\Belajar\PHP\MVC\Middleware\MustLoginMiddleware;

Database::getConnection('prod');

// HomeController
Router::add('GET', '/', HomeController::class, 'index', []);

// UserController
Router::add('GET', '/users/register', UserController::class, 'register',[MustNotLoginMiddleware::class]);
Router::add('POST', '/users/register', UserController::class, 'postRegister',[MustNotLoginMiddleware::class]);
Router::add('GET', '/users/login', UserController::class, 'login',[MustNotLoginMiddleware::class]);
Router::add('POST', '/users/login', UserController::class, 'postLogin',[MustNotLoginMiddleware::class]);
Router::add('GET', '/users/logout', UserController::class, 'logout',[MustLoginMiddleware::class]);
Router::add('GET', '/users/profile', UserController::class, 'updateProfile',[MustLoginMiddleware::class]);
Router::add('POST', '/users/profile', UserController::class, 'postUpdateProfile',[MustLoginMiddleware::class]);

Router::run();