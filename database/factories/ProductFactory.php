<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $category = Category::inRandomOrder()->limit(1)->first('id');
        $status   = Product::STATUS;
        return [
            'name'          => $this->faker->name(),
            'description'   => $this->faker->words(3 ,true),
            'image_path'    => 'Uploads/1667720151_HyperX_Cloud_Earbuds.jpg',
            'category_id'   => $category? $category->id : null,
            'price'         => $this->faker->numberBetween(50, 1000),
            'quantity'      => $this->faker->numberBetween(0, 30),
            'status'        => $status[rand(0, 1)],
        ];
    }
}
