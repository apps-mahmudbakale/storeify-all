<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductUnitsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Clear existing units
        DB::table('product_units')->truncate();

        $units = [
            // General Units
            ['code' => 'pcs', 'name' => 'Pieces', 'description' => 'Individual pieces', 'category' => 'general', 'sort_order' => 1],
            ['code' => 'box', 'name' => 'Box', 'description' => 'Box unit', 'category' => 'general', 'sort_order' => 2],
            ['code' => 'pack', 'name' => 'Pack', 'description' => 'Pack unit', 'category' => 'general', 'sort_order' => 3],
            ['code' => 'ml', 'name' => 'Milliliter', 'description' => 'Liquid measurement', 'category' => 'general', 'sort_order' => 4],
            ['code' => 'l', 'name' => 'Liter', 'description' => 'Liquid measurement', 'category' => 'general', 'sort_order' => 5],
            ['code' => 'kg', 'name' => 'Kilogram', 'description' => 'Weight measurement', 'category' => 'general', 'sort_order' => 6],
            ['code' => 'g', 'name' => 'Gram', 'description' => 'Weight measurement', 'category' => 'general', 'sort_order' => 7],
            ['code' => 'dozen', 'name' => 'Dozen', 'description' => 'Set of 12 items', 'category' => 'general', 'sort_order' => 8],

            // Restaurant Units
            ['code' => 'plate', 'name' => 'Plate', 'description' => 'Individual plate/serving', 'category' => 'restaurant', 'sort_order' => 10],
            ['code' => 'spoon', 'name' => 'Spoon', 'description' => 'Individual spoon (cutlery)', 'category' => 'restaurant', 'sort_order' => 11],
            ['code' => 'bottle', 'name' => 'Bottle', 'description' => 'Individual bottle', 'category' => 'restaurant', 'sort_order' => 12],
            ['code' => 'glass', 'name' => 'Glass', 'description' => 'Individual glass/cup', 'category' => 'restaurant', 'sort_order' => 13],
            ['code' => 'bowl', 'name' => 'Bowl', 'description' => 'Individual bowl/dish', 'category' => 'restaurant', 'sort_order' => 14],
            ['code' => 'cup', 'name' => 'Cup', 'description' => 'Individual cup', 'category' => 'restaurant', 'sort_order' => 15],
            ['code' => 'portion', 'name' => 'Portion', 'description' => 'Single serving portion', 'category' => 'restaurant', 'sort_order' => 16],
            ['code' => 'tray', 'name' => 'Tray', 'description' => 'Tray/platter', 'category' => 'restaurant', 'sort_order' => 17],
            ['code' => 'fork', 'name' => 'Fork', 'description' => 'Individual fork (cutlery)', 'category' => 'restaurant', 'sort_order' => 18],
            ['code' => 'knife', 'name' => 'Knife', 'description' => 'Individual knife (cutlery)', 'category' => 'restaurant', 'sort_order' => 19],
            ['code' => 'napkin', 'name' => 'Napkin', 'description' => 'Individual napkin', 'category' => 'restaurant', 'sort_order' => 20],

            // Medical Units
            ['code' => 'syringe', 'name' => 'Syringe', 'description' => 'Individual syringe', 'category' => 'medical', 'sort_order' => 30],
            ['code' => 'vial', 'name' => 'Vial', 'description' => 'Individual vial', 'category' => 'medical', 'sort_order' => 31],
            ['code' => 'tablet', 'name' => 'Tablet', 'description' => 'Individual tablet', 'category' => 'medical', 'sort_order' => 32],
            ['code' => 'capsule', 'name' => 'Capsule', 'description' => 'Individual capsule', 'category' => 'medical', 'sort_order' => 33],
            ['code' => 'ampule', 'name' => 'Ampule', 'description' => 'Individual ampule', 'category' => 'medical', 'sort_order' => 34],
        ];

        DB::table('product_units')->insert($units);
    }
}
