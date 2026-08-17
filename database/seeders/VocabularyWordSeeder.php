<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Language;
use App\Models\VocabularyWord;
use App\Models\VocabularyWordExample;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * A dictionary-scale English vocabulary bank for the Daily Quiz feature.
 * The 50 words below are the original starter batch proving the
 * schema/pipeline end-to-end. Growth towards the ±21,000-word target happens
 * incrementally via flat data files under `data/vocabulary/{level-slug}.php`
 * (one array of entries per file, same entry shape as below) — content
 * waves drop new files there without ever touching this class, and
 * `mergeExternal()` below dedupes by word so overlapping batches are safe.
 */
class VocabularyWordSeeder extends Seeder
{
    public function run(): void
    {
        $language = Language::where('slug', 'english')->first();

        if (! $language) {
            $this->command?->warn('  vocabulary_words: skipped, "english" language not found — run LearningContentSeeder first.');

            return;
        }

        $count = 0;

        foreach ($this->allWords() as $level => $entries) {
            foreach ($entries as $entry) {
                $word = VocabularyWord::updateOrCreate(
                    ['language_id' => $language->id, 'word' => $entry['word']],
                    [
                        'phonetic' => $entry['phonetic'] ?? null,
                        'part_of_speech' => $entry['pos'] ?? null,
                        'meaning_id' => $entry['meaning_id'],
                        'meaning_en' => $entry['meaning_en'],
                        'level' => $level,
                        'synonyms' => $entry['synonyms'] ?? null,
                        'antonyms' => $entry['antonyms'] ?? null,
                        'collocations' => $entry['collocations'] ?? null,
                    ],
                );

                $word->examples()->delete();

                foreach (array_values($entry['examples']) as $i => $example) {
                    VocabularyWordExample::create([
                        'vocabulary_word_id' => $word->id,
                        'sentence_en' => $example['en'],
                        'sentence_id' => $example['id'] ?? null,
                        'sort_order' => $i,
                    ]);
                }

                $count++;
            }
        }

        $this->command?->info("  vocabulary_words: {$count} kata dibuat/diperbarui (target ±21.000 dikejar bertahap)");
    }

    /** @return array<string, array<int, array<string, mixed>>> */
    private function allWords(): array
    {
        $words = $this->words();

        foreach ($words as $level => $entries) {
            $words[$level] = $this->mergeExternal($level, $entries);
        }

        return $words;
    }

    /**
     * Loads every `data/vocabulary/{level-slug}*.php` file (each a flat array
     * of entries, same shape as `words()` below) if any exist, and appends
     * entries whose word isn't already present for this level yet —
     * case-insensitive, so an overlapping content wave is a safe no-op
     * rather than a duplicate row. Content waves land as separate numbered
     * files (`beginner.php`, `beginner2.php`, ...) rather than one
     * ever-growing file, so each wave stays independently reviewable.
     *
     * @param  array<int, array<string, mixed>>  $entries
     * @return array<int, array<string, mixed>>
     */
    private function mergeExternal(string $level, array $entries): array
    {
        $paths = glob(database_path('seeders/data/vocabulary/'.Str::slug($level).'*.php')) ?: [];
        sort($paths);

        $seen = collect($entries)->pluck('word')->map(fn ($w) => mb_strtolower($w))->flip();

        foreach ($paths as $path) {
            foreach ((array) require $path as $entry) {
                $key = mb_strtolower($entry['word']);

                if (isset($seen[$key])) {
                    continue;
                }

                $entries[] = $entry;
                $seen[$key] = true;
            }
        }

        return $entries;
    }

    /** @return array<string, array<int, array<string, mixed>>> */
    private function words(): array
    {
        return [
            'Beginner' => [
                [
                    'word' => 'apple', 'phonetic' => '/ˈæp.əl/', 'pos' => 'noun',
                    'meaning_id' => 'apel', 'meaning_en' => 'a round fruit with red, green, or yellow skin',
                    'synonyms' => [], 'antonyms' => [], 'collocations' => ['an apple a day', 'apple juice'],
                    'examples' => [
                        ['en' => 'She eats an apple every morning.', 'id' => 'Dia makan apel setiap pagi.'],
                        ['en' => 'The apple fell from the tree.', 'id' => 'Apel itu jatuh dari pohon.'],
                    ],
                ],
                [
                    'word' => 'run', 'phonetic' => '/rʌn/', 'pos' => 'verb',
                    'meaning_id' => 'berlari', 'meaning_en' => 'to move quickly on foot',
                    'synonyms' => ['jog', 'sprint'], 'antonyms' => ['walk', 'stop'], 'collocations' => ['run fast', 'run a marathon'],
                    'examples' => [
                        ['en' => 'He runs every morning before work.', 'id' => 'Dia berlari setiap pagi sebelum bekerja.'],
                        ['en' => 'The children ran across the park.', 'id' => 'Anak-anak berlari melintasi taman.'],
                    ],
                ],
                [
                    'word' => 'happy', 'phonetic' => '/ˈhæp.i/', 'pos' => 'adjective',
                    'meaning_id' => 'senang, bahagia', 'meaning_en' => 'feeling or showing pleasure',
                    'synonyms' => ['glad', 'cheerful'], 'antonyms' => ['sad', 'unhappy'], 'collocations' => ['happy birthday', 'happy family'],
                    'examples' => [
                        ['en' => 'She looked very happy at the party.', 'id' => 'Dia terlihat sangat senang di pesta itu.'],
                        ['en' => 'I am happy to see you again.', 'id' => 'Saya senang bertemu kamu lagi.'],
                    ],
                ],
                [
                    'word' => 'house', 'phonetic' => '/haʊs/', 'pos' => 'noun',
                    'meaning_id' => 'rumah', 'meaning_en' => 'a building where people live',
                    'synonyms' => ['home'], 'antonyms' => [], 'collocations' => ['big house', 'house key'],
                    'examples' => [
                        ['en' => 'They bought a new house last year.', 'id' => 'Mereka membeli rumah baru tahun lalu.'],
                        ['en' => 'My house is near the school.', 'id' => 'Rumah saya dekat sekolah.'],
                    ],
                ],
                [
                    'word' => 'eat', 'phonetic' => '/iːt/', 'pos' => 'verb',
                    'meaning_id' => 'makan', 'meaning_en' => 'to put food into the mouth and swallow it',
                    'synonyms' => ['consume'], 'antonyms' => [], 'collocations' => ['eat breakfast', 'eat out'],
                    'examples' => [
                        ['en' => 'We eat dinner at seven every day.', 'id' => 'Kami makan malam jam tujuh setiap hari.'],
                        ['en' => 'She doesn\'t eat meat.', 'id' => 'Dia tidak makan daging.'],
                    ],
                ],
                [
                    'word' => 'water', 'phonetic' => '/ˈwɔː.tər/', 'pos' => 'noun',
                    'meaning_id' => 'air', 'meaning_en' => 'a clear liquid needed by all living things',
                    'synonyms' => [], 'antonyms' => [], 'collocations' => ['drink water', 'cold water'],
                    'examples' => [
                        ['en' => 'Please drink more water every day.', 'id' => 'Tolong minum lebih banyak air setiap hari.'],
                        ['en' => 'The glass is full of water.', 'id' => 'Gelas itu penuh dengan air.'],
                    ],
                ],
                [
                    'word' => 'big', 'phonetic' => '/bɪɡ/', 'pos' => 'adjective',
                    'meaning_id' => 'besar', 'meaning_en' => 'large in size or amount',
                    'synonyms' => ['large', 'huge'], 'antonyms' => ['small', 'tiny'], 'collocations' => ['big city', 'big problem'],
                    'examples' => [
                        ['en' => 'They live in a big city.', 'id' => 'Mereka tinggal di kota besar.'],
                        ['en' => 'That is a big decision to make.', 'id' => 'Itu adalah keputusan besar untuk diambil.'],
                    ],
                ],
                [
                    'word' => 'friend', 'phonetic' => '/frɛnd/', 'pos' => 'noun',
                    'meaning_id' => 'teman', 'meaning_en' => 'a person you know well and like',
                    'synonyms' => ['buddy', 'pal'], 'antonyms' => ['enemy'], 'collocations' => ['best friend', 'make friends'],
                    'examples' => [
                        ['en' => 'She is my best friend.', 'id' => 'Dia adalah teman terbaik saya.'],
                        ['en' => 'I made a lot of friends at school.', 'id' => 'Saya mendapat banyak teman di sekolah.'],
                    ],
                ],
                [
                    'word' => 'go', 'phonetic' => '/ɡoʊ/', 'pos' => 'verb',
                    'meaning_id' => 'pergi', 'meaning_en' => 'to move or travel from one place to another',
                    'synonyms' => ['move', 'travel'], 'antonyms' => ['stay', 'come'], 'collocations' => ['go home', 'go shopping'],
                    'examples' => [
                        ['en' => 'We go to school by bus.', 'id' => 'Kami pergi ke sekolah naik bus.'],
                        ['en' => 'Let\'s go to the beach this weekend.', 'id' => 'Ayo pergi ke pantai akhir pekan ini.'],
                    ],
                ],
                [
                    'word' => 'book', 'phonetic' => '/bʊk/', 'pos' => 'noun',
                    'meaning_id' => 'buku', 'meaning_en' => 'a set of printed pages fixed together for reading',
                    'synonyms' => [], 'antonyms' => [], 'collocations' => ['read a book', 'text book'],
                    'examples' => [
                        ['en' => 'I am reading an interesting book.', 'id' => 'Saya sedang membaca buku yang menarik.'],
                        ['en' => 'She borrowed a book from the library.', 'id' => 'Dia meminjam buku dari perpustakaan.'],
                    ],
                ],
            ],
            'Elementary' => [
                [
                    'word' => 'decide', 'phonetic' => '/dɪˈsaɪd/', 'pos' => 'verb',
                    'meaning_id' => 'memutuskan', 'meaning_en' => 'to choose something after thinking about it',
                    'synonyms' => ['choose', 'determine'], 'antonyms' => ['hesitate'], 'collocations' => ['decide on', 'decide to do'],
                    'examples' => [
                        ['en' => 'We decided to travel next month.', 'id' => 'Kami memutuskan untuk bepergian bulan depan.'],
                        ['en' => 'She couldn\'t decide what to order.', 'id' => 'Dia tidak bisa memutuskan apa yang akan dipesan.'],
                    ],
                ],
                [
                    'word' => 'weather', 'phonetic' => '/ˈweð.ər/', 'pos' => 'noun',
                    'meaning_id' => 'cuaca', 'meaning_en' => 'the condition of the atmosphere at a place and time',
                    'synonyms' => ['climate'], 'antonyms' => [], 'collocations' => ['bad weather', 'weather forecast'],
                    'examples' => [
                        ['en' => 'The weather is nice today.', 'id' => 'Cuacanya bagus hari ini.'],
                        ['en' => 'We checked the weather before the trip.', 'id' => 'Kami memeriksa cuaca sebelum perjalanan.'],
                    ],
                ],
                [
                    'word' => 'expensive', 'phonetic' => '/ɪkˈspen.sɪv/', 'pos' => 'adjective',
                    'meaning_id' => 'mahal', 'meaning_en' => 'costing a lot of money',
                    'synonyms' => ['costly'], 'antonyms' => ['cheap', 'affordable'], 'collocations' => ['too expensive', 'expensive car'],
                    'examples' => [
                        ['en' => 'This restaurant is too expensive for us.', 'id' => 'Restoran ini terlalu mahal untuk kami.'],
                        ['en' => 'She bought an expensive dress for the party.', 'id' => 'Dia membeli gaun mahal untuk pesta itu.'],
                    ],
                ],
                [
                    'word' => 'improve', 'phonetic' => '/ɪmˈpruːv/', 'pos' => 'verb',
                    'meaning_id' => 'meningkatkan, memperbaiki', 'meaning_en' => 'to make or become better',
                    'synonyms' => ['enhance', 'develop'], 'antonyms' => ['worsen'], 'collocations' => ['improve skills', 'improve quality'],
                    'examples' => [
                        ['en' => 'He wants to improve his English.', 'id' => 'Dia ingin meningkatkan bahasa Inggrisnya.'],
                        ['en' => 'The new manager improved the team\'s performance.', 'id' => 'Manajer baru itu memperbaiki kinerja tim.'],
                    ],
                ],
                [
                    'word' => 'journey', 'phonetic' => '/ˈdʒɜː.ni/', 'pos' => 'noun',
                    'meaning_id' => 'perjalanan', 'meaning_en' => 'an act of traveling from one place to another',
                    'synonyms' => ['trip', 'voyage'], 'antonyms' => [], 'collocations' => ['long journey', 'journey home'],
                    'examples' => [
                        ['en' => 'The journey took almost six hours.', 'id' => 'Perjalanan itu memakan waktu hampir enam jam.'],
                        ['en' => 'It was a tiring but exciting journey.', 'id' => 'Itu adalah perjalanan yang melelahkan namun menyenangkan.'],
                    ],
                ],
                [
                    'word' => 'nervous', 'phonetic' => '/ˈnɜː.vəs/', 'pos' => 'adjective',
                    'meaning_id' => 'gugup, cemas', 'meaning_en' => 'worried and anxious about something',
                    'synonyms' => ['anxious', 'worried'], 'antonyms' => ['calm', 'relaxed'], 'collocations' => ['feel nervous', 'nervous about'],
                    'examples' => [
                        ['en' => 'She felt nervous before the interview.', 'id' => 'Dia merasa gugup sebelum wawancara.'],
                        ['en' => 'I get nervous when I speak in public.', 'id' => 'Saya jadi gugup saat berbicara di depan umum.'],
                    ],
                ],
                [
                    'word' => 'borrow', 'phonetic' => '/ˈbɒr.oʊ/', 'pos' => 'verb',
                    'meaning_id' => 'meminjam', 'meaning_en' => 'to take something with the intention of giving it back',
                    'synonyms' => ['lend (opposite direction)'], 'antonyms' => ['lend'], 'collocations' => ['borrow money', 'borrow a book'],
                    'examples' => [
                        ['en' => 'Can I borrow your pen for a moment?', 'id' => 'Bolehkah saya meminjam pulpenmu sebentar?'],
                        ['en' => 'He borrowed some money from his brother.', 'id' => 'Dia meminjam uang dari kakaknya.'],
                    ],
                ],
                [
                    'word' => 'crowded', 'phonetic' => '/ˈkraʊ.dɪd/', 'pos' => 'adjective',
                    'meaning_id' => 'ramai, penuh sesak', 'meaning_en' => 'full of people',
                    'synonyms' => ['packed', 'busy'], 'antonyms' => ['empty', 'quiet'], 'collocations' => ['crowded street', 'crowded bus'],
                    'examples' => [
                        ['en' => 'The market was very crowded on weekends.', 'id' => 'Pasar itu sangat ramai di akhir pekan.'],
                        ['en' => 'We avoided the crowded train.', 'id' => 'Kami menghindari kereta yang penuh sesak.'],
                    ],
                ],
                [
                    'word' => 'suggest', 'phonetic' => '/səˈdʒest/', 'pos' => 'verb',
                    'meaning_id' => 'menyarankan', 'meaning_en' => 'to put forward an idea for consideration',
                    'synonyms' => ['recommend', 'propose'], 'antonyms' => [], 'collocations' => ['suggest an idea', 'suggest that'],
                    'examples' => [
                        ['en' => 'She suggested we take a break.', 'id' => 'Dia menyarankan kita istirahat sejenak.'],
                        ['en' => 'Can you suggest a good restaurant?', 'id' => 'Bisakah kamu menyarankan restoran yang bagus?'],
                    ],
                ],
                [
                    'word' => 'comfortable', 'phonetic' => '/ˈkʌmf.tə.bəl/', 'pos' => 'adjective',
                    'meaning_id' => 'nyaman', 'meaning_en' => 'giving a pleasant feeling of ease',
                    'synonyms' => ['cozy'], 'antonyms' => ['uncomfortable'], 'collocations' => ['comfortable chair', 'feel comfortable'],
                    'examples' => [
                        ['en' => 'This sofa is really comfortable.', 'id' => 'Sofa ini benar-benar nyaman.'],
                        ['en' => 'I feel comfortable talking to her.', 'id' => 'Saya merasa nyaman berbicara dengannya.'],
                    ],
                ],
            ],
            'Intermediate' => [
                [
                    'word' => 'achieve', 'phonetic' => '/əˈtʃiːv/', 'pos' => 'verb',
                    'meaning_id' => 'mencapai', 'meaning_en' => 'to successfully complete or reach a goal',
                    'synonyms' => ['accomplish', 'attain'], 'antonyms' => ['fail'], 'collocations' => ['achieve a goal', 'achieve success'],
                    'examples' => [
                        ['en' => 'She achieved her goal of becoming a doctor.', 'id' => 'Dia mencapai tujuannya menjadi dokter.'],
                        ['en' => 'The team achieved great results this year.', 'id' => 'Tim itu mencapai hasil yang bagus tahun ini.'],
                    ],
                ],
                [
                    'word' => 'reliable', 'phonetic' => '/rɪˈlaɪ.ə.bəl/', 'pos' => 'adjective',
                    'meaning_id' => 'dapat diandalkan', 'meaning_en' => 'able to be trusted to do what is expected',
                    'synonyms' => ['dependable', 'trustworthy'], 'antonyms' => ['unreliable'], 'collocations' => ['reliable source', 'reliable person'],
                    'examples' => [
                        ['en' => 'He is a reliable employee.', 'id' => 'Dia adalah karyawan yang dapat diandalkan.'],
                        ['en' => 'We need reliable information before deciding.', 'id' => 'Kami membutuhkan informasi yang dapat diandalkan sebelum memutuskan.'],
                    ],
                ],
                [
                    'word' => 'opportunity', 'phonetic' => '/ˌɒp.əˈtʃuː.nə.ti/', 'pos' => 'noun',
                    'meaning_id' => 'kesempatan', 'meaning_en' => 'a chance to do something',
                    'synonyms' => ['chance'], 'antonyms' => [], 'collocations' => ['job opportunity', 'miss an opportunity'],
                    'examples' => [
                        ['en' => 'This job is a great opportunity for her.', 'id' => 'Pekerjaan ini adalah kesempatan besar baginya.'],
                        ['en' => 'Don\'t miss this opportunity to learn.', 'id' => 'Jangan lewatkan kesempatan ini untuk belajar.'],
                    ],
                ],
                [
                    'word' => 'increase', 'phonetic' => '/ɪnˈkriːs/', 'pos' => 'verb',
                    'meaning_id' => 'meningkat, menambah', 'meaning_en' => 'to become or make greater in size or amount',
                    'synonyms' => ['rise', 'grow'], 'antonyms' => ['decrease', 'reduce'], 'collocations' => ['increase in price', 'sharply increase'],
                    'examples' => [
                        ['en' => 'The price of rice increased last month.', 'id' => 'Harga beras meningkat bulan lalu.'],
                        ['en' => 'The company plans to increase production.', 'id' => 'Perusahaan berencana meningkatkan produksi.'],
                    ],
                ],
                [
                    'word' => 'consequence', 'phonetic' => '/ˈkɒn.sɪ.kwəns/', 'pos' => 'noun',
                    'meaning_id' => 'akibat, konsekuensi', 'meaning_en' => 'a result of a particular action or situation',
                    'synonyms' => ['result', 'effect'], 'antonyms' => ['cause'], 'collocations' => ['as a consequence', 'face consequences'],
                    'examples' => [
                        ['en' => 'He had to face the consequences of his choice.', 'id' => 'Dia harus menghadapi akibat dari pilihannya.'],
                        ['en' => 'The flood was a consequence of heavy rain.', 'id' => 'Banjir itu adalah akibat dari hujan lebat.'],
                    ],
                ],
                [
                    'word' => 'give up', 'phonetic' => null, 'pos' => 'phrasal verb',
                    'meaning_id' => 'menyerah', 'meaning_en' => 'to stop trying to do something',
                    'synonyms' => ['quit', 'surrender'], 'antonyms' => ['persist'], 'collocations' => ['give up hope', 'never give up'],
                    'examples' => [
                        ['en' => 'She never gives up, even when it\'s hard.', 'id' => 'Dia tidak pernah menyerah, bahkan saat sulit.'],
                        ['en' => 'Don\'t give up on your dreams.', 'id' => 'Jangan menyerah pada impianmu.'],
                    ],
                ],
                [
                    'word' => 'come across', 'phonetic' => null, 'pos' => 'phrasal verb',
                    'meaning_id' => 'menemukan secara tidak sengaja', 'meaning_en' => 'to find or meet by chance',
                    'synonyms' => ['encounter', 'stumble upon'], 'antonyms' => [], 'collocations' => ['come across a problem'],
                    'examples' => [
                        ['en' => 'I came across an old photo of us.', 'id' => 'Saya menemukan foto lama kami secara tidak sengaja.'],
                        ['en' => 'She came across an interesting article online.', 'id' => 'Dia menemukan artikel menarik secara tidak sengaja di internet.'],
                    ],
                ],
                [
                    'word' => 'break the ice', 'phonetic' => null, 'pos' => 'idiom',
                    'meaning_id' => 'mencairkan suasana', 'meaning_en' => 'to make people feel more relaxed in a social situation',
                    'synonyms' => [], 'antonyms' => [], 'collocations' => [],
                    'examples' => [
                        ['en' => 'He told a joke to break the ice.', 'id' => 'Dia melontarkan lelucon untuk mencairkan suasana.'],
                        ['en' => 'The game helped break the ice at the party.', 'id' => 'Permainan itu membantu mencairkan suasana di pesta.'],
                    ],
                ],
                [
                    'word' => 'significant', 'phonetic' => '/sɪɡˈnɪf.ɪ.kənt/', 'pos' => 'adjective',
                    'meaning_id' => 'penting, signifikan', 'meaning_en' => 'important or large enough to be noticed',
                    'synonyms' => ['important', 'notable'], 'antonyms' => ['insignificant'], 'collocations' => ['significant change', 'significant impact'],
                    'examples' => [
                        ['en' => 'There was a significant improvement in her grades.', 'id' => 'Ada peningkatan signifikan dalam nilainya.'],
                        ['en' => 'This decision will have a significant impact on the company.', 'id' => 'Keputusan ini akan berdampak signifikan pada perusahaan.'],
                    ],
                ],
                [
                    'word' => 'accurate', 'phonetic' => '/ˈæk.jə.rət/', 'pos' => 'adjective',
                    'meaning_id' => 'akurat, tepat', 'meaning_en' => 'correct and exact in every detail',
                    'synonyms' => ['precise', 'exact'], 'antonyms' => ['inaccurate'], 'collocations' => ['accurate information', 'accurate data'],
                    'examples' => [
                        ['en' => 'The report contains accurate data.', 'id' => 'Laporan itu berisi data yang akurat.'],
                        ['en' => 'We need an accurate translation of this document.', 'id' => 'Kami memerlukan terjemahan yang akurat dari dokumen ini.'],
                    ],
                ],
            ],
            'Upper-Intermediate' => [
                [
                    'word' => 'ambiguous', 'phonetic' => '/æmˈbɪɡ.ju.əs/', 'pos' => 'adjective',
                    'meaning_id' => 'ambigu, tidak jelas', 'meaning_en' => 'having more than one possible meaning',
                    'synonyms' => ['unclear', 'vague'], 'antonyms' => ['clear', 'unambiguous'], 'collocations' => ['ambiguous statement', 'ambiguous answer'],
                    'examples' => [
                        ['en' => 'His answer was ambiguous and confusing.', 'id' => 'Jawabannya ambigu dan membingungkan.'],
                        ['en' => 'The contract contains an ambiguous clause.', 'id' => 'Kontrak itu memuat klausul yang ambigu.'],
                    ],
                ],
                [
                    'word' => 'inevitable', 'phonetic' => '/ɪˈnev.ɪ.tə.bəl/', 'pos' => 'adjective',
                    'meaning_id' => 'tidak dapat dihindari', 'meaning_en' => 'certain to happen and unable to be avoided',
                    'synonyms' => ['unavoidable'], 'antonyms' => ['avoidable'], 'collocations' => ['inevitable outcome', 'inevitable change'],
                    'examples' => [
                        ['en' => 'Change is inevitable in a growing company.', 'id' => 'Perubahan tidak dapat dihindari dalam perusahaan yang berkembang.'],
                        ['en' => 'His resignation seemed inevitable after the scandal.', 'id' => 'Pengunduran dirinya tampak tidak dapat dihindari setelah skandal itu.'],
                    ],
                ],
                [
                    'word' => 'facilitate', 'phonetic' => '/fəˈsɪl.ɪ.teɪt/', 'pos' => 'verb',
                    'meaning_id' => 'memfasilitasi, mempermudah', 'meaning_en' => 'to make an action or process easier',
                    'synonyms' => ['enable', 'assist'], 'antonyms' => ['hinder'], 'collocations' => ['facilitate discussion', 'facilitate learning'],
                    'examples' => [
                        ['en' => 'The new software facilitates communication between teams.', 'id' => 'Perangkat lunak baru itu mempermudah komunikasi antar tim.'],
                        ['en' => 'The teacher facilitated the group discussion.', 'id' => 'Guru itu memfasilitasi diskusi kelompok.'],
                    ],
                ],
                [
                    'word' => 'meticulous', 'phonetic' => '/məˈtɪk.jə.ləs/', 'pos' => 'adjective',
                    'meaning_id' => 'sangat teliti, cermat', 'meaning_en' => 'showing great attention to detail',
                    'synonyms' => ['careful', 'precise'], 'antonyms' => ['careless'], 'collocations' => ['meticulous planning', 'meticulous research'],
                    'examples' => [
                        ['en' => 'She is meticulous about every detail of her work.', 'id' => 'Dia sangat teliti terhadap setiap detail pekerjaannya.'],
                        ['en' => 'The project required meticulous planning.', 'id' => 'Proyek itu membutuhkan perencanaan yang sangat cermat.'],
                    ],
                ],
                [
                    'word' => 'substantial', 'phonetic' => '/səbˈstæn.ʃəl/', 'pos' => 'adjective',
                    'meaning_id' => 'besar, substansial', 'meaning_en' => 'of considerable importance, size, or worth',
                    'synonyms' => ['significant', 'considerable'], 'antonyms' => ['minor'], 'collocations' => ['substantial amount', 'substantial evidence'],
                    'examples' => [
                        ['en' => 'They made a substantial investment in the project.', 'id' => 'Mereka membuat investasi besar dalam proyek itu.'],
                        ['en' => 'There is substantial evidence to support the theory.', 'id' => 'Ada bukti substansial yang mendukung teori itu.'],
                    ],
                ],
                [
                    'word' => 'compelling', 'phonetic' => '/kəmˈpel.ɪŋ/', 'pos' => 'adjective',
                    'meaning_id' => 'meyakinkan, menarik', 'meaning_en' => 'evoking interest or attention in a powerful way',
                    'synonyms' => ['convincing', 'persuasive'], 'antonyms' => ['unconvincing'], 'collocations' => ['compelling argument', 'compelling evidence'],
                    'examples' => [
                        ['en' => 'She gave a compelling argument for the change.', 'id' => 'Dia memberikan argumen yang meyakinkan untuk perubahan itu.'],
                        ['en' => 'It was a compelling story from start to finish.', 'id' => 'Itu adalah cerita yang menarik dari awal hingga akhir.'],
                    ],
                ],
                [
                    'word' => 'undermine', 'phonetic' => '/ˌʌn.dəˈmaɪn/', 'pos' => 'verb',
                    'meaning_id' => 'melemahkan, merongrong', 'meaning_en' => 'to weaken or damage something gradually',
                    'synonyms' => ['weaken', 'sabotage'], 'antonyms' => ['strengthen'], 'collocations' => ['undermine confidence', 'undermine authority'],
                    'examples' => [
                        ['en' => 'Constant criticism can undermine an employee\'s confidence.', 'id' => 'Kritik terus-menerus dapat melemahkan kepercayaan diri karyawan.'],
                        ['en' => 'The scandal undermined public trust in the government.', 'id' => 'Skandal itu merongrong kepercayaan publik terhadap pemerintah.'],
                    ],
                ],
                [
                    'word' => 'take into account', 'phonetic' => null, 'pos' => 'phrase',
                    'meaning_id' => 'mempertimbangkan', 'meaning_en' => 'to consider something when making a decision',
                    'synonyms' => ['consider'], 'antonyms' => ['ignore'], 'collocations' => [],
                    'examples' => [
                        ['en' => 'We must take into account the extra costs.', 'id' => 'Kita harus mempertimbangkan biaya tambahan itu.'],
                        ['en' => 'The plan takes into account potential risks.', 'id' => 'Rencana itu mempertimbangkan risiko yang mungkin terjadi.'],
                    ],
                ],
                [
                    'word' => 'ubiquitous', 'phonetic' => '/juːˈbɪk.wɪ.təs/', 'pos' => 'adjective',
                    'meaning_id' => 'ada di mana-mana', 'meaning_en' => 'present, appearing, or found everywhere',
                    'synonyms' => ['omnipresent', 'widespread'], 'antonyms' => ['rare'], 'collocations' => ['ubiquitous technology'],
                    'examples' => [
                        ['en' => 'Smartphones have become ubiquitous in modern life.', 'id' => 'Ponsel pintar telah menjadi hal yang ada di mana-mana dalam kehidupan modern.'],
                        ['en' => 'Coffee shops are ubiquitous in this city.', 'id' => 'Kedai kopi ada di mana-mana di kota ini.'],
                    ],
                ],
                [
                    'word' => 'coherent', 'phonetic' => '/koʊˈhɪr.ənt/', 'pos' => 'adjective',
                    'meaning_id' => 'koheren, masuk akal', 'meaning_en' => 'logical and well organized',
                    'synonyms' => ['logical', 'consistent'], 'antonyms' => ['incoherent'], 'collocations' => ['coherent argument', 'coherent plan'],
                    'examples' => [
                        ['en' => 'His essay lacked a coherent structure.', 'id' => 'Esainya kekurangan struktur yang koheren.'],
                        ['en' => 'She presented a coherent plan for the project.', 'id' => 'Dia menyajikan rencana yang koheren untuk proyek itu.'],
                    ],
                ],
            ],
            'Advanced' => [
                [
                    'word' => 'esoteric', 'phonetic' => '/ˌes.əˈter.ɪk/', 'pos' => 'adjective',
                    'meaning_id' => 'esoteris, hanya dipahami segelintir orang', 'meaning_en' => 'intended for or understood by only a small group',
                    'synonyms' => ['obscure', 'arcane'], 'antonyms' => ['mainstream'], 'collocations' => ['esoteric knowledge', 'esoteric subject'],
                    'examples' => [
                        ['en' => 'The lecture covered rather esoteric topics in physics.', 'id' => 'Kuliah itu membahas topik fisika yang cukup esoteris.'],
                        ['en' => 'He has esoteric taste in music.', 'id' => 'Dia punya selera musik yang esoteris.'],
                    ],
                ],
                [
                    'word' => 'corroborate', 'phonetic' => '/kəˈrɒb.ə.reɪt/', 'pos' => 'verb',
                    'meaning_id' => 'menguatkan, mengonfirmasi', 'meaning_en' => 'to confirm or give support to a statement or theory',
                    'synonyms' => ['confirm', 'substantiate'], 'antonyms' => ['contradict'], 'collocations' => ['corroborate evidence', 'corroborate a claim'],
                    'examples' => [
                        ['en' => 'The witness\'s testimony corroborated the police report.', 'id' => 'Kesaksian saksi itu menguatkan laporan polisi.'],
                        ['en' => 'New data corroborates the original hypothesis.', 'id' => 'Data baru menguatkan hipotesis awal.'],
                    ],
                ],
                [
                    'word' => 'ostensibly', 'phonetic' => '/ɒˈsten.sɪ.bli/', 'pos' => 'adverb',
                    'meaning_id' => 'tampaknya, kelihatannya', 'meaning_en' => 'as appears or is stated to be true, though not necessarily so',
                    'synonyms' => ['apparently', 'seemingly'], 'antonyms' => [], 'collocations' => [],
                    'examples' => [
                        ['en' => 'He resigned, ostensibly for personal reasons.', 'id' => 'Dia mengundurkan diri, yang tampaknya karena alasan pribadi.'],
                        ['en' => 'The meeting was ostensibly about budget cuts.', 'id' => 'Rapat itu tampaknya membahas pemangkasan anggaran.'],
                    ],
                ],
                [
                    'word' => 'nuanced', 'phonetic' => '/ˈnjuː.ɑːnst/', 'pos' => 'adjective',
                    'meaning_id' => 'bernuansa, memiliki perbedaan halus', 'meaning_en' => 'characterized by subtle differences or shades of meaning',
                    'synonyms' => ['subtle'], 'antonyms' => ['crude', 'simplistic'], 'collocations' => ['nuanced view', 'nuanced argument'],
                    'examples' => [
                        ['en' => 'She offered a more nuanced view of the debate.', 'id' => 'Dia menawarkan pandangan yang lebih bernuansa tentang perdebatan itu.'],
                        ['en' => 'The novel presents a nuanced portrait of grief.', 'id' => 'Novel itu menghadirkan gambaran duka yang bernuansa halus.'],
                    ],
                ],
                [
                    'word' => 'pragmatic', 'phonetic' => '/præɡˈmæt.ɪk/', 'pos' => 'adjective',
                    'meaning_id' => 'pragmatis, praktis', 'meaning_en' => 'dealing with things sensibly and realistically',
                    'synonyms' => ['practical', 'realistic'], 'antonyms' => ['idealistic'], 'collocations' => ['pragmatic approach', 'pragmatic solution'],
                    'examples' => [
                        ['en' => 'The manager took a pragmatic approach to the crisis.', 'id' => 'Manajer itu mengambil pendekatan pragmatis terhadap krisis itu.'],
                        ['en' => 'We need a pragmatic solution, not an idealistic one.', 'id' => 'Kita butuh solusi pragmatis, bukan yang idealistis.'],
                    ],
                ],
                [
                    'word' => 'exacerbate', 'phonetic' => '/ɪɡˈzæs.ər.beɪt/', 'pos' => 'verb',
                    'meaning_id' => 'memperburuk', 'meaning_en' => 'to make a problem or bad situation worse',
                    'synonyms' => ['worsen', 'aggravate'], 'antonyms' => ['alleviate', 'improve'], 'collocations' => ['exacerbate the problem', 'exacerbate tension'],
                    'examples' => [
                        ['en' => 'The drought exacerbated the food shortage.', 'id' => 'Kekeringan itu memperburuk kelangkaan pangan.'],
                        ['en' => 'His comments only exacerbated the tension in the room.', 'id' => 'Komentarnya hanya memperburuk ketegangan di ruangan itu.'],
                    ],
                ],
                [
                    'word' => 'juxtaposition', 'phonetic' => '/ˌdʒʌk.stə.pəˈzɪʃ.ən/', 'pos' => 'noun',
                    'meaning_id' => 'perbandingan berdampingan', 'meaning_en' => 'the fact of placing two things close together for contrasting effect',
                    'synonyms' => ['contrast'], 'antonyms' => [], 'collocations' => ['striking juxtaposition'],
                    'examples' => [
                        ['en' => 'The film uses juxtaposition to highlight social inequality.', 'id' => 'Film itu menggunakan perbandingan berdampingan untuk menyoroti ketimpangan sosial.'],
                        ['en' => 'There\'s a striking juxtaposition between old and new architecture.', 'id' => 'Ada perbandingan berdampingan yang mencolok antara arsitektur lama dan baru.'],
                    ],
                ],
                [
                    'word' => 'circumvent', 'phonetic' => '/ˌsɜː.kəmˈvent/', 'pos' => 'verb',
                    'meaning_id' => 'menghindari, mengakali', 'meaning_en' => 'to find a way around an obstacle or rule',
                    'synonyms' => ['bypass', 'evade'], 'antonyms' => ['comply'], 'collocations' => ['circumvent the rules', 'circumvent regulations'],
                    'examples' => [
                        ['en' => 'The company tried to circumvent the new regulations.', 'id' => 'Perusahaan itu mencoba mengakali regulasi baru.'],
                        ['en' => 'They found a clever way to circumvent the restriction.', 'id' => 'Mereka menemukan cara cerdik untuk menghindari pembatasan itu.'],
                    ],
                ],
                [
                    'word' => 'unequivocal', 'phonetic' => '/ˌʌn.ɪˈkwɪv.ə.kəl/', 'pos' => 'adjective',
                    'meaning_id' => 'jelas, tidak ambigu', 'meaning_en' => 'leaving no doubt; unambiguous',
                    'synonyms' => ['clear', 'unambiguous'], 'antonyms' => ['ambiguous', 'vague'], 'collocations' => ['unequivocal support', 'unequivocal answer'],
                    'examples' => [
                        ['en' => 'She gave an unequivocal answer to the question.', 'id' => 'Dia memberikan jawaban yang jelas atas pertanyaan itu.'],
                        ['en' => 'The board expressed unequivocal support for the plan.', 'id' => 'Dewan menyatakan dukungan tegas untuk rencana itu.'],
                    ],
                ],
                [
                    'word' => 'paradigm', 'phonetic' => '/ˈpær.ə.daɪm/', 'pos' => 'noun',
                    'meaning_id' => 'paradigma, pola pikir', 'meaning_en' => 'a typical example or pattern of something',
                    'synonyms' => ['model', 'framework'], 'antonyms' => [], 'collocations' => ['paradigm shift', 'new paradigm'],
                    'examples' => [
                        ['en' => 'The discovery led to a paradigm shift in physics.', 'id' => 'Penemuan itu menyebabkan pergeseran paradigma dalam fisika.'],
                        ['en' => 'This model represents a new paradigm in education.', 'id' => 'Model ini merepresentasikan paradigma baru dalam pendidikan.'],
                    ],
                ],
            ],
        ];
    }
}
