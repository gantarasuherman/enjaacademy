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

            $this->create('english-grammar', 'Kuis Fondasi Grammar: Parts of Speech, Tense, & Struktur Kalimat', 'medium', 'B1', [
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
            ]);

            $this->create('english-vocabulary', 'Travel Vocabulary', 'easy', 'A2', [
                ['q' => 'What does "luggage" mean?', 'options' => ['bagasi' => true, 'bandara' => false, 'tiket' => false, 'penundaan' => false], 'why' => '"Luggage" refers to the bags you travel with.'],
                ['q' => 'The flight has a two-hour ___.', 'options' => ['delay' => true, 'departure' => false, 'luggage' => false, 'airport' => false], 'why' => 'A "delay" is a postponement.'],
                ['q' => 'Which word means "keberangkatan"?', 'options' => ['departure' => true, 'arrival' => false, 'boarding' => false, 'terminal' => false], 'why' => '"Departure" is the act of leaving.'],
            ]);

            $this->create('katakana', 'Kuis Katakana Dasar', 'easy', 'N5', [
                ['q' => 'Bagaimana cara membaca huruf「ア」?', 'options' => ['a' => true, 'i' => false, 'u' => false, 'e' => false], 'why' => '「ア」adalah versi katakana dari「あ」, dibaca "a".'],
                ['q' => 'Huruf apa yang dipakai untuk menulis kata serapan seperti「コーヒー」?', 'options' => ['Katakana' => true, 'Hiragana' => false, 'Kanji' => false, 'Romaji' => false], 'why' => 'Katakana khusus dipakai untuk kata serapan asing, nama asing, dan onomatope.'],
                ['q' => 'Bagaimana cara membaca huruf「カ」?', 'options' => ['ka' => true, 'sa' => false, 'ta' => false, 'na' => false], 'why' => '「カ」adalah versi katakana dari「か」, dibaca "ka".'],
                ['q' => '「メニュー」dibaca...?', 'options' => ['menyuu' => true, 'menuu' => false, 'meniyuu' => false, 'menyu' => false], 'why' => 'ニュ adalah gabungan yōon, dibaca "nyu" bukan "niyu".'],
            ]);

            $this->create('kosakata-jepang', 'Kuis Kosakata Dasar', 'easy', 'N5', [
                ['q' => '「先生」artinya?', 'options' => ['guru' => true, 'murid' => false, 'sekolah' => false, 'teman' => false], 'why' => '「先生」(sensei) berarti guru atau pengajar.'],
                ['q' => '「水」dibaca?', 'options' => ['mizu' => true, 'hi' => false, 'ki' => false, 'tsuki' => false], 'why' => '「水」(mizu) berarti air.'],
                ['q' => '「食べる」artinya?', 'options' => ['makan' => true, 'minum' => false, 'tidur' => false, 'berjalan' => false], 'why' => '「食べる」(taberu) adalah kata kerja "makan".'],
                ['q' => '「友達」artinya?', 'options' => ['teman' => true, 'keluarga' => false, 'tetangga' => false, 'rekan kerja' => false], 'why' => '「友達」(tomodachi) berarti teman.'],
                ['q' => '「今日」dibaca?', 'options' => ['kyou' => true, 'ashita' => false, 'kinou' => false, 'mainichi' => false], 'why' => '「今日」(kyou) berarti "hari ini".'],
            ]);

            $this->create('tata-bahasa-jepang', 'Kuis Partikel Dasar', 'medium', 'N5', [
                ['q' => '私＿学生です。partikel yang tepat?', 'options' => ['は' => true, 'を' => false, 'に' => false, 'で' => false], 'why' => '「は」menandai topik kalimat.'],
                ['q' => '図書館＿本を読みます。', 'options' => ['で' => true, 'を' => false, 'へ' => false, 'の' => false], 'why' => '「で」menunjukkan tempat aktivitas berlangsung.'],
                ['q' => '明日、東京＿行きます。', 'options' => ['へ' => true, 'を' => false, 'が' => false, 'と' => false], 'why' => '「へ」menunjukkan arah/tujuan perpindahan.'],
                ['q' => 'これは私＿本です。', 'options' => ['の' => true, 'は' => false, 'を' => false, 'に' => false], 'why' => '「の」menunjukkan kepemilikan.'],
                ['q' => '七時＿起きます。', 'options' => ['に' => true, 'で' => false, 'を' => false, 'へ' => false], 'why' => '「に」dipakai untuk menandai waktu spesifik.'],
            ]);

            $this->create('percakapan-jepang', 'Kuis Frasa Percakapan', 'easy', 'N5', [
                ['q' => '「はじめまして」artinya?', 'options' => ['salam kenal' => true, 'terima kasih' => false, 'selamat tinggal' => false, 'permisi' => false], 'why' => '「はじめまして」diucapkan saat bertemu seseorang untuk pertama kali.'],
                ['q' => 'Bagaimana menjawab「お元気ですか」dengan sopan?', 'options' => ['はい、元気です' => true, 'いいえ、元気です' => false, 'わかりません' => false, 'さようなら' => false], 'why' => '「はい、元気です」berarti "Ya, saya sehat/baik".'],
                ['q' => '「すみません」dipakai untuk?', 'options' => ['permisi dan minta maaf' => true, 'hanya permisi' => false, 'hanya minta maaf' => false, 'ucapan selamat' => false], 'why' => '「すみません」serbaguna: bisa berarti permisi maupun minta maaf.'],
                ['q' => 'Cara sopan memesan makanan di restoran?', 'options' => ['＿をください' => true, '＿をあげます' => false, '＿をもらいます' => false, '＿をします' => false], 'why' => '「〜をください」berarti "tolong beri saya〜", cocok untuk memesan.'],
            ]);

            $this->create('menulis-jepang', 'Kuis Aksara dan Penulisan', 'medium', 'N5', [
                ['q' => 'Urutan goresan kanji umumnya dimulai dari?', 'options' => ['atas ke bawah, kiri ke kanan' => true, 'bawah ke atas' => false, 'kanan ke kiri' => false, 'acak, bebas' => false], 'why' => 'Aturan dasar urutan goresan: atas dulu, lalu kiri sebelum kanan.'],
                ['q' => 'Aksara apa yang dipakai untuk partikel tata bahasa (は, を, に)?', 'options' => ['Hiragana' => true, 'Katakana' => false, 'Kanji' => false, 'Romaji' => false], 'why' => 'Partikel selalu ditulis dengan hiragana.'],
                ['q' => 'Nama asing seperti "Budi" biasanya ditulis dengan aksara?', 'options' => ['Katakana' => true, 'Hiragana' => false, 'Kanji' => false, 'Romaji' => false], 'why' => 'Nama/kata asing ditulis dengan katakana.'],
                ['q' => 'Furigana dipakai untuk?', 'options' => ['bantuan bacaan di atas kanji sulit' => true, 'menulis partikel' => false, 'menulis nama asing' => false, 'tanda baca' => false], 'why' => 'Furigana adalah huruf kecil hiragana di atas/samping kanji untuk membantu bacaan.'],
            ]);

            $this->create('membaca-jepang', 'Kuis Pemahaman Cerita Rakyat', 'medium', 'N4', [
                ['q' => 'マリン・クンダンの母は何をして生活していましたか。', 'options' => ['魚を取って売っていた' => true, '店で働いていた' => false, '先生だった' => false, '農業をしていた' => false], 'why' => 'Ibu Malin Kundang menangkap ikan dan menjualnya untuk menghidupi keluarga.'],
                ['q' => 'バワン・プティはなぜ小さいかぼちゃを選びましたか。', 'options' => ['謙虚だから' => true, '大きいのが嫌いだから' => false, 'おばあさんに言われたから' => false, '小さい方が軽いから' => false], 'why' => 'Bawang Putih memilih labu kecil karena sifatnya yang rendah hati.'],
                ['q' => 'ティムン・マスは鬼から逃げるために何を使いましたか。', 'options' => ['魔法の袋' => true, '剣' => false, '馬' => false, '船' => false], 'why' => 'Timun Mas menggunakan empat kantong ajaib untuk melarikan diri dari raksasa.'],
                ['q' => 'マリンは最後にどうなりましたか。', 'options' => ['石になった' => true, 'お金持ちになった' => false, '国に帰った' => false, '船長になった' => false], 'why' => 'Malin Kundang dikutuk menjadi batu setelah menyangkal ibunya sendiri.'],
            ]);

            $this->create('jlpt', 'Kuis Tinjauan N5', 'medium', 'N5', [
                ['q' => '「あ」は何行ですか。', 'options' => ['あ行' => true, 'か行' => false, 'さ行' => false, 'た行' => false], 'why' => '「あ」adalah huruf pertama pada baris vokal (あ行).'],
                ['q' => '「山」の読み方は？', 'options' => ['やま' => true, 'かわ' => false, 'うみ' => false, 'そら' => false], 'why' => '「山」(yama) berarti gunung.'],
                ['q' => '「毎日」の意味は？', 'options' => ['setiap hari' => true, 'setiap minggu' => false, 'setiap bulan' => false, 'setiap tahun' => false], 'why' => '「毎日」(mainichi) berarti "setiap hari".'],
                ['q' => '「〜てください」の使い方は？', 'options' => ['permintaan sopan' => true, 'larangan' => false, 'pertanyaan' => false, 'pujian' => false], 'why' => '「〜てください」dipakai untuk meminta seseorang melakukan sesuatu dengan sopan.'],
                ['q' => '「五」の読み方は？', 'options' => ['ご' => true, 'よん' => false, 'はち' => false, 'きゅう' => false], 'why' => '「五」(go) berarti angka lima.'],
            ]);

            $this->create('bahasa-inggris-umum', 'Kuis Bahasa Inggris Dasar', 'easy', 'A1', [
                ['q' => "How do you say 'terima kasih' in English?", 'options' => ['Thank you' => true, 'Sorry' => false, 'Please' => false, 'Excuse me' => false], 'why' => "'Thank you' adalah ungkapan terima kasih dalam bahasa Inggris."],
                ['q' => 'Choose the correct greeting for the morning:', 'options' => ['Good morning' => true, 'Good night' => false, 'Good evening' => false, 'Goodbye' => false], 'why' => "'Good morning' dipakai untuk menyapa di pagi hari."],
                ['q' => "'Family' artinya?", 'options' => ['keluarga' => true, 'teman' => false, 'tetangga' => false, 'rekan kerja' => false], 'why' => "'Family' berarti keluarga."],
                ['q' => "Pilih kata kerja yang tepat: 'I ___ to school every day.'", 'options' => ['go' => true, 'goes' => false, 'going' => false, 'gone' => false], 'why' => 'Subjek "I" memakai bentuk dasar kata kerja dalam simple present.'],
                ['q' => "'Monday' adalah hari?", 'options' => ['Senin' => true, 'Selasa' => false, 'Minggu' => false, 'Sabtu' => false], 'why' => "'Monday' berarti hari Senin."],
            ]);

            $this->create('reading', 'Kuis Pemahaman Cerita Rakyat (Inggris)', 'medium', 'B1', [
                ['q' => "Why did Malin Kundang's mother curse him?", 'options' => ['He refused to acknowledge her' => true, 'He stole money from her' => false, 'He left home without saying goodbye' => false, 'He married without her blessing' => false], 'why' => "Malin's mother cursed him after he publicly denied her as his mother."],
                ['q' => 'What did Bawang Putih choose from the old woman?', 'options' => ['The small pumpkin' => true, 'The big pumpkin' => false, 'Neither pumpkin' => false, 'Both pumpkins' => false], 'why' => 'Being humble, Bawang Putih chose the small pumpkin, which turned out to be full of treasure.'],
                ['q' => 'What did Timun Mas use to escape the giant?', 'options' => ['Four magic bags' => true, 'A magic sword' => false, 'A hidden boat' => false, 'A talking animal' => false], 'why' => 'Timun Mas used cucumber seeds, needles, salt, and shrimp paste from her magic bags.'],
                ['q' => 'What happened when Bawang Merah opened the big pumpkin?', 'options' => ['Snakes and insects came out' => true, 'It was full of gold' => false, 'It was empty' => false, 'It turned to stone' => false], 'why' => 'Her greed was punished — the big pumpkin contained dangerous snakes and insects instead of treasure.'],
            ]);

            $this->create('listening', 'Kuis Pemahaman Menyimak', 'medium', 'B1', [
                ['q' => 'In the airport announcement, which gate is boarding?', 'options' => ['Gate 12' => true, 'Gate 15' => false, 'Gate 20' => false, 'Gate 8' => false], 'why' => 'The announcement states boarding begins at gate 12.'],
                ['q' => 'In the restaurant dialogue, what did the customer order to drink?', 'options' => ['A glass of water' => true, 'Coffee' => false, 'Orange juice' => false, 'Tea' => false], 'why' => 'The customer simply asked for a glass of water.'],
                ['q' => 'In the voicemail, who is calling?', 'options' => ['Sarah from Bright Solutions' => true, 'Mr. Andi' => false, 'A restaurant manager' => false, 'A recruiter' => false], 'why' => 'Sarah introduces herself as calling from Bright Solutions.'],
                ['q' => 'What is the weather like in the afternoon in the forecast?', 'options' => ['Sunny' => true, 'Rainy' => false, 'Snowy' => false, 'Stormy' => false], 'why' => 'The forecast says the sky clears up and becomes sunny by the afternoon.'],
            ]);

            $this->create('speaking', 'Kuis Frasa Speaking', 'medium', 'B1', [
                ['q' => 'Which sound requires you to bite your lower lip lightly?', 'options' => ['V' => true, 'B' => false, 'T' => false, 'D' => false], 'why' => 'The /v/ sound is made by touching the upper teeth to the lower lip.'],
                ['q' => 'Which is a polite way to start small talk about the weather?', 'options' => ["The weather's been great this week, hasn't it?" => true, 'Give me the weather.' => false, 'Weather now?' => false, 'Is hot.' => false], 'why' => 'A tag question like this is a natural, polite way to open small talk.'],
                ['q' => 'Which phrase is used to summarize a presentation?', 'options' => ['To summarize, our key takeaway is...' => true, 'The end.' => false, 'Bye now.' => false, "That's all I know." => false], 'why' => '"To summarize" signals a formal wrap-up of key points.'],
                ['q' => "How do you politely ask about someone's job?", 'options' => ['What do you do for a living?' => true, 'How much money do you make?' => false, 'Do you even work?' => false, 'What is your salary?' => false], 'why' => '"What do you do for a living?" is the standard polite way to ask about occupation.'],
            ]);

            $this->create('writing', 'Kuis Struktur Menulis', 'medium', 'B1', [
                ['q' => 'What is the correct structure for a simple sentence?', 'options' => ['Subject + Verb + Object' => true, 'Object + Verb + Subject' => false, 'Verb only' => false, 'Object only' => false], 'why' => 'English simple sentences follow Subject-Verb-Object order.'],
                ['q' => 'What should come first in a paragraph?', 'options' => ['Topic sentence' => true, 'Concluding sentence' => false, 'A random detail' => false, 'A question' => false], 'why' => 'The topic sentence states the main idea before supporting details.'],
                ['q' => 'Which phrase is used to start a formal email?', 'options' => ['Dear Mr./Ms. [Name],' => true, 'Hey!' => false, 'Yo,' => false, "What's up," => false], 'why' => 'Formal emails begin with "Dear" plus the recipient\'s title and name.'],
                ['q' => 'What is the typical structure of narrative writing?', 'options' => ['Orientation, events, climax, resolution' => true, 'Just a list of facts' => false, 'Only dialogue' => false, 'Random sentences' => false], 'why' => 'A narrative follows a clear story arc: setup, events, climax, and resolution.'],
            ]);

            $this->create('toefl', 'Kuis Struktur dan Kosakata TOEFL', 'hard', 'B2', [
                ['q' => "'___ the rain, they went for a walk.' Which word fits?", 'options' => ['Despite' => true, 'Because' => false, 'Since' => false, 'Unless' => false], 'why' => '"Despite" is followed by a noun phrase, not a full clause.'],
                ['q' => "Find the error: 'She don't like spicy food.'", 'options' => ["'don't' should be 'doesn't'" => true, "'like' should be 'likes'" => false, "'food' should be 'foods'" => false, "'spicy' should be 'spicier'" => false], 'why' => 'Third-person singular subjects ("she") require "doesn\'t" in negative sentences.'],
                ['q' => "What does 'abundant' mean?", 'options' => ['berlimpah' => true, 'langka' => false, 'kecil' => false, 'kontroversial' => false], 'why' => "'Abundant' means existing in large quantities."],
                ['q' => 'What is the main idea of a passage about the Amazon rainforest facing deforestation?', 'options' => ['Its importance and the threats it faces' => true, 'The history of Brazil' => false, 'A list of animal species only' => false, 'Tourism in South America' => false], 'why' => 'The passage centers on the rainforest\'s ecological importance and deforestation threats.'],
            ]);

            $this->create('ielts', 'Kuis Format IELTS', 'hard', 'B2', [
                ['q' => 'In IELTS Writing Task 1, what should you avoid?', 'options' => ['Personal opinions' => true, 'Describing data' => false, 'Trend words' => false, 'An overview sentence' => false], 'why' => 'Task 1 requires an objective, factual description — not personal opinions.'],
                ['q' => 'Which phrase describes a stable trend in a graph?', 'options' => ['remained stable at...' => true, 'increased sharply' => false, 'in my opinion' => false, 'I believe that' => false], 'why' => '"Remained stable at" describes a flat, unchanging trend.'],
                ['q' => 'In the reading passage about coffee, which country is the largest producer?', 'options' => ['Brazil' => true, 'Vietnam' => false, 'Colombia' => false, 'Ethiopia' => false], 'why' => 'The passage states Brazil is the largest coffee producer today.'],
                ['q' => "What is a good response to 'Can you tell me about your hometown?' in Speaking Part 1?", 'options' => ['A short, natural descriptive answer' => true, 'A one-word answer' => false, 'A memorized script' => false, 'Silence' => false], 'why' => 'Part 1 rewards natural, conversational answers over memorized responses.'],
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
