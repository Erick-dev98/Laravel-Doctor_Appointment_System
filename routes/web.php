<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\DoctorController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified', 'patient'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::get('/doctor/dashboard', [DoctorController::class, 'loadDoctorDashboard'])
    ->name('doctor-dashboard')
    ->middleware('doctor');

Route::group(['middleware' => 'admin'], function () {
    Route::get('/admin/dashboard', [AdminController::class, 'loadAdminDashboard'])
        ->name('admin-dashboard');

    Route::get('/admin/doctors', [AdminController::class, 'loadDoctorListing'])
        ->name('admin-doctors');

    Route::get('/admin/create/doctors', [AdminController::class, 'loadDoctorForm']);

    Route::get('/admin/specialities', [AdminController::class, 'loadAllSpecialities'])
        ->name('admin-specialities');

    // specialities   
    Route::get('/admin/create/speciality', [AdminController::class, 'loadSpecialityForm']);

    // Editing Speciality
    Route::get('/edit/speciality/{speciality}', [AdminController::class, 'loadEditSpecialityForm']);

    // Deleting a speciality
    Route::get('/admin/create/speciality', [AdminController::class, 'loadSpecialityForm']);
});

require __DIR__ . '/auth.php';
