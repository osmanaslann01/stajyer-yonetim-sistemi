<?php if (isset($_SESSION['flash_message'])): ?>
    <div style="background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 10px; margin-bottom: 15px; border-radius: 4px;">
        <?= $_SESSION['flash_message']; ?>
    </div>
    <?php unset($_SESSION['flash_message']); ?>
<?php endif; ?>

<h2>Staj Başvuruları</h2>

<p><a href="index.php?url=admin/dashboard">&larr; Panele Dön</a></p>
<table border="1">



<tr>

<th>Öğrenci</th>
<th>No</th>
<th>Dönem</th>
<th>Tür</th>
<th>Durum</th>
<th>CV</th>
<th>İşlem</th>

</tr>



<?php foreach($basvurular as $basvuru): ?>


<tr>


<td>
<?= $basvuru['ad']." ".$basvuru['soyad']; ?>
</td>


<td>
<?= $basvuru['ogrenci_no']; ?>
</td>


<td>
<?= $basvuru['donem_adi']; ?>
</td>


<td>
<?= $basvuru['staj_turu']; ?>
</td>


<td>
<?= $basvuru['durum']; ?>
</td>

<td>


<a href="
index.php?url=admin/basvuruDurum&id=<?= $basvuru['basvuru_id']; ?>&durum=Onaylandı
">

<button>
Onayla
</button>

</a>



<a href="
index.php?url=admin/basvuruDurum&id=<?= $basvuru['basvuru_id']; ?>&durum=Reddedildi
">

<button>
Reddet
</button>

</a>


</td>


<td>

<a href="<?= $basvuru['cv_yolu']; ?>" target="_blank">
CV Aç
</a>

</td>


</tr>


<?php endforeach; ?>


</table>

<br>
<hr>
<h3>Başvurusu Onaylanan Öğrenciler</h3>

<table border="1">
<tr>
    <th>Öğrenci</th>
    <th>No</th>
    <th>Dönem</th>
    <th>Tür</th>
    <th>Atanan Sorumlu</th>
    <th>İşlem</th>
</tr>

<?php 
$onaylananlar = array_filter($basvurular, function($b) {
    return $b['durum'] === 'Onaylandı';
});

if (empty($onaylananlar)): 
?>
<tr>
    <td colspan="6" align="center">Başvurusu onaylanmış öğrenci bulunmamaktadır.</td>
</tr>
<?php 
else:
    foreach($onaylananlar as $b): 
?>
<tr>
    <td><?= htmlspecialchars($b['ad']." ".$b['soyad']); ?></td>
    <td><?= htmlspecialchars($b['ogrenci_no']); ?></td>
    <td><?= htmlspecialchars($b['donem_adi']); ?></td>
    <td><?= htmlspecialchars($b['staj_turu']); ?></td>
    <td>
        <?php if (!empty($b['sorumlu_id'])): ?>
            <?= htmlspecialchars(($b['sorumlu_unvan'] ?? '') . ' ' . $b['sorumlu_ad'] . ' ' . $b['sorumlu_soyad']); ?>
        <?php else: ?>
            <em>Sorumlu Atanmadı</em>
        <?php endif; ?>
    </td>
    <td>
        <?php if (empty($b['sorumlu_id'])): ?>
            <a href="index.php?url=admin/sorumluAta&id=<?= $b['basvuru_id']; ?>">
                <button>Sorumlu Ata</button>
            </a>
        <?php else: ?>
            <button disabled>Sorumlu Atandı</button>
        <?php endif; ?>
    </td>
</tr>
<?php 
    endforeach;
endif; 
?>
</table>