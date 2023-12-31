<?php

namespace Irfanm\Belajar\PHP\MVC\Service;

use Exception;
use Irfanm\Belajar\PHP\MVC\Config\Database;
use Irfanm\Belajar\PHP\MVC\Domain\User;
use Irfanm\Belajar\PHP\MVC\Exception\ValidationException;
use Irfanm\Belajar\PHP\MVC\Model\UserLoginRequest;
use Irfanm\Belajar\PHP\MVC\Model\UserLoginResponse;
use Irfanm\Belajar\PHP\MVC\Model\UserPasswordUpdateRequest;
use Irfanm\Belajar\PHP\MVC\Model\UserPasswordUpdateResponse;
use Irfanm\Belajar\PHP\MVC\Model\UserProfileUpdateRequest;
use Irfanm\Belajar\PHP\MVC\Model\UserProfileUpdateResponse;
use Irfanm\Belajar\PHP\MVC\Model\UserRegisterRequest;
use Irfanm\Belajar\PHP\MVC\Model\UserRegisterResponse;
use Irfanm\Belajar\PHP\MVC\Repository\UserRepository;

class UserService
{
    private UserRepository $userRepository;

    /**
     * Class constructor.
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function register(UserRegisterRequest $request): UserRegisterResponse
    {
        $this->validateUserRegistrationRequest($request);

        try {
            Database::beginTransaction();
            $user = $this->userRepository->findById($request->id);
            if($user != null){
                throw new ValidationException("User Id sudah ada !");
            }

            $user = new User();
            $user->id = $request->id;
            $user->name = $request->name;
            $user->password = password_hash($request->password, PASSWORD_BCRYPT);

            $this->userRepository->save($user);

            $response = new UserRegisterResponse();
            $response->user = $user;

            Database::commitTransactiion();
            return $response;

        }catch(Exception $exception){
            Database::rollbackTransaction();
            throw $exception;
        }

    }

    private function validateUserRegistrationRequest(UserRegisterRequest $request)
    {
        if($request->id == null || $request->name == null || $request->password == null || 
        trim($request->id) == "" || trim($request->name) == "" || trim($request->password) == "")
        {
            throw new ValidationException("Id, Name, dan Password wajib diisi !");
        }
    }

    public function login(UserLoginRequest $request): UserLoginResponse
    {
        $this->validateUserLoginRequest($request);

        $user = $this->userRepository->findById($request->id);
        if($user == null)
        {
            throw new ValidationException("Id atau password salah.");
        }

        if(password_verify($request->password, $user->password))
        {
            $response = new UserLoginResponse();
            $response->user = $user;
            return $response;
        }else{
            throw new ValidationException("Id atau password salah.");
        }
    }

    private function validateUserLoginRequest(UserLoginRequest $request)
    {
        if($request->id == null || $request->password == null || 
        trim($request->id) == "" || trim($request->password) == "")
        {
            throw new ValidationException("Id dan Password wajib diisi !");
        }
    }

    public function updateProfile(UserProfileUpdateRequest $request): UserProfileUpdateResponse
    {
        $this->validateUserProfileUpdateRequest($request);

        try{
            Database::beginTransaction();
            
            $user = $this->userRepository->findById($request->id);
            if($user == null) {
                throw new ValidationException("User tidak ditemukan !");
            }

            $user->name = $request->name;
            $this->userRepository->update($user);

            Database::commitTransactiion();

            $response = new UserProfileUpdateResponse();
            $response->user = $user;
            return $response;

        }catch(\Exception $exception){
            Database::rollbackTransaction();
            throw $exception;
        }
    }

    private function validateUserProfileUpdateRequest(UserProfileUpdateRequest $request)
    {
        if($request->id == null || $request->name == null || 
        trim($request->id) == "" || trim($request->name) == "")
        {
            throw new ValidationException("Nama wajib diisi !");
        }
    }

    public function updatePassword(UserPasswordUpdateRequest $request): UserPasswordUpdateResponse
    {
        $this->validationUserPasswordUpdateRequest($request);

        try{
            Database::beginTransaction();

            $user = $this->userRepository->findById($request->id);
            if ($user == null) {
                throw new ValidationException("User Id tidak ditemukan !");
            }

            if(!password_verify($request->oldPassword, $user->password)) {
                throw new ValidationException("Password lama salah !");
            }

            $user->password = password_hash($request->newPassword, PASSWORD_BCRYPT);
            $this->userRepository->update($user);

            Database::commitTransactiion();

            $response = new UserPasswordUpdateResponse();
            $response->user = $user;
            return $response;
            
        }catch(\Exception $exception){
            Database::rollbackTransaction();
            throw $exception;
        }

    }

    private function validationUserPasswordUpdateRequest(UserPasswordUpdateRequest $request)
    {
        if($request->id == null || $request->oldPassword == null || $request->newPassword == null || 
        trim($request->id) == "" || trim($request->oldPassword) == "" || trim($request->newPassword) == "")
        {
            throw new ValidationException("Id, Old Password, dan New Password wajib diisi !");
        }
    }
}
