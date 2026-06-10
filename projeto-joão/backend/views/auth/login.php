<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — CheckInd | TechForge Industrial</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary:    #3b82f6;
            --primary-d:  #2563eb;
            --accent:     #f59e0b;
            --dark:       #0f1117;
            --dark2:      #1a1d29;
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f1117 0%, #1a1d29 50%, #0d1520 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        /* Decorative background elements */
        body::before {
            content: '';
            position: fixed;
            width: 600px;
            height: 600px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(59,130,246,0.08) 0%, transparent 70%);
            top: -150px;
            right: -150px;
            pointer-events: none;
        }

        body::after {
            content: '';
            position: fixed;
            width: 400px;
            height: 400px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(245,158,11,0.06) 0%, transparent 70%);
            bottom: -100px;
            left: -100px;
            pointer-events: none;
        }

        .login-wrapper {
            width: 100%;
            max-width: 440px;
            padding: 1rem;
            position: relative;
            z-index: 10;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.10);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow:
                0 25px 50px rgba(0, 0, 0, 0.5),
                0 0 0 1px rgba(255,255,255,0.04) inset;
        }

        /* Logo */
        .logo-section {
            text-align: center;
            margin-bottom: 2rem;
        }

        .logo-icon {
            font-size: 3rem;
            display: block;
            line-height: 1;
            margin-bottom: 0.5rem;
            filter: drop-shadow(0 0 20px rgba(59,130,246,0.5));
        }

        .logo-title {
            font-size: 2rem;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: -0.5px;
            margin: 0;
        }

        .logo-title span {
            color: var(--primary);
        }

        .logo-subtitle {
            color: rgba(255,255,255,0.4);
            font-size: 0.8rem;
            font-weight: 500;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-top: 0.25rem;
        }

        .divider {
            border: none;
            border-top: 1px solid rgba(255,255,255,0.08);
            margin: 1.5rem 0;
        }

        /* Form */
        .form-label {
            color: rgba(255,255,255,0.7);
            font-size: 0.85rem;
            font-weight: 500;
            margin-bottom: 0.4rem;
        }

        .form-control {
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 10px;
            color: #ffffff;
            padding: 0.75rem 1rem;
            font-size: 0.9rem;
            transition: all 0.2s;
        }

        .form-control:focus {
            background: rgba(255,255,255,0.09);
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(59,130,246,0.2);
            color: #ffffff;
            outline: none;
        }

        .form-control::placeholder {
            color: rgba(255,255,255,0.25);
        }

        .input-group-text {
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.12);
            border-right: none;
            color: rgba(255,255,255,0.4);
            border-radius: 10px 0 0 10px;
        }

        .input-group .form-control {
            border-left: none;
            border-radius: 0 10px 10px 0;
        }

        .input-group:focus-within .input-group-text {
            border-color: var(--primary);
            color: var(--primary);
        }

        /* Botão login */
        .btn-login {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-d) 100%);
            border: none;
            border-radius: 10px;
            color: #ffffff;
            font-weight: 600;
            font-size: 0.95rem;
            padding: 0.8rem;
            width: 100%;
            transition: all 0.2s;
            box-shadow: 0 4px 15px rgba(59,130,246,0.4);
            letter-spacing: 0.3px;
        }

        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(59,130,246,0.5);
            color: #ffffff;
            background: linear-gradient(135deg, #4f8ff7 0%, var(--primary) 100%);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        /* Alert */
        .alert-danger {
            background: rgba(239,68,68,0.12);
            border: 1px solid rgba(239,68,68,0.25);
            color: #fca5a5;
            border-radius: 10px;
            font-size: 0.875rem;
        }

        /* Footer */
        .login-footer {
            text-align: center;
            margin-top: 1.5rem;
            color: rgba(255,255,255,0.2);
            font-size: 0.75rem;
        }

        .login-footer i {
            color: var(--accent);
        }
    </style>
</head>
<body>

<div class="login-wrapper">
    <div class="login-card">

        <!-- Logo -->
        <div class="logo-section">
            <span class="logo-icon">⚙</span>
            <h1 class="logo-title">Check<span>Ind</span></h1>
            <p class="logo-subtitle">TechForge Industrial</p>
        </div>

        <hr class="divider">

        <!-- Erro -->
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger d-flex align-items-center mb-3" role="alert">
                <i class="fa-solid fa-triangle-exclamation me-2"></i>
                <div><?= htmlspecialchars($error) ?></div>
            </div>
        <?php endif; ?>

        <!-- Formulário de Login -->
        <form action="<?= BASE_URL ?>/login" method="POST" autocomplete="off">

            <div class="mb-3">
                <label for="email" class="form-label">
                    <i class="fa-solid fa-envelope me-1"></i> E-mail
                </label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fa-solid fa-at fa-sm"></i>
                    </span>
                    <input
                        type="email"
                        class="form-control"
                        id="email"
                        name="email"
                        placeholder="gestor@empresa.com"
                        value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                        required
                        autofocus
                    >
                </div>
            </div>

            <div class="mb-4">
                <label for="password" class="form-label">
                    <i class="fa-solid fa-lock me-1"></i> Senha
                </label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fa-solid fa-key fa-sm"></i>
                    </span>
                    <input
                        type="password"
                        class="form-control"
                        id="password"
                        name="password"
                        placeholder="••••••••"
                        required
                    >
                </div>
            </div>

            <button type="submit" class="btn-login">
                <i class="fa-solid fa-right-to-bracket me-2"></i>
                Entrar no Sistema
            </button>

        </form>

        <div class="login-footer">
            <i class="fa-solid fa-shield-halved"></i>
            Sistema de Controle de Acesso Industrial &copy; <?= date('Y') ?>
        </div>

    </div>
</div>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
