-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Anamakine: 127.0.0.1
-- Üretim Zamanı: 21 Tem 2026, 10:56:04
-- Sunucu sürümü: 12.3.2-MariaDB
-- PHP Sürümü: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `stajbilgisistem`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `belge`
--

CREATE TABLE `belge` (
  `belge_id` int(11) NOT NULL,
  `basvuru_id` int(11) NOT NULL,
  `belge_turu` varchar(100) NOT NULL,
  `dosya_adi` varchar(255) NOT NULL,
  `dosya_yolu` varchar(500) NOT NULL,
  `mime_type` varchar(100) DEFAULT NULL,
  `dosya_boyutu` bigint(20) UNSIGNED DEFAULT NULL,
  `yuklenme_tarihi` datetime DEFAULT current_timestamp(),
  `onay_durumu` enum('Bekliyor','Onaylandi','Reddedildi') DEFAULT 'Bekliyor',
  `yukleyen_tur` enum('Ogrenci','BilgiIslem') DEFAULT 'Ogrenci',
  `imza_durumu` enum('Imzasiz','Imzali') DEFAULT 'Imzasiz'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Tablo döküm verisi `belge`
--

INSERT INTO `belge` (`belge_id`, `basvuru_id`, `belge_turu`, `dosya_adi`, `dosya_yolu`, `mime_type`, `dosya_boyutu`, `yuklenme_tarihi`, `onay_durumu`, `yukleyen_tur`, `imza_durumu`) VALUES
(1, 6, 'Staj Sözleşmesi', 'staj kabul belgesi.pdf', 'uploads/belgeler/6a5e176e05bde.pdf', 'application/pdf', 356538, '2026-07-20 15:41:18', 'Onaylandi', 'Ogrenci', 'Imzasiz');

--
-- Tetikleyiciler `belge`
--
DELIMITER $$
CREATE TRIGGER `trg_belge_log` AFTER INSERT ON `belge` FOR EACH ROW BEGIN

INSERT INTO sistem_log
(

islem,

tablo_adi,

kayit_id,

islem_tarihi

)

VALUES
(

'Belge Yüklendi',

'belge',

NEW.belge_id,

NOW()

);

END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `bildirim`
--

CREATE TABLE `bildirim` (
  `bildirim_id` int(11) NOT NULL,
  `kullanici_id` int(11) NOT NULL,
  `baslik` varchar(200) NOT NULL,
  `mesaj` text NOT NULL,
  `tip` enum('Sistem','SMS') DEFAULT 'Sistem',
  `okunma_tarihi` datetime DEFAULT NULL,
  `gonderim_tarihi` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `devamsizlik`
--

CREATE TABLE `devamsizlik` (
  `devamsizlik_id` int(11) NOT NULL,
  `basvuru_id` int(11) NOT NULL,
  `tarih` date NOT NULL,
  `devamsizlik_turu` enum('Izinsiz','Izinli','Raporlu') NOT NULL,
  `aciklama` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `izin_talebi`
--

CREATE TABLE `izin_talebi` (
  `izin_id` int(11) NOT NULL,
  `basvuru_id` int(11) NOT NULL,
  `belge_id` int(11) DEFAULT NULL,
  `onaylayan_id` int(11) DEFAULT NULL,
  `baslangic_tarihi` date NOT NULL,
  `bitis_tarihi` date NOT NULL,
  `mazeret` text NOT NULL,
  `durum` enum('Beklemede','Onaylandi','Reddedildi') DEFAULT 'Beklemede',
  `onay_tarihi` datetime DEFAULT NULL
) ;

--
-- Tetikleyiciler `izin_talebi`
--
DELIMITER $$
CREATE TRIGGER `trg_izin_onay` AFTER UPDATE ON `izin_talebi` FOR EACH ROW BEGIN

    IF NEW.durum='Onaylandi' THEN

        UPDATE devamsizlik

        SET devamsizlik_turu='Izinli'

        WHERE basvuru_id=NEW.basvuru_id

        AND tarih BETWEEN NEW.baslangic_tarihi
                       AND NEW.bitis_tarihi;

    END IF;

END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `kullanici`
--

CREATE TABLE `kullanici` (
  `kullanici_id` int(11) NOT NULL,
  `rol_id` int(11) NOT NULL,
  `ad` varchar(50) NOT NULL,
  `soyad` varchar(50) NOT NULL,
  `email` varchar(150) NOT NULL,
  `telefon` varchar(20) DEFAULT NULL,
  `sifre_hash` varchar(255) NOT NULL,
  `profil_fotografi` varchar(255) DEFAULT NULL,
  `aktif` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Tablo döküm verisi `kullanici`
--

INSERT INTO `kullanici` (`kullanici_id`, `rol_id`, `ad`, `soyad`, `email`, `telefon`, `sifre_hash`, `profil_fotografi`, `aktif`, `created_at`, `updated_at`) VALUES
(2, 2, 'osman', 'aslan', 'osman@test.com', '5555555555', '$2y$10$iPvwmypSXSn7H2KFEdLBruINsWD72oP7OLIROI.M3bsKottCtGaSG', NULL, 1, '2026-07-20 09:44:26', '2026-07-20 09:44:26');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `kurum_degerlendirme`
--

CREATE TABLE `kurum_degerlendirme` (
  `kurum_degerlendirme_id` int(11) NOT NULL,
  `basvuru_id` int(11) NOT NULL,
  `puan` tinyint(3) UNSIGNED NOT NULL,
  `yorum` text DEFAULT NULL,
  `degerlendirme_tarihi` datetime DEFAULT current_timestamp()
) ;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `ogrenci`
--

CREATE TABLE `ogrenci` (
  `ogrenci_id` int(11) NOT NULL,
  `kullanici_id` int(11) NOT NULL,
  `ogrenci_no` varchar(20) NOT NULL,
  `tc_no` char(11) NOT NULL,
  `fakulte` varchar(100) NOT NULL,
  `bolum` varchar(100) NOT NULL,
  `sinif` varchar(20) DEFAULT NULL,
  `staj_turu` enum('Zorunlu Yaz Stajı I','Zorunlu Yaz Stajı II','Gönüllü Staj','İşyeri Eğitimi') NOT NULL,
  `dogum_tarihi` date DEFAULT NULL,
  `adres` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Tablo döküm verisi `ogrenci`
--

INSERT INTO `ogrenci` (`ogrenci_id`, `kullanici_id`, `ogrenci_no`, `tc_no`, `fakulte`, `bolum`, `sinif`, `staj_turu`, `dogum_tarihi`, `adres`) VALUES
(1, 2, '230260107', '11111111111', 'mühendislik', 'bilgisayar mühendisliği', '4', 'Gönüllü Staj', NULL, 'şahinbey/gaziantep');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `ogrenci_degerlendirme`
--

CREATE TABLE `ogrenci_degerlendirme` (
  `degerlendirme_id` int(11) NOT NULL,
  `basvuru_id` int(11) NOT NULL,
  `sorumlu_id` int(11) NOT NULL,
  `puan` tinyint(3) UNSIGNED NOT NULL,
  `yorum` text DEFAULT NULL,
  `degerlendirme_tarihi` datetime DEFAULT current_timestamp()
) ;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `proje`
--

CREATE TABLE `proje` (
  `proje_id` int(11) NOT NULL,
  `basvuru_id` int(11) NOT NULL,
  `olusturan_sorumlu_id` int(11) NOT NULL,
  `proje_adi` varchar(200) NOT NULL,
  `proje_aciklamasi` text DEFAULT NULL,
  `gereksinimler` text DEFAULT NULL,
  `verilis_tarihi` date NOT NULL,
  `teslim_tarihi` date NOT NULL,
  `durum` enum('Atandi','Devam Ediyor','Teslim Edildi','Revizyon','Tamamlandi') DEFAULT 'Atandi'
) ;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `proje_teslim`
--

CREATE TABLE `proje_teslim` (
  `teslim_id` int(11) NOT NULL,
  `proje_id` int(11) NOT NULL,
  `dosya_adi` varchar(255) NOT NULL,
  `dosya_yolu` varchar(500) NOT NULL,
  `aciklama` text DEFAULT NULL,
  `teslim_durumu` enum('Teslim Edildi','Inceleniyor','Revizyon','Reddedildi','Onaylandi') DEFAULT 'Teslim Edildi',
  `teslim_tarihi` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `resmi_tatil`
--

CREATE TABLE `resmi_tatil` (
  `tatil_id` int(11) NOT NULL,
  `tatil_adi` varchar(100) NOT NULL,
  `tarih` date NOT NULL,
  `aciklama` varchar(255) DEFAULT NULL,
  `aktif` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `rol`
--

CREATE TABLE `rol` (
  `rol_id` int(11) NOT NULL,
  `rol_adi` varchar(50) NOT NULL,
  `aciklama` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Tablo döküm verisi `rol`
--

INSERT INTO `rol` (`rol_id`, `rol_adi`, `aciklama`) VALUES
(1, 'Admin', 'Sistem yöneticisi'),
(2, 'Ogrenci', 'Staj yapan öğrenci'),
(3, 'Sorumlu', 'Staj sorumlusu');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `sistem_ayar`
--

CREATE TABLE `sistem_ayar` (
  `ayar_id` int(11) NOT NULL,
  `ayar_anahtari` varchar(100) NOT NULL,
  `ayar_degeri` text NOT NULL,
  `aciklama` varchar(255) DEFAULT NULL,
  `aktif` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `sistem_log`
--

CREATE TABLE `sistem_log` (
  `log_id` int(11) NOT NULL,
  `kullanici_id` int(11) DEFAULT NULL,
  `islem` varchar(150) NOT NULL,
  `tablo_adi` varchar(100) DEFAULT NULL,
  `kayit_id` int(11) DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `tarayici` varchar(255) DEFAULT NULL,
  `islem_tarihi` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Tablo döküm verisi `sistem_log`
--

INSERT INTO `sistem_log` (`log_id`, `kullanici_id`, `islem`, `tablo_adi`, `kayit_id`, `ip`, `tarayici`, `islem_tarihi`) VALUES
(1, 2, 'Yeni Staj Başvurusu', 'staj_basvurusu', 2, NULL, NULL, '2026-07-20 11:20:35'),
(2, 2, 'Yeni Staj Başvurusu', 'staj_basvurusu', 3, NULL, NULL, '2026-07-20 11:39:44'),
(3, 2, 'Yeni Staj Başvurusu', 'staj_basvurusu', 4, NULL, NULL, '2026-07-20 11:46:35'),
(4, 2, 'Yeni Staj Başvurusu', 'staj_basvurusu', 6, NULL, NULL, '2026-07-20 12:59:25'),
(5, NULL, 'Belge Yüklendi', 'belge', 1, NULL, NULL, '2026-07-20 15:41:18');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `sms_log`
--

CREATE TABLE `sms_log` (
  `sms_id` int(11) NOT NULL,
  `bildirim_id` int(11) NOT NULL,
  `telefon` varchar(20) NOT NULL,
  `mesaj` text NOT NULL,
  `durum` enum('Bekliyor','Gonderildi','Basarisiz') DEFAULT 'Bekliyor',
  `gonderim_tarihi` datetime DEFAULT current_timestamp(),
  `servis_cevabi` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `sorumlu`
--

CREATE TABLE `sorumlu` (
  `sorumlu_id` int(11) NOT NULL,
  `kullanici_id` int(11) NOT NULL,
  `unvan` varchar(100) DEFAULT NULL,
  `birim` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `sorumlu_atama`
--

CREATE TABLE `sorumlu_atama` (
  `atama_id` int(11) NOT NULL,
  `basvuru_id` int(11) NOT NULL,
  `sorumlu_id` int(11) NOT NULL,
  `gorev` varchar(100) DEFAULT NULL,
  `atama_tarihi` datetime DEFAULT current_timestamp(),
  `aktif` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `staj_basvurusu`
--

CREATE TABLE `staj_basvurusu` (
  `basvuru_id` int(11) NOT NULL,
  `ogrenci_id` int(11) NOT NULL,
  `donem_id` int(11) NOT NULL,
  `staj_turu` enum('Zorunlu','Gönüllü') DEFAULT NULL,
  `basvuru_tarihi` datetime NOT NULL DEFAULT current_timestamp(),
  `durum` enum('Beklemede','Onaylandı','Reddedildi') DEFAULT 'Beklemede',
  `aciklama` text DEFAULT NULL,
  `cv_yolu` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `staj_durumu` enum('Başlamadı','Belgeler Bekleniyor','Staj Devam Ediyor','Tamamlandı') DEFAULT 'Başlamadı'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Tablo döküm verisi `staj_basvurusu`
--

INSERT INTO `staj_basvurusu` (`basvuru_id`, `ogrenci_id`, `donem_id`, `staj_turu`, `basvuru_tarihi`, `durum`, `aciklama`, `cv_yolu`, `created_at`, `updated_at`, `staj_durumu`) VALUES
(6, 1, 1, 'Zorunlu', '2026-07-20 12:59:25', 'Onaylandı', 'bilgi işlem stajı', 'uploads/cv/6a5df17d62bda.pdf', '2026-07-20 12:59:25', '2026-07-20 13:48:01', 'Başlamadı');

--
-- Tetikleyiciler `staj_basvurusu`
--
DELIMITER $$
CREATE TRIGGER `trg_basvuru_log` AFTER INSERT ON `staj_basvurusu` FOR EACH ROW BEGIN

INSERT INTO sistem_log
(
kullanici_id,

islem,

tablo_adi,

kayit_id,

islem_tarihi
)

SELECT

o.kullanici_id,

'Yeni Staj Başvurusu',

'staj_basvurusu',

NEW.basvuru_id,

NOW()

FROM ogrenci o

WHERE o.ogrenci_id=NEW.ogrenci_id;

END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `staj_donemi`
--

CREATE TABLE `staj_donemi` (
  `donem_id` int(11) NOT NULL,
  `donem_adi` varchar(50) NOT NULL,
  `basvuru_baslangic` date NOT NULL,
  `basvuru_bitis` date NOT NULL,
  `staj_baslangic` date NOT NULL,
  `staj_bitis` date NOT NULL,
  `aktif` tinyint(1) NOT NULL DEFAULT 0
) ;

--
-- Tablo döküm verisi `staj_donemi`
--

INSERT INTO `staj_donemi` (`donem_id`, `donem_adi`, `basvuru_baslangic`, `basvuru_bitis`, `staj_baslangic`, `staj_bitis`, `aktif`) VALUES
(1, '2026 Yaz Stajı', '2026-05-01', '2026-06-01', '2026-06-15', '2026-08-15', 1);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `yetkili_ip`
--

CREATE TABLE `yetkili_ip` (
  `ip_id` int(11) NOT NULL,
  `ip_adi` varchar(100) NOT NULL,
  `ip_adresi` varchar(45) NOT NULL,
  `aktif` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `yoklama`
--

CREATE TABLE `yoklama` (
  `yoklama_id` int(11) NOT NULL,
  `basvuru_id` int(11) NOT NULL,
  `islem_zamani` datetime NOT NULL,
  `islem_tipi` enum('Giris','Cikis') NOT NULL,
  `oturum_tipi` enum('Normal','Ogle') NOT NULL DEFAULT 'Normal',
  `ip_adresi` varchar(45) NOT NULL,
  `cihaz_bilgisi` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `belge`
--
ALTER TABLE `belge`
  ADD PRIMARY KEY (`belge_id`),
  ADD KEY `fk_belge_basvuru` (`basvuru_id`),
  ADD KEY `idx_belge_tur` (`belge_turu`);

--
-- Tablo için indeksler `bildirim`
--
ALTER TABLE `bildirim`
  ADD PRIMARY KEY (`bildirim_id`),
  ADD KEY `fk_bildirim_kullanici` (`kullanici_id`);

--
-- Tablo için indeksler `devamsizlik`
--
ALTER TABLE `devamsizlik`
  ADD PRIMARY KEY (`devamsizlik_id`),
  ADD UNIQUE KEY `uq_devamsizlik` (`basvuru_id`,`tarih`),
  ADD KEY `idx_devamsizlik_tarih` (`tarih`);

--
-- Tablo için indeksler `izin_talebi`
--
ALTER TABLE `izin_talebi`
  ADD PRIMARY KEY (`izin_id`),
  ADD KEY `fk_izin_basvuru` (`basvuru_id`),
  ADD KEY `fk_izin_belge` (`belge_id`),
  ADD KEY `fk_izin_onaylayan` (`onaylayan_id`);

--
-- Tablo için indeksler `kullanici`
--
ALTER TABLE `kullanici`
  ADD PRIMARY KEY (`kullanici_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_kullanici_rol` (`rol_id`);

--
-- Tablo için indeksler `kurum_degerlendirme`
--
ALTER TABLE `kurum_degerlendirme`
  ADD PRIMARY KEY (`kurum_degerlendirme_id`),
  ADD UNIQUE KEY `uq_kurum_degerlendirme` (`basvuru_id`);

--
-- Tablo için indeksler `ogrenci`
--
ALTER TABLE `ogrenci`
  ADD PRIMARY KEY (`ogrenci_id`),
  ADD UNIQUE KEY `kullanici_id` (`kullanici_id`),
  ADD UNIQUE KEY `ogrenci_no` (`ogrenci_no`),
  ADD UNIQUE KEY `tc_no` (`tc_no`);

--
-- Tablo için indeksler `ogrenci_degerlendirme`
--
ALTER TABLE `ogrenci_degerlendirme`
  ADD PRIMARY KEY (`degerlendirme_id`),
  ADD UNIQUE KEY `uq_ogrenci_degerlendirme` (`basvuru_id`,`sorumlu_id`),
  ADD KEY `fk_ogrenci_deg_sorumlu` (`sorumlu_id`);

--
-- Tablo için indeksler `proje`
--
ALTER TABLE `proje`
  ADD PRIMARY KEY (`proje_id`),
  ADD KEY `fk_proje_basvuru` (`basvuru_id`),
  ADD KEY `fk_proje_sorumlu` (`olusturan_sorumlu_id`);

--
-- Tablo için indeksler `proje_teslim`
--
ALTER TABLE `proje_teslim`
  ADD PRIMARY KEY (`teslim_id`),
  ADD KEY `fk_teslim_proje` (`proje_id`);

--
-- Tablo için indeksler `resmi_tatil`
--
ALTER TABLE `resmi_tatil`
  ADD PRIMARY KEY (`tatil_id`),
  ADD UNIQUE KEY `tarih` (`tarih`);

--
-- Tablo için indeksler `rol`
--
ALTER TABLE `rol`
  ADD PRIMARY KEY (`rol_id`),
  ADD UNIQUE KEY `rol_adi` (`rol_adi`);

--
-- Tablo için indeksler `sistem_ayar`
--
ALTER TABLE `sistem_ayar`
  ADD PRIMARY KEY (`ayar_id`),
  ADD UNIQUE KEY `ayar_anahtari` (`ayar_anahtari`);

--
-- Tablo için indeksler `sistem_log`
--
ALTER TABLE `sistem_log`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `fk_log_kullanici` (`kullanici_id`);

--
-- Tablo için indeksler `sms_log`
--
ALTER TABLE `sms_log`
  ADD PRIMARY KEY (`sms_id`),
  ADD KEY `fk_sms_bildirim` (`bildirim_id`);

--
-- Tablo için indeksler `sorumlu`
--
ALTER TABLE `sorumlu`
  ADD PRIMARY KEY (`sorumlu_id`),
  ADD UNIQUE KEY `kullanici_id` (`kullanici_id`);

--
-- Tablo için indeksler `sorumlu_atama`
--
ALTER TABLE `sorumlu_atama`
  ADD PRIMARY KEY (`atama_id`),
  ADD UNIQUE KEY `uq_basvuru_sorumlu` (`basvuru_id`,`sorumlu_id`),
  ADD KEY `fk_atama_sorumlu` (`sorumlu_id`);

--
-- Tablo için indeksler `staj_basvurusu`
--
ALTER TABLE `staj_basvurusu`
  ADD PRIMARY KEY (`basvuru_id`),
  ADD KEY `idx_ogrenci_donem` (`ogrenci_id`,`donem_id`),
  ADD KEY `fk_basvuru_donem` (`donem_id`),
  ADD KEY `idx_basvuru_durum` (`durum`);

--
-- Tablo için indeksler `staj_donemi`
--
ALTER TABLE `staj_donemi`
  ADD PRIMARY KEY (`donem_id`);

--
-- Tablo için indeksler `yetkili_ip`
--
ALTER TABLE `yetkili_ip`
  ADD PRIMARY KEY (`ip_id`),
  ADD UNIQUE KEY `ip_adresi` (`ip_adresi`);

--
-- Tablo için indeksler `yoklama`
--
ALTER TABLE `yoklama`
  ADD PRIMARY KEY (`yoklama_id`),
  ADD KEY `idx_yoklama_tarih` (`islem_zamani`),
  ADD KEY `idx_yoklama_basvuru` (`basvuru_id`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `belge`
--
ALTER TABLE `belge`
  MODIFY `belge_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Tablo için AUTO_INCREMENT değeri `bildirim`
--
ALTER TABLE `bildirim`
  MODIFY `bildirim_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `devamsizlik`
--
ALTER TABLE `devamsizlik`
  MODIFY `devamsizlik_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `izin_talebi`
--
ALTER TABLE `izin_talebi`
  MODIFY `izin_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `kullanici`
--
ALTER TABLE `kullanici`
  MODIFY `kullanici_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Tablo için AUTO_INCREMENT değeri `kurum_degerlendirme`
--
ALTER TABLE `kurum_degerlendirme`
  MODIFY `kurum_degerlendirme_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `ogrenci`
--
ALTER TABLE `ogrenci`
  MODIFY `ogrenci_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Tablo için AUTO_INCREMENT değeri `ogrenci_degerlendirme`
--
ALTER TABLE `ogrenci_degerlendirme`
  MODIFY `degerlendirme_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `proje`
--
ALTER TABLE `proje`
  MODIFY `proje_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `proje_teslim`
--
ALTER TABLE `proje_teslim`
  MODIFY `teslim_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `resmi_tatil`
--
ALTER TABLE `resmi_tatil`
  MODIFY `tatil_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `rol`
--
ALTER TABLE `rol`
  MODIFY `rol_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Tablo için AUTO_INCREMENT değeri `sistem_ayar`
--
ALTER TABLE `sistem_ayar`
  MODIFY `ayar_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `sistem_log`
--
ALTER TABLE `sistem_log`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Tablo için AUTO_INCREMENT değeri `sms_log`
--
ALTER TABLE `sms_log`
  MODIFY `sms_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `sorumlu`
--
ALTER TABLE `sorumlu`
  MODIFY `sorumlu_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `sorumlu_atama`
--
ALTER TABLE `sorumlu_atama`
  MODIFY `atama_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `staj_basvurusu`
--
ALTER TABLE `staj_basvurusu`
  MODIFY `basvuru_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Tablo için AUTO_INCREMENT değeri `staj_donemi`
--
ALTER TABLE `staj_donemi`
  MODIFY `donem_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `yetkili_ip`
--
ALTER TABLE `yetkili_ip`
  MODIFY `ip_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `yoklama`
--
ALTER TABLE `yoklama`
  MODIFY `yoklama_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Dökümü yapılmış tablolar için kısıtlamalar
--

--
-- Tablo kısıtlamaları `belge`
--
ALTER TABLE `belge`
  ADD CONSTRAINT `fk_belge_basvuru` FOREIGN KEY (`basvuru_id`) REFERENCES `staj_basvurusu` (`basvuru_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Tablo kısıtlamaları `bildirim`
--
ALTER TABLE `bildirim`
  ADD CONSTRAINT `fk_bildirim_kullanici` FOREIGN KEY (`kullanici_id`) REFERENCES `kullanici` (`kullanici_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Tablo kısıtlamaları `devamsizlik`
--
ALTER TABLE `devamsizlik`
  ADD CONSTRAINT `fk_devamsizlik_basvuru` FOREIGN KEY (`basvuru_id`) REFERENCES `staj_basvurusu` (`basvuru_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Tablo kısıtlamaları `izin_talebi`
--
ALTER TABLE `izin_talebi`
  ADD CONSTRAINT `fk_izin_basvuru` FOREIGN KEY (`basvuru_id`) REFERENCES `staj_basvurusu` (`basvuru_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_izin_belge` FOREIGN KEY (`belge_id`) REFERENCES `belge` (`belge_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_izin_onaylayan` FOREIGN KEY (`onaylayan_id`) REFERENCES `kullanici` (`kullanici_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Tablo kısıtlamaları `kullanici`
--
ALTER TABLE `kullanici`
  ADD CONSTRAINT `fk_kullanici_rol` FOREIGN KEY (`rol_id`) REFERENCES `rol` (`rol_id`) ON UPDATE CASCADE;

--
-- Tablo kısıtlamaları `kurum_degerlendirme`
--
ALTER TABLE `kurum_degerlendirme`
  ADD CONSTRAINT `fk_kurum_deg_basvuru` FOREIGN KEY (`basvuru_id`) REFERENCES `staj_basvurusu` (`basvuru_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Tablo kısıtlamaları `ogrenci`
--
ALTER TABLE `ogrenci`
  ADD CONSTRAINT `fk_ogrenci_kullanici` FOREIGN KEY (`kullanici_id`) REFERENCES `kullanici` (`kullanici_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Tablo kısıtlamaları `ogrenci_degerlendirme`
--
ALTER TABLE `ogrenci_degerlendirme`
  ADD CONSTRAINT `fk_ogrenci_deg_basvuru` FOREIGN KEY (`basvuru_id`) REFERENCES `staj_basvurusu` (`basvuru_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ogrenci_deg_sorumlu` FOREIGN KEY (`sorumlu_id`) REFERENCES `sorumlu` (`sorumlu_id`) ON UPDATE CASCADE;

--
-- Tablo kısıtlamaları `proje`
--
ALTER TABLE `proje`
  ADD CONSTRAINT `fk_proje_basvuru` FOREIGN KEY (`basvuru_id`) REFERENCES `staj_basvurusu` (`basvuru_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_proje_sorumlu` FOREIGN KEY (`olusturan_sorumlu_id`) REFERENCES `sorumlu` (`sorumlu_id`) ON UPDATE CASCADE;

--
-- Tablo kısıtlamaları `proje_teslim`
--
ALTER TABLE `proje_teslim`
  ADD CONSTRAINT `fk_teslim_proje` FOREIGN KEY (`proje_id`) REFERENCES `proje` (`proje_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Tablo kısıtlamaları `sistem_log`
--
ALTER TABLE `sistem_log`
  ADD CONSTRAINT `fk_log_kullanici` FOREIGN KEY (`kullanici_id`) REFERENCES `kullanici` (`kullanici_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Tablo kısıtlamaları `sms_log`
--
ALTER TABLE `sms_log`
  ADD CONSTRAINT `fk_sms_bildirim` FOREIGN KEY (`bildirim_id`) REFERENCES `bildirim` (`bildirim_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Tablo kısıtlamaları `sorumlu`
--
ALTER TABLE `sorumlu`
  ADD CONSTRAINT `fk_sorumlu_kullanici` FOREIGN KEY (`kullanici_id`) REFERENCES `kullanici` (`kullanici_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Tablo kısıtlamaları `sorumlu_atama`
--
ALTER TABLE `sorumlu_atama`
  ADD CONSTRAINT `fk_atama_basvuru` FOREIGN KEY (`basvuru_id`) REFERENCES `staj_basvurusu` (`basvuru_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_atama_sorumlu` FOREIGN KEY (`sorumlu_id`) REFERENCES `sorumlu` (`sorumlu_id`) ON UPDATE CASCADE;

--
-- Tablo kısıtlamaları `staj_basvurusu`
--
ALTER TABLE `staj_basvurusu`
  ADD CONSTRAINT `fk_basvuru_donem` FOREIGN KEY (`donem_id`) REFERENCES `staj_donemi` (`donem_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_basvuru_ogrenci` FOREIGN KEY (`ogrenci_id`) REFERENCES `ogrenci` (`ogrenci_id`) ON UPDATE CASCADE;

--
-- Tablo kısıtlamaları `yoklama`
--
ALTER TABLE `yoklama`
  ADD CONSTRAINT `fk_yoklama_basvuru` FOREIGN KEY (`basvuru_id`) REFERENCES `staj_basvurusu` (`basvuru_id`) ON DELETE CASCADE ON UPDATE CASCADE;
-- --------------------------------------------------------
--
-- Tablo için tablo yapısı `password_reset`
--

CREATE TABLE `password_reset` (
  `password_reset_id` int(11) NOT NULL AUTO_INCREMENT,
  `kullanici_id` int(11) NOT NULL,
  `kod_hash` varchar(255) NOT NULL,
  `son_gecerlilik_tarihi` datetime NOT NULL,
  `deneme_sayisi` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `dogrulandi_at` datetime DEFAULT NULL,
  `kullanildi_at` datetime DEFAULT NULL,
  `iptal_edildi_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`password_reset_id`),
  KEY `idx_password_reset_kullanici` (`kullanici_id`),
  KEY `idx_password_reset_gecerlilik` (`son_gecerlilik_tarihi`),
  CONSTRAINT `fk_password_reset_kullanici` FOREIGN KEY (`kullanici_id`) REFERENCES `kullanici` (`kullanici_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
