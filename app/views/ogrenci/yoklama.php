<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Yoklama - Öğrenci Paneli</title>
</head>
<body>

<h1>Staj Yoklama</h1>

<p><a href="index.php?url=ogrenci/dashboard">&larr; Panele Dön</a></p>

<hr>

<?php if (!$basvuru): ?>
<p>Onaylı bir staj başvurunuz bulunmamaktadır. Yoklama yapamazsınız.</p>
<?php else: ?>

<p>Staj Türü: <strong><?= htmlspecialchars($basvuru['staj_turu']); ?></strong></p>
<p>IP Adresiniz: <strong><?= $ip_adresi; ?></strong></p>

<hr>

<h3>Bugünkü Yoklama Durumu</h3>

<?php $giris = false; $cikis = false; ?>
<?php foreach ($bugunku_kayitlar as $k): ?>
    <?php if ($k['islem_tipi'] == 'Giris') $giris = true; ?>
    <?php if ($k['islem_tipi'] == 'Cikis') $cikis = true; ?>
<?php endforeach; ?>

<?php if (!$giris): ?>
<p>🔴 Bugün henüz giriş yapmadınız.</p>
<form method="POST" action="index.php?url=ogrenci/yoklamaKaydet">
    <button type="submit">📍 Giriş Yap</button>
</form>
<?php elseif ($giris && !$cikis): ?>
<p>🟢 Bugün giriş yapıldı. Çıkış yapmak için aşağıdaki butona tıklayın.</p>
<form method="POST" action="index.php?url=ogrenci/yoklamaKaydet">
    <button type="submit">📍 Çıkış Yap</button>
</form>
<?php else: ?>
<p>✅ Bugünkü giriş-çıkış yoklamanız tamamlanmıştır.</p>
<?php endif; ?>

<?php if (!empty($bugunku_kayitlar)): ?>
<h4>Bugünkü Kayıtlar:</h4>
<table border="1" cellpadding="5">
    <tr>
        <th>İşlem</th>
        <th>Saat</th>
    </tr>
    <?php foreach ($bugunku_kayitlar as $k): ?>
    <tr>
        <td><?= $k['islem_tipi'] == 'Giris' ? '➡️ Giriş' : '⬅️ Çıkış'; ?></td>
        <td><?= $k['islem_zamani']; ?></td>
    </tr>
    <?php endforeach; ?>
</table>
<?php endif; ?>

<hr>

<h3>Devamsızlık Durumu</h3>
<?php if (empty($devamsizliklar)): ?>
<p>Kayıtlı devamsızlık bulunmamaktadır.</p>
<?php else: ?>
<table border="1" cellpadding="5">
    <tr>
        <th>Tarih</th>
        <th>Tür</th>
        <th>Açıklama</th>
    </tr>
    <?php foreach ($devamsizliklar as $d): ?>
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

<hr>

<h3>Yoklama Geçmişi</h3>
<?php if (empty($yoklama_gecmisi)): ?>
<p>Henüz yoklama kaydı bulunmamaktadır.</p>
<?php else: ?>
<table border="1" cellpadding="5">
    <tr>
        <th>Tarih / Saat</th>
        <th>İşlem</th>
        <th>IP</th>
    </tr>
    <?php foreach ($yoklama_gecmisi as $y): ?>
    <tr>
        <td><?= $y['islem_zamani']; ?></td>
        <td><?= $y['islem_tipi'] == 'Giris' ? '➡️ Giriş' : '⬅️ Çıkış'; ?></td>
        <td><?= htmlspecialchars($y['ip_adresi']); ?></td>
    </tr>
    <?php endforeach; ?>
</table>
<?php endif; ?>

<?php endif; ?>

</body>
</html>
