<?php

class OgrenciDegerlendirme extends Model
{
    public function kaydet($data)
    {
        $sql = "
            INSERT INTO ogrenci_degerlendirme
            (
                basvuru_id,
                sorumlu_id,
                puan,
                yorum,
                degerlendirme_tarihi
            )
            VALUES
            (
                :basvuru_id,
                :sorumlu_id,
                :puan,
                :yorum,
                NOW()
            )
            ON DUPLICATE KEY UPDATE
            puan = :puan,
            yorum = :yorum,
            degerlendirme_tarihi = NOW()
        ";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':basvuru_id' => $data['basvuru_id'],
            ':sorumlu_id' => $data['sorumlu_id'],
            ':puan' => $data['puan'],
            ':yorum' => $data['yorum']
        ]);
    }

    public function degerlendirmeGetir($basvuru_id, $sorumlu_id)
    {
        $sql = "
            SELECT * 
            FROM ogrenci_degerlendirme 
            WHERE basvuru_id = :basvuru_id 
            AND sorumlu_id = :sorumlu_id 
            LIMIT 1
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':basvuru_id' => $basvuru_id,
            ':sorumlu_id' => $sorumlu_id
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
