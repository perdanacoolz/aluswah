<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [App\Http\Controllers\LandingController::class, 'index'])->name('welcome');

// Public TV Display (No Auth Required)
Route::get('/display', [App\Http\Controllers\DisplayController::class, 'index'])
    ->name('display.index');

// Public Pages
Route::get('/profil/struktur', [App\Http\Controllers\PublicController::class, 'structure'])->name('public.struktur');
Route::get('/profil/tentang', [App\Http\Controllers\PublicController::class, 'tentang'])->name('public.tentang');
Route::get('/transparansi/keuangan', [App\Http\Controllers\PublicController::class, 'keuangan'])->name('public.keuangan');
Route::get('/transparansi/aset', [App\Http\Controllers\PublicController::class, 'aset'])->name('public.aset');
Route::get('/ibadah/jumat', [App\Http\Controllers\PublicController::class, 'jumat'])->name('public.jumat');
Route::get('/ibadah/jadwal', [App\Http\Controllers\PublicController::class, 'jadwal'])->name('public.jadwal');
Route::get('/ibadah/agenda', [App\Http\Controllers\PublicController::class, 'agenda'])->name('public.agenda');
Route::get('/galeri', [App\Http\Controllers\PublicController::class, 'galeri'])->name('public.galeri');

// Public Financial Transparency
Route::get('/keuangan', [App\Http\Controllers\TransactionController::class, 'publicIndex'])
    ->name('keuangan.index');

Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Component showcase
Route::get('/components-showcase', function () {
    return Inertia::render('ComponentShowcase');
})->middleware(['auth'])->name('components.showcase');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // User Management (super_admin only)
    Route::get('/users', [App\Http\Controllers\UserController::class, 'index'])
        ->name('users.index');
    Route::post('/users/{user}/impersonate', [App\Http\Controllers\UserController::class, 'impersonate'])
        ->name('users.impersonate');
    Route::post('/users/stop-impersonation', [App\Http\Controllers\UserController::class, 'stopImpersonation'])
        ->name('users.stopImpersonation');

    // Global Settings
    Route::get('/settings', [App\Http\Controllers\SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [App\Http\Controllers\SettingController::class, 'update'])->name('settings.update');
    
    // Component Showcase
    Route::get('/components-showcase', function () {
        return Inertia::render('ComponentShowcase');
    })->name('components.showcase');
    
    // Transaction Management
    Route::get('/transactions', function () {
        return Inertia::render('Transactions/Index');
    })->name('transactions.index');
    Route::post('/transactions', [App\Http\Controllers\TransactionController::class, 'store'])
        ->name('transactions.store');
    Route::delete('/transactions/{id}', [App\Http\Controllers\TransactionController::class, 'destroy'])
        ->name('transactions.destroy');
    
    // Approval System (Ketua only, enforced by Gates)
    Route::get('/approvals', [App\Http\Controllers\ApprovalController::class, 'index'])
        ->name('approvals.index');
    Route::post('/approvals/{transaction}/approve', [App\Http\Controllers\ApprovalController::class, 'approve'])
        ->name('approvals.approve');
    Route::post('/approvals/{transaction}/reject', [App\Http\Controllers\ApprovalController::class, 'reject'])
        ->name('approvals.reject');
    
    // Slides Management (Marbot/Super Admin)
    Route::get('/slides', [App\Http\Controllers\SlideController::class, 'index'])
        ->name('slides.index');
    Route::post('/slides', [App\Http\Controllers\SlideController::class, 'store'])
        ->name('slides.store');
    Route::post('/slides/{slide}/toggle', [App\Http\Controllers\SlideController::class, 'toggleActive'])
        ->name('slides.toggle');
    Route::delete('/slides/{slide}', [App\Http\Controllers\SlideController::class, 'destroy'])
        ->name('slides.destroy');
    
    // Assets Management (Marbot/Super Admin)
    Route::resource('assets', App\Http\Controllers\AssetController::class);

    // Agenda Management (Marbot/Super Admin)
    Route::resource('agendas', App\Http\Controllers\AgendaController::class)
        ->middleware('can:manage_operations');
});

require __DIR__.'/auth.php';
