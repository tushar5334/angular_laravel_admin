<?php

namespace Database\Factories;

use App\Models\Admin\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $userType = ['SuperAdmin', 'Admin', 'User'];
        $status = [0, 1];
        return [
            'name' => $this->faker->name(),
            //'email' => $this->faker->unique()->safeEmail(),
            'email' => 'admin@admin.com',
            'user_type' => 'SuperAdmin',
            //'user_type' => $userType[array_rand($userType)],
            'password' => 'password', // password

            //'status' => $status[array_rand($status)],
            'status' => 1,
            'token' => Str::random(10),
            //'profile_picture' =>  $this->faker->imageUrl(640, 480, 'cats'),
            'created_by' => 1,
            'updated_by' => 1,
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
            'created_at' => now(),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return static
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }
}
