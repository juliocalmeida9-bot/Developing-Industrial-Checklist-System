<?php
class ChecklistController extends Controller
{
    private ChecklistItem $model;

    public function __construct()
    {
        $this->model = new ChecklistItem();
    }

    public function index(): void
    {
        $this->requireAuth();
        $grouped = $this->model->findAllGrouped();

        $this->view('checklist/index', [
            'title'   => 'Checklists — CheckInd',
            'grouped' => $grouped,
        ]);
    }

    public function store(): void
    {
        $this->requireAuth();
        $this->verifyCsrf();

        $question = trim($_POST['question'] ?? '');
        $type     = $_POST['type'] ?? '';

        if (!$question || !in_array($type, ['entry', 'exit'], true)) {
            $_SESSION['flash'] = [
                'type'    => 'danger',
                'message' => 'Preencha a pergunta e selecione o tipo corretamente.',
            ];
            $this->redirect('checklist');
            return;
        }

        $this->model->create([
            'question' => $question,
            'type'     => $type,
            'active'   => 1,
        ]);

        $typeLabel = $type === 'entry' ? 'Entrada' : 'Saída';
        $_SESSION['flash'] = [
            'type'    => 'success',
            'message' => "Item de checklist de {$typeLabel} adicionado com sucesso!",
        ];

        $this->redirect('checklist');
    }

    public function toggle(int $id): void
    {
        $this->requireAuth();
        $item = $this->model->findById($id);

        if (!$item) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Item não encontrado.'];
            $this->redirect('checklist');
        }

        $newStatus = $item['active'] ? 0 : 1;
        $this->model->update($id, ['active' => $newStatus]);

        $action = $newStatus ? 'ativado' : 'desativado';
        $_SESSION['flash'] = [
            'type'    => $newStatus ? 'success' : 'info',
            'message' => "Item de checklist {$action} com sucesso!",
        ];

        $this->redirect('checklist');
    }

    public function delete(int $id): void
    {
        $this->requireAuth();
        $item = $this->model->findById($id);

        if (!$item) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Item não encontrado.'];
            $this->redirect('checklist');
        }

        $this->model->delete($id);

        $_SESSION['flash'] = [
            'type'    => 'info',
            'message' => 'Item de checklist excluído com sucesso!',
        ];

        $this->redirect('checklist');
    }
}
