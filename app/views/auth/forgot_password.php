<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Şifremi Unuttum</title>
</head>
<body>
    <h2>Şifremi Unuttum</h2>

    <?php if (!empty($mesaj)): ?>
        <p><?= htmlspecialchars($mesaj, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>

    <form method="POST" action="index.php?url=sifremi-unuttum">
        <label for="email">Kayıtlı e-posta adresiniz</label><br>
        <input id="email" type="email" name="email" autocomplete="email" required>
        <br><br>
        <button type="submit">Doğrulama Kodu Oluştur</button>
    </form>

    <br>
    <a href="index.php?url=login">Giriş ekranına dön</a>
</body>
</html>
