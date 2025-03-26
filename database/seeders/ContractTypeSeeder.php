<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ContractType;

class ContractTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ContractType::updateOrCreate(['name' => 'Semanal']);
        ContractType::updateOrCreate(['name' => 'Quincenal']);
        ContractType::updateOrCreate(['name' => 'Mensual']);
    }
}
