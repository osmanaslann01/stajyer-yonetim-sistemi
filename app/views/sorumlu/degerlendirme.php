<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Değerlendirme - Sorumlu</title>
</head>
<body>

<h1>Öğrenci Performans Değerlendirmesi</h1>

<p><a href="index.php?url=sorumlu/ogrenciDetay&basvuru_id=<?= $detay['basvuru_id']; ?>">&larr; Öğrenci Detayına Dön</a></p>

<hr>

<h2><?= htmlspecialchars($detay['ad'] . ' ' . $detay['soyad']); ?></h2>
<p><?= htmlspecialchars($detay['ogrenci_no']); ?> | <?= htmlspecialchars($detay['bolum']); ?></p>

<hr>

<h3><?= $degerlendirme ? 'Değerlendirmeyi Güncelle' : 'Yeni Değerlendirme'; ?></h3>

<form method="POST" action="index.php?url=sorumlu/degerlendir">
    <input type="hidden" name="basvuru_id" value="<?= $detay['basvuru_id']; ?>">

    <label><strong>Puan (0-100):</strong></label><br>
    <input type="number" name="puan" min="0" max="100" required
        value="<?= $degerlendirme ? $degerlendirme['puan'] : ''; ?>">
    <br><br>

    <label><strong>Yorum:</strong></label><br>
    <textarea name="yorum" rows="5" cols="50" placeholder="Öğrenci hakkında değerlendirme yazınız..."><?= htmlspecialchars($degerlendirme['yorum'] ?? ''); ?></textarea>
    <br><br>

    <button type="submit"><?= $degerlendirme ? 'Değerlendirmeyi Güncelle' : 'Değerlendirmeyi Kaydet'; ?></button>
</form>

<?php if ($degerlendirme): ?>
<hr>
<h3>Mevcut Değerlendirme</h3>
<table border="1" cellpadding="5">
    <tr><th>Puan</th><td><?= $degerlendirme['puan']; ?> / 100</td></tr>
    <tr><th>Yorum</th><td><?= htmlspecialchars($degerlendirme['yorum'] ?? '-'); ?></td></tr>
    <tr><th>Değerlendirme Tarihi</th><td><?= $degerlendirme['degerlendirme_tarihi']; ?></td></tr>
</table>
<?php endif; ?>

</body>
</html>
