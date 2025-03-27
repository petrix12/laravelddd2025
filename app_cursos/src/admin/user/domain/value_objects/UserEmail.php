<?php

namespace Src\admin\user\domain\value_objects;

class UserEmail {
    private string $email;

    public function __construct(string $email) {
        // Escribir todas las validaciones necesarias
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email format.');
        }

        // Si pasa todas las validaciones
        $this->email = $email;
    }

    public function value(): string {
        return $this->email;
    }
}