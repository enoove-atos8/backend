<?php

use App\Application\Api\Employees\Controllers\EmployeeController;
use Application\Api\Auth\Controllers\AuthController;
use Application\Api\Users\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
Route::get('/version', function () {
    return [
        'API Version'   =>  '0.1.2',
        'Branch'   =>  'local',
    ];
});

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

//Route::namespace('App\Application\Api\Users\Controllers')->group(function (){
//    Route::get('/', 'UserController@index');
//    Route::post('/', 'UserController@store');
//});



Route::post('login', [AuthController::class, 'login'])->name('login');

Route::middleware('auth:sanctum')->controller(AuthController::class)->group(function () {

    Route::post('logout', 'logout');

});

Route::middleware('auth:sanctum')->controller(UserController::class)->group(function () {
    Route::get('/users', 'index')->name('users.all');

    Route::get('/users/{id}', 'show')->name('users.byID')->where('id', '[0-9]+');
    Route::post('/users', [UserController::class, 'store']);
});

Route::middleware('auth:sanctum')->controller(EmployeeController::class)->group(function () {
    Route::get('/employees', 'index')->name('employees.all');

    Route::get('/employees/{id}', 'show')->name('employees.byID')->where('id', '[0-9]+');
    Route::post('/employees', 'store')->name('employees.store');
});
