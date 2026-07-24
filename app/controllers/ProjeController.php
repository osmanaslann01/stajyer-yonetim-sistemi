<?php

class ProjeController extends Controller
{
    public function hatirlat()
    {
        $this->auth();
        if ($_SESSION['kullanici']['rol'] != 1 && $_SESSION['kullanici']['rol'] != 3) {
            die("Yetkisiz işlem.");
        }

        $basvuru_id = $_GET['basvuru_id'] ?? null;
        $proje_adi = $_GET['proje_adi'] ?? 'Staj Projesi';
        $teslim_tarihi = $_GET['teslim_tarihi'] ?? date('Y-m-d');

        if (!$basvuru_id) {
            die("Eksik başvuru ID.");
        }

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
        $stmt->execute([':id' => $basvuru_id]);
        $student = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($student && !empty($student['telefon'])) {
            SmsService::sendProjectReminder($student['telefon'], $student['kullanici_id'], $student['ad'] . ' ' . $student['soyad'], $proje_adi, $teslim_tarihi);
            echo "Hatırlatma SMS'i başarıyla gönderildi.";
        } else {
            echo "Öğrenciye ait telefon numarası bulunamadı.";
        }
    }

    public function index()
    {
        $this->onayliStajBasvurusuZorunlu();
        $this->auth();
        if ($_SESSION['kullanici']['rol'] != 2) {
            die("Bu sayfaya sadece öğrenciler erişebilir.");
        }

        require_once BASE_PATH . '/app/models/StajBasvurusu.php';
        require_once BASE_PATH . '/app/models/Proje.php';
        require_once BASE_PATH . '/app/models/ProjeTeslim.php';

        $basvuruModel = new StajBasvurusu();
        $basvuru = $basvuruModel->onayliBasvuru($_SESSION['kullanici']['ogrenci_id']);

        $projeler = [];
        if ($basvuru) {
            $projeModel = new Proje();
            $raw_projeler = $projeModel->basvuruProjeleri($basvuru['basvuru_id']);

            $teslimModel = new ProjeTeslim();
            foreach ($raw_projeler as $p) {
                $p['teslimler'] = $teslimModel->projeTeslimleri($p['proje_id']);
                $projeler[] = $p;
            }
        }

        $this->view('ogrenci/proje', [
            'basvuru' => $basvuru,
            'projeler' => $projeler
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

        // Sorumlu atamasını kontrol edelim
        $dbClass = new Database();
        $db = $dbClass->connect();
        $stmtSorumlu = $db->prepare("SELECT sorumlu_id FROM sorumlu_atama WHERE basvuru_id = :basvuru_id AND aktif = 1 LIMIT 1");
        $stmtSorumlu->execute([':basvuru_id' => $basvuru['basvuru_id']]);
        $sorumlu_id = $stmtSorumlu->fetchColumn();

        if (!$sorumlu_id) {
            die("Proje teslim edebilmeniz için önce staj sorumlunuzun atanması gerekmektedir.");
        }

        $proje_adi = $_POST['proje_adi'] ?? null;
        $aciklama = $_POST['aciklama'] ?? '';

        if (!$proje_adi || trim($proje_adi) == '') {
            die("Proje adı boş olamaz.");
        }

        if (!isset($_FILES['proje_dosya']) || $_FILES['proje_dosya']['error'] != UPLOAD_ERR_OK) {
            die("Lütfen geçerli bir dosya yükleyin.");
        }

        $dosya = $_FILES['proje_dosya'];
        $uzanti = strtolower(pathinfo($dosya['name'], PATHINFO_EXTENSION));
        $izinli = ['pdf', 'zip', 'rar', 'doc', 'docx', 'jpg', 'png'];

        if (!in_array($uzanti, $izinli)) {
            die("Geçersiz dosya uzantısı. Sadece PDF, ZIP, RAR, DOC, DOCX, JPG, PNG yüklenebilir.");
        }

        if ($dosya['size'] > 20 * 1024 * 1024) { // Max 20MB
            die("Dosya boyutu en fazla 20MB olabilir.");
        }

        // Klasör kontrol
        $hedef_dir = BASE_PATH . "/uploads/projeler/";
        if (!file_exists($hedef_dir)) {
            mkdir($hedef_dir, 0777, true);
        }

        $yeniAd = uniqid() . "." . $uzanti;
        $hedef = $hedef_dir . $yeniAd;

        if (!move_uploaded_file($dosya['tmp_name'], $hedef)) {
            die("Dosya sunucuya kaydedilirken hata oluştu.");
        }

        // Proje tablosunda kayıt var mı?
        require_once BASE_PATH . '/app/models/Proje.php';
        $projeModel = new Proje();
        
        // Bu başvuruya ait mevcut bir proje var mı diye bakalım
        $stmtExist = $db->prepare("SELECT proje_id FROM proje WHERE basvuru_id = :basvuru_id LIMIT 1");
        $stmtExist->execute([':basvuru_id' => $basvuru['basvuru_id']]);
        $proje_id = $stmtExist->fetchColumn();

        if (!$proje_id) {
            // Yeni proje kaydı oluştur
            $proje_id = $projeModel->kaydet([
                'basvuru_id' => $basvuru['basvuru_id'],
                'olusturan_sorumlu_id' => $sorumlu_id,
                'proje_adi' => $proje_adi,
                'proje_aciklamasi' => $aciklama,
                'gereksinimler' => 'Öğrenci teslimi',
                'verilis_tarihi' => date('Y-m-d'),
                'teslim_tarihi' => date('Y-m-d'),
                'durum' => 'Teslim Edildi'
            ]);
        } else {
            // Mevcut projeyi güncelle
            $stmtUpdate = $db->prepare("UPDATE proje SET durum = 'Teslim Edildi', proje_adi = :proje_adi, proje_aciklamasi = :aciklama WHERE proje_id = :proje_id");
            $stmtUpdate->execute([
                ':proje_adi' => $proje_adi,
                ':aciklama' => $aciklama,
                ':proje_id' => $proje_id
            ]);
        }

        // ProjeTeslim tablosuna teslim kaydı ekle
        require_once BASE_PATH . '/app/models/ProjeTeslim.php';
        $teslimModel = new ProjeTeslim();
        $teslimModel->kaydet([
            'proje_id' => $proje_id,
            'dosya_adi' => $dosya['name'],
            'dosya_yolu' => 'uploads/projeler/' . $yeniAd,
            'aciklama' => $aciklama,
            'teslim_durumu' => 'Teslim Edildi'
        ]);

        // Log yaz
        require_once BASE_PATH . '/app/models/SistemLog.php';
        $log = new SistemLog();
        $log->logYaz($_SESSION['kullanici']['id'], "Staj Projesi Teslim Edildi", "proje", $proje_id);

        // Danışmana Bildirim Gönder
        require_once BASE_PATH . '/app/models/Bildirim.php';
        $stmtAdvisorUser = $db->prepare("SELECT kullanici_id FROM sorumlu WHERE sorumlu_id = :sorumlu_id LIMIT 1");
        $stmtAdvisorUser->execute([':sorumlu_id' => $sorumlu_id]);
        $advisor_kullanici_id = $stmtAdvisorUser->fetchColumn();

        if ($advisor_kullanici_id) {
            $bildirim = new Bildirim();
            $bildirim->gonder($advisor_kullanici_id, "Öğrenci Proje Teslimi Yaptı", $_SESSION['kullanici']['ad'] . " " . $_SESSION['kullanici']['soyad'] . " isimli öğrenciniz staj projesini teslim etti.");
        }

        header("Location: index.php?url=ogrenci/proje");
        exit;
    }
}
