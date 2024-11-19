<?php

use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PermissionController;


use Illuminate\Support\Facades\Route;


Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/invoices', [InvoiceController::class, 'upload'])->can('upload-invoices');
    Route::get('/invoices', [InvoiceController::class, 'index'])->can('view-invoices');
});

// Route::post('/login', [AuthController::class, 'login']);
// Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// Route::middleware(['auth:sanctum'])->group(function () {
//     Route::get('/users', [UserController::class, 'index'])->middleware('permission:view-users');
//     Route::post('/users', [UserController::class, 'store'])->middleware('permission:create-users');
//     Route::put('/users/{user}', [UserController::class, 'update'])->middleware('permission:edit-users');
//     Route::delete('/users/{user}', [UserController::class, 'destroy'])->middleware('permission:delete-users');
// });

// Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
//     Route::get('/permissions', [PermissionController::class, 'index']);
//     Route::post('/permissions', [PermissionController::class, 'store']);
//     Route::put('/permissions/{permission}', [PermissionController::class, 'update']);
//     Route::delete('/permissions/{permission}', [PermissionController::class, 'destroy']);
//     Route::post('/permissions/assign-to-role/{role}', [PermissionController::class, 'assignToRole']);
//     Route::post('/permissions/assign-to-user/{userId}', [PermissionController::class, 'assignToUser']);
// });
