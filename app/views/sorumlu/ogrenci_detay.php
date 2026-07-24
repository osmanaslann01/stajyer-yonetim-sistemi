<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Öğrenci Detay - Sorumlu</title>
</head>
<body>

<h1>Öğrenci Detayı</h1>

<p><a href="index.php?url=sorumlu/ogrenciler">&larr; Öğrencilere Dön</a></p>

<hr>

<h2><?= htmlspecialchars($detay['ad'] . ' ' . $detay['soyad']); ?></h2>

<h3>Kişisel Bilgiler</h3>
<table border="1" cellpadding="5">
    <tr><th>Öğrenci No</th><td><?= htmlspecialchars($detay['ogrenci_no']); ?></td></tr>
    <tr><th>Fakülte</th><td><?= htmlspecialchars($detay['fakulte']); ?></td></tr>
    <tr><th>Bölüm</th><td><?= htmlspecialchars($detay['bolum']); ?></td></tr>
    <tr><th>Sınıf</th><td><?= htmlspecialchars($detay['sinif'] ?? '-'); ?></td></tr>
    <tr><th>Staj Türü</th><td><?= htmlspecialchars($detay['staj_turu']); ?></td></tr>
    <tr><th>Staj Durumu</th><td><?= htmlspecialchars($detay['staj_durumu'] ?? '-'); ?></td></tr>
    <tr><th>Email</th><td><?= htmlspecialchars($detay['email']); ?></td></tr>
    <tr><th>Telefon</th><td><?= htmlspecialchars($detay['telefon'] ?? '-'); ?></td></tr>
</table>

<hr>

<h3>Belgeler</h3>
<?php if (empty($belgeler)): ?>
<p>Henüz yüklenmiş belge bulunmamaktadır.</p>
<?php else: ?>
<table border="1" cellpadding="5">
    <tr>
        <th>Belge Türü</th>
        <th>Yükleyen</th>
        <th>İmza Durumu</th>
        <th>Onay Durumu</th>
        <th>Tarih</th>
        <th>İndir</th>
    </tr>
    <?php foreach ($belgeler as $b): ?>
    <tr>
        <td><?= htmlspecialchars($b['belge_turu']); ?></td>
        <td><?= $b['yukleyen_tur']; ?></td>
        <td><?= $b['imza_durumu'] == 'Imzali' ? '✅ İmzalı' : '⬜ İmzasız'; ?></td>
        <td><?= $b['onay_durumu']; ?></td>
        <td><?= $b['yuklenme_tarihi']; ?></td>
        <td><a href="index.php?url=belge/indir&id=<?= $b['belge_id']; ?>">İndir</a></td>
    </tr>
    <?php endforeach; ?>
</table>
<?php endif; ?>

<hr>

<h3>Yoklama Geçmişi (Son 30 Kayıt)</h3>
<?php if (empty($yoklamalar)): ?>
<p>Henüz yoklama kaydı bulunmamaktadır.</p>
<?php else: ?>
<table border="1" cellpadding="5">
    <tr>
        <th>Tarih / Saat</th>
        <th>İşlem Tipi</th>
        <th>IP Adresi</th>
    </tr>
    <?php $counter = 0; ?>
    <?php foreach ($yoklamalar as $y): ?>
    <?php if ($counter++ >= 30) break; ?>
    <tr>
        <td><?= $y['islem_zamani']; ?></td>
        <td><?= $y['islem_tipi'] == 'Giris' ? '➡️ Giriş' : '⬅️ Çıkış'; ?></td>
        <td><?= htmlspecialchars($y['ip_adresi']); ?></td>
    </tr>
    <?php endforeach; ?>
</table>
<?php endif; ?>

<hr>

<h3>Devamsızlık Kayıtları</h3>
<?php if (empty($devamsizliklar)): ?>
<p>Devamsızlık kaydı bulunmamaktadır.</p>
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

<h3>İzin Talepleri</h3>
<?php if (empty($izinler)): ?>
<p>İzin talebi bulunmamaktadır.</p>
<?php else: ?>
<table border="1" cellpadding="5">
    <tr>
        <th>Başlangıç</th>
        <th>Bitiş</th>
        <th>Mazeret</th>
        <th>Durum</th>
    </tr>
    <?php foreach ($izinler as $iz): ?>
    <tr>
        <td><?= $iz['baslangic_tarihi']; ?></td>
        <td><?= $iz['bitis_tarihi']; ?></td>
        <td><?= htmlspecialchars($iz['mazeret']); ?></td>
        <td>
            <?php
            if ($iz['durum'] == 'Beklemede') echo '🟡 Beklemede';
            elseif ($iz['durum'] == 'Onaylandi') echo '🟢 Onaylandı';
            else echo '🔴 Reddedildi';
            ?>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
<?php endif; ?>

<hr>

<h3>Proje Durumu</h3>
<?php if (empty($projeler)): ?>
<p>Henüz proje atanmamıştır.</p>
<?php else: ?>
<?php foreach ($projeler as $proje): ?>
<h4><?= htmlspecialchars($proje['proje_adi']); ?></h4>
<p>Durum: <strong><?= $proje['durum']; ?></strong></p>
<p>Teslim Tarihi: <?= $proje['teslim_tarihi']; ?></p>

<?php if (!empty($proje['teslimler'])): ?>
<p><strong>Teslimler:</strong></p>
<table border="1" cellpadding="5">
    <tr>
        <th>Teslim Tarihi</th>
        <th>Durum</th>
        <th>İndir</th>
    </tr>
    <?php foreach ($proje['teslimler'] as $t): ?>
    <tr>
        <td><?= $t['teslim_tarihi']; ?></td>
        <td><?= $t['teslim_durumu']; ?></td>
        <td><a href="index.php?url=belge/indir&id=<?= $t['teslim_id']; ?>">İndir</a></td>
    </tr>
    <?php endforeach; ?>
</table>
<?php endif; ?>

<?php endforeach; ?>
<?php endif; ?>

<hr>

<h3>Performans Değerlendirmesi</h3>
<?php if ($degerlendirme): ?>
<table border="1" cellpadding="5">
    <tr><th>Puan</th><td><?= $degerlendirme['puan']; ?> / 100</td></tr>
    <tr><th>Yorum</th><td><?= htmlspecialchars($degerlendirme['yorum'] ?? '-'); ?></td></tr>
    <tr><th>Tarih</th><td><?= $degerlendirme['degerlendirme_tarihi']; ?></td></tr>
</table>
<p>
    <a href="index.php?url=sorumlu/degerlendirme&basvuru_id=<?= $detay['basvuru_id']; ?>">
        <button>Değerlendirmeyi Güncelle</button>
    </a>
</p>
<?php else: ?>
<p>Henüz değerlendirme yapılmamıştır.</p>
<a href="index.php?url=sorumlu/degerlendirme&basvuru_id=<?= $detay['basvuru_id']; ?>">
    <button>Değerlendir</button>
</a>
<?php endif; ?>

</body>
</html>
