<?php

namespace Src\admin\user\infrastructure\repositories;

use Src\admin\user\domain\contracts\UserRepositoryInterface;
use Src\admin\user\domain\entities\User;
use Src\admin\user\domain\value_objects\UserEmail;
use Src\admin\user\domain\value_objects\UserName;

class MySQLUserRepository implements UserRepositoryInterface {
    public function findById(int $id): ? User {
        // TODO: Aquí la lógica para leer datos directamente de MySQL
        return null;
    }

    public function save(User $user): void {
        // TODO: Aquí la lógica para guardar datos directamente de MySQL
    }
}