<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Bonu;

class BonuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Bonu::firstOrCreate(
            [ 'bonu_name' => 'Bono Incentivo Decreto 37-2001' ],
            [
                'bonu_percentage' => null,
                'bonu_fixed_amount' => '250.00',
            ]
        );
    }
}
