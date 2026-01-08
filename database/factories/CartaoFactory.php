<?php

namespace Database\Factories;

use App\Enums\StatusCartao;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cartao>
 */
class CartaoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'saldo' => 0,
            'status' => $this->faker->randomElement([StatusCartao::ATIVO,StatusCartao::BLOQUEADO,StatusCartao::CANCELADO]),
            'number' => $this->faker->randomNumber(),
            'data_validade' => $this->faker->date(),
            'cvv' => $this->faker->randomNumber(3, true),
        ];
    }
}
