<?php

class StajDonemi extends Model
{


    public function aktifDonemler()
    {

        $sql = "
            SELECT *
            FROM staj_donemi
            WHERE aktif = 1
        ";


        $stmt = $this->db->prepare($sql);


        $stmt->execute();


        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }


}