<?php

declare(strict_types=1);

namespace Database\Seeders\Japanese;

use Database\Seeders\Concerns\SeedsLearningContent;
use Illuminate\Database\Seeder;

/**
 * Complete hiragana and katakana.
 *
 * Both syllabaries are seeded in full: the 46 gojūon, the 25 dakuten and
 * handakuten, and the 33 yōon digraphs — plus the extended katakana used to
 * write foreign sounds. Every character carries an example word so the learner
 * sees it in context rather than in isolation.
 */
class KanaSeeder extends Seeder
{
    use SeedsLearningContent;

    public function run(): void
    {
        $hiragana = $this->fill('hiragana', $this->hiraganaLessons());
        $katakana = $this->fill('katakana', $this->katakanaLessons());

        $this->command?->info("  kana: {$hiragana} hiragana + {$katakana} katakana");
    }

    /* -----------------------------------------------------------------
     | Hiragana
     | -----------------------------------------------------------------
     */

    private function hiraganaLessons(): array
    {
        return [
            [
                'title' => 'Mengenal Hiragana',
                'summary' => 'Apa itu hiragana, kapan dipakai, dan cara membacanya — sebelum mulai menghafal huruf satu per satu.',
                'minutes' => 6,
                'xp' => 10,
                'content' => '<p><strong>Hiragana</strong> (ひらがな) adalah salah satu dari tiga sistem tulisan Jepang, bersama katakana dan kanji. Hiragana adalah huruf <strong>fonetik</strong> — tiap huruf mewakili satu bunyi (suku kata), bukan sebuah arti.</p>'
                    .'<p>Hiragana dipakai untuk:</p>'
                    .'<ul>'
                    .'<li>Kata asli Jepang yang tidak (atau belum) ditulis dengan kanji</li>'
                    .'<li>Partikel tata bahasa (は, が, を, に, dan lainnya)</li>'
                    .'<li>Akhiran kata kerja dan kata sifat yang berubah bentuk (okurigana)</li>'
                    .'<li><em>Furigana</em> — bantuan bacaan kecil di atas kanji yang sulit</li>'
                    .'</ul>'
                    .'<p>Ada 46 huruf dasar (disebut <em>gojūon</em>, "50 bunyi" — meski sebenarnya 46 setelah beberapa dihapus dari penggunaan modern), ditambah variasi <em>dakuten</em> (゛), <em>handakuten</em> (゜), dan gabungan <em>yōon</em> (きゃ, しゅ, dst). Cara belajar paling efektif: hafal per baris konsonan — baris vokal dulu, baru baris K, S, T, dan seterusnya, persis urutan lesson-lesson berikutnya di modul ini.</p>',
                'items' => [],
            ],
            [
                'title' => 'Vokal — あ行',
                'summary' => 'Lima vokal dasar. Semua bunyi hiragana lain dibangun dari kelimanya.',
                'minutes' => 8,
                'items' => $this->kanaItems([
                    ['あ', 'a', 'あめ (ame)', 'hujan'],
                    ['い', 'i', 'いぬ (inu)', 'anjing'],
                    ['う', 'u', 'うみ (umi)', 'laut'],
                    ['え', 'e', 'えき (eki)', 'stasiun'],
                    ['お', 'o', 'おかね (okane)', 'uang'],
                ], 'hiragana'),
            ],
            [
                'title' => 'Baris K — か行',
                'summary' => 'Konsonan k dipasangkan dengan lima vokal.',
                'minutes' => 8,
                'items' => $this->kanaItems([
                    ['か', 'ka', 'かさ (kasa)', 'payung'],
                    ['き', 'ki', 'きって (kitte)', 'perangko'],
                    ['く', 'ku', 'くつ (kutsu)', 'sepatu'],
                    ['け', 'ke', 'けさ (kesa)', 'pagi ini'],
                    ['こ', 'ko', 'こども (kodomo)', 'anak'],
                ], 'hiragana'),
            ],
            [
                'title' => 'Baris S — さ行',
                'summary' => 'Perhatikan し yang dibaca "shi", bukan "si".',
                'minutes' => 8,
                'items' => $this->kanaItems([
                    ['さ', 'sa', 'さかな (sakana)', 'ikan'],
                    ['し', 'shi', 'しごと (shigoto)', 'pekerjaan'],
                    ['す', 'su', 'すし (sushi)', 'sushi'],
                    ['せ', 'se', 'せんせい (sensei)', 'guru'],
                    ['そ', 'so', 'そら (sora)', 'langit'],
                ], 'hiragana'),
            ],
            [
                'title' => 'Baris T — た行',
                'summary' => 'Dua pengecualian: ち dibaca "chi" dan つ dibaca "tsu".',
                'minutes' => 9,
                'items' => $this->kanaItems([
                    ['た', 'ta', 'たまご (tamago)', 'telur'],
                    ['ち', 'chi', 'ちかてつ (chikatetsu)', 'kereta bawah tanah'],
                    ['つ', 'tsu', 'つくえ (tsukue)', 'meja'],
                    ['て', 'te', 'てがみ (tegami)', 'surat'],
                    ['と', 'to', 'とけい (tokei)', 'jam'],
                ], 'hiragana'),
            ],
            [
                'title' => 'Baris N — な行',
                'summary' => 'の juga berfungsi sebagai partikel kepemilikan.',
                'minutes' => 8,
                'items' => $this->kanaItems([
                    ['な', 'na', 'なつ (natsu)', 'musim panas'],
                    ['に', 'ni', 'にく (niku)', 'daging'],
                    ['ぬ', 'nu', 'ぬの (nuno)', 'kain'],
                    ['ね', 'ne', 'ねこ (neko)', 'kucing'],
                    ['の', 'no', 'のみもの (nomimono)', 'minuman'],
                ], 'hiragana'),
            ],
            [
                'title' => 'Baris H — は行',
                'summary' => 'ふ berada di antara "hu" dan "fu"; は sebagai partikel dibaca "wa".',
                'minutes' => 9,
                'items' => $this->kanaItems([
                    ['は', 'ha', 'はな (hana)', 'bunga'],
                    ['ひ', 'hi', 'ひと (hito)', 'orang'],
                    ['ふ', 'fu', 'ふゆ (fuyu)', 'musim dingin'],
                    ['へ', 'he', 'へや (heya)', 'kamar'],
                    ['ほ', 'ho', 'ほん (hon)', 'buku'],
                ], 'hiragana'),
            ],
            [
                'title' => 'Baris M — ま行',
                'summary' => 'Baris tanpa pengecualian bacaan.',
                'minutes' => 8,
                'items' => $this->kanaItems([
                    ['ま', 'ma', 'まど (mado)', 'jendela'],
                    ['み', 'mi', 'みず (mizu)', 'air'],
                    ['む', 'mu', 'むし (mushi)', 'serangga'],
                    ['め', 'me', 'め (me)', 'mata'],
                    ['も', 'mo', 'もの (mono)', 'benda'],
                ], 'hiragana'),
            ],
            [
                'title' => 'Baris Y, R, W dan ん',
                'summary' => 'Baris ya hanya punya tiga bunyi; ん satu-satunya konsonan yang berdiri sendiri.',
                'minutes' => 10,
                'items' => $this->kanaItems([
                    ['や', 'ya', 'やま (yama)', 'gunung'],
                    ['ゆ', 'yu', 'ゆき (yuki)', 'salju'],
                    ['よ', 'yo', 'よる (yoru)', 'malam'],
                    ['ら', 'ra', 'らいねん (rainen)', 'tahun depan'],
                    ['り', 'ri', 'りんご (ringo)', 'apel'],
                    ['る', 'ru', 'るす (rusu)', 'sedang tidak di rumah'],
                    ['れ', 're', 'れきし (rekishi)', 'sejarah'],
                    ['ろ', 'ro', 'ろく (roku)', 'enam'],
                    ['わ', 'wa', 'わたし (watashi)', 'saya'],
                    ['を', 'wo', 'ほんをよむ', 'membaca buku (partikel objek)'],
                    ['ん', 'n', 'にほん (nihon)', 'Jepang'],
                ], 'hiragana'),
            ],
            [
                'title' => 'Dakuten — G, Z, D, B',
                'summary' => 'Dua garis kecil (゛) mengubah konsonan tak bersuara menjadi bersuara.',
                'level' => 'N5',
                'minutes' => 12,
                'xp' => 25,
                'items' => $this->kanaItems([
                    ['が', 'ga', 'がっこう (gakkou)', 'sekolah'],
                    ['ぎ', 'gi', 'ぎんこう (ginkou)', 'bank'],
                    ['ぐ', 'gu', 'ぐあい (guai)', 'kondisi'],
                    ['げ', 'ge', 'げんき (genki)', 'sehat, bersemangat'],
                    ['ご', 'go', 'ごはん (gohan)', 'nasi, makanan'],
                    ['ざ', 'za', 'ざっし (zasshi)', 'majalah'],
                    ['じ', 'ji', 'じかん (jikan)', 'waktu'],
                    ['ず', 'zu', 'ちず (chizu)', 'peta'],
                    ['ぜ', 'ze', 'ぜんぶ (zenbu)', 'semua'],
                    ['ぞ', 'zo', 'かぞく (kazoku)', 'keluarga'],
                    ['だ', 'da', 'だいがく (daigaku)', 'universitas'],
                    ['ぢ', 'ji', 'はなぢ (hanaji)', 'mimisan'],
                    ['づ', 'zu', 'つづく (tsuzuku)', 'berlanjut'],
                    ['で', 'de', 'でんわ (denwa)', 'telepon'],
                    ['ど', 'do', 'どこ (doko)', 'di mana'],
                    ['ば', 'ba', 'ばしょ (basho)', 'tempat'],
                    ['び', 'bi', 'びょういん (byouin)', 'rumah sakit'],
                    ['ぶ', 'bu', 'ぶんか (bunka)', 'budaya'],
                    ['べ', 'be', 'べんきょう (benkyou)', 'belajar'],
                    ['ぼ', 'bo', 'ぼうし (boushi)', 'topi'],
                ], 'hiragana'),
            ],
            [
                'title' => 'Handakuten — baris P',
                'summary' => 'Lingkaran kecil (゜) hanya berlaku pada baris は.',
                'minutes' => 8,
                'items' => $this->kanaItems([
                    ['ぱ', 'pa', 'ぱん (pan)', 'roti'],
                    ['ぴ', 'pi', 'えんぴつ (enpitsu)', 'pensil'],
                    ['ぷ', 'pu', 'てんぷら (tenpura)', 'tempura'],
                    ['ぺ', 'pe', 'ぺらぺら (perapera)', 'lancar berbicara'],
                    ['ぽ', 'po', 'さんぽ (sanpo)', 'jalan-jalan'],
                ], 'hiragana'),
            ],
            [
                'title' => 'Yōon — gabungan dengan ゃ ゅ ょ',
                'summary' => 'Kana kecil ya/yu/yo digabung dengan bunyi baris i menjadi satu suku kata.',
                'minutes' => 15,
                'xp' => 30,
                'items' => $this->kanaItems([
                    ['きゃ', 'kya', 'きゃく (kyaku)', 'tamu'],
                    ['きゅ', 'kyu', 'きゅう (kyuu)', 'sembilan'],
                    ['きょ', 'kyo', 'きょう (kyou)', 'hari ini'],
                    ['しゃ', 'sha', 'かいしゃ (kaisha)', 'perusahaan'],
                    ['しゅ', 'shu', 'しゅみ (shumi)', 'hobi'],
                    ['しょ', 'sho', 'じしょ (jisho)', 'kamus'],
                    ['ちゃ', 'cha', 'おちゃ (ocha)', 'teh'],
                    ['ちゅ', 'chu', 'ちゅうい (chuui)', 'perhatian'],
                    ['ちょ', 'cho', 'ちょっと (chotto)', 'sedikit'],
                    ['にゃ', 'nya', 'こんにゃく (konnyaku)', 'konnyaku'],
                    ['にゅ', 'nyu', 'にゅうがく (nyuugaku)', 'masuk sekolah'],
                    ['にょ', 'nyo', 'にょうぼう (nyoubou)', 'istri'],
                    ['ひゃ', 'hya', 'ひゃく (hyaku)', 'seratus'],
                    ['ひゅ', 'hyu', 'ひゅうひゅう (hyuuhyuu)', 'suara angin'],
                    ['ひょ', 'hyo', 'ひょうげん (hyougen)', 'ungkapan'],
                    ['みゃ', 'mya', 'みゃく (myaku)', 'denyut nadi'],
                    ['みゅ', 'myu', 'みゅーじっく', 'musik'],
                    ['みょ', 'myo', 'みょうじ (myouji)', 'nama keluarga'],
                    ['りゃ', 'rya', 'りゃくご (ryakugo)', 'singkatan'],
                    ['りゅ', 'ryu', 'りゅうがく (ryuugaku)', 'belajar di luar negeri'],
                    ['りょ', 'ryo', 'りょこう (ryokou)', 'perjalanan'],
                    ['ぎゃ', 'gya', 'ぎゃく (gyaku)', 'kebalikan'],
                    ['ぎゅ', 'gyu', 'ぎゅうにゅう (gyuunyuu)', 'susu sapi'],
                    ['ぎょ', 'gyo', 'ぎょうざ (gyouza)', 'gyoza'],
                    ['じゃ', 'ja', 'じゃがいも (jagaimo)', 'kentang'],
                    ['じゅ', 'ju', 'じゅぎょう (jugyou)', 'pelajaran'],
                    ['じょ', 'jo', 'じょうず (jouzu)', 'pandai'],
                    ['びゃ', 'bya', 'さんびゃく (sanbyaku)', 'tiga ratus'],
                    ['びゅ', 'byu', 'びゅう (byuu)', 'suara angin kencang'],
                    ['びょ', 'byo', 'びょうき (byouki)', 'sakit'],
                    ['ぴゃ', 'pya', 'ろっぴゃく (roppyaku)', 'enam ratus'],
                    ['ぴゅ', 'pyu', 'ぴゅうぴゅう (pyuupyuu)', 'suara siulan angin'],
                    ['ぴょ', 'pyo', 'はっぴょう (happyou)', 'presentasi'],
                ], 'hiragana'),
            ],
            [
                'title' => 'Sokuon dan bunyi panjang',
                'summary' => 'っ kecil menggandakan konsonan; vokal panjang mengubah arti kata.',
                'minutes' => 10,
                'xp' => 25,
                'content' => '<p>Dua aturan ini sering terlewat padahal mengubah arti sepenuhnya.</p>'
                    .'<p><strong>っ (sokuon)</strong> — tsu kecil, menggandakan konsonan sesudahnya dan memberi jeda singkat.</p>'
                    .'<p><strong>Vokal panjang</strong> — ditulis dengan menambah vokal: おばさん (bibi) vs おばあさん (nenek).</p>',
                'items' => [
                    ['term' => 'っ', 'reading' => 'っ', 'romaji' => '(sokuon)', 'meaning' => 'Menggandakan konsonan berikutnya', 'example' => 'きって (kitte)', 'example_meaning' => 'perangko — bandingkan きて (kite) "datanglah"', 'extra' => ['script' => 'hiragana', 'tipe' => 'aturan']],
                    ['term' => 'がっこう', 'reading' => 'がっこう', 'romaji' => 'gakkou', 'meaning' => 'sekolah', 'example' => 'Sokuon + vokal panjang dalam satu kata', 'example_meaning' => 'ga-K-kou', 'extra' => ['script' => 'hiragana', 'tipe' => 'aturan']],
                    ['term' => 'おばさん', 'reading' => 'おばさん', 'romaji' => 'obasan', 'meaning' => 'bibi', 'example' => 'おばあさん (obaasan)', 'example_meaning' => 'nenek — hanya beda satu vokal panjang', 'extra' => ['script' => 'hiragana', 'tipe' => 'aturan']],
                    ['term' => 'ゆき', 'reading' => 'ゆき', 'romaji' => 'yuki', 'meaning' => 'salju', 'example' => 'ゆうき (yuuki)', 'example_meaning' => 'keberanian', 'extra' => ['script' => 'hiragana', 'tipe' => 'aturan']],
                    ['term' => 'おじさん', 'reading' => 'おじさん', 'romaji' => 'ojisan', 'meaning' => 'paman', 'example' => 'おじいさん (ojiisan)', 'example_meaning' => 'kakek', 'extra' => ['script' => 'hiragana', 'tipe' => 'aturan']],
                ],
            ],
        ];
    }

    /* -----------------------------------------------------------------
     | Katakana
     | -----------------------------------------------------------------
     */

    private function katakanaLessons(): array
    {
        return [
            [
                'title' => 'Mengenal Katakana',
                'summary' => 'Apa bedanya dengan hiragana, dan kenapa bentuknya lebih kaku dan bersudut.',
                'minutes' => 6,
                'xp' => 10,
                'content' => '<p><strong>Katakana</strong> (カタカナ) mewakili bunyi yang <em>persis sama</em> dengan hiragana — か dan カ sama-sama dibaca "ka". Bedanya bukan pada bunyi, tapi pada <strong>kapan dipakai</strong> dan <strong>bentuknya</strong>.</p>'
                    .'<p>Katakana dipakai untuk:</p>'
                    .'<ul>'
                    .'<li>Kata serapan dari bahasa asing (コーヒー "koohii" dari <em>coffee</em>)</li>'
                    .'<li>Nama orang dan tempat luar negeri (アメリカ "Amerika")</li>'
                    .'<li>Onomatope dan bunyi-bunyian</li>'
                    .'<li>Penekanan — mirip fungsi huruf miring dalam Bahasa Indonesia</li>'
                    .'</ul>'
                    .'<p>Secara bentuk, katakana lebih <strong>bersudut dan lurus</strong> dibanding hiragana yang melengkung — ini sengaja dirancang begitu supaya dua sistem tulisan mudah dibedakan sekilas pandang dalam satu kalimat. Karena bunyinya sama persis dengan hiragana, belajar katakana biasanya lebih cepat begitu hiragana sudah dikuasai.</p>',
                'items' => [],
            ],
            [
                'title' => 'Vokal — ア行',
                'summary' => 'Katakana dipakai untuk kata serapan, nama asing, dan penekanan.',
                'minutes' => 8,
                'items' => $this->kanaItems([
                    ['ア', 'a', 'アメリカ (amerika)', 'Amerika'],
                    ['イ', 'i', 'インド (indo)', 'India'],
                    ['ウ', 'u', 'ウール (uuru)', 'wol'],
                    ['エ', 'e', 'エアコン (eakon)', 'AC'],
                    ['オ', 'o', 'オレンジ (orenji)', 'jeruk'],
                ], 'katakana'),
            ],
            [
                'title' => 'Baris K — カ行',
                'minutes' => 8,
                'items' => $this->kanaItems([
                    ['カ', 'ka', 'カメラ (kamera)', 'kamera'],
                    ['キ', 'ki', 'キロ (kiro)', 'kilo'],
                    ['ク', 'ku', 'クラス (kurasu)', 'kelas'],
                    ['ケ', 'ke', 'ケーキ (keeki)', 'kue'],
                    ['コ', 'ko', 'コーヒー (koohii)', 'kopi'],
                ], 'katakana'),
            ],
            [
                'title' => 'Baris S — サ行',
                'minutes' => 8,
                'items' => $this->kanaItems([
                    ['サ', 'sa', 'サッカー (sakkaa)', 'sepak bola'],
                    ['シ', 'shi', 'シャツ (shatsu)', 'kemeja'],
                    ['ス', 'su', 'スープ (suupu)', 'sup'],
                    ['セ', 'se', 'セーター (seetaa)', 'sweter'],
                    ['ソ', 'so', 'ソファ (sofa)', 'sofa'],
                ], 'katakana'),
            ],
            [
                'title' => 'Baris T — タ行',
                'summary' => 'ツ dan シ mirip; perhatikan arah goresannya.',
                'minutes' => 9,
                'items' => $this->kanaItems([
                    ['タ', 'ta', 'タクシー (takushii)', 'taksi'],
                    ['チ', 'chi', 'チーズ (chiizu)', 'keju'],
                    ['ツ', 'tsu', 'ツアー (tsuaa)', 'tur'],
                    ['テ', 'te', 'テレビ (terebi)', 'televisi'],
                    ['ト', 'to', 'トイレ (toire)', 'toilet'],
                ], 'katakana'),
            ],
            [
                'title' => 'Baris N — ナ行',
                'minutes' => 8,
                'items' => $this->kanaItems([
                    ['ナ', 'na', 'ナイフ (naifu)', 'pisau'],
                    ['ニ', 'ni', 'ニュース (nyuusu)', 'berita'],
                    ['ヌ', 'nu', 'ヌードル (nuudoru)', 'mi'],
                    ['ネ', 'ne', 'ネクタイ (nekutai)', 'dasi'],
                    ['ノ', 'no', 'ノート (nooto)', 'buku catatan'],
                ], 'katakana'),
            ],
            [
                'title' => 'Baris H — ハ行',
                'minutes' => 8,
                'items' => $this->kanaItems([
                    ['ハ', 'ha', 'ハンカチ (hankachi)', 'sapu tangan'],
                    ['ヒ', 'hi', 'ヒーター (hiitaa)', 'pemanas'],
                    ['フ', 'fu', 'フォーク (fooku)', 'garpu'],
                    ['ヘ', 'he', 'ヘリコプター (herikoputaa)', 'helikopter'],
                    ['ホ', 'ho', 'ホテル (hoteru)', 'hotel'],
                ], 'katakana'),
            ],
            [
                'title' => 'Baris M — マ行',
                'minutes' => 8,
                'items' => $this->kanaItems([
                    ['マ', 'ma', 'マンション (manshon)', 'apartemen'],
                    ['ミ', 'mi', 'ミルク (miruku)', 'susu'],
                    ['ム', 'mu', 'ムード (muudo)', 'suasana'],
                    ['メ', 'me', 'メール (meeru)', 'surel'],
                    ['モ', 'mo', 'モデル (moderu)', 'model'],
                ], 'katakana'),
            ],
            [
                'title' => 'Baris Y, R, W dan ン',
                'summary' => 'ン sering tertukar dengan ソ — perhatikan sudut goresannya.',
                'minutes' => 10,
                'items' => $this->kanaItems([
                    ['ヤ', 'ya', 'タイヤ (taiya)', 'ban'],
                    ['ユ', 'yu', 'ユーモア (yuumoa)', 'humor'],
                    ['ヨ', 'yo', 'ヨガ (yoga)', 'yoga'],
                    ['ラ', 'ra', 'ラジオ (rajio)', 'radio'],
                    ['リ', 'ri', 'リンゴ (ringo)', 'apel'],
                    ['ル', 'ru', 'ルール (ruuru)', 'aturan'],
                    ['レ', 're', 'レストラン (resutoran)', 'restoran'],
                    ['ロ', 'ro', 'ロボット (robotto)', 'robot'],
                    ['ワ', 'wa', 'ワイン (wain)', 'anggur'],
                    ['ヲ', 'wo', '—', 'jarang dipakai dalam katakana'],
                    ['ン', 'n', 'パン (pan)', 'roti'],
                ], 'katakana'),
            ],
            [
                'title' => 'Dakuten dan handakuten',
                'summary' => 'Aturannya sama persis dengan hiragana.',
                'minutes' => 12,
                'xp' => 25,
                'items' => $this->kanaItems([
                    ['ガ', 'ga', 'ガラス (garasu)', 'kaca'],
                    ['ギ', 'gi', 'ギター (gitaa)', 'gitar'],
                    ['グ', 'gu', 'グラス (gurasu)', 'gelas'],
                    ['ゲ', 'ge', 'ゲーム (geemu)', 'permainan'],
                    ['ゴ', 'go', 'ゴム (gomu)', 'karet'],
                    ['ザ', 'za', 'ピザ (piza)', 'pizza'],
                    ['ジ', 'ji', 'ジュース (juusu)', 'jus'],
                    ['ズ', 'zu', 'ズボン (zubon)', 'celana'],
                    ['ゼ', 'ze', 'ゼロ (zero)', 'nol'],
                    ['ゾ', 'zo', 'ゾウ (zou)', 'gajah'],
                    ['ダ', 'da', 'ダンス (dansu)', 'tari'],
                    ['ヂ', 'ji', '—', 'jarang dipakai'],
                    ['ヅ', 'zu', '—', 'jarang dipakai'],
                    ['デ', 'de', 'デパート (depaato)', 'toserba'],
                    ['ド', 'do', 'ドア (doa)', 'pintu'],
                    ['バ', 'ba', 'バス (basu)', 'bus'],
                    ['ビ', 'bi', 'ビル (biru)', 'gedung'],
                    ['ブ', 'bu', 'ブラウス (burausu)', 'blus'],
                    ['ベ', 'be', 'ベッド (beddo)', 'tempat tidur'],
                    ['ボ', 'bo', 'ボタン (botan)', 'kancing'],
                    ['パ', 'pa', 'パソコン (pasokon)', 'komputer'],
                    ['ピ', 'pi', 'ピアノ (piano)', 'piano'],
                    ['プ', 'pu', 'プール (puuru)', 'kolam renang'],
                    ['ペ', 'pe', 'ペン (pen)', 'pena'],
                    ['ポ', 'po', 'ポケット (poketto)', 'saku'],
                ], 'katakana'),
            ],
            [
                'title' => 'Yōon katakana',
                'summary' => 'Digunakan luas pada kata serapan.',
                'minutes' => 14,
                'xp' => 30,
                'items' => $this->kanaItems([
                    ['キャ', 'kya', 'キャンプ (kyanpu)', 'berkemah'],
                    ['キュ', 'kyu', 'キューバ (kyuuba)', 'Kuba'],
                    ['キョ', 'kyo', 'キョロキョロ', 'celingukan'],
                    ['シャ', 'sha', 'シャワー (shawaa)', 'pancuran'],
                    ['シュ', 'shu', 'シュークリーム', 'kue sus'],
                    ['ショ', 'sho', 'ショッピング (shoppingu)', 'berbelanja'],
                    ['チャ', 'cha', 'チャンス (chansu)', 'kesempatan'],
                    ['チュ', 'chu', 'チューブ (chuubu)', 'tabung'],
                    ['チョ', 'cho', 'チョコレート (chokoreeto)', 'cokelat'],
                    ['ニャ', 'nya', 'ニャンニャン', 'suara kucing'],
                    ['ニュ', 'nyu', 'ニュース (nyuusu)', 'berita'],
                    ['ニョ', 'nyo', 'ニョッキ (nyokki)', 'gnocchi'],
                    ['ヒャ', 'hya', '—', 'jarang'],
                    ['ヒュ', 'hyu', 'ヒューズ (hyuuzu)', 'sekring'],
                    ['ヒョ', 'hyo', '—', 'jarang'],
                    ['ミャ', 'mya', 'ミャンマー (myanmaa)', 'Myanmar'],
                    ['ミュ', 'myu', 'ミュージック (myuujikku)', 'musik'],
                    ['ミョ', 'myo', '—', 'jarang'],
                    ['リャ', 'rya', '—', 'jarang'],
                    ['リュ', 'ryu', 'リュック (ryukku)', 'ransel'],
                    ['リョ', 'ryo', '—', 'jarang'],
                    ['ギャ', 'gya', 'ギャング (gyangu)', 'gangster'],
                    ['ギュ', 'gyu', 'ギュッと', 'erat-erat'],
                    ['ギョ', 'gyo', 'ギョーザ (gyooza)', 'gyoza'],
                    ['ジャ', 'ja', 'ジャズ (jazu)', 'jazz'],
                    ['ジュ', 'ju', 'ジュース (juusu)', 'jus'],
                    ['ジョ', 'jo', 'ジョギング (jogingu)', 'lari pagi'],
                    ['ビャ', 'bya', '—', 'jarang'],
                    ['ビュ', 'byu', 'ビュッフェ (byuffe)', 'prasmanan'],
                    ['ビョ', 'byo', '—', 'jarang'],
                    ['ピャ', 'pya', '—', 'jarang'],
                    ['ピュ', 'pyu', 'コンピュータ (konpyuuta)', 'komputer'],
                    ['ピョ', 'pyo', '—', 'jarang'],
                ], 'katakana'),
            ],
            [
                'title' => 'Katakana khusus bunyi asing',
                'summary' => 'Kombinasi tambahan untuk menuliskan bunyi yang tidak ada dalam bahasa Jepang.',
                'minutes' => 12,
                'xp' => 30,
                'content' => '<p>Kombinasi ini muncul setelah masuknya kata serapan Barat, dan tidak ada padanannya di hiragana.</p>',
                'items' => $this->kanaItems([
                    ['ファ', 'fa', 'ファイル (fairu)', 'berkas'],
                    ['フィ', 'fi', 'フィルム (firumu)', 'film'],
                    ['フェ', 'fe', 'カフェ (kafe)', 'kafe'],
                    ['フォ', 'fo', 'フォーク (fooku)', 'garpu'],
                    ['ヴ', 'vu', 'ヴァイオリン', 'biola'],
                    ['ティ', 'ti', 'パーティー (paatii)', 'pesta'],
                    ['ディ', 'di', 'ディスク (disuku)', 'cakram'],
                    ['トゥ', 'tu', 'タトゥー (tatuu)', 'tato'],
                    ['ドゥ', 'du', 'ヒンドゥー (hinduu)', 'Hindu'],
                    ['シェ', 'she', 'シェフ (shefu)', 'koki'],
                    ['ジェ', 'je', 'ジェット (jetto)', 'jet'],
                    ['チェ', 'che', 'チェック (chekku)', 'periksa'],
                    ['ウィ', 'wi', 'ウィスキー (wisukii)', 'wiski'],
                    ['ウェ', 'we', 'ウェブ (webu)', 'web'],
                    ['ウォ', 'wo', 'ウォーター (wootaa)', 'air'],
                    ['ー', 'chōonpu', 'コーヒー (koohii)', 'tanda vokal panjang khusus katakana'],
                ], 'katakana'),
            ],
        ];
    }
}
