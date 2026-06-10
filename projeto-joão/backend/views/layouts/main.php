<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'CheckInd') ?> | TechForge Industrial</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?= BASE_URL ?>/public/css/style.css" rel="stylesheet">
</head>
<body>

<div class="d-flex" id="wrapper">

    <!-- ======================== SIDEBAR ======================== -->
    <nav id="sidebar">
        <!-- Logo -->
        <div class="sidebar-header">
            <div class="sidebar-logo">
                <span class="logo-icon">⚙</span>
                <div class="logo-text">
                    <span class="logo-name">CheckInd</span>
                    <span class="logo-sub">TechForge Industrial</span>
                </div>
            </div>
        </div>

        <!-- Navigation Links -->
        <ul class="sidebar-nav">
            <li>
                <a href="<?= BASE_URL ?>/dashboard"
                   class="nav-link <?= (strpos($_SERVER['REQUEST_URI'], '/dashboard') !== false) ? 'active' : '' ?>">
                    <i class="fa-solid fa-gauge-high nav-icon"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="<?= BASE_URL ?>/employees"
                   class="nav-link <?= (strpos($_SERVER['REQUEST_URI'], '/employees') !== false) ? 'active' : '' ?>">
                    <i class="fa-solid fa-users nav-icon"></i>
                    <span>Funcionários</span>
                </a>
            </li>
            <li>
                <a href="<?= BASE_URL ?>/machines"
                   class="nav-link <?= (strpos($_SERVER['REQUEST_URI'], '/machines') !== false) ? 'active' : '' ?>">
                    <i class="fa-solid fa-industry nav-icon"></i>
                    <span>Máquinas</span>
                </a>
            </li>
            <li>
                <a href="<?= BASE_URL ?>/checklist"
                   class="nav-link <?= (strpos($_SERVER['REQUEST_URI'], '/checklist') !== false) ? 'active' : '' ?>">
                    <i class="fa-solid fa-clipboard-check nav-icon"></i>
                    <span>Checklists</span>
                </a>
            </li>
            <li>
                <a href="<?= BASE_URL ?>/logs"
                   class="nav-link <?= (strpos($_SERVER['REQUEST_URI'], '/logs') !== false) ? 'active' : '' ?>">
                    <i class="fa-solid fa-timeline nav-icon"></i>
                    <span>Histórico</span>
                </a>
            </li>
        </ul>

        <!-- Sidebar Footer: User info + Logout -->
        <div class="sidebar-footer">
            <div class="user-info">
                <div class="user-avatar">
                    <i class="fa-solid fa-user-tie"></i>
                </div>
                <div class="user-details">
                    <span class="user-name"><?= htmlspecialchars($_SESSION['user_name'] ?? 'Gestor') ?></span>
                    <span class="user-email"><?= htmlspecialchars($_SESSION['user_email'] ?? '') ?></span>
                </div>
            </div>
            <a href="<?= BASE_URL ?>/logout" class="btn-logout" title="Sair do sistema">
                <i class="fa-solid fa-right-from-bracket"></i>
            </a>
        </div>
    </nav>
    <!-- ===================== END SIDEBAR ===================== -->

    <!-- ===================== MAIN CONTENT ===================== -->
    <div id="content">

        <!-- Topbar -->
        <div class="topbar">
            <div class="topbar-left">
                <button id="sidebarToggle" class="btn btn-topbar" title="Recolher sidebar">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <nav aria-label="breadcrumb" class="d-none d-md-flex align-items-center ms-2">
                    <span class="breadcrumb-current"><?= htmlspecialchars($title ?? 'CheckInd') ?></span>
                </nav>
            </div>
            <div class="topbar-right">
                <span class="badge badge-online">
                    <i class="fa-solid fa-circle fa-xs text-success me-1"></i>
                    Sistema Online
                </span>
                <span class="topbar-clock" id="topbarClock">
                    <i class="fa-regular fa-clock me-1"></i>
                    <span id="clockTime">00:00:00</span>
                </span>
            </div>
        </div>

        <!-- Flash Messages -->
        <?php if (!empty($_SESSION['flash'])): ?>
            <?php $flash = $_SESSION['flash']; unset($_SESSION['flash']); ?>
            <div class="flash-container">
                <div class="alert alert-<?= htmlspecialchars($flash['type']) ?> alert-dismissible fade show flash-alert" role="alert">
                    <?php if ($flash['type'] === 'success'): ?>
                        <i class="fa-solid fa-circle-check me-2"></i>
                    <?php elseif ($flash['type'] === 'danger'): ?>
                        <i class="fa-solid fa-circle-xmark me-2"></i>
                    <?php else: ?>
                        <i class="fa-solid fa-circle-info me-2"></i>
                    <?php endif; ?>
                    <?= htmlspecialchars($flash['message']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                </div>
            </div>
        <?php endif; ?>

        <!-- Page Content -->
        <div class="page-content">
            <?= $content ?>
        </div>

    </div>
    <!-- =================== END MAIN CONTENT =================== -->

</div>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Custom JS -->
<script src="<?= BASE_URL ?>/public/js/main.js"></script>

</body>
</html>
