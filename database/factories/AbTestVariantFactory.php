<?php

namespace Database\Factories;

use App\Models\AbTest;
use App\Models\AbTestVariant;
use Illuminate\Database\Eloquent\Factories\Factory;

class AbTestVariantFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AbTestVariant::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'ab_test_id' => AbTest::factory(),
            'name' => $this->faker->word,
            'targeting_ratio' => $this->faker->randomFloat(2, 0, 1),
        ];
    }
}
