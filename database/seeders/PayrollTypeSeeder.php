<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PayrollType;

class PayrollTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PayrollType::updateOrCreate(['name' => 'Ordinaria']);
        PayrollType::updateOrCreate(['name' => 'Extraordinaria']);
    }
}
