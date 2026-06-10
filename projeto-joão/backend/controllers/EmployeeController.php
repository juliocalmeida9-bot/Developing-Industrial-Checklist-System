<?php
class EmployeeController extends Controller
{
    private Employee $model;

    public function __construct()
    {
        $this->model = new Employee();
    }

    public function index(): void
    {
        $this->requireAuth();
        $search    = trim($_GET['search'] ?? '');
        $employees = $search
            ? $this->model->search($search)
            : $this->model->findAll();

        $this->view('employees/index', [
            'title'     => 'Funcionários — CheckInd',
            'employees' => $employees,
            'search'    => $search,
        ]);
    }

    public function create(): void
    {
        $this->requireAuth();
        $this->view('employees/form', [
            'title'    => 'Novo Funcionário — CheckInd',
            'employee' => null,
            'error'    => null,
        ]);
    }

    public function store(): void
    {
        $this->requireAuth();
        $this->verifyCsrf();

        $name      = trim($_POST['name'] ?? '');
        $matricula = trim($_POST['matricula'] ?? '');
        $email     = trim($_POST['email'] ?? '');
        $password  = $_POST['password'] ?? '';
        $sector    = trim($_POST['sector'] ?? '');

        // Validação
        if (!$name || !$matricula || !$email || !$password) {
            $this->view('employees/form', [
                'title'    => 'Novo Funcionário — CheckInd',
                'employee' => $_POST,
                'error'    => 'Preencha todos os campos obrigatórios.',
            ]);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->view('employees/form', [
                'title'    => 'Novo Funcionário — CheckInd',
                'employee' => $_POST,
                'error'    => 'E-mail inválido.',
            ]);
            return;
        }

        // Checa duplicatas
        if ($this->model->findByEmail($email)) {
            $this->view('employees/form', [
                'title'    => 'Novo Funcionário — CheckInd',
                'employee' => $_POST,
                'error'    => 'Este e-mail já está cadastrado.',
            ]);
            return;
        }

        if ($this->model->findByMatricula($matricula)) {
            $this->view('employees/form', [
                'title'    => 'Novo Funcionário — CheckInd',
                'employee' => $_POST,
                'error'    => 'Esta matrícula já está cadastrada.',
            ]);
            return;
        }

        $this->model->create([
            'name'      => $name,
            'matricula' => $matricula,
            'email'     => $email,
            'password'  => password_hash($password, PASSWORD_DEFAULT),
            'sector'    => $sector,
            'active'    => 1,
        ]);

        $_SESSION['flash'] = [
            'type'    => 'success',
            'message' => "Funcionário «{$name}» cadastrado com sucesso!",
        ];

        $this->redirect('employees');
    }

    public function edit(int $id): void
    {
        $this->requireAuth();
        $employee = $this->model->findById($id);

        if (!$employee) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Funcionário não encontrado.'];
            $this->redirect('employees');
        }

        $this->view('employees/form', [
            'title'    => 'Editar Funcionário — CheckInd',
            'employee' => $employee,
            'error'    => null,
        ]);
    }

    public function update(int $id): void
    {
        $this->requireAuth();
        $this->verifyCsrf();

        $employee = $this->model->findById($id);
        if (!$employee) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Funcionário não encontrado.'];
            $this->redirect('employees');
        }

        $name     = trim($_POST['name'] ?? '');
        $sector   = trim($_POST['sector'] ?? '');
        $password = $_POST['password'] ?? '';

        if (!$name) {
            $this->view('employees/form', [
                'title'    => 'Editar Funcionário — CheckInd',
                'employee' => array_merge($employee, $_POST),
                'error'    => 'O nome é obrigatório.',
            ]);
            return;
        }

        $data = [
            'name'   => $name,
            'sector' => $sector,
        ];

        if (!empty($password)) {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $this->model->update($id, $data);

        $_SESSION['flash'] = [
            'type'    => 'success',
            'message' => "Funcionário «{$name}» atualizado com sucesso!",
        ];

        $this->redirect('employees');
    }

    public function toggle(int $id): void
    {
        $this->requireAuth();
        $employee = $this->model->findById($id);

        if (!$employee) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Funcionário não encontrado.'];
            $this->redirect('employees');
        }

        $newStatus = $employee['active'] ? 0 : 1;
        $this->model->update($id, ['active' => $newStatus]);

        $action = $newStatus ? 'ativado' : 'desativado';
        $_SESSION['flash'] = [
            'type'    => $newStatus ? 'success' : 'info',
            'message' => "Funcionário «{$employee['name']}» {$action} com sucesso!",
        ];

        $this->redirect('employees');
    }
}
