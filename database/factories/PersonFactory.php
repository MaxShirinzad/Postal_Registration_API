<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PersonFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'mobile' => '09' . fake()->numerify('########'),
            'postal_code' => fake()->numerify('##########'),
            'address' => fake()->address(),
        ];
    }
}