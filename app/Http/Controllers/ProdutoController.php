<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProdutoRequest;
use App\Models\Categoria;
use App\Models\Produto;
use Illuminate\Http\Request;

class ProdutoController extends Controller
{
    public function index()
    {
        $q = request('q');
        $categoriaId = request('categoria_id');

        $produtos = Produto::query()
            ->with('categoria')
            ->when($q, function ($query) use ($q) {
                $query->where('nome', 'like', "%{$q}%");
            })
            ->when($categoriaId, function ($query) use ($categoriaId) {
                $query->where('categoria_id', $categoriaId);
            })
            ->orderBy('preco')
            ->paginate(10)
            ->withQueryString();

        $categorias = Categoria::orderBy('nome')->get();

        return view('produtos.index', compact('produtos', 'categorias'));
    }

    public function create()
    {
        $categorias = Categoria::where('ativa', true)->orderBy('nome')->get();
        return view('produtos.create', compact("categorias"));
    }
    public function edit(Produto $produto, Categoria $categorias)
    {
        $categorias = Categoria::where('ativa', true)->orderBy('nome')->get();
        return view('produtos.edit', compact("produto", "categorias"));
    }

    public function store(Request $request)
    {
        // Validação simples por enquanto (Form Request no Cap. 3)
        $dados = $request->validate([
            'nome' => 'required|string|max:100|unique:categorias,nome',
            'descricao' => 'nullable|string|max:500',
            "preco" => "required|numeric",
            "categoria_id" => "required|integer|exists:categorias,id",
            'ativo' => 'nullable|boolean',
        ]);

        $dados['ativa'] = $request->boolean('ativa');

        Produto::create($dados);

        return redirect()->route('produtos.index')
            ->with('sucesso', 'Produto criado com sucesso!');
    }

    public function destroy(Produto $produto)
    {
        $produto->delete();

        return redirect()->route('produtos.index')
            ->with('sucesso', 'Produto excluído com sucesso!');
    }

    public function update(ProdutoRequest $request, Produto $produto)
    {
        $dados["preco"] = number_format($request->preco, "2", ".", "");

        $dados = $request->validate([
            'nome' => 'required|string|max:100|unique:categorias,nome',
            'descricao' => 'nullable|string|max:500',
            "preco" => "required|numeric",
            "categoria_id" => "required|integer|exists:categorias,id",
            'ativo' => 'nullable|boolean',
        ]);

        // Converter checkbox para boolean
        $dados['ativa'] = $request->boolean('ativa');

        $produto->update($dados);

        return redirect()->route('produtos.index')
            ->with('sucesso', 'Produto atualizado com sucesso!');
    }
}
