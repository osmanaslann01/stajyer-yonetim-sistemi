<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Sistem Logları - Staj Yönetim Sistemi</title>
</head>
<body>

<h1>Sistem Logları</h1>

<p><a href="index.php?url=admin/dashboard">&larr; Panele Dön</a></p>

<hr>

<table border="1" cellpadding="6" cellspacing="0">
<tr>
    <th>#</th>
    <th>Kullanıcı</th>
    <th>İşlem</th>
    <th>Tablo</th>
    <th>Kayıt ID</th>
    <th>IP Adresi</th>
    <th>Tarih</th>
</tr>

<?php if (empty($loglar)): ?>
<tr>
    <td colspan="7">Henüz log kaydı bulunmamaktadır.</td>
</tr>
<?php else: ?>
<?php foreach ($loglar as $log): ?>
<tr>
    <td><?= $log['log_id']; ?></td>
    <td>
        <?php if ($log['ad']): ?>
            <?= htmlspecialchars($log['ad'] . ' ' . $log['soyad']); ?>
        <?php else: ?>
            <em>Sistem</em>
        <?php endif; ?>
    </td>
    <td><?= htmlspecialchars($log['islem']); ?></td>
    <td><?= htmlspecialchars($log['tablo_adi'] ?? '-'); ?></td>
    <td><?= $log['kayit_id'] ?? '-'; ?></td>
    <td><?= htmlspecialchars($log['ip'] ?? '-'); ?></td>
    <td><?= $log['islem_tarihi']; ?></td>
</tr>
<?php endforeach; ?>
<?php endif; ?>

</table>

</body>
</html>
