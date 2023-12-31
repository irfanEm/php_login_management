<?php

namespace Irfanm\Belajar\PHP\MVC\Controller {

    require_once __DIR__ . "/../Helper/helper.php";

    use Irfanm\Belajar\PHP\MVC\Config\Database;
    use Irfanm\Belajar\PHP\MVC\Domain\User;
    use Irfanm\Belajar\PHP\MVC\Repository\SessionRepository;
    use Irfanm\Belajar\PHP\MVC\Repository\UserRepository;
    use PHPUnit\Framework\TestCase;
    use Irfanm\Belajar\PHP\MVC\Service;
    use Irfanm\Belajar\PHP\MVC\App;
    use Irfanm\Belajar\PHP\MVC\Domain\Session;
    use Irfanm\Belajar\PHP\MVC\Service\SessionService;

    class UserControllerTest extends TestCase
    {
        private UserController $userController;
        private UserRepository $userRepository;
        private SessionRepository $sessionRepository;
        
        public function setUp(): void
        {
            $this->userController = new UserController();

            $this->sessionRepository = new SessionRepository(Database::getConnection());
            $this->sessionRepository->deleteAll();

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
            $this->expectOutputRegex("[X-PRGHTML-SESSION: ]");
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

        public function testLogout()
        {
            $user = new User();
            $user->id = 'blqs2103';
            $user->name = 'Balqis Farah Anabila';
            $user->password = password_hash('blqs2103', PASSWORD_BCRYPT);

            $this->userRepository->save($user);

            $session = new Session();
            $session->id = uniqid();
            $session->userId = $user->id;

            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $this->userController->logout();

            $this->expectOutputRegex("[Location: /]");
            $this->expectOutputRegex("[X-PRGHTML-SESSION: ]");
        }

        public function testUpdateProfile()
        {
            $user = new User();
            $user->id = 'blqs2103';
            $user->name = 'Balqis Farah Anabila';
            $user->password = password_hash('blqs2103', PASSWORD_BCRYPT);

            $this->userRepository->save($user);

            $session = new Session();
            $session->id = uniqid();
            $session->userId = $user->id;

            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $this->userController->updateProfile();

            $this->expectOutputRegex("[Profile]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[blqs2103]");
            $this->expectOutputRegex("[Name]");
            $this->expectOutputRegex("[Balqis Farah Anabila]");
        }

        public function testPostUpdateProfileSuccess()
        {
            $user = new User();
            $user->id = 'blqs2103';
            $user->name = 'Balqis Farah Anabila';
            $user->password = password_hash('blqs2103', PASSWORD_BCRYPT);

            $this->userRepository->save($user);

            $session = new Session();
            $session->id = uniqid();
            $session->userId = $user->id;

            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $_POST['name'] = "Shilvia";
            $this->userController->postUpdateProfile();

            $this->expectOutputRegex("[Location: /]");

            $result = $this->userRepository->findById('blqs2103');

            self::assertEquals("Shilvia", $result->name);
        }

        public function testPostUpdateProfileValidationError()
        {
            $user = new User();
            $user->id = 'blqs2103';
            $user->name = 'Balqis Farah Anabila';
            $user->password = password_hash('blqs2103', PASSWORD_BCRYPT);

            $this->userRepository->save($user);

            $session = new Session();
            $session->id = uniqid();
            $session->userId = $user->id;

            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $_POST['name'] = "";
            $this->userController->postUpdateProfile();

            $this->expectOutputRegex("[Profile]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[blqs2103]");
            $this->expectOutputRegex("[Name]");
            $this->expectOutputRegex("[Nama wajib diisi !]");

            
        }

        public function testUpdatePassword()
        {
            $user = new User();
            $user->id = 'blqs2103';
            $user->name = 'Balqis Farah Anabila';
            $user->password = password_hash('blqs2103', PASSWORD_BCRYPT);

            $this->userRepository->save($user);

            $session = new Session();
            $session->id = uniqid();
            $session->userId = $user->id;

            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $this->userController->updatePassword();

            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[blqs2103]");
        }

        public function testPostUpdatePasswordSuccess()
        {
            $user = new User();
            $user->id = 'blqs2103';
            $user->name = 'Balqis Farah Anabila';
            $user->password = password_hash('blqs2103', PASSWORD_BCRYPT);

            $this->userRepository->save($user);

            $session = new Session();
            $session->id = uniqid();
            $session->userId = $user->id;

            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $_POST['oldPassword'] = "blqs2103";
            $_POST['newPassword'] = "balqis2103";

            $this->userController->postUpdatePassword();

            $this->expectOutputRegex("[Location: /]");

            $result = $this->userRepository->findById($user->id);
            self::assertTrue(password_verify("balqis2103", $result->password));
        }

        public function testPostUpdatePasswordValidationError()
        {
            $user = new User();
            $user->id = 'blqs2103';
            $user->name = 'Balqis Farah Anabila';
            $user->password = password_hash('blqs2103', PASSWORD_BCRYPT);

            $this->userRepository->save($user);

            $session = new Session();
            $session->id = uniqid();
            $session->userId = $user->id;

            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $_POST['oldPassword'] = "blqs2103";
            $_POST['newPassword'] = "";

            $this->userController->postUpdatePassword();

            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[blqs2103]");
            $this->expectOutputRegex("[Id, Old Password, dan New Password wajib diisi !]");
        }

        public function testPostUpdatePasswordWrongOldPassword()
        {
            $user = new User();
            $user->id = 'blqs2103';
            $user->name = 'Balqis Farah Anabila';
            $user->password = password_hash('blqs2103', PASSWORD_BCRYPT);

            $this->userRepository->save($user);

            $session = new Session();
            $session->id = uniqid();
            $session->userId = $user->id;

            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $_POST['oldPassword'] = "salah";
            $_POST['newPassword'] = "benar";

            $this->userController->postUpdatePassword();

            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[blqs2103]");
            $this->expectOutputRegex("[Password lama salah !]");
        }   

    }

}
