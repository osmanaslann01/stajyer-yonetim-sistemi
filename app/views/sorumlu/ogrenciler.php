<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Atanan Öğrenciler - Sorumlu</title>
</head>
<body>

<h1>Atanan Öğrencilerim</h1>

<p><a href="index.php?url=sorumlu/dashboard">&larr; Panele Dön</a></p>

<hr>

<?php if (empty($ogrenciler)): ?>
<p>Henüz size atanmış öğrenci bulunmamaktadır.</p>
<?php else: ?>

<table border="1" cellpadding="6" cellspacing="0">
<tr>
    <th>Ad Soyad</th>
    <th>Öğrenci No</th>
    <th>Fakülte / Bölüm</th>
    <th>Staj Türü</th>
    <th>Staj Durumu</th>
    <th>İşlem</th>
</tr>

<?php foreach ($ogrenciler as $o): ?>
<tr>
    <td><?= htmlspecialchars($o['ad'] . ' ' . $o['soyad']); ?></td>
    <td><?= htmlspecialchars($o['ogrenci_no']); ?></td>
    <td><?= htmlspecialchars($o['fakulte'] . ' / ' . $o['bolum']); ?></td>
    <td><?= htmlspecialchars($o['staj_turu']); ?></td>
    <td><?= htmlspecialchars($o['staj_durumu'] ?? '-'); ?></td>
    <td>
        <a href="index.php?url=sorumlu/ogrenciDetay&basvuru_id=<?= $o['basvuru_id']; ?>">
            Detay Görüntüle
        </a>
        |
        <a href="index.php?url=sorumlu/degerlendirme&basvuru_id=<?= $o['basvuru_id']; ?>">
            Değerlendir
        </a>
    </td>
</tr>
<?php endforeach; ?>
</table>

<?php endif; ?>

</body>
</html>
