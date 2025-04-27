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
            'multiple_choice',
            'true_or_false',
            'open_question',
        ];

        foreach ($types as $type) {
            QuestionType::firstOrCreate(['name' => $type]);
        }
    }
}
