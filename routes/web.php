<?php

use App\Models\TanahTransmigrasi;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TanahController;
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/





Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return view('index');
    });


    Route::get('/data/penduduk', function () {
        $tanahs = TanahTransmigrasi::latest()->get();
        return view('data-penduduk', compact('tanahs'));
    });
    Route::resource('tanah', TanahController::class)->middleware('admin');
    Route::get('/dashboard', function () {
        return view('dashboard.index', [
            'user' => auth()->user(),
        ]);
    })->middleware(['auth', 'verified', 'admin'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
