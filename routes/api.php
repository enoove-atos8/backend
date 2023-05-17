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


/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
|
| Routes to authenticate in system and logout
|
*/

Route::post('login', [AuthController::class, 'login'])->name('login');
Route::middleware('auth:sanctum')->controller(AuthController::class)->group(function () {
    Route::post('logout', 'logout');
});



/*
|--------------------------------------------------------------------------
| User routes
|--------------------------------------------------------------------------
|
| Routes to get a user by different ways and register a user, update user data
| and delete user data
*/
Route::middleware('auth:sanctum')->controller(UserController::class)->group(function () {

    Route::get('/users', 'getUsers')->name('users.all');
    Route::get('/users/{id}', 'getUserByID')->name('users.byID')->where('id', '[0-9]+');
    Route::post('/users', [UserController::class, 'store']);

});

/*
|--------------------------------------------------------------------------
| Finance routes
|--------------------------------------------------------------------------
|
|
*/
Route::middleware('auth:sanctum')->controller(UserController::class)->group(function () {

    Route::get('/finance/monthlyBudget', function () {
        return [
            'titleCard'     =>  'Orçamento mensal',
            'totalValue'    =>  '101,12%',
            'variation'     =>  4,
            'chart'         =>  [
                'dataLabels' =>  ['01 Jan', '01 Fev', '01 Mar', '01 Abr'],
                'dataSeries' => [
                    'name'  => 'R$',
                    'data'  => [15521.12, 15519, 15522, 15521]
                ]
            ]
        ];
    });
});
