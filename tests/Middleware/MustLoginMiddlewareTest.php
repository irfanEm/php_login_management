<?php

namespace Irfanm\Belajar\PHP\MVC\App {

    function header(string $value){

        echo $value;

    }

}

namespace Irfanm\Belajar\PHP\MVC\Middleware {

    use Irfanm\Belajar\PHP\MVC\Config\Database;
    use Irfanm\Belajar\PHP\MVC\Domain\Session;
    use Irfanm\Belajar\PHP\MVC\Domain\User;
    use Irfanm\Belajar\PHP\MVC\Repository\SessionRepository;
    use Irfanm\Belajar\PHP\MVC\Repository\UserRepository;
    use Irfanm\Belajar\PHP\MVC\Service\SessionService;
    use PHPUnit\Framework\TestCase;
    
    class MustLoginMiddlewareTest extends TestCase
    {
        private MustLoginMiddleware $middleware;
        private UserRepository $userRepository;
        private SessionRepository $sessionRepository;
    
        protected function setUp(): void
        {
            $this->middleware = new MustLoginMiddleware();
            putenv("mode=test");

            $this->userRepository = new UserRepository(Database::getConnection());
            $this->sessionRepository = new SessionRepository(Database::getConnection());

            $this->sessionRepository->deleteAll();
            $this->userRepository->deleteAll();
        }
    
        public function testBefore()
        {
            $this->middleware->before();
            $this->expectOutputRegex("[Location: /users/login]");
        }

        public function testBeforeLoginUser()
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

            $this->middleware->before();
            $this->expectOutputString("");
        }
    }
}

