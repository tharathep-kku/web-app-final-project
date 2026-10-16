<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ReturnUnitController;
use App\Http\Controllers\UserPostController;
use App\Http\Controllers\AdminController;

Route::get('/', [ItemController::class, 'home'])->name('home');
Route::get('/archive', [ItemController::class, 'archive'])->name('archive.home');

Route::get('/search', [ItemController::class, 'search'])->name('search');
Route::delete('/search/history', [ItemController::class, 'clearHistory'])->name('search.history.clear');
Route::delete('/search/history/{index}', [ItemController::class, 'clearHistoryItem'])->whereNumber('index')->name('search.history.clear-one');
Route::middleware('auth')->get('/items/{id}', [ItemController::class, 'show'])->name('item.show');
Route::get('/return-units/{returnUnit}', [ReturnUnitController::class, 'show'])->name('return-units.show');
Route::get('location', [ReturnUnitController::class, 'index'])->name('location');

Route::middleware('auth')->group(function () {
    Route::get('/posts/{id}/edit', [UserPostController::class, 'edit'])->whereNumber('id')->name('posts.edit');
    Route::put('/posts/{id}', [UserPostController::class, 'update'])->whereNumber('id')->name('posts.update');
    Route::delete('/posts/{id}', [UserPostController::class, 'destroy'])->whereNumber('id')->name('posts.destroy');
    Route::put('/posts/{id}/evidence', [UserPostController::class, 'submitEvidence'])->whereNumber('id')->name('posts.evidence');
    Route::post('/posts', [ItemController::class, 'store'])->name('posts.store');
    Route::get('/posts/create', [ItemController::class, 'create'])->name('posts.create');
    });

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
    Route::get('/admin/stats', [AdminController::class, 'stats'])->name('admin.stats');
    Route::put('/admin/users/{id}/ban', [AdminController::class, 'toggleBan'])->whereNumber('id')->name('admin.users.ban');
    Route::delete('/admin/{id}', [AdminController::class, 'destroy'])->whereNumber('id')->name('admin.destroy');
    Route::put('/admin/{id}/handover/confirm', [AdminController::class, 'confirmHandover'])->whereNumber('id')->name('admin.handover.confirm');
    Route::put('/admin/{id}/handover/reject', [AdminController::class, 'rejectHandover'])->whereNumber('id')->name('admin.handover.reject');
});

require __DIR__.'/settings.php';
