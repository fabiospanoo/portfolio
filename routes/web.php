<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\PublicController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicController::class, 'projects'])->name('projects');

Route::get('/contact', [PublicController::class, 'contact'])->name('contact');

Route::post('/contact', [PublicController::class, 'sendContact'])->middleware('throttle:contact')->name('contact.send');

Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');

Route::get('/blog/{post:slug}', [BlogController::class, 'show'])->name('blog.show');

Route::get('/sitemap.xml', [PublicController::class, 'sitemap'])->name('sitemap');

/*
|--------------------------------------------------------------------------
| Admin (hidden login, not linked anywhere on the public site)
|--------------------------------------------------------------------------
*/

Route::get('/login', [AuthController::class, 'showLogin'])->name('admin.login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:admin-login')->name('admin.login.submit');

Route::get('/login/2fa', [AuthController::class, 'show2fa'])->name('admin.2fa');
Route::post('/login/2fa', [AuthController::class, 'verify2fa'])->middleware('throttle:admin-2fa')->name('admin.2fa.verify');

Route::get('/login/setup', [AuthController::class, 'showSetup'])->name('admin.setup');
Route::post('/login/setup', [AuthController::class, 'confirmSetup'])->middleware('throttle:admin-2fa')->name('admin.setup.confirm');

Route::post('/logout', [AuthController::class, 'logout'])->name('admin.logout');

Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::get('/projects/create', [ProjectController::class, 'create'])->name('projects.create');
    Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
    Route::get('/projects/{project}/edit', [ProjectController::class, 'edit'])->name('projects.edit');
    Route::put('/projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
    Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');

    Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
    Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::put('/posts/{post}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');

    Route::get('/audit', [ProjectController::class, 'audit'])->name('audit');
});
