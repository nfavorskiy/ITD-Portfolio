<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\User;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::post('/check-name-availability', function (Request $request) {
    $name = (string) $request->input('name'); // Force to string
    
    // Additional validation to ensure we have a valid name
    if (empty(trim($name))) {
        return response()->json(['available' => true]);
    }
    
    $exists = User::where('name', $name)->exists();
    
    return response()->json([
        'available' => !$exists
    ]);
})->name('check.name.availability');

Route::post('/check-email-availability', function (Request $request) {
    $email = (string) $request->input('email');
    
    if (empty(trim($email))) {
        return response()->json(['available' => true]);
    }
    
    $exists = User::where('email', $email)->exists();
    
    return response()->json([
        'available' => !$exists
    ]);
})->name('check.email.availability');

// Only authenticated users can access these routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('posts', PostController::class);
    Route::get('/api/posts', [App\Http\Controllers\PostController::class, 'apiIndex'])->name('posts.api');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/settings', [ProfileController::class, 'settings'])->name('profile.settings'); // <-- Add this
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Test routes for error pages (remove in production)
Route::get('/test-403', function () {
    return response()->view('errors.403', [], 403);
})->name('test.403');

Route::get('/test-404', function () {
    abort(404);
})->name('test.404');

Route::get('/test-500', function () {
    abort(500);
})->name('test.500');

require __DIR__.'/auth.php';
