<h2>Belgeler</h2>

<p><a href="index.php?url=ogrenci/dashboard">&larr; Panele Dön</a></p>

<?php if(!$basvuru): ?>

<p>
Henüz onaylanmış bir staj başvurunuz bulunmamaktadır.
</p>


<?php else: ?>


<h3>
<?= $basvuru['staj_turu']; ?> Staj Belgeleri
</h3>


<p>
Başvuru Durumu :
<b>
<?= $basvuru['durum']; ?>
</b>
</p>


<hr>


<h3>Yüklenmesi Gereken Belgeler</h3>


<!-- STAJ YAPMAYA UYGUNDUR YAZISI -->

<h4>
1. Staj Yapmaya Uygundur Yazısı (İmzasız)
</h4>


<form
method="POST"
action="/StajYonetimSistemi/public/index.php?url=ogrenci/belgeYukle"
enctype="multipart/form-data">


<input
type="hidden"
name="basvuru_id"
value="<?= $basvuru['basvuru_id']; ?>">


<input
type="hidden"
name="belge_turu"
value="Staj Yapmaya Uygundur Yazısı">


<input
type="file"
name="belge"
required>


<button type="submit">
Yükle
</button>


</form>


<br>



<!-- SİGORTA BELGESİ -->

<h4>
2. Sigorta Belgesi (İmzasız)
</h4>


<form
method="POST"
action="/StajYonetimSistemi/public/index.php?url=ogrenci/belgeYukle"
enctype="multipart/form-data">


<input
type="hidden"
name="basvuru_id"
value="<?= $basvuru['basvuru_id']; ?>">


<input
type="hidden"
name="belge_turu"
value="Sigorta Belgesi">


<input
type="file"
name="belge"
required>


<button type="submit">
Yükle
</button>


</form>


<br>



<!-- TAAHHÜTNAME -->

<h4>
3. Taahhütname (İmzalı)
</h4>


<p>
Taahhütnameyi Bilgi İşlem Daire Başkanlığı web sitesinden indiriniz,
imzalayınız ve sisteme yükleyiniz.
</p>



<form
method="POST"
action="/StajYonetimSistemi/public/index.php?url=ogrenci/belgeYukle"
enctype="multipart/form-data">


<input
type="hidden"
name="basvuru_id"
value="<?= $basvuru['basvuru_id']; ?>">


<input
type="hidden"
name="belge_turu"
value="Taahhütname">


<input
type="file"
name="belge"
required>


<button type="submit">
Yükle
</button>


</form>



<hr>


<h3>
Yüklenen Belgeler
</h3>



<table border="1" cellpadding="8">


<tr>

<th>
Belge Türü
</th>

<th>
Yükleyen
</th>

<th>
İmza Durumu
</th>

<th>
Onay Durumu
</th>

<th>
Dosya
</th>

<th>
Tarih
</th>

</tr>



<?php if(empty($belgeler)): ?>


<tr>

<td colspan="6">

Henüz belge yüklenmemiştir.

</td>

</tr>


<?php else: ?>



<?php foreach($belgeler as $belge): ?>


<tr>


<td>

<?= htmlspecialchars($belge['belge_turu']); ?>

</td>



<td>

<?= htmlspecialchars($belge['yukleyen_tur'] ?? '-'); ?>

</td>



<td>

<?= htmlspecialchars($belge['imza_durumu'] ?? '-'); ?>

</td>



<td>

<?= htmlspecialchars($belge['onay_durumu'] ?? 'Bekliyor'); ?>

</td>



<td>


<a
href="/StajYonetimSistemi/<?= $belge['dosya_yolu']; ?>"
target="_blank">

Görüntüle

</a>


</td>



<td>

<?= $belge['yuklenme_tarihi'] ?? '-'; ?>

</td>



</tr>


<?php endforeach; ?>


<?php endif; ?>


</table>



<?php endif; ?>