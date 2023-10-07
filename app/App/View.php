<?php 

namespace Irfanm\Belajar\PHP\MVC\App;

class View 
{
    public static function render(string $view, $model)
    {
        require_once __DIR__ . "/../View/" . $view . ".php";
    }
}