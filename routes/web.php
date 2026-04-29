<?php

use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ContatoController;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\SobreController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/sobre', [SobreController::class, 'index'])->name('sobre');
Route::get("/contato", [ContatoController::class, "index"])->name("contato");

route::resource("categorias", CategoriaController::class);

route::resource("produtos", ProdutoController::class);