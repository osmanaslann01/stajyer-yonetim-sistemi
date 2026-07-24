<?php

class IzinTalebi extends Model
{
    public function kaydet($data)
    {
        $sql = "
            INSERT INTO izin_talebi
            (
                basvuru_id,
                belge_id,
                baslangic_tarihi,
                bitis_tarihi,
                mazeret,
                durum
            )
            VALUES
            (
                :basvuru_id,
                :belge_id,
                :baslangic_tarihi,
                :bitis_tarihi,
                :mazeret,
                'Beklemede'
            )
        ";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':basvuru_id' => $data['basvuru_id'],
            ':belge_id' => $data['belge_id'] ?? null,
            ':baslangic_tarihi' => $data['baslangic_tarihi'],
            ':bitis_tarihi' => $data['bitis_tarihi'],
            ':mazeret' => $data['mazeret']
        ]);
    }

    public function basvuruIzinleri($basvuru_id)
    {
        $sql = "
            SELECT * 
            FROM izin_talebi 
            WHERE basvuru_id = :basvuru_id 
            ORDER BY izin_id DESC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':basvuru_id' => $basvuru_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function tumIzinler()
    {
        $sql = "
            SELECT 
                it.*,
                o.ogrenci_no,
                k.ad,
                k.soyad,
                b.dosya_yolu AS belge_yolu
            FROM izin_talebi it
            INNER JOIN staj_basvurusu sb ON it.basvuru_id = sb.basvuru_id
            INNER JOIN ogrenci o ON sb.ogrenci_id = o.ogrenci_id
            INNER JOIN kullanici k ON o.kullanici_id = k.kullanici_id
            LEFT JOIN belge b ON it.belge_id = b.belge_id
            ORDER BY it.izin_id DESC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function durumGuncelle($izin_id, $durum, $onaylayan_id)
    {
        $sql = "
            UPDATE izin_talebi
            SET 
                durum = :durum,
                onaylayan_id = :onaylayan_id,
                onay_tarihi = NOW()
            WHERE izin_id = :izin_id
        ";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':durum' => $durum,
            ':onaylayan_id' => $onaylayan_id,
            ':izin_id' => $izin_id
        ]);
    }

    public function bul($izin_id)
    {
        $sql = "SELECT * FROM izin_talebi WHERE izin_id = :izin_id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':izin_id' => $izin_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
