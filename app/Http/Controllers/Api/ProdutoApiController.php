<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProdutoResource;
use App\Models\Produto;
use Illuminate\Http\Request;

class ProdutoApiController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->query('q');
        $categoriaId = $request->query('categoria_id');

        $query = Produto::query()->with('categoria');

        if ($q) {
            $q = substr($q, 0, 80); // limita tamanho
            $query->where('nome', 'like', "%{$q}%");
        }

        if ($categoriaId) {
            $query->where('categoria_id', (int) $categoriaId);
        }

        // Ordenação segura (whitelist)
        $sort = $request->query('sort', 'nome');
        $dir = strtolower($request->query('dir', 'asc'));

        $allowedSort = ['nome', 'preco', 'created_at'];
        $allowedDir = ['asc', 'desc'];

        if (!in_array($sort, $allowedSort, true))
            $sort = 'nome';
        if (!in_array($dir, $allowedDir, true))
            $dir = 'asc';

        $produtos = $query->orderBy($sort, $dir)->paginate(10);

        return ProdutoResource::collection($produtos);
    }

    public function show(Produto $produto)
    {
        $produto->load('categoria');
        return new ProdutoResource($produto);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nome' => 'required|string|max:120',
            'preco' => 'required|numeric|min:0',
            'categoria_id' => 'required|exists:categorias,id',
        ]);

        $produto = Produto::create($data);
        $produto->load('categoria');

        return (new ProdutoResource($produto))
            ->response()
            ->setStatusCode(201);
    }

    public function update(Request $request, Produto $produto)
    {
        $data = $request->validate([
            'nome' => 'sometimes|required|string|max:120',
            'preco' => 'sometimes|required|numeric|min:0',
            'categoria_id' => 'sometimes|required|exists:categorias,id',
        ]);

        $produto->update($data);
        $produto->load('categoria');

        return new ProdutoResource($produto);
    }

    public function destroy(Produto $produto)
    {
        $produto->delete();
        return response()->json(null, 204);
    }
}