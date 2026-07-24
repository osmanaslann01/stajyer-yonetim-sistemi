<?php


class Belge extends Model
{


    public function kaydet($data)
{


    $sql = "
    INSERT INTO belge
    (
        basvuru_id,
        belge_turu,
        dosya_adi,
        dosya_yolu,
        mime_type,
        dosya_boyutu,
        yukleyen_tur,
        imza_durumu,
        onay_durumu
    )

    VALUES
    (
        :basvuru_id,
        :belge_turu,
        :dosya_adi,
        :dosya_yolu,
        :mime_type,
        :dosya_boyutu,
        :yukleyen_tur,
        :imza_durumu,
        :onay_durumu
    )
    ";



    $stmt = $this->db->prepare($sql);



    return $stmt->execute([


        ':basvuru_id'=>$data['basvuru_id'],


        ':belge_turu'=>$data['belge_turu'],


        ':dosya_adi'=>$data['dosya_adi'],


        ':dosya_yolu'=>$data['dosya_yolu'],


        ':mime_type'=>$data['mime_type'],


        ':dosya_boyutu'=>$data['dosya_boyutu'],


        ':yukleyen_tur'=>$data['yukleyen_tur'],


        ':imza_durumu'=>$data['imza_durumu'],


        ':onay_durumu'=>$data['onay_durumu'] ?? 'Bekliyor'


    ]);


}





    public function listele($basvuru_id)
    {


        $sql="
        SELECT *
        FROM belge
        WHERE basvuru_id=:basvuru_id
        ORDER BY belge_id DESC
        ";



        $stmt=$this->db->prepare($sql);


        $stmt->execute([

            ':basvuru_id'=>$basvuru_id

        ]);



        return $stmt->fetchAll(PDO::FETCH_ASSOC);


    }
    
    public function tumBelgeler()
{

    $sql = "

    SELECT

        b.*,

        k.ad,

        k.soyad,

        o.ogrenci_no,

        sb.staj_turu

    FROM belge b

    INNER JOIN staj_basvurusu sb
    ON b.basvuru_id = sb.basvuru_id

    INNER JOIN ogrenci o
    ON sb.ogrenci_id = o.ogrenci_id

    INNER JOIN kullanici k
    ON o.kullanici_id = k.kullanici_id

    ORDER BY b.belge_id DESC

    ";

    $stmt = $this->db->prepare($sql);

    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);

}
public function belgeDurumGuncelle($belge_id,$durum)
{

    $sql="

    UPDATE belge

    SET onay_durumu=:durum

    WHERE belge_id=:belge_id

    ";

    $stmt=$this->db->prepare($sql);

    return $stmt->execute([

        ':durum'=>$durum,

        ':belge_id'=>$belge_id

    ]);

}

public function onayliBelgeSayisi($basvuru_id)
{

    $sql="
    SELECT COUNT(*) AS toplam
    FROM belge
    WHERE basvuru_id=:id
    AND yukleyen_tur='BilgiIslem'
    AND imza_durumu='Imzali'
    ";

    $stmt=$this->db->prepare($sql);

    $stmt->execute([

        ':id'=>$basvuru_id

    ]);

    return $stmt->fetch(PDO::FETCH_ASSOC)['toplam'];

}

public function belgeVarMi($basvuru_id,$belge_turu,$yukleyen_tur)
{

    $sql="
    SELECT COUNT(*) AS toplam
    FROM belge
    WHERE basvuru_id=:basvuru_id
    AND belge_turu=:belge_turu
    AND yukleyen_tur=:yukleyen_tur
    ";

    $stmt=$this->db->prepare($sql);

    $stmt->execute([

        ':basvuru_id'=>$basvuru_id,

        ':belge_turu'=>$belge_turu,

        ':yukleyen_tur'=>$yukleyen_tur

    ]);

    return $stmt->fetch(PDO::FETCH_ASSOC)['toplam']>0;

}
public function ogrenciBelgeleri($basvuru_id)
{

    $sql="
    SELECT *
    FROM belge
    WHERE basvuru_id=:id
    AND yukleyen_tur='Ogrenci'
    ORDER BY belge_id DESC
    ";

    $stmt=$this->db->prepare($sql);

    $stmt->execute([

        ':id'=>$basvuru_id

    ]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);

}
public function bilgiIslemBelgeleri($basvuru_id)
{

    $sql="
    SELECT *
    FROM belge
    WHERE basvuru_id=:id
    AND yukleyen_tur='BilgiIslem'
    ORDER BY belge_id DESC
    ";

    $stmt=$this->db->prepare($sql);

    $stmt->execute([

        ':id'=>$basvuru_id

    ]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);

}

public function basvuruBelgeleriDetayli($basvuru_id)
{
    $sql = "
        SELECT 
            b.*,
            k.ad,
            k.soyad,
            o.ogrenci_no,
            sb.staj_turu
        FROM belge b
        INNER JOIN staj_basvurusu sb ON b.basvuru_id = sb.basvuru_id
        INNER JOIN ogrenci o ON sb.ogrenci_id = o.ogrenci_id
        INNER JOIN kullanici k ON o.kullanici_id = k.kullanici_id
        WHERE b.basvuru_id = :basvuru_id
        ORDER BY b.belge_id DESC
    ";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([':basvuru_id' => $basvuru_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function belgeBul($belge_id)
{
    $sql = "SELECT * FROM belge WHERE belge_id = :belge_id LIMIT 1";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([':belge_id' => $belge_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

public function belgeGecmisi($basvuru_id, $belge_turu)
{
    $sql = "
        SELECT b.*, k.ad, k.soyad
        FROM belge b
        INNER JOIN staj_basvurusu sb ON b.basvuru_id = sb.basvuru_id
        INNER JOIN ogrenci o ON sb.ogrenci_id = o.ogrenci_id
        INNER JOIN kullanici k ON o.kullanici_id = k.kullanici_id
        WHERE b.basvuru_id = :basvuru_id AND b.belge_turu = :belge_turu
        ORDER BY b.belge_id DESC
    ";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([
        ':basvuru_id' => $basvuru_id,
        ':belge_turu' => $belge_turu
    ]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

}
