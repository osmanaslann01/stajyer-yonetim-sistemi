<?php

class Sorumlu extends Model
{
    public function sorumluBulByKullanici($kullanici_id)
    {
        $sql = "SELECT * FROM sorumlu WHERE kullanici_id = :kullanici_id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':kullanici_id' => $kullanici_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function atananOgrenciler($sorumlu_id)
    {
        $sql = "
            SELECT 
                sa.atama_id,
                sa.gorev,
                sa.atama_tarihi,
                sb.basvuru_id,
                sb.staj_turu,
                sb.durum AS basvuru_durum,
                sb.staj_durumu,
                o.ogrenci_id,
                o.ogrenci_no,
                o.fakulte,
                o.bolum,
                o.sinif,
                k.ad,
                k.soyad,
                k.email,
                k.telefon
            FROM sorumlu_atama sa
            INNER JOIN staj_basvurusu sb ON sa.basvuru_id = sb.basvuru_id
            INNER JOIN ogrenci o ON sb.ogrenci_id = o.ogrenci_id
            INNER JOIN kullanici k ON o.kullanici_id = k.kullanici_id
            WHERE sa.sorumlu_id = :sorumlu_id AND sa.aktif = 1
            ORDER BY sb.basvuru_id DESC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':sorumlu_id' => $sorumlu_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function ogrenciDetayGetir($basvuru_id, $sorumlu_id)
    {
        $sql = "
            SELECT 
                sb.basvuru_id,
                sb.staj_turu,
                sb.durum AS basvuru_durum,
                sb.staj_durumu,
                sb.basvuru_tarihi,
                sb.cv_yolu,
                sb.aciklama AS basvuru_aciklamasi,
                o.ogrenci_id,
                o.ogrenci_no,
                o.tc_no,
                o.fakulte,
                o.bolum,
                o.sinif,
                o.dogum_tarihi,
                o.adres,
                k.kullanici_id,
                k.ad,
                k.soyad,
                k.email,
                k.telefon,
                sa.gorev
            FROM sorumlu_atama sa
            INNER JOIN staj_basvurusu sb ON sa.basvuru_id = sb.basvuru_id
            INNER JOIN ogrenci o ON sb.ogrenci_id = o.ogrenci_id
            INNER JOIN kullanici k ON o.kullanici_id = k.kullanici_id
            WHERE sa.basvuru_id = :basvuru_id AND sa.sorumlu_id = :sorumlu_id AND sa.aktif = 1
            LIMIT 1
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':basvuru_id' => $basvuru_id,
            ':sorumlu_id' => $sorumlu_id
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Geliştirme: Sistemdeki tüm aktif staj sorumlularını getiren model metodu.
    public function tumSorumlulariGetir()
    {
        $sql = "
            SELECT s.sorumlu_id, s.unvan, s.birim, k.ad, k.soyad 
            FROM sorumlu s 
            INNER JOIN kullanici k ON s.kullanici_id = k.kullanici_id 
            WHERE k.aktif = 1
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Geliştirme: Başvuru için aktif bir sorumlu ataması olup olmadığını kontrol eden model metodu.
    public function atamaVarMi($basvuru_id)
    {
        $sql = "SELECT COUNT(*) FROM sorumlu_atama WHERE basvuru_id = :basvuru_id AND aktif = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':basvuru_id' => $basvuru_id]);
        return $stmt->fetchColumn() > 0;
    }

    // Geliştirme: Başvuruya yeni bir staj sorumlusu atayan model metodu.
    public function sorumluAta($basvuru_id, $sorumlu_id, $gorev = '')
    {
        $sql = "
            INSERT INTO sorumlu_atama (basvuru_id, sorumlu_id, gorev, atama_tarihi, aktif) 
            VALUES (:basvuru_id, :sorumlu_id, :gorev, NOW(), 1)
        ";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':basvuru_id' => $basvuru_id,
            ':sorumlu_id' => $sorumlu_id,
            ':gorev'      => $gorev
        ]);
    }

    // Geliştirme: Belirli bir staj başvurusu için atanan sorumlu bilgilerini bulan model metodu.
    public function atananSorumluBul($basvuru_id)
    {
        $sql = "
            SELECT sa.*, s.unvan, k.ad, k.soyad 
            FROM sorumlu_atama sa 
            INNER JOIN sorumlu s ON sa.sorumlu_id = s.sorumlu_id 
            INNER JOIN kullanici k ON s.kullanici_id = k.kullanici_id 
            WHERE sa.basvuru_id = :basvuru_id AND sa.aktif = 1 
            LIMIT 1
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':basvuru_id' => $basvuru_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
