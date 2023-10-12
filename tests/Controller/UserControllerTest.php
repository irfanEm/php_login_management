<?php

namespace Irfanm\Belajar\PHP\MVC\App {

    function header(string $value){

        echo $value;

    }

}

namespace Irfanm\Belajar\PHP\MVC\Controller {

use Irfanm\Belajar\PHP\MVC\Config\Database;
use Irfanm\Belajar\PHP\MVC\Domain\User;
use Irfanm\Belajar\PHP\MVC\Repository\UserRepository;
use PHPUnit\Framework\TestCase;

class UserControllerTest extends TestCase
{
    private UserController $userController;
    private UserRepository $userRepository;
    
    public function setUp(): void
    {
        $this->userController = new UserController();

        $this->userRepository = new UserRepository(Database::getConnection());
        $this->userRepository->deleteAll();

        putenv("mode=test");
    }

    public function testRegister()
    {
        $this->userController->register();

        self::expectOutputRegex("[Register]");
        self::expectOutputRegex("[Id]");
        self::expectOutputRegex("[Name]");
        self::expectOutputRegex("[Password]");
        self::expectOutputRegex("[Register new User]");
    }

    public function testPostRegisterSuccess()
    {
        $_POST['id'] = 'coba1';
        $_POST['name'] = 'coba';
        $_POST['password'] = 'cobadong';

        $this->userController->postRegister();

        $this->expectOutputRegex("[Location: /users/login]");
    }

    public function testPostRegisterValidationError()
    {
        $_POST['id'] = '';
        $_POST['name'] = 'coba';
        $_POST['password'] = 'cobadong';

        $this->userController->postRegister();

        self::expectOutputRegex("[Register]");
        self::expectOutputRegex("[Id]");
        self::expectOutputRegex("[Name]");
        self::expectOutputRegex("[Password]");
        self::expectOutputRegex("[Register new User]");
        self::expectOutputRegex("[Id, Name, dan Password wajib diisi !]");

    }

    public function testPostRegisterDuplicate()
    {
        $user = new User();
        $user->id = 'blqs2103';
        $user->name = 'Balqis Farah Anabila';
        $user->password = 'blqs2103';

        $this->userRepository->save($user);

        $_POST['id'] = 'blqs2103';
        $_POST['name'] = 'Balqis Farah Anabila';
        $_POST['password'] = 'blqs2103';

        $this->userController->postRegister();

        self::expectOutputRegex("[Register]");
        self::expectOutputRegex("[Id]");
        self::expectOutputRegex("[Name]");
        self::expectOutputRegex("[Password]");
        self::expectOutputRegex("[Register new User]");
        self::expectOutputRegex("[User Id sudah ada !]");
    }

    public function testLogin()
    {
        $this->userController->login();

        $this->expectOutputRegex("[Login User]");
        $this->expectOutputRegex("[Id]");
        $this->expectOutputRegex("[Password]");
    }

    public function testLoginSuccess()
    {
        $user = new User();
        $user->id = "blqs2103";
        $user->name = "Balqis Farah Anabila";
        $user->password = password_hash("rahasia", PASSWORD_BCRYPT);

        $this->userRepository->save($user);

        $_POST['id'] = "blqs2103";
        $_POST['password'] = "rahasia";

        $this->userController->postLogin();

        $this->expectOutputRegex("[Location: /]");
    }

    public function testLoginValidationError()
    {
        $_POST['id'] = '';
        $_POST['password'] = '';

        $this->userController->postLogin();

        $this->expectOutputRegex("[Login User]");
        $this->expectOutputRegex("[Id]");
        $this->expectOutputRegex("[Password]");
        $this->expectOutputRegex("[Id dan Password wajib diisi !]");
    }

    public function testLoginUserNotFound()
    {
        $_POST['id'] = 'notfound';
        $_POST['password'] = 'notfound';

        $this->userController->postLogin();

        $this->expectOutputRegex("[Login User]");
        $this->expectOutputRegex("[Id]");
        $this->expectOutputRegex("[Password]");
        $this->expectOutputRegex("[Id atau password salah.]");
    }

    public function testLoginWrongPassword()
    {
        $user = new User();
        $user->id = 'blqs2103';
        $user->name = 'Balqis Farah Anabila';
        $user->password = password_hash('blqs2103', PASSWORD_BCRYPT);

        $this->userRepository->save($user);

        $_POST['id'] = 'blqs2103';
        $_POST['password'] = 'notfound';

        $this->userController->postLogin();

        $this->expectOutputRegex("[Login User]");
        $this->expectOutputRegex("[Id]");
        $this->expectOutputRegex("[Password]");
        $this->expectOutputRegex("[Id atau password salah.]");
    }
}

}
