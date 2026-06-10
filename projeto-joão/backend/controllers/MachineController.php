<?php
class MachineController extends Controller
{
    private Machine $model;

    public function __construct()
    {
        $this->model = new Machine();
    }

    public function index(): void
    {
        $this->requireAuth();
        $machines = $this->model->findAll();

        $this->view('machines/index', [
            'title'    => 'Máquinas — CheckInd',
            'machines' => $machines,
        ]);
    }

    public function create(): void
    {
        $this->requireAuth();
        $this->view('machines/form', [
            'title'   => 'Nova Máquina — CheckInd',
            'machine' => null,
            'error'   => null,
        ]);
    }

    public function store(): void
    {
        $this->requireAuth();
        $this->verifyCsrf();

        $name   = trim($_POST['name'] ?? '');
        $model  = trim($_POST['model'] ?? '');
        $sector = trim($_POST['sector'] ?? '');

        if (!$name) {
            $this->view('machines/form', [
                'title'   => 'Nova Máquina — CheckInd',
                'machine' => $_POST,
                'error'   => 'O nome da máquina é obrigatório.',
            ]);
            return;
        }

        $token = $this->model->generateToken();

        $this->model->create([
            'name'          => $name,
            'model'         => $model,
            'sector'        => $sector,
            'qr_code_token' => $token,
            'active'        => 1,
        ]);

        $_SESSION['flash'] = [
            'type'    => 'success',
            'message' => "Máquina «{$name}» cadastrada com sucesso!",
        ];

        $this->redirect('machines');
    }

    public function edit(int $id): void
    {
        $this->requireAuth();
        $machine = $this->model->findById($id);

        if (!$machine) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Máquina não encontrada.'];
            $this->redirect('machines');
        }

        $this->view('machines/form', [
            'title'   => 'Editar Máquina — CheckInd',
            'machine' => $machine,
            'error'   => null,
        ]);
    }

    public function update(int $id): void
    {
        $this->requireAuth();
        $this->verifyCsrf();

        $machine = $this->model->findById($id);
        if (!$machine) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Máquina não encontrada.'];
            $this->redirect('machines');
        }

        $name   = trim($_POST['name'] ?? '');
        $model  = trim($_POST['model'] ?? '');
        $sector = trim($_POST['sector'] ?? '');

        if (!$name) {
            $this->view('machines/form', [
                'title'   => 'Editar Máquina — CheckInd',
                'machine' => array_merge($machine, $_POST),
                'error'   => 'O nome da máquina é obrigatório.',
            ]);
            return;
        }

        // Nunca atualiza o token
        $this->model->update($id, [
            'name'   => $name,
            'model'  => $model,
            'sector' => $sector,
        ]);

        $_SESSION['flash'] = [
            'type'    => 'success',
            'message' => "Máquina «{$name}» atualizada com sucesso!",
        ];

        $this->redirect('machines');
    }

    public function toggle(int $id): void
    {
        $this->requireAuth();
        $machine = $this->model->findById($id);

        if (!$machine) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Máquina não encontrada.'];
            $this->redirect('machines');
        }

        $newStatus = $machine['active'] ? 0 : 1;
        $this->model->update($id, ['active' => $newStatus]);

        $action = $newStatus ? 'ativada' : 'desativada';
        $_SESSION['flash'] = [
            'type'    => $newStatus ? 'success' : 'info',
            'message' => "Máquina «{$machine['name']}» {$action} com sucesso!",
        ];

        $this->redirect('machines');
    }
}
