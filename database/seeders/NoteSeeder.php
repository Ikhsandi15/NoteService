<?php

namespace Database\Seeders;

use App\Models\Note;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class NoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Note::create([
            'title' => 'Note-1',
            'content' => 'ini adalah note ke-1',
            'category_id' => 1,
            'user_id' => 1
        ]);

        Note::create([
            'title' => 'Note-2',
            'content' => 'ini adalah note ke-2',
            'category_id' => 1,
            'user_id' => 1
        ]);

        Note::create([
            'title' => 'Note-3',
            'content' => 'ini adalah note ke-3',
            'category_id' => 2,
            'user_id' => 2
        ]);

        Note::create([
            'title' => 'Note-4',
            'content' => 'ini adalah note ke-4',
            'category_id' => 2,
            'user_id' => 2
        ]);

        Note::create([
            'title' => 'Note-5',
            'content' => 'ini adalah note ke-5',
            'category_id' => 1,
            'user_id' => 1
        ]);
    }
}
