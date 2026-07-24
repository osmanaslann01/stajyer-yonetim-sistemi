<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Bildirimlerim</title>
</head>
<body>

<h1>Bildirimler</h1>

<?php
$dashboardUrl = 'index.php?url=ogrenci/dashboard';
if (isset($_SESSION['kullanici']['rol'])) {
    if ($_SESSION['kullanici']['rol'] == 1) {
        $dashboardUrl = 'index.php?url=admin/dashboard';
    } elseif ($_SESSION['kullanici']['rol'] == 3) {
        $dashboardUrl = 'index.php?url=sorumlu/dashboard';
    }
}
?>
<p><a href="<?= $dashboardUrl; ?>">&larr; Panele Dön</a></p>

<hr>

<?php if (empty($bildirimler)): ?>
    <p>Herhangi bir bildiriminiz bulunmamaktadır.</p>
<?php else: ?>
    <table border="1" cellpadding="8" cellspacing="0">
        <thead>
            <tr>
                <th>Tür</th>
                <th>Başlık</th>
                <th>Mesaj</th>
                <th>Tarih</th>
                <th>Durum</th>
                <th>İşlem</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($bildirimler as $b): ?>
                <tr>
                    <td>
                        <?php if ($b['tip'] == 'SMS'): ?>
                            📱 SMS
                        <?php else: ?>
                            🔔 Sistem
                        <?php endif; ?>
                    </td>
                    <td><strong><?= htmlspecialchars($b['baslik']); ?></strong></td>
                    <td><?= htmlspecialchars($b['mesaj']); ?></td>
                    <td><?= $b['gonderim_tarihi']; ?></td>
                    <td>
                        <?php if ($b['okunma_tarihi']): ?>
                            🟢 Okundu (<?= $b['okunma_tarihi']; ?>)
                        <?php else: ?>
                            🔴 Okunmadı
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if (!$b['okunma_tarihi']): ?>
                            <a href="index.php?url=bildirimOkundu&id=<?= $b['bildirim_id']; ?>">Okundu İşaretle</a>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

</body>
</html>
