<?php
class DashboardController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();

        $logModel      = new Log();
        $employeeModel = new Employee();
        $machineModel  = new Machine();

        $stats = [
            'today_logs'      => $logModel->countToday(),
            'active_workers'  => $logModel->countActiveWorkers(),
            'rejected_today'  => $logModel->countRejectedToday(),
            'total_employees' => $employeeModel->count('active = 1'),
            'total_machines'  => $machineModel->count('active = 1'),
        ];

        $recentLogs = $logModel->findWithDetails(10);

        $this->view('dashboard/index', [
            'title'      => 'Dashboard — CheckInd',
            'stats'      => $stats,
            'recentLogs' => $recentLogs,
        ]);
    }
}
