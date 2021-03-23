<?php

namespace Database\Factories;

use App\Models\Account;
use Illuminate\Database\Eloquent\Factories\Factory;

class AccountFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Account::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'app_id' => $this->getAppID(),
            'user_id' => \App\Models\User::orderBy('created_at', 'asc')->first()['id'],
            'username' => $this->faker->userName,
            'email' => $this->faker->safeEmail,
            'password' => bcrypt('password'),
            'description' => $this->faker->text(120)
        ];
    }

    public function getAppID(){
        $apps = ['instagram', 'facebook', 'clash-of-clans', 'clash-royale'];
        $app = \App\Models\Application::whereSlug($apps[rand(0, count($apps) - 1)])->first();

        return $app->id;
    }
}
