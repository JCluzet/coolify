<?php

use App\Http\Controllers\InertiaController;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [InertiaController::class, 'dashboard'])->name('next_dashboard');

    Route::get('/projects', [InertiaController::class, 'projects'])->name('next_projects');
    Route::get('/projects/{project_uuid}', [InertiaController::class, 'project'])->name('next_project');
    Route::get('/projects/{project_uuid}/environments/{environment_uuid}', [InertiaController::class, 'environment'])->name('next_environment');

    Route::get('/servers', [InertiaController::class, 'servers'])->name('next_servers');
    Route::get('/servers/{server_uuid}', [InertiaController::class, 'server'])->name('next_server');
    Route::post('/servers/{server_uuid}', [InertiaController::class, 'storeServer'])->name('next_store_server');
});

Route::any('/{any}', function () {
    if (auth()->user()) {
        return redirect(RouteServiceProvider::HOME);
    }

    return redirect()->route('login');
})->where('any', '.*');
