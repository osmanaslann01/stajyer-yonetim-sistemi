<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Sorumlu Ata - Staj Yönetim Sistemi</title>
</head>
<body>

<h2>Öğrenciye Staj Sorumlusu Ata</h2>

<p><a href="index.php?url=admin/basvurular">&larr; Başvurulara Dön</a></p>

<hr>

<h3>Öğrenci Bilgileri</h3>
<table border="1" cellpadding="5">
    <tr>
        <th>Adı Soyadı:</th>
        <td><?= htmlspecialchars($student['ad'] . ' ' . $student['soyad']); ?></td>
    </tr>
    <tr>
        <th>Öğrenci No:</th>
        <td><?= htmlspecialchars($student['ogrenci_no']); ?></td>
    </tr>
</table>

<br>

<form method="POST" action="index.php?url=admin/sorumluAtaKaydet">
    <input type="hidden" name="basvuru_id" value="<?= $basvuru_id; ?>">

    <label for="sorumlu_id"><strong>Kayıtlı Sorumlular:</strong></label><br>
    <select name="sorumlu_id" id="sorumlu_id" required>
        <option value="">-- Lütfen Sorumlu Seçin --</option>
        <?php foreach ($sorumlular as $sorumlu): ?>
            <option value="<?= $sorumlu['sorumlu_id']; ?>">
                <?= htmlspecialchars(($sorumlu['unvan'] ?? '') . ' ' . $sorumlu['ad'] . ' ' . $sorumlu['soyad'] . ' (' . ($sorumlu['birim'] ?? '-') . ')'); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <br><br>

    <label for="gorev"><strong>Görev / Açıklama (İsteğe Bağlı):</strong></label><br>
    <input type="text" name="gorev" id="gorev" placeholder="Örn: Tez/Proje Danışmanı" style="width: 250px;">
    <br><br>

    <button type="submit">Kaydet</button>
</form>

</body>
</html>
