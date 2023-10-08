<?php 

namespace Irfanm\Belajar\PHP\MVC\Controller;

use Irfanm\Belajar\PHP\MVC\App\View;

class HomeController
{

    function index() {
        View::render('Home/index', [
            "title" => "PHP Login Management"
        ]);
    }

}