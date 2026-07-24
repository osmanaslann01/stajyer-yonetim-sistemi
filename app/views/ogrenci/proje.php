<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Proje Takibi - Öğrenci Paneli</title>
</head>
<body>

<h1>Staj Projelerim</h1>

<p><a href="index.php?url=ogrenci/dashboard">&larr; Panele Dön</a></p>

<hr>

<?php if (!$basvuru): ?>
<p>Onaylı bir staj başvurunuz bulunmamaktadır.</p>
<?php else: ?>

<?php if (empty($projeler)): ?>
<!-- Kural: Eğer öğrenciye proje atanmamışsa "Henüz size proje atanmadı." mesajı göster, teslim alanlarını gizle -->
<p>Henüz size proje atanmadı.</p>
<?php else: ?>

<?php foreach ($projeler as $proje): ?>

<h2><?= htmlspecialchars($proje['proje_adi']); ?></h2>

<table border="1" cellpadding="5">
    <tr><th>Proje Açıklaması</th><td><?= htmlspecialchars($proje['proje_aciklamasi'] ?? '-'); ?></td></tr>
    <tr><th>Gereksinimler</th><td><?= htmlspecialchars($proje['gereksinimler'] ?? '-'); ?></td></tr>
    <tr><th>Veriliş Tarihi</th><td><?= $proje['verilis_tarihi']; ?></td></tr>
    <tr><th>Teslim Tarihi</th><td><?= $proje['teslim_tarihi']; ?></td></tr>
    <tr><th>Durum</th><td><strong><?= $proje['durum']; ?></strong></td></tr>
</table>

<h3>Yeni Teslim Yap</h3>
<form method="POST" action="index.php?url=ogrenci/projeKaydet" enctype="multipart/form-data">
    <label>Proje Adı:</label><br>
    <input type="text" name="proje_adi" value="<?= htmlspecialchars($proje['proje_adi']); ?>" required>
    <br><br>

    <label>Açıklama / Not:</label><br>
    <textarea name="aciklama" rows="4" cols="50" placeholder="Proje hakkında not yazabilirsiniz..."></textarea>
    <br><br>

    <label>Proje Dosyası (PDF, ZIP, RAR, DOC, DOCX, JPG, PNG - Maks 20MB):</label><br>
    <input type="file" name="proje_dosya" required accept=".pdf,.zip,.rar,.doc,.docx,.jpg,.png">
    <br><br>

    <button type="submit">Proje Dosyasını Teslim Et</button>
</form>

<h3>Önceki Teslimler</h3>
<?php if (empty($proje['teslimler'])): ?>
<p>Henüz teslim yapılmamıştır.</p>
<?php else: ?>
<table border="1" cellpadding="5">
    <tr>
        <th>Dosya Adı</th>
        <th>Teslim Tarihi</th>
        <th>Durum</th>
        <th>Geri Bildirim</th>
    </tr>
    <?php foreach ($proje['teslimler'] as $t): ?>
    <tr>
        <td><?= htmlspecialchars($t['dosya_adi']); ?></td>
        <td><?= $t['teslim_tarihi']; ?></td>
        <td>
            <?php
            $d = $t['teslim_durumu'];
            if ($d == 'Onaylandi') echo '✅ Onaylandı';
            elseif ($d == 'Revizyon') echo '🔄 Revizyon İstendi';
            elseif ($d == 'Reddedildi') echo '❌ Reddedildi';
            elseif ($d == 'Inceleniyor') echo '🔍 İnceleniyor';
            else echo '📤 Teslim Edildi';
            ?>
        </td>
        <td><?= htmlspecialchars($t['aciklama'] ?? '-'); ?></td>
    </tr>
    <?php endforeach; ?>
</table>
<?php endif; ?>

<hr>

<?php endforeach; ?>

<?php endif; ?>

<?php endif; ?>

</body>
</html>
