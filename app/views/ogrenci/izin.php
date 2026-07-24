<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>İzin Talebi - Öğrenci Paneli</title>
</head>
<body>

<h1>İzin Taleplerim</h1>

<p><a href="index.php?url=ogrenci/dashboard">&larr; Panele Dön</a></p>

<hr>

<?php if (!$basvuru): ?>
<p>Onaylı bir staj başvurunuz bulunmamaktadır. İzin talebinde bulunamazsınız.</p>
<?php else: ?>

<h3>Yeni İzin Talebi</h3>

<form method="POST" action="index.php?url=ogrenci/izinTalebi" enctype="multipart/form-data">
    <label>Başlangıç Tarihi:</label><br>
    <input type="date" name="baslangic_tarihi" required>
    <br><br>

    <label>Bitiş Tarihi:</label><br>
    <input type="date" name="bitis_tarihi" required>
    <br><br>

    <label>Mazeret / Açıklama:</label><br>
    <textarea name="mazeret" rows="4" cols="50" required placeholder="İzin mazeretinizi açıklayınız..."></textarea>
    <br><br>

    <label>Belge / Dilekçe (Opsiyonel - PDF, JPG, PNG):</label><br>
    <input type="file" name="belge" accept=".pdf,.jpg,.png">
    <br><br>

    <button type="submit">İzin Talebini Gönder</button>
</form>

<hr>

<h3>İzin Talebi Geçmişim</h3>

<?php if (empty($izinler)): ?>
<p>Henüz izin talebinde bulunmadınız.</p>
<?php else: ?>

<table border="1" cellpadding="6" cellspacing="0">
<tr>
    <th>Başlangıç</th>
    <th>Bitiş</th>
    <th>Mazeret</th>
    <th>Durum</th>
    <th>Onay Tarihi</th>
</tr>

<?php foreach ($izinler as $izin): ?>
<tr>
    <td><?= $izin['baslangic_tarihi']; ?></td>
    <td><?= $izin['bitis_tarihi']; ?></td>
    <td><?= htmlspecialchars($izin['mazeret']); ?></td>
    <td>
        <?php
        if ($izin['durum'] == 'Beklemede') echo '🟡 Beklemede';
        elseif ($izin['durum'] == 'Onaylandi') echo '🟢 Onaylandı';
        else echo '🔴 Reddedildi';
        ?>
    </td>
    <td><?= $izin['onay_tarihi'] ?? '-'; ?></td>
</tr>
<?php endforeach; ?>

</table>
<?php endif; ?>

<?php endif; ?>

</body>
</html>
