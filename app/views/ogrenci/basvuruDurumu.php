<h2>Staj Başvuru Durumu</h2>

<p><strong>Staj Dönemi:</strong> <?= htmlspecialchars($basvuru['donem_adi'], ENT_QUOTES, 'UTF-8'); ?></p>
<p><strong>Durum:</strong> <?= htmlspecialchars($basvuru['durum'], ENT_QUOTES, 'UTF-8'); ?></p>

<?php if ($onayli): ?>

    <p>Başvurunuz onaylandı.</p>
    <p><a href="index.php?url=ogrenci/dashboard">Ana Sayfaya Dön</a></p>

<?php elseif ($reddedildi): ?>

    <p>Başvurunuz reddedildi.</p>
    <p><a href="index.php?url=ogrenci/basvuru">Tekrar Başvur</a></p>

<?php else: ?>

    <p>Başvurunuz incelenmektedir.</p>

<?php endif; ?>

<p><a href="index.php?url=logout">Çıkış Yap</a></p>
