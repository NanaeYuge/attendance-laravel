<?php

namespace Database\Factories;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminFactory extends Factory
{
    /** @var class-string<\App\Models\Admin> */
    protected $model = Admin::class;

    public function definition(): array
    {
        return [
            'name'              => $this->faker->name(),
            'email'             => Str::random(8) . '@example.com',
            'password'          => Hash::make('password'),
            'remember_token'    => Str::random(10),
            'created_at'        => now(),
            'updated_at'        => now(),
        ];
    }
}
