<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ProjectController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Task Routes
    // For Admin/Manager
    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
    
    // For Developers
    Route::get('/my-tasks', [TaskController::class, 'myTasks'])->name('tasks.my');
    
    // Project Routes
    Route::resource('projects', ProjectController::class);
});

require __DIR__.'/auth.php';
