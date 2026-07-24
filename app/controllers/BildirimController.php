<?php

class BildirimController extends Controller
{
    public function index()
    {
        $this->auth();
        require_once BASE_PATH . '/app/models/Bildirim.php';
        $bildirimModel = new Bildirim();
        
        $bildirimler = $bildirimModel->tumBildirimleriListele($_SESSION['kullanici']['id']);

        $this->view('bildirim/index', [
            'bildirimler' => $bildirimler
        ]);
    }

    public function okundu()
    {
        $this->auth();
        $bildirim_id = $_GET['id'] ?? null;
        if ($bildirim_id) {
            require_once BASE_PATH . '/app/models/Bildirim.php';
            $bildirimModel = new Bildirim();
            $bildirimModel->okunduIsaretle($bildirim_id, $_SESSION['kullanici']['id']);
        }
        header("Location: index.php?url=bildirimler");
        exit;
    }
}
