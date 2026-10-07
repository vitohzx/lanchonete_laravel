<?php
 
use App\Http\Controllers\ContatoController;
use App\Http\Controllers\SobreController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\ItemPedidoController;
use App\Http\Controllers\CepController;
 
 
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/sobre', [SobreController::class, 'index'])->name('sobre');
Route::get('/contato', [ContatoController::class, 'index'])->name('contato');
Route::resource('categorias', CategoriaController::class);
Route::resource('produtos', ProdutoController::class);
 
// Autenticação
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register.form')->middleware('guest');
Route::post('/register', [AuthController::class, 'register'])->name('register')->middleware('guest');
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.form')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->name('login')->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');
 
// CEP API
Route::get('/cep/{cep}', [CepController::class, 'show'])->name('cep.show');
 
 
// Área Autenticada
Route::middleware(['auth'])->group(function () {
    Route::view('/dashboard', 'dashboard')->name('dashboard');
    Route::resource('produtos', ProdutoController::class);
    
    // Perfil
    Route::get('/minha-conta', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/minha-conta', [ProfileController::class, 'update'])->name('profile.update');
 
    // Somente Gerente
    Route::group(['middleware' => [
        function ($request, $next) {
            abort_unless(auth()->user()?->role == 'gerente', 403);
            return $next($request);
        }
    ]], function () {
        Route::resource('categorias', CategoriaController::class);
    });
 
    // PEDIDOS (Relatórios, Status e Resource)
    Route::get('/pedidos/relatorio-dia', [PedidoController::class, 'relatorioDia'])->name('pedidos.relatorioDia');
    Route::patch('pedidos/{pedido}/status', [PedidoController::class, 'atualizarStatus'])->name('pedidos.atualizarStatus');
    Route::resource('pedidos', PedidoController::class); // Já cria index, create, store, edit, update, destroy
    
    // ITENS DO PEDIDO (AJAX)
    Route::post('pedidos/{pedido}/itens-json', [ItemPedidoController::class, 'storeJson'])
        ->name('pedidos.itens.storeJson');
    Route::patch('pedidos/{pedido}/itens-json/{itemPedido}/decrease', [ItemPedidoController::class, 'decreaseJson'])
        ->name('pedidos.itens.decreaseJson');
    Route::patch('pedidos/{pedido}/itens-json/{itemPedido}/increase', [ItemPedidoController::class, 'increaseJson'])
        ->name('pedidos.itens.increaseJson');
    Route::delete('pedidos/{pedido}/itens-json/{itemPedido}', [ItemPedidoController::class, 'destroyJson'])
        ->name('pedidos.itens.destroyJson');
    Route::patch('pedidos/{pedido}/itens-json/{itemPedido}', [ItemPedidoController::class, 'updateJson'])
        ->name('pedidos.itens.updateJson');
});
 