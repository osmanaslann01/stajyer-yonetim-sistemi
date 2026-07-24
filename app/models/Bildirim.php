<?php

class Bildirim extends Model
{
    public function gonder($kullanici_id, $baslik, $mesaj, $tip = 'Sistem')
    {
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
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':kullanici_id' => $kullanici_id,
            ':baslik' => $baslik,
            ':mesaj' => $mesaj,
            ':tip' => $tip
        ]);
    }

    public function okunmamisBildirimleriListele($kullanici_id)
    {
        $sql = "
            SELECT * 
            FROM bildirim 
            WHERE kullanici_id = :kullanici_id 
            AND okunma_tarihi IS NULL
            ORDER BY gonderim_tarihi DESC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':kullanici_id' => $kullanici_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function tumBildirimleriListele($kullanici_id)
    {
        $sql = "
            SELECT * 
            FROM bildirim 
            WHERE kullanici_id = :kullanici_id 
            ORDER BY gonderim_tarihi DESC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':kullanici_id' => $kullanici_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function okunduIsaretle($bildirim_id, $kullanici_id = null)
    {
        if ($kullanici_id !== null) {
            $sql = "
                UPDATE bildirim
                SET okunma_tarihi = NOW()
                WHERE bildirim_id = :bildirim_id
                AND kullanici_id = :kullanici_id
            ";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':bildirim_id' => $bildirim_id,
                ':kullanici_id' => $kullanici_id
            ]);
        } else {
            $sql = "
                UPDATE bildirim
                SET okunma_tarihi = NOW()
                WHERE bildirim_id = :bildirim_id
            ";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':bildirim_id' => $bildirim_id
            ]);
        }
    }
}
