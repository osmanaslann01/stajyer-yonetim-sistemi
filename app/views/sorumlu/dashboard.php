<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Sorumlu Paneli - Staj Yönetim Sistemi</title>
</head>
<body>

<h1>Sorumlu Paneli</h1>

<p>Hoş geldiniz, <strong><?= htmlspecialchars($kullanici['ad'] . ' ' . $kullanici['soyad']); ?></strong></p>
<?php if (!empty($sorumlu['unvan'])): ?>
<p><em><?= htmlspecialchars($sorumlu['unvan']); ?></em> | <?= htmlspecialchars($sorumlu['birim'] ?? ''); ?></p>
<?php endif; ?>

<hr>

<h3>Özet</h3>
<ul>
    <li>Atanan Öğrenci Sayısı: <strong><?= $ogrenci_sayisi; ?></strong></li>
</ul>

<hr>

<h3>Menü</h3>


<ul>

<li>
<a href="index.php?url=sorumlu/ogrenciler">
Atanan Öğrencilerim
</a>
</li>


<li>
<a href="index.php?url=sorumlu/projeler">
Proje Takibi
</a>
</li>


<li>
<a href="index.php?url=sorumlu/yoklama">
Yoklama Takibi
</a>
</li>


<li>
<a href="index.php?url=bildirimler">
Bildirimlerim
</a>
</li>


<li>
<a href="index.php?url=logout">
Çıkış Yap
</a>
</li>

</ul>



</body>
</html>