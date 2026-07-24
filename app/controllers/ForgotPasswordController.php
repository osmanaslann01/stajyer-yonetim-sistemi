<?php

class ForgotPasswordController extends Controller
{
    public function request(): void
    {
        unset($_SESSION['password_reset_id']);
        $this->view('auth/forgot_password', ['mesaj' => $this->flashMesajAl()]);
    }

    public function requestPost(): void
    {
        require_once BASE_PATH . '/app/models/Kullanici.php';
        require_once BASE_PATH . '/app/models/PasswordReset.php';

        $email = trim($_POST['email'] ?? '');
        $kullanici = (new Kullanici())->emailBul($email);

        if (!$kullanici || !(int) $kullanici['aktif']) {
            $this->flashMesajYaz('E-posta adresi kayitli degil veya hesap aktif degil.');
            $this->yonlendir('sifremi-unuttum');
        }

        $passwordReset = new PasswordReset();
        $passwordReset->kullanicininAktifKodlariniGecersizKil((int) $kullanici['kullanici_id']);

        $kod = (string) random_int(100000, 999999);
        $resetId = $passwordReset->olustur((int) $kullanici['kullanici_id'], $kod);

        session_regenerate_id(true);
        $_SESSION['password_reset_id'] = $resetId;

        // SMTP eklendiginde kod burada e-posta ile gonderilecektir.
        $this->flashMesajYaz('Dogrulama kodu olusturuldu. E-posta gonderimi henuz aktif degil. Test kodunuz: ' . $kod);
        $this->yonlendir('sifre-dogrula');
    }

    public function verify(): void
    {
        if (!$this->aktifResetKaydiAl()) {
            $this->flashMesajYaz('Dogrulama kodunuz gecersiz veya suresi dolmus.');
            $this->yonlendir('sifremi-unuttum');
        }

        $this->view('auth/verify_reset_code', ['mesaj' => $this->flashMesajAl()]);
    }

    public function verifyPost(): void
    {
        require_once BASE_PATH . '/app/models/PasswordReset.php';

        $resetId = (int) ($_SESSION['password_reset_id'] ?? 0);
        $kod = trim($_POST['kod'] ?? '');

        if (!preg_match('/^\d{6}$/', $kod)) {
            $this->flashMesajYaz('Lutfen 6 haneli dogrulama kodunu girin.');
            $this->yonlendir('sifre-dogrula');
        }

        $sonuc = (new PasswordReset())->kodDogrula($resetId, $kod);

        if ($sonuc === 'dogrulandi') {
            session_regenerate_id(true);
            $this->yonlendir('yeni-sifre');
        }

        if ($sonuc === 'kilitli') {
            unset($_SESSION['password_reset_id']);
            $this->flashMesajYaz('Cok fazla hatali kod girdiniz. Yeni kod talep edin.');
            $this->yonlendir('sifremi-unuttum');
        }

        if ($sonuc === 'gecersiz') {
            unset($_SESSION['password_reset_id']);
            $this->flashMesajYaz('Kod gecersiz veya suresi dolmus. Yeni kod talep edin.');
            $this->yonlendir('sifremi-unuttum');
        }

        $this->flashMesajYaz('Dogrulama kodu hatali. En fazla 5 deneme yapabilirsiniz.');
        $this->yonlendir('sifre-dogrula');
    }

    public function reset(): void
    {
        $reset = $this->aktifResetKaydiAl();

        if (!$reset || !$reset['dogrulandi_at']) {
            $this->flashMesajYaz('Yeni sifre belirlemek icin once kodu dogrulayin.');
            $this->yonlendir('sifremi-unuttum');
        }

        $this->view('auth/reset_password', ['mesaj' => $this->flashMesajAl()]);
    }

    public function resetPost(): void
    {
        require_once BASE_PATH . '/app/models/PasswordReset.php';

        $sifre = $_POST['sifre'] ?? '';
        $sifreTekrar = $_POST['sifre_tekrar'] ?? '';

        if (strlen($sifre) < 8) {
            $this->flashMesajYaz('Sifreniz en az 8 karakter olmalidir.');
            $this->yonlendir('yeni-sifre');
        }

        if (!hash_equals($sifre, $sifreTekrar)) {
            $this->flashMesajYaz('Sifreler birbiriyle eslesmiyor.');
            $this->yonlendir('yeni-sifre');
        }

        $resetId = (int) ($_SESSION['password_reset_id'] ?? 0);
        $basarili = (new PasswordReset())->sifreyiSifirla($resetId, password_hash($sifre, PASSWORD_DEFAULT));
        unset($_SESSION['password_reset_id']);

        if (!$basarili) {
            $this->flashMesajYaz('Kod gecersiz veya suresi dolmus. Yeni kod talep edin.');
            $this->yonlendir('sifremi-unuttum');
        }

        $this->flashMesajYaz('Sifreniz basariyla guncellendi. Yeni sifrenizle giris yapabilirsiniz.');
        $this->yonlendir('login');
    }

    private function aktifResetKaydiAl(): array|false
    {
        require_once BASE_PATH . '/app/models/PasswordReset.php';

        $resetId = (int) ($_SESSION['password_reset_id'] ?? 0);

        if ($resetId <= 0) {
            return false;
        }

        return (new PasswordReset())->aktifKayitBul($resetId);
    }

    private function flashMesajYaz(string $mesaj): void
    {
        $_SESSION['password_reset_flash'] = $mesaj;
    }

    private function flashMesajAl(): ?string
    {
        $mesaj = $_SESSION['password_reset_flash'] ?? null;
        unset($_SESSION['password_reset_flash']);

        return $mesaj;
    }

    private function yonlendir(string $url): void
    {
        header('Location: index.php?url=' . $url);
        exit;
    }
}
