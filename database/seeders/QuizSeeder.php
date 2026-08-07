<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\LearningModule;
use App\Models\Quiz;
use App\Models\QuizOption;
use App\Models\QuizQuestion;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuizSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $this->create('hiragana', 'Kuis Hiragana Dasar', 'easy', 'N5', [
                ['q' => 'Bagaimana cara membaca huruf「あ」?', 'options' => ['a' => true, 'i' => false, 'u' => false, 'e' => false], 'why' => '「あ」dibaca "a", vokal pertama dalam hiragana.'],
                ['q' => 'Huruf mana yang dibaca "ki"?', 'options' => ['き' => true, 'か' => false, 'く' => false, 'こ' => false], 'why' => '「き」adalah "ki" pada baris K.'],
                ['q' => 'Bagaimana cara membaca huruf「し」?', 'options' => ['shi' => true, 'si' => false, 'chi' => false, 'su' => false], 'why' => 'Baris S tidak beraturan: し dibaca "shi", bukan "si".'],
                ['q' => 'Huruf mana yang dibaca "tsu"?', 'options' => ['つ' => true, 'た' => false, 'て' => false, 'と' => false], 'why' => '「つ」adalah pengecualian baris T, dibaca "tsu".'],
                ['q' => 'Bagaimana cara membaca huruf「の」?', 'options' => ['no' => true, 'na' => false, 'nu' => false, 'ne' => false], 'why' => '「の」dibaca "no" dan juga berfungsi sebagai partikel kepemilikan.'],
            ]);

            $this->create('kanji', 'Kuis Kanji Angka', 'easy', 'N5', [
                ['q' => 'Kanji「三」berarti?', 'options' => ['tiga' => true, 'dua' => false, 'empat' => false, 'lima' => false], 'why' => '「三」ditulis dengan tiga garis dan berarti tiga.'],
                ['q' => 'Kanji mana yang berarti "air"?', 'options' => ['水' => true, '火' => false, '木' => false, '日' => false], 'why' => '「水」(mizu) berarti air.'],
                ['q' => 'Kanji「山」dibaca?', 'options' => ['yama' => true, 'kawa' => false, 'hi' => false, 'ki' => false], 'why' => '「山」dibaca "yama" (kun-yomi) dan berarti gunung.'],
                ['q' => 'Berapa jumlah goresan kanji「十」?', 'options' => ['2' => true, '1' => false, '3' => false, '4' => false], 'why' => '「十」ditulis dengan dua goresan.'],
            ]);

            $this->create('english-grammar', 'Simple Present & Past Tense', 'medium', 'A2', [
                ['q' => 'What is the past tense of "go"?', 'options' => ['went' => true, 'goed' => false, 'gone' => false, 'going' => false], 'why' => '"Go" is irregular: go / went / gone.'],
                ['q' => 'She ___ to work every day.', 'options' => ['goes' => true, 'go' => false, 'going' => false, 'gone' => false], 'why' => 'Third-person singular in simple present takes -es.'],
                ['q' => 'They ___ not finish the project yesterday.', 'options' => ['did' => true, 'do' => false, 'does' => false, 'are' => false], 'why' => 'Simple past negatives use "did not" + base verb.'],
                ['q' => 'Which sentence is correct?', 'options' => ['He does not like coffee.' => true, 'He do not likes coffee.' => false, 'He not like coffee.' => false, 'He does not likes coffee.' => false], 'why' => 'After "does not", the verb stays in its base form.'],
                ['q' => '___ you speak Japanese?', 'options' => ['Do' => true, 'Does' => false, 'Are' => false, 'Is' => false], 'why' => '"You" takes "do" in simple present questions.'],
            ]);

            $this->create('english-vocabulary', 'Travel Vocabulary', 'easy', 'A2', [
                ['q' => 'What does "luggage" mean?', 'options' => ['bagasi' => true, 'bandara' => false, 'tiket' => false, 'penundaan' => false], 'why' => '"Luggage" refers to the bags you travel with.'],
                ['q' => 'The flight has a two-hour ___.', 'options' => ['delay' => true, 'departure' => false, 'luggage' => false, 'airport' => false], 'why' => 'A "delay" is a postponement.'],
                ['q' => 'Which word means "keberangkatan"?', 'options' => ['departure' => true, 'arrival' => false, 'boarding' => false, 'terminal' => false], 'why' => '"Departure" is the act of leaving.'],
            ]);
        });
    }

    /**
     * @param  array<int, array{q:string, options:array<string,bool>, why:string}>  $questions
     */
    private function create(string $moduleSlug, string $title, string $difficulty, string $level, array $questions): void
    {
        $module = LearningModule::where('slug', $moduleSlug)->first();

        if (! $module) {
            return;
        }

        $quiz = Quiz::updateOrCreate(['slug' => str($title)->slug()->toString()], [
            'learning_module_id' => $module->id,
            'title' => $title,
            'description' => "Uji pemahamanmu tentang {$module->name}.",
            'level' => $level,
            'difficulty' => $difficulty,
            'time_limit_seconds' => count($questions) * 45,
            'pass_score' => 70,
            'xp_reward' => 50,
            'shuffle_questions' => true,
            'shuffle_options' => true,
            'show_explanation' => true,
            'is_published' => true,
        ]);

        $quiz->questions()->delete();

        foreach (array_values($questions) as $index => $definition) {
            $question = QuizQuestion::create([
                'quiz_id' => $quiz->id,
                'question' => $definition['q'],
                'type' => 'multiple_choice',
                'explanation' => $definition['why'],
                'score' => 1,
                'sort_order' => $index,
            ]);

            $order = 0;

            foreach ($definition['options'] as $label => $isCorrect) {
                QuizOption::create([
                    'quiz_question_id' => $question->id,
                    'label' => (string) $label,
                    'is_correct' => $isCorrect,
                    'sort_order' => $order++,
                ]);
            }
        }
    }
}
