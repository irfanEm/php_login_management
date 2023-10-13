<?php

namespace Irfanm\Belajar\PHP\MVC\Service;

use Irfanm\Belajar\PHP\MVC\Config\Database;
use Irfanm\Belajar\PHP\MVC\Domain\Session;
use Irfanm\Belajar\PHP\MVC\Domain\User;
use Irfanm\Belajar\PHP\MVC\Repository\SessionRepository;
use Irfanm\Belajar\PHP\MVC\Repository\UserRepository;
use PHPUnit\Framework\TestCase;

function setcookie(string $name, string $value)
{
    echo "$name: $value";
}

class SessionServiceTest extends TestCase
{

    private SessionService $sessionService;
    private SessionRepository $sessionRepository;
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        $this->sessionRepository = new SessionRepository(Database::getConnection());
        $this->userRepository = new UserRepository(Database::getConnection());
        $this->sessionService = new SessionService($this->sessionRepository, $this->userRepository);

        $this->sessionRepository->deleteAll();
        $this->userRepository->deleteAll();

        $user = new User();
        $user->id = "blqs2103";
        $user->name = "Balqis Farah Anabila";
        $user->password = "blqs2103";
        $this->userRepository->save($user);
    }

    public function testCreate()
    {
        $session = $this->sessionService->create("blqs2103");

        $this->expectOutputRegex("[X-PRGHTML-SESSION: $session->id]");

        $result = $this->sessionRepository->findById($session->id);

        self::assertEquals("blqs2103", $result->userId);
    }

    public function testDestroy()
    {
        $session = new Session();
        $session->id = uniqid();
        $session->userId = "blqs2103";

        $this->sessionRepository->save($session);

        $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

        $this->sessionService->destroy();

        $this->expectOutputRegex("[X-PRGHTML-SESSION: ]");

        $result = $this->sessionRepository->findById($session->id);
        self::assertNull($result);
    }

    public function testCurrent()
    {
        $session = new Session();
        $session->id = uniqid();
        $session->userId = "blqs2103";

        $this->sessionRepository->save($session);

        $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

        $user = $this->sessionService->current();

        self::assertEquals($session->userId, $user->id);
    }

}
