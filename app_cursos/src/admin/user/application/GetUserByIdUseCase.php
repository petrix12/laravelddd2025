<?php

namespace Src\admin\user\application;

use Src\admin\user\domain\contracts\UserRepositoryInterface;
use Src\admin\user\domain\entities\User;

class GetUserByIdUseCase {
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository) {
        $this->userRepository = $userRepository;
    }

    public function __invoke(int $id): ? User {
        return $this->userRepository->findById($id);
    }
}