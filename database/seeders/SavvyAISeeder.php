<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SavvyAISeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::query()->create([
            'name' => 'Admin',
            'email' => 'admin@modcreativeinc.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        /**
         * Add desired Trainable, Chatbot, Agent, and Dialogue seeds here.
         */
    }
}
