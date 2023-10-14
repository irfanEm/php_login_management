<?php

namespace Irfanm\Belajar\PHP\MVC\Repository;

use Irfanm\Belajar\PHP\MVC\Config\Database;
use Irfanm\Belajar\PHP\MVC\Domain\User;
use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertNull;

class UserRepositoryTest extends TestCase
{
    private UserRepository $userRepository;
    private SessionRepository $sessionRepository;

    protected function setUp(): void
    {
        $this->userRepository = new UserRepository(Database::getConnection());
        $this->sessionRepository = new SessionRepository(Database::getConnection());

        $this->sessionRepository->deleteAll();
        $this->userRepository->deleteAll();
    }

    public function testSaveSuccess()
    {
        $user = new User();
        $user->id = "blq1";
        $user->name = "Balqis";
        $user->password = "rahasia";

        $this->userRepository->save($user);

        $result = $this->userRepository->findById($user->id);

        self::assertEquals($user->id, $result->id);
        self::assertEquals($user->name, $result->name);
        self::assertEquals($user->password, $result->password);
    }

    public function findByIdNotFound()
    {
        $user = $this->userRepository->findById("notfound");
        self::assertNull($user);
    }

    public function testUpdate()
    {
        $user = new User();
        $user->id = "blq1";
        $user->name = "Balqis";
        $user->password = "rahasia";

        $this->userRepository->save($user);

        $user->name = "Farah";
        $this->userRepository->update($user);

        $result = $this->userRepository->findById($user->id);

        self::assertEquals($user->id, $result->id);
        self::assertEquals($user->name, $result->name);
        self::assertEquals($user->password, $result->password);


    }

}
