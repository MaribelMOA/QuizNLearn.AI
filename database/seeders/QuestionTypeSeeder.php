<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\QuestionType;
class QuestionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            'Multiple Choice',
            'True or False',
            'Open Questions',
        ];

        foreach ($types as $type) {
            QuestionType::firstOrCreate(['name' => $type]);
        }
    }
}
