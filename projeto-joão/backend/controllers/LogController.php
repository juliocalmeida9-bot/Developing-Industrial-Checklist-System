<?php
class LogController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();

        $logModel      = new Log();
        $employeeModel = new Employee();
        $machineModel  = new Machine();

        // Filtros via GET
        $filters = [
            'employee_id' => (int) ($_GET['employee_id'] ?? 0),
            'machine_id'  => (int) ($_GET['machine_id'] ?? 0),
            'action_type' => $_GET['action_type'] ?? '',
            'status'      => $_GET['status'] ?? '',
            'date'        => $_GET['date'] ?? '',
        ];

        $logs      = $logModel->findFiltered($filters);
        $employees = $employeeModel->findAll();
        $machines  = $machineModel->findAll();

        $this->view('logs/index', [
            'title'     => 'Histórico de Utilização — CheckInd',
            'logs'      => $logs,
            'employees' => $employees,
            'machines'  => $machines,
            'filters'   => $filters,
        ]);
    }
}
