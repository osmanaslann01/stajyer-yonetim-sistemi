<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Yoklama Takibi - Sorumlu</title>
</head>
<body>

<h1>Yoklama Takibi</h1>

<p><a href="index.php?url=sorumlu/dashboard">&larr; Panele Dön</a></p>

<hr>

<?php if (empty($ogrenciler)): ?>
<p>Henüz atanmış öğrenci bulunmamaktadır.</p>
<?php else: ?>

<?php foreach ($ogrenciler as $o): ?>

<h2><?= htmlspecialchars($o['ad'] . ' ' . $o['soyad']); ?> (<?= $o['ogrenci_no']; ?>)</h2>

<h4>Devamsızlık Ekle</h4>
<form method="POST" action="index.php?url=admin/devamsizlikEkle">
    <input type="hidden" name="basvuru_id" value="<?= $o['basvuru_id']; ?>">
    <label>Tarih:</label>
    <input type="date" name="tarih" required value="<?= date('Y-m-d'); ?>">
    <label>Tür:</label>
    <select name="devamsizlik_turu" required>
        <option value="Izinsiz">İzinsiz</option>
        <option value="Raporlu">Raporlu</option>
    </select>
    <input type="text" name="aciklama" placeholder="Açıklama">
    <button type="submit">Kaydet</button>
</form>

<h4>Devamsızlık Kayıtları</h4>
<?php if (empty($o['devamsizliklar'])): ?>
<p>Devamsızlık kaydı bulunmamaktadır.</p>
<?php else: ?>
<table border="1" cellpadding="4">
    <tr>
        <th>Tarih</th>
        <th>Tür</th>
        <th>Açıklama</th>
    </tr>
    <?php foreach ($o['devamsizliklar'] as $d): ?>
    <tr>
        <td><?= $d['tarih']; ?></td>
        <td>
            <?php
            if ($d['devamsizlik_turu'] == 'Izinsiz') echo '🔴 İzinsiz';
            elseif ($d['devamsizlik_turu'] == 'Izinli') echo '🟢 İzinli';
            else echo '🟡 Raporlu';
            ?>
        </td>
        <td><?= htmlspecialchars($d['aciklama'] ?? '-'); ?></td>
    </tr>
    <?php endforeach; ?>
</table>
<?php endif; ?>

<h4>Yoklama Giriş/Çıkış Kayıtları (Son 10)</h4>
<?php if (empty($o['yoklamalar'])): ?>
<p>Yoklama kaydı bulunmamaktadır.</p>
<?php else: ?>
<table border="1" cellpadding="4">
    <tr>
        <th>Tarih / Saat</th>
        <th>İşlem</th>
        <th>IP</th>
    </tr>
    <?php $counter = 0; ?>
    <?php foreach ($o['yoklamalar'] as $y): ?>
    <?php if ($counter++ >= 10) break; ?>
    <tr>
        <td><?= $y['islem_zamani']; ?></td>
        <td><?= $y['islem_tipi'] == 'Giris' ? '➡️ Giriş' : '⬅️ Çıkış'; ?></td>
        <td><?= htmlspecialchars($y['ip_adresi']); ?></td>
    </tr>
    <?php endforeach; ?>
</table>
<?php endif; ?>

<hr>

<?php endforeach; ?>
<?php endif; ?>

</body>
</html>
