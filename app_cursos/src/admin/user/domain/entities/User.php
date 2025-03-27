<?php

namespace Src\admin\user\domain\entities;

use Src\admin\user\domain\value_objects\UserName;
use Src\admin\user\domain\value_objects\UserEmail;

// Generación de la entidad
class User {
    private int $id;
    private UserName $name;
    private UserEmail $email;

    public function __construct(int $id, UserName $name, UserEmail $email) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
    }

    public function name(): UserName {
        return $this->name;
    }

    public function email(): UserEmail {
        return $this->email;
    }
}