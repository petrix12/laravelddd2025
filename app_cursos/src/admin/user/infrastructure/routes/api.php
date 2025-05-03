<?php

use Src\admin\user\infrastructure\controllers\CreateUserPOSTController;
use Src\admin\user\infrastructure\controllers\GetUserByIdGETController;

Route::get('/{id}', [GetUserByIdGETController::class, 'index']);
Route::get('/store', [CreateUserPOSTController::class, 'index']);
