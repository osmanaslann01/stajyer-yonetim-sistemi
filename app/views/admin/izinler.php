<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>İzin Yönetimi - Admin</title>
</head>
<body>

<h1>İzin Talepleri</h1>

<p><a href="index.php?url=admin/dashboard">&larr; Panele Dön</a></p>

<hr>

<table border="1" cellpadding="6" cellspacing="0">
<tr>
    <th>Öğrenci</th>
    <th>Numara</th>
    <th>Başlangıç</th>
    <th>Bitiş</th>
    <th>Mazeret</th>
    <th>Belge</th>
    <th>Durum</th>
    <th>İşlem</th>
</tr>

<?php if (empty($izinler)): ?>
<tr>
    <td colspan="8">Henüz izin talebi bulunmamaktadır.</td>
</tr>
<?php else: ?>
<?php foreach ($izinler as $izin): ?>
<tr>
    <td><?= htmlspecialchars($izin['ad'] . ' ' . $izin['soyad']); ?></td>
    <td><?= htmlspecialchars($izin['ogrenci_no']); ?></td>
    <td><?= $izin['baslangic_tarihi']; ?></td>
    <td><?= $izin['bitis_tarihi']; ?></td>
    <td><?= htmlspecialchars($izin['mazeret']); ?></td>
    <td>
        <?php if ($izin['belge_yolu']): ?>
            <a href="index.php?url=belge/indir&id=<?= $izin['belge_id']; ?>" target="_blank">Belgeyi İndir</a>
        <?php else: ?>
            -
        <?php endif; ?>
    </td>
    <td>
        <?php
        $durum = $izin['durum'];
        if ($durum == 'Beklemede') echo '🟡 Beklemede';
        elseif ($durum == 'Onaylandi') echo '🟢 Onaylandı';
        else echo '🔴 Reddedildi';
        ?>
    </td>
    <td>
        <?php if ($izin['durum'] == 'Beklemede'): ?>
        <a href="index.php?url=admin/izinOnayla&id=<?= $izin['izin_id']; ?>">
            <button>Onayla</button>
        </a>
        <a href="index.php?url=admin/izinReddet&id=<?= $izin['izin_id']; ?>">
            <button>Reddet</button>
        </a>
        <?php else: ?>
        İşlem Tamamlandı
        <?php if ($izin['onay_tarihi']): ?>
        <br><small><?= $izin['onay_tarihi']; ?></small>
        <?php endif; ?>
        <?php endif; ?>
    </td>
</tr>
<?php endforeach; ?>
<?php endif; ?>

</table>

</body>
</html>
