<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('services')->insert([
            "name" => 'Parking'
        ]);
        DB::table('services')->insert([
            "name" => 'Cafeteria'
        ]);
        DB::table('services')->insert([
            "name" => 'Enfermeria'
        ]);
        DB::table('services')->insert([
            "name" => 'Tienda'
        ]);
        DB::table('services')->insert([
            "name" => 'Wifi'
        ]);
        DB::table('services')->insert([
            "name" => 'Vestuarios'
        ]);

        DB::table('clubs')->insert([
            "name" => 'Admin',
            "password" => Hash::make('Administrador1*'),
            "email" => 'admin@admin.com'
        ]);

        DB::table('users')->insert([
            "name" => 'Admin',
            "password" => Hash::make('Administrador1*'),
            "email" => 'admin@admin.com'
        ]);
    }
}
