<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            ['name' => 'Gram', 'symbol' => 'g'],
            ['name' => 'Kilogram', 'symbol' => 'kg'],
            ['name' => 'Pieces', 'symbol' => 'pcs'],
            ['name' => 'Pack', 'symbol' => 'pack'],
            ['name' => 'Dus', 'symbol' => 'dus'],
            ['name' => 'Karton', 'symbol' => 'ktn'],
            ['name' => 'Liter', 'symbol' => 'l'],
            ['name' => 'Milliliter', 'symbol' => 'ml'],
            ['name' => 'Dozen', 'symbol' => 'dzn'],
            ['name' => 'Botol', 'symbol' => 'btl'],
            ['name' => 'Kaleng', 'symbol' => 'klg'],
            ['name' => 'Sachet', 'symbol' => 'sct'],
        ];

        foreach ($units as $unit) {
            Unit::create($unit);
        }
    }
}

