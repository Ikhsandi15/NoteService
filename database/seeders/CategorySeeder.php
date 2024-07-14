<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::create([
            'category' => 'category-1',
            'user_id' => 1
        ]);
        Category::create([
            'category' => 'category-2',
            'user_id' => 1
        ]);
        Category::create([
            'category' => 'category-3',
            'user_id' => 2
        ]);
        Category::create([
            'category' => 'category-4',
            'user_id' => 3
        ]);
    }
}
