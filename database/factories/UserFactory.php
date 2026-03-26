<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use App\Models\Country;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'        => fake()->name(),
            'email'       => fake()->unique()->safeEmail(),
            'password'    => Hash::make('123456'),
            'national_id' => fake()->unique()->numerify('2##########'),
            'avatar_image'=> null,
            'country_id'     => null,
            'gender'      => fake()->randomElement(['male', 'female']),
            'mobile'      => fake()->phoneNumber(),
            'is_approved' => false,
            'is_banned'   => false,
            'approved_by' => null,
            'created_by'  => null,
            'last_login_at'=> null,
        ];
    }

    // ── States ─────────────────────────────────────

    public function approved(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_approved' => true,
        ]);
    }


    public function pending(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_approved' => false,
        ]);
    }

    public function banned(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_banned' => true,
        ]);
    }
    
    public function assignCountryToClients(): static
    {
        return $this->state(fn(array $attributes) => [
            'country_id' => Country::inRandomOrder()->value('id'),
        ]);
    }
}