<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Belge Geçmişi</title>
</head>
<body>

<h1><?= htmlspecialchars($belge_turu); ?> - Belge Geçmişi</h1>

<p><a href="javascript:history.back()">&larr; Geri Dön</a></p>

<hr>

<table border="1" cellpadding="6" cellspacing="0">
<tr>
    <th>Yükleyen</th>
    <th>Dosya Adı</th>
    <th>İmza Durumu</th>
    <th>Onay Durumu</th>
    <th>Tarih</th>
    <th>İndir</th>
</tr>

<?php if (empty($gecmis)): ?>
<tr>
    <td colspan="6">Bu belge türüne ait kayıt bulunamadı.</td>
</tr>
<?php else: ?>
<?php foreach ($gecmis as $g): ?>
<tr>
    <td><?= htmlspecialchars($g['ad'] . ' ' . $g['soyad']); ?> (<?= $g['yukleyen_tur']; ?>)</td>
    <td><?= htmlspecialchars($g['dosya_adi']); ?></td>
    <td><?= $g['imza_durumu'] == 'Imzali' ? '✅ İmzalı' : '⬜ İmzasız'; ?></td>
    <td>
        <?php
        if ($g['onay_durumu'] == 'Onaylandi') echo '🟢 Onaylandı';
        elseif ($g['onay_durumu'] == 'Reddedildi') echo '🔴 Reddedildi';
        else echo '🟡 Bekliyor';
        ?>
    </td>
    <td><?= $g['yuklenme_tarihi']; ?></td>
    <td>
        <a href="index.php?url=belge/indir&id=<?= $g['belge_id']; ?>">İndir</a>
    </td>
</tr>
<?php endforeach; ?>
<?php endif; ?>
</table>

</body>
</html>
