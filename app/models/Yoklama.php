<?php

class Yoklama extends Model
{
    public function kaydet($data)
    {
        $sql = "
            INSERT INTO yoklama
            (
                basvuru_id,
                islem_zamani,
                islem_tipi,
                oturum_tipi,
                ip_adresi,
                cihaz_bilgisi
            )
            VALUES
            (
                :basvuru_id,
                NOW(),
                :islem_tipi,
                :oturum_tipi,
                :ip_adresi,
                :cihaz_bilgisi
            )
        ";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':basvuru_id' => $data['basvuru_id'],
            ':islem_tipi' => $data['islem_tipi'],
            ':oturum_tipi' => $data['oturum_tipi'] ?? 'Normal',
            ':ip_adresi' => $data['ip_adresi'],
            ':cihaz_bilgisi' => $data['cihaz_bilgisi']
        ]);
    }

    public function bugunkuYoklamaKayıtları($basvuru_id)
    {
        $sql = "
            SELECT * 
            FROM yoklama 
            WHERE basvuru_id = :basvuru_id 
            AND DATE(islem_zamani) = CURDATE()
            ORDER BY yoklama_id ASC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':basvuru_id' => $basvuru_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function basvuruYoklamaGecmisi($basvuru_id)
    {
        $sql = "
            SELECT * 
            FROM yoklama 
            WHERE basvuru_id = :basvuru_id 
            ORDER BY islem_zamani DESC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':basvuru_id' => $basvuru_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function yetkiliIpKontrol($ip)
    {
        // Yetkili IP tablosunda aktif kayıt var mı?
        $sqlCount = "SELECT COUNT(*) FROM yetkili_ip WHERE aktif = 1";
        $stmtCount = $this->db->prepare($sqlCount);
        $stmtCount->execute();
        $aktifIpSayisi = $stmtCount->fetchColumn();

        // Eğer hiç aktif yetkili IP tanımlanmamışsa, herkese izin ver
        if ($aktifIpSayisi == 0) {
            return true;
        }

        // Aktif yetkili IP tanımlanmışsa, öğrencinin IP'si listede var mı?
        $sqlCheck = "SELECT COUNT(*) FROM yetkili_ip WHERE ip_adresi = :ip AND aktif = 1";
        $stmtCheck = $this->db->prepare($sqlCheck);
        $stmtCheck->execute([':ip' => $ip]);
        return $stmtCheck->fetchColumn() > 0;
    }
}
