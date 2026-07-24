<?php

class SorumluController extends Controller
{
    private function getSorumluOrDie()
    {
        $this->auth();
        if ($_SESSION['kullanici']['rol'] != 3) {
            die("Bu sayfaya erişim yetkiniz yoktur.");
        }
        require_once BASE_PATH . '/app/models/Sorumlu.php';
        $sorumluModel = new Sorumlu();
        $sorumlu = $sorumluModel->sorumluBulByKullanici($_SESSION['kullanici']['id']);
        if (!$sorumlu) {
            die("Sorumlu kaydınız bulunamadı.");
        }
        return $sorumlu;
    }

    public function dashboard()
    {
        $sorumlu = $this->getSorumluOrDie();
        
        $sorumluModel = new Sorumlu();
        $ogrenciler = $sorumluModel->atananOgrenciler($sorumlu['sorumlu_id']);
        $ogrenci_sayisi = count($ogrenciler);

        $this->view('sorumlu/dashboard', [
            'kullanici' => $_SESSION['kullanici'],
            'sorumlu' => $sorumlu,
            'ogrenci_sayisi' => $ogrenci_sayisi
        ]);
    }

    public function ogrenciler()
    {
        $sorumlu = $this->getSorumluOrDie();
        $sorumluModel = new Sorumlu();
        $ogrenciler = $sorumluModel->atananOgrenciler($sorumlu['sorumlu_id']);

        $this->view('sorumlu/ogrenciler', [
            'ogrenciler' => $ogrenciler
        ]);
    }

    public function ogrenciDetay()
    {
        $sorumlu = $this->getSorumluOrDie();
        $basvuru_id = $_GET['basvuru_id'] ?? null;
        if (!$basvuru_id) {
            die("Başvuru belirtilmedi.");
        }

        $sorumluModel = new Sorumlu();
        $detay = $sorumluModel->ogrenciDetayGetir($basvuru_id, $sorumlu['sorumlu_id']);
        if (!$detay) {
            die("Bu öğrenci sizinle eşleştirilmemiş veya bulunamadı.");
        }

        // Belgeler
        require_once BASE_PATH . '/app/models/Belge.php';
        $belgeModel = new Belge();
        $belgeler = $belgeModel->basvuruBelgeleriDetayli($basvuru_id);

        // Yoklamalar
        require_once BASE_PATH . '/app/models/Yoklama.php';
        $yoklamaModel = new Yoklama();
        $yoklamalar = $yoklamaModel->basvuruYoklamaGecmisi($basvuru_id);

        // Devamsızlıklar
        require_once BASE_PATH . '/app/models/Devamsizlik.php';
        $devamsizlikModel = new Devamsizlik();
        $devamsizliklar = $devamsizlikModel->basvuruDevamsizlikListesi($basvuru_id);

        // İzinler
        require_once BASE_PATH . '/app/models/IzinTalebi.php';
        $izinModel = new IzinTalebi();
        $izinler = $izinModel->basvuruIzinleri($basvuru_id);

        // Projeler
        require_once BASE_PATH . '/app/models/Proje.php';
        $projeModel = new Proje();
        $projeler = $projeModel->basvuruProjeleri($basvuru_id);

        // Performans Değerlendirme
        require_once BASE_PATH . '/app/models/OgrenciDegerlendirme.php';
        $degModel = new OgrenciDegerlendirme();
        $degerlendirme = $degModel->degerlendirmeGetir($basvuru_id, $sorumlu['sorumlu_id']);

        $this->view('sorumlu/ogrenci_detay', [
            'detay' => $detay,
            'belgeler' => $belgeler,
            'yoklamalar' => $yoklamalar,
            'devamsizliklar' => $devamsizliklar,
            'izinler' => $izinler,
            'projeler' => $projeler,
            'degerlendirme' => $degerlendirme
        ]);
    }

    public function projeler()
    {
        $sorumlu = $this->getSorumluOrDie();
        require_once BASE_PATH . '/app/models/Proje.php';
        $projeModel = new Proje();
        $projeler = $projeModel->sorumluProjeleri($sorumlu['sorumlu_id']);

        // Her proje için teslimleri de çekelim
        require_once BASE_PATH . '/app/models/ProjeTeslim.php';
        $teslimModel = new ProjeTeslim();
        
        $projelerDetayli = [];
        foreach ($projeler as $p) {
            $p['teslimler'] = $teslimModel->projeTeslimleri($p['proje_id']);
            $projelerDetayli[] = $p;
        }

        // Geliştirme: Dropdown'da listelenmesi için sorumluya atanmış öğrencileri de view'a gönderiyoruz.
        $sorumluModel = new Sorumlu();
        $ogrenciler = $sorumluModel->atananOgrenciler($sorumlu['sorumlu_id']);

        $this->view('sorumlu/projeler', [
            'projeler' => $projelerDetayli,
            'ogrenciler' => $ogrenciler
        ]);
    }


    public function projeGuncelle()
    {
        $sorumlu = $this->getSorumluOrDie();
        
        $teslim_id = $_POST['teslim_id'] ?? null;
        $durum = $_POST['durum'] ?? null;
        $feedback = $_POST['feedback'] ?? '';

        if (!$teslim_id || !$durum) {
            die("Eksik bilgi.");
        }

        // Map status
        // DB enum: 'Teslim Edildi','Inceleniyor','Revizyon','Reddedildi','Onaylandi'
        $mapped_durum = 'Teslim Edildi';
        if ($durum == 'Inceleniyor') $mapped_durum = 'Inceleniyor';
        elseif ($durum == 'Revizyon' || $durum == 'Revize') $mapped_durum = 'Revizyon';
        elseif ($durum == 'Onaylandi' || $durum == 'Onaylandı') $mapped_durum = 'Onaylandi';
        elseif ($durum == 'Reddedildi') $mapped_durum = 'Reddedildi';

        require_once BASE_PATH . '/app/models/ProjeTeslim.php';
        $teslimModel = new ProjeTeslim();
        $teslimModel->durumGuncelle($teslim_id, $mapped_durum, $feedback);

        // Update main proje durum to match if Onaylandi
        $teslim = $teslimModel->teslimBul($teslim_id);
        if ($teslim) {
            require_once BASE_PATH . '/app/models/Proje.php';
            $projeModel = new Proje();
            
            $main_durum = 'Devam Ediyor';
            if ($mapped_durum == 'Onaylandi') $main_durum = 'Tamamlandi';
            elseif ($mapped_durum == 'Revizyon') $main_durum = 'Revizyon';
            elseif ($mapped_durum == 'Inceleniyor') $main_durum = 'Teslim Edildi';

            $projeModel->durumGuncelle($teslim['proje_id'], $main_durum);

            // Log
            require_once BASE_PATH . '/app/models/SistemLog.php';
            $log = new SistemLog();
            $log->logYaz($_SESSION['kullanici']['id'], "Proje Teslim Durumu Güncellendi: " . $mapped_durum, "proje_teslim", $teslim_id);

            // Notify Student
            require_once BASE_PATH . '/app/models/Bildirim.php';
            $dbClass = new Database();
            $db = $dbClass->connect();
            $stmt = $db->prepare("SELECT o.kullanici_id FROM proje p INNER JOIN staj_basvurusu sb ON p.basvuru_id = sb.basvuru_id INNER JOIN ogrenci o ON sb.ogrenci_id = o.ogrenci_id WHERE p.proje_id = :proje_id LIMIT 1");
            $stmt->execute([':proje_id' => $teslim['proje_id']]);
            $student_kullanici_id = $stmt->fetchColumn();

            if ($student_kullanici_id) {
                $bildirim = new Bildirim();
                $durum_tr = $mapped_durum;
                if ($mapped_durum == 'Revizyon') $durum_tr = 'Revize';
                if ($mapped_durum == 'Onaylandi') $durum_tr = 'Onaylandı';
                $bildirim->gonder($student_kullanici_id, "Proje Teslim Sonucu", "Proje teslim durumunuz sorumlunuz tarafından '" . $durum_tr . "' olarak güncellendi.");
            }
        }

        header("Location: index.php?url=sorumlu/projeler");
        exit;
    }

    public function degerlendirme()
    {
        $sorumlu = $this->getSorumluOrDie();
        $basvuru_id = $_GET['basvuru_id'] ?? null;
        if (!$basvuru_id) {
            die("Başvuru belirtilmedi.");
        }

        $sorumluModel = new Sorumlu();
        $detay = $sorumluModel->ogrenciDetayGetir($basvuru_id, $sorumlu['sorumlu_id']);
        if (!$detay) {
            die("Öğrenci bulunamadı.");
        }

        require_once BASE_PATH . '/app/models/OgrenciDegerlendirme.php';
        $degModel = new OgrenciDegerlendirme();
        $degerlendirme = $degModel->degerlendirmeGetir($basvuru_id, $sorumlu['sorumlu_id']);

        $this->view('sorumlu/degerlendirme', [
            'detay' => $detay,
            'degerlendirme' => $degerlendirme
        ]);
    }

    public function degerlendir()
    {
        $sorumlu = $this->getSorumluOrDie();
        
        $basvuru_id = $_POST['basvuru_id'] ?? null;
        $puan = $_POST['puan'] ?? null;
        $yorum = $_POST['yorum'] ?? '';

        if (!$basvuru_id || $puan === null) {
            die("Eksik bilgi.");
        }

        require_once BASE_PATH . '/app/models/OgrenciDegerlendirme.php';
        $degModel = new OgrenciDegerlendirme();
        $degModel->kaydet([
            'basvuru_id' => $basvuru_id,
            'sorumlu_id' => $sorumlu['sorumlu_id'],
            'puan' => $puan,
            'yorum' => $yorum
        ]);

        // Log
        require_once BASE_PATH . '/app/models/SistemLog.php';
        $log = new SistemLog();
        $log->logYaz($_SESSION['kullanici']['id'], "Öğrenci Performans Değerlendirmesi Yapıldı", "staj_basvurusu", $basvuru_id);

        // Notify Student
        require_once BASE_PATH . '/app/models/Bildirim.php';
        $dbClass = new Database();
        $db = $dbClass->connect();
        $stmt = $db->prepare("SELECT o.kullanici_id FROM staj_basvurusu sb INNER JOIN ogrenci o ON sb.ogrenci_id = o.ogrenci_id WHERE sb.basvuru_id = :id LIMIT 1");
        $stmt->execute([':id' => $basvuru_id]);
        $student_kullanici_id = $stmt->fetchColumn();

        if ($student_kullanici_id) {
            $bildirim = new Bildirim();
            $bildirim->gonder($student_kullanici_id, "Staj Değerlendirmesi Yapıldı", "Staj sorumlunuz Ahmet Hoca staj performansınızı değerlendirdi. Puan: " . $puan);
        }

        header("Location: index.php?url=sorumlu/ogrenciDetay&basvuru_id=" . $basvuru_id);
        exit;
    }

    public function yoklama()
    {
        $sorumlu = $this->getSorumluOrDie();
        $sorumluModel = new Sorumlu();
        $ogrenciler = $sorumluModel->atananOgrenciler($sorumlu['sorumlu_id']);

        // Her öğrencinin yoklama kayıtlarını çekelim
        require_once BASE_PATH . '/app/models/Yoklama.php';
        require_once BASE_PATH . '/app/models/Devamsizlik.php';
        $yoklamaModel = new Yoklama();
        $devModel = new Devamsizlik();

        $ogrencilerYoklama = [];
        foreach ($ogrenciler as $o) {
            $o['yoklamalar'] = $yoklamaModel->basvuruYoklamaGecmisi($o['basvuru_id']);
            $o['devamsizliklar'] = $devModel->basvuruDevamsizlikListesi($o['basvuru_id']);
            $ogrencilerYoklama[] = $o;
        }

        $this->view('sorumlu/yoklama', [
            'ogrenciler' => $ogrencilerYoklama
        ]);
    }

    // Geliştirme: Sorumlu panelinde öğrenciye staj projesi atamayı kaydeden aksiyon.
    public function projeAta()
    {
        $sorumlu = $this->getSorumluOrDie();
        
        $basvuru_id = $_POST['basvuru_id'] ?? null;
        $proje_adi = $_POST['proje_adi'] ?? null;
        $proje_aciklamasi = $_POST['proje_aciklamasi'] ?? '';
        $teslim_tarihi = $_POST['teslim_tarihi'] ?? null;

        // Kural: Teslim tarihi zorunlu olsun.
        if (!$basvuru_id || !$proje_adi || !$teslim_tarihi || trim($proje_adi) == '') {
            die("Hata: Öğrenci, Proje Başlığı ve Teslim Tarihi alanları zorunludur.");
        }

        // Kural: Sorumlu yalnızca kendisine atanmış öğrencileri görebilsin/proje atayabilsin (başka sorumlunun öğrencisi listelenmesin/işlem yapılamasın).
        $sorumluModel = new Sorumlu();
        $detay = $sorumluModel->ogrenciDetayGetir($basvuru_id, $sorumlu['sorumlu_id']);
        if (!$detay) {
            die("Yetkisiz işlem: Bu öğrenci size atanmamış.");
        }

        // Kural: Aynı öğrenciye aktif ikinci proje atanamasın.
        require_once BASE_PATH . '/app/models/Proje.php';
        $projeModel = new Proje();
        if ($projeModel->aktifProjeVarMi($basvuru_id)) {
            die("Hata: Bu öğrencinin zaten aktif (tamamlanmamış) bir projesi bulunmaktadır.");
        }

        // Projeyi kaydet
        $projeModel->kaydet([
            'basvuru_id' => $basvuru_id,
            'olusturan_sorumlu_id' => $sorumlu['sorumlu_id'],
            'proje_adi' => $proje_adi,
            'proje_aciklamasi' => $proje_aciklamasi,
            'gereksinimler' => 'Staj Proje Görevi',
            'verilis_tarihi' => date('Y-m-d'),
            'teslim_tarihi' => $teslim_tarihi,
            'durum' => 'Atandi'
        ]);

        // Log yaz
        require_once BASE_PATH . '/app/models/SistemLog.php';
        $log = new SistemLog();
        $log->logYaz($_SESSION['kullanici']['id'], "Öğrenciye Proje Atandı (Basvuru ID: $basvuru_id)", "proje", $basvuru_id);

        // Bildirim gönder
        require_once BASE_PATH . '/app/models/Bildirim.php';
        $student_kullanici_id = $detay['kullanici_id'];
        $bildirim = new Bildirim();
        $bildirim->gonder($student_kullanici_id, "Yeni Proje Atandı", "Staj sorumlunuz size yeni bir proje atadı: " . $proje_adi);

        $_SESSION['flash_message'] = "Proje başarıyla atanmıştır.";

        header("Location: index.php?url=sorumlu/projeler");
        exit;
    }
}
