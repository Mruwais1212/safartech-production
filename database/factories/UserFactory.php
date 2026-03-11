<?php

namespace Database\Factories;

use App\Enums\UserType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'phone' => '508'.rand(100000, 999999),
            'phone_code' => '966',
            'photo' => 'default_user.png',
            'activate' => 1,
            'activation_code' => mt_rand(1000, 9999),
            'token' => Str::random(60),
            'user_type_id' => UserType::CLIENT,
            'receive_notification' => 1,
            'lang' => 'en',
            'last_login' => now(),
            'group_privilege_id' => null,
            'gender' => 'male',
            'block' => 0,
            'player_position' => rand(1, 4),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
