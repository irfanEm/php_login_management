<?php 

namespace Irfanm\Belajar\PHP\MVC\Controller;

use Irfanm\Belajar\PHP\MVC\App\View;

class HomeController
{

    function index(): void
    {

        $model = [
            "title" => "Belajar PHP MVC",
            "content" => "Selamat belajar PHP MVC  dari PZN implement Progamer HTML."
        ];

        View::render('Home/index', $model);
    }

    function hello(): void
    {
        echo "HomeController.hello()";
    }

    function world(): void
    {
        echo "HomeController.world()";
    }

    function about(): void
    {
        echo "Author : Progamer HTML Cilacap.";
    }

    function login(): void
    {
        $request = [
            "username" => $_POST["username"],
            "password" => $_POST["password"]
        ];

        $user = [];

        $response = [
            "message" => "Login Sukses"
        ];
    }

}