<?php

class SmsService
{
    /**
     * Sends a generic SMS message and logs it.
     *
     * @param string $telefon Target phone number
     * @param string $mesaj SMS Message content
     * @param int|null $kullanici_id The user ID associated with this SMS
     * @return bool
     */
    public static function send(string $telefon, string $mesaj, ?int $kullanici_id = null): bool
    {
        return self::sendToProvider($telefon, $mesaj, $kullanici_id);
    }

    /**
     * Sends an application approved SMS.
     */
    public static function sendApplicationApproved(string $telefon, ?int $kullanici_id, string $ogrenciAdSoyad): bool
    {
        $mesaj = "Sayın $ogrenciAdSoyad, staj başvurunuz onaylanmıştır.";
        return self::send($telefon, $mesaj, $kullanici_id);
    }

    /**
     * Sends an application rejected SMS.
     */
    public static function sendApplicationRejected(string $telefon, ?int $kullanici_id, string $ogrenciAdSoyad): bool
    {
        $mesaj = "Sayın $ogrenciAdSoyad, staj başvurunuz reddedilmiştir.";
        return self::send($telefon, $mesaj, $kullanici_id);
    }

    /**
     * Sends a document approved SMS.
     */
    public static function sendDocumentApproved(string $telefon, ?int $kullanici_id, string $ogrenciAdSoyad, string $belgeTuru): bool
    {
        $mesaj = "Sayın $ogrenciAdSoyad, yüklediğiniz '$belgeTuru' belgesi onaylanmıştır.";
        return self::send($telefon, $mesaj, $kullanici_id);
    }

    /**
     * Sends a document rejected SMS.
     */
    public static function sendDocumentRejected(string $telefon, ?int $kullanici_id, string $ogrenciAdSoyad, string $belgeTuru): bool
    {
        $mesaj = "Sayın $ogrenciAdSoyad, yüklediğiniz '$belgeTuru' belgesi reddedilmiştir. Lütfen kontrol edip tekrar yükleyiniz.";
        return self::send($telefon, $mesaj, $kullanici_id);
    }

    /**
     * Sends a leave approved SMS.
     */
    public static function sendLeaveApproved(string $telefon, ?int $kullanici_id, string $ogrenciAdSoyad, string $baslangic, string $bitis): bool
    {
        $mesaj = "Sayın $ogrenciAdSoyad, $baslangic ve $bitis tarihleri arasındaki izin talebiniz onaylanmıştır.";
        return self::send($telefon, $mesaj, $kullanici_id);
    }

    /**
     * Sends a leave rejected SMS.
     */
    public static function sendLeaveRejected(string $telefon, ?int $kullanici_id, string $ogrenciAdSoyad, string $baslangic, string $bitis): bool
    {
        $mesaj = "Sayın $ogrenciAdSoyad, $baslangic ve $bitis tarihleri arasındaki izin talebiniz reddedilmiştir.";
        return self::send($telefon, $mesaj, $kullanici_id);
    }

    /**
     * Sends an attendance warning SMS.
     */
    public static function sendAttendanceWarning(string $telefon, ?int $kullanici_id, string $ogrenciAdSoyad, string $tarih, string $aciklama = ''): bool
    {
        $detay = $aciklama ? " (Açıklama: $aciklama)" : "";
        $mesaj = "Sayın $ogrenciAdSoyad, $tarih tarihi için yoklama kaydınıza izinsiz devamsızlık işlenmiştir.$detay";
        return self::send($telefon, $mesaj, $kullanici_id);
    }

    /**
     * Sends a project reminder SMS.
     */
    public static function sendProjectReminder(string $telefon, ?int $kullanici_id, string $ogrenciAdSoyad, string $projeAdi, string $teslimTarihi): bool
    {
        $mesaj = "Sayın $ogrenciAdSoyad, '$projeAdi' staj projenizin son teslim tarihi $teslimTarihi'dir. Lütfen tesliminizi tamamlayınız.";
        return self::send($telefon, $mesaj, $kullanici_id);
    }

    /**
     * Simulates sending the SMS to the provider and logs to the database.
     *
     * @param string $telefon Target phone number
     * @param string $mesaj SMS Message content
     * @param int|null $kullanici_id User ID
     * @return bool
     */
    private static function sendToProvider(string $telefon, string $mesaj, ?int $kullanici_id = null): bool
    {
        try {
            // Require models manually as required by the MVC structure
            require_once BASE_PATH . '/app/models/Sms.php';
            require_once BASE_PATH . '/app/models/Bildirim.php';

            $smsModel = new Sms();
            
            $bildirim_id = null;
            if ($kullanici_id !== null) {
                // To insert into sms_log, we need a valid bildirim_id due to foreign key constraints.
                // We create a notification of type 'SMS' in the 'bildirim' table first.
                $sql = "
                    INSERT INTO bildirim
                    (
                        kullanici_id,
                        baslik,
                        mesaj,
                        tip,
                        gonderim_tarihi
                    )
                    VALUES
                    (
                        :kullanici_id,
                        :baslik,
                        :mesaj,
                        :tip,
                        NOW()
                    )
                ";
                
                $db = (new Database())->connect();
                $stmt = $db->prepare($sql);
                $stmt->execute([
                    ':kullanici_id' => $kullanici_id,
                    ':baslik' => 'SMS Bilgilendirmesi',
                    ':mesaj' => $mesaj,
                    ':tip' => 'SMS'
                ]);
                $bildirim_id = $db->lastInsertId();
            }

            // Save to 'sms' table
            $smsData = [
                'telefon' => $telefon,
                'mesaj' => $mesaj,
                'durum' => 'TEST',
                'api_cevabi' => 'TEST_PROVIDER_RESPONSE_SUCCESS'
            ];
            $smsModel->smsKaydet($smsData);

            // Save to 'sms_log' table
            if ($bildirim_id !== null && $bildirim_id > 0) {
                $logData = [
                    'bildirim_id' => $bildirim_id,
                    'telefon' => $telefon,
                    'mesaj' => $mesaj,
                    'durum' => 'TEST',
                    'servis_cevabi' => 'TEST_PROVIDER_RESPONSE_SUCCESS'
                ];
                $smsModel->logKaydet($logData);
            }

            return true;

        } catch (Exception $e) {
            // Hata oluşursa exception fırlatma. Başarısız SMS kayıtlarını da sms_log tablosuna yaz.
            try {
                require_once BASE_PATH . '/app/models/Sms.php';
                $smsModel = new Sms();

                // Save to 'sms' table as FAILED / TEST
                $smsModel->smsKaydet([
                    'telefon' => $telefon,
                    'mesaj' => $mesaj,
                    'durum' => 'TEST',
                    'api_cevabi' => 'FAILED: ' . $e->getMessage()
                ]);

                // Create error notification to get a valid bildirim_id for sms_log
                $db = (new Database())->connect();
                $bildirim_id = null;
                if ($kullanici_id !== null) {
                    $sql = "INSERT INTO bildirim (kullanici_id, baslik, mesaj, tip, gonderim_tarihi) VALUES (:kullanici_id, 'SMS Hatasi', :mesaj, 'SMS', NOW())";
                    $stmt = $db->prepare($sql);
                    $stmt->execute([
                        ':kullanici_id' => $kullanici_id,
                        ':mesaj' => $mesaj
                    ]);
                    $bildirim_id = $db->lastInsertId();
                }

                if ($bildirim_id !== null && $bildirim_id > 0) {
                    $smsModel->logKaydet([
                        'bildirim_id' => $bildirim_id,
                        'telefon' => $telefon,
                        'mesaj' => $mesaj,
                        'durum' => 'TEST',
                        'servis_cevabi' => 'FAILED: ' . $e->getMessage()
                    ]);
                }
            } catch (Exception $innerEx) {
                // Ignore silent errors during failure logging
            }
            return false;
        }
    }
}
