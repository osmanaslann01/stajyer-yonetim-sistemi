<?php

class AuthController extends Controller
{

    // Kayıt formu
    public function register()
    {
        $this->view('auth/register');
    }


    // Kayıt işlemi
    public function registerPost()
    {
        require_once BASE_PATH . '/app/models/Kullanici.php';
        require_once BASE_PATH . '/app/models/Ogrenci.php';

        $kullaniciModel = new Kullanici();

        $kullanici_id = $kullaniciModel->kaydet([
            "rol_id" => 2,
            "ad" => $_POST["ad"],
            "soyad" => $_POST["soyad"],
            "email" => $_POST["email"],
            "telefon" => $_POST["telefon"],
            "sifre_hash" => password_hash($_POST["sifre"], PASSWORD_DEFAULT)
        ]);

        if ($kullanici_id) {

            $ogrenciModel = new Ogrenci();

            $ogrenciModel->kaydet([
                "kullanici_id" => $kullanici_id,
                "ogrenci_no" => $_POST["ogrenci_no"],
                "tc_no" => $_POST["tc_no"],
                "fakulte" => $_POST["fakulte"],
                "bolum" => $_POST["bolum"],
                "sinif" => $_POST["sinif"],
                "staj_turu" => $_POST["staj_turu"],
                "adres" => $_POST["adres"]
            ]);

            echo "Öğrenci kaydı başarılı";

        } else {

            echo "Kayıt başarısız";

        }
    }


    // Giriş formu
    public function login()
    {
        $this->view('auth/login');
    }


    // Giriş işlemi
    public function loginPost()
    {
        require_once BASE_PATH . '/app/models/Kullanici.php';

        $kullanici = new Kullanici();

        $user = $kullanici->girisYap(
            $_POST["email"],
            $_POST["sifre"]
        );

        if ($user) {


    $_SESSION["kullanici"] = [

        "id" => $user["kullanici_id"],

        "rol" => $user["rol_id"],

        "ad" => $user["ad"],

        "soyad" => $user["soyad"],

        "ogrenci_id" => $user["ogrenci_id"]

    ];

    require_once BASE_PATH . '/app/models/SistemLog.php';
    $log = new SistemLog();
    $log->logYaz($user["kullanici_id"], "Giriş Yaptı");

    if ($user["rol_id"] == 1) {
        header("Location: index.php?url=admin/dashboard");
    } elseif ($user["rol_id"] == 3) {
        header("Location: index.php?url=sorumlu/dashboard");
    } else {
        require_once BASE_PATH . '/app/models/StajBasvurusu.php';

        $basvuruModel = new StajBasvurusu();
        $sonBasvuru = $basvuruModel->sonBasvuru($user['ogrenci_id']);

        if (!$sonBasvuru) {
            header("Location: index.php?url=ogrenci/basvuru");
        } elseif ($basvuruModel->onayliBasvuru($user['ogrenci_id'])) {
            header("Location: index.php?url=ogrenci/dashboard");
        } else {
            header("Location: index.php?url=ogrenci/basvuruDurumu");
        }
    }

    exit;


}else {

            echo "Email veya şifre hatalı.";

        }
    }


    // Çıkış
    public function logout()
    {
        if (isset($_SESSION['kullanici']['id'])) {
            require_once BASE_PATH . '/app/models/SistemLog.php';
            $log = new SistemLog();
            $log->logYaz($_SESSION['kullanici']['id'], "Çıkış Yaptı");
        }
        session_destroy();

        header("Location: index.php?url=login");
        exit;
    }

}
