<?php

class Devamsizlik extends Model
{
    public function kaydet($data)
    {
        $sql = "
            INSERT INTO devamsizlik
            (
                basvuru_id,
                tarih,
                devamsizlik_turu,
                aciklama
            )
            VALUES
            (
                :basvuru_id,
                :tarih,
                :devamsizlik_turu,
                :aciklama
            )
            ON DUPLICATE KEY UPDATE
            devamsizlik_turu = :devamsizlik_turu,
            aciklama = :aciklama
        ";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':basvuru_id' => $data['basvuru_id'],
            ':tarih' => $data['tarih'],
            ':devamsizlik_turu' => $data['devamsizlik_turu'],
            ':aciklama' => $data['aciklama']
        ]);
    }

    public function basvuruDevamsizlikListesi($basvuru_id)
    {
        $sql = "
            SELECT * 
            FROM devamsizlik 
            WHERE basvuru_id = :basvuru_id 
            ORDER BY tarih DESC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':basvuru_id' => $basvuru_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function tumDevamsizliklar()
    {
        $sql = "
            SELECT 
                d.*,
                o.ogrenci_no,
                k.ad,
                k.soyad
            FROM devamsizlik d
            INNER JOIN staj_basvurusu sb ON d.basvuru_id = sb.basvuru_id
            INNER JOIN ogrenci o ON sb.ogrenci_id = o.ogrenci_id
            INNER JOIN kullanici k ON o.kullanici_id = k.kullanici_id
            ORDER BY d.tarih DESC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
