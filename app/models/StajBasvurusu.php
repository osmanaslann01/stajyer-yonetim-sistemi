<?php

class StajBasvurusu extends Model
{
    public function kaydet($data)
    {
        $sql = "
        INSERT INTO staj_basvurusu
        (
            ogrenci_id,
            donem_id,
            staj_turu,
            basvuru_tarihi,
            durum,
            aciklama,
            cv_yolu
        )
        VALUES
        (
            :ogrenci_id,
            :donem_id,
            :staj_turu,
            NOW(),
            'Beklemede',
            :aciklama,
            :cv_yolu
        )
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':ogrenci_id' => $data['ogrenci_id'],
            ':donem_id'   => $data['donem_id'],
            ':staj_turu'   => $data['staj_turu'],
            ':aciklama'   => $data['aciklama'],
            ':cv_yolu'     => $data['cv_yolu']
        ]);
    }

    public function ogrenciBasvurulari($ogrenci_id)
    {
        $sql = "
        SELECT
            sb.*,
            sd.donem_adi
        FROM staj_basvurusu sb
        INNER JOIN staj_donemi sd
        ON sb.donem_id = sd.donem_id
        WHERE sb.ogrenci_id = :ogrenci_id
        ORDER BY sb.basvuru_id DESC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':ogrenci_id' => $ogrenci_id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function varMi($ogrenci_id, $donem_id)
    {
        $sql = "
        SELECT COUNT(*)
        FROM staj_basvurusu
        WHERE ogrenci_id = :ogrenci_id
        AND donem_id = :donem_id
        AND durum <> 'Reddedildi'
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':ogrenci_id' => $ogrenci_id,
            ':donem_id'   => $donem_id
        ]);

        return $stmt->fetchColumn();
    }

    public function ogrenciSonuc($ogrenci_id)
    {
        $sql = "
        SELECT
            sb.*,
            sd.donem_adi
        FROM staj_basvurusu sb
        INNER JOIN staj_donemi sd
        ON sb.donem_id = sd.donem_id
        WHERE sb.ogrenci_id = :ogrenci_id
        ORDER BY sb.basvuru_id DESC
        LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':ogrenci_id' => $ogrenci_id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function basvuruVarMi($ogrenci_id): bool
    {
        return $this->sonBasvuru($ogrenci_id) !== false;
    }

    public function sonBasvuru($ogrenci_id)
    {
        $sql = "
            SELECT sb.*, sd.donem_adi
            FROM staj_basvurusu sb
            INNER JOIN staj_donemi sd ON sb.donem_id = sd.donem_id
            WHERE sb.ogrenci_id = :ogrenci_id
            ORDER BY sb.basvuru_id DESC
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':ogrenci_id' => $ogrenci_id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function bekleyenBasvuru($ogrenci_id)
    {
        $basvuru = $this->sonBasvuru($ogrenci_id);

        return $basvuru && $basvuru['durum'] === 'Beklemede' ? $basvuru : false;
    }

    public function reddedilenBasvuru($ogrenci_id)
    {
        $basvuru = $this->sonBasvuru($ogrenci_id);

        return $basvuru && $basvuru['durum'] === 'Reddedildi' ? $basvuru : false;
    }

    public function onayliBasvuru($ogrenci_id)
    {
        $basvuru = $this->sonBasvuru($ogrenci_id);

        return $basvuru && $basvuru['durum'] === 'Onaylandı' ? $basvuru : false;
    }

    public function tumBasvurular()
    {
        // Geliştirme: Başvurularla birlikte atanmış sorumlu bilgilerini de çekmek için LEFT JOIN ekledik.
        $sql = "
        SELECT
            sb.*,
            sd.donem_adi,
            o.ogrenci_no,
            k.ad,
            k.soyad,
            sa.atama_id,
            sa.sorumlu_id,
            sk.ad AS sorumlu_ad,
            sk.soyad AS sorumlu_soyad,
            s.unvan AS sorumlu_unvan
        FROM staj_basvurusu sb
        INNER JOIN staj_donemi sd ON sb.donem_id = sd.donem_id
        INNER JOIN ogrenci o ON sb.ogrenci_id = o.ogrenci_id
        INNER JOIN kullanici k ON o.kullanici_id = k.kullanici_id
        LEFT JOIN sorumlu_atama sa ON sb.basvuru_id = sa.basvuru_id AND sa.aktif = 1
        LEFT JOIN sorumlu s ON sa.sorumlu_id = s.sorumlu_id
        LEFT JOIN kullanici sk ON s.kullanici_id = sk.kullanici_id
        ORDER BY sb.basvuru_id DESC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function durumGuncelle($id, $durum)
    {
        if ($durum == "Onaylandı") {
            $sql = "
            UPDATE staj_basvurusu
            SET
                durum = :durum,
                staj_durumu = 'Belgeler Bekleniyor'
            WHERE basvuru_id = :id
            ";
        } else {
            $sql = "
            UPDATE staj_basvurusu
            SET
                durum = :durum
            WHERE basvuru_id = :id
            ";
        }

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':durum' => $durum,
            ':id'    => $id
        ]);
    }

    public function stajDurumuGuncelle($basvuru_id, $durum)
    {
        $sql = "
        UPDATE staj_basvurusu
        SET staj_durumu = :durum
        WHERE basvuru_id = :id
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':durum' => $durum,
            ':id'    => $basvuru_id
        ]);
    }
}
