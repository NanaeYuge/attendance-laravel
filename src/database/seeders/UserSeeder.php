<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'taro@example.com'],
            [
                'name'              => '山田 太郎',
                'password'          => Hash::make('password'),
                'email_verified_at' => now(),
                'remember_token'    => Str::random(10),
            ]
        );

        User::updateOrCreate(
            ['email' => 'hanako@example.com'],
            [
                'name'              => '佐藤 花子',
                'password'          => Hash::make('password'),
                'email_verified_at' => now(),
                'remember_token'    => Str::random(10),
            ]
        );

        User::updateOrCreate(
            ['email' => 'jiro@example.com'],
            [
                'name'              => '鈴木 次郎',
                'password'          => Hash::make('password'),
                'email_verified_at' => now(),
                'remember_token'    => Str::random(10),
            ]
        );
    }
}
