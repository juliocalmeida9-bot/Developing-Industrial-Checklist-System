<?php
class AuthApiController extends Controller
{
    public function login(): void
    {
        // Handle CORS preflight
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            $this->json(['ok' => true]);
        }

        $data     = $this->getJsonBody();
        $login    = trim($data['email'] ?? $data['matricula'] ?? '');
        $password = $data['password'] ?? '';

        if (!$login || !$password) {
            $this->json(['error' => 'E-mail/matrícula e senha são obrigatórios.'], 400);
        }

        $model = new Employee();
        $emp   = $model->findByEmail($login) ?? $model->findByMatricula($login);

        if (!$emp || !password_verify($password, $emp['password'])) {
            $this->json(['error' => 'Credenciais inválidas.'], 401);
        }

        if (!$emp['active']) {
            $this->json(['error' => 'Conta desativada. Contate o gestor.'], 403);
        }

        $token = $model->createApiToken((int) $emp['id']);

        $this->json([
            'success'  => true,
            'token'    => $token,
            'employee' => [
                'id'        => $emp['id'],
                'name'      => $emp['name'],
                'matricula' => $emp['matricula'],
                'sector'    => $emp['sector'],
            ],
        ]);
    }

    public function logout(): void
    {
        $token = $this->getApiToken();
        if ($token) {
            $model = new Employee();
            $model->revokeApiToken($token);
        }
        $this->json(['success' => true]);
    }
}
