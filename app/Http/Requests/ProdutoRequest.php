<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProdutoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // por enquanto qualquer usuário pode; autorização entra mais adiante
    }

    public function rules(): array
    {
        // Se estiver editando, o model vem pela rota (route model binding)
        $produtoId = $this->route('produto')?->id;

        return [
            'nome' => [
                'required',
                'string',
                'max:100',
                Rule::unique('categorias', 'nome')->ignore($produtoId),
            ],
            'descricao' => 'nullable|string|max:500',
            "preco" => "required|numeric",
            "categoria_id" => "required|integer|exists:categorias,id",
            'ativo' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'nome.required' => 'Informe o nome do produto.',
            'nome.max' => 'O nome deve ter no máximo :max caracteres.',
            'nome.unique' => 'Já existe uma categoria ou produto com este nome.',
            'descricao.max' => 'A descrição deve ter no máximo :max caracteres.',
        ];
    }
}