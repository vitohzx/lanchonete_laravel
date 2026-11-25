<?php

namespace Database\Factories;

use App\Models\Categoria;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Categoria>
 */
class CategoriaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nome' => $this->faker->unique()->words(2, true),
            'descricao' => $this->faker->optional()->sentence(8),
            'ativa' => $this->faker->boolean(85),
        ];
    }
}
