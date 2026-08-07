<?php

declare(strict_types=1);

namespace Database\Seeders\Japanese;

use Database\Seeders\Concerns\SeedsLearningContent;
use Illuminate\Database\Seeder;

/**
 * The Japanese dictionary, organised the way a learner actually needs it:
 * by situation, then by word class.
 *
 * Row shape: [kanji/kana, reading, romaji, meaning, example, example meaning]
 */
class VocabularySeeder extends Seeder
{
    use SeedsLearningContent;

    public function run(): void
    {
        $count = $this->fill('kosakata-jepang', $this->lessons());

        $this->command?->info("  kosakata: {$count} entri kamus");
    }

    /** @param array<int, array<int, string>> $rows */
    private function words(array $rows, string $category): array
    {
        return array_map(fn (array $r) => [
            'term' => $r[0],
            'reading' => $r[1],
            'romaji' => $r[2],
            'meaning' => $r[3],
            'example' => $r[4] ?? null,
            'example_meaning' => $r[5] ?? null,
            'extra' => ['kategori' => $category, 'jlpt' => 'N5'],
        ], $rows);
    }

    private function lessons(): array
    {
        return [
            /* ------------------------------------------------ greetings */
            [
                'title' => 'Salam dan ungkapan harian',
                'summary' => 'Dua puluh ungkapan yang dipakai setiap hari, dari bangun tidur sampai berpisah.',
                'minutes' => 12,
                'xp' => 25,
                'items' => $this->words([
                    ['おはようございます', 'おはようございます', 'ohayou gozaimasu', 'selamat pagi (sopan)', '先生、おはようございます。', 'Selamat pagi, Bu/Pak Guru.'],
                    ['こんにちは', 'こんにちは', 'konnichiwa', 'selamat siang', 'こんにちは、田中さん。', 'Selamat siang, Tanaka-san.'],
                    ['こんばんは', 'こんばんは', 'konbanwa', 'selamat malam (sapaan)', 'こんばんは、遅くなりました。', 'Selamat malam, maaf terlambat.'],
                    ['おやすみなさい', 'おやすみなさい', 'oyasuminasai', 'selamat tidur', 'では、おやすみなさい。', 'Kalau begitu, selamat tidur.'],
                    ['さようなら', 'さようなら', 'sayounara', 'selamat tinggal', 'さようなら、また明日。', 'Selamat tinggal, sampai besok.'],
                    ['またね', 'またね', 'mata ne', 'sampai jumpa (santai)', 'じゃあ、またね。', 'Kalau begitu, sampai jumpa.'],
                    ['ありがとうございます', 'ありがとうございます', 'arigatou gozaimasu', 'terima kasih (sopan)', '本当にありがとうございます。', 'Terima kasih banyak.'],
                    ['どういたしまして', 'どういたしまして', 'dou itashimashite', 'sama-sama', 'いいえ、どういたしまして。', 'Tidak, sama-sama.'],
                    ['すみません', 'すみません', 'sumimasen', 'permisi, maaf', 'すみません、ちょっといいですか。', 'Permisi, boleh minta waktunya?'],
                    ['ごめんなさい', 'ごめんなさい', 'gomennasai', 'maaf', '遅れてごめんなさい。', 'Maaf saya terlambat.'],
                    ['はじめまして', 'はじめまして', 'hajimemashite', 'salam kenal', 'はじめまして、リナです。', 'Salam kenal, saya Rina.'],
                    ['よろしくおねがいします', 'よろしくおねがいします', 'yoroshiku onegaishimasu', 'mohon bantuannya', 'どうぞよろしくお願いします。', 'Mohon bantuannya.'],
                    ['いただきます', 'いただきます', 'itadakimasu', 'ucapan sebelum makan', 'では、いただきます。', 'Kalau begitu, saya makan dulu.'],
                    ['ごちそうさまでした', 'ごちそうさまでした', 'gochisousama deshita', 'ucapan setelah makan', 'ごちそうさまでした。おいしかったです。', 'Terima kasih makanannya, enak sekali.'],
                    ['いってきます', 'いってきます', 'ittekimasu', 'saya pergi dulu', 'じゃあ、いってきます。', 'Kalau begitu, saya berangkat.'],
                    ['いってらっしゃい', 'いってらっしゃい', 'itterasshai', 'hati-hati di jalan', 'いってらっしゃい、気をつけて。', 'Hati-hati di jalan ya.'],
                    ['ただいま', 'ただいま', 'tadaima', 'saya pulang', 'ただいま、疲れた。', 'Saya pulang, capek.'],
                    ['おかえりなさい', 'おかえりなさい', 'okaerinasai', 'selamat datang kembali', 'おかえりなさい、お疲れさま。', 'Selamat datang kembali, terima kasih atas kerja kerasnya.'],
                    ['おつかれさまです', 'おつかれさまです', 'otsukaresama desu', 'terima kasih atas kerja kerasnya', '今日もお疲れさまです。', 'Terima kasih untuk kerja kerasnya hari ini.'],
                    ['おげんきですか', 'おげんきですか', 'ogenki desu ka', 'apa kabar?', 'お元気ですか。— はい、元気です。', 'Apa kabar? — Ya, saya sehat.'],
                ], 'salam'),
            ],

            /* --------------------------------------------------- numbers */
            [
                'title' => 'Angka dan penggolong',
                'summary' => 'Bahasa Jepang memakai kata penggolong berbeda menurut bentuk benda — ini yang paling sering membingungkan pemula.',
                'minutes' => 15,
                'xp' => 30,
                'content' => '<p>Menghitung dalam bahasa Jepang bukan sekadar angka + benda. Bentuk bendanya menentukan penggolong yang dipakai.</p>'
                    .'<ul><li><strong>〜つ</strong> — benda umum tanpa bentuk khusus (1–10)</li>'
                    .'<li><strong>〜人</strong> — orang</li><li><strong>〜本</strong> — benda panjang: pena, botol, payung</li>'
                    .'<li><strong>〜枚</strong> — benda tipis: kertas, kemeja, piring</li>'
                    .'<li><strong>〜台</strong> — mesin: mobil, komputer</li>'
                    .'<li><strong>〜匹</strong> — hewan kecil</li><li><strong>〜冊</strong> — buku</li></ul>',
                'items' => $this->words([
                    ['一つ', 'ひとつ', 'hitotsu', 'satu (benda umum)', 'りんごを一つください。', 'Tolong satu apel.'],
                    ['二つ', 'ふたつ', 'futatsu', 'dua (benda umum)', 'パンを二つ買いました。', 'Saya membeli dua roti.'],
                    ['三つ', 'みっつ', 'mittsu', 'tiga (benda umum)', 'いすが三つあります。', 'Ada tiga kursi.'],
                    ['一人', 'ひとり', 'hitori', 'satu orang, sendirian', '一人で行きます。', 'Saya pergi sendirian.'],
                    ['二人', 'ふたり', 'futari', 'dua orang', '二人で映画を見ました。', 'Kami berdua menonton film.'],
                    ['三人', 'さんにん', 'sannin', 'tiga orang', '家族は三人です。', 'Keluarga saya tiga orang.'],
                    ['一本', 'いっぽん', 'ippon', 'satu batang (benda panjang)', 'ペンを一本ください。', 'Tolong satu pena.'],
                    ['一枚', 'いちまい', 'ichimai', 'satu lembar (benda tipis)', '紙を一枚ください。', 'Tolong satu lembar kertas.'],
                    ['一台', 'いちだい', 'ichidai', 'satu unit (mesin)', '車が一台あります。', 'Ada satu mobil.'],
                    ['一匹', 'いっぴき', 'ippiki', 'satu ekor (hewan kecil)', '猫が一匹います。', 'Ada seekor kucing.'],
                    ['一冊', 'いっさつ', 'issatsu', 'satu buah (buku)', '本を三冊読みました。', 'Saya membaca tiga buku.'],
                    ['いくつ', 'いくつ', 'ikutsu', 'berapa buah', 'いくつありますか。', 'Ada berapa buah?'],
                    ['いくら', 'いくら', 'ikura', 'berapa harganya', 'これはいくらですか。', 'Ini berapa harganya?'],
                    ['何人', 'なんにん', 'nannin', 'berapa orang', '何人来ますか。', 'Berapa orang yang datang?'],
                    ['半分', 'はんぶん', 'hanbun', 'setengah', '半分あげます。', 'Saya beri setengahnya.'],
                    ['番', 'ばん', 'ban', 'nomor urut', '一番好きです。', 'Paling saya sukai.'],
                ], 'angka'),
            ],

            /* ------------------------------------------------------ time */
            [
                'title' => 'Waktu, hari, dan bulan',
                'summary' => 'Menyebut jam, hari, dan tanggal — termasuk bacaan tanggal yang tidak beraturan.',
                'minutes' => 15,
                'xp' => 30,
                'items' => $this->words([
                    ['今', 'いま', 'ima', 'sekarang', '今何時ですか。', 'Sekarang pukul berapa?'],
                    ['今日', 'きょう', 'kyou', 'hari ini', '今日は暑いですね。', 'Hari ini panas ya.'],
                    ['明日', 'あした', 'ashita', 'besok', '明日会いましょう。', 'Mari bertemu besok.'],
                    ['昨日', 'きのう', 'kinou', 'kemarin', '昨日は雨でした。', 'Kemarin hujan.'],
                    ['朝', 'あさ', 'asa', 'pagi', '毎朝六時に起きます。', 'Saya bangun jam enam setiap pagi.'],
                    ['昼', 'ひる', 'hiru', 'siang', '昼ご飯を食べました。', 'Saya sudah makan siang.'],
                    ['夜', 'よる', 'yoru', 'malam', '夜は静かです。', 'Malam hari sepi.'],
                    ['今朝', 'けさ', 'kesa', 'pagi ini', '今朝は忙しかったです。', 'Pagi ini sibuk.'],
                    ['今晩', 'こんばん', 'konban', 'malam ini', '今晩暇ですか。', 'Malam ini luang?'],
                    ['毎日', 'まいにち', 'mainichi', 'setiap hari', '毎日勉強します。', 'Saya belajar setiap hari.'],
                    ['月曜日', 'げつようび', 'getsuyoubi', 'Senin', '月曜日に会議があります。', 'Ada rapat hari Senin.'],
                    ['火曜日', 'かようび', 'kayoubi', 'Selasa', '火曜日は休みです。', 'Hari Selasa libur.'],
                    ['水曜日', 'すいようび', 'suiyoubi', 'Rabu', '水曜日に来てください。', 'Tolong datang hari Rabu.'],
                    ['木曜日', 'もくようび', 'mokuyoubi', 'Kamis', '木曜日まで待ちます。', 'Saya tunggu sampai Kamis.'],
                    ['金曜日', 'きんようび', 'kinyoubi', 'Jumat', '金曜日の夜は忙しい。', 'Jumat malam sibuk.'],
                    ['土曜日', 'どようび', 'doyoubi', 'Sabtu', '土曜日に買い物します。', 'Saya belanja hari Sabtu.'],
                    ['日曜日', 'にちようび', 'nichiyoubi', 'Minggu', '日曜日は休みます。', 'Hari Minggu saya istirahat.'],
                    ['一日', 'ついたち', 'tsuitachi', 'tanggal 1 (bacaan khusus)', '四月一日に始まります。', 'Dimulai tanggal 1 April.'],
                    ['二日', 'ふつか', 'futsuka', 'tanggal 2, dua hari', '二日かかりました。', 'Butuh dua hari.'],
                    ['二十日', 'はつか', 'hatsuka', 'tanggal 20 (bacaan khusus)', '二十日に帰ります。', 'Saya pulang tanggal 20.'],
                    ['先週', 'せんしゅう', 'senshuu', 'minggu lalu', '先週映画を見ました。', 'Minggu lalu saya menonton film.'],
                    ['来月', 'らいげつ', 'raigetsu', 'bulan depan', '来月日本へ行きます。', 'Bulan depan saya ke Jepang.'],
                ], 'waktu'),
            ],

            /* ---------------------------------------------------- family */
            [
                'title' => 'Keluarga dan orang',
                'summary' => 'Bahasa Jepang membedakan sebutan untuk keluarga sendiri dan keluarga orang lain.',
                'minutes' => 12,
                'xp' => 25,
                'content' => '<p>Aturan penting: gunakan bentuk <strong>merendah</strong> untuk keluarga sendiri (父, 母) dan bentuk <strong>sopan</strong> untuk keluarga orang lain (お父さん, お母さん).</p>',
                'items' => $this->words([
                    ['家族', 'かぞく', 'kazoku', 'keluarga', '家族は五人です。', 'Keluarga saya lima orang.'],
                    ['父', 'ちち', 'chichi', 'ayah (sendiri)', '父は医者です。', 'Ayah saya dokter.'],
                    ['お父さん', 'おとうさん', 'otousan', 'ayah (orang lain)', 'お父さんはお元気ですか。', 'Apa kabar ayah Anda?'],
                    ['母', 'はは', 'haha', 'ibu (sendiri)', '母は先生です。', 'Ibu saya guru.'],
                    ['お母さん', 'おかあさん', 'okaasan', 'ibu (orang lain)', 'お母さんによろしく。', 'Salam untuk ibu Anda.'],
                    ['兄', 'あに', 'ani', 'kakak laki-laki (sendiri)', '兄は大学生です。', 'Kakak saya mahasiswa.'],
                    ['姉', 'あね', 'ane', 'kakak perempuan (sendiri)', '姉は結婚しています。', 'Kakak perempuan saya sudah menikah.'],
                    ['弟', 'おとうと', 'otouto', 'adik laki-laki', '弟は高校生です。', 'Adik saya siswa SMA.'],
                    ['妹', 'いもうと', 'imouto', 'adik perempuan', '妹はまだ小さいです。', 'Adik perempuan saya masih kecil.'],
                    ['子供', 'こども', 'kodomo', 'anak', '子供が二人います。', 'Saya punya dua anak.'],
                    ['祖父', 'そふ', 'sofu', 'kakek (sendiri)', '祖父は九十歳です。', 'Kakek saya 90 tahun.'],
                    ['祖母', 'そぼ', 'sobo', 'nenek (sendiri)', '祖母は元気です。', 'Nenek saya sehat.'],
                    ['友達', 'ともだち', 'tomodachi', 'teman', '友達と映画を見ます。', 'Saya menonton film dengan teman.'],
                    ['先生', 'せんせい', 'sensei', 'guru, dokter', '先生に聞きました。', 'Saya bertanya pada guru.'],
                    ['学生', 'がくせい', 'gakusei', 'pelajar', '私は学生です。', 'Saya seorang pelajar.'],
                    ['会社員', 'かいしゃいん', 'kaishain', 'karyawan', '兄は会社員です。', 'Kakak saya karyawan.'],
                    ['お客さん', 'おきゃくさん', 'okyakusan', 'tamu, pelanggan', 'お客さんが来ました。', 'Tamu sudah datang.'],
                    ['人', 'ひと', 'hito', 'orang', 'あの人は誰ですか。', 'Orang itu siapa?'],
                ], 'keluarga'),
            ],

            /* ------------------------------------------------------ food */
            [
                'title' => 'Makanan dan minuman',
                'minutes' => 12,
                'xp' => 25,
                'items' => $this->words([
                    ['ご飯', 'ごはん', 'gohan', 'nasi, makanan', 'ご飯を食べましょう。', 'Mari makan.'],
                    ['パン', 'ぱん', 'pan', 'roti', '朝はパンを食べます。', 'Pagi hari saya makan roti.'],
                    ['肉', 'にく', 'niku', 'daging', '肉が好きです。', 'Saya suka daging.'],
                    ['魚', 'さかな', 'sakana', 'ikan', '魚を焼きます。', 'Saya memanggang ikan.'],
                    ['野菜', 'やさい', 'yasai', 'sayur', '野菜をたくさん食べます。', 'Saya makan banyak sayur.'],
                    ['果物', 'くだもの', 'kudamono', 'buah', '果物は体にいいです。', 'Buah baik untuk tubuh.'],
                    ['卵', 'たまご', 'tamago', 'telur', '卵を二つ使います。', 'Saya pakai dua telur.'],
                    ['水', 'みず', 'mizu', 'air', '水をください。', 'Tolong airnya.'],
                    ['お茶', 'おちゃ', 'ocha', 'teh', 'お茶を飲みませんか。', 'Mau minum teh?'],
                    ['コーヒー', 'こーひー', 'koohii', 'kopi', 'コーヒーが好きです。', 'Saya suka kopi.'],
                    ['牛乳', 'ぎゅうにゅう', 'gyuunyuu', 'susu sapi', '毎朝牛乳を飲みます。', 'Saya minum susu setiap pagi.'],
                    ['お酒', 'おさけ', 'osake', 'minuman beralkohol', 'お酒は飲みません。', 'Saya tidak minum alkohol.'],
                    ['塩', 'しお', 'shio', 'garam', '塩を少し入れます。', 'Masukkan sedikit garam.'],
                    ['砂糖', 'さとう', 'satou', 'gula', '砂糖はいりません。', 'Tidak perlu gula.'],
                    ['おいしい', 'おいしい', 'oishii', 'enak', 'このラーメンはおいしいです。', 'Ramen ini enak.'],
                    ['まずい', 'まずい', 'mazui', 'tidak enak', 'ちょっとまずいです。', 'Agak tidak enak.'],
                    ['朝ご飯', 'あさごはん', 'asagohan', 'sarapan', '朝ご飯を食べましたか。', 'Sudah sarapan?'],
                    ['昼ご飯', 'ひるごはん', 'hirugohan', 'makan siang', '昼ご飯は何ですか。', 'Makan siangnya apa?'],
                    ['晩ご飯', 'ばんごはん', 'bangohan', 'makan malam', '晩ご飯を作ります。', 'Saya memasak makan malam.'],
                ], 'makanan'),
            ],

            /* ----------------------------------------------------- places */
            [
                'title' => 'Tempat dan arah',
                'minutes' => 12,
                'items' => $this->words([
                    ['家', 'いえ', 'ie', 'rumah', '家に帰ります。', 'Saya pulang ke rumah.'],
                    ['学校', 'がっこう', 'gakkou', 'sekolah', '学校へ行きます。', 'Saya pergi ke sekolah.'],
                    ['会社', 'かいしゃ', 'kaisha', 'perusahaan, kantor', '会社は駅の近くです。', 'Kantornya dekat stasiun.'],
                    ['駅', 'えき', 'eki', 'stasiun', '駅で待っています。', 'Saya menunggu di stasiun.'],
                    ['病院', 'びょういん', 'byouin', 'rumah sakit', '病院へ行きました。', 'Saya pergi ke rumah sakit.'],
                    ['銀行', 'ぎんこう', 'ginkou', 'bank', '銀行はどこですか。', 'Bank di mana?'],
                    ['郵便局', 'ゆうびんきょく', 'yuubinkyoku', 'kantor pos', '郵便局で切手を買います。', 'Saya beli perangko di kantor pos.'],
                    ['店', 'みせ', 'mise', 'toko', 'あの店は安いです。', 'Toko itu murah.'],
                    ['スーパー', 'すーぱー', 'suupaa', 'supermarket', 'スーパーで買い物します。', 'Saya berbelanja di supermarket.'],
                    ['図書館', 'としょかん', 'toshokan', 'perpustakaan', '図書館で勉強します。', 'Saya belajar di perpustakaan.'],
                    ['公園', 'こうえん', 'kouen', 'taman', '公園を散歩します。', 'Saya jalan-jalan di taman.'],
                    ['トイレ', 'といれ', 'toire', 'toilet', 'トイレはどこですか。', 'Toilet di mana?'],
                    ['部屋', 'へや', 'heya', 'kamar', '部屋を掃除します。', 'Saya membersihkan kamar.'],
                    ['上', 'うえ', 'ue', 'atas', '机の上にあります。', 'Ada di atas meja.'],
                    ['下', 'した', 'shita', 'bawah', 'いすの下です。', 'Di bawah kursi.'],
                    ['中', 'なか', 'naka', 'dalam', 'かばんの中にあります。', 'Ada di dalam tas.'],
                    ['外', 'そと', 'soto', 'luar', '外は寒いです。', 'Di luar dingin.'],
                    ['近く', 'ちかく', 'chikaku', 'dekat', '駅の近くに住んでいます。', 'Saya tinggal dekat stasiun.'],
                    ['隣', 'となり', 'tonari', 'sebelah', '隣の部屋です。', 'Kamar sebelah.'],
                    ['どこ', 'どこ', 'doko', 'di mana', 'どこへ行きますか。', 'Mau pergi ke mana?'],
                ], 'tempat'),
            ],

            /* ------------------------------------------------------ verbs */
            [
                'title' => 'Kata kerja sehari-hari',
                'summary' => 'Bentuk kamus dan bentuk ~ます berdampingan, supaya dua-duanya melekat sejak awal.',
                'minutes' => 18,
                'xp' => 35,
                'items' => $this->words([
                    ['行く', 'いく', 'iku → ikimasu', 'pergi', '学校へ行きます。', 'Saya pergi ke sekolah.'],
                    ['来る', 'くる', 'kuru → kimasu', 'datang', '友達が来ます。', 'Teman saya datang.'],
                    ['帰る', 'かえる', 'kaeru → kaerimasu', 'pulang', '六時に帰ります。', 'Saya pulang jam enam.'],
                    ['食べる', 'たべる', 'taberu → tabemasu', 'makan', '朝ご飯を食べます。', 'Saya sarapan.'],
                    ['飲む', 'のむ', 'nomu → nomimasu', 'minum', '水を飲みます。', 'Saya minum air.'],
                    ['見る', 'みる', 'miru → mimasu', 'melihat, menonton', 'テレビを見ます。', 'Saya menonton TV.'],
                    ['聞く', 'きく', 'kiku → kikimasu', 'mendengar, bertanya', '音楽を聞きます。', 'Saya mendengarkan musik.'],
                    ['話す', 'はなす', 'hanasu → hanashimasu', 'berbicara', '日本語を話します。', 'Saya berbicara bahasa Jepang.'],
                    ['読む', 'よむ', 'yomu → yomimasu', 'membaca', '本を読みます。', 'Saya membaca buku.'],
                    ['書く', 'かく', 'kaku → kakimasu', 'menulis', '手紙を書きます。', 'Saya menulis surat.'],
                    ['買う', 'かう', 'kau → kaimasu', 'membeli', 'パンを買います。', 'Saya membeli roti.'],
                    ['する', 'する', 'suru → shimasu', 'melakukan', '勉強をします。', 'Saya belajar.'],
                    ['ある', 'ある', 'aru → arimasu', 'ada (benda mati)', '本があります。', 'Ada buku.'],
                    ['いる', 'いる', 'iru → imasu', 'ada (makhluk hidup)', '猫がいます。', 'Ada kucing.'],
                    ['起きる', 'おきる', 'okiru → okimasu', 'bangun', '六時に起きます。', 'Saya bangun jam enam.'],
                    ['寝る', 'ねる', 'neru → nemasu', 'tidur', '十一時に寝ます。', 'Saya tidur jam sebelas.'],
                    ['働く', 'はたらく', 'hataraku → hatarakimasu', 'bekerja', '会社で働きます。', 'Saya bekerja di kantor.'],
                    ['休む', 'やすむ', 'yasumu → yasumimasu', 'istirahat, libur', '日曜日は休みます。', 'Hari Minggu saya libur.'],
                    ['待つ', 'まつ', 'matsu → machimasu', 'menunggu', 'ここで待ってください。', 'Tolong tunggu di sini.'],
                    ['会う', 'あう', 'au → aimasu', 'bertemu', '友達に会います。', 'Saya bertemu teman.'],
                    ['分かる', 'わかる', 'wakaru → wakarimasu', 'mengerti', '意味が分かりません。', 'Saya tidak mengerti artinya.'],
                    ['教える', 'おしえる', 'oshieru → oshiemasu', 'mengajar, memberi tahu', '日本語を教えます。', 'Saya mengajar bahasa Jepang.'],
                    ['習う', 'ならう', 'narau → naraimasu', 'belajar (dari seseorang)', 'ピアノを習います。', 'Saya belajar piano.'],
                    ['使う', 'つかう', 'tsukau → tsukaimasu', 'memakai', 'パソコンを使います。', 'Saya memakai komputer.'],
                ], 'kata kerja'),
            ],

            /* -------------------------------------------------- adjectives */
            [
                'title' => 'Kata sifat い dan な',
                'summary' => 'Dua golongan kata sifat dengan aturan perubahan yang berbeda.',
                'minutes' => 15,
                'xp' => 30,
                'content' => '<p><strong>Kata sifat い</strong> berubah sendiri: 高い → 高くない → 高かった.</p>'
                    .'<p><strong>Kata sifat な</strong> butuh です/でした: 静か → 静かじゃない → 静かでした.</p>',
                'items' => $this->words([
                    ['大きい', 'おおきい', 'ookii', 'besar (い)', '大きい家ですね。', 'Rumahnya besar ya.'],
                    ['小さい', 'ちいさい', 'chiisai', 'kecil (い)', '小さい鞄がほしいです。', 'Saya ingin tas kecil.'],
                    ['高い', 'たかい', 'takai', 'tinggi, mahal (い)', 'この店は高いです。', 'Toko ini mahal.'],
                    ['安い', 'やすい', 'yasui', 'murah (い)', 'この本は安いです。', 'Buku ini murah.'],
                    ['新しい', 'あたらしい', 'atarashii', 'baru (い)', '新しい車を買いました。', 'Saya membeli mobil baru.'],
                    ['古い', 'ふるい', 'furui', 'lama, tua (い)', '古い建物です。', 'Bangunan tua.'],
                    ['いい', 'いい', 'ii', 'bagus (い, tak beraturan)', '天気がいいですね。', 'Cuacanya bagus ya.'],
                    ['悪い', 'わるい', 'warui', 'buruk (い)', '天気が悪いです。', 'Cuacanya buruk.'],
                    ['暑い', 'あつい', 'atsui', 'panas (cuaca) (い)', '今日は暑いです。', 'Hari ini panas.'],
                    ['寒い', 'さむい', 'samui', 'dingin (cuaca) (い)', '冬は寒いです。', 'Musim dingin itu dingin.'],
                    ['忙しい', 'いそがしい', 'isogashii', 'sibuk (い)', '今週は忙しいです。', 'Minggu ini sibuk.'],
                    ['楽しい', 'たのしい', 'tanoshii', 'menyenangkan (い)', '旅行は楽しかったです。', 'Perjalanannya menyenangkan.'],
                    ['難しい', 'むずかしい', 'muzukashii', 'sulit (い)', '漢字は難しいです。', 'Kanji itu sulit.'],
                    ['易しい', 'やさしい', 'yasashii', 'mudah (い)', 'このテストは易しいです。', 'Tes ini mudah.'],
                    ['面白い', 'おもしろい', 'omoshiroi', 'menarik (い)', 'この本は面白いです。', 'Buku ini menarik.'],
                    ['好き', 'すき', 'suki', 'suka (な)', '音楽が好きです。', 'Saya suka musik.'],
                    ['嫌い', 'きらい', 'kirai', 'tidak suka (な)', '納豆が嫌いです。', 'Saya tidak suka natto.'],
                    ['上手', 'じょうず', 'jouzu', 'pandai (な)', '日本語が上手ですね。', 'Bahasa Jepang Anda bagus ya.'],
                    ['下手', 'へた', 'heta', 'tidak mahir (な)', '料理が下手です。', 'Saya tidak pandai memasak.'],
                    ['元気', 'げんき', 'genki', 'sehat, bersemangat (な)', '元気になりました。', 'Saya sudah sehat.'],
                    ['静か', 'しずか', 'shizuka', 'tenang (な)', 'この町は静かです。', 'Kota ini tenang.'],
                    ['にぎやか', 'にぎやか', 'nigiyaka', 'ramai (な)', '駅前はにぎやかです。', 'Depan stasiun ramai.'],
                    ['便利', 'べんり', 'benri', 'praktis (な)', 'このアプリは便利です。', 'Aplikasi ini praktis.'],
                    ['大丈夫', 'だいじょうぶ', 'daijoubu', 'tidak apa-apa (な)', '大丈夫ですか。', 'Anda tidak apa-apa?'],
                ], 'kata sifat'),
            ],

            /* ---------------------------------------------------- adverbs */
            [
                'title' => 'Kata keterangan dan kata tanya',
                'minutes' => 12,
                'xp' => 25,
                'items' => $this->words([
                    ['とても', 'とても', 'totemo', 'sangat', 'とてもおいしいです。', 'Sangat enak.'],
                    ['少し', 'すこし', 'sukoshi', 'sedikit', '少し待ってください。', 'Tolong tunggu sebentar.'],
                    ['たくさん', 'たくさん', 'takusan', 'banyak', 'たくさん食べました。', 'Saya makan banyak.'],
                    ['あまり', 'あまり', 'amari', 'tidak begitu (+ negatif)', 'あまり分かりません。', 'Saya tidak begitu mengerti.'],
                    ['全然', 'ぜんぜん', 'zenzen', 'sama sekali tidak (+ negatif)', '全然分かりません。', 'Saya sama sekali tidak mengerti.'],
                    ['いつも', 'いつも', 'itsumo', 'selalu', 'いつも七時に起きます。', 'Saya selalu bangun jam tujuh.'],
                    ['よく', 'よく', 'yoku', 'sering, dengan baik', 'よく映画を見ます。', 'Saya sering menonton film.'],
                    ['時々', 'ときどき', 'tokidoki', 'kadang-kadang', '時々散歩します。', 'Kadang saya jalan-jalan.'],
                    ['もう', 'もう', 'mou', 'sudah', 'もう食べました。', 'Saya sudah makan.'],
                    ['まだ', 'まだ', 'mada', 'belum, masih', 'まだ終わっていません。', 'Belum selesai.'],
                    ['ちょっと', 'ちょっと', 'chotto', 'sebentar, agak', 'ちょっと高いです。', 'Agak mahal.'],
                    ['一緒に', 'いっしょに', 'issho ni', 'bersama-sama', '一緒に行きましょう。', 'Mari pergi bersama.'],
                    ['何', 'なに / なん', 'nani / nan', 'apa', 'これは何ですか。', 'Ini apa?'],
                    ['誰', 'だれ', 'dare', 'siapa', 'あの人は誰ですか。', 'Orang itu siapa?'],
                    ['いつ', 'いつ', 'itsu', 'kapan', 'いつ来ますか。', 'Kapan datang?'],
                    ['どうして', 'どうして', 'doushite', 'kenapa', 'どうして休みましたか。', 'Kenapa Anda absen?'],
                    ['どう', 'どう', 'dou', 'bagaimana', '味はどうですか。', 'Bagaimana rasanya?'],
                    ['どの', 'どの', 'dono', 'yang mana', 'どの本ですか。', 'Buku yang mana?'],
                    ['これ', 'これ', 'kore', 'ini (dekat pembicara)', 'これをください。', 'Tolong yang ini.'],
                    ['それ', 'それ', 'sore', 'itu (dekat lawan bicara)', 'それは何ですか。', 'Itu apa?'],
                    ['あれ', 'あれ', 'are', 'itu (jauh dari keduanya)', 'あれは私の車です。', 'Itu mobil saya.'],
                ], 'keterangan'),
            ],
        ];
    }
}
