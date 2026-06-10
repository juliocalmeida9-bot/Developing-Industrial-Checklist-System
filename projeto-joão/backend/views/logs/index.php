<?php
/**
 * @var array $logs
 * @var array $employees
 * @var array $machines
 * @var array $filters
 * @var string $title
 */
?>

<!-- Header -->
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Histórico de Atividades</h4>
        <p class="text-muted small mb-0">Auditoria completa de utilização de máquinas, verificação de EPIs e conformidade do chão de fábrica.</p>
    </div>
</div>

<!-- ================= PAINEL DE FILTROS ================= -->
<div class="card-premium mb-4">
    <div class="card-premium-header bg-light">
        <h6 class="card-premium-title font-semibold text-secondary">
            <i class="fa-solid fa-filter text-primary"></i>
            Filtrar Registros
        </h6>
    </div>
    <div class="card-premium-body">
        <form method="GET" action="<?= BASE_URL ?>/logs" class="row g-3">
            
            <!-- Operário -->
            <div class="col-12 col-md-6 col-lg-3">
                <label for="employee_id" class="form-label form-label-premium">Operário</label>
                <select name="employee_id" id="employee_id" class="form-select form-control-premium">
                    <option value="0">-- Todos os Operários --</option>
                    <?php foreach ($employees as $emp): ?>
                        <option value="<?= $emp['id'] ?>" <?= $filters['employee_id'] == $emp['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($emp['name']) ?> (<?= htmlspecialchars($emp['matricula']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Máquina -->
            <div class="col-12 col-md-6 col-lg-3">
                <label for="machine_id" class="form-label form-label-premium">Equipamento</label>
                <select name="machine_id" id="machine_id" class="form-select form-control-premium">
                    <option value="0">-- Todos os Equipamentos --</option>
                    <?php foreach ($machines as $mac): ?>
                        <option value="<?= $mac['id'] ?>" <?= $filters['machine_id'] == $mac['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($mac['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Operação -->
            <div class="col-12 col-md-6 col-lg-2">
                <label for="action_type" class="form-label form-label-premium">Operação</label>
                <select name="action_type" id="action_type" class="form-select form-control-premium">
                    <option value="">-- Todas --</option>
                    <option value="entry" <?= $filters['action_type'] === 'entry' ? 'selected' : '' ?>>Entrada</option>
                    <option value="exit" <?= $filters['action_type'] === 'exit' ? 'selected' : '' ?>>Saída</option>
                </select>
            </div>

            <!-- Status de Segurança -->
            <div class="col-12 col-md-6 col-lg-2">
                <label for="status" class="form-label form-label-premium">Status de Segurança</label>
                <select name="status" id="status" class="form-select form-control-premium">
                    <option value="">-- Todos --</option>
                    <option value="approved" <?= $filters['status'] === 'approved' ? 'selected' : '' ?>>Conforme (Aprovado)</option>
                    <option value="rejected" <?= $filters['status'] === 'rejected' ? 'selected' : '' ?>>Irregular (Reprovado)</option>
                </select>
            </div>

            <!-- Data -->
            <div class="col-12 col-md-6 col-lg-2">
                <label for="date" class="form-label form-label-premium">Data</label>
                <input type="date" name="date" id="date" class="form-control form-control-premium" 
                       value="<?= htmlspecialchars($filters['date']) ?>">
            </div>

            <!-- Ações dos Filtros -->
            <div class="col-12 d-flex justify-content-end gap-2 mt-3 pt-3 border-top">
                <a href="<?= BASE_URL ?>/logs" class="btn btn-premium btn-premium-secondary py-2" style="font-size: 13px;">
                    Limpar Filtros
                </a>
                <button type="submit" class="btn btn-premium btn-premium-primary py-2" style="font-size: 13px;">
                    <i class="fa-solid fa-magnifying-glass me-1"></i>
                    Aplicar Filtros
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ================= TABELA DE LOGS ================= -->
<div class="card-premium">
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
                <?php if (empty($logs)): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <i class="fa-solid fa-folder-open d-block mb-2 fs-2 text-muted"></i>
                            Nenhum registro encontrado com os filtros selecionados.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($logs as $log): ?>
                        <tr>
                            <td class="fw-bold" style="color: var(--text-main);">
                                <?= date('d/m/Y H:i:s', strtotime($log['created_at'])) ?>
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
                                    <span class="badge-status badge-status-rejected" data-bs-toggle="tooltip" data-bs-placement="top" title="Itens não conformes foram informados neste checklist.">
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
