<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/todo', [TaskController::class, 'index'])->name('tasks.index');
Route::post('/todo/tasks', [TaskController::class, 'store'])->name('tasks.store');

Route::get('/todo/tasks/{id}/edit', [TaskController::class, 'edit'])->name('tasks.edit');
Route::put('/todo/tasks/{id}', [TaskController::class, 'update'])->name('tasks.update');
Route::delete('/todo/tasks/{id}', [TaskController::class, 'destroy'])->name('tasks.destroy');

Route::get('/todo/tasks/export', [TaskController::class, 'export'])->name('tasks.export');
Route::post('/todo/tasks/import', [TaskController::class, 'import'])->name('tasks.import');