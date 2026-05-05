<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Electronics', 'status' => 1],
            ['name' => 'Carpentry',     'status' => 1],
            ['name' => 'Office',     'status' => 1],
            ['name' => 'Industrial',   'status' => 0],
        ];

        foreach ($categories as $category) {
            Category::create([
                'name'       => $category['name'],
                'status'     => $category['status'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
