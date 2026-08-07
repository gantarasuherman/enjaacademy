<?php

declare(strict_types=1);

namespace Database\Seeders\Japanese;

use Database\Seeders\Concerns\SeedsLearningContent;
use Illuminate\Database\Seeder;

/**
 * Situational Japanese dialogues.
 *
 * Each lesson item is one line of dialogue: `term` holds the Japanese,
 * `reading` the kana, `romaji` the transliteration, and `meaning` the
 * Indonesian. `extra.speaker` and `extra.giliran` let the player render it as a
 * two-sided conversation and mark which lines the learner speaks.
 *
 * Row shape: [speaker, japanese, kana, romaji, indonesian, isLearnerTurn]
 */
class ConversationSeeder extends Seeder
{
    use SeedsLearningContent;

    public function run(): void
    {
        $count = $this->fill('percakapan-jepang', $this->lessons());

        $this->command?->info("  percakapan: {$count} baris dialog");
    }

    /** @param array<int, array<int, string|bool>> $rows */
    private function lines(array $rows, string $situasi): array
    {
        return array_map(fn (array $r, int $i) => [
            'term' => $r[1],
            'reading' => $r[2],
            'romaji' => $r[3],
            'meaning' => $r[4],
            'example' => $r[0].':',
            'example_meaning' => ($r[5] ?? false) ? 'Giliran kamu berbicara' : 'Dengarkan',
            'extra' => [
                'speaker' => $r[0],
                'giliran' => ($r[5] ?? false) ? 'peserta' : 'lawan',
                'urutan' => $i + 1,
                'situasi' => $situasi,
            ],
        ], $rows, array_keys($rows));
    }

    private function lessons(): array
    {
        return [
            [
                'title' => 'Perkenalan diri',
                'summary' => 'Dialog pertama yang akan kamu pakai di kelas, kantor, atau pertemuan baru.',
                'minutes' => 12,
                'xp' => 25,
                'content' => '<p><strong>Frasa kunci:</strong> はじめまして · 〜と申します · どうぞよろしくお願いします</p>'
                    .'<p>Urutan bakunya: sapaan → nama → asal/pekerjaan → penutup 「よろしくお願いします」.</p>',
                'items' => $this->lines([
                    ['田中', 'はじめまして。田中です。', 'はじめまして。たなかです。', 'Hajimemashite. Tanaka desu.', 'Salam kenal. Saya Tanaka.', false],
                    ['あなた', 'はじめまして。リナと申します。', 'はじめまして。リナともうします。', 'Hajimemashite. Rina to moushimasu.', 'Salam kenal. Nama saya Rina.', true],
                    ['田中', 'リナさんはどちらのご出身ですか。', 'リナさんはどちらのごしゅっしんですか。', 'Rina-san wa dochira no goshusshin desu ka.', 'Rina-san berasal dari mana?', false],
                    ['あなた', 'インドネシアのジャカルタから来ました。', 'インドネシアのジャカルタからきました。', 'Indoneshia no Jakaruta kara kimashita.', 'Saya datang dari Jakarta, Indonesia.', true],
                    ['田中', 'そうですか。お仕事は何ですか。', 'そうですか。おしごとはなんですか。', 'Sou desu ka. Oshigoto wa nan desu ka.', 'Begitu ya. Pekerjaan Anda apa?', false],
                    ['あなた', 'エンジニアです。今、日本語を勉強しています。', 'エンジニアです。いま、にほんごをべんきょうしています。', 'Enjinia desu. Ima, nihongo o benkyou shite imasu.', 'Saya insinyur. Sekarang sedang belajar bahasa Jepang.', true],
                    ['田中', '日本語が上手ですね。', 'にほんごがじょうずですね。', 'Nihongo ga jouzu desu ne.', 'Bahasa Jepang Anda bagus ya.', false],
                    ['あなた', 'いいえ、まだまだです。どうぞよろしくお願いします。', 'いいえ、まだまだです。どうぞよろしくおねがいします。', 'Iie, madamada desu. Douzo yoroshiku onegaishimasu.', 'Tidak, masih jauh. Mohon bantuannya.', true],
                ], 'perkenalan'),
            ],

            [
                'title' => 'Di restoran',
                'summary' => 'Memesan makanan, bertanya rekomendasi, dan meminta bon.',
                'minutes' => 12,
                'xp' => 25,
                'content' => '<p><strong>Frasa kunci:</strong> 〜をください · おすすめは何ですか · お会計をお願いします</p>',
                'items' => $this->lines([
                    ['店員', 'いらっしゃいませ。何名様ですか。', 'いらっしゃいませ。なんめいさまですか。', 'Irasshaimase. Nanmeisama desu ka.', 'Selamat datang. Berapa orang?', false],
                    ['あなた', '二人です。', 'ふたりです。', 'Futari desu.', 'Dua orang.', true],
                    ['店員', 'こちらへどうぞ。メニューです。', 'こちらへどうぞ。メニューです。', 'Kochira e douzo. Menyuu desu.', 'Silakan ke sini. Ini menunya.', false],
                    ['あなた', 'すみません、おすすめは何ですか。', 'すみません、おすすめはなんですか。', 'Sumimasen, osusume wa nan desu ka.', 'Permisi, apa rekomendasinya?', true],
                    ['店員', '今日は魚が新しいですよ。', 'きょうはさかながあたらしいですよ。', 'Kyou wa sakana ga atarashii desu yo.', 'Hari ini ikannya segar lho.', false],
                    ['あなた', 'じゃあ、それを一つください。', 'じゃあ、それをひとつください。', 'Jaa, sore o hitotsu kudasai.', 'Kalau begitu, tolong satu itu.', true],
                    ['店員', 'お飲み物は何になさいますか。', 'おのみものはなんになさいますか。', 'Onomimono wa nan ni nasaimasu ka.', 'Minumnya mau apa?', false],
                    ['あなた', 'お茶をお願いします。', 'おちゃをおねがいします。', 'Ocha o onegaishimasu.', 'Tolong teh.', true],
                    ['店員', 'かしこまりました。少々お待ちください。', 'かしこまりました。しょうしょうおまちください。', 'Kashikomarimashita. Shoushou omachi kudasai.', 'Baik. Mohon tunggu sebentar.', false],
                    ['あなた', 'すみません、お会計をお願いします。', 'すみません、おかいけいをおねがいします。', 'Sumimasen, okaikei o onegaishimasu.', 'Permisi, tolong bonnya.', true],
                ], 'restoran'),
            ],

            [
                'title' => 'Di stasiun',
                'summary' => 'Bertanya arah, membeli tiket, dan memastikan peron yang benar.',
                'minutes' => 12,
                'xp' => 25,
                'content' => '<p><strong>Frasa kunci:</strong> 〜はどこですか · 何番線ですか · いくらですか</p>',
                'items' => $this->lines([
                    ['あなた', 'すみません、切符売り場はどこですか。', 'すみません、きっぷうりばはどこですか。', 'Sumimasen, kippu uriba wa doko desu ka.', 'Permisi, loket tiket di mana?', true],
                    ['駅員', 'あちらです。あの階段の右側にあります。', 'あちらです。あのかいだんのみぎがわにあります。', 'Achira desu. Ano kaidan no migigawa ni arimasu.', 'Di sebelah sana, di sisi kanan tangga itu.', false],
                    ['あなた', 'ありがとうございます。東京まではいくらですか。', 'ありがとうございます。とうきょうまではいくらですか。', 'Arigatou gozaimasu. Toukyou made wa ikura desu ka.', 'Terima kasih. Sampai Tokyo berapa?', true],
                    ['駅員', '八百六十円です。', 'はっぴゃくろくじゅうえんです。', 'Happyaku rokujuu en desu.', '860 yen.', false],
                    ['あなた', '次の電車は何時ですか。', 'つぎのでんしゃはなんじですか。', 'Tsugi no densha wa nanji desu ka.', 'Kereta berikutnya jam berapa?', true],
                    ['駅員', '十時十五分です。', 'じゅうじじゅうごふんです。', 'Juuji juugofun desu.', 'Pukul 10.15.', false],
                    ['あなた', '何番線ですか。', 'なんばんせんですか。', 'Nanbansen desu ka.', 'Peron nomor berapa?', true],
                    ['駅員', '三番線です。急いでください。', 'さんばんせんです。いそいでください。', 'Sanbansen desu. Isoide kudasai.', 'Peron tiga. Tolong cepat.', false],
                    ['あなた', 'はい、ありがとうございました。', 'はい、ありがとうございました。', 'Hai, arigatou gozaimashita.', 'Baik, terima kasih banyak.', true],
                ], 'stasiun'),
            ],

            [
                'title' => 'Berbelanja',
                'summary' => 'Menanyakan harga, ukuran, dan mencoba barang.',
                'minutes' => 12,
                'xp' => 25,
                'content' => '<p><strong>Frasa kunci:</strong> これはいくらですか · 試着してもいいですか · もっと安いのはありますか</p>',
                'items' => $this->lines([
                    ['店員', 'いらっしゃいませ。何かお探しですか。', 'いらっしゃいませ。なにかおさがしですか。', 'Irasshaimase. Nanika osagashi desu ka.', 'Selamat datang. Sedang mencari sesuatu?', false],
                    ['あなた', 'はい、シャツを探しています。', 'はい、シャツをさがしています。', 'Hai, shatsu o sagashite imasu.', 'Ya, saya sedang mencari kemeja.', true],
                    ['店員', 'こちらはいかがですか。', 'こちらはいかがですか。', 'Kochira wa ikaga desu ka.', 'Bagaimana yang ini?', false],
                    ['あなた', 'これはいくらですか。', 'これはいくらですか。', 'Kore wa ikura desu ka.', 'Ini berapa harganya?', true],
                    ['店員', '三千五百円です。', 'さんぜんごひゃくえんです。', 'Sanzen gohyaku en desu.', '3.500 yen.', false],
                    ['あなた', 'ちょっと高いですね。もっと安いのはありますか。', 'ちょっとたかいですね。もっとやすいのはありますか。', 'Chotto takai desu ne. Motto yasui no wa arimasu ka.', 'Agak mahal ya. Ada yang lebih murah?', true],
                    ['店員', 'では、こちらは二千円です。', 'では、こちらはにせんえんです。', 'Dewa, kochira wa nisen en desu.', 'Kalau begitu, yang ini 2.000 yen.', false],
                    ['あなた', '試着してもいいですか。', 'しちゃくしてもいいですか。', 'Shichaku shite mo ii desu ka.', 'Boleh saya coba?', true],
                    ['店員', 'はい、どうぞ。試着室はあちらです。', 'はい、どうぞ。しちゃくしつはあちらです。', 'Hai, douzo. Shichakushitsu wa achira desu.', 'Silakan. Ruang pas di sebelah sana.', false],
                    ['あなた', 'これをください。カードで払えますか。', 'これをください。カードではらえますか。', 'Kore o kudasai. Kaado de haraemasu ka.', 'Tolong yang ini. Bisa bayar pakai kartu?', true],
                ], 'belanja'),
            ],

            [
                'title' => 'Di rumah sakit',
                'summary' => 'Menjelaskan keluhan dan memahami instruksi dokter.',
                'minutes' => 12,
                'xp' => 25,
                'content' => '<p><strong>Frasa kunci:</strong> 〜が痛いです · 熱があります · お大事に</p>',
                'items' => $this->lines([
                    ['受付', 'どうしましたか。', 'どうしましたか。', 'Dou shimashita ka.', 'Ada keluhan apa?', false],
                    ['あなた', 'お腹が痛いです。', 'おなかがいたいです。', 'Onaka ga itai desu.', 'Perut saya sakit.', true],
                    ['受付', 'いつからですか。', 'いつからですか。', 'Itsu kara desu ka.', 'Sejak kapan?', false],
                    ['あなた', '昨日の夜からです。', 'きのうのよるからです。', 'Kinou no yoru kara desu.', 'Sejak tadi malam.', true],
                    ['医者', '熱はありますか。', 'ねつはありますか。', 'Netsu wa arimasu ka.', 'Apakah demam?', false],
                    ['あなた', 'はい、少しあります。', 'はい、すこしあります。', 'Hai, sukoshi arimasu.', 'Ya, sedikit.', true],
                    ['医者', '薬を出します。一日三回飲んでください。', 'くすりをだします。いちにちさんかいのんでください。', 'Kusuri o dashimasu. Ichinichi sankai nonde kudasai.', 'Saya beri obat. Tolong minum tiga kali sehari.', false],
                    ['あなた', '分かりました。ありがとうございます。', 'わかりました。ありがとうございます。', 'Wakarimashita. Arigatou gozaimasu.', 'Saya mengerti. Terima kasih.', true],
                    ['医者', 'お大事に。', 'おだいじに。', 'Odaiji ni.', 'Semoga lekas sembuh.', false],
                ], 'rumah sakit'),
            ],

            [
                'title' => 'Di kantor',
                'summary' => 'Melapor, meminta izin, dan menyampaikan kabar ke atasan.',
                'minutes' => 14,
                'xp' => 30,
                'content' => '<p><strong>Frasa kunci:</strong> お疲れさまです · 〜てもよろしいでしょうか · 承知しました</p>'
                    .'<p>Perhatikan tingkat kesopanan: kepada atasan gunakan bentuk yang lebih halus daripada kepada rekan sebaya.</p>',
                'items' => $this->lines([
                    ['あなた', '課長、おはようございます。', 'かちょう、おはようございます。', 'Kachou, ohayou gozaimasu.', 'Selamat pagi, Pak Manajer.', true],
                    ['課長', 'おはよう。今日の会議は二時からだよ。', 'おはよう。きょうのかいぎはにじからだよ。', 'Ohayou. Kyou no kaigi wa niji kara da yo.', 'Pagi. Rapat hari ini mulai jam dua.', false],
                    ['あなた', '承知しました。資料はもう準備しました。', 'しょうちしました。しりょうはもうじゅんびしました。', 'Shouchi shimashita. Shiryou wa mou junbi shimashita.', 'Baik. Materinya sudah saya siapkan.', true],
                    ['課長', 'ありがとう。助かるよ。', 'ありがとう。たすかるよ。', 'Arigatou. Tasukaru yo.', 'Terima kasih. Sangat membantu.', false],
                    ['あなた', 'あの、明日午後休んでもよろしいでしょうか。', 'あの、あしたごごやすんでもよろしいでしょうか。', 'Ano, ashita gogo yasunde mo yoroshii deshou ka.', 'Anu, boleh saya izin besok siang?', true],
                    ['課長', '大丈夫だよ。何かあったの。', 'だいじょうぶだよ。なにかあったの。', 'Daijoubu da yo. Nanika atta no.', 'Boleh. Ada apa?', false],
                    ['あなた', '病院へ行かなければなりません。', 'びょういんへいかなければなりません。', 'Byouin e ikanakereba narimasen.', 'Saya harus ke rumah sakit.', true],
                    ['課長', 'そうか。お大事に。', 'そうか。おだいじに。', 'Sou ka. Odaiji ni.', 'Begitu. Semoga lekas sembuh.', false],
                    ['あなた', 'ありがとうございます。お先に失礼します。', 'ありがとうございます。おさきにしつれいします。', 'Arigatou gozaimasu. Osaki ni shitsurei shimasu.', 'Terima kasih. Saya pamit duluan.', true],
                ], 'kantor'),
            ],

            [
                'title' => 'Bertanya arah',
                'summary' => 'Tersesat adalah situasi paling umum bagi pendatang baru.',
                'minutes' => 10,
                'xp' => 25,
                'content' => '<p><strong>Frasa kunci:</strong> 道に迷いました · まっすぐ · 右/左に曲がってください</p>',
                'items' => $this->lines([
                    ['あなた', 'すみません、道に迷いました。', 'すみません、みちにまよいました。', 'Sumimasen, michi ni mayoimashita.', 'Permisi, saya tersesat.', true],
                    ['通行人', 'どちらへ行きたいですか。', 'どちらへいきたいですか。', 'Dochira e ikitai desu ka.', 'Mau pergi ke mana?', false],
                    ['あなた', '中央図書館へ行きたいです。', 'ちゅうおうとしょかんへいきたいです。', 'Chuuou toshokan e ikitai desu.', 'Saya ingin ke perpustakaan pusat.', true],
                    ['通行人', 'この道をまっすぐ行ってください。', 'このみちをまっすぐいってください。', 'Kono michi o massugu itte kudasai.', 'Tolong lurus terus di jalan ini.', false],
                    ['通行人', '二つ目の信号を右に曲がってください。', 'ふたつめのしんごうをみぎにまがってください。', 'Futatsume no shingou o migi ni magatte kudasai.', 'Belok kanan di lampu merah kedua.', false],
                    ['あなた', 'ここから遠いですか。', 'ここからとおいですか。', 'Koko kara tooi desu ka.', 'Jauh dari sini?', true],
                    ['通行人', 'いいえ、歩いて十分ぐらいです。', 'いいえ、あるいてじゅっぷんぐらいです。', 'Iie, aruite juppun gurai desu.', 'Tidak, sekitar sepuluh menit jalan kaki.', false],
                    ['あなた', '本当にありがとうございました。', 'ほんとうにありがとうございました。', 'Hontou ni arigatou gozaimashita.', 'Terima kasih banyak.', true],
                ], 'arah'),
            ],

            [
                'title' => 'Di hotel',
                'summary' => 'Check-in, bertanya fasilitas, dan check-out.',
                'minutes' => 12,
                'xp' => 25,
                'content' => '<p><strong>Frasa kunci:</strong> 予約しています · 朝食は何時からですか · チェックアウトをお願いします</p>',
                'items' => $this->lines([
                    ['フロント', 'いらっしゃいませ。ご予約はございますか。', 'いらっしゃいませ。ごよやくはございますか。', 'Irasshaimase. Goyoyaku wa gozaimasu ka.', 'Selamat datang. Apakah sudah reservasi?', false],
                    ['あなた', 'はい、リナの名前で予約しています。', 'はい、リナのなまえでよやくしています。', 'Hai, Rina no namae de yoyaku shite imasu.', 'Ya, reservasi atas nama Rina.', true],
                    ['フロント', 'かしこまりました。三泊ですね。', 'かしこまりました。さんぱくですね。', 'Kashikomarimashita. Sanpaku desu ne.', 'Baik. Tiga malam ya.', false],
                    ['あなた', 'はい、そうです。朝食は何時からですか。', 'はい、そうです。ちょうしょくはなんじからですか。', 'Hai, sou desu. Choushoku wa nanji kara desu ka.', 'Ya, betul. Sarapan mulai jam berapa?', true],
                    ['フロント', '六時半から十時までです。', 'ろくじはんからじゅうじまでです。', 'Rokujihan kara juuji made desu.', 'Dari pukul 06.30 sampai 10.00.', false],
                    ['あなた', 'Wi-Fiはありますか。', 'ワイファイはありますか。', 'Waifai wa arimasu ka.', 'Ada Wi-Fi?', true],
                    ['フロント', 'はい、無料です。パスワードはこちらです。', 'はい、むりょうです。パスワードはこちらです。', 'Hai, muryou desu. Pasuwaado wa kochira desu.', 'Ada, gratis. Ini kata sandinya.', false],
                    ['あなた', 'チェックアウトは何時ですか。', 'チェックアウトはなんじですか。', 'Chekkuauto wa nanji desu ka.', 'Check-out jam berapa?', true],
                    ['フロント', '十一時です。ごゆっくりどうぞ。', 'じゅういちじです。ごゆっくりどうぞ。', 'Juuichiji desu. Goyukkuri douzo.', 'Pukul sebelas. Selamat beristirahat.', false],
                ], 'hotel'),
            ],
        ];
    }
}
