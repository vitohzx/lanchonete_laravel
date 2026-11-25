<?php

namespace Database\Factories;

use App\Models\Produto;
use App\Models\Categoria;
use Illuminate\Database\Eloquent\Factories\Factory;


/**
 * @extends Factory<Produto>
 */
class ProdutoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nome' => $this->faker->unique()->word(),
            'preco' => $this->faker->randomFloat(2, 1, 50),
            'categoria_id' => \App\Models\Categoria::factory(),
            'descricao' => $this->faker->optional()->sentence(12),
            'ativo' => $this->faker->boolean(90),
        ];
    }
}
