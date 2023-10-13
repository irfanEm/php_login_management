<?php

namespace Irfanm\Belajar\PHP\MVC\Controller;

use Irfanm\Belajar\PHP\MVC\Config\Database;
use Irfanm\Belajar\PHP\MVC\Domain\Session;
use Irfanm\Belajar\PHP\MVC\Domain\User;
use Irfanm\Belajar\PHP\MVC\Repository\SessionRepository;
use Irfanm\Belajar\PHP\MVC\Repository\UserRepository;
use Irfanm\Belajar\PHP\MVC\Service\SessionService;
use PHPUnit\Framework\TestCase;

class HomeControllerTest extends TestCase
{
    private HomeController $homeController;
    private UserRepository $userRepository;
    private SessionRepository $sessionRepository;

    protected function setUp(): void
    {
        $this->homeController = new HomeController();
        $this->sessionRepository = new SessionRepository(Database::getConnection());
        $this->userRepository = new UserRepository(Database::getConnection());

        $this->sessionRepository->deleteAll();
        $this->userRepository->deleteAll();
    }
    public function testGuest()
    {
        $this->homeController->index();

        $this->expectOutputRegex("[Login Management]");
    }

    public function testUserLogin()
    {

        $user = new User();
        $user->id = "blqs2103";
        $user->name = "Balqis Farah Anabila";
        $user->password = "blqs2103";
        $this->userRepository->save($user);

        $session = new Session();
        $session->id = uniqid();
        $session->userId = $user->id;
        $this->sessionRepository->save($session);

        $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

        $this->homeController->index();

        $this->expectOutputRegex("[Ahlan wa sahlan <br>Balqis Farah Anabila]");
    }
}
