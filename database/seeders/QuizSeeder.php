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
                [
                    'q' => 'Bagaimana cara membaca huruf「い」?',
                    'options' => ['i' => true, 'a' => false, 'u' => false, 'e' => false],
                    'why' => 'Huruf hiragana「い」dibaca "i", seperti pada kata "ikan" tanpa huruf k.',
                ],
                [
                    'q' => 'Bagaimana cara membaca huruf「う」?',
                    'options' => ['u' => true, 'o' => false, 'i' => false, 'e' => false],
                    'why' => 'Huruf hiragana「う」dibaca "u", bunyi vokal kelima dalam urutan あいうえお.',
                ],
                [
                    'q' => 'Bagaimana cara membaca huruf「え」?',
                    'options' => ['e' => true, 'a' => false, 'i' => false, 'u' => false],
                    'why' => 'Huruf hiragana「え」dibaca "e", termasuk salah satu dari lima huruf vokal dasar.',
                ],
                [
                    'q' => 'Bagaimana cara membaca huruf「お」?',
                    'options' => ['o' => true, 'u' => false, 'e' => false, 'a' => false],
                    'why' => 'Huruf hiragana「お」dibaca "o", huruf terakhir dari deretan vokal あいうえお.',
                ],
                [
                    'q' => 'Huruf「け」dalam hiragana dibaca sebagai...?',
                    'options' => ['ke' => true, 'ki' => false, 'ka' => false, 'ku' => false],
                    'why' => '「け」termasuk baris "k" (かきくけこ) dan berbunyi "ke".',
                ],
                [
                    'q' => 'Huruf hiragana「こ」dibaca...?',
                    'options' => ['ko' => true, 'ka' => false, 'ke' => false, 'ku' => false],
                    'why' => '「こ」adalah bunyi terakhir dari baris "k" (かきくけこ), dibaca "ko".',
                ],
                [
                    'q' => 'Huruf hiragana「せ」dibaca...?',
                    'options' => ['se' => true, 'shi' => false, 'su' => false, 'sa' => false],
                    'why' => '「せ」berada di baris "s" (さしすせそ) dan dibaca "se".',
                ],
                [
                    'q' => 'Huruf hiragana「て」dibaca...?',
                    'options' => ['te' => true, 'chi' => false, 'to' => false, 'ta' => false],
                    'why' => '「て」termasuk baris "t" (たちつてと) dan berbunyi "te".',
                ],
                [
                    'q' => 'Huruf hiragana「な」dibaca...?',
                    'options' => ['na' => true, 'ni' => false, 'nu' => false, 'no' => false],
                    'why' => '「な」adalah huruf pertama pada baris "n" (なにぬねの), dibaca "na".',
                ],
                [
                    'q' => 'Huruf hiragana「ひ」dibaca...?',
                    'options' => ['hi' => true, 'fu' => false, 'ha' => false, 'he' => false],
                    'why' => '「ひ」berada di baris "h" (はひふへほ) dan dibaca "hi".',
                ],
                [
                    'q' => 'Huruf hiragana「む」dibaca...?',
                    'options' => ['mu' => true, 'ma' => false, 'mo' => false, 'me' => false],
                    'why' => '「む」termasuk baris "m" (まみむめも) dan berbunyi "mu".',
                ],
                [
                    'q' => 'Huruf hiragana「ら」dibaca...?',
                    'options' => ['ra' => true, 'ri' => false, 'ru' => false, 'na' => false],
                    'why' => '「ら」adalah huruf pertama pada baris "r" (らりるれろ), dibaca "ra".',
                ],
                [
                    'q' => 'Jika huruf「か」diberi tanda dakuten (゛) menjadi「が」, huruf tersebut dibaca...?',
                    'options' => ['ga' => true, 'ka' => false, 'gi' => false, 'za' => false],
                    'why' => 'Tanda dakuten mengubah bunyi "k" menjadi "g", sehingga「が」dibaca "ga".',
                ],
                [
                    'q' => 'Dalam kata「がっこう」(sekolah), huruf kecil「っ」berfungsi sebagai apa?',
                    'options' => [
                        'menandakan konsonan rangkap / jeda sesaat (sokuon)' => true,
                        'mengubah vokal menjadi panjang' => false,
                        'menandakan bentuk negatif' => false,
                        'menggabungkan dua kata benda' => false,
                    ],
                    'why' => 'Huruf「っ」kecil disebut sokuon dan menandakan penggandaan konsonan berikutnya, sehingga「がっこう」dibaca "gakkou" dengan jeda sesaat sebelum "k".',
                ],
                [
                    'q' => 'Bagaimana cara membaca kata「ねこ」yang berarti "kucing"?',
                    'options' => ['neko' => true, 'noko' => false, 'niko' => false, 'neku' => false],
                    'why' => '「ね」dibaca "ne" dan「こ」dibaca "ko", sehingga「ねこ」dibaca "neko".',
                ],
            ]);

            $this->create('kanji', 'Kuis Kanji Angka', 'easy', 'N5', [
                ['q' => 'Kanji「三」berarti?', 'options' => ['tiga' => true, 'dua' => false, 'empat' => false, 'lima' => false], 'why' => '「三」ditulis dengan tiga garis dan berarti tiga.'],
                ['q' => 'Kanji mana yang berarti "air"?', 'options' => ['水' => true, '火' => false, '木' => false, '日' => false], 'why' => '「水」(mizu) berarti air.'],
                ['q' => 'Kanji「山」dibaca?', 'options' => ['yama' => true, 'kawa' => false, 'hi' => false, 'ki' => false], 'why' => '「山」dibaca "yama" (kun-yomi) dan berarti gunung.'],
                ['q' => 'Berapa jumlah goresan kanji「十」?', 'options' => ['2' => true, '1' => false, '3' => false, '4' => false], 'why' => '「十」ditulis dengan dua goresan.'],
                [
                    'q' => 'Apa arti kanji 一 dalam bahasa Indonesia?',
                    'options' => ['Satu' => true, 'Dua' => false, 'Tiga' => false, 'Sepuluh' => false],
                    'why' => '一 adalah kanji untuk angka 1 (satu).',
                ],
                [
                    'q' => 'Bagaimana cara membaca kanji 二 (angka dua) dalam bahasa Jepang?',
                    'options' => ['に (ni)' => true, 'さん (san)' => false, 'し (shi)' => false, 'ご (go)' => false],
                    'why' => '二 dibaca "に" (ni) dan berarti angka dua.',
                ],
                [
                    'q' => 'Kanji 四 (angka empat) umumnya dibaca dengan dua cara, yaitu...',
                    'options' => ['し (shi) dan よん (yon)' => true, 'いち (ichi) dan に (ni)' => false, 'ご (go) dan ろく (roku)' => false, 'なな (nana) dan はち (hachi)' => false],
                    'why' => 'Kanji 四 punya dua cara baca umum: し (shi) dan よん (yon).',
                ],
                [
                    'q' => 'Apa arti kanji 五?',
                    'options' => ['Lima' => true, 'Empat' => false, 'Enam' => false, 'Tujuh' => false],
                    'why' => '五 adalah kanji untuk angka 5 (lima), dibaca ご (go).',
                ],
                [
                    'q' => 'Bagaimana cara membaca kanji 六 (angka enam)?',
                    'options' => ['ろく (roku)' => true, 'なな (nana)' => false, 'きゅう (kyuu)' => false, 'じゅう (juu)' => false],
                    'why' => '六 dibaca ろく (roku) dan berarti angka enam.',
                ],
                [
                    'q' => 'Kanji 七 (angka tujuh) bisa dibaca dengan dua cara, yaitu...',
                    'options' => ['しち (shichi) dan なな (nana)' => true, 'はち (hachi) dan きゅう (kyuu)' => false, 'ろく (roku) dan ご (go)' => false, 'じゅう (juu) dan せん (sen)' => false],
                    'why' => 'Kanji 七 punya dua cara baca umum: しち (shichi) dan なな (nana).',
                ],
                [
                    'q' => 'Apa arti kanji 八?',
                    'options' => ['Delapan' => true, 'Tujuh' => false, 'Sembilan' => false, 'Enam' => false],
                    'why' => '八 adalah kanji untuk angka 8 (delapan), dibaca はち (hachi).',
                ],
                [
                    'q' => 'Kanji 九 (angka sembilan) memiliki dua cara baca umum, yaitu...',
                    'options' => ['きゅう (kyuu) dan く (ku)' => true, 'はち (hachi) dan しち (shichi)' => false, 'じゅう (juu) dan ひゃく (hyaku)' => false, 'ご (go) dan ろく (roku)' => false],
                    'why' => 'Kanji 九 punya dua cara baca umum: きゅう (kyuu) dan く (ku).',
                ],
                [
                    'q' => 'Bagaimana cara membaca kanji 十 (angka sepuluh)?',
                    'options' => ['じゅう (juu)' => true, 'せん (sen)' => false, 'ひゃく (hyaku)' => false, 'まん (man)' => false],
                    'why' => '十 dibaca じゅう (juu) dan berarti angka sepuluh.',
                ],
                [
                    'q' => 'Apa arti kanji 百?',
                    'options' => ['Seratus' => true, 'Sepuluh' => false, 'Seribu' => false, 'Sepuluh ribu' => false],
                    'why' => '百 adalah kanji untuk angka 100 (seratus), dibaca ひゃく (hyaku).',
                ],
                [
                    'q' => 'Apa arti kanji 千?',
                    'options' => ['Seribu' => true, 'Seratus' => false, 'Sepuluh ribu' => false, 'Sepuluh' => false],
                    'why' => '千 adalah kanji untuk angka 1.000 (seribu), dibaca せん (sen).',
                ],
                [
                    'q' => 'Apa arti kanji 万?',
                    'options' => ['Sepuluh ribu' => true, 'Seribu' => false, 'Seratus' => false, 'Satu juta' => false],
                    'why' => '万 adalah kanji untuk angka 10.000 (sepuluh ribu), dibaca まん (man).',
                ],
                [
                    'q' => 'Kanji 人 sering dipakai untuk menghitung orang. Kata 二人 dibaca...',
                    'options' => ['ふたり (futari), artinya dua orang' => true, 'ひとり (hitori), artinya satu orang' => false, 'さんにん (sannin), artinya tiga orang' => false, 'よにん (yonin), artinya empat orang' => false],
                    'why' => '二人 (dua orang) memiliki cara baca khusus, yaitu ふたり (futari), bukan gabungan biasa dari に + にん.',
                ],
                [
                    'q' => 'Apa arti kanji 日 ketika berdiri sendiri sebagai kata benda?',
                    'options' => ['Hari / matahari' => true, 'Bulan (waktu)' => false, 'Tahun' => false, 'Air' => false],
                    'why' => '日 berarti "hari" atau "matahari", dan juga muncul dalam kata seperti 日本 (Jepang, "asal matahari").',
                ],
                [
                    'q' => 'Kata 一月 (angka 一 digabung dengan kanji 月) berarti...',
                    'options' => ['Bulan Januari' => true, 'Bulan Februari' => false, 'Satu minggu' => false, 'Satu hari' => false],
                    'why' => '一月 dibaca いちがつ (ichigatsu) dan berarti bulan Januari (bulan pertama).',
                ],
                [
                    'q' => 'Apa arti kanji 年 dalam kata 一年?',
                    'options' => ['Tahun, sehingga 一年 berarti "satu tahun"' => true, 'Bulan, sehingga 一年 berarti "satu bulan"' => false, 'Hari, sehingga 一年 berarti "satu hari"' => false, 'Minggu, sehingga 一年 berarti "satu minggu"' => false],
                    'why' => '年 berarti "tahun", sehingga 一年 (ichinen) berarti "satu tahun".',
                ],
            ]);

            $this->create('english-grammar', 'Simple Present & Past Tense', 'medium', 'Elementary', [
                ['q' => 'What is the past tense of "go"?', 'options' => ['went' => true, 'goed' => false, 'gone' => false, 'going' => false], 'why' => '"Go" is irregular: go / went / gone.'],
                ['q' => 'She ___ to work every day.', 'options' => ['goes' => true, 'go' => false, 'going' => false, 'gone' => false], 'why' => 'Third-person singular in simple present takes -es.'],
                ['q' => 'They ___ not finish the project yesterday.', 'options' => ['did' => true, 'do' => false, 'does' => false, 'are' => false], 'why' => 'Simple past negatives use "did not" + base verb.'],
                ['q' => 'Which sentence is correct?', 'options' => ['He does not like coffee.' => true, 'He do not likes coffee.' => false, 'He not like coffee.' => false, 'He does not likes coffee.' => false], 'why' => 'After "does not", the verb stays in its base form.'],
                ['q' => '___ you speak Japanese?', 'options' => ['Do' => true, 'Does' => false, 'Are' => false, 'Is' => false], 'why' => '"You" takes "do" in simple present questions.'],
                [
                    'q' => 'Choose the correct past tense form: "Yesterday, I ___ a sandwich for lunch." (eat)',
                    'options' => ['ate' => true, 'eated' => false, 'eat' => false, 'eating' => false],
                    'why' => '"Eat" is an irregular verb, so its simple past form is "ate" instead of following the regular -ed pattern like "eated".',
                ],
                [
                    'q' => 'Choose the correct past tense form: "She ___ a letter to her friend last night." (write)',
                    'options' => ['wrote' => true, 'writed' => false, 'write' => false, 'writing' => false],
                    'why' => '"Write" is an irregular verb; its simple past form is "wrote", not "writed".',
                ],
                [
                    'q' => 'Choose the correct past tense form: "They ___ a new car last month." (buy)',
                    'options' => ['bought' => true, 'buyed' => false, 'buy' => false, 'buying' => false],
                    'why' => '"Buy" is an irregular verb; its simple past form is "bought", not "buyed".',
                ],
                [
                    'q' => 'Choose the correct past tense form: "We ___ a wonderful time at the beach last weekend." (have)',
                    'options' => ['had' => true, 'haved' => false, 'have' => false, 'having' => false],
                    'why' => '"Have" is an irregular verb; its simple past form is "had".',
                ],
                [
                    'q' => 'Choose the correct past tense form: "He ___ to visit us last Sunday." (come)',
                    'options' => ['came' => true, 'comed' => false, 'come' => false, 'coming' => false],
                    'why' => '"Come" is an irregular verb; its simple past form is "came".',
                ],
                [
                    'q' => 'Choose the correct past tense form: "I ___ very hard for my exam last week." (study)',
                    'options' => ['studied' => true, 'studyed' => false, 'study' => false, 'studies' => false],
                    'why' => 'For regular verbs ending in a consonant + y, change the y to i and add -ed: study becomes studied.',
                ],
                [
                    'q' => 'Choose the correct present tense form: "My brother ___ football every Saturday." (play)',
                    'options' => ['plays' => true, 'play' => false, 'playing' => false, 'played' => false],
                    'why' => 'With third-person singular subjects (he/she/it), add -s to the base verb in the simple present: play becomes plays.',
                ],
                [
                    'q' => 'Choose the correct present tense form: "My sister ___ English every evening." (study)',
                    'options' => ['studies' => true, 'studys' => false, 'study' => false, 'studying' => false],
                    'why' => 'For third-person singular verbs ending in a consonant + y, change y to i and add -es: study becomes studies.',
                ],
                [
                    'q' => 'Complete the sentence with the correct simple present form: "The sun ___ in the east." (rise)',
                    'options' => ['rises' => true, 'rise' => false, 'rose' => false, 'rising' => false],
                    'why' => 'General truths and facts of nature are expressed with the simple present tense, adding -s for the third-person subject "the sun".',
                ],
                [
                    'q' => 'Choose the correct negative sentence.',
                    'options' => ['They don\'t live in Jakarta.' => true, 'They doesn\'t live in Jakarta.' => false, 'They not live in Jakarta.' => false, 'They didn\'t lives in Jakarta.' => false],
                    'why' => 'The plural subject "they" takes "don\'t" (do + not) in the simple present negative, and the base verb "live" follows without -s.',
                ],
                [
                    'q' => 'Choose the correct negative sentence.',
                    'options' => ['I didn\'t see him yesterday.' => true, 'I didn\'t saw him yesterday.' => false, 'I not saw him yesterday.' => false, 'I doesn\'t see him yesterday.' => false],
                    'why' => 'In simple past negatives, "didn\'t" is followed by the base form of the verb ("see"), not the past form ("saw").',
                ],
                [
                    'q' => 'Choose the grammatically correct sentence.',
                    'options' => ['She didn\'t go to school because she was sick.' => true, 'She didn\'t went to school because she was sick.' => false, 'She didn\'t goes to school because she was sick.' => false, 'She not went to school because she was sick.' => false],
                    'why' => 'After "didn\'t", the verb must stay in its base form ("go"), since "didn\'t" already carries the past tense marker.',
                ],
                [
                    'q' => 'Choose the correct question: "___ she like coffee?"',
                    'options' => ['Does' => true, 'Do' => false, 'Did' => false, 'Is' => false],
                    'why' => 'For third-person singular subjects (she/he/it) in the simple present, questions are formed with "does" + the base verb.',
                ],
                [
                    'q' => 'Choose the correct question: "___ you clean your room yesterday?"',
                    'options' => ['Did' => true, 'Do' => false, 'Does' => false, 'Were' => false],
                    'why' => 'Simple past questions use "did" with all subjects, followed by the base form of the verb.',
                ],
                [
                    'q' => 'Complete the sentence with the correct verb forms: "I usually ___ breakfast at 7 AM, but yesterday I ___ breakfast at 8 AM." (eat)',
                    'options' => ['eat / ate' => true, 'eat / eaten' => false, 'ate / eat' => false, 'eats / ate' => false],
                    'why' => 'The first blank describes a habitual action, so the simple present "eat" is used; the second blank refers to a specific past event, so the simple past "ate" is used.',
                ],
            ]);

            $this->create('english-grammar', 'Kuis Fondasi Grammar: Parts of Speech, Tense, & Struktur Kalimat', 'medium', 'Intermediate', [
                ['q' => 'Kata "beautifully" pada kalimat "She sings beautifully every night." termasuk jenis kata apa?', 'options' => ['Adverb (kata keterangan)' => true, 'Adjective (kata sifat)' => false, 'Noun (kata benda)' => false, 'Verb (kata kerja)' => false], 'why' => '"Beautifully" menjelaskan bagaimana cara "she sings" (bagaimana dia bernyanyi), sehingga berfungsi sebagai adverb (kata keterangan cara). Ciri khasnya adalah akhiran "-ly" yang dibentuk dari adjective "beautiful". Adjective hanya menerangkan noun, bukan verb, sehingga bukan pilihan yang tepat di sini.'],
                ['q' => 'Perhatikan kalimat "The keys are under the sofa." Kata "under" pada kalimat tersebut termasuk jenis kata apa?', 'options' => ['Preposition (kata depan)' => true, 'Conjunction (kata sambung)' => false, 'Adverb (kata keterangan)' => false, 'Verb (kata kerja)' => false], 'why' => '"Under" menunjukkan hubungan tempat antara "the keys" dan "the sofa", yaitu ciri utama preposition (kata depan). Preposition selalu diikuti oleh noun atau noun phrase sebagai objeknya, dalam hal ini "the sofa". Conjunction menghubungkan klausa/kata, bukan menunjukkan posisi.'],
                ['q' => 'Manakah kata dalam kalimat "Wow! That roller coaster was incredibly fast." yang merupakan interjection (kata seru)?', 'options' => ['roller coaster' => false, 'Wow' => true, 'incredibly' => false, 'fast' => false], 'why' => '"Wow" adalah interjection karena berdiri sendiri (diikuti tanda seru) dan mengungkapkan emosi/reaksi spontan terkejut atau kagum, tanpa berfungsi secara gramatikal dalam struktur kalimat utama. "Incredibly" adalah adverb yang menerangkan adjective "fast", sedangkan "roller coaster" adalah noun (subjek kalimat).'],
                ['q' => 'Pada kalimat "Andi lost his phone, so he borrowed mine.", kata "so" berfungsi sebagai jenis kata apa?', 'options' => ['Preposition' => false, 'Adverb' => false, 'Conjunction (menghubungkan dua klausa, menyatakan akibat)' => true, 'Pronoun' => false], 'why' => '"So" di sini adalah coordinating conjunction (salah satu dari FANBOYS) yang menghubungkan dua klausa independen dan menyatakan hubungan sebab-akibat: "Andi lost his phone" (sebab) dan "he borrowed mine" (akibat). Preposition tidak menghubungkan dua klausa lengkap, melainkan kata dengan frasa.'],
                ['q' => 'Lengkapi kalimat berikut dengan bentuk Present Perfect yang tepat: "I ___ never ___ to Japan."', 'options' => ['have / been' => true, 'never / been' => false, 'have / went' => false, 'was / been' => false], 'why' => 'Present Perfect menggunakan rumus S + have/has + V3 (past participle). Subjek "I" memakai auxiliary "have", diikuti V3 dari "be" yaitu "been" (bukan "went" yang merupakan V2 dari "go"). Kalimat yang benar adalah "I have never been to Japan," menyatakan pengalaman yang belum pernah terjadi sampai sekarang.'],
                ['q' => 'Lengkapi pertanyaan berikut dengan bentuk yang tepat: "How long ___ you ___ English?"', 'options' => ['do / learn' => false, 'are / learning' => false, 'have / been learning' => true, 'did / learn' => false], 'why' => 'Pertanyaan dengan "How long" yang menanyakan durasi suatu aktivitas yang dimulai di masa lalu dan masih berlangsung sampai sekarang menggunakan Present Perfect Continuous: have/has + been + V-ing. Jawaban yang benar adalah "have you been learning". Simple Present dan Simple Past tidak menunjukkan durasi berkelanjutan, sedangkan Present Continuous saja tidak menghubungkan aktivitas dengan masa lalu.'],
                ['q' => 'Manakah kalimat yang tepat untuk menggambarkan dua kejadian yang berlangsung bersamaan di masa lalu?', 'options' => ['While I was cook dinner, my sister was doing her homework.' => false, 'While I was cooking dinner, my sister was doing her homework.' => true, 'While I am cooking dinner, my sister was doing her homework.' => false, 'While I was cooking dinner, my sister did her homework.' => false], 'why' => 'Past Continuous (was/were + V-ing) digunakan untuk dua aktivitas yang berlangsung bersamaan di masa lalu, ditandai kata "while". Pilihan yang benar memakai "was cooking" dan "was doing" secara konsisten. Opsi terakhir salah karena "did her homework" (Simple Past) berarti aktivitas selesai sebagai kejadian tunggal, bukan berlangsung bersamaan dengan aktivitas memasak.'],
                ['q' => 'Manakah kalimat yang menggunakan Future Continuous Tense dengan tepat untuk menyatakan aktivitas yang sedang berlangsung pada waktu tertentu di masa depan?', 'options' => ['This time tomorrow, I will taking the exam.' => false, 'This time tomorrow, I am taking the exam.' => false, 'This time tomorrow, I will be taking the exam.' => true, 'This time tomorrow, I will took the exam.' => false], 'why' => 'Future Continuous menggunakan rumus S + will + be + V-ing, digunakan untuk menyatakan aktivitas yang sedang berlangsung pada titik waktu tertentu di masa depan. Kalimat yang benar adalah "I will be taking the exam." Opsi lain salah karena kehilangan "be", menggunakan Present Continuous, atau memakai bentuk V2 setelah "will".'],
                ['q' => 'Lengkapi kalimat berikut dengan bentuk yang tepat untuk menyatakan fakta umum/ilmiah: "Water ___ at 100 degrees Celsius."', 'options' => ['boils' => true, 'is boiling' => false, 'boiled' => false, 'will boil' => false], 'why' => 'Simple Present digunakan untuk menyatakan fakta umum, kebenaran ilmiah, atau kejadian yang selalu benar. Karena "water" adalah subjek tunggal, verb memakai tambahan "-s": "boils". Present Continuous menunjukkan kejadian sementara, Simple Past menunjukkan kejadian spesifik di masa lalu, dan Simple Future menunjukkan prediksi, bukan fakta yang selalu berlaku.'],
                ['q' => 'Manakah kalimat yang tepat untuk melengkapi: "I ___ my homework yesterday."', 'options' => ['have finished' => false, 'finished' => true, 'have finish' => false, 'was finishing' => false], 'why' => 'Present Perfect tidak dapat digunakan bersama keterangan waktu spesifik di masa lalu seperti "yesterday". Karena ada penanda waktu tersebut, kalimat harus menggunakan Simple Past: "finished". "Was finishing" (Past Continuous) menyiratkan aktivitas belum selesai atau sedang berlangsung, bukan aktivitas yang selesai dilakukan.'],
                ['q' => 'Kalimat "My mother gave me a present." termasuk struktur kalimat apa?', 'options' => ['SVC (Subject-Verb-Complement)' => false, 'SVO (Subject-Verb-Object)' => false, 'SVOO (Subject-Verb-Indirect Object-Direct Object)' => true, 'SVA (Subject-Verb-Adverbial)' => false], 'why' => '"Gave" adalah ditransitive verb yang memiliki dua objek: "me" sebagai indirect object (penerima) dan "a present" sebagai direct object (benda yang diberikan), sehingga strukturnya SVOO. SVC digunakan untuk kalimat dengan complement (misalnya "She is happy"), sedangkan SVO hanya memiliki satu objek.'],
                ['q' => 'Manakah yang merupakan compound sentence (kalimat majemuk setara) yang benar menggunakan FANBOYS?', 'options' => ['I wanted to go to the beach, it was raining.' => false, 'I wanted to go to the beach but was raining.' => false, 'I wanted to go to the beach, but it was raining.' => true, 'Because I wanted to go to the beach, it was raining.' => false], 'why' => 'Compound sentence terbentuk dari dua klausa independen yang dihubungkan dengan koma + coordinating conjunction (FANBOYS), di sini "but". Opsi pertama salah karena comma splice (koma tanpa conjunction), opsi kedua kehilangan subjek "it" setelah "but", dan opsi terakhir menggunakan subordinating conjunction "because" sehingga menjadi complex sentence dengan makna yang tidak logis.'],
                ['q' => 'Manakah complex sentence dengan adverbial clause yang tepat untuk menyatakan hubungan sebab-akibat?', 'options' => ['It was raining we stayed at home.' => false, 'Because it was raining, we stayed at home.' => true, 'We stayed at home, it was raining.' => false, 'Although it was raining, so we stayed at home.' => false], 'why' => 'Complex sentence dengan adverbial clause of reason menggunakan subordinating conjunction "because" diikuti klausa, koma, lalu main clause: "Because it was raining, we stayed at home." Opsi pertama dan ketiga adalah run-on/comma splice karena tidak ada conjunction penghubung, sedangkan opsi terakhir salah karena mencampur "although" (kontras) dan "so" (akibat) sekaligus dalam satu kalimat.'],
                ['q' => 'Lengkapi kalimat conditional type 2 berikut dengan tepat: "If I ___ you, I would apologize immediately."', 'options' => ['am' => false, 'was' => false, 'were' => true, 'will be' => false], 'why' => 'Conditional Type 2 digunakan untuk situasi hipotetis/tidak nyata di masa sekarang, dengan rumus If + S + Past Simple, S + would + V1. Untuk subjek "I" dalam conditional type 2, bentuk baku (subjunctive) yang digunakan adalah "were", bukan "was", terutama pada ungkapan tetap "If I were you". "Am" adalah ciri Type 0 dan "will be" tidak pernah digunakan pada if-clause.'],
                ['q' => 'Manakah bentuk pasif (passive voice) yang benar dari kalimat "They built this house in 1990."?', 'options' => ['This house is built in 1990.' => false, 'This house was built in 1990.' => true, 'This house was build in 1990.' => false, 'This house has been built in 1990.' => false], 'why' => 'Passive voice mengikuti tense kalimat aktifnya. Karena kalimat aktif menggunakan Simple Past ("built"), bentuk pasifnya adalah S + was/were + V3: "This house was built in 1990." Opsi pertama salah karena menggunakan Simple Present, opsi ketiga memakai V1 "build" bukan V3 "built", dan opsi terakhir memakai Present Perfect yang tidak cocok dengan keterangan waktu spesifik di masa lalu "in 1990".'],
                [
                    'q' => 'Dalam kalimat "Although she was tired, she finished the marathon.", kata "Although" berfungsi sebagai apa, dan apa yang ditunjukkannya?',
                    'options' => [
                        'Konjungsi subordinatif yang menghubungkan anak kalimat dengan induk kalimat, menunjukkan hubungan pertentangan' => true,
                        'Konjungsi koordinatif yang menghubungkan dua klausa setara' => false,
                        'Preposisi yang menunjukkan waktu' => false,
                        'Kata keterangan (adverb) yang menerangkan kata kerja "finished"' => false,
                    ],
                    'why' => '"Although" adalah subordinating conjunction yang menghubungkan dependent clause ("Although she was tired") dengan independent clause ("she finished the marathon"), menunjukkan hubungan kontras/pertentangan. Ini berbeda dari konjungsi koordinatif seperti FANBOYS (for, and, nor, but, or, yet, so) yang menghubungkan dua klausa yang kedudukannya setara (compound sentence).',
                ],
                [
                    'q' => 'Lengkapi kalimat berikut dengan bentuk present perfect yang tepat: "She ___ her homework, so she can go out now." (finish)',
                    'options' => [
                        'has just finished' => true,
                        'just finished' => false,
                        'has just finish' => false,
                        'is just finishing' => false,
                    ],
                    'why' => 'Present perfect (has/have + past participle) digunakan dengan kata sinyal "just" untuk menunjukkan aksi yang baru saja selesai dan memiliki dampak pada keadaan sekarang. Pola yang benar adalah "has" (untuk subjek she) + "just" + past participle "finished".',
                ],
                [
                    'q' => 'Lengkapi kalimat conditional berikut: "If it rains tomorrow, we ___ at home." (stay)',
                    'options' => [
                        'will stay' => true,
                        'would stay' => false,
                        'stay' => false,
                        'would have stayed' => false,
                    ],
                    'why' => 'Ini adalah Conditional Type 1 (real/possible condition) yang menyatakan kemungkinan nyata di masa depan. Polanya: if + simple present, main clause + will + base verb. Berbeda dengan Conditional Type 2 yang menggunakan "would" untuk situasi hipotetis di masa sekarang.',
                ],
                [
                    'q' => 'Lengkapi kalimat conditional berikut: "If she had studied harder, she ___ the exam." (pass)',
                    'options' => [
                        'would have passed' => true,
                        'would pass' => false,
                        'will pass' => false,
                        'had passed' => false,
                    ],
                    'why' => 'Ini adalah Conditional Type 3 yang digunakan untuk membicarakan situasi hipotetis di masa lalu yang bertentangan dengan fakta (dia sebenarnya tidak belajar lebih giat dan tidak lulus). Polanya: if + past perfect (had studied), main clause + would have + past participle (would have passed).',
                ],
                [
                    'q' => 'Perhatikan struktur kalimat: "The soup tastes delicious." Apa struktur kalimat tersebut?',
                    'options' => [
                        'SVC (Subject-Verb-Complement), karena "tastes" adalah linking verb dan "delicious" adalah complement yang menerangkan subjek' => true,
                        'SVO (Subject-Verb-Object), karena "delicious" adalah objek dari kata kerja "tastes"' => false,
                        'SVOO (Subject-Verb-Object-Object), karena kalimat memiliki dua objek' => false,
                        'SV (Subject-Verb), karena "delicious" hanya kata keterangan tambahan' => false,
                    ],
                    'why' => '"Tastes" di sini berfungsi sebagai linking verb (bukan verb aksi), sehingga tidak diikuti oleh objek melainkan oleh complement ("delicious") yang menerangkan atau menggambarkan keadaan subjek "the soup". Ini berbeda dari SVO, di mana verb aksi diikuti objek yang menerima aksi tersebut.',
                ],
                [
                    'q' => 'Ubahlah kalimat aktif berikut menjadi kalimat pasif: "They will finish the project by Friday."',
                    'options' => [
                        'The project will be finished by Friday.' => true,
                        'The project was finished by Friday.' => false,
                        'The project is finished by Friday.' => false,
                        'The project will finished by Friday.' => false,
                    ],
                    'why' => 'Kalimat pasif dengan modal verb "will" menggunakan pola: subject + will + be + past participle. Karena kalimat asli menggunakan future simple ("will finish"), bentuk pasifnya juga harus mempertahankan modal "will" diikuti "be" dan past participle "finished".',
                ],
            ]);

            $this->create('english-vocabulary', 'Travel Vocabulary', 'easy', 'Elementary', [
                ['q' => 'What does "luggage" mean?', 'options' => ['bagasi' => true, 'bandara' => false, 'tiket' => false, 'penundaan' => false], 'why' => '"Luggage" refers to the bags you travel with.'],
                ['q' => 'The flight has a two-hour ___.', 'options' => ['delay' => true, 'departure' => false, 'luggage' => false, 'airport' => false], 'why' => 'A "delay" is a postponement.'],
                ['q' => 'Which word means "keberangkatan"?', 'options' => ['departure' => true, 'arrival' => false, 'boarding' => false, 'terminal' => false], 'why' => '"Departure" is the act of leaving.'],
                [
                    'q' => 'Apa arti "passport" dalam bahasa Indonesia?',
                    'options' => ['paspor' => true, 'tiket' => false, 'koper' => false, 'visa' => false],
                    'why' => '"Passport" adalah dokumen resmi untuk perjalanan internasional, dalam bahasa Indonesia disebut paspor.',
                ],
                [
                    'q' => 'What is a "boarding pass" used for?',
                    'options' => ['To board the plane' => true, 'To exchange money' => false, 'To book a hotel' => false, 'To rent a car' => false],
                    'why' => '"Boarding pass" adalah tiket yang digunakan untuk naik pesawat setelah check-in.',
                ],
                [
                    'q' => 'In an airport, what does "gate" mean?',
                    'options' => ['Pintu keberangkatan untuk naik pesawat' => true, 'Tempat menukar uang' => false, 'Tempat mengambil koper' => false, 'Tempat memesan tiket' => false],
                    'why' => '"Gate" adalah pintu di bandara tempat penumpang naik ke pesawat.',
                ],
                [
                    'q' => 'Apa arti "check-in" saat bepergian?',
                    'options' => ['Mendaftarkan diri sebelum naik pesawat atau masuk hotel' => true, 'Membayar tagihan hotel' => false, 'Mengambil koper' => false, 'Memesan taksi' => false],
                    'why' => '"Check-in" adalah proses mendaftar sebelum naik pesawat atau saat masuk ke kamar hotel.',
                ],
                [
                    'q' => 'What does "reservation" mean?',
                    'options' => ['Pemesanan (misalnya kamar hotel atau tiket)' => true, 'Pembatalan' => false, 'Pembayaran' => false, 'Penundaan' => false],
                    'why' => '"Reservation" berarti pemesanan terlebih dahulu, misalnya untuk kamar hotel atau tiket.',
                ],
                [
                    'q' => 'Apa arti "one-way ticket"?',
                    'options' => ['Tiket sekali jalan' => true, 'Tiket pulang pergi' => false, 'Tiket gratis' => false, 'Tiket kelas bisnis' => false],
                    'why' => '"One-way ticket" adalah tiket yang hanya digunakan untuk perjalanan satu arah, tanpa kembali.',
                ],
                [
                    'q' => 'Apa arti "round-trip ticket"?',
                    'options' => ['Tiket pulang pergi' => true, 'Tiket sekali jalan' => false, 'Tiket diskon' => false, 'Tiket transit' => false],
                    'why' => '"Round-trip ticket" adalah tiket untuk perjalanan pergi dan pulang.',
                ],
                [
                    'q' => 'What is an "exchange rate"?',
                    'options' => ['Nilai tukar antara dua mata uang' => true, 'Harga tiket pesawat' => false, 'Biaya sewa mobil' => false, 'Tarif kamar hotel' => false],
                    'why' => '"Exchange rate" adalah nilai tukar yang menunjukkan berapa banyak satu mata uang setara dengan mata uang lain.',
                ],
                [
                    'q' => 'Apa arti "single room" saat memesan hotel?',
                    'options' => ['Kamar untuk satu orang' => true, 'Kamar untuk dua orang' => false, 'Kamar tanpa jendela' => false, 'Kamar termahal' => false],
                    'why' => '"Single room" adalah kamar hotel yang dirancang untuk ditempati satu orang.',
                ],
                [
                    'q' => 'Apa arti kata "map" dalam bahasa Indonesia?',
                    'options' => ['peta' => true, 'kompas' => false, 'jalan' => false, 'arah' => false],
                    'why' => '"Map" berarti peta, alat yang membantu kita menemukan arah atau lokasi.',
                ],
                [
                    'q' => 'What does "turn left" mean?',
                    'options' => ['Belok kiri' => true, 'Belok kanan' => false, 'Jalan lurus' => false, 'Berhenti' => false],
                    'why' => '"Turn left" adalah instruksi arah yang berarti belok ke kiri.',
                ],
                [
                    'q' => 'Apa arti "turn right"?',
                    'options' => ['Belok kanan' => true, 'Belok kiri' => false, 'Putar balik' => false, 'Berhenti di sini' => false],
                    'why' => '"Turn right" adalah instruksi arah yang berarti belok ke kanan.',
                ],
                [
                    'q' => 'Apa arti "go straight ahead"?',
                    'options' => ['Jalan terus lurus' => true, 'Belok kiri' => false, 'Belok kanan' => false, 'Putar balik' => false],
                    'why' => '"Go straight ahead" berarti terus berjalan lurus tanpa berbelok.',
                ],
                [
                    'q' => 'What is a "taxi" used for?',
                    'options' => ['Transportasi berbayar untuk mengantar penumpang' => true, 'Menyimpan barang bawaan' => false, 'Menukar uang' => false, 'Memesan kamar hotel' => false],
                    'why' => '"Taxi" adalah kendaraan transportasi umum berbayar yang mengantar penumpang ke tujuan.',
                ],
                [
                    'q' => 'Apa arti kata "suitcase"?',
                    'options' => ['koper' => true, 'tas tangan' => false, 'dompet' => false, 'ransel' => false],
                    'why' => '"Suitcase" adalah koper besar yang digunakan untuk membawa pakaian saat bepergian.',
                ],
                [
                    'q' => 'What does "tourist" mean?',
                    'options' => ['Wisatawan' => true, 'Pemandu wisata' => false, 'Sopir taksi' => false, 'Petugas bandara' => false],
                    'why' => '"Tourist" berarti wisatawan, orang yang bepergian untuk berlibur atau melihat tempat baru.',
                ],
                [
                    'q' => 'Apa arti "sightseeing"?',
                    'options' => ['Berkeliling melihat tempat-tempat wisata' => true, 'Memesan tiket pesawat' => false, 'Mengurus paspor' => false, 'Menukar mata uang' => false],
                    'why' => '"Sightseeing" adalah kegiatan berkeliling untuk melihat dan menikmati tempat-tempat wisata.',
                ],
            ]);

            $this->create('katakana', 'Kuis Katakana Dasar', 'easy', 'N5', [
                ['q' => 'Bagaimana cara membaca huruf「ア」?', 'options' => ['a' => true, 'i' => false, 'u' => false, 'e' => false], 'why' => '「ア」adalah versi katakana dari「あ」, dibaca "a".'],
                ['q' => 'Huruf apa yang dipakai untuk menulis kata serapan seperti「コーヒー」?', 'options' => ['Katakana' => true, 'Hiragana' => false, 'Kanji' => false, 'Romaji' => false], 'why' => 'Katakana khusus dipakai untuk kata serapan asing, nama asing, dan onomatope.'],
                ['q' => 'Bagaimana cara membaca huruf「カ」?', 'options' => ['ka' => true, 'sa' => false, 'ta' => false, 'na' => false], 'why' => '「カ」adalah versi katakana dari「か」, dibaca "ka".'],
                ['q' => '「メニュー」dibaca...?', 'options' => ['menyuu' => true, 'menuu' => false, 'meniyuu' => false, 'menyu' => false], 'why' => 'ニュ adalah gabungan yōon, dibaca "nyu" bukan "niyu".'],
                [
                    'q' => 'Bagaimana cara membaca huruf katakana「イ」?',
                    'options' => ['i' => true, 'a' => false, 'u' => false, 'e' => false],
                    'why' => 'Huruf katakana「イ」melambangkan bunyi yang sama dengan hiragana「い」, yaitu "i".',
                ],
                [
                    'q' => 'Bagaimana cara membaca huruf katakana「ウ」?',
                    'options' => ['u' => true, 'o' => false, 'i' => false, 'e' => false],
                    'why' => 'Huruf katakana「ウ」dibaca "u", sama seperti hiragana「う」.',
                ],
                [
                    'q' => 'Bagaimana cara membaca huruf katakana「エ」?',
                    'options' => ['e' => true, 'a' => false, 'i' => false, 'u' => false],
                    'why' => 'Huruf katakana「エ」dibaca "e", sama seperti hiragana「え」.',
                ],
                [
                    'q' => 'Bagaimana cara membaca huruf katakana「オ」?',
                    'options' => ['o' => true, 'u' => false, 'e' => false, 'a' => false],
                    'why' => 'Huruf katakana「オ」dibaca "o", sama seperti hiragana「お」.',
                ],
                [
                    'q' => 'Bagaimana cara membaca huruf katakana「ク」?',
                    'options' => ['ku' => true, 'ki' => false, 'ka' => false, 'ke' => false],
                    'why' => '「ク」termasuk baris "k" pada katakana (カキクケコ) dan dibaca "ku".',
                ],
                [
                    'q' => 'Bagaimana cara membaca huruf katakana「セ」?',
                    'options' => ['se' => true, 'shi' => false, 'su' => false, 'so' => false],
                    'why' => '「セ」berada pada baris "s" katakana (サシスセソ) dan dibaca "se".',
                ],
                [
                    'q' => 'Bagaimana cara membaca huruf katakana「ト」?',
                    'options' => ['to' => true, 'ta' => false, 'te' => false, 'chi' => false],
                    'why' => '「ト」termasuk baris "t" katakana (タチツテト) dan dibaca "to".',
                ],
                [
                    'q' => 'Katakana umumnya digunakan untuk menuliskan jenis kata apa?',
                    'options' => [
                        'kata serapan dari bahasa asing dan nama negara/orang asing' => true,
                        'kata kerja asli bahasa Jepang' => false,
                        'partikel tata bahasa seperti は dan が' => false,
                        'angka dalam bentuk kanji' => false,
                    ],
                    'why' => 'Katakana biasanya dipakai untuk menulis kata serapan (gairaigo), nama asing, dan istilah teknis/ilmiah, berbeda dengan hiragana yang dipakai untuk kata asli Jepang dan partikel.',
                ],
                [
                    'q' => 'Kata katakana「テレビ」(terebi) merupakan serapan dari bahasa Inggris dan berarti...?',
                    'options' => ['televisi' => true, 'telepon' => false, 'komputer' => false, 'radio' => false],
                    'why' => '「テレビ」berasal dari kata bahasa Inggris "television" yang disingkat menjadi "terebi", artinya televisi.',
                ],
                [
                    'q' => 'Kata katakana「パン」(pan) berarti...?',
                    'options' => ['roti' => true, 'wajan' => false, 'panci' => false, 'permen' => false],
                    'why' => '「パン」adalah kata serapan dari bahasa Portugis "pão" yang berarti roti.',
                ],
                [
                    'q' => 'Kata katakana「バス」(basu) berarti...?',
                    'options' => ['bus' => true, 'bak mandi' => false, 'dasar' => false, 'tas' => false],
                    'why' => '「バス」merupakan serapan dari kata bahasa Inggris "bus" dan digunakan untuk menyebut kendaraan bus.',
                ],
                [
                    'q' => 'Kata katakana「タクシー」(takushii) berarti...?',
                    'options' => ['taksi' => true, 'kereta' => false, 'sepeda' => false, 'pesawat' => false],
                    'why' => '「タクシー」adalah serapan dari kata bahasa Inggris "taxi", artinya taksi.',
                ],
                [
                    'q' => 'Apa fungsi tanda garis panjang「ー」dalam penulisan katakana, seperti pada「ラーメン」?',
                    'options' => [
                        'menandakan vokal dibaca panjang' => true,
                        'menandakan konsonan rangkap' => false,
                        'menandakan kata negatif' => false,
                        'menggabungkan dua kalimat' => false,
                    ],
                    'why' => 'Tanda「ー」pada katakana berfungsi memperpanjang bunyi vokal sebelumnya, misalnya「ラーメン」dibaca "raamen" dengan bunyi "a" yang panjang.',
                ],
                [
                    'q' => 'Dalam kata katakana「ベッド」(beddo, artinya "tempat tidur"), huruf kecil「ッ」berfungsi sebagai apa?',
                    'options' => [
                        'menandakan konsonan rangkap / jeda sesaat (sokuon)' => true,
                        'memperpanjang bunyi vokal' => false,
                        'menandakan bentuk jamak' => false,
                        'mengubah kata menjadi kata tanya' => false,
                    ],
                    'why' => 'Sama seperti pada hiragana, huruf kecil「ッ」pada katakana adalah sokuon yang menandakan penggandaan konsonan berikutnya, sehingga「ベッド」dibaca "beddo".',
                ],
                [
                    'q' => 'Kata katakana「アイスクリーム」(aisukuriimu) berarti...?',
                    'options' => ['es krim' => true, 'air dingin' => false, 'kue' => false, 'susu' => false],
                    'why' => '「アイスクリーム」adalah serapan dari bahasa Inggris "ice cream" yang berarti es krim.',
                ],
                [
                    'q' => 'Selain untuk kata serapan, katakana juga sering digunakan untuk menuliskan...?',
                    'options' => [
                        'kata tiruan bunyi (onomatope), seperti「ワンワン」(bunyi gonggongan anjing)' => true,
                        'akhiran kata kerja bentuk sopan (~masu)' => false,
                        'partikel penghubung kalimat' => false,
                        'nama bulan dalam kalender Jepang' => false,
                    ],
                    'why' => 'Katakana kerap dipakai untuk menuliskan onomatope atau kata tiruan bunyi agar lebih menonjol dalam kalimat, contohnya「ワンワン」untuk suara gonggongan anjing.',
                ],
            ]);

            $this->create('kosakata-jepang', 'Kuis Kosakata Dasar', 'easy', 'N5', [
                ['q' => '「先生」artinya?', 'options' => ['guru' => true, 'murid' => false, 'sekolah' => false, 'teman' => false], 'why' => '「先生」(sensei) berarti guru atau pengajar.'],
                ['q' => '「水」dibaca?', 'options' => ['mizu' => true, 'hi' => false, 'ki' => false, 'tsuki' => false], 'why' => '「水」(mizu) berarti air.'],
                ['q' => '「食べる」artinya?', 'options' => ['makan' => true, 'minum' => false, 'tidur' => false, 'berjalan' => false], 'why' => '「食べる」(taberu) adalah kata kerja "makan".'],
                ['q' => '「友達」artinya?', 'options' => ['teman' => true, 'keluarga' => false, 'tetangga' => false, 'rekan kerja' => false], 'why' => '「友達」(tomodachi) berarti teman.'],
                ['q' => '「今日」dibaca?', 'options' => ['kyou' => true, 'ashita' => false, 'kinou' => false, 'mainichi' => false], 'why' => '「今日」(kyou) berarti "hari ini".'],
                [
                    'q' => '「母」artinya?',
                    'options' => ['ibu' => true, 'ayah' => false, 'kakak' => false, 'adik' => false],
                    'why' => '「母」(haha) berarti "ibu" dalam Bahasa Indonesia.',
                ],
                [
                    'q' => '「父」artinya?',
                    'options' => ['ayah' => true, 'ibu' => false, 'kakek' => false, 'nenek' => false],
                    'why' => '「父」(chichi) berarti "ayah" dalam Bahasa Indonesia.',
                ],
                [
                    'q' => '「犬」artinya?',
                    'options' => ['anjing' => true, 'kucing' => false, 'burung' => false, 'ikan' => false],
                    'why' => '「犬」(inu) berarti "anjing".',
                ],
                [
                    'q' => '「猫」artinya?',
                    'options' => ['kucing' => true, 'anjing' => false, 'kelinci' => false, 'tikus' => false],
                    'why' => '「猫」(neko) berarti "kucing".',
                ],
                [
                    'q' => '「学校」artinya?',
                    'options' => ['sekolah' => true, 'rumah sakit' => false, 'kantor' => false, 'toko' => false],
                    'why' => '「学校」(gakkou) berarti "sekolah".',
                ],
                [
                    'q' => '「飲む」artinya?',
                    'options' => ['minum' => true, 'makan' => false, 'tidur' => false, 'membaca' => false],
                    'why' => '「飲む」(nomu) adalah kata kerja yang berarti "minum".',
                ],
                [
                    'q' => '「大きい」artinya?',
                    'options' => ['besar' => true, 'kecil' => false, 'panjang' => false, 'pendek' => false],
                    'why' => '「大きい」(ookii) adalah kata sifat yang berarti "besar".',
                ],
                [
                    'q' => '「小さい」artinya?',
                    'options' => ['kecil' => true, 'besar' => false, 'berat' => false, 'ringan' => false],
                    'why' => '「小さい」(chiisai) adalah kata sifat yang berarti "kecil".',
                ],
                [
                    'q' => '「明日」dibaca?',
                    'options' => ['ashita' => true, 'kinou' => false, 'kyou' => false, 'mainichi' => false],
                    'why' => '「明日」dibaca "ashita" yang berarti "besok".',
                ],
                [
                    'q' => '「昨日」artinya?',
                    'options' => ['kemarin' => true, 'besok' => false, 'hari ini' => false, 'lusa' => false],
                    'why' => '「昨日」(kinou) berarti "kemarin".',
                ],
                [
                    'q' => '「本」artinya?',
                    'options' => ['buku' => true, 'pena' => false, 'meja' => false, 'kursi' => false],
                    'why' => '「本」(hon) berarti "buku".',
                ],
                [
                    'q' => '「家」dibaca?',
                    'options' => ['ie' => true, 'gakkou' => false, 'kuruma' => false, 'michi' => false],
                    'why' => '「家」dibaca "ie" yang berarti "rumah".',
                ],
                [
                    'q' => '「車」artinya?',
                    'options' => ['mobil' => true, 'sepeda' => false, 'kereta' => false, 'pesawat' => false],
                    'why' => '「車」(kuruma) berarti "mobil".',
                ],
                [
                    'q' => '「読む」artinya?',
                    'options' => ['membaca' => true, 'menulis' => false, 'mendengar' => false, 'berbicara' => false],
                    'why' => '「読む」(yomu) adalah kata kerja yang berarti "membaca".',
                ],
                [
                    'q' => '「新しい」artinya?',
                    'options' => ['baru' => true, 'lama' => false, 'mahal' => false, 'murah' => false],
                    'why' => '「新しい」(atarashii) adalah kata sifat yang berarti "baru".',
                ],
            ]);

            $this->create('tata-bahasa-jepang', 'Kuis Partikel Dasar', 'medium', 'N5', [
                ['q' => '私＿学生です。partikel yang tepat?', 'options' => ['は' => true, 'を' => false, 'に' => false, 'で' => false], 'why' => '「は」menandai topik kalimat.'],
                ['q' => '図書館＿本を読みます。', 'options' => ['で' => true, 'を' => false, 'へ' => false, 'の' => false], 'why' => '「で」menunjukkan tempat aktivitas berlangsung.'],
                ['q' => '明日、東京＿行きます。', 'options' => ['へ' => true, 'を' => false, 'が' => false, 'と' => false], 'why' => '「へ」menunjukkan arah/tujuan perpindahan.'],
                ['q' => 'これは私＿本です。', 'options' => ['の' => true, 'は' => false, 'を' => false, 'に' => false], 'why' => '「の」menunjukkan kepemilikan.'],
                ['q' => '七時＿起きます。', 'options' => ['に' => true, 'で' => false, 'を' => false, 'へ' => false], 'why' => '「に」dipakai untuk menandai waktu spesifik.'],
                [
                    'q' => 'これ＿ペンです。partikel yang tepat?',
                    'options' => ['は' => true, 'を' => false, 'に' => false, 'で' => false],
                    'why' => '"は" menandai topik kalimat, sehingga "これは" berarti "ini adalah".',
                ],
                [
                    'q' => '水＿飲みます。partikel yang tepat?',
                    'options' => ['を' => true, 'は' => false, 'に' => false, 'で' => false],
                    'why' => '"を" menandai objek langsung dari kata kerja; "水を飲みます" berarti "minum air".',
                ],
                [
                    'q' => '学校＿友達に会いました。partikel yang tepat?',
                    'options' => ['で' => true, 'に' => false, 'を' => false, 'へ' => false],
                    'why' => '"で" menunjukkan tempat terjadinya suatu aktivitas, yaitu bertemu teman di sekolah.',
                ],
                [
                    'q' => '毎朝六時＿起きます。partikel yang tepat?',
                    'options' => ['に' => true, 'で' => false, 'を' => false, 'は' => false],
                    'why' => '"に" digunakan untuk menunjukkan waktu spesifik, seperti jam 6.',
                ],
                [
                    'q' => '公園＿散歩します。partikel yang tepat?',
                    'options' => ['を' => true, 'で' => false, 'に' => false, 'へ' => false],
                    'why' => 'Kata kerja "散歩する" (jalan-jalan) menggunakan partikel "を" untuk menunjukkan tempat yang dilalui.',
                ],
                [
                    'q' => '母＿買い物に行きました。partikel yang tepat?',
                    'options' => ['と' => true, 'に' => false, 'を' => false, 'は' => false],
                    'why' => '"と" berarti "bersama dengan", yaitu pergi belanja bersama ibu.',
                ],
                [
                    'q' => 'これは誰＿本ですか。partikel yang tepat?',
                    'options' => ['の' => true, 'は' => false, 'を' => false, 'に' => false],
                    'why' => '"の" menunjukkan kepemilikan, yaitu buku milik siapa.',
                ],
                [
                    'q' => '朝から晩＿働きます。partikel yang tepat?',
                    'options' => ['まで' => true, 'から' => false, 'に' => false, 'で' => false],
                    'why' => '"まで" menunjukkan batas akhir waktu, yaitu bekerja sampai malam.',
                ],
                [
                    'q' => '九時＿五時まで働きます。partikel yang tepat?',
                    'options' => ['から' => true, 'まで' => false, 'に' => false, 'で' => false],
                    'why' => '"から" menunjukkan titik awal waktu, yaitu mulai bekerja dari jam 9.',
                ],
                [
                    'q' => '今日＿雨が降っています。partikel yang tepat?',
                    'options' => ['は' => true, 'が' => false, 'を' => false, 'に' => false],
                    'why' => '"は" menandai topik kalimat, yaitu "hari ini" sebagai topik yang dibicarakan.',
                ],
                [
                    'q' => '猫＿ネズミを追いかけます。partikel yang tepat?',
                    'options' => ['が' => true, 'は' => false, 'を' => false, 'に' => false],
                    'why' => '"が" menandai subjek kalimat, yaitu kucing yang mengejar.',
                ],
                [
                    'q' => '誰＿来ましたか。partikel yang tepat?',
                    'options' => ['が' => true, 'は' => false, 'に' => false, 'を' => false],
                    'why' => '"が" digunakan bersama kata tanya subjek seperti "誰" (siapa) dalam pertanyaan.',
                ],
                [
                    'q' => 'これ＿あれ、どちらがいいですか。partikel yang tepat?',
                    'options' => ['と' => true, 'や' => false, 'も' => false, 'の' => false],
                    'why' => '"と" menghubungkan dua benda secara lengkap, yaitu "ini dan itu".',
                ],
                [
                    'q' => '手紙＿書きます。partikel yang tepat?',
                    'options' => ['を' => true, 'に' => false, 'で' => false, 'は' => false],
                    'why' => '"を" menandai objek langsung dari kata kerja, yaitu menulis surat.',
                ],
                [
                    'q' => '駅＿歩いて行きます。partikel yang tepat?',
                    'options' => ['へ' => true, 'で' => false, 'を' => false, 'から' => false],
                    'why' => '"へ" menunjukkan arah tujuan, yaitu menuju ke stasiun.',
                ],
            ]);

            $this->create('percakapan-jepang', 'Kuis Frasa Percakapan', 'easy', 'N5', [
                ['q' => '「はじめまして」artinya?', 'options' => ['salam kenal' => true, 'terima kasih' => false, 'selamat tinggal' => false, 'permisi' => false], 'why' => '「はじめまして」diucapkan saat bertemu seseorang untuk pertama kali.'],
                ['q' => 'Bagaimana menjawab「お元気ですか」dengan sopan?', 'options' => ['はい、元気です' => true, 'いいえ、元気です' => false, 'わかりません' => false, 'さようなら' => false], 'why' => '「はい、元気です」berarti "Ya, saya sehat/baik".'],
                ['q' => '「すみません」dipakai untuk?', 'options' => ['permisi dan minta maaf' => true, 'hanya permisi' => false, 'hanya minta maaf' => false, 'ucapan selamat' => false], 'why' => '「すみません」serbaguna: bisa berarti permisi maupun minta maaf.'],
                ['q' => 'Cara sopan memesan makanan di restoran?', 'options' => ['＿をください' => true, '＿をあげます' => false, '＿をもらいます' => false, '＿をします' => false], 'why' => '「〜をください」berarti "tolong beri saya〜", cocok untuk memesan.'],
                [
                    'q' => '「おはようございます」digunakan untuk mengucapkan?',
                    'options' => ['selamat pagi' => true, 'selamat siang' => false, 'selamat malam' => false, 'selamat tinggal' => false],
                    'why' => '「おはようございます」adalah salam formal yang diucapkan di pagi hari.',
                ],
                [
                    'q' => '「こんにちは」digunakan untuk mengucapkan?',
                    'options' => ['selamat siang' => true, 'selamat pagi' => false, 'selamat malam' => false, 'selamat tidur' => false],
                    'why' => '「こんにちは」adalah salam yang diucapkan di siang hari.',
                ],
                [
                    'q' => '「こんばんは」digunakan untuk mengucapkan?',
                    'options' => ['selamat malam' => true, 'selamat pagi' => false, 'selamat siang' => false, 'sampai jumpa' => false],
                    'why' => '「こんばんは」adalah salam yang diucapkan di malam hari.',
                ],
                [
                    'q' => '「さようなら」artinya?',
                    'options' => ['selamat tinggal' => true, 'selamat datang' => false, 'terima kasih' => false, 'maaf' => false],
                    'why' => '「さようなら」digunakan untuk mengucapkan salam perpisahan secara formal.',
                ],
                [
                    'q' => '「ありがとうございます」artinya?',
                    'options' => ['terima kasih (formal)' => true, 'maaf' => false, 'tolong' => false, 'sama-sama' => false],
                    'why' => '「ありがとうございます」adalah ucapan terima kasih dalam bentuk formal.',
                ],
                [
                    'q' => 'Apa yang diucapkan orang Jepang sebelum mulai makan?',
                    'options' => ['いただきます' => true, 'ごちそうさまでした' => false, 'おやすみなさい' => false, 'ただいま' => false],
                    'why' => '「いただきます」diucapkan sebelum makan sebagai tanda rasa syukur atas makanan.',
                ],
                [
                    'q' => 'Apa yang diucapkan orang Jepang setelah selesai makan?',
                    'options' => ['ごちそうさまでした' => true, 'いただきます' => false, 'いってきます' => false, 'おかえりなさい' => false],
                    'why' => '「ごちそうさまでした」diucapkan setelah selesai makan untuk berterima kasih atas hidangannya.',
                ],
                [
                    'q' => '「いってきます」diucapkan ketika?',
                    'options' => ['akan pergi meninggalkan rumah' => true, 'baru pulang ke rumah' => false, 'sebelum tidur' => false, 'sebelum makan' => false],
                    'why' => '「いってきます」diucapkan saat seseorang akan berangkat/pergi meninggalkan rumah.',
                ],
                [
                    'q' => '「ただいま」diucapkan ketika?',
                    'options' => ['baru pulang ke rumah' => true, 'akan pergi dari rumah' => false, 'sebelum tidur' => false, 'saat berbelanja' => false],
                    'why' => '「ただいま」diucapkan ketika seseorang baru saja tiba/pulang ke rumah.',
                ],
                [
                    'q' => '「おかえりなさい」adalah balasan yang tepat untuk?',
                    'options' => ['ただいま' => true, 'いってきます' => false, 'おやすみなさい' => false, 'すみません' => false],
                    'why' => '「おかえりなさい」("selamat datang kembali") adalah jawaban untuk 「ただいま」yang diucapkan orang yang baru pulang.',
                ],
                [
                    'q' => '「おやすみなさい」artinya?',
                    'options' => ['selamat tidur' => true, 'selamat pagi' => false, 'sampai jumpa' => false, 'permisi' => false],
                    'why' => '「おやすみなさい」diucapkan sebelum seseorang tidur di malam hari.',
                ],
                [
                    'q' => '「どういたしまして」digunakan sebagai balasan untuk?',
                    'options' => ['ucapan terima kasih' => true, 'permintaan maaf' => false, 'perkenalan diri' => false, 'ucapan selamat tinggal' => false],
                    'why' => '「どういたしまして」berarti "sama-sama", jawaban sopan ketika seseorang mengucapkan terima kasih.',
                ],
                [
                    'q' => '「ごめんなさい」artinya?',
                    'options' => ['maaf' => true, 'terima kasih' => false, 'tolong' => false, 'permisi' => false],
                    'why' => '「ごめんなさい」digunakan untuk meminta maaf.',
                ],
                [
                    'q' => '「〜お願いします」digunakan untuk?',
                    'options' => ['meminta tolong/mengajukan permintaan' => true, 'mengucapkan salam perpisahan' => false, 'meminta maaf' => false, 'mengucapkan selamat' => false],
                    'why' => '「お願いします」dipakai saat meminta bantuan atau mengajukan sebuah permintaan dengan sopan.',
                ],
                [
                    'q' => '「いくらですか」artinya?',
                    'options' => ['berapa harganya?' => true, 'ini apa?' => false, 'jam berapa sekarang?' => false, 'ini dimana?' => false],
                    'why' => '「いくらですか」digunakan untuk menanyakan harga suatu barang, biasanya saat berbelanja.',
                ],
                [
                    'q' => '「わかりました」artinya?',
                    'options' => ['saya mengerti' => true, 'saya tidak tahu' => false, 'saya lapar' => false, 'saya lelah' => false],
                    'why' => '「わかりました」digunakan untuk menyatakan bahwa seseorang sudah mengerti/paham.',
                ],
            ]);

            $this->create('menulis-jepang', 'Kuis Aksara dan Penulisan', 'medium', 'N5', [
                ['q' => 'Urutan goresan kanji umumnya dimulai dari?', 'options' => ['atas ke bawah, kiri ke kanan' => true, 'bawah ke atas' => false, 'kanan ke kiri' => false, 'acak, bebas' => false], 'why' => 'Aturan dasar urutan goresan: atas dulu, lalu kiri sebelum kanan.'],
                ['q' => 'Aksara apa yang dipakai untuk partikel tata bahasa (は, を, に)?', 'options' => ['Hiragana' => true, 'Katakana' => false, 'Kanji' => false, 'Romaji' => false], 'why' => 'Partikel selalu ditulis dengan hiragana.'],
                ['q' => 'Nama asing seperti "Budi" biasanya ditulis dengan aksara?', 'options' => ['Katakana' => true, 'Hiragana' => false, 'Kanji' => false, 'Romaji' => false], 'why' => 'Nama/kata asing ditulis dengan katakana.'],
                ['q' => 'Furigana dipakai untuk?', 'options' => ['bantuan bacaan di atas kanji sulit' => true, 'menulis partikel' => false, 'menulis nama asing' => false, 'tanda baca' => false], 'why' => 'Furigana adalah huruf kecil hiragana di atas/samping kanji untuk membantu bacaan.'],
                [
                    'q' => 'Ada berapa jenis sistem tulisan utama yang digunakan dalam bahasa Jepang?',
                    'options' => ['Tiga: hiragana, katakana, dan kanji' => true, 'Dua: hiragana dan katakana' => false, 'Empat: hiragana, katakana, kanji, dan romaji' => false, 'Satu: hanya kanji' => false],
                    'why' => 'Bahasa Jepang modern menggunakan tiga sistem tulisan utama: hiragana, katakana, dan kanji (romaji hanya alat bantu, bukan sistem utama).',
                ],
                [
                    'q' => 'Dalam kata 食べる (taberu), huruf べる yang ditulis dengan hiragana setelah kanji disebut...',
                    'options' => ['Okurigana' => true, 'Furigana' => false, 'Katakana' => false, 'Kanji' => false],
                    'why' => 'Okurigana adalah huruf hiragana yang mengikuti kanji untuk menunjukkan perubahan bentuk kata (konjugasi), seperti pada 食べる.',
                ],
                [
                    'q' => 'Selain untuk kata serapan asing, katakana juga sering digunakan untuk menulis...',
                    'options' => ['Kata onomatope (tiruan bunyi), misalnya わんわん ditulis ワンワン' => true, 'Partikel tata bahasa seperti は dan を' => false, 'Akhiran konjugasi kata kerja seperti る dan た' => false, 'Angka dalam bentuk kanji' => false],
                    'why' => 'Selain kata serapan, katakana juga umum dipakai untuk menuliskan onomatope/tiruan bunyi agar terlihat menonjol dalam kalimat.',
                ],
                [
                    'q' => 'Kanji dalam sebuah kalimat bahasa Jepang paling umum digunakan untuk menulis apa?',
                    'options' => ['Kata benda dan bagian inti (akar) dari kata kerja/kata sifat' => true, 'Partikel tata bahasa' => false, 'Akhiran konjugasi kata kerja' => false, 'Kata serapan dari bahasa asing' => false],
                    'why' => 'Kanji umumnya membawa makna inti sebuah kata, seperti kata benda atau akar kata kerja/kata sifat, sedangkan tata bahasa ditulis dengan hiragana.',
                ],
                [
                    'q' => 'Apa itu 句読点 (kutouten) dalam bahasa Jepang?',
                    'options' => ['Tanda baca' => true, 'Huruf kanji' => false, 'Aksara hiragana' => false, 'Kosakata serapan' => false],
                    'why' => '句読点 adalah istilah umum untuk tanda baca dalam bahasa Jepang, termasuk 、dan 。',
                ],
                [
                    'q' => 'Apa fungsi tanda 、(touten) dalam penulisan Jepang?',
                    'options' => ['Menandai jeda dalam kalimat, mirip fungsi koma' => true, 'Mengakhiri sebuah kalimat, mirip titik' => false, 'Menandai kutipan langsung' => false, 'Menunjukkan penekanan kata' => false],
                    'why' => '、berfungsi seperti koma dalam bahasa Indonesia/Inggris, menandai jeda di tengah kalimat.',
                ],
                [
                    'q' => 'Apa fungsi tanda 。(kuten) dalam penulisan Jepang?',
                    'options' => ['Mengakhiri sebuah kalimat, mirip titik' => true, 'Menandai jeda di tengah kalimat, mirip koma' => false, 'Menandai kutipan langsung' => false, 'Menunjukkan pertanyaan' => false],
                    'why' => '。digunakan untuk mengakhiri kalimat, fungsinya setara dengan tanda titik.',
                ],
                [
                    'q' => 'Tanda kurung 「 」 dalam penulisan Jepang biasanya digunakan untuk...',
                    'options' => ['Menandai kutipan atau ucapan langsung, mirip tanda kutip' => true, 'Menandai akhir kalimat' => false, 'Menandai jeda di tengah kalimat' => false, 'Menunjukkan kata serapan asing' => false],
                    'why' => '「」digunakan seperti tanda kutip dalam bahasa Indonesia/Inggris, untuk menandai ucapan langsung atau kutipan.',
                ],
                [
                    'q' => 'Bagaimana arah penulisan tradisional (klasik) dalam bahasa Jepang?',
                    'options' => ['Vertikal dari atas ke bawah, dengan kolom berurutan dari kanan ke kiri' => true, 'Horizontal dari kiri ke kanan seperti bahasa Indonesia' => false, 'Vertikal dari bawah ke atas' => false, 'Horizontal dari kanan ke kiri' => false],
                    'why' => 'Penulisan tradisional Jepang (tategaki) ditulis secara vertikal dari atas ke bawah, dengan kolom berurutan dari kanan ke kiri.',
                ],
                [
                    'q' => 'Selain tategaki (vertikal), gaya penulisan Jepang modern yang mirip bahasa Indonesia disebut...',
                    'options' => ['Yokogaki, horizontal dari kiri ke kanan' => true, 'Katakana, horizontal dari kanan ke kiri' => false, 'Okurigana, vertikal dari kiri ke kanan' => false, 'Furigana, horizontal dari bawah ke atas' => false],
                    'why' => 'Yokogaki adalah gaya penulisan horizontal dari kiri ke kanan, umum digunakan di buku, koran, dan media digital modern.',
                ],
                [
                    'q' => 'Berapa jumlah karakter dasar dalam hiragana (gojuuon)?',
                    'options' => ['Sekitar 46 karakter dasar' => true, 'Sekitar 26 karakter dasar' => false, 'Sekitar 100 karakter dasar' => false, 'Ribuan karakter, sama seperti kanji' => false],
                    'why' => 'Hiragana memiliki sekitar 46 karakter dasar dalam tabel gojuuon, jauh lebih sedikit dibanding kanji yang jumlahnya ribuan.',
                ],
                [
                    'q' => 'Dibandingkan dengan kanji, jumlah karakter dalam sistem hiragana dan katakana adalah...',
                    'options' => ['Tetap dan terbatas (masing-masing sekitar 46 karakter dasar)' => true, 'Tidak terbatas, terus bertambah seperti kanji' => false, 'Lebih banyak daripada jumlah kanji' => false, 'Sama persis dengan jumlah kanji yang diajarkan di sekolah' => false],
                    'why' => 'Hiragana dan katakana masing-masing merupakan sistem tertutup dengan sekitar 46 karakter dasar, berbeda dengan kanji yang jumlahnya ribuan.',
                ],
                [
                    'q' => 'Secara visual, bentuk huruf katakana pada umumnya lebih...',
                    'options' => ['Bersudut dan lurus dibanding hiragana yang cenderung melengkung' => true, 'Melengkung dan bulat dibanding hiragana yang bersudut' => false, 'Rumit seperti kanji dengan banyak coretan' => false, 'Identik bentuknya dengan hiragana' => false],
                    'why' => 'Katakana umumnya memiliki bentuk garis lurus dan bersudut, berbeda dengan hiragana yang cenderung melengkung/bulat.',
                ],
                [
                    'q' => 'Tanda ー pada kata katakana seperti コーヒー (koohii, kopi) berfungsi untuk...',
                    'options' => ['Memanjangkan bunyi vokal sebelumnya' => true, 'Menggandakan konsonan berikutnya' => false, 'Menandai akhir kalimat' => false, 'Menandai kutipan' => false],
                    'why' => 'Tanda ー (choonpu) pada katakana digunakan untuk memanjangkan bunyi vokal, seperti pada コーヒー yang dibaca "koohii".',
                ],
                [
                    'q' => 'Huruf っ berukuran kecil sebelum konsonan lain (misalnya pada kata がっこう) berfungsi untuk...',
                    'options' => ['Menggandakan/menahan bunyi konsonan yang mengikutinya' => true, 'Memanjangkan bunyi vokal sebelumnya' => false, 'Menandai kata tersebut adalah kata serapan' => false, 'Mengubah kata benda menjadi kata kerja' => false],
                    'why' => 'っ (sokuon) yang berukuran kecil menandakan penggandaan/penahanan bunyi konsonan berikutnya, seperti pada がっこう (gakkou, sekolah).',
                ],
                [
                    'q' => 'Huruf kecil ゃ, ゅ, ょ yang digabung dengan kana baris "i" (seperti き, し, ち) digunakan untuk membentuk...',
                    'options' => ['Bunyi kontraksi (yoon), misalnya きゃ dibaca "kya"' => true, 'Bunyi panjang, misalnya きい dibaca "kii"' => false, 'Bunyi ganda, misalnya きっ dibaca "kk"' => false, 'Partikel tata bahasa baru' => false],
                    'why' => 'Kombinasi kana kecil ゃ/ゅ/ょ dengan kana baris "i" membentuk bunyi kontraksi (yoon) seperti きゃ (kya), しゅ (shu), ちょ (cho).',
                ],
            ]);

            $this->create('membaca-jepang', 'Kuis Pemahaman Cerita Rakyat', 'medium', 'N4', [
                ['q' => 'マリン・クンダンの母は何をして生活していましたか。', 'options' => ['魚を取って売っていた' => true, '店で働いていた' => false, '先生だった' => false, '農業をしていた' => false], 'why' => 'Ibu Malin Kundang menangkap ikan dan menjualnya untuk menghidupi keluarga.'],
                ['q' => 'バワン・プティはなぜ小さいかぼちゃを選びましたか。', 'options' => ['謙虚だから' => true, '大きいのが嫌いだから' => false, 'おばあさんに言われたから' => false, '小さい方が軽いから' => false], 'why' => 'Bawang Putih memilih labu kecil karena sifatnya yang rendah hati.'],
                ['q' => 'ティムン・マスは鬼から逃げるために何を使いましたか。', 'options' => ['魔法の袋' => true, '剣' => false, '馬' => false, '船' => false], 'why' => 'Timun Mas menggunakan empat kantong ajaib untuk melarikan diri dari raksasa.'],
                ['q' => 'マリンは最後にどうなりましたか。', 'options' => ['石になった' => true, 'お金持ちになった' => false, '国に帰った' => false, '船長になった' => false], 'why' => 'Malin Kundang dikutuk menjadi batu setelah menyangkal ibunya sendiri.'],
                [
                    'q' => 'なぜマリン・クンダンの母親は彼を呪ったのですか。',
                    'options' => ['息子だと認めず、恥じたから' => true, 'お金を渡さなかったから' => false, '妻に会わせなかったから' => false, '漁を手伝わなかったから' => false],
                    'why' => 'Malin Kundang durhaka karena menyangkal ibunya yang miskin di depan istrinya, sehingga sang ibu murka dan mengutuknya.',
                ],
                [
                    'q' => 'マリン・クンダンはどのようにして裕福な商人になりましたか。',
                    'options' => ['遠い町に渡り、商売で成功して裕福な女性と結婚した' => true, '宝物を見つけたから' => false, '王様に仕えたから' => false, '漁で大成功したから' => false],
                    'why' => 'Malin merantau ke kota lain, menjadi pedagang yang sukses, lalu menikahi seorang wanita kaya.',
                ],
                [
                    'q' => 'マリン・クンダンが母親を「知らない」と否定したとき、そばにいたのは誰でしたか。',
                    'options' => ['彼の妻' => true, '彼の息子' => false, '彼の友人' => false, '村の長' => false],
                    'why' => 'Ia menyangkal ibunya di depan istrinya karena malu ibunya terlihat miskin dan compang-camping.',
                ],
                [
                    'q' => 'マリン・クンダンの物語が伝える教訓は何ですか。',
                    'options' => ['親を敬い、感謝の気持ちを忘れてはいけないということ' => true, 'お金がすべてだということ' => false, '遠くへ旅すべきだということ' => false, '漁師になるべきだということ' => false],
                    'why' => 'Kisah ini mengajarkan agar anak tidak durhaka dan selalu menghormati serta berbakti kepada orang tua.',
                ],
                [
                    'q' => '母親がマリン・クンダンを呪った直後、何が起こりましたか。',
                    'options' => ['激しい嵐が起こり、彼の船を襲った' => true, '太陽が輝き始めた' => false, '村人が彼を歓迎した' => false, '川が干上がった' => false],
                    'why' => 'Setelah kutukan sang ibu, badai dahsyat menghantam kapal Malin Kundang, awal dari kutukannya menjadi batu.',
                ],
                [
                    'q' => 'バワン・プティは継母と義理の姉バワン・メラからどのように扱われましたか。',
                    'options' => ['家事をすべて押し付けられ、ひどく扱われた' => true, '大切に育てられた' => false, '学校に通わせてもらった' => false, '一緒に遊んでもらった' => false],
                    'why' => 'Sejak ayahnya meninggal, Bawang Putih diperlakukan buruk oleh ibu tiri dan Bawang Merah, dipaksa mengerjakan semua pekerjaan rumah.',
                ],
                [
                    'q' => 'バワン・メラが大きいかぼちゃを開けたとき、中から何が出てきましたか。',
                    'options' => ['蛇や虫などの危険な生き物' => true, '金貨や宝石' => false, '美しい着物' => false, '何も入っていなかった' => false],
                    'why' => 'Karena keserakahannya memilih labu besar, Bawang Merah malah mendapati labu itu berisi ular dan serangga berbahaya, bukan harta.',
                ],
                [
                    'q' => '森の中でバワン・プティにかぼちゃを選ばせたのは誰でしたか。',
                    'options' => ['親切な老婆（おばあさん）' => true, '王子様' => false, '魔法使い' => false, '妖精' => false],
                    'why' => 'Bawang Putih membantu seorang nenek tua di dalam hutan, dan sebagai balasannya nenek itu mempersilakannya memilih salah satu labu.',
                ],
                [
                    'q' => 'バワン・プティの服が川に流されてしまったのはなぜですか。',
                    'options' => ['継母の服を川で洗っていたときに誤って流してしまったから' => true, '川で泳いでいたから' => false, '誰かに盗まれたから' => false, '洪水が起きたから' => false],
                    'why' => 'Saat mencuci pakaian ibu tirinya di sungai, salah satu pakaian terbawa arus, dan pencarian itulah yang membawanya bertemu nenek tua di hutan.',
                ],
                [
                    'q' => 'バワン・プティはおばあさんの家で何をして手伝いましたか。',
                    'options' => ['掃除や洗濯などの家事を手伝った' => true, '料理だけを作った' => false, '何も手伝わなかった' => false, '畑仕事だけをした' => false],
                    'why' => 'Bawang Putih dengan tulus membantu nenek tua membersihkan rumah dan mencuci selama tinggal di sana, sehingga diberi imbalan.',
                ],
                [
                    'q' => 'バワン・プティとバワン・メラの物語が伝える教訓は何ですか。',
                    'options' => ['謙虚で正直な人は報われ、欲張りな人は罰を受けるということ' => true, '美しさが一番大切だということ' => false, 'お金だけが幸せをもたらすということ' => false, '姉妹は仲良くすべきではないということ' => false],
                    'why' => 'Kisah ini mengajarkan bahwa kebaikan hati dan kejujuran akan membawa berkah, sedangkan keserakahan akan berujung petaka.',
                ],
                [
                    'q' => 'ティムン・マスの両親はどのようにして娘を授かりましたか。',
                    'options' => ['子供のいない夫婦が鬼からきゅうりの種をもらい、育てた' => true, '川で赤ちゃんを見つけた' => false, '神様から授かった' => false, '隣人から譲り受けた' => false],
                    'why' => 'Pasangan petani tua yang tidak memiliki anak meminta bantuan raksasa, yang kemudian memberi mereka biji timun untuk ditanam.',
                ],
                [
                    'q' => 'きゅうりの種をもらう代わりに、両親は鬼と何を約束しましたか。',
                    'options' => ['娘が6歳になったら鬼に差し出すという約束' => true, '毎年お米を差し出すという約束' => false, '娘を鬼の弟子にするという約束' => false, '村を鬼に譲るという約束' => false],
                    'why' => 'Sebagai syarat mendapatkan anak, pasangan itu berjanji akan menyerahkan anak perempuannya kepada raksasa saat berusia enam tahun.',
                ],
                [
                    'q' => 'ティムン・マスが逃げる途中、魔法の袋からきゅうりの種をまくと何になりましたか。',
                    'options' => ['一面のきゅうり畑' => true, '竹林' => false, '広い海' => false, '高い山' => false],
                    'why' => 'Biji timun yang ditaburkan tumbuh seketika menjadi ladang timun yang luas, memperlambat kejaran raksasa yang berhenti untuk memakannya.',
                ],
                [
                    'q' => 'ティムン・マスが塩を鬼に向かってまくと、何が起こりましたか。',
                    'options' => ['広い海が現れた' => true, '高い山ができた' => false, '竹林ができた' => false, '火事が起きた' => false],
                    'why' => 'Garam yang ditaburkan berubah menjadi lautan luas yang menghalangi dan memperlambat langkah raksasa.',
                ],
                [
                    'q' => '物語の最後、ティムン・マスを追ってきた鬼はどうなりましたか。',
                    'options' => ['ぬかるみ（泥の沼）に沈んで死んだ' => true, '改心して村人になった' => false, '人間に捕まって処罰された' => false, '海の向こうへ逃げ延びた' => false],
                    'why' => 'Bumbu terasi yang dilempar Timun Mas berubah menjadi lautan lumpur yang menenggelamkan raksasa hingga tewas, menyelamatkan Timun Mas.',
                ],
            ]);

            $this->create('jlpt', 'Kuis Tinjauan N5', 'medium', 'N5', [
                ['q' => '「あ」は何行ですか。', 'options' => ['あ行' => true, 'か行' => false, 'さ行' => false, 'た行' => false], 'why' => '「あ」adalah huruf pertama pada baris vokal (あ行).'],
                ['q' => '「山」の読み方は？', 'options' => ['やま' => true, 'かわ' => false, 'うみ' => false, 'そら' => false], 'why' => '「山」(yama) berarti gunung.'],
                ['q' => '「毎日」の意味は？', 'options' => ['setiap hari' => true, 'setiap minggu' => false, 'setiap bulan' => false, 'setiap tahun' => false], 'why' => '「毎日」(mainichi) berarti "setiap hari".'],
                ['q' => '「〜てください」の使い方は？', 'options' => ['permintaan sopan' => true, 'larangan' => false, 'pertanyaan' => false, 'pujian' => false], 'why' => '「〜てください」dipakai untuk meminta seseorang melakukan sesuatu dengan sopan.'],
                ['q' => '「五」の読み方は？', 'options' => ['ご' => true, 'よん' => false, 'はち' => false, 'きゅう' => false], 'why' => '「五」(go) berarti angka lima.'],
                [
                    'q' => '「か、き、く、け、こ」は何行ですか。',
                    'options' => ['か行' => true, 'さ行' => false, 'た行' => false, 'な行' => false],
                    'why' => '「かきくけこ」は五十音表の「か行」に属します。',
                ],
                [
                    'q' => '「コーヒー」の意味は何ですか。',
                    'options' => ['Kopi' => true, 'Teh' => false, 'Susu' => false, 'Air' => false],
                    'why' => '「コーヒー」はカタカナで書かれた外来語で、「kopi」という意味です。',
                ],
                [
                    'q' => '「川」の読み方はどれですか。',
                    'options' => ['かわ' => true, 'やま' => false, 'き' => false, 'つち' => false],
                    'why' => '「川」は「かわ」と読み、「sungai」という意味です。',
                ],
                [
                    'q' => '「火」の読み方はどれですか。',
                    'options' => ['ひ' => true, 'みず' => false, 'つち' => false, 'き' => false],
                    'why' => '「火」は「ひ」と読み、「api」という意味です。',
                ],
                [
                    'q' => '「木」の意味は何ですか。',
                    'options' => ['Pohon' => true, 'Air' => false, 'Tanah' => false, 'Api' => false],
                    'why' => '「木」は「pohon/kayu」という意味です。',
                ],
                [
                    'q' => '「食べる」の意味は何ですか。',
                    'options' => ['Makan' => true, 'Minum' => false, 'Pergi' => false, 'Datang' => false],
                    'why' => '「食べる」は「makan」という意味です。',
                ],
                [
                    'q' => '「飲む」の意味は何ですか。',
                    'options' => ['Minum' => true, 'Makan' => false, 'Membaca' => false, 'Menulis' => false],
                    'why' => '「飲む」は「minum」という意味です。',
                ],
                [
                    'q' => '「食べます」はどの形ですか。',
                    'options' => ['ます形（丁寧形）' => true, '辞書形' => false, 'て形' => false, 'ない形' => false],
                    'why' => '「ます」で終わる形は動詞の丁寧な言い方（ます形）を表します。',
                ],
                [
                    'q' => '窓を開け＿ください。',
                    'options' => ['て' => true, 'た' => false, 'ない' => false, 'ます' => false],
                    'why' => '「～てください」は相手に丁寧にお願いするときの表現です。',
                ],
                [
                    'q' => '「食べません」の普通形（ない形）はどれですか。',
                    'options' => ['食べない' => true, '食べます' => false, '食べた' => false, '食べて' => false],
                    'why' => '丁寧形「食べません」に対応する普通形（ない形）は「食べない」です。',
                ],
                [
                    'q' => '「六」の読み方はどれですか。',
                    'options' => ['ろく' => true, 'なな' => false, 'はち' => false, 'きゅう' => false],
                    'why' => '「六」は「ろく」と読み、数字の6を表します。',
                ],
                [
                    'q' => '「九」の読み方はどれですか。',
                    'options' => ['きゅう' => true, 'はち' => false, 'じゅう' => false, 'なな' => false],
                    'why' => '「九」は「きゅう」と読み、数字の9を表します。',
                ],
                [
                    'q' => '「学校」の意味は何ですか。',
                    'options' => ['Sekolah' => true, 'Rumah' => false, 'Kantor' => false, 'Rumah sakit' => false],
                    'why' => '「学校」は「sekolah」という意味です。',
                ],
                [
                    'q' => '昨日、映画を見＿。',
                    'options' => ['ました' => true, 'ます' => false, 'ません' => false, 'てください' => false],
                    'why' => '「昨日」（kemarin）は過去の出来事を表すので、丁寧な過去形「ました」を使います。',
                ],
                [
                    'q' => '「ん」は何行に属しますか。',
                    'options' => ['どの行にも属さない（特別な文字）' => true, 'あ行' => false, 'な行' => false, 'ま行' => false],
                    'why' => '「ん」は撥音と呼ばれる特別な文字で、五十音表のどの行にも属しません。',
                ],
            ]);

            $this->create('bahasa-inggris-umum', 'Kuis Bahasa Inggris Dasar', 'easy', 'Beginner', [
                ['q' => "How do you say 'terima kasih' in English?", 'options' => ['Thank you' => true, 'Sorry' => false, 'Please' => false, 'Excuse me' => false], 'why' => "'Thank you' adalah ungkapan terima kasih dalam bahasa Inggris."],
                ['q' => 'Choose the correct greeting for the morning:', 'options' => ['Good morning' => true, 'Good night' => false, 'Good evening' => false, 'Goodbye' => false], 'why' => "'Good morning' dipakai untuk menyapa di pagi hari."],
                ['q' => "'Family' artinya?", 'options' => ['keluarga' => true, 'teman' => false, 'tetangga' => false, 'rekan kerja' => false], 'why' => "'Family' berarti keluarga."],
                ['q' => "Pilih kata kerja yang tepat: 'I ___ to school every day.'", 'options' => ['go' => true, 'goes' => false, 'going' => false, 'gone' => false], 'why' => 'Subjek "I" memakai bentuk dasar kata kerja dalam simple present.'],
                ['q' => "'Monday' adalah hari?", 'options' => ['Senin' => true, 'Selasa' => false, 'Minggu' => false, 'Sabtu' => false], 'why' => "'Monday' berarti hari Senin."],
                [
                    'q' => 'Apa arti "Good evening"?',
                    'options' => ['Selamat sore/malam (saat bertemu)' => true, 'Selamat pagi' => false, 'Selamat tidur' => false, 'Selamat tinggal' => false],
                    'why' => '"Good evening" digunakan untuk menyapa seseorang pada sore atau awal malam hari.',
                ],
                [
                    'q' => 'Kapan kita mengucapkan "Good night"?',
                    'options' => ['Saat akan tidur atau berpisah di malam hari' => true, 'Saat bertemu di pagi hari' => false, 'Saat makan siang' => false, 'Saat berangkat kerja' => false],
                    'why' => '"Good night" diucapkan saat akan tidur atau berpamitan pada malam hari.',
                ],
                [
                    'q' => 'Apa arti "How are you?"?',
                    'options' => ['Bagaimana kabarmu?' => true, 'Siapa namamu?' => false, 'Di mana rumahmu?' => false, 'Berapa umurmu?' => false],
                    'why' => '"How are you?" adalah pertanyaan untuk menanyakan kabar seseorang.',
                ],
                [
                    'q' => 'Apa arti kata "Mother"?',
                    'options' => ['Ibu' => true, 'Ayah' => false, 'Kakak' => false, 'Bibi' => false],
                    'why' => '"Mother" artinya ibu dalam bahasa Indonesia.',
                ],
                [
                    'q' => 'Apa arti kata "Father"?',
                    'options' => ['Ayah' => true, 'Ibu' => false, 'Paman' => false, 'Kakek' => false],
                    'why' => '"Father" artinya ayah dalam bahasa Indonesia.',
                ],
                [
                    'q' => 'Apa arti kata "Sister"?',
                    'options' => ['Saudara perempuan' => true, 'Saudara laki-laki' => false, 'Ibu' => false, 'Bibi' => false],
                    'why' => '"Sister" artinya saudara perempuan (kakak atau adik perempuan).',
                ],
                [
                    'q' => '"Tuesday" dalam bahasa Indonesia adalah?',
                    'options' => ['Selasa' => true, 'Senin' => false, 'Rabu' => false, 'Kamis' => false],
                    'why' => '"Tuesday" adalah hari Selasa, hari kedua dalam seminggu jika dimulai dari Senin.',
                ],
                [
                    'q' => '"Sunday" dalam bahasa Indonesia adalah?',
                    'options' => ['Minggu' => true, 'Sabtu' => false, 'Senin' => false, 'Jumat' => false],
                    'why' => '"Sunday" adalah hari Minggu, biasanya hari libur di akhir pekan.',
                ],
                [
                    'q' => '"January" dalam bahasa Indonesia adalah?',
                    'options' => ['Januari' => true, 'Februari' => false, 'Juni' => false, 'Juli' => false],
                    'why' => '"January" adalah bulan pertama dalam setahun, yaitu Januari.',
                ],
                [
                    'q' => 'Angka "five" dalam bahasa Indonesia adalah?',
                    'options' => ['lima' => true, 'empat' => false, 'enam' => false, 'tujuh' => false],
                    'why' => '"Five" artinya lima.',
                ],
                [
                    'q' => 'Angka "ten" dalam bahasa Indonesia adalah?',
                    'options' => ['sepuluh' => true, 'sembilan' => false, 'delapan' => false, 'seratus' => false],
                    'why' => '"Ten" artinya sepuluh.',
                ],
                [
                    'q' => 'Warna "red" dalam bahasa Indonesia adalah?',
                    'options' => ['merah' => true, 'biru' => false, 'kuning' => false, 'hijau' => false],
                    'why' => '"Red" artinya merah.',
                ],
                [
                    'q' => 'Warna "blue" dalam bahasa Indonesia adalah?',
                    'options' => ['biru' => true, 'merah' => false, 'hijau' => false, 'ungu' => false],
                    'why' => '"Blue" artinya biru.',
                ],
                [
                    'q' => 'Pilih kata kerja yang tepat: "She ___ a book every day."',
                    'options' => ['reads' => true, 'read' => false, 'reading' => false, 'to read' => false],
                    'why' => 'Untuk subjek "she" (orang ketiga tunggal) dalam simple present tense, kata kerja ditambah akhiran -s/-es, sehingga menjadi "reads".',
                ],
                [
                    'q' => 'Apa arti kata ganti "They"?',
                    'options' => ['Mereka' => true, 'Kami' => false, 'Kita' => false, 'Dia' => false],
                    'why' => '"They" adalah kata ganti orang ketiga jamak yang berarti "mereka".',
                ],
            ]);

            $this->create('reading', 'Kuis Pemahaman Cerita Rakyat (Inggris)', 'medium', 'Intermediate', [
                ['q' => "Why did Malin Kundang's mother curse him?", 'options' => ['He refused to acknowledge her' => true, 'He stole money from her' => false, 'He left home without saying goodbye' => false, 'He married without her blessing' => false], 'why' => "Malin's mother cursed him after he publicly denied her as his mother."],
                ['q' => 'What did Bawang Putih choose from the old woman?', 'options' => ['The small pumpkin' => true, 'The big pumpkin' => false, 'Neither pumpkin' => false, 'Both pumpkins' => false], 'why' => 'Being humble, Bawang Putih chose the small pumpkin, which turned out to be full of treasure.'],
                ['q' => 'What did Timun Mas use to escape the giant?', 'options' => ['Four magic bags' => true, 'A magic sword' => false, 'A hidden boat' => false, 'A talking animal' => false], 'why' => 'Timun Mas used cucumber seeds, needles, salt, and shrimp paste from her magic bags.'],
                ['q' => 'What happened when Bawang Merah opened the big pumpkin?', 'options' => ['Snakes and insects came out' => true, 'It was full of gold' => false, 'It was empty' => false, 'It turned to stone' => false], 'why' => 'Her greed was punished — the big pumpkin contained dangerous snakes and insects instead of treasure.'],
                [
                    'q' => 'Why did Malin Kundang decide to leave his village and sail away with a merchant ship?',
                    'options' => [
                        'He wanted to seek his fortune and improve his family\'s life' => true,
                        'He was banished by the village chief for stealing' => false,
                        'He was forced to join the navy' => false,
                        'He wanted to escape an arranged marriage' => false,
                    ],
                    'why' => 'Malin Kundang left home with a passing merchant ship because he wanted to find a better life and earn wealth for himself and his mother.',
                ],
                [
                    'q' => 'What happened to Malin Kundang\'s ship and its crew right after his mother\'s curse?',
                    'options' => [
                        'A sudden violent storm wrecked the ship, and the crew perished' => true,
                        'The ship safely returned to its home port' => false,
                        'The crew mutinied and threw Malin Kundang overboard' => false,
                        'The ship was captured by pirates' => false,
                    ],
                    'why' => 'As soon as the mother finished her curse, the sky darkened and a fierce storm destroyed the ship, killing the crew.',
                ],
                [
                    'q' => 'What did Malin Kundang ultimately turn into as a result of his mother\'s curse?',
                    'options' => [
                        'A large stone statue on the beach' => true,
                        'A tree that never bears fruit' => false,
                        'A seabird that cries at night' => false,
                        'A pile of ash washed away by the tide' => false,
                    ],
                    'why' => 'The curse transformed Malin Kundang\'s body into stone, which legend says can still be seen on the beach today.',
                ],
                [
                    'q' => 'Why did Malin Kundang refuse to acknowledge the old woman as his mother when he returned as a rich merchant?',
                    'options' => [
                        'He was ashamed of her poor and shabby appearance in front of his wife' => true,
                        'He genuinely did not recognize her face after so many years' => false,
                        'His wife forbade him from speaking to strangers' => false,
                        'He believed his real mother had already died' => false,
                    ],
                    'why' => 'Malin Kundang denied his mother because he felt embarrassed by her ragged clothes and humble status in front of his wealthy wife.',
                ],
                [
                    'q' => 'What is the main moral lesson taught by the story of Malin Kundang?',
                    'options' => [
                        'Children must always respect and honor their parents, no matter how poor they are' => true,
                        'Hard work always leads to great wealth' => false,
                        'Sailors should never trust the sea' => false,
                        'Wealth should always be shared with the whole village' => false,
                    ],
                    'why' => 'The tale is a cautionary story warning that disrespecting or disowning one\'s parents brings terrible consequences.',
                ],
                [
                    'q' => 'Where is the legendary "Malin Kundang stone" said to be located, according to the folk tale?',
                    'options' => [
                        'On a beach in West Sumatra' => true,
                        'On a mountain peak in Java' => false,
                        'In a cave in Kalimantan' => false,
                        'By a riverbank in Sulawesi' => false,
                    ],
                    'why' => 'The legend places the stone believed to be Malin Kundang\'s petrified body on a beach in West Sumatra, near Padang.',
                ],
                [
                    'q' => 'How did Bawang Putih\'s stepmother and stepsister generally treat her at home?',
                    'options' => [
                        'They forced her to do all the housework while they lived comfortably and did nothing' => true,
                        'They treated her exactly the same as Bawang Merah' => false,
                        'They spoiled her with gifts to gain her trust' => false,
                        'They sent her away to live with relatives' => false,
                    ],
                    'why' => 'Bawang Putih was made to cook, clean, and wash for the household while her stepmother and stepsister lived idly.',
                ],
                [
                    'q' => 'What incident led Bawang Putih to search along the river and eventually meet an old woman in the forest?',
                    'options' => [
                        'One of her mother\'s sarongs was swept away by the river current while she was washing clothes' => true,
                        'She fell into the river while fetching water' => false,
                        'She was chasing a runaway chicken' => false,
                        'She was sent to find medicinal herbs' => false,
                    ],
                    'why' => 'While doing laundry at the river, Bawang Putih accidentally let a sarong slip away in the current and had to follow it downstream.',
                ],
                [
                    'q' => 'Who did Bawang Putih meet while searching for the lost sarong, and how did this person help her?',
                    'options' => [
                        'An old woman (nenek) who let her stay, helped with chores, and later gave her a choice of pumpkins as a reward' => true,
                        'A talking deer who guided her back home' => false,
                        'A fairy who granted her three wishes' => false,
                        'A merchant who bought the sarong from her' => false,
                    ],
                    'why' => 'The kind old woman took Bawang Putih in, and after Bawang Putih helped her willingly with housework, rewarded her with a choice of pumpkins.',
                ],
                [
                    'q' => 'What is the central moral lesson of the story of Bawang Putih and Bawang Merah?',
                    'options' => [
                        'Kindness and honesty are eventually rewarded, while greed and cruelty are punished' => true,
                        'Beauty is more important than good behavior' => false,
                        'Family members should always be treated equally regardless of their actions' => false,
                        'Hard work alone guarantees riches' => false,
                    ],
                    'why' => 'The story contrasts Bawang Putih\'s humility and kindness, which are rewarded, with Bawang Merah and her mother\'s greed, which brings punishment.',
                ],
                [
                    'q' => 'What ultimately happened to Bawang Merah and her mother at the end of the story?',
                    'options' => [
                        'They faced punishment and disgrace because of their greed and jealousy' => true,
                        'They were forgiven and welcomed to live happily with Bawang Putih' => false,
                        'They moved to another village and became wealthy honestly' => false,
                        'They were rewarded despite their bad behavior' => false,
                    ],
                    'why' => 'As a consequence of their greed and mistreatment of Bawang Putih, Bawang Merah and her mother suffered disgrace and hardship in the end.',
                ],
                [
                    'q' => 'According to the story, why did the old childless couple wish so deeply for a child before Timun Mas was born?',
                    'options' => [
                        'They had been married for years but had never been able to have children of their own' => true,
                        'Their only child had died young' => false,
                        'They wanted a child to inherit their farmland' => false,
                        'The village required every family to raise a child' => false,
                    ],
                    'why' => 'The couple, especially the old woman, had long prayed for a child because they remained childless despite years of marriage.',
                ],
                [
                    'q' => 'What did the old couple promise the giant (raksasa) in exchange for the magical cucumber seed that produced Timun Mas?',
                    'options' => [
                        'They agreed to give the child back to the giant to be eaten once she grew up' => true,
                        'They promised to give the giant half of their harvest every year' => false,
                        'They promised the child would marry the giant\'s son' => false,
                        'They promised to build the giant a new home' => false,
                    ],
                    'why' => 'The giant gave the couple a cucumber seed on the condition that when the resulting child grew up, she would be surrendered to him as food.',
                ],
                [
                    'q' => 'What was the name commonly given to the giant/ogre who demanded Timun Mas in the traditional telling of the story?',
                    'options' => [
                        'Buto Ijo' => true,
                        'Ratu Boko' => false,
                        'Nyi Roro Kidul' => false,
                        'Ki Ageng' => false,
                    ],
                    'why' => 'In the well-known Javanese version of the tale, the giant who gives the cucumber seed and later hunts Timun Mas is named Buto Ijo.',
                ],
                [
                    'q' => 'When the giant chased Timun Mas, what did the salt she threw behind her transform into to block his path?',
                    'options' => [
                        'A vast sea or ocean' => true,
                        'A wall of fire' => false,
                        'A thick fog' => false,
                        'A field of thorns' => false,
                    ],
                    'why' => 'Among the magical gifts from the hermit, the salt turned into a wide sea when thrown, temporarily stopping the giant\'s pursuit.',
                ],
                [
                    'q' => 'What did the needles that Timun Mas threw behind her turn into during her escape from the giant?',
                    'options' => [
                        'A dense bamboo forest' => true,
                        'A river full of crocodiles' => false,
                        'A rocky mountain' => false,
                        'A swarm of bees' => false,
                    ],
                    'why' => 'One of the magical items given to Timun Mas was a handful of needles, which turned into a sharp, dense bamboo forest to slow the giant down.',
                ],
            ]);

            $this->create('listening', 'Kuis Pemahaman Menyimak', 'medium', 'Intermediate', [
                ['q' => 'In the airport announcement, which gate is boarding?', 'options' => ['Gate 12' => true, 'Gate 15' => false, 'Gate 20' => false, 'Gate 8' => false], 'why' => 'The announcement states boarding begins at gate 12.'],
                ['q' => 'In the restaurant dialogue, what did the customer order to drink?', 'options' => ['A glass of water' => true, 'Coffee' => false, 'Orange juice' => false, 'Tea' => false], 'why' => 'The customer simply asked for a glass of water.'],
                ['q' => 'In the voicemail, who is calling?', 'options' => ['Sarah from Bright Solutions' => true, 'Mr. Andi' => false, 'A restaurant manager' => false, 'A recruiter' => false], 'why' => 'Sarah introduces herself as calling from Bright Solutions.'],
                ['q' => 'What is the weather like in the afternoon in the forecast?', 'options' => ['Sunny' => true, 'Rainy' => false, 'Snowy' => false, 'Stormy' => false], 'why' => 'The forecast says the sky clears up and becomes sunny by the afternoon.'],
                [
                    'q' => 'In the audio, a passenger hears an announcement explaining why her flight has been delayed. What reason does the announcement give?',
                    'options' => [
                        'Bad weather conditions at the destination airport' => true,
                        'A shortage of cabin crew' => false,
                        'A mechanical problem found during inspection' => false,
                        'Overbooking of the flight' => false,
                    ],
                    'why' => 'The announcement clearly states that the flight is delayed because of bad weather conditions at the destination.',
                ],
                [
                    'q' => 'A short airport announcement tells arriving passengers which carousel to use for their luggage. Which carousel number is mentioned?',
                    'options' => [
                        'Carousel 4' => true,
                        'Carousel 1' => false,
                        'Carousel 7' => false,
                        'Carousel 10' => false,
                    ],
                    'why' => 'The announcement specifically directs passengers to collect their baggage from carousel 4.',
                ],
                [
                    'q' => 'In a train station announcement, passengers are told that their train\'s platform has changed. What is the new platform number?',
                    'options' => [
                        'Platform 6' => true,
                        'Platform 2' => false,
                        'Platform 9' => false,
                        'Platform 3' => false,
                    ],
                    'why' => 'The announcement informs passengers that the train has been moved to platform 6 instead of its original platform.',
                ],
                [
                    'q' => 'In a restaurant conversation, a customer orders their main dish. What does the customer order to eat?',
                    'options' => [
                        'Grilled chicken with rice' => true,
                        'A bowl of noodle soup' => false,
                        'A beef burger with fries' => false,
                        'A vegetable salad' => false,
                    ],
                    'why' => 'The customer tells the waiter they would like grilled chicken with rice for their meal.',
                ],
                [
                    'q' => 'A customer calls a restaurant to reserve a table. How many people is the reservation for?',
                    'options' => [
                        'Five people' => true,
                        'Two people' => false,
                        'Eight people' => false,
                        'Three people' => false,
                    ],
                    'why' => 'During the phone call, the customer clearly asks for a table reservation for a party of five.',
                ],
                [
                    'q' => 'At a cafe counter, a customer is asked a question by the staff before their order is prepared. What does the staff member ask?',
                    'options' => [
                        'Whether the order is for dine-in or takeaway' => true,
                        'Whether the customer wants a receipt' => false,
                        'Whether the customer has a loyalty card' => false,
                        'Whether the customer wants extra sugar' => false,
                    ],
                    'why' => 'The staff member asks the customer if they would like to eat in the cafe or take their order away.',
                ],
                [
                    'q' => 'A patient calls a dental clinic to book an appointment. On which day is the appointment eventually scheduled?',
                    'options' => [
                        'Thursday' => true,
                        'Monday' => false,
                        'Friday' => false,
                        'Sunday' => false,
                    ],
                    'why' => 'After checking availability, the receptionist confirms the appointment is booked for Thursday.',
                ],
                [
                    'q' => 'A customer calls a company to complain about a package. What is the main issue the customer reports?',
                    'options' => [
                        'The delivery arrived several days later than promised' => true,
                        'The package was delivered to the wrong address' => false,
                        'The item inside the package was the wrong color' => false,
                        'The customer was charged twice for the order' => false,
                    ],
                    'why' => 'The customer explains that their complaint is about the delivery being much later than the promised date.',
                ],
                [
                    'q' => 'In a voicemail message, the caller does not just introduce themselves — they also ask the listener to do something. What does the caller ask the listener to do?',
                    'options' => [
                        'Call back before the end of the day to confirm a meeting' => true,
                        'Send an email with photos attached' => false,
                        'Bring some documents to the office tomorrow' => false,
                        'Cancel a scheduled appointment' => false,
                    ],
                    'why' => 'The voicemail message asks the listener to return the call before the end of the day to confirm the meeting.',
                ],
                [
                    'q' => 'In a weather forecast for tomorrow, what condition does the presenter predict?',
                    'options' => [
                        'Sunny skies with a chance of light wind' => true,
                        'Heavy snowfall throughout the day' => false,
                        'Continuous rain from morning to night' => false,
                        'A tropical storm warning' => false,
                    ],
                    'why' => 'The forecast for tomorrow describes mostly sunny weather with only a slight breeze expected.',
                ],
                [
                    'q' => 'A weekend weather forecast warns listeners about a specific change in the weather. What change does it mention?',
                    'options' => [
                        'Temperatures will drop noticeably on Saturday night' => true,
                        'A heatwave will begin on Sunday' => false,
                        'Strong sunshine will continue all weekend' => false,
                        'Fog will clear up by Friday' => false,
                    ],
                    'why' => 'The forecast specifically warns that temperatures are expected to drop noticeably on Saturday night.',
                ],
                [
                    'q' => 'In a shop, a customer asks about the price of a jacket after a discount is applied. What is the final price mentioned?',
                    'options' => [
                        '150,000 rupiah' => true,
                        '200,000 rupiah' => false,
                        '100,000 rupiah' => false,
                        '250,000 rupiah' => false,
                    ],
                    'why' => 'The shop assistant tells the customer that after the discount, the jacket costs 150,000 rupiah.',
                ],
                [
                    'q' => 'A customer calls a store to ask about its operating hours. What time does the staff member say the store closes?',
                    'options' => [
                        '9 p.m.' => true,
                        '6 p.m.' => false,
                        '10 a.m.' => false,
                        'Midnight' => false,
                    ],
                    'why' => 'The staff member informs the caller that the store closes at 9 p.m. every day.',
                ],
                [
                    'q' => 'In a set of simple spoken instructions, a person explains how to set up a new device. What is the first step mentioned?',
                    'options' => [
                        'Press and hold the power button for five seconds' => true,
                        'Connect the device to a charger overnight' => false,
                        'Download an app before turning it on' => false,
                        'Insert the batteries provided in the box' => false,
                    ],
                    'why' => 'The instructions begin by telling the listener to press and hold the power button for five seconds to start the device.',
                ],
                [
                    'q' => 'A stranger gives spoken directions to help someone find the nearest post office. According to the directions, what should the listener do first?',
                    'options' => [
                        'Turn left at the second traffic light' => true,
                        'Cross the bridge near the market' => false,
                        'Walk straight for ten minutes' => false,
                        'Take the first street on the right' => false,
                    ],
                    'why' => 'The directions instruct the listener to first turn left at the second traffic light before continuing to the post office.',
                ],
                [
                    'q' => 'In a phone call, a colleague asks to reschedule a meeting. What new time do they agree on?',
                    'options' => [
                        '2 p.m. on Wednesday' => true,
                        '9 a.m. on Monday' => false,
                        '4 p.m. on Friday' => false,
                        '11 a.m. on Tuesday' => false,
                    ],
                    'why' => 'After discussing their availability, the colleagues agree to move the meeting to 2 p.m. on Wednesday.',
                ],
            ]);

            $this->create('speaking', 'Kuis Frasa Speaking', 'medium', 'Intermediate', [
                ['q' => 'Which sound requires you to bite your lower lip lightly?', 'options' => ['V' => true, 'B' => false, 'T' => false, 'D' => false], 'why' => 'The /v/ sound is made by touching the upper teeth to the lower lip.'],
                ['q' => 'Which is a polite way to start small talk about the weather?', 'options' => ["The weather's been great this week, hasn't it?" => true, 'Give me the weather.' => false, 'Weather now?' => false, 'Is hot.' => false], 'why' => 'A tag question like this is a natural, polite way to open small talk.'],
                ['q' => 'Which phrase is used to summarize a presentation?', 'options' => ['To summarize, our key takeaway is...' => true, 'The end.' => false, 'Bye now.' => false, "That's all I know." => false], 'why' => '"To summarize" signals a formal wrap-up of key points.'],
                ['q' => "How do you politely ask about someone's job?", 'options' => ['What do you do for a living?' => true, 'How much money do you make?' => false, 'Do you even work?' => false, 'What is your salary?' => false], 'why' => '"What do you do for a living?" is the standard polite way to ask about occupation.'],
                [
                    'q' => 'Which pair of words is a minimal pair that differs mainly in the "th" sound /θ/ versus /s/?',
                    'options' => ['think - sink' => true, 'think - thing' => false, 'sink - sing' => false, 'think - thin' => false],
                    'why' => '"Think" starts with the /θ/ sound while "sink" starts with /s/, so they form a minimal pair that many Indonesian learners confuse.',
                ],
                [
                    'q' => 'Which pair of words tests the difference between the /r/ and /l/ sounds?',
                    'options' => ['right - light' => true, 'right - write' => false, 'light - like' => false, 'right - ripe' => false],
                    'why' => '"Right" and "light" are identical except for the initial consonant, /r/ versus /l/, a common pronunciation challenge for Indonesian speakers.',
                ],
                [
                    'q' => 'Which pair of words shows the difference between the short vowel /ɪ/ and the long vowel /iː/?',
                    'options' => ['ship - sheep' => true, 'ship - shop' => false, 'sheep - sheet' => false, 'ship - chip' => false],
                    'why' => '"Ship" uses the short /ɪ/ sound and "sheep" uses the long /iː/ sound; mixing them up can change the meaning of the word entirely.',
                ],
                [
                    'q' => 'In the word "knife," which letter is silent?',
                    'options' => ['k' => true, 'n' => false, 'i' => false, 'f' => false],
                    'why' => 'The "k" in "knife" is not pronounced; the word is spoken as /naɪf/.',
                ],
                [
                    'q' => 'In the word "record," how does the stress change between the noun and the verb form?',
                    'options' => ['Noun stresses the first syllable (RE-cord), verb stresses the second (re-CORD)' => true, 'Both forms stress the first syllable equally' => false, 'Both forms stress the second syllable equally' => false, 'Noun stresses the second syllable, verb stresses the first' => false],
                    'why' => 'Many two-syllable English words shift stress depending on whether they are used as a noun or a verb; "RE-cord" is a noun and "re-CORD" is a verb.',
                ],
                [
                    'q' => 'Which phrase is the most natural way to politely agree with someone during a conversation?',
                    'options' => ['"I couldn\'t agree more."' => true, '"That is completely wrong."' => false, '"Whatever you say."' => false, '"I do not care either way."' => false],
                    'why' => '"I couldn\'t agree more" is a common, friendly way to show strong agreement in spoken English.',
                ],
                [
                    'q' => 'Which phrase is a polite way to disagree with someone without sounding rude?',
                    'options' => ['"I see your point, but I look at it differently."' => true, '"You are completely wrong."' => false, '"That makes no sense at all."' => false, '"I don\'t want to hear it."' => false],
                    'why' => 'Acknowledging the other person\'s view before disagreeing softens the disagreement and keeps the conversation polite.',
                ],
                [
                    'q' => 'Which phrase is commonly used to introduce your own opinion in a discussion?',
                    'options' => ['"In my opinion, ..."' => true, '"It is a fact that ..."' => false, '"Everyone knows that ..."' => false, '"According to the news, ..."' => false],
                    'why' => '"In my opinion" is a standard way to signal that what follows is a personal viewpoint, not a proven fact.',
                ],
                [
                    'q' => 'Which expression is the most polite way to interrupt someone who is speaking?',
                    'options' => ['"Sorry to interrupt, but may I add something?"' => true, '"Stop talking, it\'s my turn now."' => false, '"Be quiet for a second."' => false, '"Listen to me now."' => false],
                    'why' => 'Apologizing before interrupting and asking permission keeps the interruption polite and respectful.',
                ],
                [
                    'q' => 'If you did not hear what someone said, which phrase is the most natural way to ask them to repeat it?',
                    'options' => ['"Could you say that again, please?"' => true, '"What?"' => false, '"Repeat."' => false, '"I heard nothing."' => false],
                    'why' => '"Could you say that again, please?" is a polite, complete request commonly used in spoken English.',
                ],
                [
                    'q' => 'Which question is best for asking someone to explain what a word or phrase means?',
                    'options' => ['"What do you mean by that?"' => true, '"Why do you say things?"' => false, '"Explain everything now."' => false, '"Is that a word?"' => false],
                    'why' => '"What do you mean by that?" directly and politely asks for clarification of meaning during a conversation.',
                ],
                [
                    'q' => 'Which question is a common small-talk opener to ask about someone\'s upcoming free time?',
                    'options' => ['"Do you have any plans for the weekend?"' => true, '"What is your annual salary?"' => false, '"How old are you exactly?"' => false, '"Why are you not working today?"' => false],
                    'why' => 'Asking about weekend plans is a light, friendly small-talk topic commonly used to start casual conversations.',
                ],
                [
                    'q' => 'Which sentence is an appropriate way to give someone a friendly compliment in casual conversation?',
                    'options' => ['"I really like your jacket, it suits you!"' => true, '"Your jacket looks cheap."' => false, '"Why do you always wear that?"' => false, '"That jacket is very old, isn\'t it?"' => false],
                    'why' => 'A genuine, specific compliment like praising someone\'s jacket is a friendly way to build rapport in small talk.',
                ],
                [
                    'q' => 'What intonation pattern is typically used at the end of a yes/no question, such as "Are you coming?"',
                    'options' => ['Rising intonation' => true, 'Falling intonation' => false, 'Flat, unchanging intonation' => false, 'Intonation drops sharply in the middle' => false],
                    'why' => 'Yes/no questions in English usually end with rising intonation, signaling that a yes-or-no answer is expected.',
                ],
                [
                    'q' => 'What intonation pattern is typically used at the end of a wh-question, such as "Where do you live?"',
                    'options' => ['Falling intonation' => true, 'Rising intonation' => false, 'The pitch stays exactly level' => false, 'The pitch rises then rises again' => false],
                    'why' => 'Wh-questions (who, what, where, when, why, how) usually end with falling intonation in natural spoken English.',
                ],
                [
                    'q' => 'Which phrase is a natural filler used to buy time while thinking of what to say next?',
                    'options' => ['"Well, let me think about that..."' => true, '"Immediately, the answer is..."' => false, '"Never mind, forget it."' => false, '"Stop, I need silence."' => false],
                    'why' => 'Fillers like "well, let me think about that" are natural pauses speakers use to gather their thoughts without going silent.',
                ],
            ]);

            $this->create('writing', 'Kuis Struktur Menulis', 'medium', 'Intermediate', [
                ['q' => 'What is the correct structure for a simple sentence?', 'options' => ['Subject + Verb + Object' => true, 'Object + Verb + Subject' => false, 'Verb only' => false, 'Object only' => false], 'why' => 'English simple sentences follow Subject-Verb-Object order.'],
                ['q' => 'What should come first in a paragraph?', 'options' => ['Topic sentence' => true, 'Concluding sentence' => false, 'A random detail' => false, 'A question' => false], 'why' => 'The topic sentence states the main idea before supporting details.'],
                ['q' => 'Which phrase is used to start a formal email?', 'options' => ['Dear Mr./Ms. [Name],' => true, 'Hey!' => false, 'Yo,' => false, "What's up," => false], 'why' => 'Formal emails begin with "Dear" plus the recipient\'s title and name.'],
                ['q' => 'What is the typical structure of narrative writing?', 'options' => ['Orientation, events, climax, resolution' => true, 'Just a list of facts' => false, 'Only dialogue' => false, 'Random sentences' => false], 'why' => 'A narrative follows a clear story arc: setup, events, climax, and resolution.'],
                [
                    'q' => 'What is the main purpose of the supporting sentences in a paragraph?',
                    'options' => ['To provide details and evidence that develop the topic sentence' => true, 'To introduce a completely new topic' => false, 'To restate the title of the essay' => false, 'To ask the reader a rhetorical question' => false],
                    'why' => 'Supporting sentences give facts, examples, or explanations that develop and prove the idea stated in the topic sentence.',
                ],
                [
                    'q' => 'What is the purpose of a concluding sentence at the end of a paragraph?',
                    'options' => ['To summarize the paragraph\'s main idea or transition to the next point' => true, 'To introduce a brand-new argument for the first time' => false, 'To repeat the supporting details word for word' => false, 'To ask a question the writer cannot answer' => false],
                    'why' => 'A concluding sentence wraps up the paragraph\'s idea, often summarizing it or leading smoothly into the next paragraph.',
                ],
                [
                    'q' => 'In a standard five-paragraph essay, what should the introduction paragraph mainly contain?',
                    'options' => ['A hook to grab attention and a thesis statement' => true, 'A detailed summary of every body paragraph' => false, 'A list of references used in the essay' => false, 'The final conclusion of the argument' => false],
                    'why' => 'A strong introduction hooks the reader\'s interest and presents the thesis statement, which states the essay\'s main argument.',
                ],
                [
                    'q' => 'What is the main function of a body paragraph in an essay?',
                    'options' => ['To develop one main idea that supports the thesis statement' => true, 'To introduce the essay\'s topic for the first time' => false, 'To restate the thesis without any new information' => false, 'To list the writer\'s personal biography' => false],
                    'why' => 'Each body paragraph should focus on one supporting idea, backed with evidence, that helps prove the essay\'s thesis.',
                ],
                [
                    'q' => 'What should a strong conclusion paragraph typically do?',
                    'options' => ['Restate the thesis in new words and give a final closing thought' => true, 'Introduce a completely new argument not mentioned before' => false, 'Copy the introduction paragraph exactly' => false, 'End abruptly without connecting to the essay' => false],
                    'why' => 'A good conclusion reminds the reader of the thesis in different words and leaves them with a final, memorable thought.',
                ],
                [
                    'q' => 'Which sentence is more appropriate for formal academic writing?',
                    'options' => ['"The results indicate a significant improvement."' => true, '"The results are, like, way better."' => false, '"Yo, the results got way better!"' => false, '"Results r better, fyi."' => false],
                    'why' => 'Formal writing avoids slang, contractions, and casual abbreviations, using precise and objective language instead.',
                ],
                [
                    'q' => 'Why should contractions such as "don\'t" and "can\'t" generally be avoided in formal writing?',
                    'options' => ['Because formal writing prefers the full, uncontracted forms like "do not" and "cannot"' => true, 'Because contractions are grammatically incorrect in all contexts' => false, 'Because contractions can only be used in questions' => false, 'Because formal writing does not allow negative statements' => false],
                    'why' => 'Formal writing conventions favor full forms ("do not," "cannot") over contractions, which are seen as too casual.',
                ],
                [
                    'q' => 'In the sentence "I bought apples, oranges, and bananas," what is the comma before "and bananas" called?',
                    'options' => ['The Oxford (serial) comma' => true, 'The semicolon comma' => false, 'The apostrophe comma' => false, 'The hyphenated comma' => false],
                    'why' => 'The comma placed before the final "and" in a list of three or more items is known as the Oxford or serial comma.',
                ],
                [
                    'q' => 'Which sentence correctly uses a semicolon to join two related independent clauses?',
                    'options' => ['"I finished my homework; now I can relax."' => true, '"I finished my homework, now I can relax."' => false, '"I finished; my, homework now I can relax."' => false, '"I finished my homework: now I can relax and."' => false],
                    'why' => 'A semicolon can join two closely related independent clauses without needing a coordinating conjunction like "and."',
                ],
                [
                    'q' => 'Which sentence correctly uses an apostrophe to show possession?',
                    'options' => ['"The teacher\'s book is on the desk."' => true, '"The teachers book is on the desk."' => false, '"The teacher\'s\' book is on the desk."' => false, '"The teachers\'s book is on the desk."' => false],
                    'why' => 'To show singular possession, an apostrophe plus "s" is added to the noun, as in "teacher\'s."',
                ],
                [
                    'q' => 'Which transition word is best used to add an additional point to an argument?',
                    'options' => ['Furthermore' => true, 'However' => false, 'Although' => false, 'Instead' => false],
                    'why' => '"Furthermore" is used to add extra supporting information to a point already made, signaling addition rather than contrast.',
                ],
                [
                    'q' => 'Which transition word is best used to show contrast between two ideas?',
                    'options' => ['However' => true, 'Moreover' => false, 'In addition' => false, 'Similarly' => false],
                    'why' => '"However" signals a contrast or shift between two ideas, unlike words such as "moreover" that add similar information.',
                ],
                [
                    'q' => 'Which transition phrase best shows a cause-and-effect relationship between two sentences?',
                    'options' => ['"As a result, ..."' => true, '"On the other hand, ..."' => false, '"For example, ..."' => false, '"In contrast, ..."' => false],
                    'why' => '"As a result" links a cause to its effect, showing that the second sentence happened because of the first.',
                ],
                [
                    'q' => 'Which set of transition words is most appropriate for describing steps in a sequence?',
                    'options' => ['First, next, finally' => true, 'However, but, yet' => false, 'Similarly, likewise, also' => false, 'Because, since, as' => false],
                    'why' => '"First, next, finally" are sequence transitions that show the order in which steps or events occur.',
                ],
                [
                    'q' => 'Which closing is most appropriate for a formal business email?',
                    'options' => ['"Best regards,"' => true, '"See ya later!"' => false, '"Peace out,"' => false, '"Later,"' => false],
                    'why' => '"Best regards" is a polite, professional sign-off suitable for formal business correspondence.',
                ],
                [
                    'q' => 'When writing a formal letter to an unknown recipient, which greeting is most appropriate?',
                    'options' => ['"To Whom It May Concern,"' => true, '"Hey there,"' => false, '"What\'s up,"' => false, '"Yo boss,"' => false],
                    'why' => '"To Whom It May Concern" is the standard formal greeting used when the writer does not know the specific recipient\'s name.',
                ],
            ]);

            $this->create('toefl', 'Kuis Struktur dan Kosakata TOEFL', 'hard', 'Upper-Intermediate', [
                ['q' => "'___ the rain, they went for a walk.' Which word fits?", 'options' => ['Despite' => true, 'Because' => false, 'Since' => false, 'Unless' => false], 'why' => '"Despite" is followed by a noun phrase, not a full clause.'],
                ['q' => "Find the error: 'She don't like spicy food.'", 'options' => ["'don't' should be 'doesn't'" => true, "'like' should be 'likes'" => false, "'food' should be 'foods'" => false, "'spicy' should be 'spicier'" => false], 'why' => 'Third-person singular subjects ("she") require "doesn\'t" in negative sentences.'],
                ['q' => "What does 'abundant' mean?", 'options' => ['berlimpah' => true, 'langka' => false, 'kecil' => false, 'kontroversial' => false], 'why' => "'Abundant' means existing in large quantities."],
                ['q' => 'What is the main idea of a passage about the Amazon rainforest facing deforestation?', 'options' => ['Its importance and the threats it faces' => true, 'The history of Brazil' => false, 'A list of animal species only' => false, 'Tourism in South America' => false], 'why' => 'The passage centers on the rainforest\'s ecological importance and deforestation threats.'],
                [
                    'q' => 'Identify the segment containing a grammatical error: "The committee (A: have decided) (B: to postpone) (C: the meeting) (D: until further notice)."',
                    'options' => ['have decided' => true, 'to postpone' => false, 'the meeting' => false, 'until further notice' => false],
                    'why' => '"Committee" functions as a singular collective noun here, so it requires the singular verb "has decided," not "have decided."',
                ],
                [
                    'q' => 'Choose the best word to complete the sentence: "_____ the economy showed signs of recovery, unemployment rates remained stubbornly high."',
                    'options' => ['Although' => true, 'Because' => false, 'So that' => false, 'In order that' => false],
                    'why' => '"Although" correctly introduces a contrast between economic recovery and persistently high unemployment.',
                ],
                [
                    'q' => 'Which word is closest in meaning to "meticulous" as used in: "The researcher kept meticulous records of every experimental trial"?',
                    'options' => ['extremely careful and precise' => true, 'somewhat careless' => false, 'unusually fast' => false, 'occasionally accurate' => false],
                    'why' => '"Meticulous" means showing great attention to detail and being very careful and precise.',
                ],
                [
                    'q' => 'Which part of this sentence contains an error? "Having finished the experiment, (A) the data (B) was analyzed (C) by the research team."',
                    'options' => ['the data' => true, 'was analyzed' => false, 'by the research team' => false, 'Having finished the experiment' => false],
                    'why' => 'The introductory phrase "Having finished the experiment" logically should modify the person who finished it, but it illogically modifies "the data," creating a dangling modifier.',
                ],
                [
                    'q' => 'Choose the best word: "The hypothesis was widely accepted, _____ later evidence proved it to be false."',
                    'options' => ['yet' => true, 'so' => false, 'for' => false, 'and' => false],
                    'why' => '"Yet" correctly signals the contrast between the hypothesis being accepted and it later being disproven.',
                ],
                [
                    'q' => 'Read the passage and choose the best statement of its main idea: "The theory of plate tectonics, first proposed in the mid-twentieth century, revolutionized geology by explaining phenomena that earlier models could not account for, including the distribution of earthquakes, the formation of mountain ranges, and the gradual separation of continents. Prior to this theory, geologists struggled to explain why similar fossils and rock formations appeared on continents separated by vast oceans. Plate tectonics resolved this puzzle by demonstrating that the Earth\'s crust is divided into moving plates that drift atop the semi-fluid mantle."',
                    'options' => [
                        'Plate tectonics provided a unified explanation for geological phenomena that previous theories could not adequately account for.' => true,
                        'Earthquakes are caused exclusively by the movement of tectonic plates.' => false,
                        'Fossils are the primary evidence used to date rock formations.' => false,
                        'The Earth\'s mantle is composed entirely of solid rock.' => false,
                    ],
                    'why' => 'The passage centers on how plate tectonics resolved geological puzzles that earlier theories could not explain, such as earthquake distribution, mountain formation, and continental separation.',
                ],
                [
                    'q' => '"Plastic pollution has become ubiquitous in marine ecosystems, found from surface waters to the deepest ocean trenches." The word "ubiquitous" most nearly means:',
                    'options' => ['present everywhere' => true, 'rapidly increasing' => false, 'extremely harmful' => false, 'difficult to detect' => false],
                    'why' => '"Ubiquitous" means existing or being found everywhere.',
                ],
                [
                    'q' => 'Which part contains an error? "Of the two proposals, (A) the second one (B) is more preferable (C) because it requires (D) less funding."',
                    'options' => ['is more preferable' => true, 'the second one' => false, 'because it requires' => false, 'less funding' => false],
                    'why' => '"Preferable" already expresses a comparative idea, so it should not be paired with "more"; the correct form is simply "preferable."',
                ],
                [
                    'q' => 'Choose the best completion: "The new policy not only reduced carbon emissions _____ created thousands of jobs in renewable energy."',
                    'options' => ['but also' => true, 'but rather' => false, 'and so' => false, 'or else' => false],
                    'why' => '"Not only... but also" is a correlative conjunction pair used to link two related, positive points.',
                ],
                [
                    'q' => 'Read the passage and choose the best statement of its main idea: "The Industrial Revolution, which began in Britain in the late eighteenth century, transformed societies far beyond the factories where it started. As manufacturing shifted from homes to centralized factories, millions of workers migrated from rural areas to rapidly growing cities in search of employment. This mass migration strained urban infrastructure, leading to overcrowding and poor sanitation, but it also laid the groundwork for modern urban planning and labor reform movements that would emerge in the following century."',
                    'options' => [
                        'The Industrial Revolution caused sweeping social changes, including urbanization, that eventually spurred reforms in city planning and labor conditions.' => true,
                        'The Industrial Revolution began in Britain and quickly spread to rural areas.' => false,
                        'Factories were the only institutions affected by the Industrial Revolution.' => false,
                        'Urban planning existed before the Industrial Revolution and remained unchanged by it.' => false,
                    ],
                    'why' => 'The passage traces how industrialization drove urban migration and strain, which in turn led to later urban planning and labor reforms, making this the central idea.',
                ],
                [
                    'q' => '"Engineers designed the levee system to mitigate the risk of catastrophic flooding." The word "mitigate" most nearly means:',
                    'options' => ['to lessen or reduce' => true, 'to completely eliminate' => false, 'to accurately predict' => false, 'to intentionally cause' => false],
                    'why' => '"Mitigate" means to make something less severe or serious, not to eliminate it entirely.',
                ],
                [
                    'q' => 'Which part contains an error? "Neither (A) the manager (B) nor the employees (C) was aware of (D) the policy change."',
                    'options' => ['was aware of' => true, 'the manager' => false, 'nor the employees' => false, 'the policy change' => false],
                    'why' => 'In "neither...nor" constructions, the verb agrees with the subject closer to it; since "employees" is plural, the verb should be "were," not "was."',
                ],
                [
                    'q' => 'Choose the best word: "Renewable energy sources produce little pollution, _____ fossil fuels release significant greenhouse gases."',
                    'options' => ['whereas' => true, 'unless' => false, 'provided that' => false, 'in case' => false],
                    'why' => '"Whereas" is used to contrast two clauses, here highlighting the difference between renewable energy and fossil fuels.',
                ],
                [
                    'q' => '"The discovery of DNA\'s structure represented a paradigm shift in biological science." The word "paradigm" most nearly means:',
                    'options' => ['a typical pattern or model of thinking' => true, 'a laboratory experiment' => false, 'a scientific instrument' => false, 'a temporary hypothesis' => false],
                    'why' => '"Paradigm" refers to a typical example, pattern, or model, especially one that underlies an entire field of thought.',
                ],
                [
                    'q' => 'Read the passage and choose the best statement of its main idea: "Coral reefs, often called the rainforests of the sea, are experiencing unprecedented stress due to rising ocean temperatures. When water temperatures increase even slightly above normal, corals expel the symbiotic algae living in their tissues, causing them to turn white in a process known as bleaching. While bleached corals are not immediately dead, they are significantly weakened and more susceptible to disease and starvation if the stressful conditions persist. Scientists warn that without significant reductions in global carbon emissions, many of the world\'s reef systems could face irreversible decline within decades."',
                    'options' => [
                        'Rising ocean temperatures are weakening coral reefs through bleaching, and without emission reductions, reefs may decline irreversibly.' => true,
                        'Coral bleaching immediately kills all affected coral reefs.' => false,
                        'Symbiotic algae are harmful parasites that damage coral tissue.' => false,
                        'Coral reefs are unaffected by changes in ocean temperature.' => false,
                    ],
                    'why' => 'The passage explains how rising temperatures cause bleaching that weakens reefs, and warns of irreversible decline without emissions reductions, which together form the main idea.',
                ],
                [
                    'q' => 'Which part contains an error? "The professor asked (A) the students (B) to submit their essays (C) to himself (D) before Friday."',
                    'options' => ['to himself' => true, 'the students' => false, 'to submit their essays' => false, 'before Friday' => false],
                    'why' => 'The reflexive pronoun "himself" cannot be used here because the subject of the clause is "the students," not "the professor"; the correct form is the object pronoun "him."',
                ],
            ]);

            $this->create('ielts', 'Kuis Format IELTS', 'hard', 'Upper-Intermediate', [
                ['q' => 'In IELTS Writing Task 1, what should you avoid?', 'options' => ['Personal opinions' => true, 'Describing data' => false, 'Trend words' => false, 'An overview sentence' => false], 'why' => 'Task 1 requires an objective, factual description — not personal opinions.'],
                ['q' => 'Which phrase describes a stable trend in a graph?', 'options' => ['remained stable at...' => true, 'increased sharply' => false, 'in my opinion' => false, 'I believe that' => false], 'why' => '"Remained stable at" describes a flat, unchanging trend.'],
                ['q' => 'In the reading passage about coffee, which country is the largest producer?', 'options' => ['Brazil' => true, 'Vietnam' => false, 'Colombia' => false, 'Ethiopia' => false], 'why' => 'The passage states Brazil is the largest coffee producer today.'],
                ['q' => "What is a good response to 'Can you tell me about your hometown?' in Speaking Part 1?", 'options' => ['A short, natural descriptive answer' => true, 'A one-word answer' => false, 'A memorized script' => false, 'Silence' => false], 'why' => 'Part 1 rewards natural, conversational answers over memorized responses.'],
                [
                    'q' => 'What is the minimum recommended word count for IELTS Writing Task 1?',
                    'options' => ['150 words' => true, '250 words' => false, '100 words' => false, '300 words' => false],
                    'why' => 'IELTS Writing Task 1 requires a minimum of 150 words, while Task 2 requires at least 250 words.',
                ],
                [
                    'q' => 'In IELTS Writing Task 1, which of the following should generally be avoided?',
                    'options' => [
                        'Describing every single data point in exhaustive detail' => true,
                        'Identifying overall trends' => false,
                        'Grouping similar data together' => false,
                        'Using a variety of comparative language' => false,
                    ],
                    'why' => 'Candidates should summarize key trends and select the most relevant data rather than describing every individual figure, which wastes time and reduces clarity.',
                ],
                [
                    'q' => 'Which phrase best describes a sudden, significant increase in a graph?',
                    'options' => ['a sharp rise' => true, 'a gradual decline' => false, 'a slight dip' => false, 'a modest fluctuation' => false],
                    'why' => '"A sharp rise" describes a quick and significant upward movement in data.',
                ],
                [
                    'q' => 'In describing a line graph, what does it mean if a value "plateaued" after a period of growth?',
                    'options' => [
                        'it leveled off and stopped changing significantly' => true,
                        'it dropped sharply to zero' => false,
                        'it increased at an accelerating rate' => false,
                        'it fluctuated unpredictably' => false,
                    ],
                    'why' => '"Plateaued" means the value reached a stable level after rising and stopped increasing or decreasing significantly.',
                ],
                [
                    'q' => 'Read the passage: "The modern bicycle evolved gradually over more than a century of experimentation. Early versions, such as the wooden \'draisine\' of the 1810s, had no pedals and were propelled by the rider pushing their feet against the ground. It was not until the 1860s that pedals were added directly to the front wheel, creating the design known as the velocipede. Later innovations, including chain-driven rear wheels and pneumatic tires, made cycling safer, more comfortable, and accessible to a much wider segment of the population by the end of the nineteenth century." According to the passage, what was true of the earliest bicycles?',
                    'options' => [
                        'They lacked pedals and were moved by the rider\'s feet pushing against the ground.' => true,
                        'They already had chain-driven rear wheels.' => false,
                        'They were equipped with pneumatic tires from the start.' => false,
                        'They were propelled using pedals attached to the front wheel.' => false,
                    ],
                    'why' => 'The passage states that early versions such as the draisine had no pedals and were propelled by the rider pushing their feet against the ground.',
                ],
                [
                    'q' => 'Based on the same passage about the bicycle\'s history, what is its main idea?',
                    'options' => [
                        'The bicycle developed through a series of gradual innovations that made it progressively safer and more widely used.' => true,
                        'The bicycle was invented in a single breakthrough in the 1860s.' => false,
                        'Pneumatic tires were the first major feature added to bicycles.' => false,
                        'Bicycles remained largely unchanged throughout the nineteenth century.' => false,
                    ],
                    'why' => 'The passage traces multiple stages of development, from the pedal-less draisine to the velocipede to chain drives and pneumatic tires, showing gradual progress toward a safer, more accessible bicycle.',
                ],
                [
                    'q' => 'In IELTS Speaking Part 2, how much time are candidates typically given to prepare notes before speaking?',
                    'options' => ['1 minute' => true, '5 minutes' => false, '30 seconds' => false, 'No preparation time is given' => false],
                    'why' => 'Candidates are given 1 minute to prepare notes on the cue card topic before speaking for up to 2 minutes.',
                ],
                [
                    'q' => 'What are candidates typically given to help them prepare during IELTS Speaking Part 2?',
                    'options' => [
                        'a cue card with a topic and bullet points to cover' => true,
                        'a written essay to read aloud' => false,
                        'a list of vocabulary words to memorize' => false,
                        'a recording of a native speaker to imitate' => false,
                    ],
                    'why' => 'In Part 2, candidates receive a cue card outlining a topic and several bullet points they should address in their talk.',
                ],
                [
                    'q' => 'Compared to Part 1, how should candidates typically respond in IELTS Speaking Part 3?',
                    'options' => [
                        'with more extended, analytical answers that explore abstract ideas' => true,
                        'with very short one-word answers' => false,
                        'by reading directly from prepared notes' => false,
                        'by avoiding giving any personal opinions' => false,
                    ],
                    'why' => 'Part 3 involves a discussion of more abstract, general issues related to the Part 2 topic, so candidates are expected to give longer, more developed responses with reasoning and examples.',
                ],
                [
                    'q' => 'Which of the following is a common question type in the IELTS Listening test?',
                    'options' => ['form completion' => true, 'essay writing' => false, 'sentence transformation' => false, 'oral presentation' => false],
                    'why' => 'IELTS Listening includes question types such as form completion, multiple choice, matching, and labeling diagrams; essay writing and oral presentation are not part of Listening.',
                ],
                [
                    'q' => 'In the IELTS Listening test, how many times is each recording played?',
                    'options' => ['once' => true, 'twice' => false, 'three times' => false, 'as many times as needed' => false],
                    'why' => 'Unlike some other listening assessments, IELTS Listening recordings are played only once, requiring careful, sustained attention.',
                ],
                [
                    'q' => 'In the Academic IELTS Writing Task 1, what are candidates typically asked to do?',
                    'options' => [
                        'describe or summarize visual information such as a graph, chart, or diagram' => true,
                        'write a formal or informal letter' => false,
                        'write a persuasive essay arguing a position' => false,
                        'summarize a spoken lecture' => false,
                    ],
                    'why' => 'Academic Writing Task 1 requires candidates to describe, summarize, or explain visual data such as graphs, tables, charts, or diagrams.',
                ],
                [
                    'q' => 'In the General Training IELTS Writing Task 1, candidates are typically asked to:',
                    'options' => [
                        'write a letter responding to a given situation' => true,
                        'describe a bar chart showing statistical data' => false,
                        'summarize a diagram of a process' => false,
                        'compare two line graphs' => false,
                    ],
                    'why' => 'In General Training Writing Task 1, candidates write a letter (formal, semi-formal, or informal) responding to a described situation, rather than describing visual data.',
                ],
                [
                    'q' => 'Which word best describes data that repeatedly rose and fell without a clear consistent pattern?',
                    'options' => ['fluctuated' => true, 'stabilized' => false, 'plummeted' => false, 'peaked' => false],
                    'why' => '"Fluctuated" describes irregular ups and downs in data over time, without a steady trend.',
                ],
                [
                    'q' => 'Which reading strategy is most useful for quickly locating a specific date or name in an IELTS Reading passage?',
                    'options' => [
                        'scanning' => true,
                        'skimming' => false,
                        'intensive re-reading of every word' => false,
                        'translating the passage into your native language' => false,
                    ],
                    'why' => 'Scanning involves quickly searching a text for specific details, such as names, dates, or numbers, without reading every word.',
                ],
                [
                    'q' => 'Which reading strategy involves quickly reading a passage to understand its general topic and overall message?',
                    'options' => ['skimming' => true, 'scanning' => false, 'transcribing' => false, 'proofreading' => false],
                    'why' => 'Skimming means reading quickly to grasp the general idea or gist of a passage rather than every detail.',
                ],
            ]);

            $this->create(
                null,
                'Tes Komprehensif Bahasa Inggris (Beginner–Advanced)',
                'hard',
                null,
                [
                    [
                        'q' => 'Choose the correct word: "I ___ a student."',
                        'options' => ['am' => true, 'is' => false, 'are' => false, 'be' => false],
                        'why' => 'Untuk subjek "I" dalam kalimat "to be" bentuk present, kata yang benar adalah "am".',
                    ],
                    [
                        'q' => 'Choose the correct word: "She ___ a doctor."',
                        'options' => ['is' => true, 'am' => false, 'are' => false, 'be' => false],
                        'why' => 'Untuk subjek tunggal orang ketiga (she/he/it), kata "to be" yang benar adalah "is".',
                    ],
                    [
                        'q' => 'Choose the correct word: "They ___ happy."',
                        'options' => ['are' => true, 'is' => false, 'am' => false, 'be' => false],
                        'why' => 'Untuk subjek jamak "they", kata "to be" yang benar adalah "are".',
                    ],
                    [
                        'q' => 'Complete the sentence: "He ___ to school every day."',
                        'options' => ['goes' => true, 'go' => false, 'going' => false, 'gone' => false],
                        'why' => 'Untuk subjek orang ketiga tunggal (he) dalam simple present, kata kerja mendapat akhiran -es/-s, sehingga "goes" benar.',
                    ],
                    [
                        'q' => 'Complete the sentence: "I ___ coffee every morning."',
                        'options' => ['drink' => true, 'drinks' => false, 'drank' => false, 'drinking' => false],
                        'why' => 'Untuk subjek "I" dalam simple present, kata kerja tidak mendapat tambahan -s, sehingga "drink" benar.',
                    ],
                    [
                        'q' => 'Choose the correct article: "I saw ___ elephant at the zoo."',
                        'options' => ['an' => true, 'a' => false, 'the' => false, 'no article needed' => false],
                        'why' => '"Elephant" diawali huruf vokal (e), sehingga artikel yang tepat adalah "an".',
                    ],
                    [
                        'q' => 'Choose the correct article: "She has ___ cat."',
                        'options' => ['a' => true, 'an' => false, 'the' => false, 'no article needed' => false],
                        'why' => '"Cat" diawali huruf konsonan (c), sehingga artikel yang tepat adalah "a".',
                    ],
                    [
                        'q' => 'What is the plural form of "child"?',
                        'options' => ['children' => true, 'childs' => false, 'childes' => false, 'child' => false],
                        'why' => '"Child" memiliki bentuk jamak tidak beraturan, yaitu "children".',
                    ],
                    [
                        'q' => 'What is the plural form of "book"?',
                        'options' => ['books' => true, 'bookes' => false, 'bookies' => false, 'book' => false],
                        'why' => 'Kata benda beraturan seperti "book" dibuat jamak dengan menambahkan akhiran "-s", menjadi "books".',
                    ],
                    [
                        'q' => 'Choose the correct pronoun: "___ is my brother. He is ten years old."',
                        'options' => ['This' => true, 'These' => false, 'They' => false, 'Those' => false],
                        'why' => '"This" digunakan untuk menunjuk pada satu orang/benda tunggal yang dekat, sesuai dengan "brother" yang tunggal.',
                    ],
                    [
                        'q' => 'Choose the correct preposition: "The book is ___ the table."',
                        'options' => ['on' => true, 'in' => false, 'at' => false, 'under' => false],
                        'why' => '"On" digunakan untuk menyatakan posisi di atas permukaan suatu benda, seperti meja.',
                    ],
                    [
                        'q' => 'Choose the correct preposition: "We live ___ Jakarta."',
                        'options' => ['in' => true, 'on' => false, 'at' => false, 'to' => false],
                        'why' => '"In" digunakan untuk menyatakan lokasi di dalam kota atau negara, seperti "Jakarta".',
                    ],
                    [
                        'q' => 'What do you say when you meet someone in the morning?',
                        'options' => ['Good morning' => true, 'Good night' => false, 'Good bye' => false, 'Good evening' => false],
                        'why' => '"Good morning" adalah ucapan salam yang digunakan saat bertemu seseorang di pagi hari.',
                    ],
                    [
                        'q' => 'What is the English word for "ibu" (mother\'s sister)?',
                        'options' => ['aunt' => true, 'uncle' => false, 'niece' => false, 'cousin' => false],
                        'why' => '"Aunt" adalah kata dalam bahasa Inggris untuk saudara perempuan dari ayah atau ibu (bibi/tante).',
                    ],
                    [
                        'q' => 'How do you write the number "7" in English?',
                        'options' => ['seven' => true, 'six' => false, 'eight' => false, 'nine' => false],
                        'why' => 'Angka 7 dalam bahasa Inggris ditulis "seven".',
                    ],
                    [
                        'q' => 'What color do you get when you mix blue and yellow?',
                        'options' => ['green' => true, 'purple' => false, 'orange' => false, 'brown' => false],
                        'why' => 'Mencampur warna biru (blue) dan kuning (yellow) menghasilkan warna hijau (green).',
                    ],
                    [
                        'q' => 'Which day comes after "Monday"?',
                        'options' => ['Tuesday' => true, 'Sunday' => false, 'Wednesday' => false, 'Friday' => false],
                        'why' => 'Dalam urutan hari, "Tuesday" (Selasa) datang tepat setelah "Monday" (Senin).',
                    ],
                    [
                        'q' => 'What do you use to write on paper?',
                        'options' => ['a pen' => true, 'a spoon' => false, 'a chair' => false, 'a plate' => false],
                        'why' => '"A pen" (pulpen) adalah alat yang digunakan untuk menulis di atas kertas.',
                    ],
                    [
                        'q' => 'Read the passage: "My name is Tom. I am ten years old. I have a small dog named Max." How old is Tom?',
                        'options' => ['Ten years old' => true, 'Eight years old' => false, 'Twelve years old' => false, 'Five years old' => false],
                        'why' => 'Pada teks disebutkan "I am ten years old", yang berarti Tom berusia sepuluh tahun.',
                    ],
                    [
                        'q' => 'Read the passage: "Lisa has two brothers. She does not have a sister. Her brothers are named Jack and Ben." How many sisters does Lisa have?',
                        'options' => ['None' => true, 'One' => false, 'Two' => false, 'Three' => false],
                        'why' => 'Pada teks disebutkan "She does not have a sister", yang berarti Lisa tidak memiliki saudara perempuan sama sekali.',
                    ],
                    [
                        'q' => 'She ___ to the market yesterday morning.',
                        'options' => ['went' => true, 'go' => false, 'goes' => false, 'going' => false],
                        'why' => '"Yesterday morning" menunjukkan waktu lampau, sehingga digunakan simple past tense "went" (bentuk kedua dari "go").',
                    ],
                    [
                        'q' => 'Look! The children ___ football in the park right now.',
                        'options' => ['are playing' => true, 'play' => false, 'played' => false, 'plays' => false],
                        'why' => 'Kata "right now" menunjukkan kejadian sedang berlangsung, sehingga menggunakan present continuous "are playing".',
                    ],
                    [
                        'q' => 'An elephant is much ___ than a mouse.',
                        'options' => ['bigger' => true, 'more big' => false, 'biggest' => false, 'big' => false],
                        'why' => 'Untuk membandingkan dua benda digunakan bentuk komparatif adjective pendek dengan akhiran "-er", yaitu "bigger".',
                    ],
                    [
                        'q' => 'This is ___ mountain in the whole country.',
                        'options' => ['the highest' => true, 'higher' => false, 'the higher' => false, 'high' => false],
                        'why' => 'Karena membandingkan satu hal dengan semua yang lain dalam satu kelompok ("in the whole country"), digunakan bentuk superlatif "the highest".',
                    ],
                    [
                        'q' => 'How ___ water do you drink every day?',
                        'options' => ['much' => true, 'many' => false, 'a' => false, 'some' => false],
                        'why' => '"Water" adalah uncountable noun, sehingga menggunakan "much" untuk menanyakan jumlah, bukan "many" yang digunakan untuk countable noun.',
                    ],
                    [
                        'q' => 'There are only a few ___ left in the basket.',
                        'options' => ['apples' => true, 'apple' => false, 'rice' => false, 'bread' => false],
                        'why' => '"A few" digunakan sebelum countable noun bentuk jamak, sehingga jawaban yang tepat adalah "apples".',
                    ],
                    [
                        'q' => 'I ___ swim very well when I was ten years old.',
                        'options' => ['could' => true, 'can' => false, 'may' => false, 'must' => false],
                        'why' => '"Could" adalah bentuk lampau dari "can" yang digunakan untuk menyatakan kemampuan di masa lalu.',
                    ],
                    [
                        'q' => 'My brother ___ speak three languages fluently.',
                        'options' => ['can' => true, 'could' => false, 'must' => false, 'should' => false],
                        'why' => '"Can" digunakan untuk menyatakan kemampuan seseorang saat ini, yaitu kemampuan berbicara tiga bahasa.',
                    ],
                    [
                        'q' => 'Which sentence uses the frequency adverb in the correct position?',
                        'options' => [
                            'She always eats breakfast before school.' => true,
                            'She eats always breakfast before school.' => false,
                            'Always she eats breakfast before school.' => false,
                            'She eats breakfast always before school.' => false,
                        ],
                        'why' => 'Adverb frekuensi seperti "always" biasanya diletakkan sebelum kata kerja utama (bukan kata kerja "to be"), yaitu setelah subjek dan sebelum verb.',
                    ],
                    [
                        'q' => 'The meeting will start ___ 9 o\'clock ___ Monday morning.',
                        'options' => ['at / on' => true, 'on / at' => false, 'in / at' => false, 'at / in' => false],
                        'why' => 'Preposisi "at" digunakan untuk jam (9 o\'clock) dan "on" digunakan untuk hari tertentu (Monday morning).',
                    ],
                    [
                        'q' => 'The cat is sleeping ___ the sofa ___ the living room.',
                        'options' => ['on / in' => true, 'in / on' => false, 'at / on' => false, 'on / at' => false],
                        'why' => '"On" digunakan untuk posisi di atas permukaan (sofa), sedangkan "in" digunakan untuk ruang tertutup seperti ruangan.',
                    ],
                    [
                        'q' => 'Which sentence is a correct imperative (command)?',
                        'options' => [
                            'Close the door, please.' => true,
                            'You closing the door, please.' => false,
                            'Closes the door, please.' => false,
                            'Closed the door, please.' => false,
                        ],
                        'why' => 'Kalimat imperatif (perintah) menggunakan bentuk dasar kata kerja (base form) tanpa subjek, seperti "Close the door".',
                    ],
                    [
                        'q' => 'I wanted to go to the beach, ___ it started raining heavily.',
                        'options' => ['but' => true, 'and' => false, 'so' => false, 'because' => false],
                        'why' => '"But" digunakan untuk menghubungkan dua ide yang berlawanan atau kontras, yaitu keinginan pergi ke pantai dan hujan deras.',
                    ],
                    [
                        'q' => 'He studied very hard, ___ he passed the exam.',
                        'options' => ['so' => true, 'but' => false, 'or' => false, 'although' => false],
                        'why' => '"So" digunakan untuk menunjukkan hasil atau akibat, yaitu belajar keras menyebabkan lulus ujian.',
                    ],
                    [
                        'q' => 'Tom did not ___ his homework last night, so his teacher was angry.',
                        'options' => ['finish' => true, 'finished' => false, 'finishes' => false, 'finishing' => false],
                        'why' => 'Setelah auxiliary verb "did not" dalam simple past negative, kata kerja utama harus dalam bentuk dasar (base form), yaitu "finish".',
                    ],
                    [
                        'q' => '___ your parents watching TV at the moment?',
                        'options' => ['Are' => true, 'Do' => false, 'Is' => false, 'Did' => false],
                        'why' => 'Karena subjeknya jamak ("your parents") dan kalimat merupakan pertanyaan present continuous, digunakan "Are" di awal kalimat.',
                    ],
                    [
                        'q' => 'This bag is expensive, but that one is even ___.',
                        'options' => ['more expensive' => true, 'expensiver' => false, 'most expensive' => false, 'expensive' => false],
                        'why' => 'Adjective panjang seperti "expensive" menggunakan "more" untuk membentuk komparatif, yaitu "more expensive".',
                    ],
                    [
                        'q' => 'My exam score was bad, but Rina\'s score was even worse. Her score was the ___ in the whole class.',
                        'options' => ['worst' => true, 'worse' => false, 'more bad' => false, 'baddest' => false],
                        'why' => '"Bad" memiliki bentuk superlatif tidak beraturan, yaitu "worst" (bad - worse - worst).',
                    ],
                    [
                        'q' => 'Read the passage: "Maria wakes up at six every morning. She always drinks a glass of milk and eats bread for breakfast. After that, she walks to school with her younger brother. School starts at seven thirty." What does Maria drink every morning?',
                        'options' => ['A glass of milk' => true, 'A cup of coffee' => false, 'A glass of juice' => false, 'Tea with sugar' => false],
                        'why' => 'Berdasarkan teks, dinyatakan secara jelas bahwa Maria "always drinks a glass of milk" setiap pagi.',
                    ],
                    [
                        'q' => 'Read the passage: "Last weekend, David and his friends went camping near a lake. They set up their tent, cooked dinner over a fire, and told funny stories all night. The next morning, they were tired but very happy." How did David and his friends feel the next morning?',
                        'options' => ['Tired but happy' => true, 'Angry and bored' => false, 'Sick and sad' => false, 'Excited to go home' => false],
                        'why' => 'Teks menyebutkan secara eksplisit bahwa keesokan paginya mereka "were tired but very happy".',
                    ],
                    [
                        'q' => 'Complete the sentence: "___ you ever eaten sushi?"',
                        'options' => ['Have' => true, 'Did' => false, 'Do' => false, 'Are' => false],
                        'why' => '"Have you ever...?" adalah bentuk present perfect yang digunakan untuk menanyakan pengalaman seseorang.',
                    ],
                    [
                        'q' => 'Complete the sentence: "She ___ just finished her homework."',
                        'options' => ['has' => true, 'have' => false, 'did' => false, 'is' => false],
                        'why' => 'Subjek tunggal "she" dengan kata "just" dan past participle menggunakan present perfect: has + past participle.',
                    ],
                    [
                        'q' => 'Complete the sentence: "While I ___ dinner, the phone rang."',
                        'options' => ['was cooking' => true, 'cooked' => false, 'cook' => false, 'have cooked' => false],
                        'why' => 'Aksi yang sedang berlangsung (was cooking) diinterupsi oleh aksi singkat (rang) menggunakan past continuous.',
                    ],
                    [
                        'q' => 'Choose the correct question: "___ you ___ when I called you last night?"',
                        'options' => ['Were / doing' => true, 'Did / do' => false, 'Are / doing' => false, 'Have / done' => false],
                        'why' => 'Menanyakan aksi yang sedang berlangsung pada waktu tertentu di masa lalu menggunakan past continuous: were + verb-ing.',
                    ],
                    [
                        'q' => 'Complete the sentence: "I think the economy ___ improve next year."',
                        'options' => ['will' => true, 'would' => false, 'was' => false, 'has' => false],
                        'why' => 'Prediksi tanpa bukti kuat, biasanya setelah "I think", menggunakan "will".',
                    ],
                    [
                        'q' => 'Complete the sentence: "We have already booked the tickets. We ___ to Bali next week."',
                        'options' => ['are going' => true, 'will go' => false, 'go' => false, 'went' => false],
                        'why' => 'Rencana yang sudah diatur atau diputuskan sebelumnya menggunakan "going to".',
                    ],
                    [
                        'q' => 'Complete the sentence: "If it rains tomorrow, we ___ the picnic."',
                        'options' => ['will cancel' => true, 'cancel' => false, 'canceled' => false, 'would cancel' => false],
                        'why' => 'First conditional: If + present simple, ... will + verb dasar, digunakan untuk kemungkinan nyata di masa depan.',
                    ],
                    [
                        'q' => 'Choose the correct option: "___ you hurry, you will miss the bus."',
                        'options' => ['Unless' => true, 'If' => false, 'Because' => false, 'Although' => false],
                        'why' => '"Unless" berarti "if not" (kecuali/jika tidak), cocok dengan konteks kalimat ini.',
                    ],
                    [
                        'q' => 'Complete the sentence: "You look tired. You ___ get some rest."',
                        'options' => ['should' => true, 'must' => false, 'can' => false, 'will' => false],
                        'why' => '"Should" digunakan untuk memberi saran, bukan keharusan mutlak seperti "must".',
                    ],
                    [
                        'q' => 'Complete the sentence: "All passengers ___ wear seatbelts during the flight. It\'s the law."',
                        'options' => ['must' => true, 'should' => false, 'can' => false, 'might' => false],
                        'why' => '"Must" digunakan untuk kewajiban atau aturan yang kuat/mengikat.',
                    ],
                    [
                        'q' => 'Complete the sentence: "Tomorrow is a holiday, so we ___ go to work."',
                        'options' => ["don't have to" => true, "mustn't" => false, "shouldn't" => false, "can't" => false],
                        'why' => '"Don\'t have to" berarti tidak ada keharusan/tidak wajib, berbeda dengan "mustn\'t" yang berarti dilarang.',
                    ],
                    [
                        'q' => 'Choose the correct passive form: "English ___ in many countries."',
                        'options' => ['is spoken' => true, 'speaks' => false, 'is speaking' => false, 'spoke' => false],
                        'why' => 'Passive voice present simple dibentuk dengan is/am/are + past participle.',
                    ],
                    [
                        'q' => 'Complete the sentence: "This house ___ in 1985."',
                        'options' => ['was built' => true, 'built' => false, 'is built' => false, 'has build' => false],
                        'why' => 'Passive voice past simple dibentuk dengan was/were + past participle.',
                    ],
                    [
                        'q' => 'Choose the correct relative pronoun: "The woman ___ is standing over there is my teacher."',
                        'options' => ['who' => true, 'which' => false, 'whose' => false, 'when' => false],
                        'why' => '"Who" digunakan untuk menerangkan orang sebagai subjek dalam klausa relatif.',
                    ],
                    [
                        'q' => 'Complete the sentence: "That\'s the man ___ car was stolen yesterday."',
                        'options' => ['whose' => true, 'who' => false, 'which' => false, 'that' => false],
                        'why' => '"Whose" digunakan untuk menunjukkan kepemilikan (posesif) dalam klausa relatif.',
                    ],
                    [
                        'q' => 'Complete the sentence: "She enjoys ___ novels in her free time."',
                        'options' => ['reading' => true, 'to read' => false, 'read' => false, 'reads' => false],
                        'why' => 'Kata kerja "enjoy" selalu diikuti oleh gerund (verb-ing), bukan infinitive.',
                    ],
                    [
                        'q' => 'Complete the sentence: "He decided ___ a new car."',
                        'options' => ['to buy' => true, 'buying' => false, 'buy' => false, 'bought' => false],
                        'why' => 'Kata kerja "decide" diikuti oleh to-infinitive (to + verb dasar).',
                    ],
                    [
                        'q' => 'Choose the correct phrasal verb: "Could you turn ___ the volume? I can\'t hear the movie."',
                        'options' => ['up' => true, 'off' => false, 'out' => false, 'over' => false],
                        'why' => '"Turn up" berarti memperbesar volume atau suara.',
                    ],
                    [
                        'q' => 'Read the passage: "Maria works as a nurse at a busy hospital in Jakarta. She usually starts her shift at 7 a.m. and finishes at 3 p.m. Last month, she was offered a promotion to head nurse, but she has not decided yet whether to accept it. If she takes the new position, she will have more responsibility and a higher salary, but she will also work longer hours. Maria loves spending time with her patients, and she is worried that a management job will keep her away from direct patient care." What is Maria\'s current job?',
                        'options' => ['A nurse' => true, 'A head nurse' => false, 'A doctor' => false, 'A hospital manager' => false],
                        'why' => 'Teks menyatakan "Maria works as a nurse" - dia saat ini berprofesi sebagai perawat, belum menerima promosi menjadi head nurse.',
                    ],
                    [
                        'q' => 'Read the passage: "Maria works as a nurse at a busy hospital in Jakarta. She usually starts her shift at 7 a.m. and finishes at 3 p.m. Last month, she was offered a promotion to head nurse, but she has not decided yet whether to accept it. If she takes the new position, she will have more responsibility and a higher salary, but she will also work longer hours. Maria loves spending time with her patients, and she is worried that a management job will keep her away from direct patient care." According to the passage, what will happen if Maria accepts the promotion?',
                        'options' => ['She will have more responsibility, a higher salary, and longer hours' => true, 'She will work fewer hours' => false, 'She will leave the hospital' => false, 'She will get a lower salary' => false],
                        'why' => 'Teks menyatakan bahwa jika dia menerima posisi baru, dia akan memiliki lebih banyak tanggung jawab dan gaji lebih tinggi, tetapi juga bekerja lebih lama.',
                    ],
                    [
                        'q' => 'By the time the plane took off, she ___ her boarding pass twice.',
                        'options' => ['had checked' => true, 'checked' => false, 'has checked' => false, 'was checking' => false],
                        'why' => 'Karena ada dua kejadian di masa lalu, kejadian yang terjadi lebih dulu (memeriksa boarding pass) menggunakan past perfect "had checked", sedangkan "took off" (past simple) menjadi titik acuan waktu yang lebih belakangan.',
                    ],
                    [
                        'q' => "I didn't recognize the city because I ___ there before.",
                        'options' => ['had never been' => true, 'have never been' => false, 'never went' => false, 'was never being' => false],
                        'why' => 'Past perfect "had never been" menunjukkan aksi yang belum pernah terjadi sebelum peristiwa lampau lain (tidak mengenali kota tersebut).',
                    ],
                    [
                        'q' => 'If I ___ more free time, I would travel around the world.',
                        'options' => ['had' => true, 'have' => false, 'will have' => false, 'would have' => false],
                        'why' => 'Second conditional menggunakan past simple ("had") pada if-clause untuk membicarakan situasi hipotetis atau tidak nyata di masa sekarang.',
                    ],
                    [
                        'q' => 'She would accept the job offer if the salary ___ higher.',
                        'options' => ['were' => true, 'is' => false, 'will be' => false, 'has been' => false],
                        'why' => 'Dalam second conditional, bentuk subjunctive "were" digunakan untuk semua subjek (termasuk "the salary") pada kondisi tidak nyata di masa sekarang.',
                    ],
                    [
                        'q' => 'The new bridge ___ by the end of next year.',
                        'options' => ['will have been completed' => true, 'will complete' => false, 'will be completing' => false, 'has completed' => false],
                        'why' => 'Future perfect passive digunakan untuk aksi yang akan selesai sebelum suatu titik waktu di masa depan, dan subjek "the bridge" menerima aksi tersebut sehingga bentuknya pasif.',
                    ],
                    [
                        'q' => 'The report ___ carefully before it was submitted to the manager.',
                        'options' => ['had been checked' => true, 'checked' => false, 'was checking' => false, 'has been checked' => false],
                        'why' => 'Past perfect passive ("had been checked") menunjukkan aksi memeriksa terjadi sebelum aksi lampau lain (submitted), dan subjek "the report" menerima aksi tersebut.',
                    ],
                    [
                        'q' => 'She said, "I am going to the market tomorrow." She said that she ___ to the market the next day.',
                        'options' => ['was going' => true, 'is going' => false, 'went' => false, 'has gone' => false],
                        'why' => 'Dalam reported speech, present continuous berubah menjadi past continuous, dan kata keterangan waktu "tomorrow" berubah menjadi "the next day".',
                    ],
                    [
                        'q' => '"I have finished my homework," he said. He said that he ___ his homework.',
                        'options' => ['had finished' => true, 'has finished' => false, 'finished' => false, 'was finishing' => false],
                        'why' => 'Present perfect dalam kalimat langsung berubah menjadi past perfect ("had finished") ketika diubah menjadi reported speech.',
                    ],
                    [
                        'q' => '"Can you help me carry these boxes?" she asked. She asked if I ___ help her carry the boxes.',
                        'options' => ['could' => true, 'can' => false, 'would' => false, 'should' => false],
                        'why' => 'Modal "can" dalam kalimat langsung berubah menjadi "could" dalam reported speech mengikuti aturan backshift tense.',
                    ],
                    [
                        'q' => "The lights are off and the car isn't in the driveway. They ___ out.",
                        'options' => ['must be' => true, 'must have' => false, 'can be' => false, 'should be' => false],
                        'why' => '"Must be" digunakan untuk deduksi logis yang kuat tentang situasi saat ini berdasarkan bukti yang terlihat.',
                    ],
                    [
                        'q' => "He didn't answer the phone when I called last night. He ___ asleep.",
                        'options' => ['might have been' => true, 'must be' => false, 'should have been' => false, 'can be' => false],
                        'why' => '"Might have been" menunjukkan deduksi atau kemungkinan tentang situasi di masa lalu (kemungkinan dia sedang tidur saat ditelepon).',
                    ],
                    [
                        'q' => 'My brother, ___ lives in Canada, is visiting us next week.',
                        'options' => ['who' => true, 'that' => false, 'which' => false, 'whom' => false],
                        'why' => 'Dalam non-defining relative clause (diapit koma), kata "that" tidak boleh digunakan; "who" digunakan untuk merujuk orang sebagai subjek klausa.',
                    ],
                    [
                        'q' => 'The museum, ___ was built in 1890, attracts thousands of visitors every year.',
                        'options' => ['which' => true, 'that' => false, 'who' => false, 'whose' => false],
                        'why' => '"Which" digunakan dalam non-defining relative clause untuk merujuk benda atau tempat; "that" tidak dapat digunakan setelah tanda koma.',
                    ],
                    [
                        'q' => 'When I was a child, I ___ live in a small fishing village, but we moved to the city when I was ten.',
                        'options' => ['used to' => true, 'would' => false, 'was used to' => false, 'get used to' => false],
                        'why' => '"Used to" digunakan untuk menggambarkan keadaan yang berlangsung lama di masa lalu (tinggal di sebuah desa), sedangkan "would" hanya cocok untuk aksi atau kebiasaan yang berulang, bukan keadaan.',
                    ],
                    [
                        'q' => 'Every Sunday, my grandmother ___ bake fresh bread for the whole family.',
                        'options' => ['would' => true, 'used to being' => false, 'was used to' => false, 'gets used to' => false],
                        'why' => '"Would" digunakan untuk menceritakan kebiasaan atau aksi yang berulang di masa lalu, seperti membuat roti setiap hari Minggu.',
                    ],
                    [
                        'q' => 'I ___ my hair cut at a new salon yesterday.',
                        'options' => ['had' => true, 'did' => false, 'made' => false, 'let' => false],
                        'why' => 'Bentuk causative "have something done" (had + objek + past participle) digunakan untuk menyatakan bahwa seseorang meminta orang lain melakukan sesuatu untuknya.',
                    ],
                    [
                        'q' => 'She ___ the mechanic repair her car before the long road trip.',
                        'options' => ['had' => true, 'made' => false, 'let' => false, 'got' => false],
                        'why' => 'Bentuk causative aktif "have someone do something" (had + orang + bare infinitive) digunakan untuk mengatur agar orang lain melakukan sesuatu, berbeda dengan "make" (memaksa) atau "let" (mengizinkan).',
                    ],
                    [
                        'q' => 'Read the passage and answer the question.

                "Over the past decade, urban beekeeping has grown significantly in cities around the world. Many residents, who once considered beekeeping an activity reserved for the countryside, have started placing hives on rooftops and in small gardens. Although some city dwellers were initially concerned about safety, most beekeepers report that urban bees are surprisingly gentle, largely because they have easy access to a variety of flowers in parks and gardens. Local governments in several cities have even introduced programs to support these initiatives, offering training and reduced fees for equipment. Nevertheless, experts warn that if the number of hives continues to increase without proper regulation, competition for food sources could threaten wild pollinator populations. For this reason, many beekeeping associations now encourage members to plant pollinator-friendly gardens alongside their hives, rather than simply installing more colonies."

                According to the passage, why do many urban beekeepers believe their bees are gentle?',
                        'options' => [
                            'Because the bees have easy access to a variety of flowers in parks and gardens.' => true,
                            'Because they are a special breed developed specifically for cities.' => false,
                            'Because local governments require beekeepers to use gentle bees.' => false,
                            'Because rooftop hives keep bees isolated from people.' => false,
                        ],
                        'why' => 'Sesuai teks, lebah kota dianggap jinak karena mereka memiliki akses mudah ke berbagai macam bunga di taman dan kebun.',
                    ],
                    [
                        'q' => 'Based on the passage about urban beekeeping, what warning do experts give about its rapid growth?',
                        'options' => [
                            'Too many hives without proper regulation could threaten wild pollinator populations.' => true,
                            'Urban bees will eventually become extinct within a decade.' => false,
                            'City governments plan to ban beekeeping entirely in the near future.' => false,
                            'Beekeeping equipment will become too expensive for most residents.' => false,
                        ],
                        'why' => 'Teks menyatakan bahwa jika jumlah sarang terus bertambah tanpa regulasi yang tepat, persaingan sumber makanan dapat mengancam populasi penyerbuk liar (wild pollinators).',
                    ],
                    [
                        'q' => 'According to the passage, what do beekeeping associations now encourage their members to do?',
                        'options' => [
                            'Plant pollinator-friendly gardens instead of simply installing more hives.' => true,
                            'Move their hives from cities back to the countryside.' => false,
                            'Reduce the number of bees kept in each individual hive.' => false,
                            'Sell their honey exclusively at local farmers markets.' => false,
                        ],
                        'why' => 'Menurut teks, asosiasi peternak lebah kini menganjurkan anggotanya menanam kebun yang ramah bagi penyerbuk (pollinator-friendly gardens), bukan sekadar menambah jumlah koloni.',
                    ],
                    [
                        'q' => 'If she ___ the warning signs earlier, the project might not have collapsed so spectacularly.',
                        'options' => ['had heeded' => true, 'heeded' => false, 'would heed' => false, 'has heeded' => false],
                        'why' => 'Ini adalah third conditional (pengandaian tipe 3) untuk situasi hipotetis di masa lalu yang sudah tidak bisa diubah: klausa if menggunakan past perfect (had heeded), dan akibatnya menggunakan "might/would have + past participle".',
                    ],
                    [
                        'q' => 'Had the merger ___ before the recession hit, the company would have survived.',
                        'options' => ['been finalised' => true, 'finalised' => false, 'finalise' => false, 'being finalised' => false],
                        'why' => 'Ini adalah inverted third conditional (tanpa "if"): "Had the merger been finalised..." = "If the merger had been finalised...". Karena "merger" tidak bisa "finalise" dirinya sendiri, dibutuhkan bentuk pasif "been finalised".',
                    ],
                    [
                        'q' => "If he hadn't smoked so heavily in his twenties, he ___ in far better health today.",
                        'options' => ['would be' => true, 'would have been' => false, 'will be' => false, 'had been' => false],
                        'why' => 'Ini adalah mixed conditional: kondisi di masa lalu (hadn\'t smoked) menghasilkan akibat di masa sekarang. Kata "today" menandakan hasil sekarang, sehingga digunakan "would be", bukan "would have been" yang merujuk hasil masa lalu.',
                    ],
                    [
                        'q' => "If she weren't so fiercely independent, she ___ for help when the project first ran into trouble.",
                        'options' => ['would have asked' => true, 'would ask' => false, 'will ask' => false, 'had asked' => false],
                        'why' => 'Ini mixed conditional kebalikannya: kondisi tidak nyata di masa sekarang ("weren\'t") menghasilkan akibat hipotetis di masa lalu. Frasa "when the project first ran into trouble" menunjukkan waktu lampau, sehingga akibatnya memakai "would have asked".',
                    ],
                    [
                        'q' => 'The committee insisted that the report ___ before the board convened.',
                        'options' => ['be revised' => true, 'is revised' => false, 'was revised' => false, 'will be revised' => false],
                        'why' => 'Setelah verba yang menyatakan tuntutan/desakan seperti "insist that", digunakan mandative subjunctive: bentuk dasar verba (be revised) tanpa memperhatikan subjek atau waktu kalimat utama.',
                    ],
                    [
                        'q' => '___ I in your position, I would resign immediately rather than face the tribunal.',
                        'options' => ['Were' => true, 'Was' => false, 'Am' => false, 'Be' => false],
                        'why' => 'Ini inversi formal dari subjunctive "If I were in your position...": "Were I in your position..." Bentuk "were" digunakan untuk semua subjek dalam subjunctive formal, bukan "was".',
                    ],
                    [
                        'q' => '___ had the results been published than the methodology was called into question.',
                        'options' => ['No sooner' => true, 'Not until' => false, 'Hardly' => false, 'Scarcely' => false],
                        'why' => '"No sooner...than" adalah pasangan tetap untuk inversi yang menyatakan dua kejadian yang terjadi hampir bersamaan. "Hardly" dan "Scarcely" berpasangan dengan "when", bukan "than", sehingga tidak cocok di sini.',
                    ],
                    [
                        'q' => '___ did the researchers overlook a critical confound, but they also misreported their statistical significance levels.',
                        'options' => ['Not only' => true, 'Not even' => false, 'Not that' => false, 'Never' => false],
                        'why' => '"Not only...but also" adalah struktur inversi baku untuk menekankan dua fakta negatif/mengejutkan sekaligus. Pilihan lain tidak berpasangan secara gramatikal dengan "but also" pada klausa berikutnya.',
                    ],
                    [
                        'q' => '___ any discrepancies arise during the audit, the finance team must notify the board within 24 hours.',
                        'options' => ['Should' => true, 'Would' => false, 'Were' => false, 'Had' => false],
                        'why' => '"Should" di awal kalimat adalah inversi formal dari "If any discrepancies should arise...", digunakan dalam register formal/legal untuk kondisi yang dianggap kurang mungkin terjadi.',
                    ],
                    [
                        'q' => "___ the ambiguity of the contract's wording, not the intentions of either party, that ultimately led to litigation.",
                        'options' => ['It was' => true, 'It is' => false, 'There was' => false, 'This was' => false],
                        'why' => 'Ini adalah it-cleft sentence dengan pola "It was X, not Y, that..." untuk memberi penekanan pada X. Karena akibatnya ("led to litigation") berbentuk lampau, digunakan "It was", bukan "It is".',
                    ],
                    [
                        'q' => '___ the committee failed to anticipate was the sheer scale of public backlash.',
                        'options' => ['What' => true, 'That' => false, 'Which' => false, 'It' => false],
                        'why' => 'Ini adalah what-cleft (pseudo-cleft sentence) dengan pola "What + klausa + be + unsur yang ditekankan", digunakan untuk memberi penekanan pada informasi di akhir kalimat.',
                    ],
                    [
                        'q' => 'The ancient manuscript ___ to have been forged, though no conclusive evidence has emerged.',
                        'options' => ['is believed' => true, 'believes' => false, 'is believing' => false, 'has believed' => false],
                        'why' => 'Ini struktur passive reporting verb: "is believed to have been forged" digunakan untuk melaporkan opini umum secara objektif tanpa menyebut sumbernya, khas register akademik/formal.',
                    ],
                    [
                        'q' => 'By the time inspectors arrived, the evidence ___, making prosecution all but impossible.',
                        'options' => ['had already been destroyed' => true, 'had already destroyed' => false, 'was already destroying' => false, 'already destroyed' => false],
                        'why' => 'Karena "evidence" adalah objek yang dikenai tindakan (bukan pelaku), dibutuhkan bentuk pasif. "By the time inspectors arrived" menandakan peristiwa yang selesai sebelum peristiwa lampau lain, sehingga dipakai past perfect passive: "had already been destroyed".',
                    ],
                    [
                        'q' => 'The data ___ suggest a correlation, although the sample size precludes any definitive conclusion.',
                        'options' => ['would seem to' => true, 'seems to' => false, 'must' => false, 'clearly' => false],
                        'why' => '"Would seem to" adalah bahasa hedging (melunakkan klaim) yang lazim dalam register akademik ketika bukti belum kuat. "Seems to" juga salah secara tata bahasa karena "data" bersifat jamak (harus "seem"), sedangkan "must" dan "clearly" justru terlalu tegas, bertentangan dengan nada hati-hati kalimat.',
                    ],
                    [
                        'q' => 'Which sentence best demonstrates academic nominalization of the idea: "They analysed the data carefully, and this led them to reject the hypothesis."',
                        'options' => [
                            'The careful analysis of the data led to the rejection of the hypothesis.' => true,
                            'They carefully analysed the data and rejected the hypothesis.' => false,
                            'The data was analysed by them carefully, rejecting the hypothesis.' => false,
                            'Analysing carefully, the hypothesis was rejected by the data.' => false,
                        ],
                        'why' => 'Nominalisasi mengubah verba (analysed, reject) menjadi kata benda abstrak (analysis, rejection), gaya khas tulisan akademik yang formal dan padat. Hanya opsi pertama melakukan transformasi ini secara konsisten dan gramatikal.',
                    ],
                    [
                        'q' => 'He believed the theory was flawed, and ___.',
                        'options' => ['so did his colleagues' => true, 'so his colleagues did' => false, 'also his colleagues did' => false, 'neither did his colleagues' => false],
                        'why' => 'Untuk menyatakan kesepakatan positif dengan pernyataan sebelumnya, digunakan pola substitusi "so + auxiliary + subject" (inversi setelah "so"). "Neither" salah karena bermakna negatif, bertentangan dengan konteks.',
                    ],
                    [
                        'q' => "The council hadn't anticipated the backlash, and ___ had the advisory board.",
                        'options' => ['neither' => true, 'either' => false, 'so' => false, 'also' => false],
                        'why' => 'Untuk menyatakan kesepakatan negatif ("sama-sama tidak"), digunakan pola "neither + auxiliary + subject". "Either" hanya dipakai di akhir kalimat negatif tanpa inversi, sedangkan "so" digunakan untuk kesepakatan positif.',
                    ],
                    [
                        'q' => 'Read the passage, then answer: "The Sapir–Whorf hypothesis, first articulated in the early twentieth century, posits that the structure of a language shapes, or at the very least influences, the cognitive processes of its speakers. In its strongest form—linguistic determinism—the hypothesis contends that thought is wholly constrained by linguistic categories, a claim that has been largely discredited by subsequent research. A weaker, more defensible version, often termed linguistic relativity, suggests merely that language exerts a subtle influence on perception and memory rather than dictating the very boundaries of thought. Empirical support for this softer stance has accumulated steadily: studies of speakers whose languages encode spatial relations in absolute terms (north/south) rather than relative ones (left/right) have revealed marked differences in navigational ability. Critics, however, caution against overstating these findings, noting that a correlation between linguistic structure and cognitive tendency does not necessarily establish causation. Were researchers able to isolate language entirely from the cultural practices with which it is inextricably intertwined, the debate might be settled more conclusively. Until such methodological rigor is achieved, the extent to which language moulds thought remains a matter of considerable scholarly contention." According to the passage, what is the key difference between linguistic determinism and linguistic relativity?',
                        'options' => [
                            'Determinism claims language completely controls thought, while relativity claims language merely influences it.' => true,
                            'Determinism has strong empirical support, while relativity has none.' => false,
                            'Determinism concerns spatial language, while relativity concerns temporal language.' => false,
                            'Determinism is a modern theory, while relativity is the original twentieth-century version.' => false,
                        ],
                        'why' => 'Paragraf menyatakan determinism berpendapat pikiran "wholly constrained" oleh kategori bahasa, sedangkan relativity hanya menyatakan bahasa "exerts a subtle influence" — perbedaan tingkat kekuatan klaim, bukan topik atau bukti empirisnya.',
                    ],
                    [
                        'q' => 'Read the passage, then answer: "The Sapir–Whorf hypothesis, first articulated in the early twentieth century, posits that the structure of a language shapes, or at the very least influences, the cognitive processes of its speakers. In its strongest form—linguistic determinism—the hypothesis contends that thought is wholly constrained by linguistic categories, a claim that has been largely discredited by subsequent research. A weaker, more defensible version, often termed linguistic relativity, suggests merely that language exerts a subtle influence on perception and memory rather than dictating the very boundaries of thought. Empirical support for this softer stance has accumulated steadily: studies of speakers whose languages encode spatial relations in absolute terms (north/south) rather than relative ones (left/right) have revealed marked differences in navigational ability. Critics, however, caution against overstating these findings, noting that a correlation between linguistic structure and cognitive tendency does not necessarily establish causation. Were researchers able to isolate language entirely from the cultural practices with which it is inextricably intertwined, the debate might be settled more conclusively. Until such methodological rigor is achieved, the extent to which language moulds thought remains a matter of considerable scholarly contention." Why do critics urge caution regarding the navigational studies mentioned in the passage?',
                        'options' => [
                            'Because a correlation between language and cognition does not prove that language causes the cognitive difference.' => true,
                            'Because the studies were conducted before the twentieth century and are outdated.' => false,
                            'Because the researchers only studied speakers of relative spatial languages.' => false,
                            'Because linguistic determinism has already been proven false by the same data.' => false,
                        ],
                        'why' => 'Kalimat kritik dalam paragraf secara eksplisit menyebut bahwa "a correlation...does not necessarily establish causation" — inti argumennya adalah korelasi bukan berarti sebab-akibat.',
                    ],
                    [
                        'q' => 'Read the passage, then answer: "The Sapir–Whorf hypothesis, first articulated in the early twentieth century, posits that the structure of a language shapes, or at the very least influences, the cognitive processes of its speakers. In its strongest form—linguistic determinism—the hypothesis contends that thought is wholly constrained by linguistic categories, a claim that has been largely discredited by subsequent research. A weaker, more defensible version, often termed linguistic relativity, suggests merely that language exerts a subtle influence on perception and memory rather than dictating the very boundaries of thought. Empirical support for this softer stance has accumulated steadily: studies of speakers whose languages encode spatial relations in absolute terms (north/south) rather than relative ones (left/right) have revealed marked differences in navigational ability. Critics, however, caution against overstating these findings, noting that a correlation between linguistic structure and cognitive tendency does not necessarily establish causation. Were researchers able to isolate language entirely from the cultural practices with which it is inextricably intertwined, the debate might be settled more conclusively. Until such methodological rigor is achieved, the extent to which language moulds thought remains a matter of considerable scholarly contention." What does the inverted structure "Were researchers able to isolate language entirely from the cultural practices..." imply about the current state of the debate?',
                        'options' => [
                            'It expresses a hypothetical, currently unfulfilled condition — such isolation has not yet been achieved, so the debate remains unresolved.' => true,
                            'It states a fact that researchers have already successfully isolated language from culture.' => false,
                            'It predicts that researchers will never attempt such an isolation.' => false,
                            'It confirms that culture has no real connection to language.' => false,
                        ],
                        'why' => '"Were researchers able to..." adalah inversi dari second conditional ("If researchers were able to..."), menyatakan kondisi hipotetis yang belum terwujud saat ini — sejalan dengan kalimat penutup bahwa perdebatan masih "a matter of considerable scholarly contention".',
                    ],
                ],
                category: 'test',
                timeLimitSeconds: 120 * 60,
                description: '100 soal komprehensif dari level Beginner hingga Advanced dalam satu sesi berwaktu — seperti placement test sungguhan.',
            );

            $this->create(
                null,
                'Tes Level Beginner',
                'easy',
                'Beginner',
                [
                    [
                        'q' => 'Choose the correct word: "I ___ a student."',
                        'options' => ['am' => true, 'is' => false, 'are' => false, 'be' => false],
                        'why' => 'Untuk subjek "I" dalam kalimat "to be" bentuk present, kata yang benar adalah "am".',
                    ],
                    [
                        'q' => 'Choose the correct word: "She ___ a doctor."',
                        'options' => ['is' => true, 'am' => false, 'are' => false, 'be' => false],
                        'why' => 'Untuk subjek tunggal orang ketiga (she/he/it), kata "to be" yang benar adalah "is".',
                    ],
                    [
                        'q' => 'Choose the correct word: "They ___ happy."',
                        'options' => ['are' => true, 'is' => false, 'am' => false, 'be' => false],
                        'why' => 'Untuk subjek jamak "they", kata "to be" yang benar adalah "are".',
                    ],
                    [
                        'q' => 'Complete the sentence: "He ___ to school every day."',
                        'options' => ['goes' => true, 'go' => false, 'going' => false, 'gone' => false],
                        'why' => 'Untuk subjek orang ketiga tunggal (he) dalam simple present, kata kerja mendapat akhiran -es/-s, sehingga "goes" benar.',
                    ],
                    [
                        'q' => 'Complete the sentence: "I ___ coffee every morning."',
                        'options' => ['drink' => true, 'drinks' => false, 'drank' => false, 'drinking' => false],
                        'why' => 'Untuk subjek "I" dalam simple present, kata kerja tidak mendapat tambahan -s, sehingga "drink" benar.',
                    ],
                    [
                        'q' => 'Choose the correct article: "I saw ___ elephant at the zoo."',
                        'options' => ['an' => true, 'a' => false, 'the' => false, 'no article needed' => false],
                        'why' => '"Elephant" diawali huruf vokal (e), sehingga artikel yang tepat adalah "an".',
                    ],
                    [
                        'q' => 'Choose the correct article: "She has ___ cat."',
                        'options' => ['a' => true, 'an' => false, 'the' => false, 'no article needed' => false],
                        'why' => '"Cat" diawali huruf konsonan (c), sehingga artikel yang tepat adalah "a".',
                    ],
                    [
                        'q' => 'What is the plural form of "child"?',
                        'options' => ['children' => true, 'childs' => false, 'childes' => false, 'child' => false],
                        'why' => '"Child" memiliki bentuk jamak tidak beraturan, yaitu "children".',
                    ],
                    [
                        'q' => 'What is the plural form of "book"?',
                        'options' => ['books' => true, 'bookes' => false, 'bookies' => false, 'book' => false],
                        'why' => 'Kata benda beraturan seperti "book" dibuat jamak dengan menambahkan akhiran "-s", menjadi "books".',
                    ],
                    [
                        'q' => 'Choose the correct pronoun: "___ is my brother. He is ten years old."',
                        'options' => ['This' => true, 'These' => false, 'They' => false, 'Those' => false],
                        'why' => '"This" digunakan untuk menunjuk pada satu orang/benda tunggal yang dekat, sesuai dengan "brother" yang tunggal.',
                    ],
                    [
                        'q' => 'Choose the correct preposition: "The book is ___ the table."',
                        'options' => ['on' => true, 'in' => false, 'at' => false, 'under' => false],
                        'why' => '"On" digunakan untuk menyatakan posisi di atas permukaan suatu benda, seperti meja.',
                    ],
                    [
                        'q' => 'Choose the correct preposition: "We live ___ Jakarta."',
                        'options' => ['in' => true, 'on' => false, 'at' => false, 'to' => false],
                        'why' => '"In" digunakan untuk menyatakan lokasi di dalam kota atau negara, seperti "Jakarta".',
                    ],
                    [
                        'q' => 'What do you say when you meet someone in the morning?',
                        'options' => ['Good morning' => true, 'Good night' => false, 'Good bye' => false, 'Good evening' => false],
                        'why' => '"Good morning" adalah ucapan salam yang digunakan saat bertemu seseorang di pagi hari.',
                    ],
                    [
                        'q' => 'What is the English word for "ibu" (mother\'s sister)?',
                        'options' => ['aunt' => true, 'uncle' => false, 'niece' => false, 'cousin' => false],
                        'why' => '"Aunt" adalah kata dalam bahasa Inggris untuk saudara perempuan dari ayah atau ibu (bibi/tante).',
                    ],
                    [
                        'q' => 'How do you write the number "7" in English?',
                        'options' => ['seven' => true, 'six' => false, 'eight' => false, 'nine' => false],
                        'why' => 'Angka 7 dalam bahasa Inggris ditulis "seven".',
                    ],
                    [
                        'q' => 'What color do you get when you mix blue and yellow?',
                        'options' => ['green' => true, 'purple' => false, 'orange' => false, 'brown' => false],
                        'why' => 'Mencampur warna biru (blue) dan kuning (yellow) menghasilkan warna hijau (green).',
                    ],
                    [
                        'q' => 'Which day comes after "Monday"?',
                        'options' => ['Tuesday' => true, 'Sunday' => false, 'Wednesday' => false, 'Friday' => false],
                        'why' => 'Dalam urutan hari, "Tuesday" (Selasa) datang tepat setelah "Monday" (Senin).',
                    ],
                    [
                        'q' => 'What do you use to write on paper?',
                        'options' => ['a pen' => true, 'a spoon' => false, 'a chair' => false, 'a plate' => false],
                        'why' => '"A pen" (pulpen) adalah alat yang digunakan untuk menulis di atas kertas.',
                    ],
                    [
                        'q' => 'Read the passage: "My name is Tom. I am ten years old. I have a small dog named Max." How old is Tom?',
                        'options' => ['Ten years old' => true, 'Eight years old' => false, 'Twelve years old' => false, 'Five years old' => false],
                        'why' => 'Pada teks disebutkan "I am ten years old", yang berarti Tom berusia sepuluh tahun.',
                    ],
                    [
                        'q' => 'Read the passage: "Lisa has two brothers. She does not have a sister. Her brothers are named Jack and Ben." How many sisters does Lisa have?',
                        'options' => ['None' => true, 'One' => false, 'Two' => false, 'Three' => false],
                        'why' => 'Pada teks disebutkan "She does not have a sister", yang berarti Lisa tidak memiliki saudara perempuan sama sekali.',
                    ],
                    [
                        'q' => 'Complete the sentence: "We ___ from Indonesia."',
                        'options' => ['are' => true, 'is' => false, 'am' => false, 'be' => false],
                        'why' => 'Subjek "We" (jamak) menggunakan "are" dalam kalimat to be.',
                    ],
                    [
                        'q' => 'Complete the sentence: "It ___ a big house."',
                        'options' => ['is' => true, 'are' => false, 'am' => false, 'be' => false],
                        'why' => 'Subjek "It" (tunggal) menggunakan "is" dalam kalimat to be.',
                    ],
                    [
                        'q' => 'Complete the sentence: "My father ___ in a bank." (work)',
                        'options' => ['works' => true, 'work' => false, 'working' => false, 'worked' => false],
                        'why' => 'Untuk subjek orang ketiga tunggal (he/she/it) dalam simple present, kata kerja ditambah "-s" menjadi "works".',
                    ],
                    [
                        'q' => 'Complete the sentence: "I ___ like coffee."',
                        'options' => ["don't" => true, "doesn't" => false, 'not' => false, 'no' => false],
                        'why' => 'Untuk subjek "I" dalam kalimat negatif simple present, kita menggunakan "don\'t" + kata kerja dasar.',
                    ],
                    [
                        'q' => 'Choose the correct article: "I need ___ umbrella."',
                        'options' => ['an' => true, 'a' => false, 'the' => false, 'some' => false],
                        'why' => 'Kata "umbrella" diawali bunyi vokal, sehingga menggunakan artikel "an".',
                    ],
                    [
                        'q' => 'Choose the correct article: "He is ___ teacher."',
                        'options' => ['a' => true, 'an' => false, 'the' => false, 'some' => false],
                        'why' => 'Kata "teacher" diawali bunyi konsonan, sehingga menggunakan artikel "a".',
                    ],
                    [
                        'q' => 'What is the plural form of "man"?',
                        'options' => ['men' => true, 'mans' => false, 'mens' => false, 'manes' => false],
                        'why' => '"Man" adalah kata benda tidak beraturan, bentuk jamaknya adalah "men".',
                    ],
                    [
                        'q' => 'What is the plural form of "foot"?',
                        'options' => ['feet' => true, 'foots' => false, 'feets' => false, 'foot' => false],
                        'why' => '"Foot" adalah kata benda tidak beraturan, bentuk jamaknya adalah "feet".',
                    ],
                    [
                        'q' => 'Choose the correct pronoun: "This is my mother. ___ is kind."',
                        'options' => ['She' => true, 'He' => false, 'It' => false, 'They' => false],
                        'why' => '"Mother" adalah perempuan tunggal, sehingga menggunakan kata ganti "She".',
                    ],
                    [
                        'q' => 'Choose the correct preposition: "The cat is ___ the box."',
                        'options' => ['in' => true, 'on' => false, 'at' => false, 'under' => false],
                        'why' => 'Kata depan "in" digunakan untuk menunjukkan sesuatu berada di dalam suatu ruang, seperti kotak.',
                    ],
                    [
                        'q' => 'What do you say when you leave someone at night?',
                        'options' => ['Good night' => true, 'Good morning' => false, 'Good afternoon' => false, 'See you soon' => false],
                        'why' => '"Good night" digunakan sebagai ucapan perpisahan pada malam hari.',
                    ],
                    [
                        'q' => 'What do you say when you meet someone for the first time?',
                        'options' => ['Nice to meet you' => true, 'See you later' => false, 'Good night' => false, 'How are you' => false],
                        'why' => '"Nice to meet you" adalah ungkapan sopan saat berkenalan dengan seseorang untuk pertama kali.',
                    ],
                    [
                        'q' => "What do you call your mother's brother?",
                        'options' => ['Uncle' => true, 'Aunt' => false, 'Cousin' => false, 'Nephew' => false],
                        'why' => 'Saudara laki-laki dari ibu disebut "uncle" (paman) dalam bahasa Inggris.',
                    ],
                    [
                        'q' => "What do you call your father's mother?",
                        'options' => ['Grandmother' => true, 'Grandfather' => false, 'Mother' => false, 'Sister' => false],
                        'why' => 'Ibu dari ayah disebut "grandmother" (nenek) dalam bahasa Inggris.',
                    ],
                    [
                        'q' => 'How do you write "12" in English?',
                        'options' => ['twelve' => true, 'twenty' => false, 'two' => false, 'eleven' => false],
                        'why' => 'Angka 12 dalam bahasa Inggris ditulis "twelve".',
                    ],
                    [
                        'q' => 'What number comes after "nine"?',
                        'options' => ['ten' => true, 'eight' => false, 'eleven' => false, 'nineteen' => false],
                        'why' => 'Setelah "nine" (9), angka berikutnya adalah "ten" (10).',
                    ],
                    [
                        'q' => 'What color do you get when you mix red and white?',
                        'options' => ['pink' => true, 'purple' => false, 'orange' => false, 'brown' => false],
                        'why' => 'Mencampur warna merah (red) dan putih (white) menghasilkan warna merah muda (pink).',
                    ],
                    [
                        'q' => 'What color is the sky on a clear day?',
                        'options' => ['blue' => true, 'green' => false, 'red' => false, 'yellow' => false],
                        'why' => 'Pada hari yang cerah, langit berwarna biru (blue).',
                    ],
                    [
                        'q' => 'Which day comes before "Friday"?',
                        'options' => ['Thursday' => true, 'Wednesday' => false, 'Saturday' => false, 'Sunday' => false],
                        'why' => 'Urutan hari: ..., Wednesday, Thursday, Friday, ... sehingga hari sebelum Friday adalah Thursday.',
                    ],
                    [
                        'q' => 'Which two days are called "the weekend"?',
                        'options' => ['Saturday and Sunday' => true, 'Friday and Saturday' => false, 'Monday and Tuesday' => false, 'Sunday and Monday' => false],
                        'why' => 'Akhir pekan (weekend) dalam bahasa Inggris terdiri dari hari Saturday dan Sunday.',
                    ],
                    [
                        'q' => 'What do you use to cut paper?',
                        'options' => ['Scissors' => true, 'Spoon' => false, 'Ruler' => false, 'Fork' => false],
                        'why' => '"Scissors" (gunting) adalah alat yang digunakan untuk memotong kertas.',
                    ],
                    [
                        'q' => 'What do you use to tell the time?',
                        'options' => ['Clock' => true, 'Calendar' => false, 'Mirror' => false, 'Map' => false],
                        'why' => '"Clock" (jam) adalah benda yang digunakan untuk mengetahui waktu.',
                    ],
                    [
                        'q' => 'What is the English word for "nasi"?',
                        'options' => ['rice' => true, 'bread' => false, 'noodle' => false, 'corn' => false],
                        'why' => 'Kata "nasi" dalam bahasa Inggris adalah "rice".',
                    ],
                    [
                        'q' => 'What do you wear on your feet?',
                        'options' => ['Shoes' => true, 'Hat' => false, 'Gloves' => false, 'Scarf' => false],
                        'why' => '"Shoes" (sepatu) adalah pakaian yang dikenakan pada kaki.',
                    ],
                    [
                        'q' => 'Read the passage: "Anna lives in a small house with her parents. She has one brother named Ben. Every morning, they eat breakfast together." How many brothers does Anna have?',
                        'options' => ['One' => true, 'Two' => false, 'Three' => false, 'None' => false],
                        'why' => 'Teks menyebutkan Anna memiliki satu saudara laki-laki bernama Ben.',
                    ],
                    [
                        'q' => 'Read the passage: "Tom likes to play football on weekends. On Saturday, he plays with his friends in the park. He is very happy after playing." When does Tom play football with his friends?',
                        'options' => ['On Saturday' => true, 'On Monday' => false, 'On Wednesday' => false, 'Every day' => false],
                        'why' => 'Teks menyatakan bahwa Tom bermain sepak bola bersama temannya pada hari Sabtu (Saturday).',
                    ],
                    [
                        'q' => 'Read the passage: "It is winter now. The weather is very cold. Sarah wears a warm jacket and a scarf every day." What is the weather like in the passage?',
                        'options' => ['Cold' => true, 'Hot' => false, 'Sunny' => false, 'Rainy' => false],
                        'why' => 'Teks menyatakan bahwa cuacanya sangat dingin ("very cold") pada musim dingin.',
                    ],
                    [
                        'q' => 'Read the passage: "Peter has a red bicycle. He rides it to school every day. His school is near his house." How does Peter go to school?',
                        'options' => ['By bicycle' => true, 'By car' => false, 'By bus' => false, 'On foot' => false],
                        'why' => 'Teks menyebutkan bahwa Peter mengendarai sepedanya ("rides it") ke sekolah setiap hari.',
                    ],
                    [
                        'q' => 'Read the passage: "Mia goes to the market with her mother every Sunday. They buy fruits and vegetables. Mia likes bananas the most." What fruit does Mia like the most?',
                        'options' => ['Bananas' => true, 'Apples' => false, 'Mangoes' => false, 'Grapes' => false],
                        'why' => 'Teks menyatakan bahwa Mia paling suka pisang ("likes bananas the most").',
                    ],
                    [
                        'q' => 'Read the passage: "David has three pets: a dog, a cat, and a bird. He feeds them every morning before school." How many pets does David have?',
                        'options' => ['Three' => true, 'Two' => false, 'Four' => false, 'One' => false],
                        'why' => 'Teks menyebutkan David memiliki tiga hewan peliharaan: anjing, kucing, dan burung.',
                    ],
                ],
                category: 'test',
                timeLimitSeconds: 30 * 60,
                description: '20 soal berfokus pada level Beginner saja, dengan waktu pengerjaan 30 menit — cocok untuk latihan tes bertahap sebelum mencoba tes penempatan lengkap.',
            );

            $this->create(
                null,
                'Tes Level Elementary',
                'easy',
                'Elementary',
                [
                    [
                        'q' => 'She ___ to the market yesterday morning.',
                        'options' => ['went' => true, 'go' => false, 'goes' => false, 'going' => false],
                        'why' => '"Yesterday morning" menunjukkan waktu lampau, sehingga digunakan simple past tense "went" (bentuk kedua dari "go").',
                    ],
                    [
                        'q' => 'Look! The children ___ football in the park right now.',
                        'options' => ['are playing' => true, 'play' => false, 'played' => false, 'plays' => false],
                        'why' => 'Kata "right now" menunjukkan kejadian sedang berlangsung, sehingga menggunakan present continuous "are playing".',
                    ],
                    [
                        'q' => 'An elephant is much ___ than a mouse.',
                        'options' => ['bigger' => true, 'more big' => false, 'biggest' => false, 'big' => false],
                        'why' => 'Untuk membandingkan dua benda digunakan bentuk komparatif adjective pendek dengan akhiran "-er", yaitu "bigger".',
                    ],
                    [
                        'q' => 'This is ___ mountain in the whole country.',
                        'options' => ['the highest' => true, 'higher' => false, 'the higher' => false, 'high' => false],
                        'why' => 'Karena membandingkan satu hal dengan semua yang lain dalam satu kelompok ("in the whole country"), digunakan bentuk superlatif "the highest".',
                    ],
                    [
                        'q' => 'How ___ water do you drink every day?',
                        'options' => ['much' => true, 'many' => false, 'a' => false, 'some' => false],
                        'why' => '"Water" adalah uncountable noun, sehingga menggunakan "much" untuk menanyakan jumlah, bukan "many" yang digunakan untuk countable noun.',
                    ],
                    [
                        'q' => 'There are only a few ___ left in the basket.',
                        'options' => ['apples' => true, 'apple' => false, 'rice' => false, 'bread' => false],
                        'why' => '"A few" digunakan sebelum countable noun bentuk jamak, sehingga jawaban yang tepat adalah "apples".',
                    ],
                    [
                        'q' => 'I ___ swim very well when I was ten years old.',
                        'options' => ['could' => true, 'can' => false, 'may' => false, 'must' => false],
                        'why' => '"Could" adalah bentuk lampau dari "can" yang digunakan untuk menyatakan kemampuan di masa lalu.',
                    ],
                    [
                        'q' => 'My brother ___ speak three languages fluently.',
                        'options' => ['can' => true, 'could' => false, 'must' => false, 'should' => false],
                        'why' => '"Can" digunakan untuk menyatakan kemampuan seseorang saat ini, yaitu kemampuan berbicara tiga bahasa.',
                    ],
                    [
                        'q' => 'Which sentence uses the frequency adverb in the correct position?',
                        'options' => [
                            'She always eats breakfast before school.' => true,
                            'She eats always breakfast before school.' => false,
                            'Always she eats breakfast before school.' => false,
                            'She eats breakfast always before school.' => false,
                        ],
                        'why' => 'Adverb frekuensi seperti "always" biasanya diletakkan sebelum kata kerja utama (bukan kata kerja "to be"), yaitu setelah subjek dan sebelum verb.',
                    ],
                    [
                        'q' => 'The meeting will start ___ 9 o\'clock ___ Monday morning.',
                        'options' => ['at / on' => true, 'on / at' => false, 'in / at' => false, 'at / in' => false],
                        'why' => 'Preposisi "at" digunakan untuk jam (9 o\'clock) dan "on" digunakan untuk hari tertentu (Monday morning).',
                    ],
                    [
                        'q' => 'The cat is sleeping ___ the sofa ___ the living room.',
                        'options' => ['on / in' => true, 'in / on' => false, 'at / on' => false, 'on / at' => false],
                        'why' => '"On" digunakan untuk posisi di atas permukaan (sofa), sedangkan "in" digunakan untuk ruang tertutup seperti ruangan.',
                    ],
                    [
                        'q' => 'Which sentence is a correct imperative (command)?',
                        'options' => [
                            'Close the door, please.' => true,
                            'You closing the door, please.' => false,
                            'Closes the door, please.' => false,
                            'Closed the door, please.' => false,
                        ],
                        'why' => 'Kalimat imperatif (perintah) menggunakan bentuk dasar kata kerja (base form) tanpa subjek, seperti "Close the door".',
                    ],
                    [
                        'q' => 'I wanted to go to the beach, ___ it started raining heavily.',
                        'options' => ['but' => true, 'and' => false, 'so' => false, 'because' => false],
                        'why' => '"But" digunakan untuk menghubungkan dua ide yang berlawanan atau kontras, yaitu keinginan pergi ke pantai dan hujan deras.',
                    ],
                    [
                        'q' => 'He studied very hard, ___ he passed the exam.',
                        'options' => ['so' => true, 'but' => false, 'or' => false, 'although' => false],
                        'why' => '"So" digunakan untuk menunjukkan hasil atau akibat, yaitu belajar keras menyebabkan lulus ujian.',
                    ],
                    [
                        'q' => 'Tom did not ___ his homework last night, so his teacher was angry.',
                        'options' => ['finish' => true, 'finished' => false, 'finishes' => false, 'finishing' => false],
                        'why' => 'Setelah auxiliary verb "did not" dalam simple past negative, kata kerja utama harus dalam bentuk dasar (base form), yaitu "finish".',
                    ],
                    [
                        'q' => '___ your parents watching TV at the moment?',
                        'options' => ['Are' => true, 'Do' => false, 'Is' => false, 'Did' => false],
                        'why' => 'Karena subjeknya jamak ("your parents") dan kalimat merupakan pertanyaan present continuous, digunakan "Are" di awal kalimat.',
                    ],
                    [
                        'q' => 'This bag is expensive, but that one is even ___.',
                        'options' => ['more expensive' => true, 'expensiver' => false, 'most expensive' => false, 'expensive' => false],
                        'why' => 'Adjective panjang seperti "expensive" menggunakan "more" untuk membentuk komparatif, yaitu "more expensive".',
                    ],
                    [
                        'q' => 'My exam score was bad, but Rina\'s score was even worse. Her score was the ___ in the whole class.',
                        'options' => ['worst' => true, 'worse' => false, 'more bad' => false, 'baddest' => false],
                        'why' => '"Bad" memiliki bentuk superlatif tidak beraturan, yaitu "worst" (bad - worse - worst).',
                    ],
                    [
                        'q' => 'Read the passage: "Maria wakes up at six every morning. She always drinks a glass of milk and eats bread for breakfast. After that, she walks to school with her younger brother. School starts at seven thirty." What does Maria drink every morning?',
                        'options' => ['A glass of milk' => true, 'A cup of coffee' => false, 'A glass of juice' => false, 'Tea with sugar' => false],
                        'why' => 'Berdasarkan teks, dinyatakan secara jelas bahwa Maria "always drinks a glass of milk" setiap pagi.',
                    ],
                    [
                        'q' => 'Read the passage: "Last weekend, David and his friends went camping near a lake. They set up their tent, cooked dinner over a fire, and told funny stories all night. The next morning, they were tired but very happy." How did David and his friends feel the next morning?',
                        'options' => ['Tired but happy' => true, 'Angry and bored' => false, 'Sick and sad' => false, 'Excited to go home' => false],
                        'why' => 'Teks menyebutkan secara eksplisit bahwa keesokan paginya mereka "were tired but very happy".',
                    ],
                    [
                        'q' => 'They ___ a new house last year.',
                        'options' => ['bought' => true, 'buy' => false, 'buys' => false, 'buying' => false],
                        'why' => "'Bought' adalah bentuk lampau (past tense) tidak beraturan dari 'buy', digunakan karena ada keterangan waktu 'last year'.",
                    ],
                    [
                        'q' => 'We ___ pizza for dinner last night.',
                        'options' => ['ordered' => true, 'order' => false, 'orders' => false, 'ordering' => false],
                        'why' => "'Ordered' adalah bentuk lampau dari 'order', sesuai dengan keterangan waktu 'last night'.",
                    ],
                    [
                        'q' => '___ you visit your grandmother last weekend?',
                        'options' => ['Did' => true, 'Do' => false, 'Does' => false, 'Are' => false],
                        'why' => "Kalimat tanya dalam simple past menggunakan 'Did' di awal kalimat, diikuti kata kerja bentuk dasar.",
                    ],
                    [
                        'q' => 'Sarah ___ eat breakfast this morning because she woke up late.',
                        'options' => ['did not' => true, 'does not' => false, 'is not' => false, 'was not' => false],
                        'why' => "Kalimat negatif simple past menggunakan 'did not' + kata kerja bentuk dasar.",
                    ],
                    [
                        'q' => 'Right now, my father ___ the newspaper in the living room.',
                        'options' => ['is reading' => true, 'reads' => false, 'read' => false, 'was reading' => false],
                        'why' => "'Right now' menunjukkan kejadian sedang berlangsung, sehingga menggunakan present continuous 'is reading'.",
                    ],
                    [
                        'q' => 'The students ___ not listening to the teacher at the moment.',
                        'options' => ['are' => true, 'is' => false, 'was' => false, 'do' => false],
                        'why' => "Subjek 'the students' adalah jamak, sehingga menggunakan 'are' untuk bentuk present continuous negatif.",
                    ],
                    [
                        'q' => 'What ___ you doing right now?',
                        'options' => ['are' => true, 'is' => false, 'do' => false, 'did' => false],
                        'why' => "Kalimat tanya present continuous dengan subjek 'you' menggunakan 'are'.",
                    ],
                    [
                        'q' => 'This exercise is ___ than the last one.',
                        'options' => ['more difficult' => true, 'difficulter' => false, 'most difficult' => false, 'difficult' => false],
                        'why' => "Untuk kata sifat panjang seperti 'difficult', bentuk perbandingan menggunakan 'more' + kata sifat.",
                    ],
                    [
                        'q' => 'My sister is ___ than me.',
                        'options' => ['taller' => true, 'more tall' => false, 'tallest' => false, 'tall' => false],
                        'why' => "Kata sifat pendek seperti 'tall' membentuk perbandingan dengan menambahkan '-er'.",
                    ],
                    [
                        'q' => 'This is ___ restaurant in town.',
                        'options' => ['the best' => true, 'the goodest' => false, 'better' => false, 'good' => false],
                        'why' => "'Good' adalah kata sifat tidak beraturan, bentuk superlatifnya adalah 'the best'.",
                    ],
                    [
                        'q' => 'That was ___ movie I have ever seen.',
                        'options' => ['the most boring' => true, 'the boringest' => false, 'more boring' => false, 'boring' => false],
                        'why' => "Untuk kata sifat panjang seperti 'boring', bentuk superlatif menggunakan 'the most' + kata sifat.",
                    ],
                    [
                        'q' => 'How ___ books do you have?',
                        'options' => ['many' => true, 'much' => false, 'a little' => false, 'is' => false],
                        'why' => "'Books' adalah kata benda yang dapat dihitung (countable), sehingga menggunakan 'many'.",
                    ],
                    [
                        'q' => "There isn't ___ sugar in the jar.",
                        'options' => ['any' => true, 'some' => false, 'many' => false, 'few' => false],
                        'why' => "Dalam kalimat negatif, kata benda tak terhitung seperti 'sugar' menggunakan 'any', bukan 'some'.",
                    ],
                    [
                        'q' => 'My little brother ___ ride a bicycle already.',
                        'options' => ['can' => true, 'could' => false, 'cans' => false, 'is can' => false],
                        'why' => "'Can' digunakan untuk menyatakan kemampuan di masa sekarang.",
                    ],
                    [
                        'q' => 'Before he broke his leg, he ___ play football every weekend.',
                        'options' => ['could' => true, 'can' => false, 'cans' => false, 'will' => false],
                        'why' => "'Could' digunakan untuk menyatakan kemampuan di masa lalu, sebelum kejadian kakinya patah.",
                    ],
                    [
                        'q' => '___ I borrow your pen, please?',
                        'options' => ['Can' => true, 'Am' => false, 'Do' => false, 'Does' => false],
                        'why' => "'Can' digunakan untuk meminta izin secara sopan dalam kalimat tanya.",
                    ],
                    [
                        'q' => 'My father ___ drinks coffee in the morning; he has it every day.',
                        'options' => ['always' => true, 'never' => false, 'rarely' => false, 'seldom' => false],
                        'why' => "Karena ia minum kopi 'setiap hari', kata keterangan frekuensi yang tepat adalah 'always' (selalu).",
                    ],
                    [
                        'q' => 'We ___ eat fast food because it is not healthy.',
                        'options' => ['rarely' => true, 'always' => false, 'usually' => false, 'often' => false],
                        'why' => "Karena fast food tidak sehat, kata keterangan frekuensi yang logis adalah 'rarely' (jarang).",
                    ],
                    [
                        'q' => 'Her birthday is ___ July.',
                        'options' => ['in' => true, 'on' => false, 'at' => false, 'to' => false],
                        'why' => "Preposisi 'in' digunakan untuk bulan, seperti 'in July'.",
                    ],
                    [
                        'q' => 'We always go swimming ___ Saturdays.',
                        'options' => ['on' => true, 'in' => false, 'at' => false, 'for' => false],
                        'why' => "Preposisi 'on' digunakan untuk hari, seperti 'on Saturdays'.",
                    ],
                    [
                        'q' => 'The keys are ___ the drawer.',
                        'options' => ['in' => true, 'on' => false, 'at' => false, 'under' => false],
                        'why' => "Preposisi 'in' digunakan karena kunci berada di dalam laci.",
                    ],
                    [
                        'q' => 'There is a picture ___ the wall.',
                        'options' => ['on' => true, 'in' => false, 'at' => false, 'under' => false],
                        'why' => "Preposisi 'on' digunakan untuk sesuatu yang menempel pada permukaan, seperti dinding.",
                    ],
                    [
                        'q' => '___ the door before you leave the house.',
                        'options' => ['Close' => true, 'Closing' => false, 'Closes' => false, 'To close' => false],
                        'why' => 'Kalimat perintah (imperative) menggunakan kata kerja bentuk dasar di awal kalimat.',
                    ],
                    [
                        'q' => '___ touch that hot pan!',
                        'options' => ["Don't" => true, "Doesn't" => false, 'Not' => false, 'No' => false],
                        'why' => "Kalimat perintah larangan menggunakan 'Don't' + kata kerja bentuk dasar.",
                    ],
                    [
                        'q' => 'I like tea ___ my sister likes coffee.',
                        'options' => ['and' => true, 'but' => false, 'so' => false, 'because' => false],
                        'why' => "'And' digunakan untuk menghubungkan dua informasi yang sejalan, bukan bertentangan.",
                    ],
                    [
                        'q' => 'He was late ___ he missed the bus.',
                        'options' => ['because' => true, 'so' => false, 'and' => false, 'but' => false],
                        'why' => "'Because' digunakan untuk menjelaskan alasan/sebab dari suatu kejadian.",
                    ],
                    [
                        'q' => 'Read the passage: "Every Friday, Mr. Hendra goes to the market with his wife. They buy fresh vegetables, fruit, and fish. After shopping, they usually have lunch at a small restaurant near the market. Mr. Hendra always pays for the lunch." What does Mr. Hendra always do?',
                        'options' => ['He pays for the lunch.' => true, 'He cooks dinner.' => false, 'He buys a car.' => false, 'He cleans the house.' => false],
                        'why' => "Teks menyebutkan 'Mr. Hendra always pays for the lunch', artinya ia selalu membayar makan siang.",
                    ],
                    [
                        'q' => "Read the passage: \"Yesterday was Siti's birthday. Her friends came to her house in the afternoon. They brought presents and a big chocolate cake. Everyone sang a birthday song, and Siti was very happy.\" What did Siti's friends bring?",
                        'options' => ['Presents and a cake' => true, 'Books and pens' => false, 'Flowers only' => false, 'Nothing' => false],
                        'why' => "Teks menyebutkan teman-temannya membawa 'presents and a big chocolate cake'.",
                    ],
                    [
                        'q' => 'Read the passage: "Ali works at a small bookstore in the city. He opens the shop at eight every morning and closes it at six in the evening. On Sundays, the shop is closed, so Ali stays home and rests." When is the bookstore closed?',
                        'options' => ['On Sundays' => true, 'On Saturdays' => false, 'Every evening' => false, 'Every morning' => false],
                        'why' => "Teks menyebutkan 'On Sundays, the shop is closed', artinya toko tutup setiap hari Minggu.",
                    ],
                    [
                        'q' => 'Read the passage: "Last month, Dina and her family went to the zoo. They saw lions, elephants, and monkeys. Dina liked the monkeys the most because they were very funny. The family took a lot of photos before they went home." Why did Dina like the monkeys the most?',
                        'options' => ['Because they were very funny' => true, 'Because they were sleeping' => false, 'Because they were big' => false, 'Because they were dangerous' => false],
                        'why' => "Teks menyebutkan Dina menyukai monyet paling banyak 'because they were very funny'.",
                    ],
                ],
                category: 'test',
                timeLimitSeconds: 30 * 60,
                description: '20 soal berfokus pada level Elementary saja, dengan waktu pengerjaan 30 menit — cocok untuk latihan tes bertahap sebelum mencoba tes penempatan lengkap.',
            );

            $this->create(
                null,
                'Tes Level Intermediate',
                'medium',
                'Intermediate',
                [
                    [
                        'q' => 'Complete the sentence: "___ you ever eaten sushi?"',
                        'options' => ['Have' => true, 'Did' => false, 'Do' => false, 'Are' => false],
                        'why' => '"Have you ever...?" adalah bentuk present perfect yang digunakan untuk menanyakan pengalaman seseorang.',
                    ],
                    [
                        'q' => 'Complete the sentence: "She ___ just finished her homework."',
                        'options' => ['has' => true, 'have' => false, 'did' => false, 'is' => false],
                        'why' => 'Subjek tunggal "she" dengan kata "just" dan past participle menggunakan present perfect: has + past participle.',
                    ],
                    [
                        'q' => 'Complete the sentence: "While I ___ dinner, the phone rang."',
                        'options' => ['was cooking' => true, 'cooked' => false, 'cook' => false, 'have cooked' => false],
                        'why' => 'Aksi yang sedang berlangsung (was cooking) diinterupsi oleh aksi singkat (rang) menggunakan past continuous.',
                    ],
                    [
                        'q' => 'Choose the correct question: "___ you ___ when I called you last night?"',
                        'options' => ['Were / doing' => true, 'Did / do' => false, 'Are / doing' => false, 'Have / done' => false],
                        'why' => 'Menanyakan aksi yang sedang berlangsung pada waktu tertentu di masa lalu menggunakan past continuous: were + verb-ing.',
                    ],
                    [
                        'q' => 'Complete the sentence: "I think the economy ___ improve next year."',
                        'options' => ['will' => true, 'would' => false, 'was' => false, 'has' => false],
                        'why' => 'Prediksi tanpa bukti kuat, biasanya setelah "I think", menggunakan "will".',
                    ],
                    [
                        'q' => 'Complete the sentence: "We have already booked the tickets. We ___ to Bali next week."',
                        'options' => ['are going' => true, 'will go' => false, 'go' => false, 'went' => false],
                        'why' => 'Rencana yang sudah diatur atau diputuskan sebelumnya menggunakan "going to".',
                    ],
                    [
                        'q' => 'Complete the sentence: "If it rains tomorrow, we ___ the picnic."',
                        'options' => ['will cancel' => true, 'cancel' => false, 'canceled' => false, 'would cancel' => false],
                        'why' => 'First conditional: If + present simple, ... will + verb dasar, digunakan untuk kemungkinan nyata di masa depan.',
                    ],
                    [
                        'q' => 'Choose the correct option: "___ you hurry, you will miss the bus."',
                        'options' => ['Unless' => true, 'If' => false, 'Because' => false, 'Although' => false],
                        'why' => '"Unless" berarti "if not" (kecuali/jika tidak), cocok dengan konteks kalimat ini.',
                    ],
                    [
                        'q' => 'Complete the sentence: "You look tired. You ___ get some rest."',
                        'options' => ['should' => true, 'must' => false, 'can' => false, 'will' => false],
                        'why' => '"Should" digunakan untuk memberi saran, bukan keharusan mutlak seperti "must".',
                    ],
                    [
                        'q' => 'Complete the sentence: "All passengers ___ wear seatbelts during the flight. It\'s the law."',
                        'options' => ['must' => true, 'should' => false, 'can' => false, 'might' => false],
                        'why' => '"Must" digunakan untuk kewajiban atau aturan yang kuat/mengikat.',
                    ],
                    [
                        'q' => 'Complete the sentence: "Tomorrow is a holiday, so we ___ go to work."',
                        'options' => ["don't have to" => true, "mustn't" => false, "shouldn't" => false, "can't" => false],
                        'why' => '"Don\'t have to" berarti tidak ada keharusan/tidak wajib, berbeda dengan "mustn\'t" yang berarti dilarang.',
                    ],
                    [
                        'q' => 'Choose the correct passive form: "English ___ in many countries."',
                        'options' => ['is spoken' => true, 'speaks' => false, 'is speaking' => false, 'spoke' => false],
                        'why' => 'Passive voice present simple dibentuk dengan is/am/are + past participle.',
                    ],
                    [
                        'q' => 'Complete the sentence: "This house ___ in 1985."',
                        'options' => ['was built' => true, 'built' => false, 'is built' => false, 'has build' => false],
                        'why' => 'Passive voice past simple dibentuk dengan was/were + past participle.',
                    ],
                    [
                        'q' => 'Choose the correct relative pronoun: "The woman ___ is standing over there is my teacher."',
                        'options' => ['who' => true, 'which' => false, 'whose' => false, 'when' => false],
                        'why' => '"Who" digunakan untuk menerangkan orang sebagai subjek dalam klausa relatif.',
                    ],
                    [
                        'q' => 'Complete the sentence: "That\'s the man ___ car was stolen yesterday."',
                        'options' => ['whose' => true, 'who' => false, 'which' => false, 'that' => false],
                        'why' => '"Whose" digunakan untuk menunjukkan kepemilikan (posesif) dalam klausa relatif.',
                    ],
                    [
                        'q' => 'Complete the sentence: "She enjoys ___ novels in her free time."',
                        'options' => ['reading' => true, 'to read' => false, 'read' => false, 'reads' => false],
                        'why' => 'Kata kerja "enjoy" selalu diikuti oleh gerund (verb-ing), bukan infinitive.',
                    ],
                    [
                        'q' => 'Complete the sentence: "He decided ___ a new car."',
                        'options' => ['to buy' => true, 'buying' => false, 'buy' => false, 'bought' => false],
                        'why' => 'Kata kerja "decide" diikuti oleh to-infinitive (to + verb dasar).',
                    ],
                    [
                        'q' => 'Choose the correct phrasal verb: "Could you turn ___ the volume? I can\'t hear the movie."',
                        'options' => ['up' => true, 'off' => false, 'out' => false, 'over' => false],
                        'why' => '"Turn up" berarti memperbesar volume atau suara.',
                    ],
                    [
                        'q' => 'Read the passage: "Maria works as a nurse at a busy hospital in Jakarta. She usually starts her shift at 7 a.m. and finishes at 3 p.m. Last month, she was offered a promotion to head nurse, but she has not decided yet whether to accept it. If she takes the new position, she will have more responsibility and a higher salary, but she will also work longer hours. Maria loves spending time with her patients, and she is worried that a management job will keep her away from direct patient care." What is Maria\'s current job?',
                        'options' => ['A nurse' => true, 'A head nurse' => false, 'A doctor' => false, 'A hospital manager' => false],
                        'why' => 'Teks menyatakan "Maria works as a nurse" - dia saat ini berprofesi sebagai perawat, belum menerima promosi menjadi head nurse.',
                    ],
                    [
                        'q' => 'Read the passage: "Maria works as a nurse at a busy hospital in Jakarta. She usually starts her shift at 7 a.m. and finishes at 3 p.m. Last month, she was offered a promotion to head nurse, but she has not decided yet whether to accept it. If she takes the new position, she will have more responsibility and a higher salary, but she will also work longer hours. Maria loves spending time with her patients, and she is worried that a management job will keep her away from direct patient care." According to the passage, what will happen if Maria accepts the promotion?',
                        'options' => ['She will have more responsibility, a higher salary, and longer hours' => true, 'She will work fewer hours' => false, 'She will leave the hospital' => false, 'She will get a lower salary' => false],
                        'why' => 'Teks menyatakan bahwa jika dia menerima posisi baru, dia akan memiliki lebih banyak tanggung jawab dan gaji lebih tinggi, tetapi juga bekerja lebih lama.',
                    ],
                    [
                        'q' => 'Complete the sentence: "I ___ this company since 2019."',
                        'options' => ['have worked' => true, 'am working' => false, 'worked' => false, 'work' => false],
                        'why' => 'Present perfect (have + V3) digunakan untuk aksi yang dimulai di masa lalu dan masih berlanjut hingga sekarang, ditandai dengan kata "since".',
                    ],
                    [
                        'q' => 'Complete the sentence: "I ___ that movie three times already."',
                        'options' => ['have watched' => true, 'watched' => false, 'am watching' => false, 'watch' => false],
                        'why' => 'Present perfect digunakan untuk menyatakan pengalaman yang sudah terjadi tanpa menyebut waktu pastinya, sering disertai kata "already".',
                    ],
                    [
                        'q' => 'Complete the sentence: "I ___ a shower when the doorbell rang."',
                        'options' => ['was taking' => true, 'took' => false, 'take' => false, 'am taking' => false],
                        'why' => 'Past continuous (was/were + V-ing) menunjukkan aksi yang sedang berlangsung ketika aksi lain (past simple) tiba-tiba terjadi.',
                    ],
                    [
                        'q' => 'Complete the sentence: "While my father ___ the newspaper, my mother was cooking dinner."',
                        'options' => ['was reading' => true, 'read' => false, 'reads' => false, 'is reading' => false],
                        'why' => 'Dua aksi yang berlangsung bersamaan di masa lalu sama-sama menggunakan past continuous.',
                    ],
                    [
                        'q' => 'Complete the sentence: "Look at those dark clouds! It ___ rain soon."',
                        'options' => ['is going to' => true, 'will' => false, 'is' => false, 'was going to' => false],
                        'why' => '"Going to" digunakan untuk prediksi berdasarkan bukti yang terlihat saat ini, seperti awan gelap.',
                    ],
                    [
                        'q' => 'Complete the sentence: "I\'m so thirsty. I ___ a glass of water."',
                        'options' => ['will have' => true, 'am having' => false, 'have' => false, 'am going to have' => false],
                        'why' => '"Will" digunakan untuk keputusan spontan yang dibuat saat itu juga, tanpa rencana sebelumnya.',
                    ],
                    [
                        'q' => 'Complete the sentence: "If the weather ___ nice tomorrow, we will go hiking."',
                        'options' => ['is' => true, 'will be' => false, 'was' => false, 'would be' => false],
                        'why' => 'Pada conditional type 1, klausa "if" menggunakan present simple meskipun mengacu pada kejadian di masa depan.',
                    ],
                    [
                        'q' => 'Complete the sentence: "If you save money regularly, you ___ enough for a vacation by next year."',
                        'options' => ['will have' => true, 'have' => false, 'had' => false, 'would have' => false],
                        'why' => 'Pada conditional type 1, klausa utama menggunakan "will + V1" untuk menyatakan hasil yang mungkin terjadi di masa depan.',
                    ],
                    [
                        'q' => 'Complete the sentence: "I ___ call my mother tonight; I promised her."',
                        'options' => ['must' => true, 'have to' => false, 'should' => false, 'can' => false],
                        'why' => '"Must" digunakan untuk kewajiban yang berasal dari keinginan atau perasaan pribadi pembicara, bukan aturan eksternal.',
                    ],
                    [
                        'q' => 'Complete the sentence: "You ___ smoke here; it is strictly forbidden."',
                        'options' => ['mustn\'t' => true, 'don\'t have to' => false, 'shouldn\'t' => false, 'can\'t' => false],
                        'why' => '"Mustn\'t" berarti dilarang keras (prohibition), berbeda dengan "don\'t have to" yang berarti tidak wajib.',
                    ],
                    [
                        'q' => 'Complete the sentence: "You ___ wear a uniform on Fridays. It is optional."',
                        'options' => ['don\'t have to' => true, 'mustn\'t' => false, 'shouldn\'t' => false, 'can\'t' => false],
                        'why' => '"Don\'t have to" menunjukkan tidak ada kewajiban/opsional, berbeda dengan "mustn\'t" yang berarti dilarang.',
                    ],
                    [
                        'q' => 'Choose the correct passive form: "This bridge ___ by thousands of cars every day."',
                        'options' => ['is used' => true, 'uses' => false, 'is using' => false, 'used' => false],
                        'why' => 'Passive voice present simple ("is/am/are + past participle") digunakan karena subjek (bridge) menerima aksi, bukan melakukannya.',
                    ],
                    [
                        'q' => 'Choose the correct passive form: "The novel ___ by a famous author."',
                        'options' => ['was written' => true, 'wrote' => false, 'has written' => false, 'writes' => false],
                        'why' => 'Passive voice past simple menggunakan "was/were + past participle" untuk aksi yang selesai di masa lalu.',
                    ],
                    [
                        'q' => 'Choose the correct passive question: "___ the museum built in the 19th century?"',
                        'options' => ['Was' => true, 'Did' => false, 'Has' => false, 'Is' => false],
                        'why' => 'Untuk kalimat pasif past simple bentuk tanya, digunakan "Was/Were" di awal kalimat, bukan "Did".',
                    ],
                    [
                        'q' => 'Choose the correct relative pronoun: "The book ___ I borrowed from the library is fascinating."',
                        'options' => ['which' => true, 'who' => false, 'where' => false, 'whose' => false],
                        'why' => '"Which" digunakan untuk menggantikan kata benda berupa benda/objek (book), bukan orang atau tempat.',
                    ],
                    [
                        'q' => 'Choose the correct relative pronoun: "This is the restaurant ___ we had our first date."',
                        'options' => ['where' => true, 'which' => false, 'who' => false, 'whom' => false],
                        'why' => '"Where" digunakan untuk menerangkan tempat (restaurant) dalam klausa relatif.',
                    ],
                    [
                        'q' => 'Choose the correct sentence:',
                        'options' => ['The phone that I bought last week stopped working.' => true, 'The phone whom I bought last week stopped working.' => false, 'The phone where I bought last week stopped working.' => false, 'The phone whose I bought last week stopped working.' => false],
                        'why' => '"That" dapat digunakan untuk menggantikan "which" atau "who" dalam defining relative clause, terutama dalam bahasa Inggris informal.',
                    ],
                    [
                        'q' => 'Complete the sentence: "I am really looking forward to ___ you again."',
                        'options' => ['seeing' => true, 'see' => false, 'saw' => false, 'seen' => false],
                        'why' => 'Setelah frasa "look forward to", digunakan gerund (V-ing) karena "to" di sini berfungsi sebagai preposisi, bukan penanda infinitive.',
                    ],
                    [
                        'q' => 'Complete the sentence: "___ a new language takes a lot of practice."',
                        'options' => ['Learning' => true, 'Learn' => false, 'To learning' => false, 'Learned' => false],
                        'why' => 'Gerund (V-ing) dapat berfungsi sebagai subjek kalimat.',
                    ],
                    [
                        'q' => 'Complete the sentence: "It is important ___ enough water every day."',
                        'options' => ['to drink' => true, 'drinking' => false, 'drink' => false, 'drank' => false],
                        'why' => 'Setelah pola "It is + adjective", digunakan to-infinitive.',
                    ],
                    [
                        'q' => 'Complete the sentence: "The doctor suggested ___ more vegetables."',
                        'options' => ['eating' => true, 'to eat' => false, 'eat' => false, 'ate' => false],
                        'why' => 'Kata kerja "suggest" selalu diikuti oleh gerund (V-ing), bukan to-infinitive.',
                    ],
                    [
                        'q' => 'Complete the sentence: "They plan ___ their house next year."',
                        'options' => ['to sell' => true, 'selling' => false, 'sell' => false, 'sold' => false],
                        'why' => 'Kata kerja "plan" diikuti oleh to-infinitive.',
                    ],
                    [
                        'q' => 'Choose the correct phrasal verb: "She finally ___ smoking after years of trying."',
                        'options' => ['gave up' => true, 'gave in' => false, 'gave away' => false, 'gave out' => false],
                        'why' => '"Give up" berarti berhenti melakukan sesuatu, misalnya sebuah kebiasaan.',
                    ],
                    [
                        'q' => 'Choose the correct phrasal verb: "Can you ___ my cat while I\'m on vacation?"',
                        'options' => ['look after' => true, 'look for' => false, 'look up' => false, 'look into' => false],
                        'why' => '"Look after" berarti merawat atau menjaga seseorang/sesuatu.',
                    ],
                    [
                        'q' => 'Choose the correct phrasal verb: "The meeting has been ___ until next Monday."',
                        'options' => ['put off' => true, 'put on' => false, 'put up' => false, 'put away' => false],
                        'why' => '"Put off" berarti menunda sesuatu ke waktu lain.',
                    ],
                    [
                        'q' => 'Choose the correct phrasal verb: "I ___ my old classmate at the mall yesterday."',
                        'options' => ['ran into' => true, 'ran out' => false, 'ran off' => false, 'ran over' => false],
                        'why' => '"Run into" berarti bertemu seseorang secara tidak sengaja.',
                    ],
                    [
                        'q' => 'Complete the sentence: "It ___ rain later, so bring an umbrella just in case."',
                        'options' => ['might' => true, 'must' => false, 'should' => false, 'have to' => false],
                        'why' => '"Might" digunakan untuk menyatakan kemungkinan (possibility), bukan kepastian.',
                    ],
                    [
                        'q' => 'Read the passage: "Budi is a university student in Surabaya who has always dreamed of studying abroad. Last year, he started saving money and practicing English every day. He has already taken the IELTS test twice, but he needs a higher score to get accepted into his dream university in Australia. If he passes the test next month, he will apply for a scholarship immediately. Budi\'s parents are proud of him, although they will miss him a lot if he moves overseas. He is determined not to give up until he achieves his goal." What has Budi done to prepare for studying abroad?',
                        'options' => ['He has saved money and practiced English every day.' => true, 'He has already moved to Australia.' => false, 'He has stopped taking English lessons.' => false, 'He has decided not to study abroad.' => false],
                        'why' => 'Sesuai paragraf, Budi mulai menabung dan berlatih bahasa Inggris setiap hari sejak tahun lalu.',
                    ],
                    [
                        'q' => 'Read the passage: "Budi is a university student in Surabaya who has always dreamed of studying abroad. Last year, he started saving money and practicing English every day. He has already taken the IELTS test twice, but he needs a higher score to get accepted into his dream university in Australia. If he passes the test next month, he will apply for a scholarship immediately. Budi\'s parents are proud of him, although they will miss him a lot if he moves overseas. He is determined not to give up until he achieves his goal." According to the passage, what will Budi do if he passes the IELTS test next month?',
                        'options' => ['He will apply for a scholarship immediately.' => true, 'He will take the IELTS test again.' => false, 'He will give up his dream.' => false, 'He will stay in Surabaya permanently.' => false],
                        'why' => 'Paragraf menyebutkan bahwa jika ia lulus tes bulan depan, ia akan langsung mendaftar beasiswa (first conditional).',
                    ],
                    [
                        'q' => 'Read the passage: "Budi is a university student in Surabaya who has always dreamed of studying abroad. Last year, he started saving money and practicing English every day. He has already taken the IELTS test twice, but he needs a higher score to get accepted into his dream university in Australia. If he passes the test next month, he will apply for a scholarship immediately. Budi\'s parents are proud of him, although they will miss him a lot if he moves overseas. He is determined not to give up until he achieves his goal." How do Budi\'s parents feel about his plan?',
                        'options' => ['They are proud of him but will miss him.' => true, 'They are angry about his decision.' => false, 'They are indifferent to his plan.' => false, 'They want him to stay home forever.' => false],
                        'why' => 'Paragraf menyatakan orang tua Budi bangga padanya meskipun akan merindukannya jika ia pindah ke luar negeri.',
                    ],
                ],
                category: 'test',
                timeLimitSeconds: 30 * 60,
                description: '20 soal berfokus pada level Intermediate saja, dengan waktu pengerjaan 30 menit — cocok untuk latihan tes bertahap sebelum mencoba tes penempatan lengkap.',
            );

            $this->create(
                null,
                'Tes Level Upper-Intermediate',
                'medium',
                'Upper-Intermediate',
                [
                    [
                        'q' => 'By the time the plane took off, she ___ her boarding pass twice.',
                        'options' => ['had checked' => true, 'checked' => false, 'has checked' => false, 'was checking' => false],
                        'why' => 'Karena ada dua kejadian di masa lalu, kejadian yang terjadi lebih dulu (memeriksa boarding pass) menggunakan past perfect "had checked", sedangkan "took off" (past simple) menjadi titik acuan waktu yang lebih belakangan.',
                    ],
                    [
                        'q' => "I didn't recognize the city because I ___ there before.",
                        'options' => ['had never been' => true, 'have never been' => false, 'never went' => false, 'was never being' => false],
                        'why' => 'Past perfect "had never been" menunjukkan aksi yang belum pernah terjadi sebelum peristiwa lampau lain (tidak mengenali kota tersebut).',
                    ],
                    [
                        'q' => 'If I ___ more free time, I would travel around the world.',
                        'options' => ['had' => true, 'have' => false, 'will have' => false, 'would have' => false],
                        'why' => 'Second conditional menggunakan past simple ("had") pada if-clause untuk membicarakan situasi hipotetis atau tidak nyata di masa sekarang.',
                    ],
                    [
                        'q' => 'She would accept the job offer if the salary ___ higher.',
                        'options' => ['were' => true, 'is' => false, 'will be' => false, 'has been' => false],
                        'why' => 'Dalam second conditional, bentuk subjunctive "were" digunakan untuk semua subjek (termasuk "the salary") pada kondisi tidak nyata di masa sekarang.',
                    ],
                    [
                        'q' => 'The new bridge ___ by the end of next year.',
                        'options' => ['will have been completed' => true, 'will complete' => false, 'will be completing' => false, 'has completed' => false],
                        'why' => 'Future perfect passive digunakan untuk aksi yang akan selesai sebelum suatu titik waktu di masa depan, dan subjek "the bridge" menerima aksi tersebut sehingga bentuknya pasif.',
                    ],
                    [
                        'q' => 'The report ___ carefully before it was submitted to the manager.',
                        'options' => ['had been checked' => true, 'checked' => false, 'was checking' => false, 'has been checked' => false],
                        'why' => 'Past perfect passive ("had been checked") menunjukkan aksi memeriksa terjadi sebelum aksi lampau lain (submitted), dan subjek "the report" menerima aksi tersebut.',
                    ],
                    [
                        'q' => 'She said, "I am going to the market tomorrow." She said that she ___ to the market the next day.',
                        'options' => ['was going' => true, 'is going' => false, 'went' => false, 'has gone' => false],
                        'why' => 'Dalam reported speech, present continuous berubah menjadi past continuous, dan kata keterangan waktu "tomorrow" berubah menjadi "the next day".',
                    ],
                    [
                        'q' => '"I have finished my homework," he said. He said that he ___ his homework.',
                        'options' => ['had finished' => true, 'has finished' => false, 'finished' => false, 'was finishing' => false],
                        'why' => 'Present perfect dalam kalimat langsung berubah menjadi past perfect ("had finished") ketika diubah menjadi reported speech.',
                    ],
                    [
                        'q' => '"Can you help me carry these boxes?" she asked. She asked if I ___ help her carry the boxes.',
                        'options' => ['could' => true, 'can' => false, 'would' => false, 'should' => false],
                        'why' => 'Modal "can" dalam kalimat langsung berubah menjadi "could" dalam reported speech mengikuti aturan backshift tense.',
                    ],
                    [
                        'q' => "The lights are off and the car isn't in the driveway. They ___ out.",
                        'options' => ['must be' => true, 'must have' => false, 'can be' => false, 'should be' => false],
                        'why' => '"Must be" digunakan untuk deduksi logis yang kuat tentang situasi saat ini berdasarkan bukti yang terlihat.',
                    ],
                    [
                        'q' => "He didn't answer the phone when I called last night. He ___ asleep.",
                        'options' => ['might have been' => true, 'must be' => false, 'should have been' => false, 'can be' => false],
                        'why' => '"Might have been" menunjukkan deduksi atau kemungkinan tentang situasi di masa lalu (kemungkinan dia sedang tidur saat ditelepon).',
                    ],
                    [
                        'q' => 'My brother, ___ lives in Canada, is visiting us next week.',
                        'options' => ['who' => true, 'that' => false, 'which' => false, 'whom' => false],
                        'why' => 'Dalam non-defining relative clause (diapit koma), kata "that" tidak boleh digunakan; "who" digunakan untuk merujuk orang sebagai subjek klausa.',
                    ],
                    [
                        'q' => 'The museum, ___ was built in 1890, attracts thousands of visitors every year.',
                        'options' => ['which' => true, 'that' => false, 'who' => false, 'whose' => false],
                        'why' => '"Which" digunakan dalam non-defining relative clause untuk merujuk benda atau tempat; "that" tidak dapat digunakan setelah tanda koma.',
                    ],
                    [
                        'q' => 'When I was a child, I ___ live in a small fishing village, but we moved to the city when I was ten.',
                        'options' => ['used to' => true, 'would' => false, 'was used to' => false, 'get used to' => false],
                        'why' => '"Used to" digunakan untuk menggambarkan keadaan yang berlangsung lama di masa lalu (tinggal di sebuah desa), sedangkan "would" hanya cocok untuk aksi atau kebiasaan yang berulang, bukan keadaan.',
                    ],
                    [
                        'q' => 'Every Sunday, my grandmother ___ bake fresh bread for the whole family.',
                        'options' => ['would' => true, 'used to being' => false, 'was used to' => false, 'gets used to' => false],
                        'why' => '"Would" digunakan untuk menceritakan kebiasaan atau aksi yang berulang di masa lalu, seperti membuat roti setiap hari Minggu.',
                    ],
                    [
                        'q' => 'I ___ my hair cut at a new salon yesterday.',
                        'options' => ['had' => true, 'did' => false, 'made' => false, 'let' => false],
                        'why' => 'Bentuk causative "have something done" (had + objek + past participle) digunakan untuk menyatakan bahwa seseorang meminta orang lain melakukan sesuatu untuknya.',
                    ],
                    [
                        'q' => 'She ___ the mechanic repair her car before the long road trip.',
                        'options' => ['had' => true, 'made' => false, 'let' => false, 'got' => false],
                        'why' => 'Bentuk causative aktif "have someone do something" (had + orang + bare infinitive) digunakan untuk mengatur agar orang lain melakukan sesuatu, berbeda dengan "make" (memaksa) atau "let" (mengizinkan).',
                    ],
                    [
                        'q' => 'Read the passage and answer the question.

                "Over the past decade, urban beekeeping has grown significantly in cities around the world. Many residents, who once considered beekeeping an activity reserved for the countryside, have started placing hives on rooftops and in small gardens. Although some city dwellers were initially concerned about safety, most beekeepers report that urban bees are surprisingly gentle, largely because they have easy access to a variety of flowers in parks and gardens. Local governments in several cities have even introduced programs to support these initiatives, offering training and reduced fees for equipment. Nevertheless, experts warn that if the number of hives continues to increase without proper regulation, competition for food sources could threaten wild pollinator populations. For this reason, many beekeeping associations now encourage members to plant pollinator-friendly gardens alongside their hives, rather than simply installing more colonies."

                According to the passage, why do many urban beekeepers believe their bees are gentle?',
                        'options' => [
                            'Because the bees have easy access to a variety of flowers in parks and gardens.' => true,
                            'Because they are a special breed developed specifically for cities.' => false,
                            'Because local governments require beekeepers to use gentle bees.' => false,
                            'Because rooftop hives keep bees isolated from people.' => false,
                        ],
                        'why' => 'Sesuai teks, lebah kota dianggap jinak karena mereka memiliki akses mudah ke berbagai macam bunga di taman dan kebun.',
                    ],
                    [
                        'q' => 'Based on the passage about urban beekeeping, what warning do experts give about its rapid growth?',
                        'options' => [
                            'Too many hives without proper regulation could threaten wild pollinator populations.' => true,
                            'Urban bees will eventually become extinct within a decade.' => false,
                            'City governments plan to ban beekeeping entirely in the near future.' => false,
                            'Beekeeping equipment will become too expensive for most residents.' => false,
                        ],
                        'why' => 'Teks menyatakan bahwa jika jumlah sarang terus bertambah tanpa regulasi yang tepat, persaingan sumber makanan dapat mengancam populasi penyerbuk liar (wild pollinators).',
                    ],
                    [
                        'q' => 'According to the passage, what do beekeeping associations now encourage their members to do?',
                        'options' => [
                            'Plant pollinator-friendly gardens instead of simply installing more hives.' => true,
                            'Move their hives from cities back to the countryside.' => false,
                            'Reduce the number of bees kept in each individual hive.' => false,
                            'Sell their honey exclusively at local farmers markets.' => false,
                        ],
                        'why' => 'Menurut teks, asosiasi peternak lebah kini menganjurkan anggotanya menanam kebun yang ramah bagi penyerbuk (pollinator-friendly gardens), bukan sekadar menambah jumlah koloni.',
                    ],
                    [
                        'q' => 'By the time the guests arrived, we ___ already decorated the entire living room.',
                        'options' => ['had' => true, 'have' => false, 'were' => false, 'did' => false],
                        'why' => 'Past perfect ("had" + V3) digunakan karena tindakan mendekorasi ruangan selesai sebelum tamu datang, yaitu sebelum kejadian lampau lainnya.',
                    ],
                    [
                        'q' => 'He couldn\'t get into the house because he ___ his keys at the office.',
                        'options' => ['had left' => true, 'left' => false, 'has left' => false, 'was leaving' => false],
                        'why' => 'Past perfect ("had left") menunjukkan bahwa tindakan meninggalkan kunci terjadi sebelum kejadian tidak bisa masuk rumah.',
                    ],
                    [
                        'q' => 'When the firefighters arrived, the fire ___ most of the building.',
                        'options' => ['had already destroyed' => true, 'already destroyed' => false, 'has already destroyed' => false, 'was already destroying' => false],
                        'why' => 'Kebakaran sudah menghancurkan sebagian besar gedung sebelum petugas pemadam tiba, sehingga digunakan past perfect.',
                    ],
                    [
                        'q' => 'If I ___ you, I would apologize to her immediately.',
                        'options' => ['were' => true, 'was' => false, 'am' => false, 'will be' => false],
                        'why' => 'Dalam conditional tipe 2, ungkapan "if I were you" selalu menggunakan "were" untuk semua subjek saat memberi saran atas situasi andaian.',
                    ],
                    [
                        'q' => 'What would you do if you ___ a large sum of money on the street?',
                        'options' => ['found' => true, 'find' => false, 'will find' => false, 'had found' => false],
                        'why' => 'Conditional tipe 2 menggunakan simple past ("found") pada klausa if untuk situasi hipotetis di masa sekarang atau masa depan.',
                    ],
                    [
                        'q' => 'If the company ___ better working conditions, more employees would stay.',
                        'options' => ['offered' => true, 'offers' => false, 'will offer' => false, 'had offered' => false],
                        'why' => 'Klausa if pada conditional tipe 2 memakai simple past ("offered") untuk menyatakan andaian yang tidak nyata saat ini.',
                    ],
                    [
                        'q' => 'The contract ___ by both parties before the meeting ends.',
                        'options' => ['must be signed' => true, 'must sign' => false, 'must have signed' => false, 'must being signed' => false],
                        'why' => '"must be signed" adalah modal passive (modal + be + V3) karena kontrak dikenai tindakan menandatangani, bukan pelaku tindakan.',
                    ],
                    [
                        'q' => 'The old factory ___ due to safety concerns; it will reopen next year.',
                        'options' => ['has been closed' => true, 'has closed' => false, 'was close' => false, 'is closing' => false],
                        'why' => 'Present perfect passive ("has been closed") dipakai karena pabrik ditutup dan hasilnya masih relevan hingga sekarang.',
                    ],
                    [
                        'q' => 'By the time you receive this letter, the decision ___ .',
                        'options' => ['will have been made' => true, 'will make' => false, 'will have made' => false, 'will be made' => false],
                        'why' => 'Future perfect passive digunakan karena keputusan akan sudah dibuat sebelum surat ini diterima.',
                    ],
                    [
                        'q' => '"I will call you tomorrow," she told him. She told him that she ___ him the next day.',
                        'options' => ['would call' => true, 'will call' => false, 'called' => false, 'calls' => false],
                        'why' => 'Dalam reported speech, "will" berubah menjadi "would" (backshift tense) saat kalimat langsung diubah menjadi tidak langsung.',
                    ],
                    [
                        'q' => '"Where did you buy that jacket?" he asked me. He asked me ___ .',
                        'options' => ['where I had bought that jacket' => true, 'where did I buy that jacket' => false, 'where I bought that jacket' => false, 'where had I bought that jacket' => false],
                        'why' => 'Reported question menggunakan susunan kalimat pernyataan (subject + verb) dan past simple berubah menjadi past perfect.',
                    ],
                    [
                        'q' => '"Don\'t touch the paintings," the guide warned us. The guide warned us ___ .',
                        'options' => ['not to touch the paintings' => true, 'to not touch the paintings' => false, 'don\'t touch the paintings' => false, 'that we don\'t touch the paintings' => false],
                        'why' => 'Perintah negatif dalam reported speech menggunakan pola "not + to + infinitive".',
                    ],
                    [
                        'q' => '"I have never been to Japan," my colleague admitted. My colleague admitted that ___ .',
                        'options' => ['she had never been to Japan' => true, 'she has never been to Japan' => false, 'she never been to Japan' => false, 'she was never been to Japan' => false],
                        'why' => 'Present perfect ("have never been") berubah menjadi past perfect ("had never been") dalam reported speech.',
                    ],
                    [
                        'q' => 'The streets are wet and there are puddles everywhere. It ___ during the night.',
                        'options' => ['must have rained' => true, 'must rain' => false, 'should have rained' => false, 'might rain' => false],
                        'why' => '"must have" + V3 digunakan untuk deduksi kuat tentang kejadian di masa lalu berdasarkan bukti yang terlihat sekarang.',
                    ],
                    [
                        'q' => 'Her calendar shows a meeting until 3 p.m., so she ___ in a meeting right now.',
                        'options' => ['must be' => true, 'might be' => false, 'could be' => false, 'would be' => false],
                        'why' => '"must be" menunjukkan deduksi logis yang kuat karena ada bukti jelas (jadwal di kalender), bukan sekadar kemungkinan.',
                    ],
                    [
                        'q' => 'He only started the report an hour ago, so he ___ finished it already.',
                        'options' => ['can\'t have' => true, 'mustn\'t have' => false, 'shouldn\'t have' => false, 'isn\'t able to have' => false],
                        'why' => '"can\'t have" + V3 digunakan untuk menyatakan bahwa sesuatu hampir pasti tidak mungkin terjadi di masa lalu.',
                    ],
                    [
                        'q' => 'Look at the thick smoke coming from the kitchen window. Something ___ .',
                        'options' => ['must be burning' => true, 'might burnt' => false, 'could burning' => false, 'should burn' => false],
                        'why' => '"must be" + V-ing digunakan untuk deduksi kuat tentang sesuatu yang sedang terjadi saat ini berdasarkan bukti.',
                    ],
                    [
                        'q' => 'The new manager, ___ studied engineering in Germany, has completely reorganized our department.',
                        'options' => ['who' => true, 'which' => false, 'that' => false, 'whose' => false],
                        'why' => '"who" digunakan untuk menerangkan orang (the new manager) dalam non-defining relative clause yang dipisahkan koma.',
                    ],
                    [
                        'q' => 'The Amazon rainforest, ___ covers much of Brazil, is often called the lungs of the earth.',
                        'options' => ['which' => true, 'who' => false, 'that' => false, 'whose' => false],
                        'why' => '"which" digunakan untuk menerangkan benda atau tempat (rainforest) dalam non-defining relative clause.',
                    ],
                    [
                        'q' => 'Mr. Tanaka, ___ daughter studies at the same university as me, invited us to dinner.',
                        'options' => ['whose' => true, 'who' => false, 'which' => false, 'that' => false],
                        'why' => '"whose" menunjukkan kepemilikan (daughter adalah milik Mr. Tanaka) dalam non-defining relative clause.',
                    ],
                    [
                        'q' => 'My father ___ own a small bookstore near the train station before he retired.',
                        'options' => ['used to' => true, 'would' => false, 'use to' => false, 'was used to' => false],
                        'why' => '"used to" dipakai untuk kondisi/keadaan (kepemilikan toko) di masa lalu yang sudah tidak berlaku; "would" tidak bisa dipakai untuk state verb.',
                    ],
                    [
                        'q' => 'As children, my sister and I ___ spend hours building sandcastles at the beach whenever we visited our grandparents.',
                        'options' => ['would' => true, 'was used to' => false, 'use to' => false, 'get used to' => false],
                        'why' => '"would" digunakan untuk menyatakan kebiasaan berulang (aksi) di masa lalu.',
                    ],
                    [
                        'q' => 'I ___ hate vegetables when I was young; nowadays I actually enjoy them.',
                        'options' => ['used to' => true, 'would' => false, 'use to' => false, 'am used to' => false],
                        'why' => '"hate" adalah state verb, sehingga hanya "used to" yang tepat untuk menyatakan keadaan di masa lalu, bukan "would".',
                    ],
                    [
                        'q' => 'The company ___ its offices renovated last spring.',
                        'options' => ['had' => true, 'made' => false, 'let' => false, 'did' => false],
                        'why' => 'Causative form "have something done" (had + objek + V3) digunakan saat seseorang menyuruh orang lain melakukan sesuatu untuknya.',
                    ],
                    [
                        'q' => 'They ___ their kitchen renovated by a local contractor last month.',
                        'options' => ['had' => true, 'did' => false, 'made' => false, 'let' => false],
                        'why' => 'Causative form "had + objek + V3 + by + agent" menunjukkan bahwa mereka menyuruh kontraktor merenovasi dapur mereka.',
                    ],
                    [
                        'q' => 'We\'re going to ___ our house painted before the guests arrive.',
                        'options' => ['get' => true, 'make' => false, 'let' => false, 'do' => false],
                        'why' => '"get something done" adalah bentuk causative lain yang berarti menyuruh orang lain melakukan sesuatu untuk kita.',
                    ],
                    [
                        'q' => 'Read the passage and answer the question.
                "Over the last several years, podcast listening has become an increasingly popular way for people to consume news, stories, and educational content. Unlike traditional radio, podcasts allow listeners to choose exactly what they want to hear and to pause or replay episodes whenever they like. Many commuters, who once relied solely on music during their journeys, now spend their travel time listening to interviews or true-crime series instead. Advertisers have taken notice of this shift, and podcast advertising revenue has increased dramatically in recent years. However, some critics argue that the sheer number of new shows being released each week makes it difficult for smaller, independent podcasters to reach an audience. As a result, several podcast platforms have introduced recommendation algorithms designed to help listeners discover niche content that matches their interests, rather than only promoting the most popular shows."
                According to the passage, how has podcast advertising revenue changed in recent years?',
                        'options' => ['It has increased dramatically' => true, 'It has decreased slightly' => false, 'It has remained the same' => false, 'It has disappeared completely' => false],
                        'why' => 'Paragraf menyatakan secara eksplisit bahwa "podcast advertising revenue has increased dramatically in recent years".',
                    ],
                    [
                        'q' => 'Read the passage and answer the question.
                "Over the last several years, podcast listening has become an increasingly popular way for people to consume news, stories, and educational content. Unlike traditional radio, podcasts allow listeners to choose exactly what they want to hear and to pause or replay episodes whenever they like. Many commuters, who once relied solely on music during their journeys, now spend their travel time listening to interviews or true-crime series instead. Advertisers have taken notice of this shift, and podcast advertising revenue has increased dramatically in recent years. However, some critics argue that the sheer number of new shows being released each week makes it difficult for smaller, independent podcasters to reach an audience. As a result, several podcast platforms have introduced recommendation algorithms designed to help listeners discover niche content that matches their interests, rather than only promoting the most popular shows."
                According to the passage, why do some critics feel it is difficult for smaller podcasters to succeed?',
                        'options' => ['because too many new shows are released every week' => true, 'because advertisers refuse to work with them' => false, 'because platforms have banned independent podcasters' => false, 'because listeners prefer traditional radio' => false],
                        'why' => 'Paragraf menyebutkan bahwa "the sheer number of new shows being released each week makes it difficult for smaller, independent podcasters to reach an audience".',
                    ],
                    [
                        'q' => 'Read the passage and answer the question.
                "In many major cities, building owners have begun installing green roofs, which are rooftops covered with soil and vegetation instead of traditional materials like asphalt or gravel. These green roofs help to reduce indoor temperatures during the summer, meaning that buildings often require less energy for air conditioning. In addition, the plants absorb rainwater, which reduces the risk of flooding during heavy storms in cities with old drainage systems. Some architects had initially worried that the extra weight of soil and plants would damage building structures, but modern green roofs are designed with lightweight materials that address this concern. Local governments in cities such as Toronto and Singapore have introduced regulations requiring new commercial buildings to include a certain percentage of green roof space. Despite the clear environmental benefits, the upfront installation cost remains a significant barrier for many smaller property owners."
                According to the passage, what concern did some architects initially have about green roofs?',
                        'options' => ['that the weight of soil and plants would damage the building structure' => true, 'that plants would attract too many insects' => false, 'that green roofs would be too expensive to water' => false, 'that green roofs would block sunlight from neighboring buildings' => false],
                        'why' => 'Paragraf menyatakan "Some architects had initially worried that the extra weight of soil and plants would damage building structures".',
                    ],
                    [
                        'q' => 'Read the passage and answer the question.
                "In many major cities, building owners have begun installing green roofs, which are rooftops covered with soil and vegetation instead of traditional materials like asphalt or gravel. These green roofs help to reduce indoor temperatures during the summer, meaning that buildings often require less energy for air conditioning. In addition, the plants absorb rainwater, which reduces the risk of flooding during heavy storms in cities with old drainage systems. Some architects had initially worried that the extra weight of soil and plants would damage building structures, but modern green roofs are designed with lightweight materials that address this concern. Local governments in cities such as Toronto and Singapore have introduced regulations requiring new commercial buildings to include a certain percentage of green roof space. Despite the clear environmental benefits, the upfront installation cost remains a significant barrier for many smaller property owners."
                According to the passage, what remains a major obstacle preventing smaller property owners from installing green roofs?',
                        'options' => ['the high upfront installation cost' => true, 'a lack of available rooftop space' => false, 'government regulations banning them' => false, 'the extra weight of soil and plants' => false],
                        'why' => 'Paragraf menyatakan "the upfront installation cost remains a significant barrier for many smaller property owners".',
                    ],
                ],
                category: 'test',
                timeLimitSeconds: 30 * 60,
                description: '20 soal berfokus pada level Upper-Intermediate saja, dengan waktu pengerjaan 30 menit — cocok untuk latihan tes bertahap sebelum mencoba tes penempatan lengkap.',
            );

            $this->create(
                null,
                'Tes Level Advanced',
                'hard',
                'Advanced',
                [
                    [
                        'q' => 'If she ___ the warning signs earlier, the project might not have collapsed so spectacularly.',
                        'options' => ['had heeded' => true, 'heeded' => false, 'would heed' => false, 'has heeded' => false],
                        'why' => 'Ini adalah third conditional (pengandaian tipe 3) untuk situasi hipotetis di masa lalu yang sudah tidak bisa diubah: klausa if menggunakan past perfect (had heeded), dan akibatnya menggunakan "might/would have + past participle".',
                    ],
                    [
                        'q' => 'Had the merger ___ before the recession hit, the company would have survived.',
                        'options' => ['been finalised' => true, 'finalised' => false, 'finalise' => false, 'being finalised' => false],
                        'why' => 'Ini adalah inverted third conditional (tanpa "if"): "Had the merger been finalised..." = "If the merger had been finalised...". Karena "merger" tidak bisa "finalise" dirinya sendiri, dibutuhkan bentuk pasif "been finalised".',
                    ],
                    [
                        'q' => "If he hadn't smoked so heavily in his twenties, he ___ in far better health today.",
                        'options' => ['would be' => true, 'would have been' => false, 'will be' => false, 'had been' => false],
                        'why' => 'Ini adalah mixed conditional: kondisi di masa lalu (hadn\'t smoked) menghasilkan akibat di masa sekarang. Kata "today" menandakan hasil sekarang, sehingga digunakan "would be", bukan "would have been" yang merujuk hasil masa lalu.',
                    ],
                    [
                        'q' => "If she weren't so fiercely independent, she ___ for help when the project first ran into trouble.",
                        'options' => ['would have asked' => true, 'would ask' => false, 'will ask' => false, 'had asked' => false],
                        'why' => 'Ini mixed conditional kebalikannya: kondisi tidak nyata di masa sekarang ("weren\'t") menghasilkan akibat hipotetis di masa lalu. Frasa "when the project first ran into trouble" menunjukkan waktu lampau, sehingga akibatnya memakai "would have asked".',
                    ],
                    [
                        'q' => 'The committee insisted that the report ___ before the board convened.',
                        'options' => ['be revised' => true, 'is revised' => false, 'was revised' => false, 'will be revised' => false],
                        'why' => 'Setelah verba yang menyatakan tuntutan/desakan seperti "insist that", digunakan mandative subjunctive: bentuk dasar verba (be revised) tanpa memperhatikan subjek atau waktu kalimat utama.',
                    ],
                    [
                        'q' => '___ I in your position, I would resign immediately rather than face the tribunal.',
                        'options' => ['Were' => true, 'Was' => false, 'Am' => false, 'Be' => false],
                        'why' => 'Ini inversi formal dari subjunctive "If I were in your position...": "Were I in your position..." Bentuk "were" digunakan untuk semua subjek dalam subjunctive formal, bukan "was".',
                    ],
                    [
                        'q' => '___ had the results been published than the methodology was called into question.',
                        'options' => ['No sooner' => true, 'Not until' => false, 'Hardly' => false, 'Scarcely' => false],
                        'why' => '"No sooner...than" adalah pasangan tetap untuk inversi yang menyatakan dua kejadian yang terjadi hampir bersamaan. "Hardly" dan "Scarcely" berpasangan dengan "when", bukan "than", sehingga tidak cocok di sini.',
                    ],
                    [
                        'q' => '___ did the researchers overlook a critical confound, but they also misreported their statistical significance levels.',
                        'options' => ['Not only' => true, 'Not even' => false, 'Not that' => false, 'Never' => false],
                        'why' => '"Not only...but also" adalah struktur inversi baku untuk menekankan dua fakta negatif/mengejutkan sekaligus. Pilihan lain tidak berpasangan secara gramatikal dengan "but also" pada klausa berikutnya.',
                    ],
                    [
                        'q' => '___ any discrepancies arise during the audit, the finance team must notify the board within 24 hours.',
                        'options' => ['Should' => true, 'Would' => false, 'Were' => false, 'Had' => false],
                        'why' => '"Should" di awal kalimat adalah inversi formal dari "If any discrepancies should arise...", digunakan dalam register formal/legal untuk kondisi yang dianggap kurang mungkin terjadi.',
                    ],
                    [
                        'q' => "___ the ambiguity of the contract's wording, not the intentions of either party, that ultimately led to litigation.",
                        'options' => ['It was' => true, 'It is' => false, 'There was' => false, 'This was' => false],
                        'why' => 'Ini adalah it-cleft sentence dengan pola "It was X, not Y, that..." untuk memberi penekanan pada X. Karena akibatnya ("led to litigation") berbentuk lampau, digunakan "It was", bukan "It is".',
                    ],
                    [
                        'q' => '___ the committee failed to anticipate was the sheer scale of public backlash.',
                        'options' => ['What' => true, 'That' => false, 'Which' => false, 'It' => false],
                        'why' => 'Ini adalah what-cleft (pseudo-cleft sentence) dengan pola "What + klausa + be + unsur yang ditekankan", digunakan untuk memberi penekanan pada informasi di akhir kalimat.',
                    ],
                    [
                        'q' => 'The ancient manuscript ___ to have been forged, though no conclusive evidence has emerged.',
                        'options' => ['is believed' => true, 'believes' => false, 'is believing' => false, 'has believed' => false],
                        'why' => 'Ini struktur passive reporting verb: "is believed to have been forged" digunakan untuk melaporkan opini umum secara objektif tanpa menyebut sumbernya, khas register akademik/formal.',
                    ],
                    [
                        'q' => 'By the time inspectors arrived, the evidence ___, making prosecution all but impossible.',
                        'options' => ['had already been destroyed' => true, 'had already destroyed' => false, 'was already destroying' => false, 'already destroyed' => false],
                        'why' => 'Karena "evidence" adalah objek yang dikenai tindakan (bukan pelaku), dibutuhkan bentuk pasif. "By the time inspectors arrived" menandakan peristiwa yang selesai sebelum peristiwa lampau lain, sehingga dipakai past perfect passive: "had already been destroyed".',
                    ],
                    [
                        'q' => 'The data ___ suggest a correlation, although the sample size precludes any definitive conclusion.',
                        'options' => ['would seem to' => true, 'seems to' => false, 'must' => false, 'clearly' => false],
                        'why' => '"Would seem to" adalah bahasa hedging (melunakkan klaim) yang lazim dalam register akademik ketika bukti belum kuat. "Seems to" juga salah secara tata bahasa karena "data" bersifat jamak (harus "seem"), sedangkan "must" dan "clearly" justru terlalu tegas, bertentangan dengan nada hati-hati kalimat.',
                    ],
                    [
                        'q' => 'Which sentence best demonstrates academic nominalization of the idea: "They analysed the data carefully, and this led them to reject the hypothesis."',
                        'options' => [
                            'The careful analysis of the data led to the rejection of the hypothesis.' => true,
                            'They carefully analysed the data and rejected the hypothesis.' => false,
                            'The data was analysed by them carefully, rejecting the hypothesis.' => false,
                            'Analysing carefully, the hypothesis was rejected by the data.' => false,
                        ],
                        'why' => 'Nominalisasi mengubah verba (analysed, reject) menjadi kata benda abstrak (analysis, rejection), gaya khas tulisan akademik yang formal dan padat. Hanya opsi pertama melakukan transformasi ini secara konsisten dan gramatikal.',
                    ],
                    [
                        'q' => 'He believed the theory was flawed, and ___.',
                        'options' => ['so did his colleagues' => true, 'so his colleagues did' => false, 'also his colleagues did' => false, 'neither did his colleagues' => false],
                        'why' => 'Untuk menyatakan kesepakatan positif dengan pernyataan sebelumnya, digunakan pola substitusi "so + auxiliary + subject" (inversi setelah "so"). "Neither" salah karena bermakna negatif, bertentangan dengan konteks.',
                    ],
                    [
                        'q' => "The council hadn't anticipated the backlash, and ___ had the advisory board.",
                        'options' => ['neither' => true, 'either' => false, 'so' => false, 'also' => false],
                        'why' => 'Untuk menyatakan kesepakatan negatif ("sama-sama tidak"), digunakan pola "neither + auxiliary + subject". "Either" hanya dipakai di akhir kalimat negatif tanpa inversi, sedangkan "so" digunakan untuk kesepakatan positif.',
                    ],
                    [
                        'q' => 'Read the passage, then answer: "The Sapir–Whorf hypothesis, first articulated in the early twentieth century, posits that the structure of a language shapes, or at the very least influences, the cognitive processes of its speakers. In its strongest form—linguistic determinism—the hypothesis contends that thought is wholly constrained by linguistic categories, a claim that has been largely discredited by subsequent research. A weaker, more defensible version, often termed linguistic relativity, suggests merely that language exerts a subtle influence on perception and memory rather than dictating the very boundaries of thought. Empirical support for this softer stance has accumulated steadily: studies of speakers whose languages encode spatial relations in absolute terms (north/south) rather than relative ones (left/right) have revealed marked differences in navigational ability. Critics, however, caution against overstating these findings, noting that a correlation between linguistic structure and cognitive tendency does not necessarily establish causation. Were researchers able to isolate language entirely from the cultural practices with which it is inextricably intertwined, the debate might be settled more conclusively. Until such methodological rigor is achieved, the extent to which language moulds thought remains a matter of considerable scholarly contention." According to the passage, what is the key difference between linguistic determinism and linguistic relativity?',
                        'options' => [
                            'Determinism claims language completely controls thought, while relativity claims language merely influences it.' => true,
                            'Determinism has strong empirical support, while relativity has none.' => false,
                            'Determinism concerns spatial language, while relativity concerns temporal language.' => false,
                            'Determinism is a modern theory, while relativity is the original twentieth-century version.' => false,
                        ],
                        'why' => 'Paragraf menyatakan determinism berpendapat pikiran "wholly constrained" oleh kategori bahasa, sedangkan relativity hanya menyatakan bahasa "exerts a subtle influence" — perbedaan tingkat kekuatan klaim, bukan topik atau bukti empirisnya.',
                    ],
                    [
                        'q' => 'Read the passage, then answer: "The Sapir–Whorf hypothesis, first articulated in the early twentieth century, posits that the structure of a language shapes, or at the very least influences, the cognitive processes of its speakers. In its strongest form—linguistic determinism—the hypothesis contends that thought is wholly constrained by linguistic categories, a claim that has been largely discredited by subsequent research. A weaker, more defensible version, often termed linguistic relativity, suggests merely that language exerts a subtle influence on perception and memory rather than dictating the very boundaries of thought. Empirical support for this softer stance has accumulated steadily: studies of speakers whose languages encode spatial relations in absolute terms (north/south) rather than relative ones (left/right) have revealed marked differences in navigational ability. Critics, however, caution against overstating these findings, noting that a correlation between linguistic structure and cognitive tendency does not necessarily establish causation. Were researchers able to isolate language entirely from the cultural practices with which it is inextricably intertwined, the debate might be settled more conclusively. Until such methodological rigor is achieved, the extent to which language moulds thought remains a matter of considerable scholarly contention." Why do critics urge caution regarding the navigational studies mentioned in the passage?',
                        'options' => [
                            'Because a correlation between language and cognition does not prove that language causes the cognitive difference.' => true,
                            'Because the studies were conducted before the twentieth century and are outdated.' => false,
                            'Because the researchers only studied speakers of relative spatial languages.' => false,
                            'Because linguistic determinism has already been proven false by the same data.' => false,
                        ],
                        'why' => 'Kalimat kritik dalam paragraf secara eksplisit menyebut bahwa "a correlation...does not necessarily establish causation" — inti argumennya adalah korelasi bukan berarti sebab-akibat.',
                    ],
                    [
                        'q' => 'Read the passage, then answer: "The Sapir–Whorf hypothesis, first articulated in the early twentieth century, posits that the structure of a language shapes, or at the very least influences, the cognitive processes of its speakers. In its strongest form—linguistic determinism—the hypothesis contends that thought is wholly constrained by linguistic categories, a claim that has been largely discredited by subsequent research. A weaker, more defensible version, often termed linguistic relativity, suggests merely that language exerts a subtle influence on perception and memory rather than dictating the very boundaries of thought. Empirical support for this softer stance has accumulated steadily: studies of speakers whose languages encode spatial relations in absolute terms (north/south) rather than relative ones (left/right) have revealed marked differences in navigational ability. Critics, however, caution against overstating these findings, noting that a correlation between linguistic structure and cognitive tendency does not necessarily establish causation. Were researchers able to isolate language entirely from the cultural practices with which it is inextricably intertwined, the debate might be settled more conclusively. Until such methodological rigor is achieved, the extent to which language moulds thought remains a matter of considerable scholarly contention." What does the inverted structure "Were researchers able to isolate language entirely from the cultural practices..." imply about the current state of the debate?',
                        'options' => [
                            'It expresses a hypothetical, currently unfulfilled condition — such isolation has not yet been achieved, so the debate remains unresolved.' => true,
                            'It states a fact that researchers have already successfully isolated language from culture.' => false,
                            'It predicts that researchers will never attempt such an isolation.' => false,
                            'It confirms that culture has no real connection to language.' => false,
                        ],
                        'why' => '"Were researchers able to..." adalah inversi dari second conditional ("If researchers were able to..."), menyatakan kondisi hipotetis yang belum terwujud saat ini — sejalan dengan kalimat penutup bahwa perdebatan masih "a matter of considerable scholarly contention".',
                    ],
                    [
                        'q' => 'If the negotiators ___ more flexible, the deal would have been signed on time.',
                        'options' => ['had been' => true, 'were' => false, 'would have been' => false, 'have been' => false],
                        'why' => '"had been" (past perfect) digunakan pada klausa if untuk conditional type 3, karena klausa utama sudah menggunakan "would have been signed" yang menunjukkan pengandaian masa lalu yang tidak terjadi.',
                    ],
                    [
                        'q' => 'Had the pilot ___ the mechanical fault, the emergency landing could have been avoided.',
                        'options' => ['noticed' => true, 'notice' => false, 'been noticed' => false, 'have noticed' => false],
                        'why' => 'Ini adalah inversi dari "If the pilot had noticed...". Setelah "Had + subjek" pada conditional type 3, kata kerja berikutnya harus dalam bentuk past participle, yaitu "noticed".',
                    ],
                    [
                        'q' => 'If she ___ harder during her twenties, she would be a senior partner by now.',
                        'options' => ['had worked' => true, 'worked' => false, 'would have worked' => false, 'has worked' => false],
                        'why' => 'Ini adalah mixed conditional: klausa if merujuk kondisi masa lalu (past perfect "had worked"), sementara akibatnya dirasakan di masa sekarang ("would be... by now").',
                    ],
                    [
                        'q' => 'If he ___ so risk-averse by nature, he would have accepted the offer when it was first made.',
                        'options' => ['weren\'t' => true, 'hadn\'t been' => false, 'wasn\'t' => false, 'isn\'t' => false],
                        'why' => 'Ini mixed conditional lain: sifat umum/permanen seseorang dinyatakan dengan present ("weren\'t"), sedangkan akibatnya terjadi di masa lalu ("would have accepted").',
                    ],
                    [
                        'q' => 'The regulator demanded that the firm ___ all trading activity pending investigation.',
                        'options' => ['cease' => true, 'ceases' => false, 'ceased' => false, 'will cease' => false],
                        'why' => 'Setelah verba yang menuntut seperti "demand that", digunakan subjunctive mood, yaitu bentuk dasar kata kerja tanpa -s ("cease"), terlepas dari subjeknya.',
                    ],
                    [
                        'q' => 'It is imperative that every witness ___ present at the hearing.',
                        'options' => ['be' => true, 'is' => false, 'was' => false, 'will be' => false],
                        'why' => 'Setelah ungkapan formal seperti "it is imperative/essential/vital that", digunakan subjunctive "be", bukan bentuk konjugasi biasa.',
                    ],
                    [
                        'q' => 'Not only ___ change her testimony, but she also refused to answer further questions.',
                        'options' => ['did the witness' => true, 'the witness did' => false, 'the witness has' => false, 'has the witness' => false],
                        'why' => 'Frasa negatif "Not only" di awal kalimat memicu inversion (auxiliary + subjek: "did the witness"), dan karena kata kerja utamanya "change" (bentuk dasar), auxiliary yang cocok adalah "did".',
                    ],
                    [
                        'q' => 'Seldom ___ such a compelling piece of evidence been presented in court.',
                        'options' => ['has' => true, 'have' => false, 'did' => false, 'was' => false],
                        'why' => 'Adverbia negatif "Seldom" di awal kalimat memicu inversion. Subjek "such a compelling piece of evidence" tunggal sehingga menggunakan "has" (present perfect passive: has been presented).',
                    ],
                    [
                        'q' => 'No sooner ___ the announcement been made than shares plummeted.',
                        'options' => ['had' => true, 'has' => false, 'did' => false, 'was' => false],
                        'why' => '"No sooner... than" menggunakan past perfect dengan inversion ("had the announcement been made"), konsisten dengan klausa utama yang berbentuk past simple ("shares plummeted").',
                    ],
                    [
                        'q' => 'Under no circumstances ___ the confidential files be shared with third parties.',
                        'options' => ['should' => true, 'are' => false, 'is' => false, 'were' => false],
                        'why' => 'Frasa negatif "Under no circumstances" di awal kalimat memicu inversion modal + subjek + bare infinitive ("should the files be shared"). Hanya modal yang gramatikal di posisi ini.',
                    ],
                    [
                        'q' => '___ the flawed methodology, not the sample size, that undermined the study\'s credibility.',
                        'options' => ['It was' => true, 'It is' => false, 'That was' => false, 'This was' => false],
                        'why' => 'Ini adalah it-cleft sentence ("It was X that...") yang digunakan untuk menekankan elemen tertentu (flawed methodology) sebagai penyebab utama, membedakannya dari faktor lain (sample size).',
                    ],
                    [
                        'q' => '___ is a complete overhaul of the regulatory framework, not minor amendments.',
                        'options' => ['What the crisis demands' => true, 'That the crisis demands' => false, 'The crisis demands what' => false, 'What does the crisis demand' => false],
                        'why' => 'Ini adalah what-cleft sentence ("What + klausa + is + penekanan") yang digunakan untuk menonjolkan objek yang ditekankan, yaitu "a complete overhaul of the regulatory framework".',
                    ],
                    [
                        'q' => 'What surprised the panel most ___ the candidate\'s audacity in challenging the premise of the question.',
                        'options' => ['was' => true, 'is' => false, 'were' => false, 'has been' => false],
                        'why' => 'Subjek "What surprised the panel most" bersifat tunggal dan peristiwanya terjadi di masa lalu, sehingga kata kerja yang tepat adalah "was" (past, singular).',
                    ],
                    [
                        'q' => 'The suspect ___ to have fled the country before the warrant was issued.',
                        'options' => ['is believed' => true, 'believes' => false, 'is believing' => false, 'believed' => false],
                        'why' => 'Struktur passive reporting "is believed to have + V3" digunakan untuk melaporkan opini umum tentang peristiwa yang terjadi sebelum waktu tertentu di masa lalu.',
                    ],
                    [
                        'q' => 'By the time auditors arrived, crucial documents ___ already shredded.',
                        'options' => ['had been' => true, 'were' => false, 'have been' => false, 'had' => false],
                        'why' => 'Past perfect passive ("had been shredded") digunakan karena tindakan penghancuran dokumen sudah selesai sebelum tindakan lain di masa lalu (kedatangan auditor).',
                    ],
                    [
                        'q' => 'The findings ___ indicate a possible link between the two variables, although further research is needed.',
                        'options' => ['appear to' => true, 'clearly' => false, 'definitely' => false, 'undoubtedly' => false],
                        'why' => 'Register akademik formal cenderung menggunakan hedging ("appear to") untuk menghindari klaim yang terlalu pasti, sejalan dengan frasa "although further research is needed".',
                    ],
                    [
                        'q' => 'Which sentence best demonstrates academic nominalization of the idea: "The company expanded rapidly, and this surprised investors."?',
                        'options' => ['The company\'s rapid expansion surprised investors.' => true, 'The company expanded rapidly, surprising investors.' => false, 'Investors were surprised because the company expanded rapidly.' => false, 'Rapidly, the company expanded and surprised investors.' => false],
                        'why' => 'Nominalization mengubah klausa verba ("expanded rapidly") menjadi frasa nomina ("rapid expansion") sebagai subjek kalimat, ciri khas register akademik formal.',
                    ],
                    [
                        'q' => 'It ___ be argued that the policy\'s long-term benefits outweigh its short-term costs.',
                        'options' => ['could' => true, 'must' => false, 'will' => false, 'shall' => false],
                        'why' => 'Modal "could" digunakan sebagai hedging untuk menyampaikan argumen secara hati-hati dan tidak absolut, umum dalam tulisan akademik formal.',
                    ],
                    [
                        'q' => 'The senior researcher rejected the proposal outright, and so ___ her colleagues.',
                        'options' => ['did' => true, 'have' => false, 'were' => false, 'had' => false],
                        'why' => 'Struktur "so + auxiliary + subjek" menyatakan kesamaan tindakan. Karena kata kerja utama "rejected" berbentuk past simple, auxiliary yang tepat adalah "did".',
                    ],
                    [
                        'q' => 'Some analysts predicted a swift recovery; others, a prolonged recession. What grammatical process allows the omission of "predicted" in the second clause?',
                        'options' => ['Gapping (ellipsis of a repeated verb in coordinate clauses)' => true, 'Cleft sentence formation' => false, 'Subjunctive mood' => false, 'Passive transformation' => false],
                        'why' => 'Gapping adalah proses elipsis yang menghilangkan kata kerja yang berulang pada klausa koordinatif kedua ("others, a prolonged recession") untuk menghindari repetisi.',
                    ],
                    [
                        'q' => 'Read the passage, then answer: "Confirmation bias, a term coined by psychologist Peter Wason in the 1960s, refers to the tendency to seek out, interpret, and recall information in a manner that confirms one\'s pre-existing beliefs while disregarding evidence to the contrary. This cognitive tendency is not merely an occasional lapse in judgement; rather, it appears to be a pervasive feature of human reasoning, observable across domains as varied as political affiliation, medical diagnosis, and scientific research. Were individuals to evaluate evidence with perfect objectivity, confirmation bias would scarcely warrant scholarly attention. Yet studies consistently demonstrate that even trained professionals, whose careers depend on impartial judgement, are far from immune. Physicians, for instance, have been shown to give disproportionate weight to symptoms that align with an initial diagnosis, sometimes overlooking indicators that point to an alternative condition. Some researchers contend that confirmation bias evolved as an adaptive mechanism, allowing our ancestors to make rapid decisions under conditions of uncertainty rather than deliberating exhaustively over every scrap of evidence. Others argue that this evolutionary explanation, however plausible, does little to mitigate the very real costs the bias imposes in contexts, such as courtrooms and hospitals, where accuracy is paramount. Whatever its origins, the consensus among cognitive scientists is that awareness of the bias, though necessary, is far from sufficient to overcome it." According to the passage, what does the inverted conditional "Were individuals to evaluate evidence with perfect objectivity..." suggest?',
                        'options' => ['That people do not, in reality, evaluate evidence with complete objectivity' => true, 'That most people already evaluate evidence objectively' => false, 'That objectivity is easy to achieve with training' => false, 'That the author is uncertain whether objectivity exists' => false],
                        'why' => 'Kalimat pengandaian formal ini (bentuk lain dari "If individuals evaluated...") menyiratkan bahwa objektivitas sempurna sebenarnya tidak terjadi, sehingga bias konfirmasi menjadi topik penting untuk dikaji.',
                    ],
                    [
                        'q' => 'Read the passage, then answer: "Confirmation bias, a term coined by psychologist Peter Wason in the 1960s, refers to the tendency to seek out, interpret, and recall information in a manner that confirms one\'s pre-existing beliefs while disregarding evidence to the contrary. This cognitive tendency is not merely an occasional lapse in judgement; rather, it appears to be a pervasive feature of human reasoning, observable across domains as varied as political affiliation, medical diagnosis, and scientific research. Were individuals to evaluate evidence with perfect objectivity, confirmation bias would scarcely warrant scholarly attention. Yet studies consistently demonstrate that even trained professionals, whose careers depend on impartial judgement, are far from immune. Physicians, for instance, have been shown to give disproportionate weight to symptoms that align with an initial diagnosis, sometimes overlooking indicators that point to an alternative condition. Some researchers contend that confirmation bias evolved as an adaptive mechanism, allowing our ancestors to make rapid decisions under conditions of uncertainty rather than deliberating exhaustively over every scrap of evidence. Others argue that this evolutionary explanation, however plausible, does little to mitigate the very real costs the bias imposes in contexts, such as courtrooms and hospitals, where accuracy is paramount. Whatever its origins, the consensus among cognitive scientists is that awareness of the bias, though necessary, is far from sufficient to overcome it." Why does the passage mention physicians specifically?',
                        'options' => ['To illustrate that even trained professionals whose work demands objectivity are susceptible to confirmation bias' => true, 'To argue that physicians are more biased than other professionals' => false, 'To suggest that medical diagnosis is inherently unreliable' => false, 'To prove that confirmation bias only affects untrained individuals' => false],
                        'why' => 'Contoh dokter digunakan untuk menunjukkan bahwa bias konfirmasi bahkan memengaruhi profesional terlatih yang seharusnya objektif, memperkuat klaim bahwa bias ini bersifat pervasif.',
                    ],
                    [
                        'q' => 'Read the passage, then answer: "Confirmation bias, a term coined by psychologist Peter Wason in the 1960s, refers to the tendency to seek out, interpret, and recall information in a manner that confirms one\'s pre-existing beliefs while disregarding evidence to the contrary. This cognitive tendency is not merely an occasional lapse in judgement; rather, it appears to be a pervasive feature of human reasoning, observable across domains as varied as political affiliation, medical diagnosis, and scientific research. Were individuals to evaluate evidence with perfect objectivity, confirmation bias would scarcely warrant scholarly attention. Yet studies consistently demonstrate that even trained professionals, whose careers depend on impartial judgement, are far from immune. Physicians, for instance, have been shown to give disproportionate weight to symptoms that align with an initial diagnosis, sometimes overlooking indicators that point to an alternative condition. Some researchers contend that confirmation bias evolved as an adaptive mechanism, allowing our ancestors to make rapid decisions under conditions of uncertainty rather than deliberating exhaustively over every scrap of evidence. Others argue that this evolutionary explanation, however plausible, does little to mitigate the very real costs the bias imposes in contexts, such as courtrooms and hospitals, where accuracy is paramount. Whatever its origins, the consensus among cognitive scientists is that awareness of the bias, though necessary, is far from sufficient to overcome it." What is the relationship between the two viewpoints presented about the evolutionary origin of confirmation bias?',
                        'options' => ['One view sees the bias as an adaptive survival mechanism, while the other emphasizes its costs in high-stakes modern contexts regardless of its origin' => true, 'Both views agree that the bias is entirely beneficial in modern contexts' => false, 'The second view refutes the existence of confirmation bias altogether' => false, 'One view is presented as scientifically proven and the other as pure speculation' => false],
                        'why' => 'Paragraf menyajikan dua sudut pandang yang saling melengkapi: satu menjelaskan asal-usul evolusioner bias, satu lagi menyoroti dampak buruknya di konteks modern seperti pengadilan dan rumah sakit, terlepas dari asal-usulnya.',
                    ],
                    [
                        'q' => 'Read the passage, then answer: "Confirmation bias, a term coined by psychologist Peter Wason in the 1960s, refers to the tendency to seek out, interpret, and recall information in a manner that confirms one\'s pre-existing beliefs while disregarding evidence to the contrary. This cognitive tendency is not merely an occasional lapse in judgement; rather, it appears to be a pervasive feature of human reasoning, observable across domains as varied as political affiliation, medical diagnosis, and scientific research. Were individuals to evaluate evidence with perfect objectivity, confirmation bias would scarcely warrant scholarly attention. Yet studies consistently demonstrate that even trained professionals, whose careers depend on impartial judgement, are far from immune. Physicians, for instance, have been shown to give disproportionate weight to symptoms that align with an initial diagnosis, sometimes overlooking indicators that point to an alternative condition. Some researchers contend that confirmation bias evolved as an adaptive mechanism, allowing our ancestors to make rapid decisions under conditions of uncertainty rather than deliberating exhaustively over every scrap of evidence. Others argue that this evolutionary explanation, however plausible, does little to mitigate the very real costs the bias imposes in contexts, such as courtrooms and hospitals, where accuracy is paramount. Whatever its origins, the consensus among cognitive scientists is that awareness of the bias, though necessary, is far from sufficient to overcome it." Based on the final sentence, what is the author\'s conclusion about overcoming confirmation bias?',
                        'options' => ['Being aware of the bias is necessary but not enough on its own to eliminate its effects' => true, 'Awareness alone is sufficient to eliminate the bias completely' => false, 'Confirmation bias cannot be studied scientifically' => false, 'Cognitive scientists disagree entirely on how the bias originates' => false],
                        'why' => 'Kalimat terakhir secara eksplisit menyatakan bahwa kesadaran akan bias ini "necessary" tetapi "far from sufficient" untuk mengatasinya sepenuhnya.',
                    ],
                    [
                        'q' => 'Read the passage, then answer: "Carbon pricing, whether implemented as a tax or a cap-and-trade scheme, is predicated on a deceptively simple premise: that assigning a monetary cost to greenhouse gas emissions will incentivize firms and consumers to curb their carbon footprint. Proponents argue that, were the price set sufficiently high, market forces alone would drive the transition to cleaner technologies far more efficiently than prescriptive regulation ever could. Critics, however, contend that this elegant theory frequently founders on political realities: prices set too low to be politically palatable fail to alter behaviour meaningfully, while prices set high enough to matter tend to provoke public backlash, particularly among lower-income households for whom energy costs constitute a larger share of expenditure. It is this distributional concern, rather than any doubt about the underlying economics, that has proven the single greatest obstacle to widespread adoption. Several jurisdictions have attempted to address the issue by redistributing carbon revenue directly to citizens as a dividend, a policy that has, in some cases, bolstered public support considerably. Nevertheless, the extent to which carbon pricing alone, absent complementary measures such as subsidies for renewable infrastructure, can achieve the emissions reductions required to meet international climate targets remains a matter of vigorous debate among economists." According to the passage, what is the main obstacle to widespread adoption of carbon pricing?',
                        'options' => ['Concerns about the unequal impact of higher energy costs on lower-income households' => true, 'Doubts among economists about whether the underlying theory is sound' => false, 'A lack of any jurisdictions willing to try cap-and-trade schemes' => false, 'The absence of any method to redistribute carbon revenue' => false],
                        'why' => 'Kalimat "It is this distributional concern... that has proven the single greatest obstacle" (cleft sentence) menegaskan bahwa kekhawatiran soal dampak tidak merata pada rumah tangga berpenghasilan rendah adalah hambatan utama, bukan keraguan ekonomi.',
                    ],
                    [
                        'q' => 'Read the passage, then answer: "Carbon pricing, whether implemented as a tax or a cap-and-trade scheme, is predicated on a deceptively simple premise: that assigning a monetary cost to greenhouse gas emissions will incentivize firms and consumers to curb their carbon footprint. Proponents argue that, were the price set sufficiently high, market forces alone would drive the transition to cleaner technologies far more efficiently than prescriptive regulation ever could. Critics, however, contend that this elegant theory frequently founders on political realities: prices set too low to be politically palatable fail to alter behaviour meaningfully, while prices set high enough to matter tend to provoke public backlash, particularly among lower-income households for whom energy costs constitute a larger share of expenditure. It is this distributional concern, rather than any doubt about the underlying economics, that has proven the single greatest obstacle to widespread adoption. Several jurisdictions have attempted to address the issue by redistributing carbon revenue directly to citizens as a dividend, a policy that has, in some cases, bolstered public support considerably. Nevertheless, the extent to which carbon pricing alone, absent complementary measures such as subsidies for renewable infrastructure, can achieve the emissions reductions required to meet international climate targets remains a matter of vigorous debate among economists." What grammatical function does the cleft structure "It is this distributional concern... that has proven..." serve in this context?',
                        'options' => ['It emphasizes that the distributional concern, rather than doubts about economic theory, is the primary obstacle' => true, 'It expresses a hypothetical situation that never occurred' => false, 'It signals uncertainty about which factor is the obstacle' => false, 'It reports what another author claimed without endorsing it' => false],
                        'why' => 'Struktur cleft "It is X that..." digunakan untuk memberi penekanan khusus pada elemen tertentu (distributional concern) sebagai jawaban atas pertanyaan "apa hambatan utamanya?", membedakannya secara tegas dari faktor lain seperti keraguan teori ekonomi.',
                    ],
                    [
                        'q' => 'Read the passage, then answer: "Carbon pricing, whether implemented as a tax or a cap-and-trade scheme, is predicated on a deceptively simple premise: that assigning a monetary cost to greenhouse gas emissions will incentivize firms and consumers to curb their carbon footprint. Proponents argue that, were the price set sufficiently high, market forces alone would drive the transition to cleaner technologies far more efficiently than prescriptive regulation ever could. Critics, however, contend that this elegant theory frequently founders on political realities: prices set too low to be politically palatable fail to alter behaviour meaningfully, while prices set high enough to matter tend to provoke public backlash, particularly among lower-income households for whom energy costs constitute a larger share of expenditure. It is this distributional concern, rather than any doubt about the underlying economics, that has proven the single greatest obstacle to widespread adoption. Several jurisdictions have attempted to address the issue by redistributing carbon revenue directly to citizens as a dividend, a policy that has, in some cases, bolstered public support considerably. Nevertheless, the extent to which carbon pricing alone, absent complementary measures such as subsidies for renewable infrastructure, can achieve the emissions reductions required to meet international climate targets remains a matter of vigorous debate among economists." What does the passage suggest about carbon dividends?',
                        'options' => ['Redistributing carbon revenue to citizens has, in some cases, increased public support for carbon pricing' => true, 'Carbon dividends have been rejected in every jurisdiction that attempted them' => false, 'Carbon dividends eliminate the need for any other climate policy' => false, 'Economists universally agree that dividends solve the distributional problem entirely' => false],
                        'why' => 'Teks menyatakan kebijakan dividen karbon "has, in some cases, bolstered public support considerably", namun tidak mengklaim bahwa itu menyelesaikan masalah sepenuhnya atau diterima secara universal.',
                    ],
                    [
                        'q' => 'Read the passage, then answer: "For much of the twentieth century, neuroscientists operated under the assumption that the adult brain, once fully developed, was essentially fixed, its neural architecture incapable of significant reorganization beyond childhood. Not until the latter decades of the century did a substantial body of evidence emerge to challenge this assumption, giving rise to the concept now known as neuroplasticity: the brain\'s capacity to reorganize itself by forming new neural connections throughout life. Had this discovery been made earlier, entire fields of rehabilitative medicine might have developed along markedly different lines. Stroke rehabilitation, for example, now routinely incorporates intensive, repetitive exercises predicated on the principle that undamaged regions of the brain can, to some extent, assume functions previously performed by damaged areas. It would be a mistake, however, to assume that plasticity renders the adult brain infinitely malleable; the degree and speed of reorganization diminish considerably with age, and certain types of neural damage remain, as yet, irreversible. Nonetheless, the discovery has had a transformative effect on clinical practice, shifting the prevailing outlook from one of resigned pessimism to cautious optimism regarding recovery from brain injury." What does the inverted structure "Not until the latter decades of the century did a substantial body of evidence emerge..." emphasize?',
                        'options' => ['That evidence challenging the fixed-brain assumption only appeared relatively late in the twentieth century' => true, 'That evidence against the fixed-brain assumption existed from the very beginning of the century' => false, 'That no evidence for neuroplasticity has ever been found' => false, 'That the fixed-brain assumption was proven correct' => false],
                        'why' => 'Struktur inversi "Not until... did..." menekankan bahwa bukti substansial baru muncul belakangan di abad ke-20, bukan sejak awal abad tersebut.',
                    ],
                    [
                        'q' => 'Read the passage, then answer: "For much of the twentieth century, neuroscientists operated under the assumption that the adult brain, once fully developed, was essentially fixed, its neural architecture incapable of significant reorganization beyond childhood. Not until the latter decades of the century did a substantial body of evidence emerge to challenge this assumption, giving rise to the concept now known as neuroplasticity: the brain\'s capacity to reorganize itself by forming new neural connections throughout life. Had this discovery been made earlier, entire fields of rehabilitative medicine might have developed along markedly different lines. Stroke rehabilitation, for example, now routinely incorporates intensive, repetitive exercises predicated on the principle that undamaged regions of the brain can, to some extent, assume functions previously performed by damaged areas. It would be a mistake, however, to assume that plasticity renders the adult brain infinitely malleable; the degree and speed of reorganization diminish considerably with age, and certain types of neural damage remain, as yet, irreversible. Nonetheless, the discovery has had a transformative effect on clinical practice, shifting the prevailing outlook from one of resigned pessimism to cautious optimism regarding recovery from brain injury." What does the third conditional sentence "Had this discovery been made earlier, entire fields of rehabilitative medicine might have developed along markedly different lines" imply?',
                        'options' => ['The discovery was not made earlier, so rehabilitative medicine developed differently than it might have' => true, 'The discovery was in fact made very early in the century' => false, 'Rehabilitative medicine has not been affected by the discovery at all' => false, 'The discovery will be made at some point in the future' => false],
                        'why' => 'Ini adalah conditional type 3 yang membahas kondisi hipotetis di masa lalu yang tidak terjadi (penemuan tidak dibuat lebih awal), sehingga hasil hipotetisnya (perkembangan berbeda) juga tidak terjadi.',
                    ],
                    [
                        'q' => 'Read the passage, then answer: "For much of the twentieth century, neuroscientists operated under the assumption that the adult brain, once fully developed, was essentially fixed, its neural architecture incapable of significant reorganization beyond childhood. Not until the latter decades of the century did a substantial body of evidence emerge to challenge this assumption, giving rise to the concept now known as neuroplasticity: the brain\'s capacity to reorganize itself by forming new neural connections throughout life. Had this discovery been made earlier, entire fields of rehabilitative medicine might have developed along markedly different lines. Stroke rehabilitation, for example, now routinely incorporates intensive, repetitive exercises predicated on the principle that undamaged regions of the brain can, to some extent, assume functions previously performed by damaged areas. It would be a mistake, however, to assume that plasticity renders the adult brain infinitely malleable; the degree and speed of reorganization diminish considerably with age, and certain types of neural damage remain, as yet, irreversible. Nonetheless, the discovery has had a transformative effect on clinical practice, shifting the prevailing outlook from one of resigned pessimism to cautious optimism regarding recovery from brain injury." According to the passage, what is the author\'s overall stance on the limits of neuroplasticity?',
                        'options' => ['Plasticity is real and clinically significant, but it is not unlimited, as reorganization slows with age and some damage remains irreversible' => true, 'Neuroplasticity has been thoroughly discredited by modern neuroscience' => false, 'The adult brain is infinitely malleable at any age' => false, 'Plasticity only occurs in cases of stroke and no other condition' => false],
                        'why' => 'Penulis mengakui manfaat nyata neuroplastisitas dalam rehabilitasi, tetapi juga menegaskan batasannya: "It would be a mistake... to assume that plasticity renders the adult brain infinitely malleable".',
                    ],
                ],
                category: 'test',
                timeLimitSeconds: 30 * 60,
                description: '20 soal berfokus pada level Advanced saja, dengan waktu pengerjaan 30 menit — cocok untuk latihan tes bertahap sebelum mencoba tes penempatan lengkap.',
            );

            $this->create(
                null,
                'Tes Simulasi TOEFL',
                'hard',
                null,
                [
                    [
                        'q' => 'Which sentence contains a grammatical error?',
                        'options' => [
                            'Despite of the heavy rain, the marathon continued as scheduled.' => true,
                            'Although she was tired, she finished the report.' => false,
                            'The committee approved the proposal after lengthy discussion.' => false,
                            'Neither the manager nor the employees were satisfied with the outcome.' => false,
                        ],
                        'why' => 'Kata "Despite" tidak boleh diikuti "of". Bentuk yang benar adalah "Despite the heavy rain" atau "In spite of the heavy rain".',
                    ],
                    [
                        'q' => '______ the economy improved, unemployment rates remained high.',
                        'options' => [
                            'Although' => true,
                            'Because' => false,
                            'Since' => false,
                            'Due to' => false,
                        ],
                        'why' => 'Kedua klausa menunjukkan pertentangan (ekonomi membaik tetapi pengangguran tetap tinggi), sehingga membutuhkan konjungsi pertentangan "Although".',
                    ],
                    [
                        'q' => 'The experiment failed ______ the researchers followed the protocol precisely.',
                        'options' => [
                            'even though' => true,
                            'so that' => false,
                            'in order that' => false,
                            'as a result' => false,
                        ],
                        'why' => 'Kalimat menunjukkan hasil yang berlawanan dengan harapan (mengikuti protokol tapi tetap gagal), sehingga digunakan "even though" untuk menyatakan kontras.',
                    ],
                    [
                        'q' => 'The professor is known for being knowledgeable, patient, and ______ .',
                        'options' => [
                            'inspiring' => true,
                            'he inspires students' => false,
                            'inspiration' => false,
                            'to inspire' => false,
                        ],
                        'why' => 'Struktur paralel mengharuskan tiga kata sifat sejajar: knowledgeable, patient, dan inspiring.',
                    ],
                    [
                        'q' => 'Which sentence contains a misplaced or dangling modifier?',
                        'options' => [
                            'Walking through the museum, the ancient artifacts fascinated her.' => true,
                            'While walking through the museum, she was fascinated by the ancient artifacts.' => false,
                            'She walked through the museum and admired the ancient artifacts.' => false,
                            'Having finished her tour, she left the museum satisfied.' => false,
                        ],
                        'why' => 'Frasa "Walking through the museum" secara logis harus menerangkan subjek pelaku, bukan "the ancient artifacts", sehingga kalimat ini mengandung dangling modifier.',
                    ],
                    [
                        'q' => 'The scientist ______ discovery revolutionized modern medicine passed away last year.',
                        'options' => [
                            'whose' => true,
                            'who' => false,
                            'which' => false,
                            'that' => false,
                        ],
                        'why' => 'Klausa relatif ini menunjukkan kepemilikan (discovery milik sang ilmuwan), sehingga pronoun relatif yang tepat adalah "whose".',
                    ],
                    [
                        'q' => 'Choose the sentence with correct parallel structure.',
                        'options' => [
                            'She enjoys reading, writing, and painting.' => true,
                            'She enjoys reading, writing, and to paint.' => false,
                            'She enjoys to read, writing, and painting.' => false,
                            'She enjoys reading, to write, and painting.' => false,
                        ],
                        'why' => 'Struktur paralel mengharuskan ketiga kegiatan menggunakan bentuk gerund yang konsisten: reading, writing, painting.',
                    ],
                    [
                        'q' => 'Which sentence is grammatically correct?',
                        'options' => [
                            'The number of students who fail the exam is increasing.' => true,
                            'The number of students who fail the exam are increasing.' => false,
                            'The number of students who fail the exam were increasing.' => false,
                            'The number of students who fail the exam have increased.' => false,
                        ],
                        'why' => 'Subjek utama kalimat adalah "The number", yang merupakan subjek tunggal, sehingga kata kerjanya harus "is increasing".',
                    ],
                    [
                        'q' => 'If the government ______ more funding, the research would have progressed faster.',
                        'options' => [
                            'had provided' => true,
                            'provided' => false,
                            'provides' => false,
                            'will provide' => false,
                        ],
                        'why' => 'Klausa utama menggunakan "would have progressed" (past perfect conditional tipe 3), sehingga klausa if harus menggunakan past perfect "had provided".',
                    ],
                    [
                        'q' => 'This method is far ______ than the traditional approach.',
                        'options' => [
                            'more efficient' => true,
                            'efficient' => false,
                            'most efficient' => false,
                            'efficienter' => false,
                        ],
                        'why' => 'Kata "than" menandakan perbandingan (comparative), dan "efficient" adalah kata sifat panjang sehingga bentuk comparative-nya adalah "more efficient".',
                    ],
                    [
                        'q' => 'The new policy ______ by the board of directors next month.',
                        'options' => [
                            'will be reviewed' => true,
                            'will review' => false,
                            'reviews' => false,
                            'is reviewing' => false,
                        ],
                        'why' => 'Kebijakan (the policy) adalah objek yang menerima tindakan tinjauan, sehingga digunakan bentuk pasif masa depan "will be reviewed".',
                    ],
                    [
                        'q' => 'The proposal was rejected not only because of its cost but also ______ its impracticality.',
                        'options' => [
                            'because of' => true,
                            'but' => false,
                            'since' => false,
                            'due' => false,
                        ],
                        'why' => 'Struktur korelatif "not only...but also" harus paralel; karena bagian pertama menggunakan "because of", bagian kedua juga harus "because of".',
                    ],
                    [
                        'q' => 'Which sentence contains a grammatical error?',
                        'options' => [
                            'By the time the guests arrived, she has already finished cooking dinner.' => true,
                            'By the time the guests arrived, she had already finished cooking dinner.' => false,
                            'The train had left before we reached the station.' => false,
                            'He realized that he had forgotten his passport.' => false,
                        ],
                        'why' => 'Karena ada dua peristiwa di masa lalu dan salah satunya terjadi lebih dulu, kalimat harus menggunakan past perfect "had finished", bukan present perfect "has finished".',
                    ],
                    [
                        'q' => 'The teacher asked ______ .',
                        'options' => [
                            'why the results were inconsistent' => true,
                            'why were the results inconsistent' => false,
                            'why the results inconsistent were' => false,
                            'why inconsistent were the results' => false,
                        ],
                        'why' => 'Dalam noun clause (klausa benda) setelah kata tanya seperti "why", urutan kata harus mengikuti pola kalimat pernyataan (subject + verb), bukan pola pertanyaan.',
                    ],
                    [
                        'q' => 'Which sentence contains a misplaced modifier?',
                        'options' => [
                            'Having read the instructions carefully, the exam was easy for him.' => true,
                            'Having read the instructions carefully, he found the exam easy.' => false,
                            'After reading the instructions carefully, he completed the exam easily.' => false,
                            'He read the instructions carefully before starting the exam.' => false,
                        ],
                        'why' => 'Frasa "Having read the instructions carefully" seharusnya menerangkan "he", bukan "the exam", sehingga kalimat ini mengandung dangling modifier.',
                    ],
                    [
                        'q' => "The professor's argument was so cogent that even skeptics were persuaded. What does \"cogent\" most nearly mean in this context?",
                        'options' => [
                            'convincing' => true,
                            'confusing' => false,
                            'lengthy' => false,
                            'controversial' => false,
                        ],
                        'why' => 'Karena argumen tersebut berhasil meyakinkan orang-orang yang skeptis, "cogent" berarti "convincing" (meyakinkan).',
                    ],
                    [
                        'q' => "The company's profits have fluctuated significantly over the past decade. \"Fluctuated\" most nearly means:",
                        'options' => [
                            'varied irregularly' => true,
                            'increased steadily' => false,
                            'remained constant' => false,
                            'declined sharply' => false,
                        ],
                        'why' => '"Fluctuated" berarti naik turun secara tidak teratur, sesuai konteks laba perusahaan yang tidak stabil.',
                    ],
                    [
                        'q' => 'Her meticulous attention to detail made her an excellent editor. "Meticulous" most nearly means:',
                        'options' => [
                            'extremely careful' => true,
                            'very fast' => false,
                            'somewhat careless' => false,
                            'highly creative' => false,
                        ],
                        'why' => '"Meticulous" berarti sangat teliti dan berhati-hati terhadap detail, yang cocok dengan konteks seorang editor yang unggul.',
                    ],
                    [
                        'q' => 'The evidence presented was largely circumstantial rather than direct. "Circumstantial" most nearly means:',
                        'options' => [
                            'based on inference' => true,
                            'based on direct proof' => false,
                            'completely false' => false,
                            'officially confirmed' => false,
                        ],
                        'why' => '"Circumstantial evidence" adalah bukti yang bersifat tidak langsung dan didasarkan pada kesimpulan logis, bukan bukti langsung.',
                    ],
                    [
                        'q' => 'The new regulations aim to mitigate the environmental impact of industrial waste. "Mitigate" most nearly means:',
                        'options' => [
                            'reduce' => true,
                            'increase' => false,
                            'ignore' => false,
                            'measure' => false,
                        ],
                        'why' => '"Mitigate" berarti mengurangi atau meredakan dampak buruk sesuatu, dalam hal ini dampak lingkungan.',
                    ],
                    [
                        'q' => 'Despite the setback, the team remained resilient and completed the project on time. "Resilient" most nearly means:',
                        'options' => [
                            'able to recover quickly' => true,
                            'easily discouraged' => false,
                            'extremely slow' => false,
                            'financially strong' => false,
                        ],
                        'why' => '"Resilient" berarti mampu bangkit kembali dengan cepat setelah mengalami kesulitan.',
                    ],
                    [
                        'q' => "The scientist's hypothesis was later corroborated by independent research. \"Corroborated\" most nearly means:",
                        'options' => [
                            'confirmed' => true,
                            'disproven' => false,
                            'questioned' => false,
                            'ignored' => false,
                        ],
                        'why' => '"Corroborated" berarti dikuatkan atau dibuktikan kebenarannya oleh sumber atau penelitian lain.',
                    ],
                    [
                        'q' => 'The lecture was so esoteric that only specialists could fully understand it. "Esoteric" most nearly means:',
                        'options' => [
                            'understood by few' => true,
                            'widely known' => false,
                            'very simple' => false,
                            'entertaining' => false,
                        ],
                        'why' => '"Esoteric" berarti hanya dapat dipahami oleh kalangan tertentu atau ahli, sesuai konteks kuliah yang hanya dipahami spesialis.',
                    ],
                    [
                        'q' => "The government's austerity measures were deeply unpopular among citizens. \"Austerity\" most nearly means:",
                        'options' => [
                            'strict economizing' => true,
                            'generous spending' => false,
                            'military action' => false,
                            'tax reduction' => false,
                        ],
                        'why' => '"Austerity" mengacu pada kebijakan penghematan ketat, biasanya berupa pemotongan anggaran pemerintah.',
                    ],
                    [
                        'q' => "The author's ambiguous conclusion left readers uncertain about her true stance. \"Ambiguous\" most nearly means:",
                        'options' => [
                            'open to multiple interpretations' => true,
                            'perfectly clear' => false,
                            'highly detailed' => false,
                            'strongly opinionated' => false,
                        ],
                        'why' => '"Ambiguous" berarti dapat ditafsirkan dengan lebih dari satu makna, sehingga pembaca menjadi tidak yakin.',
                    ],
                    [
                        'q' => "Read the passage and answer the question.\n\nOcean acidification occurs when seawater absorbs excess carbon dioxide from the atmosphere, causing a decrease in pH levels. This chemical change reduces the availability of carbonate ions, which many marine organisms, such as corals and shellfish, require to build their calcium carbonate skeletons and shells. As a result, these organisms often develop weaker structures, making them more vulnerable to predation and environmental stress. Scientists warn that if current carbon emission trends continue, ocean pH could drop significantly by the end of the century, threatening entire marine food webs. Some researchers are exploring selective breeding of more resilient coral species as a potential mitigation strategy, though such efforts remain in early experimental stages.\n\nWhat is the main idea of the passage?",
                        'options' => [
                            'Rising CO2 levels are making oceans more acidic, harming marine organisms that depend on carbonate ions.' => true,
                            'Coral reefs are the only ecosystems affected by climate change.' => false,
                            'Scientists have already solved the problem of ocean acidification.' => false,
                            'Shellfish are unaffected by changes in ocean pH.' => false,
                        ],
                        'why' => 'Gagasan utama paragraf ini adalah peningkatan CO2 menyebabkan lautan menjadi lebih asam, yang merugikan organisme laut yang bergantung pada ion karbonat.',
                    ],
                    [
                        'q' => "Read the passage and answer the question.\n\nOcean acidification occurs when seawater absorbs excess carbon dioxide from the atmosphere, causing a decrease in pH levels. This chemical change reduces the availability of carbonate ions, which many marine organisms, such as corals and shellfish, require to build their calcium carbonate skeletons and shells. As a result, these organisms often develop weaker structures, making them more vulnerable to predation and environmental stress. Scientists warn that if current carbon emission trends continue, ocean pH could drop significantly by the end of the century, threatening entire marine food webs. Some researchers are exploring selective breeding of more resilient coral species as a potential mitigation strategy, though such efforts remain in early experimental stages.\n\nAccording to the passage, what do corals and shellfish need to build their skeletons and shells?",
                        'options' => [
                            'carbonate ions' => true,
                            'excess carbon dioxide' => false,
                            'seawater alone' => false,
                            'predators' => false,
                        ],
                        'why' => 'Sesuai teks, karang dan kerang membutuhkan ion karbonat (carbonate ions) untuk membangun kerangka dan cangkang kalsium karbonat mereka.',
                    ],
                    [
                        'q' => "Read the passage and answer the question.\n\nOcean acidification occurs when seawater absorbs excess carbon dioxide from the atmosphere, causing a decrease in pH levels. This chemical change reduces the availability of carbonate ions, which many marine organisms, such as corals and shellfish, require to build their calcium carbonate skeletons and shells. As a result, these organisms often develop weaker structures, making them more vulnerable to predation and environmental stress. Scientists warn that if current carbon emission trends continue, ocean pH could drop significantly by the end of the century, threatening entire marine food webs. Some researchers are exploring selective breeding of more resilient coral species as a potential mitigation strategy, though such efforts remain in early experimental stages.\n\nWhat can be inferred about the current state of selective breeding research on coral?",
                        'options' => [
                            'It is still in the early, experimental phase and not yet a proven solution.' => true,
                            'It has completely stopped ocean acidification.' => false,
                            'It has been abandoned by scientists.' => false,
                            'It is the primary cause of ocean acidification.' => false,
                        ],
                        'why' => 'Teks menyatakan bahwa upaya pembiakan selektif "remain in early experimental stages", artinya penelitian ini masih dalam tahap awal dan belum terbukti menjadi solusi pasti.',
                    ],
                    [
                        'q' => "Read the passage and answer the question.\n\nOcean acidification occurs when seawater absorbs excess carbon dioxide from the atmosphere, causing a decrease in pH levels. This chemical change reduces the availability of carbonate ions, which many marine organisms, such as corals and shellfish, require to build their calcium carbonate skeletons and shells. As a result, these organisms often develop weaker structures, making them more vulnerable to predation and environmental stress. Scientists warn that if current carbon emission trends continue, ocean pH could drop significantly by the end of the century, threatening entire marine food webs. Some researchers are exploring selective breeding of more resilient coral species as a potential mitigation strategy, though such efforts remain in early experimental stages.\n\nIn the passage, the word \"vulnerable\" most nearly means:",
                        'options' => [
                            'susceptible to harm' => true,
                            'completely protected' => false,
                            'extremely strong' => false,
                            'unaffected' => false,
                        ],
                        'why' => '"Vulnerable" berarti rentan atau mudah terkena bahaya, sesuai konteks organisme laut yang strukturnya melemah.',
                    ],
                    [
                        'q' => "Read the passage and answer the question.\n\nOcean acidification occurs when seawater absorbs excess carbon dioxide from the atmosphere, causing a decrease in pH levels. This chemical change reduces the availability of carbonate ions, which many marine organisms, such as corals and shellfish, require to build their calcium carbonate skeletons and shells. As a result, these organisms often develop weaker structures, making them more vulnerable to predation and environmental stress. Scientists warn that if current carbon emission trends continue, ocean pH could drop significantly by the end of the century, threatening entire marine food webs. Some researchers are exploring selective breeding of more resilient coral species as a potential mitigation strategy, though such efforts remain in early experimental stages.\n\nWhat happens to seawater pH when it absorbs excess carbon dioxide?",
                        'options' => [
                            'It decreases.' => true,
                            'It increases.' => false,
                            'It stays exactly the same.' => false,
                            'It becomes neutral.' => false,
                        ],
                        'why' => 'Sesuai teks, penyerapan kelebihan karbon dioksida oleh air laut menyebabkan penurunan tingkat pH ("a decrease in pH levels").',
                    ],
                    [
                        'q' => "Read the passage and answer the question.\n\nThe invention of the printing press by Johannes Gutenberg in the mid-fifteenth century transformed the way information was produced and distributed throughout Europe. Before this innovation, books were copied by hand, a slow and expensive process that limited literacy to a small elite. Gutenberg's use of movable metal type allowed texts to be reproduced quickly and in large quantities, dramatically lowering the cost of books. This increased accessibility contributed to rising literacy rates and the rapid spread of new ideas, including those that fueled the Protestant Reformation. Historians consider the printing press one of the most influential inventions in human history, comparable in impact to the internet in the modern era.\n\nWhat is the main idea of the passage?",
                        'options' => [
                            'The printing press revolutionized the production and spread of information in Europe.' => true,
                            'Gutenberg invented the internet.' => false,
                            'Books were always cheap and widely available before Gutenberg.' => false,
                            'The printing press had little historical significance.' => false,
                        ],
                        'why' => 'Gagasan utama paragraf ini adalah bahwa mesin cetak Gutenberg mengubah cara informasi diproduksi dan disebarkan di Eropa.',
                    ],
                    [
                        'q' => "Read the passage and answer the question.\n\nThe invention of the printing press by Johannes Gutenberg in the mid-fifteenth century transformed the way information was produced and distributed throughout Europe. Before this innovation, books were copied by hand, a slow and expensive process that limited literacy to a small elite. Gutenberg's use of movable metal type allowed texts to be reproduced quickly and in large quantities, dramatically lowering the cost of books. This increased accessibility contributed to rising literacy rates and the rapid spread of new ideas, including those that fueled the Protestant Reformation. Historians consider the printing press one of the most influential inventions in human history, comparable in impact to the internet in the modern era.\n\nBefore the printing press, how were books produced?",
                        'options' => [
                            'They were copied by hand.' => true,
                            'They were printed digitally.' => false,
                            'They were produced using movable type.' => false,
                            'They were mass-produced in factories.' => false,
                        ],
                        'why' => 'Teks menyatakan bahwa sebelum penemuan mesin cetak, buku disalin dengan tangan ("books were copied by hand").',
                    ],
                    [
                        'q' => "Read the passage and answer the question.\n\nThe invention of the printing press by Johannes Gutenberg in the mid-fifteenth century transformed the way information was produced and distributed throughout Europe. Before this innovation, books were copied by hand, a slow and expensive process that limited literacy to a small elite. Gutenberg's use of movable metal type allowed texts to be reproduced quickly and in large quantities, dramatically lowering the cost of books. This increased accessibility contributed to rising literacy rates and the rapid spread of new ideas, including those that fueled the Protestant Reformation. Historians consider the printing press one of the most influential inventions in human history, comparable in impact to the internet in the modern era.\n\nWhat can be inferred about literacy before the printing press was invented?",
                        'options' => [
                            'It was limited mostly to a small elite because books were expensive.' => true,
                            'It was widespread among all social classes.' => false,
                            'It was completely nonexistent.' => false,
                            'It was higher than after the printing press.' => false,
                        ],
                        'why' => 'Teks menyatakan bahwa proses penyalinan buku yang lambat dan mahal membatasi literasi hanya pada kalangan elite kecil, sehingga dapat disimpulkan literasi sebelumnya sangat terbatas.',
                    ],
                    [
                        'q' => "Read the passage and answer the question.\n\nThe invention of the printing press by Johannes Gutenberg in the mid-fifteenth century transformed the way information was produced and distributed throughout Europe. Before this innovation, books were copied by hand, a slow and expensive process that limited literacy to a small elite. Gutenberg's use of movable metal type allowed texts to be reproduced quickly and in large quantities, dramatically lowering the cost of books. This increased accessibility contributed to rising literacy rates and the rapid spread of new ideas, including those that fueled the Protestant Reformation. Historians consider the printing press one of the most influential inventions in human history, comparable in impact to the internet in the modern era.\n\nIn the passage, the word \"fueled\" most nearly means:",
                        'options' => [
                            'stimulated' => true,
                            'extinguished' => false,
                            'delayed' => false,
                            'ignored' => false,
                        ],
                        'why' => '"Fueled" dalam konteks ini berarti mendorong atau memicu, yaitu memicu penyebaran gerakan Reformasi Protestan.',
                    ],
                    [
                        'q' => "Read the passage and answer the question.\n\nThe invention of the printing press by Johannes Gutenberg in the mid-fifteenth century transformed the way information was produced and distributed throughout Europe. Before this innovation, books were copied by hand, a slow and expensive process that limited literacy to a small elite. Gutenberg's use of movable metal type allowed texts to be reproduced quickly and in large quantities, dramatically lowering the cost of books. This increased accessibility contributed to rising literacy rates and the rapid spread of new ideas, including those that fueled the Protestant Reformation. Historians consider the printing press one of the most influential inventions in human history, comparable in impact to the internet in the modern era.\n\nWhat movement is mentioned as having been spread by the printing press?",
                        'options' => [
                            'the Protestant Reformation' => true,
                            'the French Revolution' => false,
                            'the Industrial Revolution' => false,
                            'the Renaissance in Italy' => false,
                        ],
                        'why' => 'Teks secara eksplisit menyebutkan bahwa ide-ide baru yang disebarkan melalui mesin cetak turut memicu Reformasi Protestan.',
                    ],
                    [
                        'q' => "Read the passage and answer the question.\n\nAutonomous vehicles rely on a combination of sensors, cameras, and artificial intelligence algorithms to navigate roads without human intervention. These systems continuously collect data about their surroundings, including the position of other vehicles, pedestrians, and obstacles, in order to make real-time driving decisions. Proponents argue that widespread adoption of self-driving technology could significantly reduce traffic accidents, since human error accounts for the majority of collisions. However, critics point out that current systems still struggle with unpredictable scenarios, such as sudden weather changes or unusual pedestrian behavior. Regulatory frameworks for autonomous vehicles also remain inconsistent across countries, complicating efforts toward widespread deployment.\n\nWhat is the main idea of the passage?",
                        'options' => [
                            'Autonomous vehicles offer potential safety benefits but face significant technical and regulatory challenges.' => true,
                            'Autonomous vehicles have completely eliminated traffic accidents.' => false,
                            'Human drivers are more reliable than autonomous systems in every situation.' => false,
                            'Regulatory frameworks for autonomous vehicles are fully standardized worldwide.' => false,
                        ],
                        'why' => 'Gagasan utama paragraf ini adalah kendaraan otonom menawarkan manfaat keselamatan namun masih menghadapi tantangan teknis dan regulasi yang signifikan.',
                    ],
                    [
                        'q' => "Read the passage and answer the question.\n\nAutonomous vehicles rely on a combination of sensors, cameras, and artificial intelligence algorithms to navigate roads without human intervention. These systems continuously collect data about their surroundings, including the position of other vehicles, pedestrians, and obstacles, in order to make real-time driving decisions. Proponents argue that widespread adoption of self-driving technology could significantly reduce traffic accidents, since human error accounts for the majority of collisions. However, critics point out that current systems still struggle with unpredictable scenarios, such as sudden weather changes or unusual pedestrian behavior. Regulatory frameworks for autonomous vehicles also remain inconsistent across countries, complicating efforts toward widespread deployment.\n\nWhat do autonomous vehicles use to navigate roads?",
                        'options' => [
                            'sensors, cameras, and artificial intelligence algorithms' => true,
                            "only human drivers' input" => false,
                            'satellite maps alone' => false,
                            'manual controls exclusively' => false,
                        ],
                        'why' => 'Teks menyatakan bahwa kendaraan otonom mengandalkan kombinasi sensor, kamera, dan algoritma kecerdasan buatan untuk bernavigasi.',
                    ],
                    [
                        'q' => "Read the passage and answer the question.\n\nAutonomous vehicles rely on a combination of sensors, cameras, and artificial intelligence algorithms to navigate roads without human intervention. These systems continuously collect data about their surroundings, including the position of other vehicles, pedestrians, and obstacles, in order to make real-time driving decisions. Proponents argue that widespread adoption of self-driving technology could significantly reduce traffic accidents, since human error accounts for the majority of collisions. However, critics point out that current systems still struggle with unpredictable scenarios, such as sudden weather changes or unusual pedestrian behavior. Regulatory frameworks for autonomous vehicles also remain inconsistent across countries, complicating efforts toward widespread deployment.\n\nWhat can be inferred about the current limitations of autonomous vehicle technology?",
                        'options' => [
                            'It still has difficulty handling unpredictable situations like sudden weather changes.' => true,
                            'It has no limitations and performs perfectly in all conditions.' => false,
                            'It cannot collect any data about its surroundings.' => false,
                            'It works only in controlled laboratory environments.' => false,
                        ],
                        'why' => 'Teks menyebutkan bahwa sistem saat ini masih kesulitan menghadapi skenario tak terduga seperti perubahan cuaca mendadak, sehingga dapat disimpulkan teknologi ini masih memiliki keterbatasan.',
                    ],
                    [
                        'q' => "Read the passage and answer the question.\n\nAutonomous vehicles rely on a combination of sensors, cameras, and artificial intelligence algorithms to navigate roads without human intervention. These systems continuously collect data about their surroundings, including the position of other vehicles, pedestrians, and obstacles, in order to make real-time driving decisions. Proponents argue that widespread adoption of self-driving technology could significantly reduce traffic accidents, since human error accounts for the majority of collisions. However, critics point out that current systems still struggle with unpredictable scenarios, such as sudden weather changes or unusual pedestrian behavior. Regulatory frameworks for autonomous vehicles also remain inconsistent across countries, complicating efforts toward widespread deployment.\n\nIn the passage, the word \"proponents\" most nearly means:",
                        'options' => [
                            'supporters' => true,
                            'opponents' => false,
                            'regulators' => false,
                            'engineers' => false,
                        ],
                        'why' => '"Proponents" berarti pihak yang mendukung atau membela suatu gagasan, dalam hal ini pendukung teknologi kendaraan otonom.',
                    ],
                    [
                        'q' => "Read the passage and answer the question.\n\nAutonomous vehicles rely on a combination of sensors, cameras, and artificial intelligence algorithms to navigate roads without human intervention. These systems continuously collect data about their surroundings, including the position of other vehicles, pedestrians, and obstacles, in order to make real-time driving decisions. Proponents argue that widespread adoption of self-driving technology could significantly reduce traffic accidents, since human error accounts for the majority of collisions. However, critics point out that current systems still struggle with unpredictable scenarios, such as sudden weather changes or unusual pedestrian behavior. Regulatory frameworks for autonomous vehicles also remain inconsistent across countries, complicating efforts toward widespread deployment.\n\nAccording to the passage, what accounts for the majority of traffic collisions?",
                        'options' => [
                            'human error' => true,
                            'vehicle malfunction' => false,
                            'poor road conditions' => false,
                            'software bugs' => false,
                        ],
                        'why' => 'Teks menyatakan secara eksplisit bahwa kesalahan manusia (human error) menjadi penyebab mayoritas kecelakaan lalu lintas.',
                    ],
                    [
                        'q' => 'Which sentence contains a grammatical error?',
                        'options' => [
                            'Neither of the students have completed the assignment yet.' => true,
                            'She has lived in Jakarta for over a decade.' => false,
                            'The committee will announce its decision next Friday.' => false,
                            'By the time we arrived, the meeting had already started.' => false,
                        ],
                        'why' => '"Neither" adalah subjek tunggal, sehingga kata kerja yang tepat adalah "has", bukan "have" — kalimat ini mengandung kesalahan tata bahasa.',
                    ],
                    [
                        'q' => '______ the results were inconclusive, the researchers decided to publish their findings for peer review.',
                        'options' => [
                            'Although' => true,
                            'Because' => false,
                            'So that' => false,
                            'Unless' => false,
                        ],
                        'why' => '"Although" digunakan untuk menunjukkan pertentangan antara hasil yang tidak meyakinkan dan keputusan untuk tetap mempublikasikan penelitian.',
                    ],
                    [
                        'q' => 'The new curriculum was praised not only for its rigor but also ______ its emphasis on critical thinking.',
                        'options' => [
                            'for' => true,
                            'because of' => false,
                            'due to' => false,
                            'as' => false,
                        ],
                        'why' => 'Struktur korelatif "not only...but also" harus paralel; karena frasa pertama menggunakan "for its rigor", frasa kedua juga harus menggunakan "for".',
                    ],
                    [
                        'q' => 'The keynote speaker was articulate, insightful, and ______ .',
                        'options' => [
                            'persuasive' => true,
                            'persuasion' => false,
                            'to persuade' => false,
                            'persuasively' => false,
                        ],
                        'why' => 'Daftar kata sifat harus paralel secara gramatikal; karena "articulate" dan "insightful" adalah kata sifat, kata berikutnya juga harus berupa kata sifat, yaitu "persuasive".',
                    ],
                    [
                        'q' => 'Which sentence contains a misplaced or dangling modifier?',
                        'options' => [
                            'Walking through the museum, the ancient artifacts amazed the tourists.' => true,
                            'Having reviewed the manuscript twice, the editor still found several errors.' => false,
                            'After finishing the report, she submitted it before the deadline.' => false,
                            'Exhausted from the long flight, the passengers went straight to their hotel.' => false,
                        ],
                        'why' => 'Frasa modifier "Walking through the museum" seharusnya menerangkan orang yang berjalan, bukan "the ancient artifacts", sehingga kalimat ini mengandung dangling modifier.',
                    ],
                    [
                        'q' => 'The engineer ______ design won the international award was invited to speak at the conference.',
                        'options' => [
                            'whose' => true,
                            'who' => false,
                            'which' => false,
                            'who\'s' => false,
                        ],
                        'why' => '"Whose" adalah kata ganti relatif posesif yang menunjukkan bahwa "design" adalah milik "the engineer".',
                    ],
                    [
                        'q' => 'Choose the sentence with correct parallel structure.',
                        'options' => [
                            'The manager asked us to arrive early, to prepare the materials, and to finalize the agenda.' => true,
                            'The manager asked us to arrive early, to prepare the materials, and finalizing the agenda.' => false,
                            'The manager asked us arriving early, preparing the materials, and to finalize the agenda.' => false,
                            'The manager asked us to arrive early, preparing the materials, and finalize the agenda.' => false,
                        ],
                        'why' => 'Struktur paralel mengharuskan ketiga frasa menggunakan bentuk "to + verb" yang konsisten: "to arrive", "to prepare", dan "to finalize".',
                    ],
                    [
                        'q' => 'Which sentence is grammatically correct?',
                        'options' => [
                            'The number of students enrolled in the program has increased this year.' => true,
                            'The amount of students enrolled in the program has increased this year.' => false,
                            'The number of students enrolled in the program have increased this year.' => false,
                            'The amount of students enrolled in the program have increased this year.' => false,
                        ],
                        'why' => '"Number of" digunakan untuk benda yang dapat dihitung (students), dan "the number" berfungsi sebagai subjek tunggal sehingga memerlukan kata kerja tunggal "has".',
                    ],
                    [
                        'q' => 'Smartphones have become so ubiquitous that it is rare to see someone without one in public. "Ubiquitous" most nearly means:',
                        'options' => [
                            'present everywhere' => true,
                            'extremely expensive' => false,
                            'poorly designed' => false,
                            'newly invented' => false,
                        ],
                        'why' => '"Ubiquitous" berarti hadir atau ditemukan di mana-mana, sesuai konteks kalimat bahwa smartphone sangat umum dijumpai.',
                    ],
                    [
                        'q' => 'The researcher insisted that conclusions be based on empirical evidence rather than personal opinion. "Empirical" most nearly means:',
                        'options' => [
                            'based on observation or experiment' => true,
                            'based on tradition' => false,
                            'based on emotion' => false,
                            'based on speculation without testing' => false,
                        ],
                        'why' => '"Empirical" berarti berdasarkan pengamatan atau eksperimen, bukan opini pribadi atau spekulasi.',
                    ],
                    [
                        'q' => 'Climbing the mountain without proper equipment proved to be an arduous task for the inexperienced hikers. "Arduous" most nearly means:',
                        'options' => [
                            'extremely difficult' => true,
                            'quite enjoyable' => false,
                            'financially costly' => false,
                            'briefly accomplished' => false,
                        ],
                        'why' => '"Arduous" berarti sangat sulit atau melelahkan, sesuai konteks mendaki gunung tanpa peralatan yang memadai.',
                    ],
                    [
                        'q' => 'Rather than pursuing an idealistic solution, the manager adopted a more pragmatic approach to solving the budget crisis. "Pragmatic" most nearly means:',
                        'options' => [
                            'practical' => true,
                            'theoretical' => false,
                            'expensive' => false,
                            'traditional' => false,
                        ],
                        'why' => '"Pragmatic" berarti praktis atau realistis, sesuai konteks manajer memilih pendekatan yang bersifat praktis dibanding idealis.',
                    ],
                    [
                        'q' => 'Read the passage and answer the question.
                Dendrochronology, the scientific method of dating tree rings to the exact year in which they formed, has become an invaluable tool for archaeologists studying past civilizations. Because trees add a new growth ring each year, and the width of each ring varies according to climatic conditions such as rainfall and temperature, researchers can match ring patterns from wooden artifacts to a master chronology built from living and ancient trees. This technique allows scientists not only to determine when a structure was built but also to reconstruct historical climate patterns, including periods of drought or unusually wet weather. Unlike radiocarbon dating, which provides only an approximate range of years, dendrochronology can pinpoint the exact calendar year in which a tree was felled. However, the method is limited to regions with distinct seasonal variation, since trees in tropical climates often lack clearly defined annual rings. Archaeologists frequently combine dendrochronology with other dating techniques to build a more complete picture of ancient settlements.
                What is the main idea of the passage?',
                        'options' => [
                            'Dendrochronology is a valuable technique for precisely dating wooden artifacts and reconstructing past climates.' => true,
                            'Radiocarbon dating is more accurate than dendrochronology in all circumstances.' => false,
                            'Tropical climates provide the best conditions for dendrochronology.' => false,
                            'Archaeologists no longer need multiple dating techniques.' => false,
                        ],
                        'why' => 'Paragraf ini secara keseluruhan menjelaskan bagaimana dendrokronologi berguna untuk menentukan usia benda kayu secara presisi sekaligus merekonstruksi iklim masa lalu.',
                    ],
                    [
                        'q' => 'Read the passage and answer the question.
                Dendrochronology, the scientific method of dating tree rings to the exact year in which they formed, has become an invaluable tool for archaeologists studying past civilizations. Because trees add a new growth ring each year, and the width of each ring varies according to climatic conditions such as rainfall and temperature, researchers can match ring patterns from wooden artifacts to a master chronology built from living and ancient trees. This technique allows scientists not only to determine when a structure was built but also to reconstruct historical climate patterns, including periods of drought or unusually wet weather. Unlike radiocarbon dating, which provides only an approximate range of years, dendrochronology can pinpoint the exact calendar year in which a tree was felled. However, the method is limited to regions with distinct seasonal variation, since trees in tropical climates often lack clearly defined annual rings. Archaeologists frequently combine dendrochronology with other dating techniques to build a more complete picture of ancient settlements.
                According to the passage, what advantage does dendrochronology have over radiocarbon dating?',
                        'options' => [
                            'It can identify the exact calendar year a tree was cut down.' => true,
                            'It works equally well in tropical and temperate climates.' => false,
                            'It does not require comparison with a master chronology.' => false,
                            'It is a faster and less expensive method overall.' => false,
                        ],
                        'why' => 'Sesuai paragraf, dendrokronologi dapat menentukan tahun kalender yang tepat, sementara radiocarbon dating hanya memberikan perkiraan rentang tahun.',
                    ],
                    [
                        'q' => 'Read the passage and answer the question.
                Dendrochronology, the scientific method of dating tree rings to the exact year in which they formed, has become an invaluable tool for archaeologists studying past civilizations. Because trees add a new growth ring each year, and the width of each ring varies according to climatic conditions such as rainfall and temperature, researchers can match ring patterns from wooden artifacts to a master chronology built from living and ancient trees. This technique allows scientists not only to determine when a structure was built but also to reconstruct historical climate patterns, including periods of drought or unusually wet weather. Unlike radiocarbon dating, which provides only an approximate range of years, dendrochronology can pinpoint the exact calendar year in which a tree was felled. However, the method is limited to regions with distinct seasonal variation, since trees in tropical climates often lack clearly defined annual rings. Archaeologists frequently combine dendrochronology with other dating techniques to build a more complete picture of ancient settlements.
                What can be inferred about dendrochronology from the passage?',
                        'options' => [
                            'Its effectiveness depends on the presence of clearly defined seasonal growth rings.' => true,
                            'It is the only dating method archaeologists currently use.' => false,
                            'It was developed more recently than radiocarbon dating.' => false,
                            'It cannot be combined with other archaeological dating techniques.' => false,
                        ],
                        'why' => 'Paragraf menyebutkan bahwa metode ini terbatas pada wilayah dengan variasi musim yang jelas, sehingga dapat disimpulkan efektivitasnya bergantung pada keberadaan cincin pertumbuhan musiman yang jelas.',
                    ],
                    [
                        'q' => 'Read the passage and answer the question.
                Dendrochronology, the scientific method of dating tree rings to the exact year in which they formed, has become an invaluable tool for archaeologists studying past civilizations. Because trees add a new growth ring each year, and the width of each ring varies according to climatic conditions such as rainfall and temperature, researchers can match ring patterns from wooden artifacts to a master chronology built from living and ancient trees. This technique allows scientists not only to determine when a structure was built but also to reconstruct historical climate patterns, including periods of drought or unusually wet weather. Unlike radiocarbon dating, which provides only an approximate range of years, dendrochronology can pinpoint the exact calendar year in which a tree was felled. However, the method is limited to regions with distinct seasonal variation, since trees in tropical climates often lack clearly defined annual rings. Archaeologists frequently combine dendrochronology with other dating techniques to build a more complete picture of ancient settlements.
                In the passage, the word "pinpoint" most nearly means:',
                        'options' => [
                            'identify precisely' => true,
                            'estimate roughly' => false,
                            'dispute' => false,
                            'ignore' => false,
                        ],
                        'why' => '"Pinpoint" berarti mengidentifikasi sesuatu secara tepat atau presisi.',
                    ],
                    [
                        'q' => 'Read the passage and answer the question.
                Confirmation bias refers to the tendency of individuals to search for, interpret, and recall information in ways that support their preexisting beliefs while disregarding evidence that contradicts them. Psychologists have demonstrated this phenomenon across contexts ranging from political debates to scientific reasoning, showing that even highly educated individuals are not immune to its effects. One classic study found that when participants were presented with mixed evidence on a controversial issue, they rated evidence supporting their existing viewpoint as more convincing than evidence that challenged it, regardless of the actual quality of the arguments. This bias can have serious consequences in fields such as medicine and law, where professionals may unconsciously overlook data that conflicts with an initial diagnosis or judgment. To counteract confirmation bias, researchers recommend deliberately seeking out opposing viewpoints before reaching a conclusion. Despite growing awareness of this bias, studies suggest that simply knowing about it does little to reduce its influence on everyday judgment.
                What is the main idea of the passage?',
                        'options' => [
                            'Confirmation bias leads people to favor information that supports their existing beliefs, and merely knowing about it is not enough to overcome it.' => true,
                            'Confirmation bias only affects individuals with little formal education.' => false,
                            'Medicine and law are the only fields affected by confirmation bias.' => false,
                            'Awareness of confirmation bias completely eliminates its effects.' => false,
                        ],
                        'why' => 'Paragraf menjelaskan bahwa bias konfirmasi membuat orang lebih memilih informasi yang mendukung keyakinan mereka, dan kesadaran akan bias ini saja tidak cukup untuk mengatasinya.',
                    ],
                    [
                        'q' => 'Read the passage and answer the question.
                Confirmation bias refers to the tendency of individuals to search for, interpret, and recall information in ways that support their preexisting beliefs while disregarding evidence that contradicts them. Psychologists have demonstrated this phenomenon across contexts ranging from political debates to scientific reasoning, showing that even highly educated individuals are not immune to its effects. One classic study found that when participants were presented with mixed evidence on a controversial issue, they rated evidence supporting their existing viewpoint as more convincing than evidence that challenged it, regardless of the actual quality of the arguments. This bias can have serious consequences in fields such as medicine and law, where professionals may unconsciously overlook data that conflicts with an initial diagnosis or judgment. To counteract confirmation bias, researchers recommend deliberately seeking out opposing viewpoints before reaching a conclusion. Despite growing awareness of this bias, studies suggest that simply knowing about it does little to reduce its influence on everyday judgment.
                According to the passage, what did the classic study find?',
                        'options' => [
                            'Participants judged evidence supporting their existing views as more convincing, regardless of the quality of the arguments.' => true,
                            'Participants judged all evidence equally regardless of their prior beliefs.' => false,
                            'Participants changed their opinions immediately after seeing opposing evidence.' => false,
                            'Participants were unable to recall any of the evidence presented to them.' => false,
                        ],
                        'why' => 'Studi klasik yang disebutkan menemukan bahwa partisipan menilai bukti yang mendukung pandangan mereka sebagai lebih meyakinkan, terlepas dari kualitas argumen sebenarnya.',
                    ],
                    [
                        'q' => 'Read the passage and answer the question.
                Confirmation bias refers to the tendency of individuals to search for, interpret, and recall information in ways that support their preexisting beliefs while disregarding evidence that contradicts them. Psychologists have demonstrated this phenomenon across contexts ranging from political debates to scientific reasoning, showing that even highly educated individuals are not immune to its effects. One classic study found that when participants were presented with mixed evidence on a controversial issue, they rated evidence supporting their existing viewpoint as more convincing than evidence that challenged it, regardless of the actual quality of the arguments. This bias can have serious consequences in fields such as medicine and law, where professionals may unconsciously overlook data that conflicts with an initial diagnosis or judgment. To counteract confirmation bias, researchers recommend deliberately seeking out opposing viewpoints before reaching a conclusion. Despite growing awareness of this bias, studies suggest that simply knowing about it does little to reduce its influence on everyday judgment.
                What can be inferred about the effectiveness of simply being aware of confirmation bias?',
                        'options' => [
                            'Awareness alone is generally insufficient to reduce its influence on judgment.' => true,
                            'Awareness alone is sufficient to eliminate the bias completely.' => false,
                            'Awareness makes the bias stronger in most individuals.' => false,
                            'Awareness of the bias has never been studied by researchers.' => false,
                        ],
                        'why' => 'Paragraf menyatakan bahwa sekadar mengetahui tentang bias ini "does little to reduce its influence", sehingga dapat disimpulkan bahwa kesadaran saja tidak cukup untuk mengatasinya.',
                    ],
                    [
                        'q' => 'Read the passage and answer the question.
                Confirmation bias refers to the tendency of individuals to search for, interpret, and recall information in ways that support their preexisting beliefs while disregarding evidence that contradicts them. Psychologists have demonstrated this phenomenon across contexts ranging from political debates to scientific reasoning, showing that even highly educated individuals are not immune to its effects. One classic study found that when participants were presented with mixed evidence on a controversial issue, they rated evidence supporting their existing viewpoint as more convincing than evidence that challenged it, regardless of the actual quality of the arguments. This bias can have serious consequences in fields such as medicine and law, where professionals may unconsciously overlook data that conflicts with an initial diagnosis or judgment. To counteract confirmation bias, researchers recommend deliberately seeking out opposing viewpoints before reaching a conclusion. Despite growing awareness of this bias, studies suggest that simply knowing about it does little to reduce its influence on everyday judgment.
                In the passage, the word "immune" most nearly means:',
                        'options' => [
                            'protected or not susceptible' => true,
                            'grateful' => false,
                            'confused' => false,
                            'determined' => false,
                        ],
                        'why' => '"Immune" berarti terlindungi atau tidak rentan terhadap sesuatu.',
                    ],
                ],
                category: 'test',
                timeLimitSeconds: 60 * 60,
                description: '40 soal simulasi TOEFL (Structure & Written Expression, Vocabulary, Reading Comprehension) dengan waktu 60 menit.',
            );

            $this->create(
                null,
                'Tes Simulasi IELTS',
                'hard',
                null,
                [
                    [
                        'q' => 'In IELTS Writing Task 1 (Academic module), what are candidates required to do?',
                        'options' => [
                            'Describe, summarize, or explain visual data such as a graph, chart, table, or diagram' => true,
                            'Write an essay presenting and justifying a personal opinion' => false,
                            'Write a formal or informal letter responding to a situation' => false,
                            'Describe a personal experience from memory' => false,
                        ],
                        'why' => 'Task 1 Academic meminta kandidat mendeskripsikan data visual (grafik, tabel, diagram) secara objektif, bukan menulis opini atau surat.',
                    ],
                    [
                        'q' => 'What is the minimum recommended word count for IELTS Writing Task 2?',
                        'options' => [
                            '250 words' => true,
                            '150 words' => false,
                            '300 words' => false,
                            '200 words' => false,
                        ],
                        'why' => 'Task 2 mensyaratkan minimal 250 kata; menulis kurang dari itu akan mengurangi skor Task Achievement.',
                    ],
                    [
                        'q' => 'In IELTS Speaking Part 2 (the "long turn"), how much time does a candidate get to prepare notes before speaking?',
                        'options' => [
                            '1 minute' => true,
                            '30 seconds' => false,
                            '2 minutes' => false,
                            'No preparation time is given' => false,
                        ],
                        'why' => 'Kandidat diberi 1 menit untuk membuat catatan menggunakan pensil dan kertas sebelum berbicara selama 1-2 menit.',
                    ],
                    [
                        'q' => 'Which IELTS Listening question type requires candidates to select letters corresponding to correct options from a list of choices?',
                        'options' => [
                            'Multiple choice' => true,
                            'Sentence completion' => false,
                            'Map/plan/diagram labelling' => false,
                            'Form completion' => false,
                        ],
                        'why' => 'Soal multiple choice meminta peserta memilih huruf jawaban yang benar dari beberapa pilihan yang tersedia.',
                    ],
                    [
                        'q' => 'What does IELTS Speaking Part 3 mainly assess, compared to Part 2?',
                        'options' => [
                            'The ability to discuss abstract ideas and issues connected to the Part 2 topic in more depth' => true,
                            'The ability to describe a personal photograph in detail' => false,
                            'The ability to read aloud a prepared script fluently' => false,
                            'The ability to summarize a graph verbally' => false,
                        ],
                        'why' => 'Part 3 berupa diskusi dua arah yang lebih analitis dan abstrak, memperluas topik yang dibahas di Part 2.',
                    ],
                    [
                        'q' => 'In IELTS Writing Task 1 for the General Training module, what must candidates do instead of describing visual data?',
                        'options' => [
                            'Write a letter (formal, semi-formal, or informal) responding to a given situation' => true,
                            'Write a discursive essay presenting two viewpoints' => false,
                            'Describe a bar chart showing statistical trends' => false,
                            'Summarize information from a table' => false,
                        ],
                        'why' => 'Pada modul General Training, Task 1 berupa penulisan surat sesuai situasi yang diberikan, bukan deskripsi data visual.',
                    ],
                    [
                        'q' => 'Which band score descriptor corresponds to "Competent User" on the IELTS 9-band scale?',
                        'options' => [
                            'Band 6' => true,
                            'Band 9' => false,
                            'Band 4' => false,
                            'Band 8' => false,
                        ],
                        'why' => 'Band 6 disebut "Competent User", sementara Band 9 adalah "Expert User", Band 8 "Very Good User", dan Band 4 "Limited User".',
                    ],
                    [
                        'q' => 'In IELTS Listening, why must candidates pay close attention to spelling and capitalization when writing answers?',
                        'options' => [
                            'Because an otherwise correct answer will be marked wrong if spelled incorrectly' => true,
                            'Because the examiner only checks the accent of the speaker' => false,
                            'Because the order in which questions appear determines the score' => false,
                            'Because spelling is not checked at all in Listening' => false,
                        ],
                        'why' => 'Jawaban yang benar secara isi tetap akan disalahkan jika ejaannya salah, sehingga ketelitian ejaan sangat penting.',
                    ],
                    [
                        'q' => 'How many sections does the IELTS Listening test contain?',
                        'options' => [
                            '4' => true,
                            '3' => false,
                            '5' => false,
                            '2' => false,
                        ],
                        'why' => 'IELTS Listening terdiri dari 4 section dengan tingkat kesulitan yang meningkat secara bertahap.',
                    ],
                    [
                        'q' => 'What is the recommended strategy for the IELTS Reading section when time is limited?',
                        'options' => [
                            'Skim the passage first for gist, then scan for specific keywords related to each question' => true,
                            'Read every word carefully from start to finish before answering any question' => false,
                            'Answer the questions in reverse order to save time' => false,
                            'Translate the entire passage into your native language first' => false,
                        ],
                        'why' => 'Skimming dan scanning lebih efisien daripada membaca kata per kata, karena waktu Reading sangat terbatas (60 menit untuk 3 teks).',
                    ],
                    [
                        'q' => 'Which phrase is commonly used to describe a sudden, significant increase in a graph or chart?',
                        'options' => [
                            'A sharp rise' => true,
                            'A slight dip' => false,
                            'A gradual decline' => false,
                            'A plateau' => false,
                        ],
                        'why' => '"A sharp rise" menggambarkan kenaikan yang cepat dan signifikan, cocok untuk mendeskripsikan tren naik tajam.',
                    ],
                    [
                        'q' => 'Which word best describes a trend that remains unchanged over a period of time in a chart?',
                        'options' => [
                            'Plateau / stabilize' => true,
                            'Fluctuate' => false,
                            'Surge' => false,
                            'Plummet' => false,
                        ],
                        'why' => '"Plateau" atau "stabilize" digunakan untuk menggambarkan data yang mendatar/tidak berubah dalam suatu periode.',
                    ],
                    [
                        'q' => 'Which linking word is used to introduce a contrasting idea in academic writing?',
                        'options' => [
                            'Nevertheless' => true,
                            'Furthermore' => false,
                            'In addition' => false,
                            'Similarly' => false,
                        ],
                        'why' => '"Nevertheless" digunakan untuk menyampaikan ide yang berlawanan atau kontras, sedangkan pilihan lain digunakan untuk menambah informasi yang sejalan.',
                    ],
                    [
                        'q' => 'Which phrase is appropriate for introducing a personal opinion in a formal IELTS Task 2 essay?',
                        'options' => [
                            'In my view / It is my belief that' => true,
                            'I reckon' => false,
                            'Everybody knows that' => false,
                            'As far as I am concerned, whatever' => false,
                        ],
                        'why' => 'Frasa "In my view" bersifat formal dan sesuai untuk esai akademik, sementara pilihan lain terlalu informal atau tidak tepat secara register.',
                    ],
                    [
                        'q' => 'What does the term "plummet" mean when describing a trend in a graph?',
                        'options' => [
                            'To decrease very rapidly and suddenly' => true,
                            'To increase steadily over time' => false,
                            'To remain constant without change' => false,
                            'To fluctuate slightly up and down' => false,
                        ],
                        'why' => '"Plummet" berarti penurunan yang sangat tajam dan tiba-tiba, berlawanan makna dengan "soar" atau "surge".',
                    ],
                    [
                        'q' => 'Which word means "an amount that is roughly but not exactly correct," often used when describing data?',
                        'options' => [
                            'Approximately' => true,
                            'Precisely' => false,
                            'Significantly' => false,
                            'Exclusively' => false,
                        ],
                        'why' => '"Approximately" digunakan untuk menyatakan perkiraan angka, bukan angka yang pasti seperti "precisely".',
                    ],
                    [
                        'q' => 'Which of the following is a formal linking phrase used to add supporting evidence to an argument in academic writing?',
                        'options' => [
                            'Moreover / In addition' => true,
                            'By the way' => false,
                            'Anyway' => false,
                            'So yeah' => false,
                        ],
                        'why' => '"Moreover" dan "In addition" adalah linking words formal untuk menambahkan bukti pendukung, sesuai gaya penulisan akademik.',
                    ],
                    [
                        'q' => 'Which verb best completes the sentence: "Unemployment rates ___ dramatically during the recession before recovering slowly."',
                        'options' => [
                            'Soared' => true,
                            'Declined' => false,
                            'Stabilized' => false,
                            'Leveled off' => false,
                        ],
                        'why' => '"Soared" berarti meningkat drastis, sesuai konteks resesi yang menyebabkan pengangguran melonjak sebelum akhirnya pulih.',
                    ],
                    [
                        'q' => 'Which phrase is typically used to concede a counter-argument before presenting one\'s own opinion in a Task 2 essay?',
                        'options' => [
                            'While it is true that...' => true,
                            'Firstly,' => false,
                            'In conclusion,' => false,
                            'For example,' => false,
                        ],
                        'why' => '"While it is true that..." digunakan untuk mengakui sudut pandang lain sebelum menyampaikan pendapat sendiri, sebuah teknik umum di esai argumentatif.',
                    ],
                    [
                        'q' => 'Which word describes data in a chart that changes irregularly, moving up and down repeatedly?',
                        'options' => [
                            'Fluctuate' => true,
                            'Rocket' => false,
                            'Collapse' => false,
                            'Flatten' => false,
                        ],
                        'why' => '"Fluctuate" tepat menggambarkan data yang naik-turun secara tidak teratur, berbeda dengan tren yang konsisten naik atau turun.',
                    ],
                    [
                        'q' => 'Read the passage and answer the question. "Over the past two decades, Southeast Asia has become one of the most popular regions in the world for budget travelers. Countries such as Thailand, Vietnam, and Indonesia offer a combination of affordable accommodation, inexpensive street food, and stunning natural scenery, making them ideal destinations for backpackers on tight budgets. Many travelers report that they can comfortably survive on less than twenty-five US dollars a day, including lodging, meals, and local transport. However, this affordability has led to overcrowding in certain hotspots, such as Bali\'s Kuta Beach and Vietnam\'s Ha Long Bay, prompting some local governments to introduce visitor caps and higher entrance fees. Critics argue that mass tourism, while economically beneficial in the short term, can strain local infrastructure and damage fragile ecosystems if not carefully managed. Sustainable tourism initiatives, including community-based homestays and eco-certified tour operators, are increasingly seen as a way to balance economic growth with environmental protection." What is the main idea of the passage?',
                        'options' => [
                            'Southeast Asia\'s affordability attracts budget travelers, but growing tourism creates challenges that sustainable initiatives aim to address' => true,
                            'Southeast Asia is too expensive for most backpackers to visit comfortably' => false,
                            'Local governments have banned all tourism in Bali and Vietnam' => false,
                            'Street food in Southeast Asia is considered unsafe for tourists' => false,
                        ],
                        'why' => 'Paragraf membahas daya tarik biaya murah Asia Tenggara bagi backpacker, sekaligus tantangan overtourism dan solusi pariwisata berkelanjutan.',
                    ],
                    [
                        'q' => 'Referring to the same passage about budget travel in Southeast Asia: According to the passage, roughly how much can travelers spend per day and still travel comfortably?',
                        'options' => [
                            'Less than twenty-five US dollars a day' => true,
                            'Around fifty US dollars a day' => false,
                            'About one hundred US dollars a day' => false,
                            'Less than ten US dollars a day' => false,
                        ],
                        'why' => 'Teks menyebutkan bahwa banyak wisatawan dapat bertahan dengan nyaman dengan kurang dari dua puluh lima dolar AS per hari.',
                    ],
                    [
                        'q' => 'Referring to the same passage about budget travel in Southeast Asia: Which three countries are specifically mentioned as popular budget travel destinations?',
                        'options' => [
                            'Thailand, Vietnam, and Indonesia' => true,
                            'Thailand, Malaysia, and the Philippines' => false,
                            'Vietnam, Cambodia, and Laos' => false,
                            'Indonesia, Singapore, and Myanmar' => false,
                        ],
                        'why' => 'Teks secara eksplisit menyebutkan Thailand, Vietnam, dan Indonesia sebagai negara tujuan backpacker dengan biaya terjangkau.',
                    ],
                    [
                        'q' => 'Referring to the same passage about budget travel in Southeast Asia: What can be inferred about local governments\' response to overcrowding at tourist hotspots?',
                        'options' => [
                            'They are taking active measures, such as visitor caps and higher fees, due to concerns about infrastructure and the environment' => true,
                            'They are completely ignoring the problem of overcrowding' => false,
                            'They have banned tourism entirely in Bali and Vietnam' => false,
                            'They are lowering entrance fees to attract even more tourists' => false,
                        ],
                        'why' => 'Teks menyatakan pemerintah lokal memperkenalkan batas jumlah pengunjung dan biaya masuk lebih tinggi sebagai respons terhadap overcrowding.',
                    ],
                    [
                        'q' => 'Referring to the same passage about budget travel in Southeast Asia: In the passage, the word "strain" most nearly means:',
                        'options' => [
                            'To put pressure or stress on something' => true,
                            'To strengthen or reinforce something' => false,
                            'To simplify a complicated process' => false,
                            'To completely ignore a problem' => false,
                        ],
                        'why' => 'Dalam konteks "strain local infrastructure", kata "strain" berarti memberi tekanan atau membebani infrastruktur setempat.',
                    ],
                    [
                        'q' => 'Referring to the same passage about budget travel in Southeast Asia: Is the following statement True, False, or Not Given? "Community-based homestays are mentioned as one example of sustainable tourism."',
                        'options' => [
                            'True' => true,
                            'False' => false,
                            'Not Given' => false,
                        ],
                        'why' => 'Teks secara jelas menyebutkan "community-based homestays" sebagai salah satu contoh inisiatif pariwisata berkelanjutan.',
                    ],
                    [
                        'q' => 'Referring to the same passage about budget travel in Southeast Asia: Is the following statement True, False, or Not Given? "All backpackers in Southeast Asia stay exclusively in eco-certified accommodations."',
                        'options' => [
                            'False' => true,
                            'True' => false,
                            'Not Given' => false,
                        ],
                        'why' => 'Teks hanya menyebutkan eco-certified tour operators sebagai salah satu inisiatif, bukan bahwa semua backpacker menginap di akomodasi tersebut, sehingga pernyataan ini salah (terlalu general/absolut).',
                    ],
                    [
                        'q' => 'Read the passage and answer the question. "Scientific research increasingly shows that sleep plays a far more critical role in human health than was once believed. Adults who consistently sleep fewer than six hours per night have been found to have a higher risk of cardiovascular disease, obesity, and impaired cognitive function. During deep sleep stages, the brain clears out metabolic waste products, including proteins linked to neurodegenerative diseases such as Alzheimer\'s. Despite this evidence, surveys suggest that a significant proportion of adults in industrialized countries routinely sleep less than the recommended seven to nine hours, often due to work pressures or excessive screen time before bed. Sleep experts recommend maintaining a consistent sleep schedule, limiting caffeine intake in the afternoon, and reducing exposure to blue light from phones and computers in the hour before bedtime. While occasional sleep deprivation is unlikely to cause lasting harm, chronic sleep loss accumulated over years appears to have measurable long-term consequences for both physical and mental well-being." What is the main idea of the passage?',
                        'options' => [
                            'Chronic lack of sleep has serious long-term health consequences, yet many adults still fail to get enough rest' => true,
                            'Sleep has no real connection to physical health, only to mood' => false,
                            'Everyone in industrialized countries sleeps the recommended amount' => false,
                            'Caffeine is the only factor that affects how much people sleep' => false,
                        ],
                        'why' => 'Paragraf menjelaskan pentingnya tidur bagi kesehatan jangka panjang dan fakta bahwa banyak orang dewasa masih kurang tidur.',
                    ],
                    [
                        'q' => 'Referring to the same passage about sleep and health: According to the passage, what happens in the brain during deep sleep stages?',
                        'options' => [
                            'It clears out metabolic waste products, including proteins linked to neurodegenerative diseases' => true,
                            'It stores long-term memories exclusively' => false,
                            'It increases blood pressure significantly' => false,
                            'It completely shuts down all activity' => false,
                        ],
                        'why' => 'Teks menyebutkan bahwa selama tidur dalam, otak membersihkan produk sisa metabolisme, termasuk protein yang terkait penyakit neurodegeneratif seperti Alzheimer.',
                    ],
                    [
                        'q' => 'Referring to the same passage about sleep and health: How many hours of sleep per night do experts recommend for adults, according to the passage?',
                        'options' => [
                            'Seven to nine hours' => true,
                            'Four to five hours' => false,
                            'Ten to twelve hours' => false,
                            'Six to seven hours' => false,
                        ],
                        'why' => 'Teks menyatakan jumlah tidur yang direkomendasikan adalah tujuh hingga sembilan jam per malam.',
                    ],
                    [
                        'q' => 'Referring to the same passage about sleep and health: What can be inferred about the cause of insufficient sleep among adults in industrialized countries?',
                        'options' => [
                            'It is often linked to work pressures and excessive screen time before bed' => true,
                            'It is mainly caused by underlying medical conditions' => false,
                            'It is caused by taking too many naps during the day' => false,
                            'It is completely unrelated to lifestyle factors' => false,
                        ],
                        'why' => 'Teks menyebutkan tekanan pekerjaan dan penggunaan layar berlebihan sebelum tidur sebagai penyebab kurang tidur pada orang dewasa.',
                    ],
                    [
                        'q' => 'Referring to the same passage about sleep and health: In the passage, the word "chronic" most nearly means:',
                        'options' => [
                            'Persisting or recurring over a long period of time' => true,
                            'Sudden and severe' => false,
                            'Rare and mild' => false,
                            'Completely harmless' => false,
                        ],
                        'why' => '"Chronic sleep loss" berarti kekurangan tidur yang berlangsung terus-menerus dalam jangka panjang, bukan yang terjadi sesekali.',
                    ],
                    [
                        'q' => 'Referring to the same passage about sleep and health: Is the following statement True, False, or Not Given? "Sleeping fewer than six hours a night is linked to a higher risk of obesity."',
                        'options' => [
                            'True' => true,
                            'False' => false,
                            'Not Given' => false,
                        ],
                        'why' => 'Teks secara eksplisit menyatakan bahwa tidur kurang dari enam jam berhubungan dengan risiko obesitas yang lebih tinggi.',
                    ],
                    [
                        'q' => 'Referring to the same passage about sleep and health: Is the following statement True, False, or Not Given? "The passage states that daytime napping fully compensates for lost nighttime sleep."',
                        'options' => [
                            'Not Given' => true,
                            'True' => false,
                            'False' => false,
                        ],
                        'why' => 'Teks tidak pernah membahas tentang tidur siang sebagai pengganti tidur malam, sehingga informasi ini tidak disebutkan (Not Given).',
                    ],
                    [
                        'q' => 'Read the passage and answer the question. "Plastic pollution has become one of the most pressing environmental issues facing coastal nations, with Indonesia ranked among the world\'s largest contributors to marine plastic waste. Much of this pollution originates from mismanaged waste on land, where plastic packaging and single-use items end up in rivers before being carried out to sea. Once in the ocean, plastic debris breaks down into microplastics, tiny particles that have been found in fish, drinking water, and even human blood. In response, several Indonesian cities have introduced bans on single-use plastic bags in retail stores, while grassroots organizations run beach clean-up campaigns and public education programs. Some experts argue that such measures, although valuable, are insufficient without stronger enforcement of waste management regulations and greater investment in recycling infrastructure. Nonetheless, public awareness of the issue has grown considerably over the past decade, encouraging more consumers to switch to reusable bags and containers." What is the main idea of the passage?',
                        'options' => [
                            'Indonesia faces a serious marine plastic pollution problem, and while local efforts exist, further action is still needed' => true,
                            'Indonesia has completely solved its plastic pollution problem through bag bans' => false,
                            'Marine plastic pollution only affects countries outside of Southeast Asia' => false,
                            'Microplastics have never been found in any food or water sources' => false,
                        ],
                        'why' => 'Paragraf menjelaskan besarnya masalah polusi plastik di Indonesia serta upaya yang ada namun masih dianggap belum cukup.',
                    ],
                    [
                        'q' => 'Referring to the same passage about plastic pollution: Where does much of the plastic pollution originate, according to the passage?',
                        'options' => [
                            'From mismanaged waste on land that is carried by rivers into the sea' => true,
                            'Directly from ships dumping waste far out at sea' => false,
                            'From plastic factories located directly on beaches' => false,
                            'Exclusively from waste imported from other countries' => false,
                        ],
                        'why' => 'Teks menyatakan bahwa sebagian besar polusi berasal dari sampah yang tidak terkelola di darat dan terbawa sungai ke laut.',
                    ],
                    [
                        'q' => 'Referring to the same passage about plastic pollution: According to the passage, what have researchers found microplastics present in?',
                        'options' => [
                            'Fish, drinking water, and human blood' => true,
                            'Only in ocean water samples' => false,
                            'Only in soil samples' => false,
                            'Only in air samples' => false,
                        ],
                        'why' => 'Teks menyebutkan mikroplastik ditemukan pada ikan, air minum, bahkan darah manusia.',
                    ],
                    [
                        'q' => 'Referring to the same passage about plastic pollution: What can be inferred about the effectiveness of current measures such as plastic bag bans?',
                        'options' => [
                            'They are helpful but not sufficient on their own to solve the problem' => true,
                            'They have completely eliminated plastic pollution in Indonesia' => false,
                            'They have had absolutely no effect on plastic pollution' => false,
                            'They are strongly opposed by every Indonesian consumer' => false,
                        ],
                        'why' => 'Teks menyebutkan bahwa langkah-langkah tersebut "valuable" namun "insufficient" tanpa penegakan regulasi dan investasi lebih besar dalam daur ulang.',
                    ],
                    [
                        'q' => 'Referring to the same passage about plastic pollution: In the passage, the word "grassroots" most nearly means:',
                        'options' => [
                            'Organized by ordinary local people rather than by government or large institutions' => true,
                            'Related to agriculture and farming' => false,
                            'Funded entirely by the national government' => false,
                            'Operating only in foreign countries' => false,
                        ],
                        'why' => '"Grassroots organizations" merujuk pada organisasi yang digerakkan oleh masyarakat akar rumput, bukan oleh pemerintah atau lembaga besar.',
                    ],
                    [
                        'q' => 'Referring to the same passage about plastic pollution: Is the following statement True, False, or Not Given? "Public awareness of plastic pollution in Indonesia has increased over the past decade."',
                        'options' => [
                            'True' => true,
                            'False' => false,
                            'Not Given' => false,
                        ],
                        'why' => 'Teks menyatakan secara eksplisit bahwa kesadaran publik terhadap isu ini "has grown considerably over the past decade".',
                    ],
                    [
                        'q' => 'In IELTS Writing Task 1 (Academic module), when describing a process diagram, what should candidates primarily focus on?',
                        'options' => ['describing the stages of the process in a logical, chronological order' => true, 'giving a personal opinion about whether the process is efficient' => false, 'comparing the process to a similar one in the candidate\'s own country' => false, 'predicting how the process might change in the future' => false],
                        'why' => 'Untuk diagram proses pada Task 1 Akademik, kandidat harus menjelaskan tahapan proses secara berurutan dan logis, bukan memberikan opini pribadi atau prediksi.',
                    ],
                    [
                        'q' => 'In IELTS Speaking Part 1, what type of topics are candidates typically asked about?',
                        'options' => ['familiar, everyday topics such as hobbies, work, and hometown' => true, 'abstract social issues requiring detailed analysis' => false, 'a two-minute prepared talk on a given topic card' => false, 'hypothetical future scenarios only' => false],
                        'why' => 'Speaking Part 1 berisi pertanyaan tentang topik umum dan familiar seperti hobi, pekerjaan, atau tempat tinggal, bukan isu abstrak atau presentasi panjang.',
                    ],
                    [
                        'q' => 'Which IELTS Listening question type asks candidates to match a list of items (such as speakers, places, or events) to a set of options provided?',
                        'options' => ['Matching' => true, 'Sentence completion' => false, 'Multiple choice with multiple answers' => false, 'Diagram labelling' => false],
                        'why' => 'Tipe soal Matching meminta kandidat mencocokkan daftar item dengan pilihan jawaban yang sudah disediakan.',
                    ],
                    [
                        'q' => 'In the IELTS Writing Task 2 band descriptors, what does "Task Response" primarily evaluate?',
                        'options' => ['how fully and relevantly the essay addresses all parts of the question' => true, 'grammatical accuracy alone' => false, 'the use of advanced vocabulary alone' => false, 'the neatness of the candidate\'s handwriting' => false],
                        'why' => 'Task Response menilai seberapa lengkap dan relevan esai menjawab seluruh bagian dari pertanyaan yang diberikan.',
                    ],
                    [
                        'q' => 'Which reading strategy involves quickly running your eyes over a text to locate a specific piece of information, such as a date or a name?',
                        'options' => ['Scanning' => true, 'Skimming' => false, 'Paraphrasing' => false, 'Summarizing' => false],
                        'why' => 'Scanning digunakan untuk mencari informasi spesifik dengan cepat, berbeda dengan skimming yang bertujuan mendapatkan gambaran umum teks.',
                    ],
                    [
                        'q' => 'Which phrase best describes a gradual, steady increase over time in a line graph?',
                        'options' => ['a steady climb' => true, 'a sharp decline' => false, 'a sudden drop' => false, 'a flat trend' => false],
                        'why' => '"A steady climb" menggambarkan kenaikan yang bertahap dan konsisten, bukan penurunan atau kestabilan.',
                    ],
                    [
                        'q' => 'Which word describes a trend that stops rising or falling and remains constant for a period, often shown as a flat section on a graph?',
                        'options' => ['plateaued' => true, 'fluctuated' => false, 'rocketed' => false, 'dwindled' => false],
                        'why' => '"Plateaued" berarti data menjadi mendatar/stabil setelah sebelumnya naik atau turun.',
                    ],
                    [
                        'q' => 'Which linking word is most appropriate for adding a further supporting point in academic writing?',
                        'options' => ['Furthermore' => true, 'Whereas' => false, 'Although' => false, 'Instead' => false],
                        'why' => '"Furthermore" digunakan untuk menambahkan poin pendukung tambahan, sedangkan kata lain menunjukkan kontras.',
                    ],
                    [
                        'q' => 'Which phrase is commonly used to introduce the concluding paragraph that summarizes an essay\'s main points in IELTS Writing Task 2?',
                        'options' => ['To conclude,' => true, 'For instance,' => false, 'On the other hand,' => false, 'In the first place,' => false],
                        'why' => '"To conclude," secara konvensional digunakan untuk memulai paragraf kesimpulan yang merangkum poin utama esai.',
                    ],
                    [
                        'q' => 'Which word means "to increase suddenly and by a large amount," often used to describe a spike in a chart?',
                        'options' => ['surge' => true, 'stagnate' => false, 'diminish' => false, 'taper' => false],
                        'why' => '"Surge" berarti peningkatan yang tiba-tiba dan besar, berbeda dengan stagnate (tidak berubah) atau diminish (menurun).',
                    ],
                    [
                        'q' => 'Read the passage and answer the question. "In many rural regions of sub-Saharan Africa and South Asia, access to a centralized electricity grid remains limited or unreliable, leaving millions of households without consistent power. Over the past decade, solar mini-grids small, localized networks that generate and distribute electricity from photovoltaic panels have emerged as a practical alternative for these underserved communities. Unlike large-scale power plants, mini-grids can be installed relatively quickly and require far less infrastructure, making them well suited to remote villages that are difficult to connect to a national grid. Studies have shown that households gaining access to mini-grid electricity often see improvements in children\'s study time after dark, small business productivity, and access to refrigeration for medicines. Nevertheless, the upfront capital cost of installing solar panels and battery storage remains a significant barrier, and many projects depend on subsidies or international development funding to become financially viable. Analysts predict that as battery technology continues to improve and costs fall, mini-grids will play an increasingly central role in achieving universal electricity access by the end of the decade." What is the main idea of the passage?',
                        'options' => ['the benefits and challenges of using solar mini-grids to expand rural electricity access' => true, 'the difficulties of extending national power grids to rural areas' => false, 'the environmental impact of manufacturing photovoltaic panels' => false, 'a comparison between the costs of solar and wind energy' => false],
                        'why' => 'Bacaan secara keseluruhan membahas manfaat solar mini-grid bagi akses listrik pedesaan sekaligus tantangan seperti biaya awal yang tinggi.',
                    ],
                    [
                        'q' => 'Referring to the same passage about solar mini-grids in rural areas: According to the passage, what improvements have been observed in households that gain access to mini-grid electricity?',
                        'options' => ['increased children\'s study time after dark, better business productivity, and access to refrigeration for medicines' => true, 'permanent elimination of the need for any future national grid connection' => false, 'a reduction in local internet subscription costs' => false, 'a nationwide decrease in fuel imports' => false],
                        'why' => 'Paragraf tersebut secara eksplisit menyebutkan peningkatan waktu belajar anak, produktivitas usaha kecil, dan akses ke pendingin untuk obat-obatan.',
                    ],
                    [
                        'q' => 'Referring to the same passage about solar mini-grids in rural areas: What can be inferred about why solar mini-grids are particularly suited to remote villages?',
                        'options' => ['they can be installed more quickly and with less infrastructure than connecting to a national grid' => true, 'they generate more total electricity than any other renewable energy source' => false, 'they completely remove the need for any government or donor subsidies' => false, 'they are cheaper than any other type of small-scale generator technology' => false],
                        'why' => 'Bacaan menyatakan mini-grid "can be installed relatively quickly and require far less infrastructure", sehingga cocok untuk desa terpencil yang sulit dijangkau jaringan nasional.',
                    ],
                    [
                        'q' => 'Referring to the same passage about solar mini-grids in rural areas: In the passage, the word "underserved" most nearly means:',
                        'options' => ['not adequately provided with services' => true, 'heavily populated' => false, 'economically prosperous' => false, 'technologically advanced' => false],
                        'why' => '"Underserved communities" merujuk pada komunitas yang belum memperoleh layanan (dalam hal ini listrik) secara memadai.',
                    ],
                    [
                        'q' => 'Referring to the same passage about solar mini-grids in rural areas: Is the following statement True, False, or Not Given? "The passage suggests that battery technology costs are expected to decrease in the future."',
                        'options' => ['True' => true, 'False' => false, 'Not Given' => false],
                        'why' => 'Bacaan menyatakan "as battery technology continues to improve and costs fall," yang berarti biaya baterai diperkirakan akan menurun di masa depan.',
                    ],
                    [
                        'q' => 'Read the passage and answer the question. "As cities around the world continue to expand, urban planners and environmental groups have increasingly turned their attention to the decline of pollinator populations such as bees and butterflies. Pesticide use, habitat loss, and climate change have all contributed to shrinking numbers of these insects, which are essential for pollinating roughly a third of the food crops humans consume. In response, a growing number of municipalities have begun converting unused rooftops, road medians, and vacant lots into pollinator-friendly gardens planted with native, flowering species. Rooftop beekeeping has also gained popularity in cities such as Paris, New York, and Tokyo, with hotels and office buildings installing hives to support local ecosystems and, in some cases, produce small batches of honey. Researchers monitoring these urban green spaces have found that they can host a surprisingly diverse range of pollinator species, sometimes even outperforming nearby rural farmland, where monoculture crops and pesticide use limit biodiversity. Critics caution, however, that poorly planned urban beekeeping can sometimes crowd out wild native bee species by concentrating too many honeybee colonies in a small area." What is the main idea of the passage?',
                        'options' => ['the growing effort to support pollinator populations through urban green spaces, and its potential drawbacks' => true, 'the decline of the commercial honey industry in major world cities' => false, 'a comparison of hotel amenities in Paris, New York, and Tokyo' => false, 'the overall negative effects of climate change on global agriculture' => false],
                        'why' => 'Bacaan membahas upaya kota-kota mendukung populasi polinator melalui taman dan lebah atap, sekaligus menyinggung kritik terhadap praktik yang kurang terencana.',
                    ],
                    [
                        'q' => 'Referring to the same passage about urban pollinator conservation: According to the passage, roughly what proportion of the food crops humans consume depend on pollination?',
                        'options' => ['about one third' => true, 'about one tenth' => false, 'nearly all of them' => false, 'less than five percent' => false],
                        'why' => 'Bacaan menyebutkan polinator "essential for pollinating roughly a third of the food crops humans consume."',
                    ],
                    [
                        'q' => 'Referring to the same passage about urban pollinator conservation: What can be inferred from the finding that urban green spaces sometimes host more diverse pollinators than nearby rural farmland?',
                        'options' => ['monoculture farming and pesticide use on rural farms may limit pollinator biodiversity there' => true, 'rural farmland is generally a far better habitat for pollinators than any city' => false, 'urban pollinator gardens produce more honey than commercial rural farms' => false, 'pollinators are unable to survive in any agricultural environment' => false],
                        'why' => 'Karena lahan pertanian pedesaan sering menggunakan monokultur dan pestisida yang membatasi keragaman hayati, sedangkan ruang hijau kota justru bisa lebih beragam.',
                    ],
                    [
                        'q' => 'Referring to the same passage about urban pollinator conservation: In the passage, the word "monoculture" most nearly means:',
                        'options' => ['the practice of growing a single crop over a wide area' => true, 'a method of organic pest control' => false, 'a type of rooftop garden design' => false, 'a mixture of many different plant species' => false],
                        'why' => '"Monoculture" berarti praktik menanam satu jenis tanaman saja di lahan yang luas, yang justru membatasi keragaman hayati.',
                    ],
                    [
                        'q' => 'Referring to the same passage about urban pollinator conservation: Is the following statement True, False, or Not Given? "According to the passage, urban beekeeping has no negative effects on wild bee populations."',
                        'options' => ['False' => true, 'True' => false, 'Not Given' => false],
                        'why' => 'Bacaan menyatakan bahwa lebah madu perkotaan yang terlalu terkonsentrasi dapat "crowd out wild native bee species," sehingga pernyataan bahwa tidak ada dampak negatif adalah salah.',
                    ],
                ],
                category: 'test',
                timeLimitSeconds: 60 * 60,
                description: '40 soal simulasi IELTS (format & strategi, kosakata tren/opini, Reading Comprehension) dengan waktu 60 menit.',
            );

            $this->create(
                null,
                'Tes Fokus Grammar Semua Level',
                'medium',
                null,
                [
                    [
                        'q' => 'She ___ a teacher.',
                        'options' => ['is' => true, 'am' => false, 'are' => false, 'be' => false],
                        'why' => 'Subjek tunggal "she" menggunakan kata kerja "is" dalam bentuk to be.',
                    ],
                    [
                        'q' => 'I ___ from Indonesia.',
                        'options' => ['am' => true, 'is' => false, 'are' => false, 'be' => false],
                        'why' => 'Subjek "I" selalu menggunakan "am" dalam bentuk to be.',
                    ],
                    [
                        'q' => 'They ___ to school every day.',
                        'options' => ['go' => true, 'goes' => false, 'going' => false, 'went' => false],
                        'why' => 'Subjek jamak "they" pada simple present menggunakan bentuk dasar verb tanpa tambahan -s.',
                    ],
                    [
                        'q' => 'He ___ TV every night.',
                        'options' => ['watches' => true, 'watch' => false, 'watching' => false, 'watched' => false],
                        'why' => 'Subjek tunggal orang ketiga (he) pada simple present menambahkan -es pada kata kerja "watch".',
                    ],
                    [
                        'q' => 'I saw ___ elephant at the zoo.',
                        'options' => ['an' => true, 'a' => false, 'the' => false, 'no article' => false],
                        'why' => '"Elephant" diawali huruf vokal sehingga menggunakan artikel "an", bukan "a".',
                    ],
                    [
                        'q' => 'There are three ___ on the table.',
                        'options' => ['books' => true, 'book' => false, 'bookes' => false, 'bookies' => false],
                        'why' => 'Kata benda "book" yang jamak (lebih dari satu) ditambahkan -s menjadi "books".',
                    ],
                    [
                        'q' => 'She ___ to Bali last year.',
                        'options' => ['traveled' => true, 'travels' => false, 'is traveling' => false, 'will travel' => false],
                        'why' => 'Kata "last year" menunjukkan waktu lampau, sehingga menggunakan simple past "traveled".',
                    ],
                    [
                        'q' => 'Look! The children ___ in the park right now.',
                        'options' => ['are playing' => true, 'play' => false, 'played' => false, 'plays' => false],
                        'why' => 'Kata "right now" menunjukkan tindakan sedang berlangsung, sehingga menggunakan present continuous "are playing".',
                    ],
                    [
                        'q' => 'This bag is ___ than that one.',
                        'options' => ['bigger' => true, 'big' => false, 'biggest' => false, 'more big' => false],
                        'why' => 'Perbandingan antara dua benda menggunakan bentuk comparative "bigger" (adjective pendek + -er).',
                    ],
                    [
                        'q' => '___ you swim when you were five?',
                        'options' => ['Could' => true, 'Can' => false, 'Will' => false, 'Should' => false],
                        'why' => '"Could" digunakan untuk menyatakan kemampuan di masa lalu, sesuai konteks "when you were five".',
                    ],
                    [
                        'q' => '___ I open the window, please?',
                        'options' => ['Can' => true, 'Could have' => false, 'Must' => false, 'Should have' => false],
                        'why' => '"Can" digunakan untuk meminta izin secara sopan dan sederhana pada situasi sekarang.',
                    ],
                    [
                        'q' => 'This is the ___ movie I have ever watched.',
                        'options' => ['most exciting' => true, 'more exciting' => false, 'exciting' => false, 'excitinger' => false],
                        'why' => 'Untuk adjective panjang, bentuk superlative dibuat dengan "most" + adjective, yaitu "most exciting".',
                    ],
                    [
                        'q' => 'I ___ my homework already.',
                        'options' => ['have finished' => true, 'finished' => false, 'finish' => false, 'am finishing' => false],
                        'why' => 'Kata "already" menunjukkan hasil dari suatu tindakan hingga sekarang, sehingga menggunakan present perfect "have finished".',
                    ],
                    [
                        'q' => 'If it rains tomorrow, we ___ the picnic.',
                        'options' => ['will cancel' => true, 'cancel' => false, 'canceled' => false, 'would cancel' => false],
                        'why' => 'First conditional menggunakan "if + simple present, will + verb dasar" untuk kemungkinan nyata di masa depan.',
                    ],
                    [
                        'q' => 'The letter ___ by Maria yesterday.',
                        'options' => ['was written' => true, 'wrote' => false, 'writes' => false, 'is written' => false],
                        'why' => 'Kalimat pasif bentuk lampau menggunakan "was/were + past participle", yaitu "was written".',
                    ],
                    [
                        'q' => 'The woman ___ lives next door is a doctor.',
                        'options' => ['who' => true, 'which' => false, 'whom' => false, 'whose' => false],
                        'why' => '"Who" digunakan sebagai relative pronoun untuk menerangkan subjek orang ("the woman").',
                    ],
                    [
                        'q' => 'This is the house ___ roof was damaged in the storm.',
                        'options' => ['whose' => true, 'who' => false, 'which' => false, 'that' => false],
                        'why' => '"Whose" digunakan untuk menunjukkan kepemilikan dalam relative clause, di sini kepemilikan atap rumah.',
                    ],
                    [
                        'q' => 'The bridge ___ by workers next month.',
                        'options' => ['will be built' => true, 'builds' => false, 'is building' => false, 'built' => false],
                        'why' => 'Kalimat pasif untuk kejadian di masa depan menggunakan "will be + past participle".',
                    ],
                    [
                        'q' => 'When I arrived at the station, the train ___ already.',
                        'options' => ['had left' => true, 'left' => false, 'has left' => false, 'was leaving' => false],
                        'why' => 'Past perfect "had left" digunakan karena kereta sudah pergi sebelum tindakan lampau lain (saya tiba) terjadi.',
                    ],
                    [
                        'q' => 'If I ___ rich, I would travel around the world.',
                        'options' => ['were' => true, 'was' => false, 'am' => false, 'had been' => false],
                        'why' => 'Second conditional menggunakan "if + past simple (were untuk semua subjek)" untuk situasi tidak nyata di masa sekarang.',
                    ],
                    [
                        'q' => 'She said that she ___ tired.',
                        'options' => ['was' => true, 'is' => false, 'has been' => false, 'were' => false],
                        'why' => 'Dalam reported speech, kalimat langsung "I am tired" berubah menjadi "was" karena backshift tense.',
                    ],
                    [
                        'q' => 'He isn\'t answering his phone. He ___ be asleep.',
                        'options' => ['must' => true, 'can' => false, 'should' => false, 'may not' => false],
                        'why' => '"Must" digunakan untuk deduksi logis yang kuat berdasarkan bukti (tidak menjawab telepon berarti kemungkinan besar tidur).',
                    ],
                    [
                        'q' => 'The teacher told the students ___ talking.',
                        'options' => ['to stop' => true, 'stop' => false, 'stopped' => false, 'stopping' => false],
                        'why' => 'Reported speech untuk perintah menggunakan pola "told someone + to + verb dasar", yaitu "to stop".',
                    ],
                    [
                        'q' => 'That noise is strange. It ___ be the neighbor\'s dog.',
                        'options' => ['might' => true, 'must' => false, 'can\'t' => false, 'have to' => false],
                        'why' => '"Might" digunakan untuk deduksi yang menyatakan kemungkinan, bukan kepastian.',
                    ],
                    [
                        'q' => 'If she had studied harder, she ___ the exam.',
                        'options' => ['would have passed' => true, 'would pass' => false, 'passed' => false, 'will pass' => false],
                        'why' => 'Third conditional menggunakan "if + past perfect, would have + past participle" untuk situasi tidak nyata di masa lalu.',
                    ],
                    [
                        'q' => 'If I had taken that job, I ___ in Jakarta now.',
                        'options' => ['would be living' => true, 'would have lived' => false, 'will live' => false, 'am living' => false],
                        'why' => 'Mixed conditional menggabungkan syarat lampau ("had taken") dengan akibat di masa sekarang ("would be living").',
                    ],
                    [
                        'q' => 'The doctor recommended that he ___ more water.',
                        'options' => ['drink' => true, 'drinks' => false, 'drank' => false, 'will drink' => false],
                        'why' => 'Subjunctive mood setelah kata kerja "recommend" menggunakan bentuk dasar verb tanpa -s, yaitu "drink".',
                    ],
                    [
                        'q' => 'It is essential that every student ___ the rules.',
                        'options' => ['follow' => true, 'follows' => false, 'followed' => false, 'is following' => false],
                        'why' => 'Setelah ungkapan "it is essential that", digunakan subjunctive dengan bentuk dasar verb "follow".',
                    ],
                    [
                        'q' => '___ had I arrived home when the phone rang.',
                        'options' => ['No sooner' => true, 'Hardly' => false, 'Only' => false, 'Never' => false],
                        'why' => 'Inversion "No sooner had I arrived...when..." digunakan untuk menekankan dua kejadian yang terjadi hampir bersamaan.',
                    ],
                    [
                        'q' => '___ was John who broke the window, not his brother.',
                        'options' => ['It' => true, 'This' => false, 'There' => false, 'That' => false],
                        'why' => 'Cleft sentence dengan pola "It was...who..." digunakan untuk menekankan bahwa John lah yang memecahkan jendela.',
                    ],
                    [
                        'q' => 'We ___ happy about the news.',
                        'options' => ['are' => true, 'is' => false, 'am' => false, 'be' => false],
                        'why' => '"Are" digunakan dengan subjek jamak "we" dalam kalimat to be.',
                    ],
                    [
                        'q' => '___ she a nurse?',
                        'options' => ['Is' => true, 'Are' => false, 'Am' => false, 'Do' => false],
                        'why' => '"Is" digunakan untuk subjek tunggal orang ketiga (she) dalam kalimat tanya dengan to be.',
                    ],
                    [
                        'q' => 'She ___ not like spicy food.',
                        'options' => ['does' => true, 'do' => false, 'is' => false, 'has' => false],
                        'why' => '"Does not" digunakan untuk kalimat negatif simple present dengan subjek tunggal orang ketiga.',
                    ],
                    [
                        'q' => 'She is ___ honest person.',
                        'options' => ['an' => true, 'a' => false, 'the' => false, 'some' => false],
                        'why' => '"An" digunakan sebelum kata yang diawali bunyi vokal, seperti "honest" (bunyi "o").',
                    ],
                    [
                        'q' => 'How many ___ do you have?',
                        'options' => ['children' => true, 'childs' => false, 'child' => false, 'childrens' => false],
                        'why' => 'Kata "child" memiliki bentuk jamak tidak beraturan, yaitu "children".',
                    ],
                    [
                        'q' => 'Give the book to ___. She needs it.',
                        'options' => ['her' => true, 'she' => false, 'hers' => false, 'herself' => false],
                        'why' => '"Her" adalah kata ganti objek yang digunakan untuk "she".',
                    ],
                    [
                        'q' => 'We ___ pizza for dinner last night.',
                        'options' => ['ate' => true, 'eat' => false, 'eating' => false, 'eaten' => false],
                        'why' => '"Ate" adalah bentuk lampau (past tense) dari "eat", sesuai dengan keterangan waktu "last night".',
                    ],
                    [
                        'q' => 'He ___ not go to work yesterday.',
                        'options' => ['did' => true, 'does' => false, 'do' => false, 'was' => false],
                        'why' => 'Kalimat negatif dalam simple past menggunakan "did not" diikuti kata kerja bentuk dasar.',
                    ],
                    [
                        'q' => 'Please be quiet. I ___ on the phone right now.',
                        'options' => ['am talking' => true, 'talk' => false, 'talked' => false, 'talks' => false],
                        'why' => '"Am talking" (present continuous) digunakan untuk tindakan yang sedang berlangsung saat ini, ditandai dengan "right now".',
                    ],
                    [
                        'q' => 'This book is ___ than that one.',
                        'options' => ['more interesting' => true, 'interesting' => false, 'interestinger' => false, 'most interesting' => false],
                        'why' => 'Kata sifat panjang (lebih dari dua suku kata) membentuk komparatif dengan menambahkan "more" di depannya.',
                    ],
                    [
                        'q' => '___ you speak French?',
                        'options' => ['Can' => true, 'Could' => false, 'Do' => false, 'Are' => false],
                        'why' => '"Can" digunakan untuk menanyakan kemampuan seseorang di masa sekarang.',
                    ],
                    [
                        'q' => 'When she was young, she ___ run very fast.',
                        'options' => ['could' => true, 'can' => false, 'was able' => false, 'cans' => false],
                        'why' => '"Could" digunakan untuk menyatakan kemampuan seseorang di masa lalu.',
                    ],
                    [
                        'q' => 'I ___ never been to Japan.',
                        'options' => ['have' => true, 'has' => false, 'had' => false, 'am' => false],
                        'why' => '"Have" digunakan dengan subjek "I" dalam present perfect (have/has + past participle).',
                    ],
                    [
                        'q' => 'She ___ just finished her homework.',
                        'options' => ['has' => true, 'have' => false, 'had' => false, 'is' => false],
                        'why' => '"Has" digunakan dengan subjek tunggal orang ketiga (she) dalam present perfect.',
                    ],
                    [
                        'q' => 'If you heat ice, it ___ melt.',
                        'options' => ['will' => true, 'would' => false, 'is' => false, 'can' => false],
                        'why' => 'First conditional (kondisi nyata) menggunakan "will" pada klausa utama untuk hasil yang pasti terjadi.',
                    ],
                    [
                        'q' => 'The cake ___ baked by my mother.',
                        'options' => ['was' => true, 'is' => false, 'were' => false, 'has' => false],
                        'why' => 'Kalimat pasif bentuk lampau menggunakan "was/were" + past participle; "cake" bersubjek tunggal sehingga memakai "was".',
                    ],
                    [
                        'q' => 'The book ___ I bought yesterday is really interesting.',
                        'options' => ['which' => true, 'who' => false, 'whose' => false, 'where' => false],
                        'why' => '"Which" digunakan untuk merujuk pada benda (the book) dalam klausa relatif.',
                    ],
                    [
                        'q' => 'This is the restaurant ___ we had dinner.',
                        'options' => ['where' => true, 'which' => false, 'who' => false, 'when' => false],
                        'why' => '"Where" digunakan untuk menunjukkan tempat dalam klausa relatif.',
                    ],
                    [
                        'q' => 'She ___ finished cooking before the guests arrived.',
                        'options' => ['had' => true, 'has' => false, 'have' => false, 'was' => false],
                        'why' => '"Had" + past participle (past perfect) digunakan untuk kejadian yang selesai sebelum kejadian lampau lainnya (tamu datang).',
                    ],
                    [
                        'q' => 'If they ___ more time, they would finish the project.',
                        'options' => ['had' => true, 'have' => false, 'has' => false, 'would have' => false],
                        'why' => 'Second conditional menggunakan past tense ("had") pada klausa if untuk situasi khayalan di masa sekarang.',
                    ],
                    [
                        'q' => 'He said he ___ come to the party.',
                        'options' => ['would' => true, 'will' => false, 'can' => false, 'is going to' => false],
                        'why' => 'Dalam reported speech, "will" berubah menjadi "would".',
                    ],
                    [
                        'q' => 'He told me he ___ working on a new project.',
                        'options' => ['was' => true, 'is' => false, 'has been' => false, 'will be' => false],
                        'why' => 'Dalam reported speech, present continuous ("is working") berubah menjadi past continuous ("was working").',
                    ],
                    [
                        'q' => 'The ground is wet. It ___ have rained last night.',
                        'options' => ['must' => true, 'can' => false, 'should' => false, 'would' => false],
                        'why' => '"Must have" digunakan untuk membuat kesimpulan logis yang kuat tentang kejadian di masa lalu.',
                    ],
                    [
                        'q' => 'He told us he was broke, but he just bought a new car. He ___ be lying.',
                        'options' => ['must' => true, 'might' => false, 'can' => false, 'should' => false],
                        'why' => '"Must be" digunakan untuk menyatakan kesimpulan yang hampir pasti benar berdasarkan bukti yang ada.',
                    ],
                    [
                        'q' => 'If I had known about the meeting, I ___ attended it.',
                        'options' => ['would have' => true, 'will have' => false, 'would' => false, 'had' => false],
                        'why' => 'Third conditional menggunakan "would have" + past participle untuk membayangkan hasil berbeda dari kejadian lampau yang sudah tidak bisa diubah.',
                    ],
                    [
                        'q' => 'If she ___ harder at school, she would have a better job now.',
                        'options' => ['had studied' => true, 'studied' => false, 'has studied' => false, 'would study' => false],
                        'why' => 'Mixed conditional (syarat lampau, akibat sekarang) menggunakan past perfect pada klausa if.',
                    ],
                    [
                        'q' => "If he weren't so lazy, he ___ finished the project by now.",
                        'options' => ['would have' => true, 'would' => false, 'will have' => false, 'had' => false],
                        'why' => 'Mixed conditional (syarat sekarang, akibat lampau) menggunakan "would have" + past participle pada klausa hasil.',
                    ],
                    [
                        'q' => 'The manager insisted that the report ___ submitted by Friday.',
                        'options' => ['be' => true, 'is' => false, 'was' => false, 'will be' => false],
                        'why' => 'Subjunctive mood menggunakan bentuk dasar kata kerja ("be") setelah kata kerja seperti "insist" untuk menyatakan tuntutan.',
                    ],
                    [
                        'q' => '___ have I seen such a beautiful sunset.',
                        'options' => ['Never' => true, 'Ever' => false, 'Not' => false, 'No' => false],
                        'why' => 'Inversi dengan kata keterangan negatif "Never" di awal kalimat mengubah urutan menjadi auxiliary + subjek.',
                    ],
                    [
                        'q' => '___ she wants is a bit more support from her team.',
                        'options' => ['What' => true, 'That' => false, 'It' => false, 'Which' => false],
                        'why' => 'Wh-cleft sentence menggunakan "What" di awal kalimat untuk menekankan hal yang diinginkan.',
                    ],
                ],
                category: 'test',
                timeLimitSeconds: 40 * 60,
                description: '30 soal grammar murni dari level Beginner hingga Advanced, tersusun bertahap dari mudah ke sulit, dengan waktu 40 menit.',
            );

            $this->create(
                null,
                'Tes Fokus Vocabulary & Reading Semua Level',
                'medium',
                null,
                [
                    [
                        'q' => "What is the meaning of the word 'mother' in Bahasa Indonesia?",
                        'options' => ['Ibu' => true, 'Ayah' => false, 'Kakak' => false, 'Nenek' => false],
                        'why' => "'Mother' berarti 'ibu', yaitu orang tua perempuan.",
                    ],
                    [
                        'q' => "What does the word 'book' mean?",
                        'options' => ['Buku' => true, 'Meja' => false, 'Pintu' => false, 'Jendela' => false],
                        'why' => "'Book' adalah benda yang dibaca, artinya 'buku'.",
                    ],
                    [
                        'q' => "Which Indonesian word means the color 'blue'?",
                        'options' => ['Biru' => true, 'Merah' => false, 'Kuning' => false, 'Hijau' => false],
                        'why' => "'Blue' adalah nama warna yang dalam Bahasa Indonesia berarti 'biru'.",
                    ],
                    [
                        'q' => "What does the verb 'eat' mean?",
                        'options' => ['Makan' => true, 'Minum' => false, 'Tidur' => false, 'Berjalan' => false],
                        'why' => "'Eat' adalah kata kerja yang berarti 'makan'.",
                    ],
                    [
                        'q' => "What is the English word 'chair' in Bahasa Indonesia?",
                        'options' => ['Kursi' => true, 'Meja' => false, 'Lampu' => false, 'Cermin' => false],
                        'why' => "'Chair' adalah benda untuk duduk, artinya 'kursi'.",
                    ],
                    [
                        'q' => "What does the word 'bread' mean?",
                        'options' => ['Roti' => true, 'Nasi' => false, 'Susu' => false, 'Telur' => false],
                        'why' => "'Bread' adalah makanan yang terbuat dari tepung, artinya 'roti'.",
                    ],
                    [
                        'q' => "What does the word 'airport' mean?",
                        'options' => ['Bandara' => true, 'Stasiun kereta' => false, 'Pelabuhan' => false, 'Terminal bus' => false],
                        'why' => "'Airport' adalah tempat pesawat terbang lepas landas dan mendarat, artinya 'bandara'.",
                    ],
                    [
                        'q' => "What does the phrase 'wake up' mean?",
                        'options' => ['Bangun tidur' => true, 'Pergi tidur' => false, 'Mandi' => false, 'Sarapan' => false],
                        'why' => "'Wake up' berarti bangun dari tidur di pagi hari.",
                    ],
                    [
                        'q' => "What does the adjective 'happy' mean?",
                        'options' => ['Senang atau bahagia' => true, 'Sedih' => false, 'Marah' => false, 'Takut' => false],
                        'why' => "'Happy' menggambarkan perasaan senang atau bahagia.",
                    ],
                    [
                        'q' => "What does the adjective 'expensive' mean?",
                        'options' => ['Mahal' => true, 'Murah' => false, 'Berat' => false, 'Ringan' => false],
                        'why' => "'Expensive' menggambarkan sesuatu yang harganya tinggi, artinya 'mahal'.",
                    ],
                    [
                        'q' => "What does the word 'passport' mean?",
                        'options' => ['Paspor' => true, 'Tiket pesawat' => false, 'Peta' => false, 'Koper' => false],
                        'why' => "'Passport' adalah dokumen identitas untuk bepergian ke luar negeri, artinya 'paspor'.",
                    ],
                    [
                        'q' => "What does the adjective 'tired' mean?",
                        'options' => ['Lelah' => true, 'Segar' => false, 'Kuat' => false, 'Sehat' => false],
                        'why' => "'Tired' menggambarkan kondisi tubuh yang kekurangan energi, artinya 'lelah'.",
                    ],
                    [
                        'q' => "What does the verb 'achieve' mean?",
                        'options' => ['Mencapai' => true, 'Menghindari' => false, 'Membatalkan' => false, 'Mengabaikan' => false],
                        'why' => "'Achieve' berarti berhasil mendapatkan atau mencapai sesuatu setelah berusaha.",
                    ],
                    [
                        'q' => "What does the adjective 'reliable' mean?",
                        'options' => ['Dapat diandalkan' => true, 'Tidak stabil' => false, 'Mahal' => false, 'Terkenal' => false],
                        'why' => "'Reliable' menggambarkan sesuatu atau seseorang yang dapat dipercaya dan diandalkan.",
                    ],
                    [
                        'q' => "What does the verb 'increase' mean?",
                        'options' => ['Meningkat' => true, 'Menurun' => false, 'Tetap sama' => false, 'Menghilang' => false],
                        'why' => "'Increase' berarti bertambah atau naik jumlahnya.",
                    ],
                    [
                        'q' => "What does the noun 'opportunity' mean?",
                        'options' => ['Kesempatan' => true, 'Kesalahan' => false, 'Kegagalan' => false, 'Keputusan' => false],
                        'why' => "'Opportunity' berarti peluang atau kesempatan untuk melakukan sesuatu.",
                    ],
                    [
                        'q' => "Read the passage: \"Maria works at a small bakery in Jakarta. Every morning she wakes up at five o'clock to prepare fresh bread before the shop opens. Her customers love her bread because it is soft and always fresh. Last month, she started selling cakes too, and now more people visit her shop every day.\" According to the passage, why do customers love Maria's bread?",
                        'options' => ['Because it is soft and always fresh' => true, 'Because it is very cheap' => false, 'Because it is imported from another country' => false, 'Because it is decorated beautifully' => false],
                        'why' => 'Teks menyebutkan bahwa pelanggan menyukai roti Maria karena lembut dan selalu segar.',
                    ],
                    [
                        'q' => "Read the passage: \"Maria works at a small bakery in Jakarta. Every morning she wakes up at five o'clock to prepare fresh bread before the shop opens. Her customers love her bread because it is soft and always fresh. Last month, she started selling cakes too, and now more people visit her shop every day.\" What did Maria start doing last month?",
                        'options' => ['Selling cakes' => true, 'Opening a new shop' => false, 'Hiring an assistant' => false, 'Closing the shop early' => false],
                        'why' => 'Teks menyebutkan bahwa bulan lalu Maria mulai menjual kue selain roti.',
                    ],
                    [
                        'q' => "What does the phrasal verb 'give up' mean?",
                        'options' => ['To stop trying' => true, 'To start something new' => false, 'To succeed at something' => false, 'To celebrate an achievement' => false],
                        'why' => "'Give up' berarti berhenti mencoba atau menyerah.",
                    ],
                    [
                        'q' => "What does the idiom 'break the ice' mean?",
                        'options' => ['To start a conversation in an awkward or formal situation' => true, 'To cause an accident' => false, 'To end a friendship suddenly' => false, 'To cancel a planned meeting' => false],
                        'why' => "'Break the ice' adalah idiom yang berarti memulai percakapan untuk mencairkan suasana canggung.",
                    ],
                    [
                        'q' => "What does the collocation 'make a decision' mean?",
                        'options' => ['To decide something' => true, 'To make a mistake' => false, 'To create a physical object' => false, 'To change your mind repeatedly' => false],
                        'why' => "'Make a decision' adalah kolokasi umum yang berarti mengambil keputusan.",
                    ],
                    [
                        'q' => "What does the phrasal verb 'come across' mean?",
                        'options' => ['To find or meet something by chance' => true, 'To ignore something deliberately' => false, 'To plan something carefully' => false, 'To avoid someone on purpose' => false],
                        'why' => "'Come across' berarti menemukan atau bertemu sesuatu/seseorang secara tidak sengaja.",
                    ],
                    [
                        'q' => 'Read the passage: "Rina had been job hunting for six months without any luck. She was ready to give up when she came across an advertisement for a marketing position online. At the interview, she managed to break the ice with the panel by talking about her hobbies. To her surprise, the company decided to hire her the following week, and she finally felt her hard work had paid off." Why did Rina almost give up?',
                        'options' => ['Because she had been job hunting for six months without success' => true, 'Because she disliked working in marketing' => false, 'Because she failed her job interview' => false, 'Because she had to move to another city' => false],
                        'why' => 'Teks menyebutkan Rina hampir menyerah karena sudah mencari pekerjaan selama enam bulan tanpa hasil.',
                    ],
                    [
                        'q' => 'Read the passage: "Rina had been job hunting for six months without any luck. She was ready to give up when she came across an advertisement for a marketing position online. At the interview, she managed to break the ice with the panel by talking about her hobbies. To her surprise, the company decided to hire her the following week, and she finally felt her hard work had paid off." How did Rina break the ice during the interview?',
                        'options' => ['By talking about her hobbies' => true, 'By discussing her salary expectations' => false, 'By asking about the company\'s history' => false, 'By showing her certificates' => false],
                        'why' => 'Teks menyebutkan Rina mencairkan suasana dengan membicarakan hobinya kepada panel wawancara.',
                    ],
                    [
                        'q' => "What does the academic word 'ubiquitous' mean?",
                        'options' => ['Present or found everywhere' => true, 'Extremely rare and hard to find' => false, 'Highly expensive and exclusive' => false, 'Difficult to understand or explain' => false],
                        'why' => "'Ubiquitous' berarti ada di mana-mana atau sangat umum ditemukan.",
                    ],
                    [
                        'q' => "What does the academic word 'hypothesis' mean?",
                        'options' => ['A proposed explanation based on limited evidence, to be tested further' => true, 'A scientific law that has been proven true' => false, 'A summary of experimental results' => false, 'A type of laboratory equipment' => false],
                        'why' => "'Hypothesis' adalah dugaan sementara yang perlu diuji lebih lanjut, bukan hukum yang sudah terbukti.",
                    ],
                    [
                        'q' => "What does the academic word 'ambiguous' mean?",
                        'options' => ['Having more than one possible meaning or interpretation' => true, 'Completely clear and easy to understand' => false, 'Very simple and straightforward' => false, 'Extremely accurate and precise' => false],
                        'why' => "'Ambiguous' berarti sesuatu yang dapat ditafsirkan lebih dari satu makna, sehingga tidak jelas.",
                    ],
                    [
                        'q' => "What does the academic verb 'substantiate' mean?",
                        'options' => ['To provide evidence or proof to support a claim' => true, 'To reject an argument without explanation' => false, 'To summarize a long text briefly' => false, 'To translate a document into another language' => false],
                        'why' => "'Substantiate' berarti memberikan bukti untuk mendukung suatu pernyataan atau klaim.",
                    ],
                    [
                        'q' => 'Read the passage: "Climate scientists have long debated the extent to which human activity accelerates global warming. Recent studies suggest that greenhouse gas emissions from industrial processes are a primary contributor to rising global temperatures. Researchers gathered data from multiple continents over three decades to substantiate their hypothesis. Although some skeptics argue that natural climate cycles play an equally significant role, the majority of evidence points toward anthropogenic causes. Consequently, many governments have begun implementing stricter regulations on carbon emissions. Critics, however, contend that these policies could have unintended economic consequences for developing nations." What did researchers do to substantiate their hypothesis about global warming?',
                        'options' => ['They gathered data from multiple continents over three decades' => true, 'They conducted a single laboratory experiment' => false, 'They surveyed public opinion in one country' => false, 'They reviewed historical newspaper articles' => false],
                        'why' => 'Teks menyebutkan para peneliti mengumpulkan data dari berbagai benua selama tiga dekade untuk mendukung hipotesis mereka.',
                    ],
                    [
                        'q' => 'Read the passage: "Climate scientists have long debated the extent to which human activity accelerates global warming. Recent studies suggest that greenhouse gas emissions from industrial processes are a primary contributor to rising global temperatures. Researchers gathered data from multiple continents over three decades to substantiate their hypothesis. Although some skeptics argue that natural climate cycles play an equally significant role, the majority of evidence points toward anthropogenic causes. Consequently, many governments have begun implementing stricter regulations on carbon emissions. Critics, however, contend that these policies could have unintended economic consequences for developing nations." According to the passage, what concern do critics raise about stricter carbon emission regulations?',
                        'options' => ['They could have unintended economic consequences for developing nations' => true, 'They are too lenient to reduce emissions effectively' => false, 'They ignore the scientific consensus on climate change' => false, 'They apply only to industrialized nations' => false],
                        'why' => 'Teks menyebutkan bahwa kritikus khawatir kebijakan tersebut dapat berdampak buruk pada ekonomi negara berkembang.',
                    ],
                    [
                        'q' => "What is the meaning of the word 'shirt' in Bahasa Indonesia?",
                        'options' => ['baju kemeja' => true, 'celana' => false, 'sepatu' => false, 'topi' => false],
                        'why' => "'Shirt' berarti 'baju kemeja', pakaian atas yang dikenakan di badan.",
                    ],
                    [
                        'q' => "What does the word 'rain' mean?",
                        'options' => ['air yang turun dari langit (hujan)' => true, 'salju' => false, 'angin kencang' => false, 'matahari' => false],
                        'why' => "'Rain' berarti 'hujan', yaitu air yang turun dari langit.",
                    ],
                    [
                        'q' => "Which Indonesian word means the body part 'hand'?",
                        'options' => ['tangan' => true, 'kaki' => false, 'kepala' => false, 'telinga' => false],
                        'why' => "'Hand' berarti 'tangan', bagian tubuh yang digunakan untuk memegang sesuatu.",
                    ],
                    [
                        'q' => "What does the word 'morning' mean?",
                        'options' => ['waktu pagi hari' => true, 'waktu malam hari' => false, 'waktu siang hari' => false, 'waktu sore hari' => false],
                        'why' => "'Morning' berarti 'pagi', bagian awal hari sebelum siang.",
                    ],
                    [
                        'q' => "What is the English word 'shoes' in Bahasa Indonesia?",
                        'options' => ['sepatu' => true, 'kaos kaki' => false, 'topi' => false, 'sarung tangan' => false],
                        'why' => "'Shoes' berarti 'sepatu', alas kaki yang dipakai saat berjalan.",
                    ],
                    [
                        'q' => "What does the adjective 'cold' mean?",
                        'options' => ['dingin' => true, 'panas' => false, 'hangat' => false, 'lembap' => false],
                        'why' => "'Cold' berarti 'dingin', suhu yang rendah.",
                    ],
                    [
                        'q' => "What does the hobby 'painting' mean?",
                        'options' => ['kegiatan melukis dengan cat' => true, 'kegiatan memasak' => false, 'kegiatan berenang' => false, 'kegiatan membaca' => false],
                        'why' => "'Painting' berarti 'melukis', kegiatan membuat gambar menggunakan cat.",
                    ],
                    [
                        'q' => "What does the word 'discount' mean?",
                        'options' => ['potongan harga' => true, 'kenaikan harga' => false, 'biaya pengiriman' => false, 'tanda terima' => false],
                        'why' => "'Discount' berarti 'potongan harga' atau diskon saat berbelanja.",
                    ],
                    [
                        'q' => "What does the word 'medicine' mean?",
                        'options' => ['obat' => true, 'penyakit' => false, 'dokter' => false, 'rumah sakit' => false],
                        'why' => "'Medicine' berarti 'obat', digunakan untuk menyembuhkan penyakit.",
                    ],
                    [
                        'q' => "What does the adjective 'angry' mean?",
                        'options' => ['marah' => true, 'sedih' => false, 'senang' => false, 'takut' => false],
                        'why' => "'Angry' berarti 'marah', perasaan kesal atau tidak senang.",
                    ],
                    [
                        'q' => "What does the word 'cashier' mean?",
                        'options' => ['kasir, orang yang menerima pembayaran di toko' => true, 'pelanggan toko' => false, 'pemilik toko' => false, 'satpam toko' => false],
                        'why' => "'Cashier' berarti 'kasir', orang yang bertugas menerima pembayaran.",
                    ],
                    [
                        'q' => "What does the word 'exercise' mean?",
                        'options' => ['olahraga atau latihan fisik' => true, 'istirahat total' => false, 'makan besar' => false, 'tidur siang' => false],
                        'why' => "'Exercise' berarti 'olahraga' atau kegiatan latihan fisik untuk menjaga kesehatan.",
                    ],
                    [
                        'q' => "What does the verb 'improve' mean?",
                        'options' => ['menjadi lebih baik' => true, 'menjadi lebih buruk' => false, 'tetap sama' => false, 'berhenti total' => false],
                        'why' => "'Improve' berarti 'meningkat' atau menjadi lebih baik.",
                    ],
                    [
                        'q' => "What does the adjective 'confident' mean?",
                        'options' => ['percaya diri' => true, 'ragu-ragu' => false, 'malu' => false, 'takut' => false],
                        'why' => "'Confident' berarti 'percaya diri', yakin dengan kemampuan sendiri.",
                    ],
                    [
                        'q' => "What does the verb 'decrease' mean?",
                        'options' => ['berkurang atau menurun' => true, 'bertambah atau meningkat' => false, 'tetap stabil' => false, 'berubah bentuk' => false],
                        'why' => "'Decrease' berarti 'menurun' atau berkurang jumlahnya.",
                    ],
                    [
                        'q' => "What does the adjective 'convenient' mean?",
                        'options' => ['nyaman dan mudah digunakan' => true, 'rumit dan sulit' => false, 'mahal harganya' => false, 'jauh letaknya' => false],
                        'why' => "'Convenient' berarti 'nyaman' atau praktis, mudah digunakan.",
                    ],
                    [
                        'q' => 'Read the passage: "Doni volunteers at an animal shelter in Bandung every weekend. He feeds the cats and dogs and cleans their cages before lunchtime. Many of the animals were rescued from the street and needed medical care when they arrived. Last week, a family adopted two kittens that Doni had taken care of for months, and he felt very proud." According to the passage, why did many of the animals need medical care when they arrived?',
                        'options' => ['karena mereka diselamatkan dari jalanan' => true, 'karena mereka sakit karena makanan' => false, 'karena mereka terlalu tua' => false, 'karena cuaca dingin' => false],
                        'why' => 'Menurut teks, hewan-hewan itu diselamatkan dari jalanan sehingga membutuhkan perawatan medis.',
                    ],
                    [
                        'q' => 'Read the passage: "Doni volunteers at an animal shelter in Bandung every weekend. He feeds the cats and dogs and cleans their cages before lunchtime. Many of the animals were rescued from the street and needed medical care when they arrived. Last week, a family adopted two kittens that Doni had taken care of for months, and he felt very proud." What happened last week according to the passage?',
                        'options' => ['sebuah keluarga mengadopsi dua anak kucing' => true, 'Doni berhenti menjadi sukarelawan' => false, 'penampungan hewan ditutup' => false, 'Doni mengadopsi seekor anjing' => false],
                        'why' => 'Menurut teks, minggu lalu sebuah keluarga mengadopsi dua anak kucing yang dirawat Doni.',
                    ],
                    [
                        'q' => "What does the idiom 'hit the books' mean?",
                        'options' => ['belajar dengan sungguh-sungguh' => true, 'membeli buku baru' => false, 'membuang buku lama' => false, 'meminjam buku dari perpustakaan' => false],
                        'why' => "'Hit the books' adalah idiom yang berarti belajar dengan giat/sungguh-sungguh.",
                    ],
                    [
                        'q' => "What does the phrasal verb 'look after' mean?",
                        'options' => ['merawat atau menjaga seseorang/sesuatu' => true, 'mencari sesuatu yang hilang' => false, 'menghindari seseorang' => false, 'mengejar seseorang' => false],
                        'why' => "'Look after' berarti 'merawat' atau 'menjaga'.",
                    ],
                    [
                        'q' => "What does the collocation 'take a break' mean?",
                        'options' => ['beristirahat sejenak dari suatu kegiatan' => true, 'memulai pekerjaan baru' => false, 'menyelesaikan tugas dengan cepat' => false, 'membatalkan rencana' => false],
                        'why' => "'Take a break' berarti beristirahat sejenak dari suatu kegiatan atau pekerjaan.",
                    ],
                    [
                        'q' => "What does the phrasal verb 'run into' mean?",
                        'options' => ['bertemu seseorang secara tidak sengaja' => true, 'menabrakkan mobil dengan sengaja' => false, 'menghindari suatu tempat' => false, 'berlari menjauh dari seseorang' => false],
                        'why' => "'Run into' berarti bertemu seseorang secara kebetulan/tidak sengaja.",
                    ],
                    [
                        'q' => "Read the passage: \"When Sarah moved to a new city for her job, she found it hard to make friends at first. She decided to look after her neighbor's dog while he was away, and that's when she ran into a former classmate at the park. They decided to hit the books together for an upcoming certification exam. After weeks of studying, they both passed and celebrated by taking a break at the beach.\" Why did Sarah find it hard to make friends when she first moved?",
                        'options' => ['karena dia baru pindah ke kota yang baru' => true, 'karena dia tidak suka bertemu orang' => false, 'karena dia sibuk bekerja lembur' => false, 'karena dia tidak bisa berbahasa setempat' => false],
                        'why' => 'Menurut teks, Sarah baru pindah ke kota baru sehingga sulit mencari teman pada awalnya.',
                    ],
                    [
                        'q' => "Read the passage: \"When Sarah moved to a new city for her job, she found it hard to make friends at first. She decided to look after her neighbor's dog while he was away, and that's when she ran into a former classmate at the park. They decided to hit the books together for an upcoming certification exam. After weeks of studying, they both passed and celebrated by taking a break at the beach.\" How did Sarah and her former classmate spend time together after meeting again?",
                        'options' => ['mereka belajar bersama untuk ujian sertifikasi' => true, 'mereka bekerja di kantor yang sama' => false, 'mereka merawat anjing bersama setiap hari' => false, 'mereka pindah rumah bersama' => false],
                        'why' => 'Menurut teks, mereka belajar bersama (hit the books) untuk ujian sertifikasi yang akan datang.',
                    ],
                    [
                        'q' => "What does the academic word 'meticulous' mean?",
                        'options' => ['sangat teliti dan cermat dalam melakukan sesuatu' => true, 'ceroboh dan tergesa-gesa' => false, 'malas dan tidak peduli' => false, 'ragu-ragu dan tidak yakin' => false],
                        'why' => "'Meticulous' berarti sangat teliti, cermat, dan memperhatikan detail.",
                    ],
                    [
                        'q' => "What does the academic word 'inevitable' mean?",
                        'options' => ['tidak dapat dihindari, pasti terjadi' => true, 'sangat jarang terjadi' => false, 'dapat dicegah dengan mudah' => false, 'tidak penting untuk dipikirkan' => false],
                        'why' => "'Inevitable' berarti sesuatu yang pasti terjadi dan tidak dapat dihindari.",
                    ],
                    [
                        'q' => "What does the academic word 'coherent' mean?",
                        'options' => ['logis dan mudah dipahami karena tersusun rapi' => true, 'membingungkan dan tidak jelas' => false, 'sangat panjang dan bertele-tele' => false, 'ditulis secara sembarangan' => false],
                        'why' => "'Coherent' berarti logis, jelas, dan tersusun dengan baik sehingga mudah dipahami.",
                    ],
                    [
                        'q' => "What does the academic verb 'facilitate' mean?",
                        'options' => ['mempermudah atau memperlancar suatu proses' => true, 'mempersulit suatu proses' => false, 'menghentikan suatu proses' => false, 'mengabaikan suatu proses' => false],
                        'why' => "'Facilitate' berarti mempermudah atau membantu memperlancar sesuatu.",
                    ],
                    [
                        'q' => "Read the passage: \"Neuroscientists have increasingly focused on the relationship between sleep quality and cognitive performance among university students. A comprehensive study conducted across several institutions revealed that students who slept fewer than six hours per night exhibited significantly diminished memory retention. Researchers meticulously monitored participants' sleep patterns using wearable devices over a twelve-week period to ensure accurate data collection. The findings indicate that adequate sleep is not merely beneficial but essential for optimal academic achievement. Nevertheless, some scholars caution that the study's sample size may limit the generalizability of its conclusions to broader populations. Universities are now being encouraged to facilitate healthier sleep habits through revised class scheduling policies.\" How did researchers ensure accurate data collection in the sleep study?",
                        'options' => ['dengan memantau pola tidur peserta menggunakan perangkat wearable selama dua belas minggu' => true, 'dengan mewawancarai peserta setiap hari' => false, 'dengan meminta peserta mengisi kuesioner sekali saja' => false, 'dengan mengamati peserta secara langsung tanpa alat' => false],
                        'why' => 'Menurut teks, peneliti memantau pola tidur peserta secara cermat menggunakan perangkat wearable selama dua belas minggu.',
                    ],
                    [
                        'q' => "Read the passage: \"Neuroscientists have increasingly focused on the relationship between sleep quality and cognitive performance among university students. A comprehensive study conducted across several institutions revealed that students who slept fewer than six hours per night exhibited significantly diminished memory retention. Researchers meticulously monitored participants' sleep patterns using wearable devices over a twelve-week period to ensure accurate data collection. The findings indicate that adequate sleep is not merely beneficial but essential for optimal academic achievement. Nevertheless, some scholars caution that the study's sample size may limit the generalizability of its conclusions to broader populations. Universities are now being encouraged to facilitate healthier sleep habits through revised class scheduling policies.\" What limitation do some scholars point out about the study?",
                        'options' => ['ukuran sampel penelitian mungkin membatasi generalisasi kesimpulannya' => true, 'penelitian dilakukan terlalu singkat' => false, 'para peneliti tidak memiliki cukup dana' => false, 'universitas menolak menerapkan hasil penelitian' => false],
                        'why' => 'Menurut teks, sebagian ilmuwan mengingatkan bahwa ukuran sampel penelitian dapat membatasi generalisasi kesimpulan ke populasi yang lebih luas.',
                    ],
                ],
                category: 'test',
                timeLimitSeconds: 40 * 60,
                description: '30 soal kosakata dan pemahaman bacaan dari level Beginner hingga Advanced, tersusun bertahap dari mudah ke sulit, dengan waktu 40 menit.',
            );
        });
    }

    /**
     * @param  array<int, array{q:string, options:array<string,bool>, why:string}>  $questions
     */
    private function create(
        ?string $moduleSlug,
        string $title,
        string $difficulty,
        ?string $level,
        array $questions,
        string $category = 'quiz',
        ?int $timeLimitSeconds = null,
        ?string $description = null,
    ): void {
        $module = $moduleSlug ? LearningModule::where('slug', $moduleSlug)->first() : null;

        if ($moduleSlug && ! $module) {
            return;
        }

        $quiz = Quiz::updateOrCreate(['slug' => str($title)->slug()->toString()], [
            'learning_module_id' => $module?->id,
            'title' => $title,
            'description' => $description ?? "Uji pemahamanmu tentang {$module?->name}.",
            'level' => $level,
            'category' => $category,
            'difficulty' => $difficulty,
            'time_limit_seconds' => $timeLimitSeconds ?? (count($questions) * 45),
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
