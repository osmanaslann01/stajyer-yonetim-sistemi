<?php

class BelgeController extends Controller
{
    private function adminCheck()
    {
        $this->auth();
        if ($_SESSION['kullanici']['rol'] != 1) {
            die("Bu sayfaya sadece yöneticiler erişebilir.");
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Öğrenci Belge Ekranı
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $this->onayliStajBasvurusuZorunlu();

        require_once BASE_PATH . '/app/models/StajBasvurusu.php';
        require_once BASE_PATH . '/app/models/Belge.php';

        $basvuruModel = new StajBasvurusu();
        $belgeModel = new Belge();

        $basvuru = $basvuruModel->onayliBasvuru($_SESSION['kullanici']['ogrenci_id']);
        $belgeler = [];

        if ($basvuru) {
            $belgeler = $belgeModel->listele($basvuru['basvuru_id']);
        }

        $this->view('ogrenci/belgeler', [
            'basvuru'  => $basvuru,
            'belgeler' => $belgeler
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Öğrenci Belge Yükleme
    |--------------------------------------------------------------------------
    */
    public function upload()
    {
        $basvuru = $this->onayliStajBasvurusuZorunlu();

        if ((int) ($_POST['basvuru_id'] ?? 0) !== (int) $basvuru['basvuru_id']) {
            die('Yetkisiz basvuru islemi.');
        }

        require_once BASE_PATH . '/app/models/Belge.php';
        $model = new Belge();

        if ($model->belgeVarMi($_POST['basvuru_id'], $_POST['belge_turu'], "Ogrenci")) {
            echo "Bu belgeyi daha önce yüklediniz.";
            exit;
        }

        if (!isset($_FILES['belge']) || $_FILES['belge']['error'] !== UPLOAD_ERR_OK) {
            echo "Dosya seçilmedi veya yükleme hatası oluştu.";
            exit;
        }

        $dosya = $_FILES['belge'];
        $uzanti = strtolower(pathinfo($dosya['name'], PATHINFO_EXTENSION));
        $izinli = ['pdf', 'jpg', 'png'];

        if (!in_array($uzanti, $izinli)) {
            echo "Geçersiz dosya türü. Sadece PDF, JPG ve PNG yüklenebilir.";
            exit;
        }

        $yeniAd = uniqid() . "." . $uzanti;
        $hedef = BASE_PATH . "/uploads/belgeler/" . $yeniAd;

        if (move_uploaded_file($dosya['tmp_name'], $hedef)) {
            $model->kaydet([
                "basvuru_id"   => $_POST['basvuru_id'],
                "belge_turu"   => $_POST['belge_turu'],
                "dosya_adi"     => $dosya['name'],
                "dosya_yolu"    => 'uploads/belgeler/' . $yeniAd,
                "mime_type"    => $dosya['type'],
                "dosya_boyutu"  => $dosya['size'],
                "yukleyen_tur" => "Ogrenci",
                "imza_durumu"  => "Imzasiz"
            ]);

            echo "Belge yükleme başarılı.";
        } else {
            echo "Dosya yüklenirken sunucu hatası oluştu.";
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Admin / Bilgi İşlem Belge Yükleme
    |--------------------------------------------------------------------------
    */
    public function adminUpload()
    {
        $this->adminCheck();

        if (!isset($_FILES['belge']) || $_FILES['belge']['error'] !== UPLOAD_ERR_OK) {
            echo "Lütfen geçerli bir belge yükleyin.";
            exit;
        }

        require_once BASE_PATH . '/app/models/Belge.php';
        $model = new Belge();

        $dosya = $_FILES['belge'];
        $uzanti = strtolower(pathinfo($dosya['name'], PATHINFO_EXTENSION));
        $izinli = ['pdf', 'jpg', 'png'];

        if (!in_array($uzanti, $izinli)) {
            echo "Geçersiz dosya türü.";
            exit;
        }

        $yeniAd = uniqid() . "." . $uzanti;
        $hedef = BASE_PATH . "/uploads/belgeler/" . $yeniAd;

        if (move_uploaded_file($dosya['tmp_name'], $hedef)) {
            $model->kaydet([
                "basvuru_id"   => $_POST['basvuru_id'],
                "belge_turu"   => $_POST['belge_turu'],
                "dosya_adi"     => $dosya['name'],
                "dosya_yolu"    => 'uploads/belgeler/' . $yeniAd,
                "mime_type"    => $dosya['type'],
                "dosya_boyutu"  => $dosya['size'],
                "yukleyen_tur" => "BilgiIslem",
                "imza_durumu"  => "Imzali",
                "onay_durumu"  => "Onaylandi"
            ]);

            // Log yaz
            require_once BASE_PATH . '/app/models/SistemLog.php';
            $log = new SistemLog();
            $log->logYaz($_SESSION['kullanici']['id'] ?? null, "İmzalı Belge Yüklendi", "belge", $_POST['basvuru_id']);

            // Bildirim gönder
            require_once BASE_PATH . '/app/models/Bildirim.php';
            $dbClass = new Database();
            $db = $dbClass->connect();
            $stmtKullanici = $db->prepare("SELECT o.kullanici_id FROM staj_basvurusu sb INNER JOIN ogrenci o ON sb.ogrenci_id = o.ogrenci_id WHERE sb.basvuru_id = :id LIMIT 1");
            $stmtKullanici->execute([':id' => $_POST['basvuru_id']]);
            $student_kullanici_id = $stmtKullanici->fetchColumn();

            if ($student_kullanici_id) {
                $bildirim = new Bildirim();
                $bildirim->gonder($student_kullanici_id, "İmzalı Belge Yüklendi", "Yönetici stajınız için imzalı belge yükledi: " . $_POST['belge_turu']);
            }

            echo "İmzalı belge yüklendi.";
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Admin Belge Listesi
    |--------------------------------------------------------------------------
    */
    public function adminIndex()
    {
        $this->adminCheck();

        require_once BASE_PATH . '/app/models/Belge.php';
        $model = new Belge();

        $basvuru_id = $_GET['basvuru_id'] ?? null;

        if ($basvuru_id) {
            $belgeler = $model->basvuruBelgeleriDetayli($basvuru_id);
        } else {
            $belgeler = $model->tumBelgeler();
        }

        $this->view('admin/belgeler', [
            'belgeler'   => $belgeler,
            'basvuru_id' => $basvuru_id
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Admin Belge Onay / Red
    |--------------------------------------------------------------------------
    */
    public function durumGuncelle()
    {
        $this->adminCheck();

        if (!isset($_GET['id']) || !isset($_GET['durum'])) {
            die("Eksik parametre.");
        }

        require_once BASE_PATH . '/app/models/Belge.php';

        $durum = $_GET['durum'];
        if ($durum == "onay") {
            $durum = "Onaylandi";
        } elseif ($durum == "red") {
            $durum = "Reddedildi";
        }

        $model = new Belge();
        $belge_id = $_GET['id'];
        $model->belgeDurumGuncelle($belge_id, $durum);

        // Bildirim Gönder ve Log Yaz
        require_once BASE_PATH . '/app/models/SistemLog.php';
        require_once BASE_PATH . '/app/models/Bildirim.php';
        require_once BASE_PATH . '/app/services/SmsService.php';

        $log = new SistemLog();
        $log->logYaz($_SESSION['kullanici']['id'] ?? null, "Belge Onay Durumu Güncellendi: " . $durum, "belge", $belge_id);

        $dbClass = new Database();
        $db = $dbClass->connect();
        $stmtBelge = $db->prepare("
            SELECT o.kullanici_id, b.belge_turu, k.telefon, k.ad, k.soyad 
            FROM belge b 
            INNER JOIN staj_basvurusu sb ON b.basvuru_id = sb.basvuru_id 
            INNER JOIN ogrenci o ON sb.ogrenci_id = o.ogrenci_id 
            INNER JOIN kullanici k ON o.kullanici_id = k.kullanici_id 
            WHERE b.belge_id = :id LIMIT 1
        ");
        $stmtBelge->execute([':id' => $belge_id]);
        $res = $stmtBelge->fetch(PDO::FETCH_ASSOC);

        if ($res) {
            $student_kullanici_id = $res['kullanici_id'];
            $telefon = $res['telefon'];
            $adSoyad = $res['ad'] . ' ' . $res['soyad'];
            $belgeTuru = $res['belge_turu'];

            $bildirim = new Bildirim();
            $status_tr = $durum == 'Onaylandi' ? 'onaylandı' : 'reddedildi';
            $bildirim->gonder($student_kullanici_id, "Belge " . ucfirst($status_tr), "Yüklediğiniz '" . $belgeTuru . "' belgesi yönetici tarafından " . $status_tr . ".");

            // SMS gönder
            if (!empty($telefon)) {
                if ($durum == 'Onaylandi') {
                    SmsService::sendDocumentApproved($telefon, $student_kullanici_id, $adSoyad, $belgeTuru);
                } else {
                    SmsService::sendDocumentRejected($telefon, $student_kullanici_id, $adSoyad, $belgeTuru);
                }
            }
        }

        header("Location: index.php?url=admin/belgeler");
        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | Güvenli Dosya İndirme
    |--------------------------------------------------------------------------
    */
    public function download()
    {
        $this->auth();

        require_once BASE_PATH . '/app/models/Belge.php';
        $model = new Belge();

        $belge_id = $_GET['id'] ?? null;
        if (!$belge_id) {
            die("Belge ID belirtilmedi.");
        }

        $belge = $model->belgeBul($belge_id);
        if (!$belge) {
            die("Belge bulunamadı.");
        }

        // Yetki Kontrolü
        $user = $_SESSION['kullanici'];
        if ($user['rol'] == 2) { // Öğrenci
            $dbClass = new Database();
            $db = $dbClass->connect();
            $stmt = $db->prepare("SELECT COUNT(*) FROM staj_basvurusu WHERE basvuru_id = :basvuru_id AND ogrenci_id = :ogrenci_id");
            $stmt->execute([
                ':basvuru_id' => $belge['basvuru_id'],
                ':ogrenci_id' => $user['ogrenci_id']
            ]);
            if ($stmt->fetchColumn() == 0) {
                die("Bu belgeye erişim yetkiniz bulunmamaktadır.");
            }
        } elseif ($user['rol'] == 3) { // Sorumlu
            $dbClass = new Database();
            $db = $dbClass->connect();
            $stmt = $db->prepare("SELECT COUNT(*) FROM sorumlu_atama sa INNER JOIN sorumlu s ON sa.sorumlu_id = s.sorumlu_id WHERE sa.basvuru_id = :basvuru_id AND s.kullanici_id = :kullanici_id AND sa.aktif = 1");
            $stmt->execute([
                ':basvuru_id' => $belge['basvuru_id'],
                ':kullanici_id' => $user['id']
            ]);
            if ($stmt->fetchColumn() == 0) {
                die("Bu öğrencinin belgesine erişim yetkiniz bulunmamaktadır.");
            }
        }

        // Dosyayı sun
        $filepath = BASE_PATH . '/' . $belge['dosya_yolu'];
        if (file_exists($filepath)) {
            require_once BASE_PATH . '/app/models/SistemLog.php';
            $log = new SistemLog();
            $log->logYaz($user['id'], "Belge İndirdi", "belge", $belge_id);

            header('Content-Description: File Transfer');
            header('Content-Type: ' . ($belge['mime_type'] ?? 'application/octet-stream'));
            header('Content-Disposition: attachment; filename="' . basename($belge['dosya_adi']) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filepath));
            readfile($filepath);
            exit;
        } else {
            die("Dosya fiziksel sunucuda bulunamadı.");
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Belge Geçmişi
    |--------------------------------------------------------------------------
    */
    public function belgeGecmisi()
    {
        $this->auth();

        require_once BASE_PATH . '/app/models/Belge.php';
        $model = new Belge();

        $basvuru_id = $_GET['basvuru_id'] ?? null;
        $belge_turu = $_GET['belge_turu'] ?? null;

        if (!$basvuru_id || !$belge_turu) {
            die("Eksik parametre.");
        }

        $user = $_SESSION['kullanici'];
        if ($user['rol'] == 2) {
            $dbClass = new Database();
            $db = $dbClass->connect();
            $stmt = $db->prepare("SELECT COUNT(*) FROM staj_basvurusu WHERE basvuru_id = :basvuru_id AND ogrenci_id = :ogrenci_id");
            $stmt->execute([
                ':basvuru_id' => $basvuru_id,
                ':ogrenci_id' => $user['ogrenci_id']
            ]);
            if ($stmt->fetchColumn() == 0) {
                die("Yetkisiz erişim.");
            }
        }

        $gecmis = $model->belgeGecmisi($basvuru_id, $belge_turu);

        $this->view('admin/belge_gecmisi', [
            'gecmis'     => $gecmis,
            'belge_turu' => $belge_turu
        ]);
    }
}
