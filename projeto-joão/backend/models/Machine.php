<?php
class Machine extends Model
{
    protected string $table = 'machines';

    public function findByToken(string $token): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM machines WHERE qr_code_token = ? AND active = 1 LIMIT 1'
        );
        $stmt->execute([$token]);
        return $stmt->fetch() ?: null;
    }

    public function findActive(): array
    {
        return $this->findAll('active = 1', [], 'name ASC');
    }

    /** Gera um token hexadecimal único para o QR Code */
    public function generateToken(): string
    {
        do {
            $token = bin2hex(random_bytes(16)); // 32 chars
        } while ($this->findByToken($token)); // garante unicidade

        return $token;
    }
}
