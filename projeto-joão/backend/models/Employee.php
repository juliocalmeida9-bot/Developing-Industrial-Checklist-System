<?php
class Employee extends Model
{
    protected string $table = 'employees';

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM employees WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        return $stmt->fetch() ?: null;
    }

    public function findByMatricula(string $matricula): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM employees WHERE matricula = ? LIMIT 1');
        $stmt->execute([$matricula]);
        return $stmt->fetch() ?: null;
    }

    public function findActive(): array
    {
        return $this->findAll('active = 1', [], 'name ASC');
    }

    /**
     * Cria (ou renova) o token de API do funcionário.
     * Invalida o token anterior para segurança.
     */
    public function createApiToken(int $employeeId): string
    {
        // Remove tokens antigos
        $del = $this->db->prepare('DELETE FROM api_tokens WHERE employee_id = ?');
        $del->execute([$employeeId]);

        $token = bin2hex(random_bytes(32)); // 64 chars hexadecimais

        $ins = $this->db->prepare('INSERT INTO api_tokens (employee_id, token) VALUES (?, ?)');
        $ins->execute([$employeeId, $token]);

        return $token;
    }

    public function revokeApiToken(string $token): void
    {
        $stmt = $this->db->prepare('DELETE FROM api_tokens WHERE token = ?');
        $stmt->execute([$token]);
    }
}
