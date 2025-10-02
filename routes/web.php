<?php

use App\Http\Controllers\TaskController;

Route::get('/clear-caches', function() {
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    return "Caches cleared!";
});


Route::get('/', fn() => redirect()->route('tasks.index'));

Route::resource('tasks', TaskController::class);
Route::patch('/tasks/{task}/toggle', [TaskController::class, 'toggle'])->name('tasks.toggle');
Route::post('tasks/reorder', [TaskController::class, 'reorder'])->name('tasks.reorder');
