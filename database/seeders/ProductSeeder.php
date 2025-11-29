<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::all();

        $products = [
            // Tablets
            ['name' => 'Paracetamol 500mg', 'category' => 'Tablets', 'price' => 500, 'reorder' => 50],
            ['name' => 'Amoxicillin 250mg', 'category' => 'Tablets', 'price' => 1500, 'reorder' => 30],
            ['name' => 'Ciprofloxacin 500mg', 'category' => 'Tablets', 'price' => 2000, 'reorder' => 20],
            ['name' => 'Metronidazole 400mg', 'category' => 'Tablets', 'price' => 800, 'reorder' => 40],
            ['name' => 'Ibuprofen 400mg', 'category' => 'Tablets', 'price' => 600, 'reorder' => 50],
            
            // Capsules
            ['name' => 'Omeprazole 20mg', 'category' => 'Capsules', 'price' => 1200, 'reorder' => 30],
            ['name' => 'Amoxicillin 500mg Capsules', 'category' => 'Capsules', 'price' => 2000, 'reorder' => 25],
            
            // Syrups
            ['name' => 'Multivitamin Syrup 200ml', 'category' => 'Syrups', 'price' => 8000, 'reorder' => 15],
            ['name' => 'Cough Syrup 100ml', 'category' => 'Syrups', 'price' => 5000, 'reorder' => 20],
            ['name' => 'Paracetamol Syrup 100ml', 'category' => 'Syrups', 'price' => 3500, 'reorder' => 25],
            
            // Injections
            ['name' => 'Diclofenac Injection 75mg', 'category' => 'Injections', 'price' => 3000, 'reorder' => 20],
            ['name' => 'Vitamin B Complex Injection', 'category' => 'Injections', 'price' => 4000, 'reorder' => 15],
            
            // Creams & Ointments
            ['name' => 'Hydrocortisone Cream 1%', 'category' => 'Creams & Ointments', 'price' => 2500, 'reorder' => 20],
            ['name' => 'Clotrimazole Cream', 'category' => 'Creams & Ointments', 'price' => 3000, 'reorder' => 15],
            ['name' => 'Petroleum Jelly 50g', 'category' => 'Creams & Ointments', 'price' => 2000, 'reorder' => 30],
            
            // Drops
            ['name' => 'Eye Drops (Antibiotic)', 'category' => 'Drops', 'price' => 4500, 'reorder' => 15],
            ['name' => 'Ear Drops', 'category' => 'Drops', 'price' => 3500, 'reorder' => 15],
            
            // Vitamins & Supplements
            ['name' => 'Vitamin C 1000mg', 'category' => 'Vitamins & Supplements', 'price' => 15000, 'reorder' => 10],
            ['name' => 'Multivitamin Tablets', 'category' => 'Vitamins & Supplements', 'price' => 12000, 'reorder' => 10],
            ['name' => 'Calcium + Vitamin D3', 'category' => 'Vitamins & Supplements', 'price' => 18000, 'reorder' => 10],
            
            // Medical Supplies
            ['name' => 'Surgical Gloves (Box)', 'category' => 'Medical Supplies', 'price' => 25000, 'reorder' => 5],
            ['name' => 'Bandages (Pack of 10)', 'category' => 'Medical Supplies', 'price' => 5000, 'reorder' => 20],
            ['name' => 'Cotton Wool 500g', 'category' => 'Medical Supplies', 'price' => 8000, 'reorder' => 10],
            ['name' => 'Thermometer (Digital)', 'category' => 'Medical Supplies', 'price' => 15000, 'reorder' => 5],
            ['name' => 'Face Masks (Box of 50)', 'category' => 'Medical Supplies', 'price' => 20000, 'reorder' => 5],
        ];

        foreach ($products as $productData) {
            $category = $categories->firstWhere('name', $productData['category']);
            
            if ($category) {
                Product::create([
                    'name' => $productData['name'],
                    'category_id' => $category->id,
                    'selling_price' => $productData['price'],
                    'reorder_level' => $productData['reorder'],
                    'description' => 'Sample product for testing',
                ]);
            }
        }
    }
}
