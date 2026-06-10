<?php
class Log extends Model
{
    protected string $table = 'usage_logs';

    // ------------------------------------------------------------------
    // Consultas enriquecidas com JOIN
    // ------------------------------------------------------------------

    /**
     * Retorna todos os logs com detalhes, suportando filtros.
     * Interface simplificada para o controller.
     */
    public function findFiltered(array $filters = []): array
    {
        return $this->findWithDetails(500, 0, $filters);
    }

    /**
     * Retorna logs com nome do funcionário e da máquina.
     * Suporta filtros opcionais.
     */
    public function findWithDetails(int $limit = 50, int $offset = 0, array $filters = []): array
    {
        $where  = [];
        $params = [];

        if (!empty($filters['employee_id'])) {
            $where[]  = 'ul.employee_id = ?';
            $params[] = $filters['employee_id'];
        }
        if (!empty($filters['machine_id'])) {
            $where[]  = 'ul.machine_id = ?';
            $params[] = $filters['machine_id'];
        }
        if (!empty($filters['action_type'])) {
            $where[]  = 'ul.action_type = ?';
            $params[] = $filters['action_type'];
        }
        if (!empty($filters['status'])) {
            $where[]  = 'ul.status = ?';
            $params[] = $filters['status'];
        }
        if (!empty($filters['date'])) {
            $where[]  = 'DATE(ul.created_at) = ?';
            $params[] = $filters['date'];
        }

        $whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        $sql = "
            SELECT
                ul.id,
                ul.action_type,
                ul.status,
                ul.created_at,
                e.name       AS employee_name,
                e.matricula  AS employee_matricula,
                m.name       AS machine_name,
                m.sector     AS machine_sector
            FROM usage_logs ul
            JOIN employees  e ON ul.employee_id = e.id
            JOIN machines   m ON ul.machine_id  = m.id
            {$whereClause}
            ORDER BY ul.created_at DESC
            LIMIT {$limit} OFFSET {$offset}
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /** Retorna as respostas individuais de um log específico */
    public function getResponses(int $logId): array
    {
        $stmt = $this->db->prepare("
            SELECT cr.answer, ci.question, ci.type
            FROM checklist_responses cr
            JOIN checklist_items ci ON cr.item_id = ci.id
            WHERE cr.log_id = ?
            ORDER BY ci.id ASC
        ");
        $stmt->execute([$logId]);
        return $stmt->fetchAll();
    }

    // ------------------------------------------------------------------
    // Métricas para o Dashboard
    // ------------------------------------------------------------------

    public function countToday(): int
    {
        $stmt = $this->db->query(
            "SELECT COUNT(*) FROM usage_logs WHERE DATE(created_at) = CURDATE()"
        );
        return (int) $stmt->fetchColumn();
    }

    /** Funcionários com entrada registrada hoje, mas ainda sem saída */
    public function countActiveWorkers(): int
    {
        $stmt = $this->db->query("
            SELECT COUNT(DISTINCT employee_id)
            FROM usage_logs
            WHERE action_type = 'entry'
              AND DATE(created_at) = CURDATE()
              AND employee_id NOT IN (
                  SELECT employee_id FROM usage_logs
                  WHERE action_type = 'exit'
                    AND DATE(created_at) = CURDATE()
              )
        ");
        return (int) $stmt->fetchColumn();
    }

    public function countRejectedToday(): int
    {
        $stmt = $this->db->query(
            "SELECT COUNT(*) FROM usage_logs WHERE status = 'rejected' AND DATE(created_at) = CURDATE()"
        );
        return (int) $stmt->fetchColumn();
    }

    // ------------------------------------------------------------------
    // Criação de log com respostas (transação atômica)
    // ------------------------------------------------------------------

    /**
     * Cria um log de uso e insere todas as respostas em uma única transação.
     * Os logs são imutáveis — não há update ou delete nesta tabela.
     *
     * @param array $logData   ['employee_id', 'machine_id', 'action_type', 'status']
     * @param array $responses [['item_id' => int, 'answer' => bool], ...]
     */
    public function createWithResponses(array $logData, array $responses): int
    {
        $this->db->beginTransaction();

        try {
            $logId = $this->create($logData);

            $stmt = $this->db->prepare(
                'INSERT INTO checklist_responses (log_id, item_id, answer) VALUES (?, ?, ?)'
            );

            foreach ($responses as $resp) {
                $stmt->execute([
                    $logId,
                    (int) $resp['item_id'],
                    $resp['answer'] ? 1 : 0,
                ]);
            }

            $this->db->commit();
            return $logId;
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
