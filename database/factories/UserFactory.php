<?php

namespace Database\Factories;

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
            'phone' => fake()->optional()->phoneNumber(),
            'email_verified_at' => fake()->optional()->dateTime(),
            'password' => Hash::make('password123'),
            'role' => fake()->randomElement(['admin', 'user', 'moderator']),
            'status' => fake()->randomElement(['active', 'inactive', 'suspended']),
            'date_of_birth' => fake()->optional()->date('Y-m-d', '-18 years'),
            'gender' => fake()->optional()->randomElement(['male', 'female', 'other']),
            'address' => fake()->optional()->address(),
            'avatar' => null,
            'last_login_at' => fake()->optional()->dateTimeBetween('-1 month', 'now'),
            'remember_token' => Str::random(10),
        ];
    }
 public function admin()
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'admin',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
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
