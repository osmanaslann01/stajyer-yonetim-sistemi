<?php

class YoklamaController extends Controller
{
    public function index()
    {
        $this->onayliStajBasvurusuZorunlu();
        $this->auth();
        if ($_SESSION['kullanici']['rol'] != 2) {
            die("Bu sayfaya sadece öğrenciler erişebilir.");
        }

        require_once BASE_PATH . '/app/models/StajBasvurusu.php';
        require_once BASE_PATH . '/app/models/Yoklama.php';
        require_once BASE_PATH . '/app/models/Devamsizlik.php';

        $basvuruModel = new StajBasvurusu();
        $basvuru = $basvuruModel->onayliBasvuru($_SESSION['kullanici']['ogrenci_id']);

        $bugunku_kayitlar = [];
        $yoklama_gecmisi = [];
        $devamsizliklar = [];

        if ($basvuru) {
            $yoklamaModel = new Yoklama();
            $bugunku_kayitlar = $yoklamaModel->bugunkuYoklamaKayıtları($basvuru['basvuru_id']);
            $yoklama_gecmisi = $yoklamaModel->basvuruYoklamaGecmisi($basvuru['basvuru_id']);

            $devModel = new Devamsizlik();
            $devamsizliklar = $devModel->basvuruDevamsizlikListesi($basvuru['basvuru_id']);
        }

        $this->view('ogrenci/yoklama', [
            'basvuru' => $basvuru,
            'bugunku_kayitlar' => $bugunku_kayitlar,
            'yoklama_gecmisi' => $yoklama_gecmisi,
            'devamsizliklar' => $devamsizliklar,
            'ip_adresi' => $_SERVER['REMOTE_ADDR']
        ]);
    }

    public function store()
    {
        $this->onayliStajBasvurusuZorunlu();
        $this->auth();
        if ($_SESSION['kullanici']['rol'] != 2) {
            die("Yetkisiz işlem.");
        }

        require_once BASE_PATH . '/app/models/Yoklama.php';
        $yoklamaModel = new Yoklama();

        // IP Kontrolü
        $client_ip = $_SERVER['REMOTE_ADDR'];
        if (!$yoklamaModel->yetkiliIpKontrol($client_ip)) {
            die("Bulunduğunuz IP adresinden ($client_ip) yoklama girişi yapılmasına izin verilmiyor.");
        }

        require_once BASE_PATH . '/app/models/StajBasvurusu.php';
        $basvuruModel = new StajBasvurusu();
        $basvuru = $basvuruModel->onayliBasvuru($_SESSION['kullanici']['ogrenci_id']);

        if (!$basvuru) {
            die("Onaylı bir staj başvurunuz bulunmamaktadır.");
        }

        $bugunku_kayitlar = $yoklamaModel->bugunkuYoklamaKayıtları($basvuru['basvuru_id']);

        $islem_tipi = 'Giris';
        if (count($bugunku_kayitlar) == 0) {
            $islem_tipi = 'Giris';
        } elseif (count($bugunku_kayitlar) == 1 && $bugunku_kayitlar[0]['islem_tipi'] == 'Giris') {
            $islem_tipi = 'Cikis';
        } else {
            die("Bugün için yoklama giriş ve çıkış işlemleriniz zaten tamamlanmıştır.");
        }

        $yoklamaModel->kaydet([
            'basvuru_id' => $basvuru['basvuru_id'],
            'islem_tipi' => $islem_tipi,
            'oturum_tipi' => 'Normal',
            'ip_adresi' => $client_ip,
            'cihaz_bilgisi' => $_SERVER['HTTP_USER_AGENT']
        ]);

        // Log yaz
        require_once BASE_PATH . '/app/models/SistemLog.php';
        $log = new SistemLog();
        $log->logYaz($_SESSION['kullanici']['id'], "Yoklama Girişi Yapıldı ($islem_tipi)", "yoklama", $basvuru['basvuru_id']);

        header("Location: index.php?url=ogrenci/yoklama");
        exit;
    }

    public function adminIndex()
    {
        $this->auth();
        if ($_SESSION['kullanici']['rol'] != 1) {
            die("Bu sayfaya sadece yöneticiler erişebilir.");
        }

        require_once BASE_PATH . '/app/models/Devamsizlik.php';
        require_once BASE_PATH . '/app/models/StajBasvurusu.php';

        $devModel = new Devamsizlik();
        $devamsizliklar = $devModel->tumDevamsizliklar();

        $basvuruModel = new StajBasvurusu();
        $basvurular = $basvuruModel->tumBasvurular();
        // Sadece onaylı olanları filtrele
        $onayli_basvurular = array_filter($basvurular, function($b) {
            return $b['durum'] == 'Onaylandı';
        });

        // Tüm yoklama kayıtlarını da admin görsün
        $dbClass = new Database();
        $db = $dbClass->connect();
        $yoklamaQuery = "
            SELECT 
                y.*,
                o.ogrenci_no,
                k.ad,
                k.soyad
            FROM yoklama y
            INNER JOIN staj_basvurusu sb ON y.basvuru_id = sb.basvuru_id
            INNER JOIN ogrenci o ON sb.ogrenci_id = o.ogrenci_id
            INNER JOIN kullanici k ON o.kullanici_id = k.kullanici_id
            ORDER BY y.islem_zamani DESC
            LIMIT 500
        ";
        $yoklamalar = $db->query($yoklamaQuery)->fetchAll(PDO::FETCH_ASSOC);

        $this->view('admin/yoklama', [
            'devamsizliklar' => $devamsizliklar,
            'basvurular' => $onayli_basvurular,
            'yoklamalar' => $yoklamalar
        ]);
    }

    public function devamsizlikEkle()
    {
        $this->auth();
        $rol = $_SESSION['kullanici']['rol'];
        if ($rol != 1 && $rol != 3) {
            die("Yetkisiz işlem.");
        }

        $basvuru_id = $_POST['basvuru_id'] ?? null;
        $tarih = $_POST['tarih'] ?? null;
        $devamsizlik_turu = $_POST['devamsizlik_turu'] ?? null;
        $aciklama = $_POST['aciklama'] ?? '';

        if (!$basvuru_id || !$tarih || !$devamsizlik_turu) {
            die("Eksik parametre.");
        }

        require_once BASE_PATH . '/app/models/Devamsizlik.php';
        $devModel = new Devamsizlik();
        $devModel->kaydet([
            'basvuru_id' => $basvuru_id,
            'tarih' => $tarih,
            'devamsizlik_turu' => $devamsizlik_turu,
            'aciklama' => $aciklama
        ]);

        // Log yaz
        require_once BASE_PATH . '/app/models/SistemLog.php';
        $log = new SistemLog();
        $log->logYaz($_SESSION['kullanici']['id'], "Devamsızlık Kaydı Eklendi: " . $devamsizlik_turu . " (Tarih: " . $tarih . ")", "devamsizlik", $basvuru_id);

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
        $stmt->execute([':id' => $basvuru_id]);
        $student = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($student) {
            $student_kullanici_id = $student['kullanici_id'];
            $telefon = $student['telefon'];
            $adSoyad = $student['ad'] . ' ' . $student['soyad'];
            
            $bildirim = new Bildirim();
            $tur_tr = $devamsizlik_turu == 'Izinsiz' ? 'İzinsiz Devamsızlık' : ($devamsizlik_turu == 'Izinli' ? 'İzinli' : 'Raporlu');
            $bildirim->gonder($student_kullanici_id, "Devamsızlık/İzin Kaydı Eklendi", "$tarih tarihi için staj yoklama kaydınıza '$tur_tr' durumu işlendi. Açıklama: $aciklama");
            
            // SMS gönder (Sadece İzinsiz ise)
            if ($devamsizlik_turu == 'Izinsiz' && !empty($telefon)) {
                SmsService::sendAttendanceWarning($telefon, $student_kullanici_id, $adSoyad, $tarih, $aciklama);
            }
        }

        if ($rol == 1) {
            header("Location: index.php?url=admin/yoklama");
        } else {
            header("Location: index.php?url=sorumlu/yoklama");
        }
        exit;
    }
}
