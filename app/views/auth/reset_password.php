<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Yeni Şifre</title>
</head>
<body>
    <h2>Yeni Şifrenizi Belirleyin</h2>

    <?php if (!empty($mesaj)): ?>
        <p><?= htmlspecialchars($mesaj, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>

    <form method="POST" action="index.php?url=yeni-sifre">
        <label for="sifre">Yeni şifre</label><br>
        <input id="sifre" type="password" name="sifre" minlength="8" autocomplete="new-password" required>
        <br><br>

        <label for="sifre_tekrar">Yeni şifre tekrar</label><br>
        <input id="sifre_tekrar" type="password" name="sifre_tekrar" minlength="8" autocomplete="new-password" required>
        <br><br>

        <button type="submit">Şifremi Güncelle</button>
    </form>
</body>
</html>
