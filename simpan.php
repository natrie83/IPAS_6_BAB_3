<?php
// Menerima hasil kuis (JSON) dan menyimpannya ke MySQL.
require __DIR__ . '/config.php';
header('Content-Type: application/json; charset=utf-8');

function keluar($ok, $pesan = '', $kode = 200) {
    http_response_code($kode);
    echo json_encode(['ok' => $ok, 'pesan' => $pesan]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') keluar(false, 'Metode tidak diizinkan', 405);

$in = json_decode(file_get_contents('php://input'), true);
if (!is_array($in)) keluar(false, 'Data tidak valid', 400);

$nama    = mb_substr(trim(strip_tags($in['nama'] ?? '')), 0, 100);
$sekolah = mb_substr(trim(strip_tags($in['sekolah'] ?? '')), 0, 150);
if ($nama === '' || $sekolah === '') keluar(false, 'Nama dan sekolah wajib diisi', 400);

$total  = JUMLAH_SOAL;
$benar  = max(0, min($total, (int)($in['benar'] ?? 0)));
$kosong = max(0, min($total - $benar, (int)($in['kosong'] ?? 0)));
$salah  = $total - $benar - $kosong;
$skor   = (int)round($benar * 100 / $total);
$durasi = max(0, min(MENIT_UJIAN * 60 + 10, (int)($in['durasi'] ?? 0)));
$ip     = $_SERVER['REMOTE_ADDR'] ?? '';

try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER, DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    $st = $pdo->prepare(
        'INSERT INTO hasil_kuis (nama, sekolah, benar, salah, kosong, skor, durasi_detik, ip)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
    );
    $st->execute([$nama, $sekolah, $benar, $salah, $kosong, $skor, $durasi, $ip]);
    keluar(true, 'Tersimpan');
} catch (Throwable $e) {
    error_log('kuis simpan: ' . $e->getMessage());
    keluar(false, 'Gagal menyimpan ke database', 500);
}
