<?php

namespace Src\admin\user\infrastructure\repositories;

use App\Models\User as EloquentUser;
use Src\admin\user\domain\contracts\UserRepositoryInterface;
use Src\admin\user\domain\entities\User;
use Src\admin\user\domain\value_objects\UserEmail;
use Src\admin\user\domain\value_objects\UserName;

class EloquentUserRepository implements UserRepositoryInterface {
    public function findById(int $id): ? User {
        $user = EloquentUser::find($id);
        
        if(!$user) return null;
        
        return new User(
            $user->id,
            new UserName($user->name),
            new UserEmail($user->email)
        );
    }

    public function save(User $user): void {
        EloquentUser::updateOrCreate(
            ['id' => $user->id()],
            ['username' => $user->name()->value()],
            ['email' => $user->email()->value()]
        );
    }
}