<?php

namespace Database\Factories;

use App\Models\Parcel;
use App\Models\Person;
use Illuminate\Database\Eloquent\Factories\Factory;

class ParcelFactory extends Factory
{
    protected $model = Parcel::class;

    public function definition(): array
    {
        return [
            'sender_id' => Person::factory(),
            'receiver_id' => Person::factory(),
            'weight' => fake()->randomFloat(2, 0.1, 50),
            'length' => fake()->randomFloat(2, 1, 100),
            'width' => fake()->randomFloat(2, 1, 100),
            'height' => fake()->randomFloat(2, 1, 100),
            'tracking_code' => fake()->numerify('608850##############'), // فرمت کد رهگیری پستی ایرانی
        ];
    }
}
