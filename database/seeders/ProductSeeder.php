<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductUnit;
use App\Models\Stock;
use App\Models\Category;
use App\Models\Unit;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $gramUnit = Unit::where('symbol', 'g')->first();
        $kgUnit = Unit::where('symbol', 'kg')->first();
        $pcsUnit = Unit::where('symbol', 'pcs')->first();
        $packUnit = Unit::where('symbol', 'pack')->first();
        $literUnit = Unit::where('symbol', 'l')->first();
        $mlUnit = Unit::where('symbol', 'ml')->first();
        $sachUnit = Unit::where('symbol', 'sct')->first();

        $sembakoCategory = Category::where('name', 'Sembako')->first();
        $makminCategory = Category::where('name', 'Makanan & Minuman')->first();
        $snackCategory = Category::where('name', 'Snack & Permen')->first();

        // Beras
        $beras = Product::create([
            'name' => 'Beras Premium',
            'barcode' => '1234567890001',
            'category_id' => $sembakoCategory->id,
            'description' => 'Beras premium kualitas terbaik',
            'stock_alert_minimum' => 10000, // 10 kg dalam gram
        ]);

        // Unit untuk beras
        ProductUnit::create([
            'product_id' => $beras->id,
            'unit_id' => $gramUnit->id,
            'price' => 12.50,
            'conversion_rate' => 1,
            'is_base_unit' => true,
        ]);

        ProductUnit::create([
            'product_id' => $beras->id,
            'unit_id' => $kgUnit->id,
            'price' => 12500,
            'conversion_rate' => 1000,
            'is_base_unit' => false,
        ]);

        // Stock awal beras
        Stock::create([
            'product_id' => $beras->id,
            'unit_id' => $kgUnit->id,
            'quantity' => 50, // 50 kg
        ]);

        // Minyak Goreng
        $minyak = Product::create([
            'name' => 'Minyak Goreng Tropical',
            'barcode' => '1234567890002',
            'category_id' => $sembakoCategory->id,
            'description' => 'Minyak goreng berkualitas',
            'stock_alert_minimum' => 2000, // 2 liter dalam ml
        ]);

        ProductUnit::create([
            'product_id' => $minyak->id,
            'unit_id' => $mlUnit->id,
            'price' => 15,
            'conversion_rate' => 1,
            'is_base_unit' => true,
        ]);

        ProductUnit::create([
            'product_id' => $minyak->id,
            'unit_id' => $literUnit->id,
            'price' => 15000,
            'conversion_rate' => 1000,
            'is_base_unit' => false,
        ]);

        Stock::create([
            'product_id' => $minyak->id,
            'unit_id' => $literUnit->id,
            'quantity' => 20, // 20 liter
        ]);

        // Gula Pasir
        $gula = Product::create([
            'name' => 'Gula Pasir Lokal',
            'barcode' => '1234567890003',
            'category_id' => $sembakoCategory->id,
            'description' => 'Gula pasir berkualitas lokal',
            'stock_alert_minimum' => 5000, // 5 kg dalam gram
        ]);

        ProductUnit::create([
            'product_id' => $gula->id,
            'unit_id' => $gramUnit->id,
            'price' => 14,
            'conversion_rate' => 1,
            'is_base_unit' => true,
        ]);

        ProductUnit::create([
            'product_id' => $gula->id,
            'unit_id' => $kgUnit->id,
            'price' => 14000,
            'conversion_rate' => 1000,
            'is_base_unit' => false,
        ]);

        Stock::create([
            'product_id' => $gula->id,
            'unit_id' => $kgUnit->id,
            'quantity' => 25, // 25 kg
        ]);

        // Indomie
        $indomie = Product::create([
            'name' => 'Indomie Goreng',
            'barcode' => '1234567890004',
            'category_id' => $makminCategory->id,
            'description' => 'Mie instan rasa ayam bawang',
            'stock_alert_minimum' => 24, // 2 dus
        ]);

        ProductUnit::create([
            'product_id' => $indomie->id,
            'unit_id' => $pcsUnit->id,
            'price' => 3500,
            'conversion_rate' => 1,
            'is_base_unit' => true,
        ]);

        ProductUnit::create([
            'product_id' => $indomie->id,
            'unit_id' => $packUnit->id, // 1 pack = 5 pcs
            'price' => 17000,
            'conversion_rate' => 5,
            'is_base_unit' => false,
        ]);

        Stock::create([
            'product_id' => $indomie->id,
            'unit_id' => $packUnit->id,
            'quantity' => 20, // 20 pack
        ]);

        // Kopi Sachet
        $kopi = Product::create([
            'name' => 'Kopi Kapal Api Sachet',
            'barcode' => '1234567890005',
            'category_id' => $makminCategory->id,
            'description' => 'Kopi instant sachet',
            'stock_alert_minimum' => 50,
        ]);

        ProductUnit::create([
            'product_id' => $kopi->id,
            'unit_id' => $sachUnit->id,
            'price' => 1000,
            'conversion_rate' => 1,
            'is_base_unit' => true,
        ]);

        ProductUnit::create([
            'product_id' => $kopi->id,
            'unit_id' => $packUnit->id, // 1 pack = 10 sachet
            'price' => 9500,
            'conversion_rate' => 10,
            'is_base_unit' => false,
        ]);

        Stock::create([
            'product_id' => $kopi->id,
            'unit_id' => $packUnit->id,
            'quantity' => 15, // 15 pack
        ]);

        // Chitato
        $chitato = Product::create([
            'name' => 'Chitato Rasa Sapi Panggang',
            'barcode' => '1234567890006',
            'category_id' => $snackCategory->id,
            'description' => 'Keripik kentang rasa sapi panggang',
            'stock_alert_minimum' => 12,
        ]);

        ProductUnit::create([
            'product_id' => $chitato->id,
            'unit_id' => $pcsUnit->id,
            'price' => 8500,
            'conversion_rate' => 1,
            'is_base_unit' => true,
        ]);

        Stock::create([
            'product_id' => $chitato->id,
            'unit_id' => $pcsUnit->id,
            'quantity' => 24, // 24 pcs
        ]);
    }
}