<?php

namespace Database\Factories;

use App\Models\Service;
use App\Models\User;
use App\Models\UserType;
use App\Models\Worker;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Service::class;

    protected function withFaker()
    {
        return \Faker\Factory::create('pt_BR');
    }

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {

        return [
            'title' => $this->faker->sentence(2),
            'description' => $this->faker->sentence(4),
            'value' => $this->faker->randomFloat(2, 10, 1000)
        ];
    }
}
