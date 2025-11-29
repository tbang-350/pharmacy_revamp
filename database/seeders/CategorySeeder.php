<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Tablets', 'description' => 'Tablet medications'],
            ['name' => 'Capsules', 'description' => 'Capsule medications'],
            ['name' => 'Syrups', 'description' => 'Liquid medications'],
            ['name' => 'Injections', 'description' => 'Injectable medications'],
            ['name' => 'Creams & Ointments', 'description' => 'Topical medications'],
            ['name' => 'Drops', 'description' => 'Eye, ear, and nose drops'],
            ['name' => 'Vitamins & Supplements', 'description' => 'Nutritional supplements'],
            ['name' => 'Medical Supplies', 'description' => 'Bandages, syringes, etc.'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
