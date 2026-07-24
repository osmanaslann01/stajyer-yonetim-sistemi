<?php

class Sms extends Model
{
    /**
     * Inserts a record into the sms table.
     *
     * @param array $data Contains telefon, mesaj, durum, api_cevabi
     * @return bool
     */
    public function smsKaydet(array $data): bool
    {
        $sql = "
            INSERT INTO sms
            (
                telefon,
                mesaj,
                gonderim_tarihi,
                durum,
                api_cevabi
            )
            VALUES
            (
                :telefon,
                :mesaj,
                NOW(),
                :durum,
                :api_cevabi
            )
        ";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':telefon' => $data['telefon'],
            ':mesaj' => $data['mesaj'],
            ':durum' => $data['durum'] ?? 'TEST',
            ':api_cevabi' => $data['api_cevabi'] ?? null
        ]);
    }

    /**
     * Inserts a record into the sms_log table.
     *
     * @param array $data Contains bildirim_id, telefon, mesaj, durum, servis_cevabi
     * @return bool
     */
    public function logKaydet(array $data): bool
    {
        $sql = "
            INSERT INTO sms_log
            (
                bildirim_id,
                telefon,
                mesaj,
                durum,
                gonderim_tarihi,
                servis_cevabi
            )
            VALUES
            (
                :bildirim_id,
                :telefon,
                :mesaj,
                :durum,
                NOW(),
                :servis_cevabi
            )
        ";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':bildirim_id' => $data['bildirim_id'],
            ':telefon' => $data['telefon'],
            ':mesaj' => $data['mesaj'],
            ':durum' => $data['durum'] ?? 'Bekliyor',
            ':servis_cevabi' => $data['servis_cevabi'] ?? null
        ]);
    }
}
