<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.process');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::controller(DashboardController::class)->group(function () {
        Route::get('/dashboard', 'index')->name('dashboard');
        Route::get('/mata-kuliah', 'mataKuliah')->name('mata-kuliah.index');
        Route::get('/ruang', 'ruang')->name('ruang.index');
        Route::get('/dosen', 'dosen')->name('dosen.index');
        Route::get('/waktu', 'waktu')->name('waktu.index');
        Route::get('/generate-jadwal', 'generateJadwal')->name('generate-jadwal.index');
        Route::post('/generate-jadwal/proses', 'prosesGenerateJadwal')->name('generate-jadwal.proses');
        Route::get('/kalender', 'kalender')->name('kalender.index');
    });
});
