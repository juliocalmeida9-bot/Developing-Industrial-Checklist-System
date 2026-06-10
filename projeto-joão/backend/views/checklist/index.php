<?php
/**
 * @var array $grouped
 * @var string $title
 */
?>

<!-- Header -->
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Modelos de Checklist</h4>
        <p class="text-muted small mb-0">Crie, ative ou desative as perguntas de segurança e conformidade respondidas pelos operários no aplicativo.</p>
    </div>
</div>

<div class="row g-4">
    <!-- ================= FORMULÁRIO DE CADASTRO ================= -->
    <div class="col-12 col-xl-4">
        <div class="card-premium" style="position: sticky; top: 94px;">
            <div class="card-premium-header">
                <h5 class="card-premium-title">
                    <i class="fa-solid fa-folder-plus text-primary"></i>
                    Nova Pergunta de Segurança
                </h5>
            </div>
            <div class="card-premium-body">
                <form action="<?= BASE_URL ?>/checklist/store" method="POST">
                    <!-- CSRF Token -->
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($this->csrfToken()) ?>">

                    <!-- Pergunta -->
                    <div class="mb-3">
                        <label for="question" class="form-label form-label-premium">Texto da Pergunta / Verificação</label>
                        <textarea name="question" id="question" required rows="3"
                                  class="form-control form-control-premium"
                                  placeholder="Ex: O botão de emergência está desobstruído e operacional?"></textarea>
                    </div>

                    <!-- Tipo -->
                    <div class="mb-4">
                        <label for="type" class="form-label form-label-premium">Momento da Verificação</label>
                        <select name="type" id="type" required class="form-select form-control-premium">
                            <option value="entry">Entrada (Início do Turno)</option>
                            <option value="exit">Saída (Fim do Turno)</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-premium btn-premium-primary w-100 py-2.5">
                        <i class="fa-solid fa-plus me-1"></i>
                        Adicionar ao Modelo
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- ================= ITENS DE CHECKLIST ================= -->
    <div class="col-12 col-xl-8">
        <div class="row g-4">
            
            <!-- CHECKLIST DE ENTRADA -->
            <div class="col-12 col-lg-6">
                <div class="card-premium">
                    <div class="card-premium-header bg-primary text-white py-3">
                        <h6 class="m-0 fw-bold d-flex align-items-center gap-2">
                            <i class="fa-solid fa-right-to-bracket text-warning"></i>
                            Checklist de Entrada
                        </h6>
                        <span class="badge bg-white text-primary fw-bold" style="font-size: 11px;">
                            <?= count($grouped['entry']) ?> Itens
                        </span>
                    </div>
                    <div class="card-premium-body p-0">
                        <ul class="list-group list-group-flush" style="border-radius: 0 0 var(--radius-lg) var(--radius-lg);">
                            <?php if (empty($grouped['entry'])): ?>
                                <li class="list-group-item text-center py-4 text-muted">
                                    Nenhum item cadastrado para a entrada.
                                </li>
                            <?php else: ?>
                                <?php foreach ($grouped['entry'] as $item): ?>
                                    <li class="list-group-item d-flex align-items-center justify-content-between p-3" style="border-bottom: 1px solid var(--border-color);">
                                        <div style="max-width: 75%; flex-grow: 1;">
                                            <p class="mb-1 fw-semibold text-dark" style="font-size: 13.5px; line-height: 1.4; <?= !$item['active'] ? 'text-decoration: line-through; color: var(--text-muted) !important;' : '' ?>">
                                                <?= htmlspecialchars($item['question']) ?>
                                            </p>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <!-- Status -->
                                            <?php if ($item['active']): ?>
                                                <span class="badge bg-success-subtle text-success border border-success-subtle py-1" style="font-size: 10px; border-radius: 4px;">Ativo</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle py-1" style="font-size: 10px; border-radius: 4px;">Inativo</span>
                                            <?php endif; ?>

                                            <!-- Toggle status button -->
                                            <a href="<?= BASE_URL ?>/checklist/toggle/<?= $item['id'] ?>" 
                                               class="btn btn-sm <?= $item['active'] ? 'btn-outline-danger' : 'btn-outline-success' ?>" 
                                               style="padding: 4px 8px; border-radius: 4px;"
                                               title="<?= $item['active'] ? 'Desativar item' : 'Ativar item' ?>">
                                                <i class="fa-solid <?= $item['active'] ? 'fa-toggle-on' : 'fa-toggle-off' ?>"></i>
                                            </a>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- CHECKLIST DE SAÍDA -->
            <div class="col-12 col-lg-6">
                <div class="card-premium">
                    <div class="card-premium-header bg-dark text-white py-3">
                        <h6 class="m-0 fw-bold d-flex align-items-center gap-2">
                            <i class="fa-solid fa-right-from-bracket text-warning"></i>
                            Checklist de Saída
                        </h6>
                        <span class="badge bg-white text-dark fw-bold" style="font-size: 11px;">
                            <?= count($grouped['exit']) ?> Itens
                        </span>
                    </div>
                    <div class="card-premium-body p-0">
                        <ul class="list-group list-group-flush" style="border-radius: 0 0 var(--radius-lg) var(--radius-lg);">
                            <?php if (empty($grouped['exit'])): ?>
                                <li class="list-group-item text-center py-4 text-muted">
                                    Nenhum item cadastrado para a saída.
                                </li>
                            <?php else: ?>
                                <?php foreach ($grouped['exit'] as $item): ?>
                                    <li class="list-group-item d-flex align-items-center justify-content-between p-3" style="border-bottom: 1px solid var(--border-color);">
                                        <div style="max-width: 75%; flex-grow: 1;">
                                            <p class="mb-1 fw-semibold text-dark" style="font-size: 13.5px; line-height: 1.4; <?= !$item['active'] ? 'text-decoration: line-through; color: var(--text-muted) !important;' : '' ?>">
                                                <?= htmlspecialchars($item['question']) ?>
                                            </p>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <!-- Status -->
                                            <?php if ($item['active']): ?>
                                                <span class="badge bg-success-subtle text-success border border-success-subtle py-1" style="font-size: 10px; border-radius: 4px;">Ativo</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle py-1" style="font-size: 10px; border-radius: 4px;">Inativo</span>
                                            <?php endif; ?>

                                            <!-- Toggle status button -->
                                            <a href="<?= BASE_URL ?>/checklist/toggle/<?= $item['id'] ?>" 
                                               class="btn btn-sm <?= $item['active'] ? 'btn-outline-danger' : 'btn-outline-success' ?>" 
                                               style="padding: 4px 8px; border-radius: 4px;"
                                               title="<?= $item['active'] ? 'Desativar item' : 'Ativar item' ?>">
                                                <i class="fa-solid <?= $item['active'] ? 'fa-toggle-on' : 'fa-toggle-off' ?>"></i>
                                            </a>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
