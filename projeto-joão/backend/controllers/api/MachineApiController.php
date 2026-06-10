<?php
class MachineApiController extends Controller
{
    public function show(string $token): void
    {
        $employee = $this->requireApiAuth();
        $machine  = (new Machine())->findByToken($token);

        if (!$machine) {
            $this->json(['error' => 'Máquina não encontrada ou inativa.'], 404);
        }

        // Determina o tipo de checklist:
        // Se o funcionário tem entrada aberta hoje (sem saída correspondente), exibe checklist de SAÍDA
        $db   = Database::getConnection();
        $stmt = $db->prepare("
            SELECT id FROM usage_logs
            WHERE employee_id = ? AND machine_id = ? AND action_type = 'entry'
              AND DATE(created_at) = CURDATE()
              AND id NOT IN (
                  SELECT ul2.id FROM usage_logs ul2
                  WHERE ul2.employee_id = ? AND ul2.machine_id = ?
                    AND ul2.action_type = 'exit' AND DATE(ul2.created_at) = CURDATE()
              )
            ORDER BY created_at DESC LIMIT 1
        ");
        $stmt->execute([$employee['id'], $machine['id'], $employee['id'], $machine['id']]);
        $hasOpenEntry = (bool) $stmt->fetch();

        $checklistType = $hasOpenEntry ? 'exit' : 'entry';
        $items         = (new ChecklistItem())->findByType($checklistType);

        $this->json([
            'success'         => true,
            'machine'         => [
                'id'     => $machine['id'],
                'name'   => $machine['name'],
                'model'  => $machine['model'],
                'sector' => $machine['sector'],
            ],
            'checklist_type'  => $checklistType,
            'checklist_items' => array_map(fn($i) => [
                'id'       => $i['id'],
                'question' => $i['question'],
            ], $items),
        ]);
    }
}
