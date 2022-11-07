<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $status = Category::STATUS;
        return [
            'name'          => $this->faker->name(),
            'description'   => $this->faker->words(3 ,true),
            'status'        => $status[rand(0, 1)],
        ];
    }
}
