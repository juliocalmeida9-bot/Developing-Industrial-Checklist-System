<?php
// ====================================================================
// CheckInd — Controller Base
// Todos os controllers herdam desta classe
// ====================================================================

class Controller
{
    // ------------------------------------------------------------------
    // Renderização de Views (com suporte a layout)
    // ------------------------------------------------------------------

    /**
     * Renderiza uma view, opcionalmente envolvida no layout principal.
     *
     * @param string $view      Caminho relativo à pasta views/ (sem .php)
     * @param array  $data      Variáveis disponíveis na view
     * @param bool   $useLayout Se true, envolve com views/layouts/main.php
     */
    protected function view(string $view, array $data = [], bool $useLayout = true): void
    {
        extract($data, EXTR_SKIP);

        $viewPath = ROOT_PATH . '/views/' . $view . '.php';

        if (!file_exists($viewPath)) {
            die("View não encontrada: {$viewPath}");
        }

        if ($useLayout) {
            // Captura o conteúdo da view
            ob_start();
            require $viewPath;
            $content = ob_get_clean();

            // Renderiza o layout com $content disponível
            $layoutPath = ROOT_PATH . '/views/layouts/main.php';
            require $layoutPath;
        } else {
            // View standalone (ex.: login, páginas de erro)
            require $viewPath;
        }
    }

    // ------------------------------------------------------------------
    // Redirecionamento
    // ------------------------------------------------------------------

    protected function redirect(string $path): void
    {
        $url = BASE_URL . '/' . ltrim($path, '/');
        header("Location: {$url}");
        exit;
    }

    // ------------------------------------------------------------------
    // Autenticação Web (Sessão)
    // ------------------------------------------------------------------

    protected function isAuthenticated(): bool
    {
        return !empty($_SESSION['user_id']);
    }

    protected function requireAuth(): void
    {
        if (!$this->isAuthenticated()) {
            $this->redirect('login');
        }
    }

    // ------------------------------------------------------------------
    // Respostas JSON (para a API)
    // ------------------------------------------------------------------

    protected function json(mixed $data, int $statusCode = 200): never
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');

        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        exit;
    }

    // ------------------------------------------------------------------
    // Autenticação da API (Token Bearer)
    // ------------------------------------------------------------------

    protected function getApiToken(): ?string
    {
        // Tenta pegar do header Authorization
        $headers = function_exists('getallheaders') ? getallheaders() : [];

        foreach ($headers as $name => $value) {
            if (strtolower($name) === 'authorization') {
                if (preg_match('/Bearer\s+(.+)$/i', $value, $m)) {
                    return trim($m[1]);
                }
            }
        }

        // Fallback: parâmetro GET/POST (para testes)
        return $_GET['token'] ?? $_POST['token'] ?? null;
    }

    /**
     * Valida o token da API e retorna os dados do funcionário.
     * Termina com JSON 401 se inválido.
     */
    protected function requireApiAuth(): array
    {
        $token = $this->getApiToken();

        if (!$token) {
            $this->json(['error' => 'Token de autenticação necessário.'], 401);
        }

        $db   = Database::getConnection();
        $stmt = $db->prepare(
            'SELECT e.* FROM api_tokens t
             JOIN employees e ON t.employee_id = e.id
             WHERE t.token = ? AND e.active = 1'
        );
        $stmt->execute([$token]);
        $employee = $stmt->fetch();

        if (!$employee) {
            $this->json(['error' => 'Token inválido ou expirado.'], 401);
        }

        return $employee;
    }

    // ------------------------------------------------------------------
    // Utilitários
    // ------------------------------------------------------------------

    /** Decodifica o corpo JSON da requisição */
    protected function getJsonBody(): array
    {
        $body = file_get_contents('php://input');
        return json_decode($body ?: '{}', true) ?? [];
    }

    /** Gera ou retorna o token CSRF da sessão */
    protected function csrfToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /** Verifica o token CSRF (aborta com 403 se inválido) */
    protected function verifyCsrf(): void
    {
        $submitted = $_POST['csrf_token'] ?? '';
        $stored    = $_SESSION['csrf_token'] ?? '';

        if (!$stored || !hash_equals($stored, $submitted)) {
            http_response_code(403);
            die('Token de segurança inválido. Por favor, tente novamente.');
        }
    }

    /** Sanitiza string para exibição segura */
    protected function esc(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}
