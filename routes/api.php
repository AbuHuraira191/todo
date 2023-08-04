<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TodoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
//
//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});


// Auth Routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/verify/{code}', [AuthController::class, 'verify']);




// ToDo Routes
Route::middleware(['auth.jwt'])->group(function () {
    Route::get('todos-list', [ToDoController::class, 'index']);
    Route::post('todos-store', [ToDoController::class, 'store']);
    Route::get('todo-show/{id}', [ToDoController::class, 'show']);
    Route::post('todo-update/{id}', [ToDoController::class, 'update']);
    Route::delete('todo-delete/{id}', [ToDoController::class, 'destroy']);
});
