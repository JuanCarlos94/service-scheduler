<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

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
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => md5('secret'),
            'cellphone' => $this->faker->numerify('###########'),
            'address' => $this->faker->address,
            'city' => $this->faker->city,
            'state' => $this->faker->stateAbbr(),
            'zip_code' => $this->faker->numerify('########')
        ];
    }
}
