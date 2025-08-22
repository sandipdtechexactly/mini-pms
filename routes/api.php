<?php

use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\TaskController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Project routes
Route::prefix('projects')->middleware(['auth:sanctum'])->group(function () {
    // Get all projects (with search and filtering)
    Route::get('/', [ProjectController::class, 'index']);
    
    // Create a new project (admin/manager only)
    Route::post('/', [ProjectController::class, 'store']);
    
    // Get a specific project
    Route::get('/{project}', [ProjectController::class, 'show']);
    
    // Update a project (admin/manager only)
    Route::put('/{project}', [ProjectController::class, 'update']);
    
    // Delete a project (admin/owner only)
    Route::delete('/{project}', [ProjectController::class, 'destroy']);
});

// Task routes
Route::prefix('tasks')->middleware(['auth:sanctum'])->group(function () {
    // Get all tasks (with search and filtering)
    Route::get('/', [TaskController::class, 'index']);
    
    // Create a new task
    Route::post('/', [TaskController::class, 'store']);
    
    // Get a specific task
    Route::get('/{task}', [TaskController::class, 'show']);
    
    // Update a task
    Route::put('/{task}', [TaskController::class, 'update']);
    
    // Delete a task
    Route::delete('/{task}', [TaskController::class, 'destroy']);
    
    // Mark task as completed
    Route::patch('/{task}/complete', [TaskController::class, 'markAsCompleted']);
});

// Authentication routes
Route::prefix('auth')->group(function () {
    Route::post('/register', [\App\Http\Controllers\Api\Auth\AuthController::class, 'register']);
    Route::post('/login', [\App\Http\Controllers\Api\Auth\AuthController::class, 'login']);
    
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [\App\Http\Controllers\Api\Auth\AuthController::class, 'logout']);
        Route::get('/user', [\App\Http\Controllers\Api\Auth\AuthController::class, 'user']);
    });
});

// Include authentication routes
require __DIR__.'/auth.php';
