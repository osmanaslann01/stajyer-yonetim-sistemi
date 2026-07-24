<?php


class Ogrenci extends Model
{


    public function profilGetir($kullanici_id)
    {

        $sql = "

        SELECT

            k.kullanici_id,
            k.ad,
            k.soyad,
            k.email,
            k.telefon,
            k.profil_fotografi,

            o.ogrenci_id,
            o.ogrenci_no,
            o.tc_no,
            o.fakulte,
            o.bolum,
            o.sinif,
            o.staj_turu,
            o.dogum_tarihi,
            o.adres


        FROM kullanici k

        INNER JOIN ogrenci o

        ON k.kullanici_id = o.kullanici_id


        WHERE k.kullanici_id = :id

        LIMIT 1

        ";


        $stmt = $this->db->prepare($sql);


        $stmt->execute([

            ':id' => $kullanici_id

        ]);


        return $stmt->fetch(PDO::FETCH_ASSOC);


    }

    public function kaydet($data)
{


    $sql = "

    INSERT INTO ogrenci
    (
        kullanici_id,
        ogrenci_no,
        tc_no,
        fakulte,
        bolum,
        sinif,
        staj_turu,
        adres
    )

    VALUES
    (
        :kullanici_id,
        :ogrenci_no,
        :tc_no,
        :fakulte,
        :bolum,
        :sinif,
        :staj_turu,
        :adres
    )

    ";


    $stmt = $this->db->prepare($sql);


    return $stmt->execute([

        ':kullanici_id'=>$data['kullanici_id'],

        ':ogrenci_no'=>$data['ogrenci_no'],

        ':tc_no'=>$data['tc_no'],

        ':fakulte'=>$data['fakulte'],

        ':bolum'=>$data['bolum'],

        ':sinif'=>$data['sinif'],

        ':staj_turu'=>$data['staj_turu'],

        ':adres'=>$data['adres']

    ]);


}


}