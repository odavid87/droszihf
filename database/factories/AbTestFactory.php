<?php

namespace Database\Factories;

use App\Models\AbTest;
use Illuminate\Database\Eloquent\Factories\Factory;

class AbTestFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AbTest::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->sentence,
            'status' => $this->faker->randomElement([AbTest::STATUS_READY, AbTest::STATUS_STARTED, AbTest::STATUS_STOPPED]),
        ];
    }

    public function started(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => AbTest::STATUS_STARTED
        ]);
    }
}
