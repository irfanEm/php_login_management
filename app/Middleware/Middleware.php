<?php 

namespace Irfanm\Belajar\PHP\MVC\Middleware;

interface Middleware
{
    function before(): void;
}