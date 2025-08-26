<?php

use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');
});

Route::prefix('admin')
    ->middleware(['auth'])
    ->as('admin.')
    ->group(function () {
        Route::resource('users', UserController::class)
            ->parameters(['users' => 'user'])
            ->except(['create','edit']);
    });


Route::view('/api/docs', 'api-swagger');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
