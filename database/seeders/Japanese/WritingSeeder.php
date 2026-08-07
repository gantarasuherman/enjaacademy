<?php

declare(strict_types=1);

namespace Database\Seeders\Japanese;

use Database\Seeders\Concerns\SeedsLearningContent;
use Illuminate\Database\Seeder;

/**
 * Japanese writing practice (作文), from stroke order up to a short essay.
 *
 * Writing is the one skill where Japanese diverges hardest from Indonesian:
 * stroke order, the three scripts, vertical vs horizontal, and the plain/polite
 * split all have to be practised deliberately rather than absorbed.
 *
 * Row shape: [prompt, kana, romaji, what to produce, model answer, translation]
 */
class WritingSeeder extends Seeder
{
    use SeedsLearningContent;

    public function run(): void
    {
        $count = $this->fill('menulis-jepang', $this->lessons());

        $this->command?->info("  menulis: {$count} latihan");
    }

    /** @param array<int, array<int, string>> $rows */
    private function tasks(array $rows, string $tipe): array
    {
        return array_map(fn (array $r) => [
            'term' => $r[0],
            'reading' => $r[1] ?: null,
            'romaji' => $r[2] ?: null,
            'meaning' => $r[3],
            'example' => $r[4] ?? null,
            'example_meaning' => $r[5] ?? null,
            'extra' => ['tipe' => $tipe, 'jlpt' => 'N5'],
        ], $rows);
    }

    private function lessons(): array
    {
        return [
            [
                'title' => 'Urutan goresan (筆順)',
                'summary' => 'Delapan aturan yang menentukan urutan menulis hampir semua kanji.',
                'minutes' => 15,
                'xp' => 30,
                'content' => '<p>Urutan goresan bukan formalitas. Ia menentukan bentuk akhir tulisan, memungkinkan orang lain '
                    .'membaca tulisan tangan cepat, dan menjadi dasar pencarian kanji di kamus.</p>'
                    .'<p>Latihan: tulis tiap kanji sepuluh kali sambil menghitung goresannya dengan suara.</p>',
                'items' => $this->tasks([
                    ['Atas dulu, baru bawah', '', '', 'Aturan 1 — goresan atas selalu didahulukan', '三 (satu → dua → tiga, dari atas)', 'Tiga garis ditulis berurutan dari atas'],
                    ['Kiri dulu, baru kanan', '', '', 'Aturan 2 — dari kiri ke kanan', '川 (kiri → tengah → kanan)', 'Tiga garis tegak dari kiri'],
                    ['Mendatar dulu, baru menurun', '', '', 'Aturan 3 — garis horizontal mendahului vertikal yang memotongnya', '十 (mendatar → menurun)', 'Palang dulu, tiang kemudian'],
                    ['Tengah dulu pada bentuk simetris', '', '', 'Aturan 4 — pusat sebelum sayap', '小 (tengah → kiri → kanan)', 'Titik tengah lebih dulu'],
                    ['Bingkai luar sebelum isi', '', '', 'Aturan 5 — kotak dulu, isinya kemudian', '国 (bingkai → isi → tutup bawah)', 'Sisi bawah selalu terakhir'],
                    ['Goresan penembus paling akhir', '', '', 'Aturan 6 — garis yang menembus seluruh karakter ditulis terakhir', '車 (semua → garis tegak terakhir)', 'Tiang panjang menutup karakter'],
                    ['Kiri-bawah sebelum kanan-bawah', '', '', 'Aturan 7 — sapuan kiri mendahului sapuan kanan', '人 (sapuan kiri → sapuan kanan)', 'Dua sapuan membentuk huruf orang'],
                    ['Titik terakhir', '', '', 'Aturan 8 — titik kecil di kanan atas ditulis paling akhir', '犬 (大 → titik)', 'Titik yang membedakan 大 dan 犬'],
                ], 'aturan'),
            ],

            [
                'title' => 'Memilih aksara yang tepat',
                'summary' => 'Kapan memakai hiragana, katakana, atau kanji dalam satu kalimat.',
                'minutes' => 15,
                'xp' => 30,
                'content' => '<p>Bahasa Jepang menulis satu kalimat dengan tiga aksara sekaligus. Pembagiannya bukan selera, melainkan aturan:</p>'
                    .'<ul><li><strong>Kanji</strong> — akar kata benda, kata kerja, kata sifat</li>'
                    .'<li><strong>Hiragana</strong> — partikel, akhiran, kata bantu</li>'
                    .'<li><strong>Katakana</strong> — kata serapan, nama asing, onomatope, penekanan</li></ul>',
                'items' => $this->tasks([
                    ['私はコーヒーを飲みます。', 'わたしはコーヒーをのみます。', 'Watashi wa koohii o nomimasu.', 'Tulis ulang kalimat ini dan tandai tiap aksara', '私 (kanji) は (hiragana) コーヒー (katakana) を (hiragana) 飲 (kanji) みます (hiragana)', 'Satu kalimat memuat ketiga aksara sekaligus'],
                    ['たなかさんはにほんじんです。', '', '', 'Tulis ulang dengan kanji yang tepat', '田中さんは日本人です。', 'Tanaka-san adalah orang Jepang.'],
                    ['わたしはインドネシアからきました。', '', '', 'Tulis ulang dengan kanji yang tepat', '私はインドネシアから来ました。', 'Saya datang dari Indonesia. — nama negara tetap katakana'],
                    ['まいにちがっこうへいきます。', '', '', 'Tulis ulang dengan kanji yang tepat', '毎日学校へ行きます。', 'Saya pergi ke sekolah setiap hari.'],
                    ['Tulis nama sendiri dalam katakana', '', '', 'Terapkan aturan bunyi panjang dan sokuon', 'Budi → ブディ · Sarah → サラ · Rizky → リズキ', 'Nama asing selalu ditulis dengan katakana'],
                    ['あのレストランのりょうりはおいしいです。', '', '', 'Tulis ulang dengan kanji yang tepat', 'あのレストランの料理はおいしいです。', 'Masakan restoran itu enak.'],
                ], 'aksara'),
            ],

            [
                'title' => 'Menyusun kalimat dengan partikel',
                'summary' => 'Latihan tertulis untuk memasang partikel yang benar.',
                'minutes' => 18,
                'xp' => 35,
                'content' => '<p>Salah satu partikel saja mengubah arti seluruh kalimat. Kerjakan tanpa melihat kunci, '
                    .'lalu bandingkan.</p><p>Pola dasar: <code>[Topik] は [Tempat] で [Objek] を [Kata kerja]</code></p>',
                'items' => $this->tasks([
                    ['私 ___ 学生です。', '', '', 'Isi partikel yang tepat', 'は — 私は学生です。', 'Saya adalah pelajar (は sebagai penanda topik)'],
                    ['図書館 ___ 本 ___ 読みます。', '', '', 'Isi dua partikel', 'で / を — 図書館で本を読みます。', 'Saya membaca buku di perpustakaan'],
                    ['七時 ___ 起きます。', '', '', 'Isi partikel waktu', 'に — 七時に起きます。', 'Saya bangun jam tujuh'],
                    ['友達 ___ 映画 ___ 見ました。', '', '', 'Isi dua partikel', 'と / を — 友達と映画を見ました。', 'Saya menonton film bersama teman'],
                    ['九時 ___ 五時 ___ 働きます。', '', '', 'Isi partikel rentang waktu', 'から / まで — 九時から五時まで働きます。', 'Saya bekerja dari jam sembilan sampai lima'],
                    ['これは私 ___ 本です。', '', '', 'Isi partikel kepemilikan', 'の — これは私の本です。', 'Ini buku saya'],
                    ['Susun: 食べます / を / パン / 私 / は', '', '', 'Urutkan menjadi kalimat benar', '私はパンを食べます。', 'Saya makan roti'],
                    ['Susun: 行きます / へ / 日本 / 来年 / 私 / は', '', '', 'Urutkan menjadi kalimat benar', '私は来年日本へ行きます。', 'Tahun depan saya pergi ke Jepang'],
                ], 'latihan'),
            ],

            [
                'title' => 'Menulis tentang diri sendiri (自己紹介)',
                'summary' => 'Karangan pertama: 60–80 karakter memperkenalkan diri.',
                'minutes' => 25,
                'xp' => 40,
                'content' => '<p><strong>Tugas:</strong> tulis perkenalan diri sepanjang 60–80 karakter Jepang.</p>'
                    .'<p><strong>Wajib memuat:</strong> nama · asal · pekerjaan atau status · satu hobi · penutup よろしくお願いします</p>'
                    .'<p><strong>Rubrik:</strong> ketepatan partikel (30) · penggunaan aksara (30) · kelengkapan isi (25) · kesopanan (15)</p>',
                'items' => $this->tasks([
                    ['はじめまして。___ と申します。', '', '', 'Kerangka kalimat 1 — perkenalan', 'はじめまして。リナと申します。', 'Salam kenal. Nama saya Rina.'],
                    ['___ から来ました。', '', '', 'Kerangka kalimat 2 — asal', 'インドネシアのジャカルタから来ました。', 'Saya datang dari Jakarta, Indonesia.'],
                    ['今、___ です。', '', '', 'Kerangka kalimat 3 — status', '今、大学生です。', 'Sekarang saya mahasiswa.'],
                    ['趣味は ___ ことです。', '', '', 'Kerangka kalimat 4 — hobi', '趣味は音楽を聞くことです。', 'Hobi saya mendengarkan musik.'],
                    ['どうぞよろしくお願いします。', '', '', 'Kerangka kalimat 5 — penutup wajib', 'どうぞよろしくお願いします。', 'Mohon bantuannya.'],
                    ['Contoh karangan utuh', '', '', 'Bandingkan dengan tulisanmu sendiri', 'はじめまして。リナと申します。インドネシアのジャカルタから来ました。今、大学で日本語を勉強しています。趣味は音楽を聞くことと料理を作ることです。日本の文化がとても好きです。どうぞよろしくお願いします。', 'Salam kenal. Nama saya Rina. Saya datang dari Jakarta, Indonesia. Sekarang saya belajar bahasa Jepang di universitas. Hobi saya mendengarkan musik dan memasak. Saya sangat menyukai budaya Jepang. Mohon bantuannya.'],
                ], 'karangan'),
            ],

            [
                'title' => 'Menulis buku harian (日記)',
                'summary' => 'Melatih bentuk lampau lewat catatan harian pendek.',
                'minutes' => 22,
                'xp' => 35,
                'content' => '<p><strong>Tugas:</strong> tulis catatan harian 80–100 karakter tentang kegiatanmu kemarin.</p>'
                    .'<p><strong>Wajib memuat:</strong> tanggal dan cuaca · minimal empat kegiatan berurutan · '
                    .'satu kalimat perasaan · seluruhnya dalam bentuk lampau (〜ました / 〜でした)</p>'
                    .'<p><strong>Rubrik:</strong> konsistensi bentuk lampau (35) · urutan waktu (25) · variasi kata kerja (25) · panjang sesuai (15)</p>',
                'items' => $this->tasks([
                    ['〇月〇日（〇曜日）晴れ', '', '', 'Baris kepala wajib: tanggal, hari, cuaca', '八月七日（木曜日）晴れ', '7 Agustus (Kamis), cerah'],
                    ['今日は ___ ました。', '', '', 'Kegiatan pertama', '今日は六時に起きました。', 'Hari ini saya bangun jam enam.'],
                    ['それから、___ ました。', '', '', 'Penghubung antar kegiatan', 'それから、朝ご飯を食べました。', 'Setelah itu, saya sarapan.'],
                    ['午後、___ ました。', '', '', 'Kegiatan sore', '午後、友達と買い物に行きました。', 'Sore hari saya pergi berbelanja dengan teman.'],
                    ['とても ___ でした。', '', '', 'Kalimat perasaan penutup', 'とても楽しかったです。', 'Sangat menyenangkan.'],
                    ['Contoh buku harian utuh', '', '', 'Perhatikan konsistensi bentuk lampau', '八月七日（木曜日）晴れ。今日は六時に起きました。朝ご飯を食べてから、学校へ行きました。日本語の授業はちょっと難しかったですが、面白かったです。午後、友達と買い物に行きました。新しい鞄を買いました。とても楽しい一日でした。', '7 Agustus (Kamis), cerah. Hari ini saya bangun jam enam. Setelah sarapan, saya pergi ke sekolah. Pelajaran bahasa Jepang agak sulit, tetapi menarik. Sore hari saya berbelanja dengan teman dan membeli tas baru. Hari yang sangat menyenangkan.'],
                ], 'karangan'),
            ],

            [
                'title' => 'Menulis surat dan pesan',
                'summary' => 'Format surat Jepang berbeda jauh dari surat Indonesia.',
                'minutes' => 22,
                'xp' => 35,
                'content' => '<p><strong>Tugas:</strong> tulis satu pesan singkat kepada guru untuk izin tidak masuk.</p>'
                    .'<p><strong>Struktur surat Jepang:</strong> sapaan pembuka → basa-basi cuaca/kabar → maksud utama → '
                    .'permintaan maaf/terima kasih → penutup</p>'
                    .'<p><strong>Rubrik:</strong> struktur lengkap (30) · tingkat kesopanan (30) · kejelasan maksud (25) · ejaan (15)</p>',
                'items' => $this->tasks([
                    ['〇〇先生', '', '', 'Sapaan pembuka — nama + gelar', '田中先生', 'Kepada Bu/Pak Tanaka'],
                    ['お世話になっております。', 'おせわになっております。', 'Osewa ni natte orimasu.', 'Basa-basi pembuka baku', 'いつもお世話になっております。', 'Terima kasih atas bimbingannya selama ini.'],
                    ['実は、___ 。', 'じつは', 'Jitsu wa', 'Membuka maksud utama', '実は、明日体調が悪くて学校を休みたいです。', 'Sebenarnya, besok kondisi saya kurang baik dan ingin izin.'],
                    ['ご迷惑をおかけして申し訳ありません。', 'ごめいわくをおかけしてもうしわけありません。', 'Gomeiwaku o okake shite moushiwake arimasen.', 'Permintaan maaf baku', 'ご迷惑をおかけして申し訳ありません。', 'Mohon maaf telah merepotkan.'],
                    ['よろしくお願いいたします。', '', '', 'Penutup baku (lebih halus dari します)', 'どうぞよろしくお願いいたします。', 'Mohon bantuannya.'],
                    ['Contoh pesan utuh', '', '', 'Perhatikan alur lima bagiannya', '田中先生／いつもお世話になっております。リナです。実は、昨日から熱があって、明日の授業を休ませていただきたいです。ご迷惑をおかけして申し訳ありません。宿題は後で必ず出します。どうぞよろしくお願いいたします。／リナ', 'Bu Tanaka / Terima kasih atas bimbingannya selama ini. Saya Rina. Sebenarnya sejak kemarin saya demam dan ingin izin dari pelajaran besok. Mohon maaf telah merepotkan. Tugasnya pasti saya kumpulkan nanti. Mohon bantuannya. / Rina'],
                ], 'surat'),
            ],

            [
                'title' => 'Deskripsi dan pendapat',
                'summary' => 'Latihan terakhir: menggambarkan sesuatu dan menyatakan alasan.',
                'minutes' => 25,
                'xp' => 40,
                'content' => '<p><strong>Tugas:</strong> tulis 100–120 karakter tentang tempat favoritmu.</p>'
                    .'<p><strong>Wajib memuat:</strong> minimal tiga kata sifat · satu kalimat 〜から (alasan) · '
                    .'satu perbandingan 〜より atau 一番 · satu kalimat 〜たいです</p>'
                    .'<p><strong>Rubrik:</strong> pemakaian kata sifat い/な (30) · struktur alasan (25) · perbandingan (25) · kelancaran (20)</p>',
                'items' => $this->tasks([
                    ['私の好きな場所は ___ です。', '', '', 'Kalimat pembuka', '私の好きな場所は家の近くの公園です。', 'Tempat favorit saya adalah taman dekat rumah.'],
                    ['そこは ___ で、___ です。', '', '', 'Rangkai dua kata sifat', 'そこは静かで、きれいです。', 'Tempat itu tenang dan indah.'],
                    ['___ から、よく行きます。', '', '', 'Nyatakan alasan dengan から', '木がたくさんあって涼しいから、よく行きます。', 'Karena banyak pohon dan sejuk, saya sering ke sana.'],
                    ['___ より ___ のほうが好きです。', '', '', 'Buat perbandingan', '町より公園のほうが好きです。', 'Saya lebih suka taman daripada kota.'],
                    ['今度 ___ たいです。', '', '', 'Nyatakan keinginan', '今度友達と一緒に行きたいです。', 'Lain kali saya ingin pergi bersama teman.'],
                    ['Contoh karangan utuh', '', '', 'Periksa apakah semua syarat terpenuhi', '私の好きな場所は家の近くの公園です。そこは静かできれいな公園です。木がたくさんあって、夏でも涼しいから、毎週日曜日に行きます。にぎやかな町より静かな公園のほうがずっと好きです。公園の中で一番好きな場所は大きい木の下のベンチです。今度、友達と一緒にお弁当を持って行きたいです。', 'Tempat favorit saya adalah taman dekat rumah. Taman itu tenang dan indah. Karena banyak pohon dan sejuk bahkan di musim panas, saya pergi ke sana setiap Minggu. Saya jauh lebih suka taman yang tenang daripada kota yang ramai. Tempat yang paling saya sukai di taman adalah bangku di bawah pohon besar. Lain kali saya ingin pergi bersama teman sambil membawa bekal.'],
                ], 'karangan'),
            ],
        ];
    }
}
