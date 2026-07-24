<h2>Belge Yönetimi</h2>


<p><a href="index.php?url=admin/dashboard">&larr; Panele Dön</a></p>

<table border="1" cellpadding="8">


<tr>

<th>Öğrenci</th>

<th>Numara</th>

<th>Staj Türü</th>

<th>Belge Türü</th>

<th>Yükleyen</th>

<th>İmza Durumu</th>

<th>Dosya</th>

<th>Durum</th>

<th>İşlem</th>

</tr>



<?php foreach($belgeler as $belge): ?>


<tr>


<td>

<?= $belge['ad']." ".$belge['soyad']; ?>

</td>


<td>

<?= $belge['ogrenci_no']; ?>

</td>


<td>

<?= $belge['staj_turu']; ?>

</td>


<td>

<?= $belge['belge_turu']; ?>

</td>


<td>

<?= $belge['yukleyen_tur'] ?? '-'; ?>

</td>


<td>

<?= $belge['imza_durumu'] ?? '-'; ?>

</td>


<td>


<a
href="/StajYonetimSistemi/<?= $belge['dosya_yolu']; ?>"
target="_blank">

Görüntüle

</a>


</td>


<td>

<?= $belge['onay_durumu']; ?>

</td>


<td>



<?php if($belge['belge_turu']=="Taahhütname"): ?>


<?php if($belge['onay_durumu']=="Bekliyor"): ?>


<a href="index.php?url=admin/belgeDurum&id=<?= $belge['belge_id']; ?>&durum=onay">

<button>

Onayla

</button>

</a>



<a href="index.php?url=admin/belgeDurum&id=<?= $belge['belge_id']; ?>&durum=red">

<button>

Reddet

</button>

</a>


<?php else: ?>

İşlem Tamamlandı


<?php endif; ?>



<?php endif; ?>





<?php if(
$belge['belge_turu']=="Staj Yapmaya Uygundur Yazısı"
||
$belge['belge_turu']=="Sigorta Belgesi"
): ?>


<h4>
İmzalı Belge Yükle
</h4>


<form
method="POST"
action="index.php?url=admin/belgeYukle"
enctype="multipart/form-data">


<input
type="hidden"
name="basvuru_id"
value="<?= $belge['basvuru_id']; ?>">


<input
type="hidden"
name="belge_turu"
value="<?= $belge['belge_turu']; ?>">



<input
type="file"
name="belge"
required>



<button type="submit">

Yükle

</button>


</form>


<?php endif; ?>



</td>


</tr>


<?php endforeach; ?>


</table>