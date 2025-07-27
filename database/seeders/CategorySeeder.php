<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Makanan & Minuman', 'description' => 'Produk makanan dan minuman sehari-hari'],
            ['name' => 'Sembako', 'description' => 'Bahan pokok kebutuhan sehari-hari'],
            ['name' => 'Peralatan Rumah Tangga', 'description' => 'Peralatan dan perlengkapan rumah tangga'],
            ['name' => 'Kebersihan & Kesehatan', 'description' => 'Produk kebersihan dan kesehatan'],
            ['name' => 'Rokok & Tembakau', 'description' => 'Produk rokok dan tembakau'],
            ['name' => 'Snack & Permen', 'description' => 'Makanan ringan dan permen'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
