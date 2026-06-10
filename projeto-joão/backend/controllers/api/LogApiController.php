<?php
class LogApiController extends Controller
{
    public function store(): void
    {
        $employee = $this->requireApiAuth();
        $data     = $this->getJsonBody();

        $machineId  = (int) ($data['machine_id']  ?? 0);
        $actionType = $data['action_type'] ?? '';
        $responses  = $data['responses']  ?? [];

        if (!$machineId || !in_array($actionType, ['entry', 'exit'], true)) {
            $this->json(['error' => 'machine_id e action_type são obrigatórios.'], 400);
        }

        if (empty($responses)) {
            $this->json(['error' => 'Respostas do checklist não podem ser vazias.'], 400);
        }

        // Status: 'approved' só se TODAS as respostas forem verdadeiras
        $allOk  = array_reduce($responses, fn($carry, $r) => $carry && (bool) $r['answer'], true);
        $status = $allOk ? 'approved' : 'rejected';

        $logModel = new Log();
        $logId    = $logModel->createWithResponses(
            [
                'employee_id' => (int) $employee['id'],
                'machine_id'  => $machineId,
                'action_type' => $actionType,
                'status'      => $status,
            ],
            $responses
        );

        $actionLabel = $actionType === 'entry' ? 'Entrada' : 'Saída';
        $this->json([
            'success' => true,
            'log_id'  => $logId,
            'status'  => $status,
            'message' => $status === 'approved'
                ? "Checklist de {$actionLabel} aprovado! ✓"
                : "Atenção: Checklist de {$actionLabel} com itens reprovados!",
        ]);
    }
}
