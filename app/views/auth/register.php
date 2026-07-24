<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <title>Öğrenci Kayıt</title>
</head>

<body>

<h2>Öğrenci Kayıt Formu</h2>


<form method="POST" action="index.php?url=register">


    <label>Ad</label><br>
    <input type="text" name="ad" required>

    <br><br>


    <label>Soyad</label><br>
    <input type="text" name="soyad" required>

    <br><br>


    <label>Email</label><br>
    <input type="email" name="email" required>

    <br><br>


    <label>Telefon</label><br>
    <input type="text" name="telefon">

    <br><br>

    <label>Öğrenci No</label>
<br>
<input type="text" name="ogrenci_no" required>

<br><br>


<label>TC No</label>
<br>
<input type="text" name="tc_no" maxlength="11" required>

<br><br>


<label>Fakülte</label>
<br>
<input type="text" name="fakulte" required>

<br><br>


<label>Bölüm</label>
<br>
<input type="text" name="bolum" required>

<br><br>


<label>Sınıf</label>
<br>
<input type="text" name="sinif">

<br><br>


<label>Staj Türü</label>
<br>
<label>Staj Türü</label>
<br>

<select name="staj_turu" required>

    <option value="">Seçiniz</option>

    <option value="Zorunlu Yaz Stajı I">
        Zorunlu Yaz Stajı I
    </option>

    <option value="Zorunlu Yaz Stajı II">
        Zorunlu Yaz Stajı II
    </option>

    <option value="Gönüllü Staj">
        Gönüllü Staj
    </option>

    <option value="İşyeri Eğitimi">
        İşyeri Eğitimi
    </option>

</select>

<br><br>
<br><br>


<label>Adres</label>
<br>
<textarea name="adres"></textarea>

<br><br>


    <label>Şifre</label><br>
    <input type="password" name="sifre" required>

    <br><br>


    <button type="submit">
        Kayıt Ol
    </button>


</form>


<br>


<a href="index.php?url=login">
    Giriş Yap
</a>


</body>

</html>