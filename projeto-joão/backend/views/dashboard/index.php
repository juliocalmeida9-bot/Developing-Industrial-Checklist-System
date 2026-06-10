<?php
/**
 * @var array $stats
 * @var array $recentLogs
 * @var string $title
 */
?>

<!-- Título da Seção -->
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Painel de Monitoramento</h4>
        <p class="text-muted small mb-0">Controle operacional e auditoria em tempo real do chão de fábrica da TechForge.</p>
    </div>
</div>

<!-- ================= STAT CARDS GRID ================= -->
<div class="row g-3 mb-4">
    <!-- Checklists Hoje -->
    <div class="col-12 col-sm-6 col-lg-3 col-xl-2.4" style="flex: 0 0 auto; width: 20%; min-width: 220px;">
        <div class="stat-card theme-primary">
            <div class="stat-info">
                <span class="stat-label">Registros Hoje</span>
                <span class="stat-value"><?= (int) $stats['today_logs'] ?></span>
            </div>
            <div class="stat-icon-wrapper">
                <i class="fa-solid fa-clipboard-check"></i>
            </div>
        </div>
    </div>

    <!-- Operários Ativos -->
    <div class="col-12 col-sm-6 col-lg-3 col-xl-2.4" style="flex: 0 0 auto; width: 20%; min-width: 220px;">
        <div class="stat-card theme-success">
            <div class="stat-info">
                <span class="stat-label">Operários Ativos</span>
                <span class="stat-value"><?= (int) $stats['active_workers'] ?></span>
            </div>
            <div class="stat-icon-wrapper">
                <i class="fa-solid fa-user-gear"></i>
            </div>
        </div>
    </div>

    <!-- Falhas Hoje -->
    <div class="col-12 col-sm-6 col-lg-3 col-xl-2.4" style="flex: 0 0 auto; width: 20%; min-width: 220px;">
        <div class="stat-card theme-danger">
            <div class="stat-info">
                <span class="stat-label">Irregularidades</span>
                <span class="stat-value"><?= (int) $stats['rejected_today'] ?></span>
            </div>
            <div class="stat-icon-wrapper">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
        </div>
    </div>

    <!-- Funcionários Cadastrados -->
    <div class="col-12 col-sm-6 col-lg-3 col-xl-2.4" style="flex: 0 0 auto; width: 20%; min-width: 220px;">
        <div class="stat-card theme-info">
            <div class="stat-info">
                <span class="stat-label">Total Operários</span>
                <span class="stat-value"><?= (int) $stats['total_employees'] ?></span>
            </div>
            <div class="stat-icon-wrapper">
                <i class="fa-solid fa-users"></i>
            </div>
        </div>
    </div>

    <!-- Máquinas Ativas -->
    <div class="col-12 col-sm-6 col-lg-3 col-xl-2.4" style="flex: 0 0 auto; width: 20%; min-width: 220px;">
        <div class="stat-card theme-warning">
            <div class="stat-info">
                <span class="stat-label">Máquinas Ativas</span>
                <span class="stat-value"><?= (int) $stats['total_machines'] ?></span>
            </div>
            <div class="stat-icon-wrapper">
                <i class="fa-solid fa-industry"></i>
            </div>
        </div>
    </div>
</div>

<!-- ================= HISTÓRICO RECENTE ================= -->
<div class="card-premium">
    <div class="card-premium-header">
        <h5 class="card-premium-title">
            <i class="fa-solid fa-clock-rotate-left text-primary"></i>
            Últimas Atividades no Chão de Fábrica
        </h5>
        <a href="<?= BASE_URL ?>/logs" class="btn btn-sm btn-outline-primary" style="font-size: 12px; font-weight: 600; border-radius: var(--radius-sm);">
            Ver Histórico Completo
        </a>
    </div>
    
    <div class="table-responsive">
        <table class="table table-premium align-middle">
            <thead>
                <tr>
                    <th>Data & Hora</th>
                    <th>Operário</th>
                    <th>Matrícula</th>
                    <th>Máquina</th>
                    <th>Setor</th>
                    <th>Operação</th>
                    <th>Status de Segurança</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($recentLogs)): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="fa-solid fa-inbox d-block mb-2 fs-3 text-muted"></i>
                            Nenhuma atividade registrada no momento.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($recentLogs as $log): ?>
                        <tr>
                            <td class="fw-bold" style="color: var(--text-main);">
                                <?= date('d/M/Y H:i:s', strtotime($log['created_at'])) ?>
                            </td>
                            <td>
                                <span class="fw-semibold text-dark"><?= htmlspecialchars($log['employee_name']) ?></span>
                            </td>
                            <td class="text-muted font-monospace">
                                <?= htmlspecialchars($log['employee_matricula']) ?>
                            </td>
                            <td>
                                <span class="badge bg-secondary py-1.5 px-2.5 fw-semibold" style="border-radius: var(--radius-sm); font-size: 11px;">
                                    <i class="fa-solid fa-cube me-1"></i>
                                    <?= htmlspecialchars($log['machine_name']) ?>
                                </span>
                            </td>
                            <td class="text-muted small">
                                <?= htmlspecialchars($log['machine_sector']) ?>
                            </td>
                            <td>
                                <?php if ($log['action_type'] === 'entry'): ?>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1.5 fw-bold" style="font-size: 10px; border-radius: 4px;">
                                        <i class="fa-solid fa-right-to-bracket me-1"></i>
                                        ENTRADA
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2.5 py-1.5 fw-bold" style="font-size: 10px; border-radius: 4px;">
                                        <i class="fa-solid fa-right-from-bracket me-1"></i>
                                        SAÍDA
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($log['status'] === 'approved'): ?>
                                    <span class="badge-status badge-status-approved">
                                        <i class="fa-solid fa-circle-check fs-6"></i>
                                        CONFORME (Acesso Liberado)
                                    </span>
                                <?php else: ?>
                                    <span class="badge-status badge-status-rejected" data-bs-toggle="tooltip" data-bs-placement="top" title="Itens não conformes foram detectados!">
                                        <i class="fa-solid fa-triangle-exclamation fs-6"></i>
                                        IRREGULAR (Acesso Bloqueado)
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
