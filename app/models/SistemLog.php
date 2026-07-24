<?php

class SistemLog extends Model
{
    public function logYaz($kullanici_id, $islem, $tablo_adi = null, $kayit_id = null)
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $tarayici = $_SERVER['HTTP_USER_AGENT'] ?? 'CLI';

        $sql = "
            INSERT INTO sistem_log
            (
                kullanici_id,
                islem,
                tablo_adi,
                kayit_id,
                ip,
                tarayici,
                islem_tarihi
            )
            VALUES
            (
                :kullanici_id,
                :islem,
                :tablo_adi,
                :kayit_id,
                :ip,
                :tarayici,
                NOW()
            )
        ";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':kullanici_id' => $kullanici_id,
            ':islem' => $islem,
            ':tablo_adi' => $tablo_adi,
            ':kayit_id' => $kayit_id,
            ':ip' => $ip,
            ':tarayici' => $tarayici
        ]);
    }

    public function tumLoglar()
    {
        $sql = "
            SELECT 
                sl.*,
                k.ad,
                k.soyad,
                k.email
            FROM sistem_log sl
            LEFT JOIN kullanici k ON sl.kullanici_id = k.kullanici_id
            ORDER BY sl.log_id DESC
            LIMIT 500
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
