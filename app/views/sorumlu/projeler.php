<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Proje Takibi - Sorumlu</title>
</head>
<body>

<h1>Proje Takibi</h1>

<p><a href="index.php?url=sorumlu/dashboard">&larr; Panele Dön</a></p>

<?php if (isset($_SESSION['flash_message'])): ?>
    <div style="background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 10px; margin-bottom: 15px; border-radius: 4px; display: inline-block;">
        <?= $_SESSION['flash_message']; ?>
    </div>
    <?php unset($_SESSION['flash_message']); ?>
<?php endif; ?>

<div style="background-color: #f8f9fa; border: 1px solid #dee2e6; padding: 15px; border-radius: 4px; margin-bottom: 20px; max-width: 500px;">
    <h3>Yeni Proje Ata</h3>
    <form method="POST" action="index.php?url=sorumlu/projeAta">
        <p>
            <label for="basvuru_id"><strong>Öğrenci Seç:</strong></label><br>
            <select name="basvuru_id" id="basvuru_id" required style="width: 100%; padding: 5px;">
                <option value="">-- Öğrenci Seçin --</option>
                <?php foreach ($ogrenciler as $o): ?>
                    <option value="<?= $o['basvuru_id']; ?>">
                        <?= htmlspecialchars($o['ad'] . ' ' . $o['soyad'] . ' (' . $o['ogrenci_no'] . ')'); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </p>
        <p>
            <label for="proje_adi"><strong>Proje Başlığı:</strong></label><br>
            <input type="text" name="proje_adi" id="proje_adi" required style="width: 97%; padding: 5px;">
        </p>
        <p>
            <label for="proje_aciklamasi"><strong>Proje Açıklaması:</strong></label><br>
            <textarea name="proje_aciklamasi" id="proje_aciklamasi" rows="4" style="width: 97%; padding: 5px;"></textarea>
        </p>
        <p>
            <label for="teslim_tarihi"><strong>Teslim Tarihi:</strong></label><br>
            <input type="date" name="teslim_tarihi" id="teslim_tarihi" required style="width: 100%; padding: 5px; box-sizing: border-box;">
        </p>
        <button type="submit" style="padding: 6px 12px; cursor: pointer;">Kaydet</button>
    </form>
</div>

<hr>

<?php if (empty($projeler)): ?>
<p>Henüz size ait proje bulunmamaktadır.</p>
<?php else: ?>

<?php foreach ($projeler as $proje): ?>

<h2><?= htmlspecialchars($proje['proje_adi']); ?></h2>

<table border="1" cellpadding="5">
    <tr><th>Öğrenci</th><td><?= htmlspecialchars($proje['ogrenci_ad'] . ' ' . $proje['ogrenci_soyad']); ?></td></tr>
    <tr><th>Öğrenci No</th><td><?= htmlspecialchars($proje['ogrenci_no']); ?></td></tr>
    <tr><th>Proje Durumu</th><td><strong><?= $proje['durum']; ?></strong></td></tr>
    <tr><th>Teslim Tarihi</th><td><?= $proje['teslim_tarihi']; ?></td></tr>
</table>

<?php if (!empty($proje['teslimler'])): ?>
<h3>Teslimler</h3>
<table border="1" cellpadding="5">
    <tr>
        <th>Dosya Adı</th>
        <th>Teslim Tarihi</th>
        <th>Durum</th>
        <th>Açıklama</th>
        <th>İndir</th>
        <th>Güncelle</th>
    </tr>
    <?php foreach ($proje['teslimler'] as $teslim): ?>
    <tr>
        <td><?= htmlspecialchars($teslim['dosya_adi']); ?></td>
        <td><?= $teslim['teslim_tarihi']; ?></td>
        <td><strong><?= $teslim['teslim_durumu']; ?></strong></td>
        <td><?= htmlspecialchars($teslim['aciklama'] ?? '-'); ?></td>
        <td>
            <a href="<?= '/StajYonetimSistemi/' . $teslim['dosya_yolu']; ?>" target="_blank">İndir</a>
        </td>
        <td>
            <form method="POST" action="index.php?url=sorumlu/projeGuncelle">
                <input type="hidden" name="teslim_id" value="<?= $teslim['teslim_id']; ?>">
                <select name="durum">
                    <option value="Inceleniyor" <?= $teslim['teslim_durumu'] == 'Inceleniyor' ? 'selected' : ''; ?>>İnceleniyor</option>
                    <option value="Revizyon" <?= $teslim['teslim_durumu'] == 'Revizyon' ? 'selected' : ''; ?>>Revizyon</option>
                    <option value="Onaylandi" <?= $teslim['teslim_durumu'] == 'Onaylandi' ? 'selected' : ''; ?>>Onaylandı</option>
                    <option value="Reddedildi" <?= $teslim['teslim_durumu'] == 'Reddedildi' ? 'selected' : ''; ?>>Reddedildi</option>
                </select>
                <input type="text" name="feedback" placeholder="Geri bildirim (opsiyonel)">
                <button type="submit">Güncelle</button>
            </form>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
<?php else: ?>
<p>Henüz teslim yapılmamıştır.</p>
<?php endif; ?>

<hr>

<?php endforeach; ?>

<?php endif; ?>

</body>
</html>
