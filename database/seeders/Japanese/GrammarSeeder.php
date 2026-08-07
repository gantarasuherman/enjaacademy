<?php

declare(strict_types=1);

namespace Database\Seeders\Japanese;

use Database\Seeders\Concerns\SeedsLearningContent;
use Illuminate\Database\Seeder;

/**
 * JLPT N5 grammar. Particles come first because nothing else in Japanese
 * parses until they do.
 *
 * Row shape: [pattern, reading, romaji, meaning, example, example meaning]
 */
class GrammarSeeder extends Seeder
{
    use SeedsLearningContent;

    public function run(): void
    {
        $count = $this->fill('tata-bahasa-jepang', $this->lessons());

        $this->command?->info("  tata bahasa: {$count} pola N5");
    }

    /** @param array<int, array<int, string>> $rows */
    private function points(array $rows, string $group): array
    {
        return array_map(fn (array $r) => [
            'term' => $r[0],
            'reading' => $r[1],
            'romaji' => $r[2],
            'meaning' => $r[3],
            'example' => $r[4] ?? null,
            'example_meaning' => $r[5] ?? null,
            'extra' => ['golongan' => $group, 'jlpt' => 'N5'],
        ], $rows);
    }

    private function lessons(): array
    {
        return [
            [
                'title' => 'Partikel dasar',
                'summary' => 'Sepuluh partikel yang menentukan peran tiap kata dalam kalimat.',
                'minutes' => 20,
                'xp' => 35,
                'content' => '<p>Urutan kata dalam bahasa Jepang bebas; <strong>partikel</strong>lah yang menentukan siapa melakukan apa. '
                    .'Inilah sebabnya partikel harus dikuasai lebih dulu daripada kosakata sebanyak apa pun.</p>'
                    .'<p>Pola dasar kalimat: <code>[Topik] は [Objek] を [Kata kerja]</code></p>',
                'items' => $this->points([
                    ['は', 'は (wa)', 'wa', 'penanda topik — ditulis は tapi dibaca "wa"', '私は学生です。', 'Saya (topik) adalah pelajar.'],
                    ['が', 'が', 'ga', 'penanda subjek; menekankan siapa/apa', '誰が来ましたか。', 'Siapa yang datang?'],
                    ['を', 'を (o)', 'o', 'penanda objek langsung', 'パンを食べます。', 'Saya makan roti.'],
                    ['に', 'に', 'ni', 'waktu, tujuan, keberadaan', '七時に起きます。', 'Saya bangun jam tujuh.'],
                    ['で', 'で', 'de', 'tempat kegiatan, alat, cara', '図書館で勉強します。', 'Saya belajar di perpustakaan.'],
                    ['へ', 'へ (e)', 'e', 'arah tujuan — ditulis へ dibaca "e"', '日本へ行きます。', 'Saya pergi ke Jepang.'],
                    ['と', 'と', 'to', 'dan (daftar lengkap), bersama', '友達と行きます。', 'Saya pergi bersama teman.'],
                    ['も', 'も', 'mo', 'juga', '私も学生です。', 'Saya juga pelajar.'],
                    ['の', 'の', 'no', 'kepemilikan, penjelas', '私の本です。', 'Ini buku saya.'],
                    ['から', 'から', 'kara', 'dari (waktu/tempat), karena', '九時から始まります。', 'Dimulai dari jam sembilan.'],
                    ['まで', 'まで', 'made', 'sampai', '五時まで働きます。', 'Saya bekerja sampai jam lima.'],
                    ['や', 'や', 'ya', 'dan (daftar tak lengkap)', '本やノートを買いました。', 'Saya membeli buku, catatan, dan lain-lain.'],
                ], 'partikel'),
            ],

            [
                'title' => 'Kalimat です dan bentuk lampau',
                'summary' => 'Empat bentuk dasar: positif, negatif, lampau, lampau negatif.',
                'minutes' => 15,
                'xp' => 30,
                'items' => $this->points([
                    ['〜です', 'です', 'desu', 'adalah (sopan, kini)', 'これは本です。', 'Ini adalah buku.'],
                    ['〜じゃありません', 'じゃありません', 'ja arimasen', 'bukan (sopan, kini)', '学生じゃありません。', 'Saya bukan pelajar.'],
                    ['〜でした', 'でした', 'deshita', 'adalah (sopan, lampau)', '昨日は雨でした。', 'Kemarin hujan.'],
                    ['〜じゃありませんでした', 'じゃありませんでした', 'ja arimasen deshita', 'bukan (sopan, lampau)', '休みじゃありませんでした。', 'Bukan hari libur.'],
                    ['〜ます', 'ます', 'masu', 'kata kerja sopan, kini/akan datang', '毎日勉強します。', 'Saya belajar setiap hari.'],
                    ['〜ません', 'ません', 'masen', 'kata kerja sopan negatif', 'お酒は飲みません。', 'Saya tidak minum alkohol.'],
                    ['〜ました', 'ました', 'mashita', 'kata kerja sopan lampau', '昨日映画を見ました。', 'Kemarin saya menonton film.'],
                    ['〜ませんでした', 'ませんでした', 'masen deshita', 'kata kerja sopan lampau negatif', '何も食べませんでした。', 'Saya tidak makan apa pun.'],
                ], 'kopula'),
            ],

            [
                'title' => 'Ajakan, permintaan, dan keinginan',
                'minutes' => 15,
                'xp' => 30,
                'items' => $this->points([
                    ['〜ましょう', 'ましょう', 'mashou', 'mari (ajakan)', '一緒に行きましょう。', 'Mari pergi bersama.'],
                    ['〜ませんか', 'ませんか', 'masen ka', 'maukah? (ajakan sopan)', 'お茶を飲みませんか。', 'Mau minum teh?'],
                    ['〜てください', 'てください', 'te kudasai', 'tolong lakukan', 'ちょっと待ってください。', 'Tolong tunggu sebentar.'],
                    ['〜をください', 'をください', 'o kudasai', 'tolong berikan', '水をください。', 'Tolong airnya.'],
                    ['〜たいです', 'たいです', 'tai desu', 'ingin (melakukan)', '日本へ行きたいです。', 'Saya ingin pergi ke Jepang.'],
                    ['〜がほしいです', 'がほしいです', 'ga hoshii desu', 'ingin (benda)', '新しい鞄がほしいです。', 'Saya ingin tas baru.'],
                    ['〜てもいいですか', 'てもいいですか', 'te mo ii desu ka', 'boleh saya…?', '入ってもいいですか。', 'Boleh saya masuk?'],
                    ['〜ないでください', 'ないでください', 'naide kudasai', 'tolong jangan', 'ここで写真を撮らないでください。', 'Tolong jangan memotret di sini.'],
                ], 'ungkapan'),
            ],

            [
                'title' => 'Bentuk て dan kalimat majemuk',
                'summary' => 'Bentuk て adalah simpul yang menyambung dua kalimat menjadi satu.',
                'minutes' => 18,
                'xp' => 35,
                'content' => '<p>Bentuk て dipakai untuk merangkai kegiatan, menyatakan sedang berlangsung, dan meminta izin. '
                    .'Menguasainya membuka hampir separuh tata bahasa N5 sekaligus.</p>',
                'items' => $this->points([
                    ['〜て、〜', 'て', 'te', 'lalu (merangkai kegiatan)', '起きて、顔を洗います。', 'Saya bangun, lalu mencuci muka.'],
                    ['〜ています', 'ています', 'te imasu', 'sedang berlangsung', '今、本を読んでいます。', 'Sekarang saya sedang membaca buku.'],
                    ['〜ています (keadaan)', 'ています', 'te imasu', 'keadaan yang bertahan', '東京に住んでいます。', 'Saya tinggal di Tokyo.'],
                    ['〜てから', 'てから', 'te kara', 'setelah', 'ご飯を食べてから、出かけます。', 'Setelah makan, saya keluar.'],
                    ['〜まえに', 'まえに', 'mae ni', 'sebelum', '寝る前に歯を磨きます。', 'Saya menyikat gigi sebelum tidur.'],
                    ['〜ながら', 'ながら', 'nagara', 'sambil', '音楽を聞きながら勉強します。', 'Saya belajar sambil mendengarkan musik.'],
                    ['〜けど', 'けど', 'kedo', 'tetapi', '安いけど、おいしくないです。', 'Murah, tetapi tidak enak.'],
                    ['〜から (sebab)', 'から', 'kara', 'karena', '寒いから、上着を着ます。', 'Karena dingin, saya memakai jaket.'],
                ], 'penghubung'),
            ],

            [
                'title' => 'Perbandingan dan keberadaan',
                'minutes' => 15,
                'xp' => 30,
                'items' => $this->points([
                    ['〜があります', 'があります', 'ga arimasu', 'ada (benda mati)', '机の上に本があります。', 'Ada buku di atas meja.'],
                    ['〜がいます', 'がいます', 'ga imasu', 'ada (makhluk hidup)', '庭に猫がいます。', 'Ada kucing di halaman.'],
                    ['〜より', 'より', 'yori', 'daripada', '飛行機は電車より速いです。', 'Pesawat lebih cepat daripada kereta.'],
                    ['〜のほうが', 'のほうが', 'no hou ga', 'lebih (dari dua pilihan)', 'こっちのほうが安いです。', 'Yang ini lebih murah.'],
                    ['いちばん〜', 'いちばん', 'ichiban', 'paling', '一番好きな色は青です。', 'Warna yang paling saya suka adalah biru.'],
                    ['〜と同じ', 'とおなじ', 'to onaji', 'sama dengan', '私のと同じです。', 'Sama dengan punya saya.'],
                    ['〜ができます', 'ができます', 'ga dekimasu', 'bisa, mampu', '日本語ができます。', 'Saya bisa bahasa Jepang.'],
                    ['〜ことができます', 'ことができます', 'koto ga dekimasu', 'bisa melakukan', '泳ぐことができます。', 'Saya bisa berenang.'],
                ], 'perbandingan'),
            ],
        ];
    }
}
