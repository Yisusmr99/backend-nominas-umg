<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\ContractType;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->call([
            RoleSeeder::class
        ]);

        User::firstOrCreate(
            ['email' => 'superadmin@example.com'],
            [
            'username' => 'superadmin',
            'name' => 'Super',
            'last_name' => 'Admin',
            'password' => bcrypt('password'),
            'role_id' => 1,
            'is_active' => 1,
            ]
        );
        
        ContractType::firstOrCreate(
            [ 'name' => 'Quincenal' ],
            [
                'name' => 'Quincenal',
            ]
        );
    }
}
