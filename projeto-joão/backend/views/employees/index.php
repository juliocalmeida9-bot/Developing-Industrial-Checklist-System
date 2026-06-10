<?php
/**
 * @var array $employees
 * @var string $search
 * @var string $title
 */
?>

<!-- Header -->
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Cadastro de Funcionários</h4>
        <p class="text-muted small mb-0">Gerencie as credenciais e permissões dos operários autorizados a realizar os checklists.</p>
    </div>
    <a href="<?= BASE_URL ?>/employees/create" class="btn btn-primary btn-premium btn-premium-primary" style="padding: 10px 20px; font-size: 13px;">
        <i class="fa-solid fa-user-plus me-1"></i>
        Cadastrar Operário
    </a>
</div>

<!-- Filtro de Busca -->
<div class="card-premium mb-4">
    <div class="card-premium-body py-3">
        <form method="GET" action="<?= BASE_URL ?>/employees" class="row g-2 align-items-center">
            <div class="col-12 col-md-8 col-lg-9">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted" style="border-radius: var(--radius-md) 0 0 var(--radius-md);">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                    <input type="text" name="search" class="form-control form-control-premium border-start-0" 
                           placeholder="Buscar por nome, e-mail ou matrícula..." 
                           value="<?= htmlspecialchars($search) ?>"
                           style="border-radius: 0 var(--radius-md) var(--radius-md) 0;">
                </div>
            </div>
            <div class="col-6 col-md-2 col-lg-1.5 d-grid">
                <button type="submit" class="btn btn-premium btn-premium-primary" style="padding: 10px 16px; font-size: 13px;">
                    Filtrar
                </button>
            </div>
            <?php if (!empty($search)): ?>
                <div class="col-6 col-md-2 col-lg-1.5 d-grid">
                    <a href="<?= BASE_URL ?>/employees" class="btn btn-premium btn-premium-secondary" style="padding: 10px 16px; font-size: 13px;">
                        Limpar
                    </a>
                </div>
            <?php endif; ?>
        </form>
    </div>
</div>

<!-- Lista de Funcionários -->
<div class="card-premium">
    <div class="table-responsive">
        <table class="table table-premium align-middle">
            <thead>
                <tr>
                    <th>Operário</th>
                    <th>Matrícula</th>
                    <th>Setor</th>
                    <th>Status no App</th>
                    <th class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($employees)): ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted py-5">
                            <i class="fa-solid fa-user-slash d-block mb-2 fs-2 text-muted"></i>
                            Nenhum funcionário encontrado.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($employees as $emp): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="user-avatar" style="width: 40px; height: 40px; background-color: #f1f5f9; color: var(--primary); font-size: 16px; border: 1px solid var(--border-color);">
                                        <i class="fa-solid fa-user"></i>
                                    </div>
                                    <div class="d-flex flex-direction-column">
                                        <span class="fw-bold text-dark" style="font-size: 14px;"><?= htmlspecialchars($emp['name']) ?></span>
                                        <span class="text-muted small" style="font-size: 12px;"><?= htmlspecialchars($emp['email']) ?></span>
                                    </div>
                                </div>
                            </td>
                            <td class="font-monospace fw-semibold" style="color: var(--text-main);">
                                <?= htmlspecialchars($emp['matricula']) ?>
                            </td>
                            <td>
                                <span class="badge bg-light text-secondary border px-2.5 py-1.5 fw-semibold" style="border-radius: var(--radius-sm); font-size: 11px;">
                                    <i class="fa-solid fa-industry me-1"></i>
                                    <?= htmlspecialchars($emp['sector'] ?: 'Não Definido') ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($emp['active']): ?>
                                    <span class="badge-status badge-status-approved">
                                        <i class="fa-solid fa-circle fa-xs me-1"></i>
                                        Ativo (Acesso Liberado)
                                    </span>
                                <?php else: ?>
                                    <span class="badge-status badge-status-rejected">
                                        <i class="fa-solid fa-circle fa-xs me-1"></i>
                                        Suspenso (Bloqueado)
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-2">
                                    <!-- Editar -->
                                    <a href="<?= BASE_URL ?>/employees/edit/<?= $emp['id'] ?>" 
                                       class="btn btn-sm btn-outline-primary" 
                                       style="padding: 6px 10px; border-radius: var(--radius-sm);" 
                                       title="Editar Cadastro">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    
                                    <!-- Ativar/Suspender -->
                                    <?php if ($emp['active']): ?>
                                        <a href="<?= BASE_URL ?>/employees/toggle/<?= $emp['id'] ?>" 
                                           class="btn btn-sm btn-outline-danger" 
                                           style="padding: 6px 10px; border-radius: var(--radius-sm);" 
                                           title="Suspender Operário"
                                           onclick="return confirm('Deseja realmente suspender o acesso do operário «<?= htmlspecialchars($emp['name']) ?>»?')">
                                            <i class="fa-solid fa-user-minus"></i>
                                        </a>
                                    <?php else: ?>
                                        <a href="<?= BASE_URL ?>/employees/toggle/<?= $emp['id'] ?>" 
                                           class="btn btn-sm btn-outline-success" 
                                           style="padding: 6px 10px; border-radius: var(--radius-sm);" 
                                           title="Ativar Operário">
                                            <i class="fa-solid fa-user-plus"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
