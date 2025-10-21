<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            ['name' => 'Renteng', 'symbol' => 'rtg'],
            ['name' => 'Pieces', 'symbol' => 'pcs'],
            ['name' => 'Gram', 'symbol' => 'g'],
            ['name' => 'Kilogram', 'symbol' => 'kg'],
            ['name' => '1/2 Kilogram', 'symbol' => '1/2 kg'],
            ['name' => '1/4 Kilogram', 'symbol' => '1/4 kg'],
            ['name' => 'Ons', 'symbol' => 'ons'],
            ['name' => '1/2 Ons', 'symbol' => '1/2 ons'],
            ['name' => 'Pack', 'symbol' => 'pack'],
            ['name' => 'Dus', 'symbol' => 'dus'],
            ['name' => 'Liter', 'symbol' => 'l'],
            ['name' => 'Milliliter', 'symbol' => 'ml'],
            

        ];

        foreach ($units as $unit) {
            Unit::create($unit);
        }
    }
}

