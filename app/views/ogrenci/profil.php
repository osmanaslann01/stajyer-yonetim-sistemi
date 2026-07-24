<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Profil Düzenle - Öğrenci Paneli</title>
</head>
<body>

<h1>Öğrenci Profilim</h1>

<p><a href="index.php?url=ogrenci/dashboard">&larr; Panele Dön</a></p>

<hr>

<?php if (isset($_SESSION['flash_message'])): ?>
    <div style="background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 10px; margin-bottom: 15px; border-radius: 4px; display: inline-block;">
        <?= $_SESSION['flash_message']; ?>
    </div>
    <?php unset($_SESSION['flash_message']); ?>
<?php endif; ?>

<form method="POST" action="index.php?url=ogrenci/profilKaydet" enctype="multipart/form-data">
    
    <h3>Kimlik Bilgileri</h3>
    <p>
        <label>Ad:</label><br>
        <input type="text" value="<?= htmlspecialchars($profil['ad']); ?>" disabled>
    </p>
    <p>
        <label>Soyad:</label><br>
        <input type="text" value="<?= htmlspecialchars($profil['soyad']); ?>" disabled>
    </p>
    <p>
        <label>Öğrenci No:</label><br>
        <input type="text" value="<?= htmlspecialchars($profil['ogrenci_no']); ?>" disabled>
    </p>
    <p>
        <label>TC Kimlik No:</label><br>
        <input type="text" value="<?= htmlspecialchars($profil['tc_no'] ?? ''); ?>" disabled>
    </p>
    
    <h3>İletişim & Profil Bilgileri </h3>
    <p>
        <label for="email">E-posta:</label><br>
        <input type="email" name="email" id="email" value="<?= htmlspecialchars($profil['email']); ?>" required>
    </p>
    <p>
        <label for="telefon">Telefon:</label><br>
        <input type="text" name="telefon" id="telefon" value="<?= htmlspecialchars($profil['telefon']); ?>">
    </p>
    
    <p>
        <label>Profil Fotoğrafı:</label><br>
        <?php if (!empty($profil['profil_fotografi'])): ?>
            <img src="<?= htmlspecialchars($profil['profil_fotografi']); ?>" alt="Profil Fotoğrafı" style="max-width: 120px; max-height: 120px; border-radius: 50%; margin-bottom: 10px; display: block;">
        <?php else: ?>
            <em style="color: #666; display: block; margin-bottom: 10px;">Profil fotoğrafı yüklenmemiş.</em>
        <?php endif; ?>
        <input type="file" name="profil_foto" accept="image/*">
    </p>
    
    <p>
        <label for="sifre">Yeni Şifre (Değiştirmek istemiyorsanız boş bırakın):</label><br>
        <input type="password" name="sifre" id="sifre" placeholder="Yeni şifrenizi girin">
    </p>
    
    <br>
    <button type="submit">Değişiklikleri Kaydet</button>
<a href="index.php?url=ogrenci/profil">
    <button>Profili Düzenle</button>
</a>
</li>

</form>

<hr>

<h3>Öğrenim Bilgileri</h3>
<p>Fakülte: <?= htmlspecialchars($profil['fakulte']); ?></p>
<p>Bölüm: <?= htmlspecialchars($profil['bolum']); ?></p>
<p>Sınıf: <?= htmlspecialchars($profil['sinif']); ?></p>
<p>Staj Türü: <?= htmlspecialchars($profil['staj_turu']); ?></p>
<p>Adres: <?= htmlspecialchars($profil['adres'] ?? ''); ?></p>

</body>
</html>