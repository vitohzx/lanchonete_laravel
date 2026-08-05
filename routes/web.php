<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ContatoController;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SobreController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/sobre', [SobreController::class, 'index'])->name('sobre');
Route::get("/contato", [ContatoController::class, "index"])->name("contato");

route::resource("categorias", CategoriaController::class);

route::resource("produtos", ProdutoController::class);



Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register.form')->middleware('guest');
Route::post('/register', [AuthController::class, 'register'])->name('register')->middleware('guest');

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.form')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->name('login')->middleware('guest');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');
 
Route::middleware(['auth'])->group(function () {
// Rota para exibir o formulário
    Route::get('/minha-conta', [ProfileController::class, 'edit'])->name('profile.edit');
// Rota para salvar as alterações
    Route::put('/minha-conta', [ProfileController::class, 'update'])->name('profile.update');
});

Route::middleware(['auth'])->group(function () {
    Route::view('/dashboard', 'dashboard')->name('dashboard');
    Route::resource('produtos', ProdutoController::class);

        Route::group(['middleware' => [
        function ($request, $next) {
            abort_unless(auth()->user()?->role == 'gerente', 403);
            return $next($request);
        }
    ]], function () {
        Route::resource('categorias', CategoriaController::class);
    });
});
