<?php

class ProjeTeslim extends Model
{
    public function kaydet($data)
    {
        $sql = "
            INSERT INTO proje_teslim
            (
                proje_id,
                dosya_adi,
                dosya_yolu,
                aciklama,
                teslim_durumu,
                teslim_tarihi
            )
            VALUES
            (
                :proje_id,
                :dosya_adi,
                :dosya_yolu,
                :aciklama,
                :teslim_durumu,
                NOW()
            )
        ";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':proje_id' => $data['proje_id'],
            ':dosya_adi' => $data['dosya_adi'],
            ':dosya_yolu' => $data['dosya_yolu'],
            ':aciklama' => $data['aciklama'] ?? '',
            ':teslim_durumu' => $data['teslim_durumu'] ?? 'Teslim Edildi'
        ]);
    }

    public function projeTeslimleri($proje_id)
    {
        $sql = "
            SELECT * 
            FROM proje_teslim 
            WHERE proje_id = :proje_id 
            ORDER BY teslim_id DESC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':proje_id' => $proje_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function teslimBul($teslim_id)
    {
        $sql = "SELECT * FROM proje_teslim WHERE teslim_id = :teslim_id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':teslim_id' => $teslim_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function durumGuncelle($teslim_id, $durum, $feedback = null)
    {
        if ($feedback !== null && trim($feedback) !== '') {
            $sql = "
                UPDATE proje_teslim
                SET 
                    teslim_durumu = :durum,
                    aciklama = CONCAT(aciklama, '\n\n[Sorumlu Geri Bildirimi - ', NOW(), ']:\n', :feedback)
                WHERE teslim_id = :teslim_id
            ";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':durum' => $durum,
                ':feedback' => $feedback,
                ':teslim_id' => $teslim_id
            ]);
        } else {
            $sql = "
                UPDATE proje_teslim
                SET 
                    teslim_durumu = :durum
                WHERE teslim_id = :teslim_id
            ";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':durum' => $durum,
                ':teslim_id' => $teslim_id
            ]);
        }
    }
}
