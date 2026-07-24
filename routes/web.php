<?php

/*
|--------------------------------------------------------------------------
| STAJ YÖNETİM SİSTEMİ - WEB ROTALARI (ROUTES)
|--------------------------------------------------------------------------
|
| Bu dosya uygulamadaki tüm HTTP istek rotalarını, ilgili Controller 
| sınıflarına ve metotlarına eşleştiren ana rota tanımlama dosyasıdır.
|
*/


/*
|--------------------------------------------------------------------------
| 1. AUTHENTICATION (KİMLİK DOĞRULAMA) ROTALARI
|--------------------------------------------------------------------------
|
| Kullanıcı giriş, kayıt ve oturum kapatma işlemleri.
|
*/

// Kullanıcı giriş formunu görüntüler.
$router->get('login', 'AuthController@login');

// Kullanıcı giriş form verilerini işler ve oturum açar.
$router->post('login', 'AuthController@loginPost');

// Öğrenci kayıt formunu görüntüler.
$router->get('register', 'AuthController@register');

// Öğrenci kayıt form verilerini işler ve yeni öğrenci hesabı oluşturur.
$router->post('register', 'AuthController@registerPost');

// Kullanıcı oturumunu sonlandırır ve giriş sayfasına yönlendirir.
$router->get('logout', 'AuthController@logout');


/*
|--------------------------------------------------------------------------
| 2. ŞİFRE SIFIRLAMA (FORGOT PASSWORD) ROTALARI
|--------------------------------------------------------------------------
|
| SMS/E-posta doğrulama kodlu şifre yenileme süreci rotaları.
|
*/

// Şifre sıfırlama e-posta/telefon talep formunu gösterir.
$router->get('sifremi-unuttum', 'ForgotPasswordController@request');

// Şifre sıfırlama talebini işler ve doğrulama kodu üretir.
$router->post('sifremi-unuttum', 'ForgotPasswordController@requestPost');

// 6 haneli doğrulama kodu girme ekranını görüntüler.
$router->get('sifre-dogrula', 'ForgotPasswordController@verify');

// Girilen doğrulama kodunu kontrol eder.
$router->post('sifre-dogrula', 'ForgotPasswordController@verifyPost');

// Yeni şifre belirleme formunu görüntüler.
$router->get('yeni-sifre', 'ForgotPasswordController@reset');

// Yeni şifreyi kaydederek günceller.
$router->post('yeni-sifre', 'ForgotPasswordController@resetPost');


/*
|--------------------------------------------------------------------------
| 3. ÖĞRENCİ MODÜLÜ ROTALARI
|--------------------------------------------------------------------------
|
| Stajyer öğrencilerin başvuru, takip, belge, yoklama ve izin ekranları.
|
*/

// Öğrenci ana panel (Dashboard) ekranını görüntüler (Onaylı staj başvurusu gerektirir).
$router->get('ogrenci/dashboard', 'OgrenciController@dashboard');

// Öğrenci profil bilgilerini görüntüler.
$router->get('ogrenci/profil', 'OgrenciController@profil');

// Öğrenci yeni staj başvurusu oluşturma formunu görüntüler.
$router->get('ogrenci/basvuru', 'BasvuruController@create');

// Öğrencinin mevcut staj başvuru durumunu (Beklemede/Red/Onay) gösterir.
$router->get('ogrenci/basvuruDurumu', 'BasvuruController@basvuruDurumu');

// Yeni staj başvuru form verilerini ve CV dosyasını kaydeder.
$router->post('ogrenci/basvuruKaydet', 'BasvuruController@store');

// Öğrencinin geçmiş staj başvurularını listeler.
$router->get('ogrenci/basvurularim', 'OgrenciController@basvurularim');

// Öğrencinin staj sonu değerlendirme ve başarı sonucunu görüntüler.
$router->get('ogrenci/sonuc', 'OgrenciController@sonuc');

// Öğrenci belge yükleme ve listeleme ekranını görüntüler.
$router->get('ogrenci/belgeler', 'BelgeController@index');

// Öğrenci staj belgesi (PDF/Görsel) yükleme işlemini gerçekleştirir.
$router->post('ogrenci/belgeYukle', 'BelgeController@upload');

// Öğrenci staj projesi ve teslim takip ekranını görüntüler.
$router->get('ogrenci/proje', 'ProjeController@index');

// Öğrencinin proje teslim dosyasını kaydeder.
$router->post('ogrenci/projeKaydet', 'ProjeController@store');

// Öğrenci günlük yoklama giriş/çıkış ekranını görüntüler.
$router->get('ogrenci/yoklama', 'YoklamaController@index');

// Öğrenci günlük IP kontrollü yoklama giriş/çıkışını kaydeder.
$router->post('ogrenci/yoklamaKaydet', 'YoklamaController@store');

// Öğrenci izin talepleri ve izin formu ekranını görüntüler.
$router->get('ogrenci/izin', 'IzinController@index');

// Öğrencinin yeni mazeret/izin talebini kaydeder.
$router->post('ogrenci/izinTalebi', 'IzinController@store');

// Öğrencinin profil bilgilerini günceller.
$router->post('ogrenci/profilKaydet', 'OgrenciController@profilKaydet');



/*
|--------------------------------------------------------------------------
| 4. ADMIN (YÖNETİCİ) MODÜLÜ ROTALARI
|--------------------------------------------------------------------------
|
| Sistem yöneticisinin başvuru onayları, belge yönetimi, yoklama ve log takibi rotaları.
|
*/

// Admin genel istatistikler ve yönetim paneli ekranını görüntüler.
$router->get('admin/dashboard', 'AdminController@dashboard');

// Tüm staj başvurularının listelendiği yönetim ekranını görüntüler.
$router->get('admin/basvurular', 'AdminController@basvurular');

// Staj başvuru durumunu (Onaylandı/Reddedildi) günceller ve SMS/Bildirim gönderir.
$router->get('admin/basvuruDurum', 'AdminController@basvuruDurum');

// Öğrencilerin yüklediği belgelerin yönetim ekranını görüntüler.
$router->get('admin/belgeler', 'BelgeController@adminIndex');

// Admin tarafından imzalı staj belgesi yükleme işlemini gerçekleştirir.
$router->post('admin/belgeYukle', 'BelgeController@adminUpload');

// Yüklenen staj belgesinin onay/red durumunu günceller.
$router->get('admin/belgeDurum', 'BelgeController@durumGuncelle');

// Admin tüm öğrencilerin günlük yoklama ve devamsızlık listesini görüntüler.
$router->get('admin/yoklama', 'YoklamaController@adminIndex');

// Admin tarafından öğrenciye manuel devamsızlık kaydı ekler.
$router->post('admin/devamsizlikEkle', 'YoklamaController@devamsizlikEkle');

// Öğrencilerden gelen izin taleplerinin listelendiği ekranı görüntüler.
$router->get('admin/izinler', 'IzinController@adminIndex');

// İzin talebini onaylar.
$router->get('admin/izinOnayla', 'IzinController@onayla');

// İzin talebini reddeder.
$router->get('admin/izinReddet', 'IzinController@reddet');

// Sistemdeki tüm işlem loglarını listeler.
$router->get('admin/loglar', 'AdminController@loglar');

// Admin sorumlu atama formunu görüntüler.
$router->get('admin/sorumluAta', 'AdminController@sorumluAta');

// Admin sorumlu atama kaydını gerçekleştirir.
$router->post('admin/sorumluAtaKaydet', 'AdminController@sorumluAtaKaydet');



/*
|--------------------------------------------------------------------------
| 5. SORUMLU (STAJ SORUMLUSU) MODÜLÜ ROTALARI
|--------------------------------------------------------------------------
|
| Kurum staj sorumlusunun öğrenci, proje, değerlendirme ve yoklama takip rotaları.
|
*/

// Staj sorumlusu ana yönetim panelini görüntüler.
$router->get('sorumlu/dashboard', 'SorumluController@dashboard');

// Sorumluya atanan öğrencilerin listesini görüntüler.
$router->get('sorumlu/ogrenciler', 'SorumluController@ogrenciler');

// Sorumluya atanan belirli bir öğrencinin detaylarını, yoklama, proje ve belgelerini görüntüler.
$router->get('sorumlu/ogrenciDetay', 'SorumluController@ogrenciDetay');

// Sorumlu tarafından takip edilen projeler ve öğrenci teslimlerini görüntüler.
$router->get('sorumlu/projeler', 'SorumluController@projeler');

// Öğrencinin gönderdiği proje teslim durumunu (Onay/Revize/Red) günceller.
$router->post('sorumlu/projeGuncelle', 'SorumluController@projeGuncelle');

// Öğrenci performans değerlendirme formunu görüntüler.
$router->get('sorumlu/degerlendirme', 'SorumluController@degerlendirme');

// Öğrenciye puan ve yorum vererek performans değerlendirmesini kaydeder.
$router->post('sorumlu/degerlendir', 'SorumluController@degerlendir');

// Atanan öğrencilerin yoklama ve devamsızlık geçmişini görüntüler.
$router->get('sorumlu/yoklama', 'SorumluController@yoklama');

// Proje teslim süresi gelen öğrenciye SMS hatırlatması gönderir.
$router->get('sorumlu/projeHatirlat', 'ProjeController@hatirlat');

// Sorumlu tarafından öğrenciye staj projesi atanmasını kaydeder.
$router->post('sorumlu/projeAta', 'SorumluController@projeAta');



/*
|--------------------------------------------------------------------------
| 6. BELGE İNDİRME VE GEÇMİŞ ROTALARI
|--------------------------------------------------------------------------
|
| Genel belge indirme ve versiyon/geçmiş sorgulama rotaları.
|
*/

// Güvenli belge indirme işlemini gerçekleştirir.
$router->get('belge/indir', 'BelgeController@download');

// Belge onay ve yükleme geçmişini listeler.
$router->get('belge/gecmis', 'BelgeController@belgeGecmisi');


/*
|--------------------------------------------------------------------------
| 7. BİLDİRİM VE SMS ROTALARI
|--------------------------------------------------------------------------
|
| Kullanıcı sistem içi bildirimleri ve SMS bilgilendirmeleri rotaları.
|
*/

// Kullanıcı bildirim listesini görüntüler.
$router->get('bildirimler', 'BildirimController@index');

// Bildirimi okundu olarak işaretler.
$router->get('bildirimOkundu', 'BildirimController@okundu');
