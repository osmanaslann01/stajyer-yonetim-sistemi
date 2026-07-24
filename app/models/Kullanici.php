<?php


class Kullanici extends Model
{


    public function kaydet($data)
    {

	

        $sql = "INSERT INTO kullanici
        (
            rol_id,
            ad,
            soyad,
            email,
            telefon,
            sifre_hash
        )
        VALUES
        (
            :rol_id,
            :ad,
            :soyad,
            :email,
            :telefon,
            :sifre_hash
        )";


        $stmt = $this->db->prepare($sql);


       if($stmt->execute([

    ':rol_id' => $data['rol_id'],

    ':ad' => $data['ad'],

    ':soyad' => $data['soyad'],

    ':email' => $data['email'],

    ':telefon' => $data['telefon'],

    ':sifre_hash' => $data['sifre_hash']

]))
{

    return $this->db->lastInsertId();

}


return false;
    }

public function emailBul($email)
{

    $sql = "
        SELECT 
            k.*,
            o.ogrenci_id

        FROM kullanici k

        LEFT JOIN ogrenci o
        ON k.kullanici_id = o.kullanici_id

        WHERE k.email = :email

        LIMIT 1
    ";


    $stmt = $this->db->prepare($sql);


    $stmt->execute([
        ':email'=>$email
    ]);


    return $stmt->fetch(PDO::FETCH_ASSOC);

}

public function telefonBul(string $telefon)
{
    $normalTelefon = $this->telefonNormalizeEt($telefon);

    if ($normalTelefon === '') {
        return false;
    }

    $stmt = $this->db->prepare("SELECT * FROM kullanici WHERE aktif = 1 ORDER BY kullanici_id ASC");
    $stmt->execute();

    while ($user = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if ($this->telefonNormalizeEt((string) $user['telefon']) === $normalTelefon) {
            return $user;
        }
    }

    return false;
}

private function telefonNormalizeEt(string $telefon): string
{
    $telefon = preg_replace('/\D+/', '', $telefon);

    if (strpos($telefon, '90') === 0 && strlen($telefon) === 12) {
        $telefon = substr($telefon, 2);
    }

    if (strpos($telefon, '0') === 0 && strlen($telefon) === 11) {
        $telefon = substr($telefon, 1);
    }

    return $telefon;
}

public function sifreGuncelle(int $kullaniciId, string $sifreHash): bool
{
    $sql = "UPDATE kullanici SET sifre_hash = :sifre_hash WHERE kullanici_id = :kullanici_id AND aktif = 1";
    $stmt = $this->db->prepare($sql);

    return $stmt->execute([
        ':sifre_hash' => $sifreHash,
        ':kullanici_id' => $kullaniciId
    ]);
}

public function girisYap($email, $sifre)
{

    $sql = "
        SELECT 
            k.*,
            o.ogrenci_id

        FROM kullanici k

        LEFT JOIN ogrenci o
        ON k.kullanici_id = o.kullanici_id

        WHERE k.email = :email

        LIMIT 1
    ";


    $stmt = $this->db->prepare($sql);


    $stmt->execute([
        ':email' => $email
    ]);


    $user = $stmt->fetch(PDO::FETCH_ASSOC);



    if($user)
    {

        if(password_verify($sifre, $user['sifre_hash']))
        {
            return $user;
        }

    }


    return false;

}

    // Geliştirme: Kullanıcı profil bilgilerini (E-posta, Telefon, Profil Fotoğrafı, Şifre) güncelleyen model metodu.
    public function profilGuncelle($kullanici_id, $data)
    {
        $sql = "UPDATE kullanici SET email = :email, telefon = :telefon";
        $params = [
            ':email' => $data['email'],
            ':telefon' => $data['telefon'],
            ':kullanici_id' => $kullanici_id
        ];

        if (isset($data['profil_fotografi'])) {
            $sql .= ", profil_fotografi = :profil_fotografi";
            $params[':profil_fotografi'] = $data['profil_fotografi'];
        }

        if (isset($data['sifre_hash'])) {
            $sql .= ", sifre_hash = :sifre_hash";
            $params[':sifre_hash'] = $data['sifre_hash'];
        }

        $sql .= " WHERE kullanici_id = :kullanici_id AND aktif = 1";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

}
