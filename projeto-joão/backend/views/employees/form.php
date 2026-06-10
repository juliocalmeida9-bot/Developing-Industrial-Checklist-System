<?php
/**
 * @var array|null $employee
 * @var string|null $error
 * @var string $title
 */

$isEdit = !empty($employee['id']);
$actionUrl = $isEdit ? BASE_URL . "/employees/update/{$employee['id']}" : BASE_URL . "/employees/store";
?>

<!-- Header -->
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-dark"><?= $isEdit ? 'Editar Cadastro de Funcionário' : 'Cadastrar Novo Funcionário' ?></h4>
        <p class="text-muted small mb-0"><?= $isEdit ? 'Atualize as informações do operário cadastrado.' : 'Registre um novo operário para liberar o acesso ao aplicativo mobile.' ?></p>
    </div>
    <a href="<?= BASE_URL ?>/employees" class="btn btn-premium btn-premium-secondary" style="padding: 10px 16px; font-size: 13px;">
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
                    <i class="fa-solid <?= $isEdit ? 'fa-user-pen text-primary' : 'fa-user-plus text-primary' ?>"></i>
                    Informações do Operário
                </h5>
            </div>
            
            <div class="card-premium-body">
                <form action="<?= $actionUrl ?>" method="POST">
                    <!-- CSRF Token -->
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($this->csrfToken()) ?>">

                    <div class="row g-3">
                        <!-- Nome Completo -->
                        <div class="col-12">
                            <label for="name" class="form-label form-label-premium">Nome Completo</label>
                            <input type="text" name="name" id="name" required
                                   class="form-control form-control-premium"
                                   placeholder="Ex: João da Silva"
                                   value="<?= htmlspecialchars($employee['name'] ?? '') ?>">
                        </div>

                        <!-- Matrícula -->
                        <div class="col-12 col-md-6">
                            <label for="matricula" class="form-label form-label-premium">Número de Matrícula</label>
                            <input type="text" name="matricula" id="matricula" required
                                   class="form-control form-control-premium"
                                   placeholder="Ex: TF98765"
                                   value="<?= htmlspecialchars($employee['matricula'] ?? '') ?>"
                                   <?= $isEdit ? 'disabled readonly style="background-color: #f1f5f9; cursor: not-allowed;"' : '' ?>>
                            <?php if ($isEdit): ?>
                                <span class="text-muted small" style="font-size: 11px;">A matrícula é um identificador único imutável.</span>
                            <?php endif; ?>
                        </div>

                        <!-- Setor -->
                        <div class="col-12 col-md-6">
                            <label for="sector" class="form-label form-label-premium">Setor de Atuação</label>
                            <input type="text" name="sector" id="sector"
                                   class="form-control form-control-premium"
                                   placeholder="Ex: Estamparia, Soldagem..."
                                   value="<?= htmlspecialchars($employee['sector'] ?? '') ?>">
                        </div>

                        <!-- E-mail -->
                        <div class="col-12">
                            <label for="email" class="form-label form-label-premium">E-mail Corporativo</label>
                            <input type="email" name="email" id="email" required
                                   class="form-control form-control-premium"
                                   placeholder="Ex: joao.silva@techforge.com"
                                   value="<?= htmlspecialchars($employee['email'] ?? '') ?>"
                                   <?= $isEdit ? 'disabled readonly style="background-color: #f1f5f9; cursor: not-allowed;"' : '' ?>>
                            <?php if ($isEdit): ?>
                                <span class="text-muted small" style="font-size: 11px;">O e-mail é imutável para segurança do usuário.</span>
                            <?php endif; ?>
                        </div>

                        <!-- Senha -->
                        <div class="col-12">
                            <label for="password" class="form-label form-label-premium">
                                <?= $isEdit ? 'Nova Senha (Opcional)' : 'Senha de Acesso' ?>
                            </label>
                            <input type="password" name="password" id="password"
                                   class="form-control form-control-premium"
                                   placeholder="<?= $isEdit ? 'Deixe em branco para manter a senha atual' : 'Crie uma senha forte de 6+ caracteres' ?>"
                                   <?= $isEdit ? '' : 'required' ?>>
                            <?php if ($isEdit): ?>
                                <span class="text-muted small" style="font-size: 11px;">Informe uma nova senha apenas se desejar redefinir o acesso do operário.</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top d-flex align-items-center justify-content-end gap-2">
                        <a href="<?= BASE_URL ?>/employees" class="btn btn-premium btn-premium-secondary" style="padding: 10px 20px; font-size: 13px;">
                            Cancelar
                        </a>
                        <button type="submit" class="btn btn-premium btn-premium-primary" style="padding: 10px 20px; font-size: 13px;">
                            <i class="fa-solid fa-floppy-disk me-1"></i>
                            <?= $isEdit ? 'Salvar Alterações' : 'Salvar Operário' ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
