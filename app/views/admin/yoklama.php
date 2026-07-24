<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Yoklama Takibi - Admin</title>
</head>
<body>

<h1>Yoklama Takibi</h1>

<p><a href="index.php?url=admin/dashboard">&larr; Panele Dön</a></p>

<hr>

<h2>Devamsızlık Ekle</h2>

<form method="POST" action="index.php?url=admin/devamsizlikEkle">
    <label>Başvuru:</label>
    <select name="basvuru_id" required>
        <option value="">-- Öğrenci Seçin --</option>
        <?php foreach ($basvurular as $b): ?>
        <option value="<?= $b['basvuru_id']; ?>">
            <?= htmlspecialchars($b['ad'] . ' ' . $b['soyad'] . ' (' . $b['ogrenci_no'] . ')'); ?>
        </option>
        <?php endforeach; ?>
    </select>
    <br><br>

    <label>Tarih:</label>
    <input type="date" name="tarih" required value="<?= date('Y-m-d'); ?>">
    <br><br>

    <label>Devamsızlık Türü:</label>
    <select name="devamsizlik_turu" required>
        <option value="Izinsiz">İzinsiz</option>
        <option value="Izinli">İzinli</option>
        <option value="Raporlu">Raporlu</option>
    </select>
    <br><br>

    <label>Açıklama:</label>
    <input type="text" name="aciklama" placeholder="İsteğe bağlı açıklama">
    <br><br>

    <button type="submit">Kaydet</button>
</form>

<hr>

<h2>Devamsızlık Kayıtları</h2>

<table border="1" cellpadding="6" cellspacing="0">
<tr>
    <th>Öğrenci</th>
    <th>Numara</th>
    <th>Tarih</th>
    <th>Tür</th>
    <th>Açıklama</th>
</tr>

<?php if (empty($devamsizliklar)): ?>
<tr>
    <td colspan="5">Henüz devamsızlık kaydı bulunmamaktadır.</td>
</tr>
<?php else: ?>
<?php foreach ($devamsizliklar as $dev): ?>
<tr>
    <td><?= htmlspecialchars($dev['ad'] . ' ' . $dev['soyad']); ?></td>
    <td><?= htmlspecialchars($dev['ogrenci_no']); ?></td>
    <td><?= $dev['tarih']; ?></td>
    <td>
        <?php
        if ($dev['devamsizlik_turu'] == 'Izinsiz') echo '🔴 İzinsiz';
        elseif ($dev['devamsizlik_turu'] == 'Izinli') echo '🟢 İzinli';
        else echo '🟡 Raporlu';
        ?>
    </td>
    <td><?= htmlspecialchars($dev['aciklama'] ?? '-'); ?></td>
</tr>
<?php endforeach; ?>
<?php endif; ?>
</table>

<hr>

<h2>Yoklama Giriş/Çıkış Kayıtları</h2>

<table border="1" cellpadding="6" cellspacing="0">
<tr>
    <th>Öğrenci</th>
    <th>Numara</th>
    <th>İşlem Tipi</th>
    <th>Oturum</th>
    <th>IP Adresi</th>
    <th>Tarih/Saat</th>
</tr>

<?php if (empty($yoklamalar)): ?>
<tr>
    <td colspan="6">Henüz yoklama kaydı bulunmamaktadır.</td>
</tr>
<?php else: ?>
<?php foreach ($yoklamalar as $y): ?>
<tr>
    <td><?= htmlspecialchars($y['ad'] . ' ' . $y['soyad']); ?></td>
    <td><?= htmlspecialchars($y['ogrenci_no']); ?></td>
    <td><?= $y['islem_tipi'] == 'Giris' ? '➡️ Giriş' : '⬅️ Çıkış'; ?></td>
    <td><?= $y['oturum_tipi']; ?></td>
    <td><?= htmlspecialchars($y['ip_adresi']); ?></td>
    <td><?= $y['islem_zamani']; ?></td>
</tr>
<?php endforeach; ?>
<?php endif; ?>
</table>

</body>
</html>
