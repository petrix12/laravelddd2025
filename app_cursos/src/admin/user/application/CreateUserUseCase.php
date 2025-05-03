<?php

namespace Src\admin\user\application;

use Src\admin\user\domain\contracts\UserRepositoryInterface;
use Src\admin\user\domain\entities\User;
use Src\admin\user\domain\value_objects\UserEmail;
use Src\admin\user\domain\value_objects\UserName;

class CreateUserUseCase {
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository) {
        $this->userRepository = $userRepository;
    }

    public function execute(int $id, string $name, string $email) {
        $nameValueObject = new UserName($name);
        $emailValueObject = new UserEmail($email);
        $user = new User($id, $nameValueObject, $emailValueObject);
        $this->userRepository->save($user);
    }
}
