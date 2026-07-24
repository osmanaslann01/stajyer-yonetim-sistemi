<?php


class BasvuruController extends Controller
{
    public function __construct()
    {
        require_once BASE_PATH . '/app/services/SmsService.php';
    }


  public function basvuruDurumu()
{
    $this->auth();

    if ($_SESSION['kullanici']['rol'] != 2) {
        die('Bu sayfaya sadece ogrenciler erisebilir.');
    }

    require_once BASE_PATH . '/app/models/StajBasvurusu.php';

    $model = new StajBasvurusu();
    $ogrenciId = $_SESSION['kullanici']['ogrenci_id'];
    $basvuru = $model->sonBasvuru($ogrenciId);

    if (!$basvuru) {
        header('Location: index.php?url=ogrenci/basvuru');
        exit;
    }

    $onayliBasvuru = $model->onayliBasvuru($ogrenciId);
    $reddedilenBasvuru = $model->reddedilenBasvuru($ogrenciId);

    $this->view('ogrenci/basvuruDurumu', [
        'basvuru' => $basvuru,
        'onayli' => $onayliBasvuru !== false,
        'reddedildi' => $reddedilenBasvuru !== false
    ]);
}

  public function create()
{
    $this->auth();

    if ($_SESSION['kullanici']['rol'] != 2) {
        die('Bu sayfaya sadece ogrenciler erisebilir.');
    }

    require_once BASE_PATH . '/app/models/StajDonemi.php';
    require_once BASE_PATH . '/app/models/StajBasvurusu.php';

    $donemModel = new StajDonemi();
    $basvuruModel = new StajBasvurusu();

    $sonBasvuru = $basvuruModel->sonBasvuru($_SESSION['kullanici']['ogrenci_id']);

    if ($sonBasvuru && $sonBasvuru['durum'] !== 'Reddedildi') {
        header('Location: index.php?url=ogrenci/basvuruDurumu');
        exit;
    }

    $donemler = $donemModel->aktifDonemler();
    $basvurular = $basvuruModel->ogrenciBasvurulari($_SESSION['kullanici']['ogrenci_id']);

    $this->view('ogrenci/basvuru', [
        'donemler' => $donemler,
        'basvurular' => $basvurular
    ]);
}



public function store()
{
    $this->auth();

    if ($_SESSION['kullanici']['rol'] != 2) {
        die('Yetkisiz islem.');
    }

    require_once BASE_PATH .
    '/app/models/StajBasvurusu.php';

 $model = new StajBasvurusu();

//aynı döneme ait basvuru var mı hata mesajı

if($model->varMi(
$_SESSION['kullanici']['ogrenci_id'],
$_POST['donem_id']
))
{
    echo "Bu dönem için zaten başvurunuz var.";
    exit;
}




    $cv_yolu = null;



    if(isset($_FILES['cv']) && $_FILES['cv']['error'] == 0)
    {


        $dosya = $_FILES['cv'];


        $uzanti = strtolower(
            pathinfo(
                $dosya['name'],
                PATHINFO_EXTENSION
            )
        );


        $izinli = [
            'pdf',
            'doc',
            'docx'
        ];



        if(in_array($uzanti,$izinli))
        {


            $yeniAd =
            uniqid()
            .'.'
            .$uzanti;



            $hedef = BASE_PATH .
            '/uploads/cv/'
            .$yeniAd;



            if(move_uploaded_file(
                $dosya['tmp_name'],
                $hedef
            ))
            {

                $cv_yolu =
                'uploads/cv/'.$yeniAd;

            }


        }

    }



    $model = new StajBasvurusu();



    $sonuc = $model->kaydet([


        "ogrenci_id" =>
        $_SESSION['kullanici']['ogrenci_id'],


        "donem_id" =>
        $_POST['donem_id'],


        "staj_turu" =>
        $_POST['staj_turu'],


        "aciklama" =>
        $_POST['aciklama'] ?? null,


        "cv_yolu" =>
        $cv_yolu


    ]);



    if($sonuc)
    {
        header('Location: index.php?url=ogrenci/basvuruDurumu');
        exit;

        echo "Başvuru oluşturuldu";

    }
    else
    {

        echo "Başvuru başarısız";

    }


}






}
