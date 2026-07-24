<h2>Staj Başvurusu</h2>

<p><a href="index.php?url=ogrenci/dashboard">&larr; Panele Dön</a></p>

<form method="POST"
enctype="multipart/form-data"
action="index.php?url=ogrenci/basvuruKaydet">



<label>Staj Dönemi</label>

<br>

<select name="donem_id" required>

<option value="">
Seçiniz
</option>


<?php foreach($donemler as $donem): ?>

<option value="<?= $donem['donem_id']; ?>">

<?= $donem['donem_adi']; ?>

</option>

<?php endforeach; ?>


</select>


<br><br>



<label>Staj Türü</label>

<br>

<input 
type="radio"
name="staj_turu"
value="Zorunlu"
required>

Zorunlu


<br>


<input 
type="radio"
name="staj_turu"
value="Gönüllü">

Gönüllü

<br><br>


<label>
CV Yükle
</label>

<br>

<input
type="file"
name="cv"
accept=".pdf,.doc,.docx"
required>

<br><br>



<label>Açıklama</label>

<br>

<textarea 
name="aciklama"
rows="5"
cols="40"
placeholder="Varsa açıklama giriniz">
</textarea>



<br><br>



<button type="submit">

Başvuruyu Gönder

</button>


</form>