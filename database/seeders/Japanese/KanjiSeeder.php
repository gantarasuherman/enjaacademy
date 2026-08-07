<?php

declare(strict_types=1);

namespace Database\Seeders\Japanese;

use Database\Seeders\Concerns\SeedsLearningContent;
use Illuminate\Database\Seeder;

/**
 * The complete JLPT N5 kanji set, grouped by theme rather than by stroke count
 * — learners remember 山川田 together far better than they remember an
 * arbitrary numeric ordering.
 *
 * Each row: [kanji, on'yomi, kun'yomi, meaning, strokes, example, example meaning]
 */
class KanjiSeeder extends Seeder
{
    use SeedsLearningContent;

    public function run(): void
    {
        $count = $this->fill('kanji', $this->lessons());

        $this->command?->info("  kanji: {$count} karakter N5");
    }

    /** @param array<int, array<int, string|int>> $rows */
    private function kanji(array $rows): array
    {
        return array_map(fn (array $r) => [
            'term' => $r[0],
            'reading' => $r[2],                       // kun'yomi is what beginners meet first
            'romaji' => $r[1],                        // on'yomi in romaji
            'meaning' => $r[3],
            'example' => $r[5] ?? null,
            'example_meaning' => $r[6] ?? null,
            'extra' => [
                'onyomi' => $r[1],
                'kunyomi' => $r[2],
                'strokes' => $r[4],
                'jlpt' => 'N5',
            ],
        ], $rows);
    }

    private function lessons(): array
    {
        return [
            [
                'title' => 'Angka 1–10',
                'summary' => 'Sepuluh kanji pertama yang dipelajari setiap pemula.',
                'minutes' => 12,
                'xp' => 25,
                'items' => $this->kanji([
                    ['一', 'ICHI', 'ひと(つ)', 'satu', 1, '一つ (hitotsu)', 'satu buah'],
                    ['二', 'NI', 'ふた(つ)', 'dua', 2, '二人 (futari)', 'dua orang'],
                    ['三', 'SAN', 'みっ(つ)', 'tiga', 3, '三月 (sangatsu)', 'Maret'],
                    ['四', 'SHI', 'よっ(つ)', 'empat', 5, '四時 (yoji)', 'pukul empat'],
                    ['五', 'GO', 'いつ(つ)', 'lima', 4, '五分 (gofun)', 'lima menit'],
                    ['六', 'ROKU', 'むっ(つ)', 'enam', 4, '六月 (rokugatsu)', 'Juni'],
                    ['七', 'SHICHI', 'なな(つ)', 'tujuh', 2, '七時 (shichiji)', 'pukul tujuh'],
                    ['八', 'HACHI', 'やっ(つ)', 'delapan', 2, '八日 (youka)', 'tanggal delapan'],
                    ['九', 'KYUU', 'ここの(つ)', 'sembilan', 2, '九月 (kugatsu)', 'September'],
                    ['十', 'JUU', 'とお', 'sepuluh', 2, '十分 (juppun)', 'sepuluh menit'],
                ]),
            ],
            [
                'title' => 'Angka besar dan uang',
                'summary' => 'Satuan yang wajib dikuasai sebelum berbelanja di Jepang.',
                'minutes' => 10,
                'items' => $this->kanji([
                    ['百', 'HYAKU', 'もも', 'seratus', 6, '三百 (sanbyaku)', 'tiga ratus'],
                    ['千', 'SEN', 'ち', 'seribu', 3, '五千 (gosen)', 'lima ribu'],
                    ['万', 'MAN', 'よろず', 'sepuluh ribu', 3, '一万円 (ichiman-en)', '10.000 yen'],
                    ['円', 'EN', 'まる(い)', 'yen, bulat', 4, '千円 (sen-en)', '1.000 yen'],
                    ['何', 'KA', 'なに / なん', 'apa, berapa', 7, '何時 (nanji)', 'pukul berapa'],
                ]),
            ],
            [
                'title' => 'Alam',
                'summary' => 'Kanji piktografik — bentuknya masih menyerupai benda aslinya.',
                'minutes' => 14,
                'xp' => 25,
                'items' => $this->kanji([
                    ['日', 'NICHI', 'ひ / か', 'matahari, hari', 4, '日曜日 (nichiyoubi)', 'hari Minggu'],
                    ['月', 'GETSU', 'つき', 'bulan', 4, '一月 (ichigatsu)', 'Januari'],
                    ['火', 'KA', 'ひ', 'api', 4, '火曜日 (kayoubi)', 'hari Selasa'],
                    ['水', 'SUI', 'みず', 'air', 4, '水曜日 (suiyoubi)', 'hari Rabu'],
                    ['木', 'MOKU', 'き', 'pohon, kayu', 4, '木曜日 (mokuyoubi)', 'hari Kamis'],
                    ['金', 'KIN', 'かね', 'emas, uang', 8, 'お金 (okane)', 'uang'],
                    ['土', 'DO', 'つち', 'tanah', 3, '土曜日 (doyoubi)', 'hari Sabtu'],
                    ['山', 'SAN', 'やま', 'gunung', 3, '富士山 (fujisan)', 'Gunung Fuji'],
                    ['川', 'SEN', 'かわ', 'sungai', 3, '川口 (kawaguchi)', 'muara sungai'],
                    ['天', 'TEN', 'あま', 'langit, surga', 4, '天気 (tenki)', 'cuaca'],
                    ['気', 'KI', '—', 'udara, semangat', 6, '元気 (genki)', 'sehat'],
                    ['雨', 'U', 'あめ', 'hujan', 8, '雨天 (uten)', 'cuaca hujan'],
                    ['空', 'KUU', 'そら', 'langit, kosong', 8, '空港 (kuukou)', 'bandara'],
                    ['花', 'KA', 'はな', 'bunga', 7, '花見 (hanami)', 'melihat bunga sakura'],
                    ['魚', 'GYO', 'さかな', 'ikan', 11, '魚屋 (sakanaya)', 'penjual ikan'],
                ]),
            ],
            [
                'title' => 'Orang dan keluarga',
                'minutes' => 14,
                'xp' => 25,
                'items' => $this->kanji([
                    ['人', 'JIN', 'ひと', 'orang', 2, '日本人 (nihonjin)', 'orang Jepang'],
                    ['男', 'DAN', 'おとこ', 'laki-laki', 7, '男性 (dansei)', 'pria'],
                    ['女', 'JO', 'おんな', 'perempuan', 3, '女性 (josei)', 'wanita'],
                    ['子', 'SHI', 'こ', 'anak', 3, '子供 (kodomo)', 'anak'],
                    ['父', 'FU', 'ちち', 'ayah', 4, 'お父さん (otousan)', 'ayah (sopan)'],
                    ['母', 'BO', 'はは', 'ibu', 5, 'お母さん (okaasan)', 'ibu (sopan)'],
                    ['兄', 'KEI', 'あに', 'kakak laki-laki', 5, 'お兄さん (oniisan)', 'kakak laki-laki'],
                    ['姉', 'SHI', 'あね', 'kakak perempuan', 8, 'お姉さん (oneesan)', 'kakak perempuan'],
                    ['弟', 'TEI', 'おとうと', 'adik laki-laki', 7, '弟さん (otoutosan)', 'adik laki-laki (orang lain)'],
                    ['妹', 'MAI', 'いもうと', 'adik perempuan', 8, '妹さん (imoutosan)', 'adik perempuan (orang lain)'],
                    ['友', 'YUU', 'とも', 'teman', 4, '友達 (tomodachi)', 'teman'],
                    ['名', 'MEI', 'な', 'nama', 6, '名前 (namae)', 'nama'],
                ]),
            ],
            [
                'title' => 'Tubuh dan indra',
                'minutes' => 10,
                'items' => $this->kanji([
                    ['目', 'MOKU', 'め', 'mata', 5, '目薬 (megusuri)', 'obat tetes mata'],
                    ['耳', 'JI', 'みみ', 'telinga', 6, '耳鼻科 (jibika)', 'THT'],
                    ['口', 'KOU', 'くち', 'mulut', 3, '入口 (iriguchi)', 'pintu masuk'],
                    ['手', 'SHU', 'て', 'tangan', 4, '手紙 (tegami)', 'surat'],
                    ['足', 'SOKU', 'あし', 'kaki', 7, '足りる (tariru)', 'cukup'],
                    ['体', 'TAI', 'からだ', 'badan', 7, '体育 (taiiku)', 'pendidikan jasmani'],
                ]),
            ],
            [
                'title' => 'Arah, posisi, dan ukuran',
                'minutes' => 14,
                'xp' => 25,
                'items' => $this->kanji([
                    ['上', 'JOU', 'うえ', 'atas', 3, '上手 (jouzu)', 'pandai'],
                    ['下', 'KA', 'した', 'bawah', 3, '下手 (heta)', 'tidak mahir'],
                    ['中', 'CHUU', 'なか', 'tengah, dalam', 4, '中国 (chuugoku)', 'Tiongkok'],
                    ['外', 'GAI', 'そと', 'luar', 5, '外国 (gaikoku)', 'luar negeri'],
                    ['左', 'SA', 'ひだり', 'kiri', 5, '左手 (hidarite)', 'tangan kiri'],
                    ['右', 'U', 'みぎ', 'kanan', 5, '右側 (migigawa)', 'sisi kanan'],
                    ['前', 'ZEN', 'まえ', 'depan, sebelum', 9, '午前 (gozen)', 'pagi (AM)'],
                    ['後', 'GO', 'うし(ろ) / あと', 'belakang, sesudah', 9, '午後 (gogo)', 'sore (PM)'],
                    ['北', 'HOKU', 'きた', 'utara', 5, '北海道 (hokkaidou)', 'Hokkaido'],
                    ['南', 'NAN', 'みなみ', 'selatan', 9, '南口 (minamiguchi)', 'pintu selatan'],
                    ['東', 'TOU', 'ひがし', 'timur', 8, '東京 (toukyou)', 'Tokyo'],
                    ['西', 'SEI', 'にし', 'barat', 6, '西口 (nishiguchi)', 'pintu barat'],
                    ['大', 'DAI', 'おお(きい)', 'besar', 3, '大学 (daigaku)', 'universitas'],
                    ['小', 'SHOU', 'ちい(さい)', 'kecil', 3, '小学校 (shougakkou)', 'sekolah dasar'],
                    ['高', 'KOU', 'たか(い)', 'tinggi, mahal', 10, '高校 (koukou)', 'SMA'],
                    ['安', 'AN', 'やす(い)', 'murah, tenang', 6, '安心 (anshin)', 'lega'],
                    ['長', 'CHOU', 'なが(い)', 'panjang', 8, '社長 (shachou)', 'direktur'],
                    ['新', 'SHIN', 'あたら(しい)', 'baru', 13, '新聞 (shinbun)', 'koran'],
                    ['古', 'KO', 'ふる(い)', 'lama, tua', 5, '中古 (chuuko)', 'bekas'],
                    ['白', 'HAKU', 'しろ(い)', 'putih', 5, '白紙 (hakushi)', 'kertas kosong'],
                ]),
            ],
            [
                'title' => 'Waktu',
                'minutes' => 12,
                'xp' => 25,
                'items' => $this->kanji([
                    ['年', 'NEN', 'とし', 'tahun', 6, '今年 (kotoshi)', 'tahun ini'],
                    ['時', 'JI', 'とき', 'waktu, jam', 10, '時間 (jikan)', 'waktu'],
                    ['分', 'FUN', 'わ(かる)', 'menit, membagi', 4, '十分 (juppun)', 'sepuluh menit'],
                    ['半', 'HAN', 'なか(ば)', 'setengah', 5, '一時半 (ichijihan)', 'pukul setengah dua'],
                    ['今', 'KON', 'いま', 'sekarang', 4, '今日 (kyou)', 'hari ini'],
                    ['毎', 'MAI', '—', 'setiap', 6, '毎日 (mainichi)', 'setiap hari'],
                    ['週', 'SHUU', '—', 'minggu', 11, '来週 (raishuu)', 'minggu depan'],
                    ['曜', 'YOU', '—', 'hari dalam minggu', 18, '何曜日 (nan-youbi)', 'hari apa'],
                    ['午', 'GO', '—', 'tengah hari', 4, '午後 (gogo)', 'sore'],
                    ['間', 'KAN', 'あいだ', 'jeda, antara', 12, '時間 (jikan)', 'waktu'],
                    ['先', 'SEN', 'さき', 'sebelumnya, ujung', 6, '先生 (sensei)', 'guru'],
                    ['来', 'RAI', 'く(る)', 'datang, mendatang', 7, '来年 (rainen)', 'tahun depan'],
                ]),
            ],
            [
                'title' => 'Tempat dan bangunan',
                'minutes' => 12,
                'items' => $this->kanji([
                    ['国', 'KOKU', 'くに', 'negara', 8, '外国人 (gaikokujin)', 'orang asing'],
                    ['家', 'KA', 'いえ', 'rumah', 10, '家族 (kazoku)', 'keluarga'],
                    ['店', 'TEN', 'みせ', 'toko', 8, '喫茶店 (kissaten)', 'kedai kopi'],
                    ['駅', 'EKI', '—', 'stasiun', 14, '駅前 (ekimae)', 'depan stasiun'],
                    ['社', 'SHA', 'やしろ', 'perusahaan, kuil', 7, '会社 (kaisha)', 'perusahaan'],
                    ['校', 'KOU', '—', 'sekolah', 10, '学校 (gakkou)', 'sekolah'],
                    ['室', 'SHITSU', 'むろ', 'ruangan', 9, '教室 (kyoushitsu)', 'ruang kelas'],
                    ['道', 'DOU', 'みち', 'jalan', 12, '道路 (douro)', 'jalan raya'],
                ]),
            ],
            [
                'title' => 'Kata kerja dasar',
                'summary' => 'Kanji yang paling sering muncul sebagai kata kerja.',
                'minutes' => 15,
                'xp' => 30,
                'items' => $this->kanji([
                    ['行', 'KOU', 'い(く)', 'pergi', 6, '銀行 (ginkou)', 'bank'],
                    ['見', 'KEN', 'み(る)', 'melihat', 7, '見学 (kengaku)', 'kunjungan belajar'],
                    ['聞', 'BUN', 'き(く)', 'mendengar, bertanya', 14, '新聞 (shinbun)', 'koran'],
                    ['言', 'GEN', 'い(う)', 'berkata', 7, '言葉 (kotoba)', 'kata, bahasa'],
                    ['読', 'DOKU', 'よ(む)', 'membaca', 14, '読書 (dokusho)', 'membaca buku'],
                    ['書', 'SHO', 'か(く)', 'menulis', 10, '辞書 (jisho)', 'kamus'],
                    ['話', 'WA', 'はな(す)', 'berbicara', 13, '電話 (denwa)', 'telepon'],
                    ['食', 'SHOKU', 'た(べる)', 'makan', 9, '食堂 (shokudou)', 'kantin'],
                    ['飲', 'IN', 'の(む)', 'minum', 12, '飲み物 (nomimono)', 'minuman'],
                    ['買', 'BAI', 'か(う)', 'membeli', 12, '買い物 (kaimono)', 'belanja'],
                    ['帰', 'KI', 'かえ(る)', 'pulang', 10, '帰国 (kikoku)', 'pulang ke negara asal'],
                    ['出', 'SHUTSU', 'で(る)', 'keluar', 5, '出口 (deguchi)', 'pintu keluar'],
                    ['入', 'NYUU', 'はい(る)', 'masuk', 2, '入学 (nyuugaku)', 'masuk sekolah'],
                    ['立', 'RITSU', 'た(つ)', 'berdiri', 5, '国立 (kokuritsu)', 'milik negara'],
                    ['休', 'KYUU', 'やす(む)', 'istirahat', 6, '休日 (kyuujitsu)', 'hari libur'],
                    ['会', 'KAI', 'あ(う)', 'bertemu', 6, '会議 (kaigi)', 'rapat'],
                    ['作', 'SAKU', 'つく(る)', 'membuat', 7, '作文 (sakubun)', 'karangan'],
                    ['住', 'JUU', 'す(む)', 'tinggal', 7, '住所 (juusho)', 'alamat'],
                    ['待', 'TAI', 'ま(つ)', 'menunggu', 9, '待合室 (machiaishitsu)', 'ruang tunggu'],
                ]),
            ],
            [
                'title' => 'Belajar dan benda sehari-hari',
                'minutes' => 12,
                'items' => $this->kanji([
                    ['学', 'GAKU', 'まな(ぶ)', 'belajar', 8, '学生 (gakusei)', 'pelajar'],
                    ['生', 'SEI', 'い(きる) / う(まれる)', 'hidup, lahir', 5, '先生 (sensei)', 'guru'],
                    ['語', 'GO', 'かた(る)', 'bahasa', 14, '日本語 (nihongo)', 'bahasa Jepang'],
                    ['本', 'HON', 'もと', 'buku, asal', 5, '日本 (nihon)', 'Jepang'],
                    ['文', 'BUN', 'ふみ', 'kalimat, tulisan', 4, '文法 (bunpou)', 'tata bahasa'],
                    ['字', 'JI', 'あざ', 'huruf', 6, '漢字 (kanji)', 'kanji'],
                    ['英', 'EI', '—', 'Inggris, cemerlang', 8, '英語 (eigo)', 'bahasa Inggris'],
                    ['車', 'SHA', 'くるま', 'mobil, kendaraan', 7, '電車 (densha)', 'kereta listrik'],
                    ['電', 'DEN', '—', 'listrik', 13, '電気 (denki)', 'listrik, lampu'],
                    ['物', 'BUTSU', 'もの', 'benda', 8, '食べ物 (tabemono)', 'makanan'],
                    ['服', 'FUKU', '—', 'pakaian', 8, '洋服 (youfuku)', 'pakaian gaya Barat'],
                    ['多', 'TA', 'おお(い)', 'banyak', 6, '多分 (tabun)', 'mungkin'],
                    ['少', 'SHOU', 'すく(ない)', 'sedikit', 4, '少し (sukoshi)', 'sedikit'],
                ]),
            ],
        ];
    }
}
