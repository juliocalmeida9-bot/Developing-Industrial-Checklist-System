<?php
class AuthController extends Controller
{
    private User $model;

    public function __construct()
    {
        $this->model = new User();
    }

    public function redirectToRoot(): void
    {
        $this->isAuthenticated() ? $this->redirect('dashboard') : $this->redirect('login');
    }

    public function loginForm(): void
    {
        if ($this->isAuthenticated()) {
            $this->redirect('dashboard');
        }
        $this->view('auth/login', ['error' => null], false);
    }

    public function login(): void
    {
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $user     = $this->model->findByEmail($email);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id']    = $user['id'];
            $_SESSION['user_name']  = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $this->redirect('dashboard');
        }

        $this->view('auth/login', ['error' => 'E-mail ou senha inválidos.'], false);
    }

    public function logout(): void
    {
        session_destroy();
        $this->redirect('login');
    }
}
