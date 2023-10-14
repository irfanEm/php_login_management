<?php

namespace Irfanm\Belajar\PHP\MVC\Service;

use Irfanm\Belajar\PHP\MVC\Config\Database;
use Irfanm\Belajar\PHP\MVC\Domain\User;
use Irfanm\Belajar\PHP\MVC\Exception\ValidationException;
use Irfanm\Belajar\PHP\MVC\Model\UserLoginRequest;
use Irfanm\Belajar\PHP\MVC\Model\UserProfileUpdateRequest;
use Irfanm\Belajar\PHP\MVC\Model\UserRegisterRequest;
use Irfanm\Belajar\PHP\MVC\Repository\SessionRepository;
use Irfanm\Belajar\PHP\MVC\Repository\UserRepository;
use PHPUnit\Framework\TestCase;

class UserServiceTest extends TestCase
{
    private UserService $userService;
    private UserRepository $userRepository;
    private SessionRepository $sessionRepository;

    protected function setUp(): void
    {
        $connection = Database::getConnection();
        $this->userRepository = new UserRepository($connection);
        $this->userService = new UserService($this->userRepository);
        $this->sessionRepository = new SessionRepository(Database::getConnection());

        $this->sessionRepository->deleteAll();
        $this->userRepository->deleteAll();
    }

    public function testRegisterSuccess()
    {
        $request = new UserRegisterRequest();
        $request->id = "blqs1";
        $request->name = "Balqis";
        $request->password = "rahasia";

        $response = $this->userService->register($request);

        self::assertEquals($request->id, $response->user->id);
        self::assertEquals($request->name, $response->user->name);
        self::assertNotEquals($request->password, $response->user->password);

        self::assertTrue(password_verify($request->password, $response->user->password));
    }
    public function testRegisterFailed()
    {
        $this->expectException(ValidationException::class);
        $request = new UserRegisterRequest();
        $request->id = "";
        $request->name = "";
        $request->password = "";

        $response = $this->userService->register($request);

    }
    public function testRegisterDuplicate()
    {

        $user = new User();
        $user->id = "blqs1";
        $user->name = "Balqis";
        $user->password = "rahasia";

        $this->userRepository->save($user);

        $this->expectException(ValidationException::class);

        $request = new UserRegisterRequest();
        $request->id = "blqs1";
        $request->name = "Balqis";
        $request->password = "rahasia";

        $this->userService->register($request);

    }

    public function testLoginNotFound()
    {
        $this->expectException(ValidationException::class);
        $request = new UserLoginRequest();
        $request->id = "test";
        $request->password = "rahasia";

        $this->userService->login($request);
    }

    public function testLoginWrongPassword()
    {
        $user = new User();
        $user->id = "tes13";
        $user->name = "Testing";
        $user->password = password_hash("rahasia", PASSWORD_BCRYPT);

        $this->expectException(ValidationException::class);
        $request = new UserLoginRequest();
        $request->id = "tes13";
        $request->password = "nggarahasia";

        $this->userService->login($request);
    }

    public function testLoginSuccess()
    {
        $user = new User();
        $user->id = "tes13";
        $user->name = "Testing";
        $user->password = password_hash("rahasia", PASSWORD_BCRYPT);

        $this->expectException(ValidationException::class);
        $request = new UserLoginRequest();
        $request->id = "tes13";
        $request->password = "rahasia";

        $response = $this->userService->login($request);
        self::assertEquals($response->user->id, $request->id);
        self::assertTrue(password_verify($request->password, $response->user->password));
    }

    public function testUpdateSuccess()
    {
        $user = new User();
        $user->id = "tes13";
        $user->name = "Testing";
        $user->password = password_hash("rahasia", PASSWORD_BCRYPT);
        $this->userRepository->save($user);
        
        $request = new UserProfileUpdateRequest();
        $request->id = "tes13";
        $request->name = "Fulan123";
        $this->userService->updateProfile($request);

        $result = $this->userRepository->findById($user->id);

        self::assertEquals($request->name, $result->name);

    }

    public function testUpdateValidationError()
    {
        $this->expectException(ValidationException::class);

        $request = new UserProfileUpdateRequest();
        $request->id = "";
        $request->name = "";
        $this->userService->updateProfile($request);
    }

    public function testUpdateNotFound()
    {
        $this->expectException(ValidationException::class);
        
        $request = new UserProfileUpdateRequest();
        $request->id = "tes13";
        $request->name = "Fulan123";
        $this->userService->updateProfile($request);
    }
}
