<h2>Staj Başvuru Sonucu</h2>

<p><a href="index.php?url=ogrenci/dashboard">&larr; Panele Dön</a></p>

<?php if($basvuru): ?>


<table border="1">


<tr>
<th>Staj Dönemi</th>
<td>
<?= $basvuru['donem_adi']; ?>
</td>
</tr>



<tr>
<th>Staj Türü</th>
<td>
<?= $basvuru['staj_turu']; ?>
</td>
</tr>



<tr>
<th>Başvuru Tarihi</th>
<td>
<?= $basvuru['basvuru_tarihi']; ?>
</td>
</tr>



<tr>
<th>Durum</th>
<td>

<?php if($basvuru['durum']=="Beklemede"): ?>

🟡 Beklemede


<?php elseif($basvuru['durum']=="Onaylandı"): ?>

🟢 Onaylandı


<?php else: ?>

🔴 Reddedildi


<?php endif; ?>


</td>
</tr>



<tr>
<th>Açıklama</th>
<td>
<?= $basvuru['aciklama']; ?>
</td>
</tr>



<tr>
<th>CV</th>

<td>

<a href="<?= $basvuru['cv_yolu']; ?>" target="_blank">
CV Görüntüle
</a>

</td>

</tr>


</table>


<?php else: ?>


<p>
Henüz staj başvurunuz bulunmamaktadır.
</p>


<?php endif; ?>