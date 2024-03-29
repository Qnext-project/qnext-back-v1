<?php

use App\Http\Controllers\DoctorController;
use App\Http\Controllers\ExpertiseController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Clinic;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1')->group(function () {
    Route::post('/login', [UserController::class, 'login']);
    Route::post('socket', [DoctorController::class,'updateSocket']);

    Route::middleware('auth')->group(function(){
        Route::middleware('clinic')->group(function () {
            //admin routes
            Route::prefix('admin')->middleware('auth.admin')->group(function () {
                Route::get('list/full', [DoctorController::class, 'listAd']);
                Route::get('list', [DoctorController::class, 'adminList']);
                Route::post('clinic/edit', function(Request $request){
                    $clinic = $request->session()->get('clinic');
                    $clinic = Clinic::find($clinic->getId());
                    $clinic->update(['name' => $request['name']]);
                    return response()->json($clinic);
                });
                Route::prefix('room')->group(function () {
                    Route::post('', [RoomController::class, 'create']);
                    Route::put('{room}', [RoomController::class, 'edit']);
                    Route::get('', [RoomController::class, 'index']);
                    Route::get('{room}', [RoomController::class, 'show']);
                    Route::delete('{room}', [RoomController::class, 'remove']);
                    Route::post('floor', [RoomController::class, 'createFloor']);
                    Route::put('floor/{room}', [RoomController::class, 'editFloor']);
                    Route::get('floor', [RoomController::class, 'floorList']);
                });
                Route::prefix('doctor')->group(function () {
                    Route::post('', [DoctorController::class, 'create']);
                    Route::put('{user}', [DoctorController::class, 'edit']);
                    Route::get('', [DoctorController::class, 'index']);
                    Route::get('{user}', [DoctorController::class, 'show']);
                    Route::delete('{user}', [DoctorController::class, 'remove']);
                    Route::get('{user}/{room}', [DoctorController::class, 'loginAsDoctor']);
                    Route::post('turn', [DoctorController::class, 'updateTurn']);
                    Route::post('turn/voice', [DoctorController::class, 'getDocVoice']);
                    Route::post('purge', [DoctorController::class, 'purgeAll']);
                });
                Route::prefix('expertise')->group(function () {
                    Route::post('', [ExpertiseController::class, 'create']);
                    Route::put('{expertise}', [ExpertiseController::class, 'edit']);
                    Route::get('', [ExpertiseController::class, 'index']);
                    Route::get('{expertise}', [ExpertiseController::class, 'show']);
                    Route::delete('{expertise}', [ExpertiseController::class, 'remove']);
                });
                Route::prefix('media')->group(function () {
                    Route::get('{type}', [MediaController::class, 'index'])->whereIn('type', ['doctor', 'number', 'room', 'expertise']);
                    Route::post('{type}', [MediaController::class, 'store'])->whereIn('type', ['doctor', 'number', 'room', 'expertise']);
                    Route::delete('{media}', [MediaController::class, 'remove']);
                });
            });
        });
    });
});
