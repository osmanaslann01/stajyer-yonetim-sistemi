<?php
$passwordResetMesaj = $_SESSION['password_reset_flash'] ?? null;
unset($_SESSION['password_reset_flash']);
?>
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <title>Giriş Yap</title>
</head>

<body>

<?php if ($passwordResetMesaj): ?>
    <p><?= htmlspecialchars($passwordResetMesaj, ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>

<h2>Staj Yönetim Sistemi Giriş</h2>


<form method="POST" action="index.php?url=login">


    <label>Email</label>
    <br>

    <input 
        type="email" 
        name="email"
        required
    >

    <br><br>


    <label>Şifre</label>
    <br>

    <input 
        type="password" 
        name="sifre"
        required
    >

    <br><br>


    <button type="submit">
        Giriş Yap
    </button>


</form>


<br>


<a href="index.php?url=register">
    Kayıt Ol
</a>


<br><br>

<a href="index.php?url=sifremi-unuttum">
    Şifremi Unuttum
</a>

</body>

</html>
