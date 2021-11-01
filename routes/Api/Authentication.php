<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\RolesController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('auth')->group( function () {
    Route::get('user', [AuthController::class, 'user']);
    Route::put('user', [AuthController::class, 'update']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('verify', [AuthController::class, 'verify']);
    Route::get('reverify', [AuthController::class, 'reverify']);
    Route::prefix('roles')->group( function () {
        Route::post('/attach', [RolesController::class, 'attach_role']);
        Route::post('/create', [RolesController::class, 'create_role']);
        Route::get('/', [RolesController::class, 'list_roles']);
        Route::post('/permissions/create', [RolesController::class, 'create_permission']);
        Route::get('/permissions', [RolesController::class, 'list_permissions']);
    });
});
