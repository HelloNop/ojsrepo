<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PublicController;

Route::get('/', [PublicController::class, 'index'])->name('home');
Route::get('/articles', [PublicController::class, 'browse'])->name('articles.index');
Route::get('/articles/{article}', [PublicController::class, 'article'])->name('articles.show');
Route::get('/journals', [PublicController::class, 'journals'])->name('journals.index');
Route::get('/journals/{journal}', [PublicController::class, 'journal'])->name('journals.show');
Route::get('/authors/{author}', [PublicController::class, 'author'])->name('authors.show');
