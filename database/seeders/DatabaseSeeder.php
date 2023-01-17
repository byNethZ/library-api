<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        // \App\Models\User::factory()->create([

        // ]);
        DB::table('users')->insert([
            'role' => 1,
            'name' => 'John',
            'lastname' => 'Doe',
            'email' => 'admin@library.com',
            'phone' => '1234567890',
            'password' => bcrypt('admin'),
        ]);


    }
}
