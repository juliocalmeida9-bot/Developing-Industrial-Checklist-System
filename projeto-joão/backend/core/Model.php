<?php
// ====================================================================
// CheckInd — Model Base (Abstrato)
// Operações CRUD genéricas via PDO
// ====================================================================

abstract class Model
{
    protected PDO    $db;
    protected string $table;
    protected string $primaryKey = 'id';

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    // ------------------------------------------------------------------
    // Leitura
    // ------------------------------------------------------------------

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ? LIMIT 1"
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Busca múltiplos registros com filtros opcionais.
     *
     * @param string $where   Cláusula WHERE (sem a palavra WHERE)
     * @param array  $params  Parâmetros para a cláusula WHERE
     * @param string $orderBy Campo e direção de ordenação (ex.: "name ASC")
     * @param int    $limit   Limite de registros (0 = sem limite)
     * @param int    $offset  Deslocamento
     */
    public function findAll(
        string $where   = '',
        array  $params  = [],
        string $orderBy = '',
        int    $limit   = 0,
        int    $offset  = 0
    ): array {
        $sql = "SELECT * FROM {$this->table}";

        if ($where)   $sql .= " WHERE {$where}";
        if ($orderBy) $sql .= " ORDER BY {$orderBy}";
        if ($limit)   $sql .= " LIMIT {$limit}";
        if ($offset)  $sql .= " OFFSET {$offset}";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function count(string $where = '', array $params = []): int
    {
        $sql = "SELECT COUNT(*) FROM {$this->table}";
        if ($where) $sql .= " WHERE {$where}";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    // ------------------------------------------------------------------
    // Escrita
    // ------------------------------------------------------------------

    public function create(array $data): int
    {
        $columns      = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $stmt = $this->db->prepare(
            "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})"
        );
        $stmt->execute(array_values($data));
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $set  = implode(', ', array_map(fn($col) => "{$col} = ?", array_keys($data)));
        $stmt = $this->db->prepare(
            "UPDATE {$this->table} SET {$set} WHERE {$this->primaryKey} = ?"
        );
        return $stmt->execute([...array_values($data), $id]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare(
            "DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?"
        );
        return $stmt->execute([$id]);
    }
}
