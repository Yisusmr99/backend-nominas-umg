<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Deduction;

class DeductionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Deduction::firstOrCreate(
            [ 'deduction_name' => 'IGSS' ],
            [
                'deduction_percentage' => '4.83',
                'deduction_fixed_amount' => null,
            ]
        );
        Deduction::firstOrCreate(
            [ 'deduction_name' => 'IRTRA' ],
            [
                'deduction_percentage' => '1.00',
                'deduction_fixed_amount' => null,
            ]
        );Deduction::firstOrCreate(
            [ 'deduction_name' => 'ISR' ],
            [
                'deduction_percentage' => '5.00',
                'deduction_fixed_amount' => null,
            ]
        );
    }
}
