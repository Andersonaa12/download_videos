<?php

use Illuminate\Database\Seeder;
use App\Models\User\User;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'type_id' => 1,
            'name' => 'Usuario Admin',
            'email' => 'admin@stockago.com',
            'email_verified_at' => now(),
            'password' => Hash::make('123'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
