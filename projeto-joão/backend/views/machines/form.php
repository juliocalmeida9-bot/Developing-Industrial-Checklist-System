<?php
/**
 * @var array|null $machine
 * @var string|null $error
 * @var string $title
 */

$isEdit = !empty($machine['id']);
$actionUrl = $isEdit ? BASE_URL . "/machines/update/{$machine['id']}" : BASE_URL . "/machines/store";
?>

<!-- Header -->
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-dark"><?= $isEdit ? 'Editar Máquina' : 'Cadastrar Nova Máquina' ?></h4>
        <p class="text-muted small mb-0"><?= $isEdit ? 'Atualize as informações do maquinário cadastrado.' : 'Adicione uma nova máquina ao chão de fábrica, gerando um token de identificação único.' ?></p>
    </div>
    <a href="<?= BASE_URL ?>/machines" class="btn btn-premium btn-premium-secondary" style="padding: 10px 16px; font-size: 13px;">
        <i class="fa-solid fa-arrow-left me-1"></i>
        Voltar à Lista
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        
        <!-- Alerta de Erro -->
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger border-0 shadow-sm d-flex align-items-center mb-4" role="alert" style="border-radius: var(--radius-md);">
                <i class="fa-solid fa-circle-exclamation fs-5 me-3"></i>
                <div>
                    <?= htmlspecialchars($error) ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Formulário Premium -->
        <div class="card-premium">
            <div class="card-premium-header">
                <h5 class="card-premium-title">
                    <i class="fa-solid <?= $isEdit ? 'fa-screwdriver-wrench text-primary' : 'fa-square-plus text-primary' ?>"></i>
                    Informações da Máquina
                </h5>
            </div>
            
            <div class="card-premium-body">
                <form action="<?= $actionUrl ?>" method="POST">
                    <!-- CSRF Token -->
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($this->csrfToken()) ?>">

                    <div class="row g-3">
                        <!-- Nome / Identificação -->
                        <div class="col-12">
                            <label for="name" class="form-label form-label-premium">Nome da Máquina / Equipamento</label>
                            <input type="text" name="name" id="name" required
                                   class="form-control form-control-premium"
                                   placeholder="Ex: Prensa Hidráulica de 50T"
                                   value="<?= htmlspecialchars($machine['name'] ?? '') ?>">
                        </div>

                        <!-- Modelo / Fabricante -->
                        <div class="col-12 col-md-6">
                            <label for="model" class="form-label form-label-premium">Modelo / Série</label>
                            <input type="text" name="model" id="model"
                                   class="form-control form-control-premium"
                                   placeholder="Ex: PH-500-V2"
                                   value="<?= htmlspecialchars($machine['model'] ?? '') ?>">
                        </div>

                        <!-- Setor -->
                        <div class="col-12 col-md-6">
                            <label for="sector" class="form-label form-label-premium">Setor / Localização</label>
                            <input type="text" name="sector" id="sector"
                                   class="form-control form-control-premium"
                                   placeholder="Ex: Estamparia A, Montagem..."
                                   value="<?= htmlspecialchars($machine['sector'] ?? '') ?>">
                        </div>

                        <?php if ($isEdit): ?>
                            <!-- Exibição do Token (Apenas Leitura) -->
                            <div class="col-12">
                                <label class="form-label form-label-premium d-block">Token de Identificação Atual</label>
                                <code class="machine-token font-monospace text-uppercase" style="font-size: 14px; font-weight: 700; padding: 10px 16px; border: 1px solid var(--border-color); display: inline-block;">
                                    <?= htmlspecialchars($machine['qr_code_token']) ?>
                                </code>
                                <span class="text-muted d-block small mt-2" style="font-size: 11px;">O token é imutável. Alterar o token exigiria re-imprimir a etiqueta de QR Code da máquina.</span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="mt-4 pt-3 border-top d-flex align-items-center justify-content-end gap-2">
                        <a href="<?= BASE_URL ?>/machines" class="btn btn-premium btn-premium-secondary" style="padding: 10px 20px; font-size: 13px;">
                            Cancelar
                        </a>
                        <button type="submit" class="btn btn-premium btn-premium-primary" style="padding: 10px 20px; font-size: 13px;">
                            <i class="fa-solid fa-floppy-disk me-1"></i>
                            <?= $isEdit ? 'Salvar Alterações' : 'Salvar Equipamento' ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
