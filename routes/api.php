<?php

use App\Http\Controllers\Api\ProjectController;
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

// Include authentication routes
require __DIR__.'/auth.php';
