-- Import lewat phpMyAdmin (pilih database dulu, lalu tab Import).
CREATE TABLE IF NOT EXISTS hasil_kuis (
  id           INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  nama         VARCHAR(100) NOT NULL,
  sekolah      VARCHAR(150) NOT NULL,
  benar        TINYINT UNSIGNED NOT NULL,
  salah        TINYINT UNSIGNED NOT NULL,
  kosong       TINYINT UNSIGNED NOT NULL,
  skor         TINYINT UNSIGNED NOT NULL,
  durasi_detik SMALLINT UNSIGNED NOT NULL,
  ip           VARCHAR(45) DEFAULT NULL,
  waktu        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_sekolah (sekolah),
  KEY idx_waktu (waktu)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
