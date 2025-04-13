<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Quiz;
use Illuminate\Support\Str;
class QuizSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $difficultyLevels = ['Easy', 'Medium', 'Hard'];
        $modes = ['Quiz', 'Study', 'Arena'];

        $users = User::all();

        foreach ($users as $user) {
            for ($i = 0; $i < 5; $i++) {
                Quiz::create([
                    'user_id' => $user->id,
                    'title' => 'Quiz ' . Str::title(Str::random(6)),
                    'num_questions' => rand(5, 20),
                    'difficulty_level' => $difficultyLevels[array_rand($difficultyLevels)],
                    'mode' => $modes[array_rand($modes)],
                    'quiz_data' => json_encode([
                        'description' => 'This is a sample quiz description.',
                        'tags' => ['math', 'science', 'history'][rand(0, 2)],
                        'questions' => [],
                    ]),
                ]);
            }
        }
    }
}
