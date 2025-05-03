<?php

namespace Src\admin\user\infrastructure\validators;

use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
{
    public function authorize() {
        return true;
    }

    public function rules() {
        return [
            'id' => 'required',
            'username' => 'required|max:255|min:3',
            'email' => 'required|email|max:255|min:3'
        ];
    }
}