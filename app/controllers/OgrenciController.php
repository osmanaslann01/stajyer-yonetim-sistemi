<?php


class OgrenciController extends Controller
{


    public function dashboard()
    {
        $this->onayliStajBasvurusuZorunlu();

        $this->auth();


        $kullanici = Auth::user();


        $this->view(
            'ogrenci/dashboard',
            [
                'kullanici'=>$kullanici
            ]
        );

    }

    public function profil()
    {
    $this->onayliStajBasvurusuZorunlu();
	$this->auth();


        require_once BASE_PATH .
        '/app/models/Ogrenci.php';



        $model = new Ogrenci();



        $profil = $model->profilGetir(
            Auth::user()['id']
        );



        $this->view(
            'ogrenci/profil',
            [
                'profil'=>$profil
            ]
        );
	}
public function basvurularim()
{

    require_once BASE_PATH .
    '/app/models/StajBasvurusu.php';


    $model = new StajBasvurusu();


    $basvurular = $model->ogrenciBasvurulari(
        $_SESSION['kullanici']['ogrenci_id']
    );


    $this->view(
        'ogrenci/basvurularim',
        [
            'basvurular'=>$basvurular
        ]
    );

}
public function sonuc()
{
    $this->onayliStajBasvurusuZorunlu();

    require_once BASE_PATH .
    '/app/models/StajBasvurusu.php';


    $model = new StajBasvurusu();


    require_once BASE_PATH .
    '/app/models/StajBasvurusu.php';


    $model = new StajBasvurusu();


    $basvuru = $model->ogrenciSonuc(
        $_SESSION['kullanici']['ogrenci_id']
    );


    $this->view(
        'ogrenci/sonuc',
        [
            "basvuru"=>$basvuru
        ]
    );

}

    // Geliştirme: Öğrenci profil bilgilerini güncelleyen POST metodu.
    public function profilKaydet()
    {
        $this->onayliStajBasvurusuZorunlu();
        $this->auth();
        if ($_SESSION['kullanici']['rol'] != 2) {
            die("Yetkisiz işlem.");
        }

        $email = $_POST['email'] ?? null;
        $telefon = $_POST['telefon'] ?? null;
        $sifre = $_POST['sifre'] ?? null;

        if (!$email) {
            die("E-posta adresi boş bırakılamaz.");
        }

        $updateData = [
            'email' => $email,
            'telefon' => $telefon
        ];

        // Şifre boş değilse şifre_hash alanını güncelle
        if (!empty($sifre)) {
            $updateData['sifre_hash'] = password_hash($sifre, PASSWORD_DEFAULT);
        }

        // Profil Fotoğrafı yükleme işlemi
        if (isset($_FILES['profil_foto']) && $_FILES['profil_foto']['error'] === UPLOAD_ERR_OK) {
            $dosya = $_FILES['profil_foto'];
            $uzanti = strtolower(pathinfo($dosya['name'], PATHINFO_EXTENSION));
            $izinli = ['jpg', 'jpeg', 'png', 'gif'];
            
            if (!in_array($uzanti, $izinli)) {
                die("Geçersiz profil fotoğrafı türü. Sadece JPG, JPEG, PNG, GIF yüklenebilir.");
            }
            
            if ($dosya['size'] > 5 * 1024 * 1024) { // Maks 5MB
                die("Profil fotoğrafı en fazla 5MB olabilir.");
            }
            
            $hedef_dir = BASE_PATH . "/uploads/profil/";
            if (!file_exists($hedef_dir)) {
                mkdir($hedef_dir, 0777, true);
            }
            
            $yeniAd = uniqid('profil_', true) . "." . $uzanti;
            $hedef = $hedef_dir . $yeniAd;
            
            if (move_uploaded_file($dosya['tmp_name'], $hedef)) {
                $updateData['profil_fotografi'] = 'uploads/profil/' . $yeniAd;
            } else {
                die("Fotoğraf yüklenirken bir hata oluştu.");
            }
        }

        require_once BASE_PATH . '/app/models/Kullanici.php';
        $kullaniciModel = new Kullanici();
        
        // E-posta adresi kullanımda mı kontrolü (başka bir kullanıcının e-postasıyla çakışmasın)
        $existingUser = $kullaniciModel->emailBul($email);
        if ($existingUser && $existingUser['kullanici_id'] != $_SESSION['kullanici']['id']) {
            die("Bu e-posta adresi başka bir kullanıcı tarafından kullanılmaktadır.");
        }

        $kullaniciModel->profilGuncelle($_SESSION['kullanici']['id'], $updateData);

        // Log yaz
        require_once BASE_PATH . '/app/models/SistemLog.php';
        $log = new SistemLog();
        $log->logYaz($_SESSION['kullanici']['id'], "Öğrenci Profil Bilgilerini Güncelledi");

        // Başarılı mesajı
        $_SESSION['flash_message'] = "Profil bilgileriniz başarıyla güncellendi.";

        header("Location: index.php?url=ogrenci/profil");
        exit;
    }

}
