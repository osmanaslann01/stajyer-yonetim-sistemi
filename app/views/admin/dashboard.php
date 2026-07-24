<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Admin Paneli - Staj Yönetim Sistemi</title>
</head>
<body>

<h1>Admin Paneli</h1>

<p>Hoş geldiniz, <strong><?= htmlspecialchars($kullanici['ad'] . ' ' . $kullanici['soyad']); ?></strong></p>

<hr>

<h3>Özet</h3>
<ul>
    <li>Toplam Başvuru: <strong><?= $toplam_basvuru; ?></strong></li>
    <li>Bekleyen Başvuru: <strong><?= $bekleyen; ?></strong></li>
    <li>Onaylı Başvuru: <strong><?= $onayli; ?></strong></li>
</ul>

<hr>

<h3>Menü</h3>
<ul>
    <li><a href="index.php?url=admin/basvurular">Staj Başvuruları</a></li>
    <li><a href="index.php?url=admin/belgeler">Belge Yönetimi</a></li>
    <li><a href="index.php?url=admin/yoklama">Yoklama Takibi</a></li>
    <li><a href="index.php?url=admin/izinler">İzin Talepleri</a></li>
    <li><a href="index.php?url=admin/loglar">Sistem Logları</a></li>
    <li><a href="index.php?url=bildirimler">Bildirimlerim</a></li>
    <li><a href="index.php?url=logout">Çıkış Yap</a></li>
</ul>

</body>
</html>