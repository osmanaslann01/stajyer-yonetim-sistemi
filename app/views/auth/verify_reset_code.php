<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Kod Doğrulama</title>
</head>
<body>
    <h2>Doğrulama Kodunu Girin</h2>

    <?php if (!empty($mesaj)): ?>
        <p><?= htmlspecialchars($mesaj, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>

    <form method="POST" action="index.php?url=sifre-dogrula">
        <label for="kod">6 haneli doğrulama kodu</label><br>
        <input id="kod" type="text" name="kod" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" autocomplete="one-time-code" required>
        <br><br>
        <button type="submit">Kodu Doğrula</button>
    </form>

    <br>
    <a href="index.php?url=sifremi-unuttum">Yeni kod oluştur</a>
    <br>
    <a href="index.php?url=login">Giriş ekranına dön</a>
</body>
</html>
