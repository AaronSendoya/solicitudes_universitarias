<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentRequestController;
use App\Http\Controllers\ManagerRequestController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TechnicianManagerController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TechnicianRequestController;
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
});

Route::middleware(['auth', 'role:Student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/requests', [StudentRequestController::class, 'index'])->name('requests.index');
    Route::get('/requests/create', [StudentRequestController::class, 'create'])->name('requests.create');
    Route::post('/requests', [StudentRequestController::class, 'store'])->name('requests.store');
    Route::get('/requests/{id}', [StudentRequestController::class, 'show'])->name('requests.show');
});

// Rutas exclusivas para el Gestor
Route::middleware(['auth', 'role:Manager'])->prefix('manager')->name('manager.')->group(function () {
    Route::get('/dashboard', [ReportController::class, 'index'])->name('dashboard');
    Route::get('/requests', [ManagerRequestController::class, 'index'])->name('requests.index');
    Route::get('/requests/{id}', [ManagerRequestController::class, 'show'])->name('requests.show');
    Route::put('/requests/{id}', [ManagerRequestController::class, 'update'])->name('requests.update');

    // Gestión de Técnicos
    Route::get('/technicians', [TechnicianManagerController::class, 'index'])->name('technicians.index');
    Route::get('/technicians/create', [TechnicianManagerController::class, 'create'])->name('technicians.create');
    Route::post('/technicians', [TechnicianManagerController::class, 'store'])->name('technicians.store');
    Route::get('/technicians/{id}/edit', [TechnicianManagerController::class, 'edit'])->name('technicians.edit');
    Route::put('/technicians/{id}', [TechnicianManagerController::class, 'update'])->name('technicians.update');
    Route::patch('/technicians/{id}/toggle', [TechnicianManagerController::class, 'toggleActive'])->name('technicians.toggle');

    // Gestión de Categorías
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{id}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/categories/{id}', [CategoryController::class, 'update'])->name('categories.update');
    Route::patch('/categories/{id}/toggle', [CategoryController::class, 'toggleActive'])->name('categories.toggle');
});

// Rutas exclusivas para el Técnico
Route::middleware(['auth', 'role:Technician'])->prefix('technician')->name('technician.')->group(function () {
    Route::get('/requests', [TechnicianRequestController::class, 'index'])->name('requests.index');
    Route::get('/requests/{id}', [TechnicianRequestController::class, 'show'])->name('requests.show');
    Route::put('/requests/{id}', [TechnicianRequestController::class, 'update'])->name('requests.update');
});

require __DIR__.'/auth.php';
