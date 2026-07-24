<?php

class IzinController extends Controller
{
    public function index()
    {
        $this->onayliStajBasvurusuZorunlu();
        $this->auth();
        if ($_SESSION['kullanici']['rol'] != 2) {
            die("Bu sayfaya sadece öğrenciler erişebilir.");
        }

        require_once BASE_PATH . '/app/models/StajBasvurusu.php';
        require_once BASE_PATH . '/app/models/IzinTalebi.php';

        $basvuruModel = new StajBasvurusu();
        $basvuru = $basvuruModel->onayliBasvuru($_SESSION['kullanici']['ogrenci_id']);

        $izinler = [];
        if ($basvuru) {
            $izinModel = new IzinTalebi();
            $izinler = $izinModel->basvuruIzinleri($basvuru['basvuru_id']);
        }

        $this->view('ogrenci/izin', [
            'basvuru' => $basvuru,
            'izinler' => $izinler
        ]);
    }

    public function store()
    {
        $this->onayliStajBasvurusuZorunlu();
        $this->auth();
        if ($_SESSION['kullanici']['rol'] != 2) {
            die("Yetkisiz işlem.");
        }

        require_once BASE_PATH . '/app/models/StajBasvurusu.php';
        $basvuruModel = new StajBasvurusu();
        $basvuru = $basvuruModel->onayliBasvuru($_SESSION['kullanici']['ogrenci_id']);

        if (!$basvuru) {
            die("Onaylı bir staj başvurunuz bulunmamaktadır.");
        }

        $baslangic_tarihi = $_POST['baslangic_tarihi'] ?? null;
        $bitis_tarihi = $_POST['bitis_tarihi'] ?? null;
        $mazeret = $_POST['mazeret'] ?? null;

        if (!$baslangic_tarihi || !$bitis_tarihi || !$mazeret) {
            die("Eksik bilgi girdiniz.");
        }

        // İsteğe bağlı dosya yükleme (İzin Raporu/Dilekçesi)
        $belge_id = null;
        if (isset($_FILES['belge']) && $_FILES['belge']['error'] == UPLOAD_ERR_OK) {
            require_once BASE_PATH . '/app/models/Belge.php';
            $belgeModel = new Belge();

            $dosya = $_FILES['belge'];
            $uzanti = strtolower(pathinfo($dosya['name'], PATHINFO_EXTENSION));
            $izinli = ['pdf', 'jpg', 'png'];

            if (!in_array($uzanti, $izinli)) {
                die("Geçersiz belge uzantısı. Sadece PDF, JPG, PNG kabul edilir.");
            }

            if ($dosya['size'] > 5 * 1024 * 1024) {
                die("Dosya boyutu en fazla 5MB olabilir.");
            }

            $yeniAd = uniqid() . "." . $uzanti;
            $hedef = BASE_PATH . "/uploads/belgeler/" . $yeniAd;

            if (move_uploaded_file($dosya['tmp_name'], $hedef)) {
                $belgeModel->kaydet([
                    "basvuru_id" => $basvuru['basvuru_id'],
                    "belge_turu" => "İzin Raporu / Dilekçesi",
                    "dosya_adi" => $dosya['name'],
                    "dosya_yolu" => 'uploads/belgeler/' . $yeniAd,
                    "mime_type" => $dosya['type'],
                    "dosya_boyutu" => $dosya['size'],
                    "yukleyen_tur" => "Ogrenci",
                    "imza_durumu" => "Imzasiz"
                ]);

                // Son eklenen belge_id'sini alalım
                $dbClass = new Database();
                $db = $dbClass->connect();
                $belge_id = $db->lastInsertId();
            }
        }

        require_once BASE_PATH . '/app/models/IzinTalebi.php';
        $izinModel = new IzinTalebi();
        $izinModel->kaydet([
            'basvuru_id' => $basvuru['basvuru_id'],
            'belge_id' => $belge_id,
            'baslangic_tarihi' => $baslangic_tarihi,
            'bitis_tarihi' => $bitis_tarihi,
            'mazeret' => $mazeret
        ]);

        // Log yaz
        require_once BASE_PATH . '/app/models/SistemLog.php';
        $log = new SistemLog();
        $log->logYaz($_SESSION['kullanici']['id'], "İzin Talebi Oluşturuldu", "izin_talebi", $basvuru['basvuru_id']);

        header("Location: index.php?url=ogrenci/izin");
        exit;
    }

    public function adminIndex()
    {
        $this->auth();
        if ($_SESSION['kullanici']['rol'] != 1) {
            die("Bu sayfaya sadece yöneticiler erişebilir.");
        }

        require_once BASE_PATH . '/app/models/IzinTalebi.php';
        $izinModel = new IzinTalebi();
        $izinler = $izinModel->tumIzinler();

        $this->view('admin/izinler', [
            'izinler' => $izinler
        ]);
    }

    public function onayla()
    {
        $this->auth();
        if ($_SESSION['kullanici']['rol'] != 1) {
            die("Yetkisiz işlem.");
        }

        $izin_id = $_GET['id'] ?? null;
        if (!$izin_id) {
            die("İzin belirtilmedi.");
        }

        require_once BASE_PATH . '/app/models/IzinTalebi.php';
        $izinModel = new IzinTalebi();
        $izin = $izinModel->bul($izin_id);

        if ($izin) {
            $izinModel->durumGuncelle($izin_id, 'Onaylandi', $_SESSION['kullanici']['id']);

            // Devamsızlık tablosuna Izinli olarak günleri ekle (Trigger'ın çalışması ve geçmişte görünmesi için)
            require_once BASE_PATH . '/app/models/Devamsizlik.php';
            $devModel = new Devamsizlik();

            $begin = new DateTime($izin['baslangic_tarihi']);
            $end = new DateTime($izin['bitis_tarihi']);
            $end = $end->modify('+1 day'); // inclusive

            $interval = new DateInterval('P1D');
            $daterange = new DatePeriod($begin, $interval ,$end);

            foreach($daterange as $date){
                $tarih_str = $date->format("Y-m-d");
                $devModel->kaydet([
                    'basvuru_id' => $izin['basvuru_id'],
                    'tarih' => $tarih_str,
                    'devamsizlik_turu' => 'Izinli',
                    'aciklama' => 'İzin Talebi Onaylandı (Mazeret: ' . $izin['mazeret'] . ')'
                ]);
            }

            // Log
            require_once BASE_PATH . '/app/models/SistemLog.php';
            $log = new SistemLog();
            $log->logYaz($_SESSION['kullanici']['id'], "İzin Talebi Onaylandı (ID: $izin_id)", "izin_talebi", $izin['basvuru_id']);

            // Bildirim
            require_once BASE_PATH . '/app/models/Bildirim.php';
            require_once BASE_PATH . '/app/services/SmsService.php';
            $dbClass = new Database();
            $db = $dbClass->connect();
            $stmt = $db->prepare("
                SELECT o.kullanici_id, k.telefon, k.ad, k.soyad 
                FROM staj_basvurusu sb 
                INNER JOIN ogrenci o ON sb.ogrenci_id = o.ogrenci_id 
                INNER JOIN kullanici k ON o.kullanici_id = k.kullanici_id 
                WHERE sb.basvuru_id = :id LIMIT 1
            ");
            $stmt->execute([':id' => $izin['basvuru_id']]);
            $student = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($student) {
                $student_kullanici_id = $student['kullanici_id'];
                $telefon = $student['telefon'];
                $adSoyad = $student['ad'] . ' ' . $student['soyad'];
                
                $bildirim = new Bildirim();
                $bildirim->gonder($student_kullanici_id, "İzin Talebiniz Onaylandı", $izin['baslangic_tarihi'] . " - " . $izin['bitis_tarihi'] . " tarihleri arasındaki izin talebiniz onaylanmıştır.");
                
                // SMS gönder
                if (!empty($telefon)) {
                    SmsService::sendLeaveApproved($telefon, $student_kullanici_id, $adSoyad, $izin['baslangic_tarihi'], $izin['bitis_tarihi']);
                }
            }
        }

        header("Location: index.php?url=admin/izinler");
        exit;
    }

    public function reddet()
    {
        $this->auth();
        if ($_SESSION['kullanici']['rol'] != 1) {
            die("Yetkisiz işlem.");
        }

        $izin_id = $_GET['id'] ?? null;
        if (!$izin_id) {
            die("İzin belirtilmedi.");
        }

        require_once BASE_PATH . '/app/models/IzinTalebi.php';
        $izinModel = new IzinTalebi();
        $izin = $izinModel->bul($izin_id);

        if ($izin) {
            $izinModel->durumGuncelle($izin_id, 'Reddedildi', $_SESSION['kullanici']['id']);

            // Log
            require_once BASE_PATH . '/app/models/SistemLog.php';
            $log = new SistemLog();
            $log->logYaz($_SESSION['kullanici']['id'], "İzin Talebi Reddedildi (ID: $izin_id)", "izin_talebi", $izin['basvuru_id']);

            // Bildirim
            require_once BASE_PATH . '/app/models/Bildirim.php';
            require_once BASE_PATH . '/app/services/SmsService.php';
            $dbClass = new Database();
            $db = $dbClass->connect();
            $stmt = $db->prepare("
                SELECT o.kullanici_id, k.telefon, k.ad, k.soyad 
                FROM staj_basvurusu sb 
                INNER JOIN ogrenci o ON sb.ogrenci_id = o.ogrenci_id 
                INNER JOIN kullanici k ON o.kullanici_id = k.kullanici_id 
                WHERE sb.basvuru_id = :id LIMIT 1
            ");
            $stmt->execute([':id' => $izin['basvuru_id']]);
            $student = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($student) {
                $student_kullanici_id = $student['kullanici_id'];
                $telefon = $student['telefon'];
                $adSoyad = $student['ad'] . ' ' . $student['soyad'];
                
                $bildirim = new Bildirim();
                $bildirim->gonder($student_kullanici_id, "İzin Talebiniz Reddedildi", $izin['baslangic_tarihi'] . " - " . $izin['bitis_tarihi'] . " tarihleri arasındaki izin talebiniz reddedilmiştir.");
                
                // SMS gönder
                if (!empty($telefon)) {
                    SmsService::sendLeaveRejected($telefon, $student_kullanici_id, $adSoyad, $izin['baslangic_tarihi'], $izin['bitis_tarihi']);
                }
            }
        }

        header("Location: index.php?url=admin/izinler");
        exit;
    }
}
