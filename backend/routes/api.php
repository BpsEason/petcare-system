<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PetController;
use App\Http\Controllers\HealthRecordController;
use App\Http\Controllers\BehaviorLogController; // 新增

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

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes (requires Sanctum authentication)
Route::group(['middleware' => ['auth:sanctum']], function () {
    // User related
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Pet management
    Route::apiResource('pets', PetController::class);
    Route::post('/pets/{pet}/set-default', [PetController::class, 'setDefaultPet']);
    Route::get('/pets/{pet}/health-records', [HealthRecordController::class, 'indexByPet']);
    Route::get('/pets/{pet}/behavior-logs', [BehaviorLogController::class, 'indexByPet']); // 新增

    // Health Record management
    Route::apiResource('health-records', HealthRecordController::class);

    // Behavior Log management (新增)
    Route::apiResource('behavior-logs', BehaviorLogController::class)->except(['index', 'show']); // show 和 index 會由 pets/{pet}/behavior-logs 處理
});
