<?php

namespace Src\admin\user\infrastructure\controllers;

use App\Http\Controllers\Controller;
use Src\admin\user\application\GetUserByIdUseCase;
use Src\admin\user\infrastructure\repositories\EloquentUserRepository;

final class GetUserByIdGETController extends Controller { 
    public function index($id) {
        //$eloquentUserRepository = new EloquentUserRepository();
        $getUserByIdUseCase = new GetUserByIdUseCase(new EloquentUserRepository());
        $user = $getUserByIdUseCase($id);

        return response()->json([
            'status' => true,
            'data' => $user,
            'message' => 'success'
        ]);
    }
}