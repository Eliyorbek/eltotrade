<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'eltotrade',
            'email' => 'elto@eltotrade.com',
            'password' => Hash::make('eltotrade'),
            'phone' => '+998939637074',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $this->command->info('Super Admin created successfully!');
        $this->command->info('Email: elto@eltotrade.com');
        $this->command->info('Password: eltotrade');
    }
}
