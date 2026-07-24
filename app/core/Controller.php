<?php

class Controller
{
    protected function view(string $view, array $data = [])
    {
        extract($data);

        require BASE_PATH . "/app/views/$view.php";
    }

    protected function redirect(string $url)
    {
        header("Location: /$url");
        exit;
    }

    protected function auth()
    {

        if(!Auth::check())
        {

            $this->redirect('login');

        }

    }

    protected function onayliStajBasvurusuZorunlu(): array
    {
        $this->auth();

        if ($_SESSION['kullanici']['rol'] != 2) {
            die('Bu sayfaya sadece ogrenciler erisebilir.');
        }

        require_once BASE_PATH . '/app/models/StajBasvurusu.php';

        $basvuruModel = new StajBasvurusu();
        $ogrenciId = $_SESSION['kullanici']['ogrenci_id'];
        $sonBasvuru = $basvuruModel->sonBasvuru($ogrenciId);

        if (!$sonBasvuru) {
            header('Location: index.php?url=ogrenci/basvuru');
            exit;
        }

        $onayliBasvuru = $basvuruModel->onayliBasvuru($ogrenciId);

        if (!$onayliBasvuru || (int) $onayliBasvuru['basvuru_id'] !== (int) $sonBasvuru['basvuru_id']) {
            header('Location: index.php?url=ogrenci/basvuruDurumu');
            exit;
        }

        return $onayliBasvuru;
    }

}
