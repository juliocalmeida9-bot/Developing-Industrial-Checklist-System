<?php
declare(strict_types=1);

// ====================================================================
// CheckInd — Front Controller
// Ponto de entrada único do sistema
// ====================================================================

session_start();

define('ROOT_PATH', __DIR__);

// Carregar configurações e core
require_once ROOT_PATH . '/config/app.php';
require_once ROOT_PATH . '/config/database.php';
require_once ROOT_PATH . '/core/Router.php';
require_once ROOT_PATH . '/core/Controller.php';
require_once ROOT_PATH . '/core/Model.php';

// Carregar todos os Models
require_once ROOT_PATH . '/models/User.php';
require_once ROOT_PATH . '/models/Employee.php';
require_once ROOT_PATH . '/models/Machine.php';
require_once ROOT_PATH . '/models/ChecklistItem.php';
require_once ROOT_PATH . '/models/Log.php';

$router = new Router();

// ====================================================================
// ROTAS WEB (Painel do Gestor)
// ====================================================================

// Raiz → redireciona para login ou dashboard
$router->get('',                      'AuthController',      'redirectToRoot');

// Autenticação
$router->get('login',                 'AuthController',      'loginForm');
$router->post('login',                'AuthController',      'login');
$router->get('logout',                'AuthController',      'logout');

// Dashboard
$router->get('dashboard',             'DashboardController', 'index');

// CRUD Funcionários
$router->get('employees',             'EmployeeController',  'index');
$router->get('employees/create',      'EmployeeController',  'create');
$router->post('employees/create',     'EmployeeController',  'store');
$router->get('employees/edit/{id}',   'EmployeeController',  'edit');
$router->post('employees/edit/{id}',  'EmployeeController',  'update');
$router->get('employees/toggle/{id}', 'EmployeeController',  'toggle');

// CRUD Máquinas
$router->get('machines',              'MachineController',   'index');
$router->get('machines/create',       'MachineController',   'create');
$router->post('machines/create',      'MachineController',   'store');
$router->get('machines/edit/{id}',    'MachineController',   'edit');
$router->post('machines/edit/{id}',   'MachineController',   'update');
$router->get('machines/toggle/{id}',  'MachineController',   'toggle');

// Checklist Items
$router->get('checklist',             'ChecklistController', 'index');
$router->post('checklist/create',     'ChecklistController', 'store');
$router->get('checklist/toggle/{id}', 'ChecklistController', 'toggle');
$router->get('checklist/delete/{id}', 'ChecklistController', 'delete');

// Histórico de Logs
$router->get('logs',                  'LogController',       'index');

// ====================================================================
// ROTAS DA API REST (Consumida pelo App Mobile)
// ====================================================================
$router->post('api/auth/login',       'api/AuthApiController',    'login');
$router->post('api/auth/logout',      'api/AuthApiController',    'logout');
$router->get('api/machine/{token}',   'api/MachineApiController', 'show');
$router->post('api/log',              'api/LogApiController',     'store');

// Disparar o roteador
$router->dispatch();
