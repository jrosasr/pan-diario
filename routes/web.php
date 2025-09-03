<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

use App\Http\Controllers\DeliveryPdfController;

use App\Models\Beneficiary;
use Illuminate\Support\Facades\Storage;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

Route::get('/delivery/{id}/pdf', [DeliveryPdfController::class, 'generate'])->name('delivery.pdf');
Route::get('/delivery/{id}/pdf/view', [DeliveryPdfController::class, 'view'])->name('delivery.pdf.view');

require __DIR__.'/auth.php';
