<?php

namespace Src\admin\user\domain\value_objects;

class UserName {
    private string $name;

    public function __construct(string $name) {
        // Escribir todas las validaciones necesarias
        if(strlen($name) < 3) {
            throw new \InvalidArgumentException('Name must be at least 3 characters long.');
        }

        // Si pasa todas las validaciones
        $this->name = $name;
    }

    public function value(): string {
        return $this->name;
    }
}