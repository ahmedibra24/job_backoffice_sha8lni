<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\usersController;
use App\Http\Controllers\companyController;
use App\Http\Controllers\categoryController;
use App\Http\Controllers\jobVacancyController;
use App\Http\Controllers\applicationController;
use App\Http\Controllers\dashboardController;


//* ================================================ shared routes for admin and recruiter ================================================ */
Route::middleware(['auth', 'role:admin,recruiter'])->group(function () {
    //! dashboard
    Route::get('/', [dashboardController::class, 'index'])->name('dashboard');

    //! job vacancies
    Route::resource('job-vacancy', jobVacancyController::class);   
    Route::put('job-vacancy/{category}/restore', [jobVacancyController::class,'restore'])->name('job-vacancy.restore');
    Route::delete('job-vacancy', [jobVacancyController::class,'bulkDestroy'])->name('job-vacancy.bulkDestroy');
    Route::put('job-vacancy', [jobVacancyController::class,'bulkRestore'])->name('job-vacancy.bulkRestore');

    //! applications
    Route::resource('application', applicationController::class);    
    Route::put('application/{category}/restore', [applicationController::class,'restore'])->name('application.restore');
    Route::delete('application', [applicationController::class,'bulkDestroy'])->name('application.bulkDestroy');
    Route::put('application', [applicationController::class,'bulkRestore'])->name('application.bulkRestore');


});

//* ========================================================= company routes only ============================================================= */
Route::middleware(['auth', 'role:recruiter'])->group(function () {
    Route::get('company/my-company', [companyController::class,'show'])->name('my-company.show');
    Route::get('company/my-company/edit', [companyController::class,'edit'])->name('my-company.edit');
    Route::put('company/my-company', [companyController::class,'update'])->name('my-company.update');
});

//* ========================================================= Admin route only ================================================================ */
Route::middleware(['auth', 'role:admin'])->group(function () {
    //! users
    Route::resource('user', usersController::class);
    Route::put('user/{user}/restore', [usersController::class,'restore'])->name('user.restore');
    Route::delete('user', [usersController::class,'bulkDestroy'])->name('user.bulkDestroy');
    Route::put('user', [usersController::class,'bulkRestore'])->name('user.bulkRestore');


    //! company
    Route::resource('company', companyController::class);
    Route::put( 'company/{company}/restore', [companyController::class,'restore'])->name('company.restore');
    Route::delete('company', [companyController::class,'bulkDestroy'])->name('company.bulkDestroy');
    Route::put('company', [companyController::class,'bulkRestore'])->name('company.bulkRestore');

    //! categories
    Route::resource('category', categoryController::class);
    Route::put('category/{category}/restore', [categoryController::class,'restore'])->name('category.restore');
    Route::delete('category', [categoryController::class,'bulkDestroy'])->name('category.bulkDestroy');
    Route::put('category', [categoryController::class,'bulkRestore'])->name('category.bulkRestore');


    });
            
require __DIR__.'/auth.php';
