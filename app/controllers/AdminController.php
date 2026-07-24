<?php

class AdminController extends Controller
{
    private function adminCheck()
    {
        $this->auth();
        if ($_SESSION['kullanici']['rol'] != 1) {
            die("Bu sayfaya sadece yöneticiler erişebilir.");
        }
    }

    public function basvurular()
    {
        $this->adminCheck();

        require_once BASE_PATH . '/app/models/StajBasvurusu.php';

        $model = new StajBasvurusu();
        $basvurular = $model->tumBasvurular();

        $this->view('admin/basvurular', [
            "basvurular" => $basvurular
        ]);
    }

    public function basvuruDurum()
    {
        $this->adminCheck();

        if (!isset($_GET['id']) || !isset($_GET['durum'])) {
            die("Eksik parametre.");
        }

        require_once BASE_PATH . '/app/models/StajBasvurusu.php';

        $model = new StajBasvurusu();
        $model->durumGuncelle($_GET['id'], $_GET['durum']);

        // Log yaz
        require_once BASE_PATH . '/app/models/SistemLog.php';
        $log = new SistemLog();
        $log->logYaz($_SESSION['kullanici']['id'] ?? null, "Başvuru Durumu Güncellendi: " . $_GET['durum'], "staj_basvurusu", $_GET['id']);

        // Bildirim gönder
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
        $stmt->execute([':id' => $_GET['id']]);
        $student = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($student) {
            $student_kullanici_id = $student['kullanici_id'];
            $telefon = $student['telefon'];
            $adSoyad = $student['ad'] . ' ' . $student['soyad'];

            $bildirim = new Bildirim();
            $durum_mesaj = $_GET['durum'] == 'Onaylandı' ? 'onaylandı' : 'reddedildi';
            $bildirim->gonder($student_kullanici_id, "Staj Başvurusu " . ucfirst($durum_mesaj), "Staj başvurunuz yönetici tarafından " . $durum_mesaj . ".");

            // SMS gönder
            if (!empty($telefon)) {
                if ($_GET['durum'] == 'Onaylandı') {
                    SmsService::sendApplicationApproved($telefon, $student_kullanici_id, $adSoyad);
                } else {
                    SmsService::sendApplicationRejected($telefon, $student_kullanici_id, $adSoyad);
                }
            }
        }

        header("Location: index.php?url=admin/basvurular");
        exit;
    }

    public function loglar()
    {
        $this->adminCheck();

        require_once BASE_PATH . '/app/models/SistemLog.php';
        $logModel = new SistemLog();
        $loglar = $logModel->tumLoglar();

        $this->view('admin/loglar', [
            'loglar' => $loglar
        ]);
    }

    public function dashboard()
    {
        $this->adminCheck();

        require_once BASE_PATH . '/app/models/StajBasvurusu.php';
        $basvuruModel = new StajBasvurusu();
        $basvurular = $basvuruModel->tumBasvurular();

        $bekleyen = count(array_filter($basvurular, fn($b) => $b['durum'] == 'Beklemede'));
        $onayli = count(array_filter($basvurular, fn($b) => $b['durum'] == 'Onaylandı'));

        $this->view('admin/dashboard', [
            'kullanici' => $_SESSION['kullanici'],
            'toplam_basvuru' => count($basvurular),
            'bekleyen' => $bekleyen,
            'onayli' => $onayli
        ]);
    }

    // Geliştirme: Admin panelinde sorumlu atama formunu yükleyen aksiyon.
    public function sorumluAta()
    {
        $this->adminCheck();

        $basvuru_id = $_GET['id'] ?? null;
        if (!$basvuru_id) {
            die("Eksik parametre: Başvuru ID.");
        }

        require_once BASE_PATH . '/app/models/StajBasvurusu.php';
        require_once BASE_PATH . '/app/models/Sorumlu.php';

        $basvuruModel = new StajBasvurusu();
        
        // Veritabanından başvuru bilgilerini çekelim
        $dbClass = new Database();
        $db = $dbClass->connect();
        $stmt = $db->prepare("SELECT * FROM staj_basvurusu WHERE basvuru_id = :id LIMIT 1");
        $stmt->execute([':id' => $basvuru_id]);
        $basvuru = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$basvuru) {
            die("Başvuru bulunamadı.");
        }

        // Kural: Başvurusu onaylanmamış öğrenciye sorumlu atanamasın.
        if ($basvuru['durum'] !== 'Onaylandı') {
            die("Hata: Başvurusu onaylanmamış öğrenciye sorumlu atanamaz.");
        }

        $sorumluModel = new Sorumlu();
        // Kural: Aynı öğrenciye ikinci kez sorumlu atanamasın (mükerrer atama kontrolü).
        if ($sorumluModel->atamaVarMi($basvuru_id)) {
            die("Hata: Bu öğrenciye zaten bir sorumlu atanmıştır.");
        }

        // Sorumluları listele
        $sorumlular = $sorumluModel->tumSorumlulariGetir();

        // Öğrenci kullanıcı bilgilerini al
        $stmtStudent = $db->prepare("
            SELECT o.ogrenci_no, k.ad, k.soyad 
            FROM ogrenci o 
            INNER JOIN kullanici k ON o.kullanici_id = k.kullanici_id 
            WHERE o.ogrenci_id = :ogrenci_id LIMIT 1
        ");
        $stmtStudent->execute([':ogrenci_id' => $basvuru['ogrenci_id']]);
        $student = $stmtStudent->fetch(PDO::FETCH_ASSOC);

        $this->view('admin/sorumlu_ata', [
            'basvuru_id' => $basvuru_id,
            'student' => $student,
            'sorumlular' => $sorumlular
        ]);
    }

    // Geliştirme: Admin panelinde sorumlu atamasını kaydeden aksiyon.
    public function sorumluAtaKaydet()
    {
        $this->adminCheck();

        $basvuru_id = $_POST['basvuru_id'] ?? null;
        $sorumlu_id = $_POST['sorumlu_id'] ?? null;
        $gorev = $_POST['gorev'] ?? '';

        if (!$basvuru_id || !$sorumlu_id) {
            die("Hata: Gerekli alanları doldurunuz.");
        }

        require_once BASE_PATH . '/app/models/StajBasvurusu.php';
        require_once BASE_PATH . '/app/models/Sorumlu.php';

        $dbClass = new Database();
        $db = $dbClass->connect();
        
        $stmt = $db->prepare("SELECT * FROM staj_basvurusu WHERE basvuru_id = :id LIMIT 1");
        $stmt->execute([':id' => $basvuru_id]);
        $basvuru = $stmt->fetch(PDO::FETCH_ASSOC);

        // Kural: Başvurusu onaylanmamış öğrenciye sorumlu atanamasın.
        if (!$basvuru || $basvuru['durum'] !== 'Onaylandı') {
            die("Hata: Başvurusu onaylanmamış öğrenciye sorumlu atanamaz.");
        }

        $sorumluModel = new Sorumlu();
        // Kural: Aynı öğrenciye ikinci kez sorumlu atanamasın (mükerrer atama kontrolü).
        if ($sorumluModel->atamaVarMi($basvuru_id)) {
            die("Hata: Bu öğrenciye zaten bir sorumlu atanmıştır.");
        }

        // Atamayı kaydet (atama tarihi bu model metodunda NOW() olarak kaydedilir)
        if ($sorumluModel->sorumluAta($basvuru_id, $sorumlu_id, $gorev)) {
            // Staj durumunu güncelle: Staj Devam Ediyor
            $basvuruModel = new StajBasvurusu();
            $basvuruModel->stajDurumuGuncelle($basvuru_id, 'Staj Devam Ediyor');

            // Log yaz
            require_once BASE_PATH . '/app/models/SistemLog.php';
            $log = new SistemLog();
            $log->logYaz($_SESSION['kullanici']['id'], "Öğrenciye Sorumlu Atandı (Basvuru ID: $basvuru_id, Sorumlu ID: $sorumlu_id)", "sorumlu_atama", $basvuru_id);

            // Bildirim gönder
            require_once BASE_PATH . '/app/models/Bildirim.php';
            $stmtStudent = $db->prepare("SELECT o.kullanici_id FROM ogrenci o WHERE o.ogrenci_id = :ogrenci_id LIMIT 1");
            $stmtStudent->execute([':ogrenci_id' => $basvuru['ogrenci_id']]);
            $student_kullanici_id = $stmtStudent->fetchColumn();

            if ($student_kullanici_id) {
                $bildirim = new Bildirim();
                $stmtSorumluName = $db->prepare("SELECT k.ad, k.soyad, s.unvan FROM sorumlu s INNER JOIN kullanici k ON s.kullanici_id = k.kullanici_id WHERE s.sorumlu_id = :sorumlu_id LIMIT 1");
                $stmtSorumluName->execute([':sorumlu_id' => $sorumlu_id]);
                $sorumluInfo = $stmtSorumluName->fetch(PDO::FETCH_ASSOC);
                $sorumluAdSoyad = ($sorumluInfo['unvan'] ?? '') . ' ' . ($sorumluInfo['ad'] ?? '') . ' ' . ($sorumluInfo['soyad'] ?? '');

                $bildirim->gonder($student_kullanici_id, "Sorumlu Atandı", "Stajınız için staj sorumlusu olarak " . $sorumluAdSoyad . " atanmıştır.");
            }

            // Kural: Atama başarılı olunca bilgilendirme mesajı gösterilsin.
            $_SESSION['flash_message'] = "Staj sorumlusu başarıyla atanmıştır.";
        } else {
            $_SESSION['flash_message'] = "Staj sorumlusu atanırken bir hata oluştu.";
        }

        header("Location: index.php?url=admin/basvurular");
        exit;
    }
}