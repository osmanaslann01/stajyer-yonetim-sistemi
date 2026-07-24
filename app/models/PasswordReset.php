<?php

class PasswordReset extends Model
{
    public const MAX_DENEME_SAYISI = 5;

    public function kullanicininAktifKodlariniGecersizKil(int $kullaniciId): void
    {
        $sql = "UPDATE password_reset SET iptal_edildi_at = NOW() WHERE kullanici_id = :kullanici_id AND kullanildi_at IS NULL AND iptal_edildi_at IS NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':kullanici_id' => $kullaniciId]);
    }

    public function olustur(int $kullaniciId, string $kod): int
    {
        $sql = "INSERT INTO password_reset (kullanici_id, kod_hash, son_gecerlilik_tarihi, deneme_sayisi, created_at) VALUES (:kullanici_id, :kod_hash, DATE_ADD(NOW(), INTERVAL 5 MINUTE), 0, NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':kullanici_id' => $kullaniciId,
            ':kod_hash' => password_hash($kod, PASSWORD_DEFAULT)
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function aktifKayitBul(int $resetId): array|false
    {
        $sql = "SELECT * FROM password_reset WHERE password_reset_id = :password_reset_id AND kullanildi_at IS NULL AND iptal_edildi_at IS NULL AND son_gecerlilik_tarihi >= NOW() LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':password_reset_id' => $resetId]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function kodDogrula(int $resetId, string $kod): string
    {
        $this->db->beginTransaction();

        try {
            $stmt = $this->db->prepare("SELECT * FROM password_reset WHERE password_reset_id = :password_reset_id FOR UPDATE");
            $stmt->execute([':password_reset_id' => $resetId]);
            $reset = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$reset || $reset['kullanildi_at'] || $reset['iptal_edildi_at'] || strtotime($reset['son_gecerlilik_tarihi']) < time()) {
                $this->db->rollBack();
                return 'gecersiz';
            }

            if ((int) $reset['deneme_sayisi'] >= self::MAX_DENEME_SAYISI) {
                $this->koduIptalEt($resetId);
                $this->db->commit();
                return 'kilitli';
            }

            if (!password_verify($kod, $reset['kod_hash'])) {
                $yeniDenemeSayisi = (int) $reset['deneme_sayisi'] + 1;
                $stmt = $this->db->prepare("UPDATE password_reset SET deneme_sayisi = :deneme_sayisi, iptal_edildi_at = CASE WHEN :kilitle THEN NOW() ELSE NULL END WHERE password_reset_id = :password_reset_id");
                $stmt->execute([
                    ':deneme_sayisi' => $yeniDenemeSayisi,
                    ':kilitle' => $yeniDenemeSayisi >= self::MAX_DENEME_SAYISI ? 1 : 0,
                    ':password_reset_id' => $resetId
                ]);
                $this->db->commit();

                return $yeniDenemeSayisi >= self::MAX_DENEME_SAYISI ? 'kilitli' : 'hatali';
            }

            $stmt = $this->db->prepare("UPDATE password_reset SET dogrulandi_at = NOW() WHERE password_reset_id = :password_reset_id");
            $stmt->execute([':password_reset_id' => $resetId]);
            $this->db->commit();

            return 'dogrulandi';
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw $e;
        }
    }

    public function sifreyiSifirla(int $resetId, string $sifreHash): bool
    {
        $this->db->beginTransaction();

        try {
            $stmt = $this->db->prepare("SELECT * FROM password_reset WHERE password_reset_id = :password_reset_id FOR UPDATE");
            $stmt->execute([':password_reset_id' => $resetId]);
            $reset = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$reset || !$reset['dogrulandi_at'] || $reset['kullanildi_at'] || $reset['iptal_edildi_at'] || strtotime($reset['son_gecerlilik_tarihi']) < time()) {
                $this->db->rollBack();
                return false;
            }

            $stmt = $this->db->prepare("UPDATE kullanici SET sifre_hash = :sifre_hash WHERE kullanici_id = :kullanici_id AND aktif = 1");
            $stmt->execute([
                ':sifre_hash' => $sifreHash,
                ':kullanici_id' => $reset['kullanici_id']
            ]);

            if ($stmt->rowCount() !== 1) {
                $this->db->rollBack();
                return false;
            }

            $stmt = $this->db->prepare("UPDATE password_reset SET kullanildi_at = NOW() WHERE password_reset_id = :password_reset_id");
            $stmt->execute([':password_reset_id' => $resetId]);
            $this->db->commit();

            return true;
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw $e;
        }
    }

    public function koduIptalEt(int $resetId): void
    {
        $stmt = $this->db->prepare("UPDATE password_reset SET iptal_edildi_at = NOW() WHERE password_reset_id = :password_reset_id");
        $stmt->execute([':password_reset_id' => $resetId]);
    }
}
