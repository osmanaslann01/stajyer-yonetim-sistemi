<?php

class Proje extends Model
{
    public function kaydet($data)
    {
        $sql = "
            INSERT INTO proje
            (
                basvuru_id,
                olusturan_sorumlu_id,
                proje_adi,
                proje_aciklamasi,
                gereksinimler,
                verilis_tarihi,
                teslim_tarihi,
                durum
            )
            VALUES
            (
                :basvuru_id,
                :olusturan_sorumlu_id,
                :proje_adi,
                :proje_aciklamasi,
                :gereksinimler,
                :verilis_tarihi,
                :teslim_tarihi,
                :durum
            )
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':basvuru_id' => $data['basvuru_id'],
            ':olusturan_sorumlu_id' => $data['olusturan_sorumlu_id'],
            ':proje_adi' => $data['proje_adi'],
            ':proje_aciklamasi' => $data['proje_aciklamasi'] ?? '',
            ':gereksinimler' => $data['gereksinimler'] ?? '',
            ':verilis_tarihi' => $data['verilis_tarihi'] ?? date('Y-m-d'),
            ':teslim_tarihi' => $data['teslim_tarihi'] ?? date('Y-m-d'),
            ':durum' => $data['durum'] ?? 'Atandi'
        ]);
        return $this->db->lastInsertId();
    }

    public function basvuruProjeleri($basvuru_id)
    {
        $sql = "
            SELECT p.*, s.unvan, k.ad AS sorumlu_ad, k.soyad AS sorumlu_soyad
            FROM proje p
            LEFT JOIN sorumlu s ON p.olusturan_sorumlu_id = s.sorumlu_id
            LEFT JOIN kullanici k ON s.kullanici_id = k.kullanici_id
            WHERE p.basvuru_id = :basvuru_id
            ORDER BY p.proje_id DESC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':basvuru_id' => $basvuru_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function projeBul($proje_id)
    {
        $sql = "SELECT * FROM proje WHERE proje_id = :proje_id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':proje_id' => $proje_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function durumGuncelle($proje_id, $durum)
    {
        $sql = "UPDATE proje SET durum = :durum WHERE proje_id = :proje_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':durum' => $durum,
            ':proje_id' => $proje_id
        ]);
    }

    public function sorumluProjeleri($sorumlu_id)
    {
        $sql = "
            SELECT 
                p.*,
                o.ogrenci_no,
                k.ad AS ogrenci_ad,
                k.soyad AS ogrenci_soyad
            FROM proje p
            INNER JOIN staj_basvurusu sb ON p.basvuru_id = sb.basvuru_id
            INNER JOIN ogrenci o ON sb.ogrenci_id = o.ogrenci_id
            INNER JOIN kullanici k ON o.kullanici_id = k.kullanici_id
            WHERE p.olusturan_sorumlu_id = :sorumlu_id
            ORDER BY p.proje_id DESC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':sorumlu_id' => $sorumlu_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Geliştirme: Öğrencinin aktif (tamamlanmamış) staj projesi olup olmadığını kontrol eden model metodu.
    public function aktifProjeVarMi($basvuru_id)
    {
        $sql = "SELECT COUNT(*) FROM proje WHERE basvuru_id = :basvuru_id AND durum <> 'Tamamlandi'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':basvuru_id' => $basvuru_id]);
        return $stmt->fetchColumn() > 0;
    }
}
