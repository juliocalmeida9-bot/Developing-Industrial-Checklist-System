<?php
// ====================================================================
// CheckInd — Roteador (Router)
// Faz o mapeamento URL → Controller::Action
// ====================================================================

class Router
{
    /** @var array<int, array{method:string, path:string, controller:string, action:string}> */
    private array $routes = [];

    public function get(string $path, string $controller, string $action): void
    {
        $this->addRoute('GET', $path, $controller, $action);
    }

    public function post(string $path, string $controller, string $action): void
    {
        $this->addRoute('POST', $path, $controller, $action);
    }

    private function addRoute(string $method, string $path, string $controller, string $action): void
    {
        $this->routes[] = compact('method', 'path', 'controller', 'action');
    }

    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];

        // Captura a URI sem query string
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        if (($pos = strpos($uri, '?')) !== false) {
            $uri = substr($uri, 0, $pos);
        }
        $uri = urldecode($uri);

        // Remove o prefixo BASE_PATH (ex.: /checkind)
        $base = rtrim(BASE_PATH, '/');
        if ($base !== '' && str_starts_with($uri, $base)) {
            $uri = substr($uri, strlen($base));
        }

        $uri = trim($uri, '/');

        // Percorre as rotas registradas
        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            $routePath = trim($route['path'], '/');

            // Converte {param} em grupo de captura regex
            $pattern = preg_replace('/\{[^}]+\}/', '([^/]+)', $routePath);
            $pattern = '#^' . $pattern . '$#u';

            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches); // remove a correspondência completa

                $this->callController($route['controller'], $route['action'], $matches);
                return;
            }
        }

        // Nenhuma rota encontrada — 404
        http_response_code(404);

        if (str_contains($_SERVER['REQUEST_URI'] ?? '', '/api/')) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Rota não encontrada']);
        } else {
            echo '<!DOCTYPE html><html lang="pt-BR"><head><meta charset="UTF-8">
                  <title>404 — CheckInd</title>
                  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
                  </head><body class="bg-dark text-white d-flex align-items-center justify-content-center" style="height:100vh">
                  <div class="text-center"><h1 class="display-1">404</h1>
                  <p class="lead">Página não encontrada.</p>
                  <a href="' . BASE_URL . '" class="btn btn-primary">Voltar ao Início</a>
                  </div></body></html>';
        }
    }

    private function callController(string $controllerPath, string $action, array $params): void
    {
        // Suporta caminhos como "api/AuthApiController"
        $file  = ROOT_PATH . '/controllers/' . str_replace('/', DIRECTORY_SEPARATOR, $controllerPath) . '.php';
        $class = basename($controllerPath);

        if (!file_exists($file)) {
            http_response_code(500);
            die("Controller não encontrado: {$file}");
        }

        require_once $file;

        if (!class_exists($class)) {
            http_response_code(500);
            die("Classe não encontrada: {$class}");
        }

        $controller = new $class();
        call_user_func_array([$controller, $action], $params);
    }
}
