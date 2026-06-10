<?php
/**
 * @var array $machines
 * @var string $title
 */
?>

<!-- Header -->
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Cadastro de Máquinas</h4>
        <p class="text-muted small mb-0">Cadastre e monitore o maquinário fabril, gerando QR Codes únicos para identificação física.</p>
    </div>
    <a href="<?= BASE_URL ?>/machines/create" class="btn btn-primary btn-premium btn-premium-primary" style="padding: 10px 20px; font-size: 13px;">
        <i class="fa-solid fa-square-plus me-1"></i>
        Nova Máquina
    </a>
</div>

<!-- Lista de Máquinas -->
<div class="card-premium">
    <div class="table-responsive">
        <table class="table table-premium align-middle">
            <thead>
                <tr>
                    <th>Máquina</th>
                    <th>Modelo</th>
                    <th>Setor</th>
                    <th>Token de Conexão</th>
                    <th>Status Operacional</th>
                    <th>Identificação</th>
                    <th class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($machines)): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <i class="fa-solid fa-sheet-plastic d-block mb-2 fs-2 text-muted"></i>
                            Nenhuma máquina cadastrada.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($machines as $mac): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="user-avatar" style="width: 40px; height: 40px; background-color: #fffbeb; color: var(--accent); font-size: 16px; border: 1px solid var(--border-color);">
                                        <i class="fa-solid fa-screwdriver-wrench"></i>
                                    </div>
                                    <span class="fw-bold text-dark" style="font-size: 14px;"><?= htmlspecialchars($mac['name']) ?></span>
                                </div>
                            </td>
                            <td style="color: var(--text-main);">
                                <?= htmlspecialchars($mac['model'] ?: '—') ?>
                            </td>
                            <td>
                                <span class="badge bg-light text-secondary border px-2.5 py-1.5 fw-semibold" style="border-radius: var(--radius-sm); font-size: 11px;">
                                    <i class="fa-solid fa-industry me-1"></i>
                                    <?= htmlspecialchars($mac['sector'] ?: 'Não Definido') ?>
                                </span>
                            </td>
                            <td>
                                <code class="machine-token font-monospace text-uppercase" style="font-size: 12px; font-weight: 700;">
                                    <?= htmlspecialchars($mac['qr_code_token']) ?>
                                </code>
                            </td>
                            <td>
                                <?php if ($mac['active']): ?>
                                    <span class="badge-status badge-status-approved">
                                        <i class="fa-solid fa-circle fa-xs me-1"></i>
                                        Operativa (Ativa)
                                    </span>
                                <?php else: ?>
                                    <span class="badge-status badge-status-rejected">
                                        <i class="fa-solid fa-circle fa-xs me-1"></i>
                                        Interditada (Inativa)
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button type="button" 
                                        class="btn btn-sm btn-outline-dark" 
                                        style="padding: 6px 12px; font-size: 12px; font-weight: 600; border-radius: var(--radius-sm);"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#qrModal" 
                                        data-token="<?= htmlspecialchars($mac['qr_code_token']) ?>"
                                        data-name="<?= htmlspecialchars($mac['name']) ?>"
                                        data-sector="<?= htmlspecialchars($mac['sector']) ?>">
                                    <i class="fa-solid fa-qrcode me-1"></i>
                                    QR Code
                                </button>
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-2">
                                    <!-- Editar -->
                                    <a href="<?= BASE_URL ?>/machines/edit/<?= $mac['id'] ?>" 
                                       class="btn btn-sm btn-outline-primary" 
                                       style="padding: 6px 10px; border-radius: var(--radius-sm);" 
                                       title="Editar Máquina">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    
                                    <!-- Ativar/Suspender -->
                                    <?php if ($mac['active']): ?>
                                        <a href="<?= BASE_URL ?>/machines/toggle/<?= $mac['id'] ?>" 
                                           class="btn btn-sm btn-outline-danger" 
                                           style="padding: 6px 10px; border-radius: var(--radius-sm);" 
                                           title="Desativar Máquina"
                                           onclick="return confirm('Deseja realmente desativar a máquina «<?= htmlspecialchars($mac['name']) ?>»?')">
                                            <i class="fa-solid fa-ban"></i>
                                        </a>
                                    <?php else: ?>
                                        <a href="<?= BASE_URL ?>/machines/toggle/<?= $mac['id'] ?>" 
                                           class="btn btn-sm btn-outline-success" 
                                           style="padding: 6px 10px; border-radius: var(--radius-sm);" 
                                           title="Ativar Máquina">
                                            <i class="fa-solid fa-circle-check"></i>
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

<!-- ================= MODAL DE QR CODE ================= -->
<div class="modal fade" id="qrModal" tabindex="-1" aria-labelledby="qrModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: var(--radius-lg); border: none; overflow: hidden; box-shadow: var(--shadow-lg);">
            <div class="modal-header bg-dark text-white py-3">
                <h5 class="modal-title fw-bold" id="qrModalLabel" style="font-size: 16px;">
                    <i class="fa-solid fa-print text-warning me-2"></i>
                    Etiqueta de QR Code Industrial
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            
            <div class="modal-body text-center p-4">
                <div id="printableQrArea">
                    <div class="qr-container">
                        <!-- Título da Etiqueta -->
                        <div class="text-center">
                            <span class="fw-extrabold text-uppercase text-dark tracking-wider d-block mb-1" style="font-size: 18px; font-weight: 800;">TECHFORGE</span>
                            <span class="badge bg-dark px-3 py-1 fw-bold text-warning" style="font-size: 11px; border-radius: 4px;">SISTEMA CHECKIND</span>
                        </div>
                        
                        <!-- Localizador do QR Code -->
                        <div class="qr-code-canvas mt-2" id="qrcodeCanvas"></div>
                        
                        <!-- Dados da Máquina -->
                        <div class="text-center mt-2">
                            <h5 class="fw-bold mb-1 text-dark" id="qrMachineName" style="font-size: 16px;">MÁQUINA</h5>
                            <span class="text-muted d-block small mb-2" id="qrMachineSector" style="font-size: 12px; font-weight: 500;">SETOR</span>
                            <span class="font-monospace fw-bold px-2 py-1 bg-light border text-primary" id="qrMachineToken" style="font-size: 13px; border-radius: 4px; letter-spacing: 0.5px;">TOKEN</span>
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-top d-flex align-items-center justify-content-end gap-2">
                    <button type="button" class="btn btn-premium btn-premium-secondary py-2" data-bs-dismiss="modal">Fechar</button>
                    <button type="button" class="btn btn-premium btn-premium-primary py-2" onclick="printQrEtiqueta()">
                        <i class="fa-solid fa-print me-1"></i>
                        Imprimir Etiqueta
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Dependência QRCodeJS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

<!-- Script do Modal de QR Code -->
<script>
let activeQrInstance = null;

document.addEventListener('DOMContentLoaded', () => {
    const qrModal = document.getElementById('qrModal');
    if (qrModal) {
        qrModal.addEventListener('show.bs.modal', (event) => {
            const button = event.relatedTarget;
            const token = button.getAttribute('data-token');
            const name = button.getAttribute('data-name');
            const sector = button.getAttribute('data-sector') || 'Não Definido';
            
            // Popula os dados textuais no modal
            document.getElementById('qrMachineName').textContent = name;
            document.getElementById('qrMachineSector').textContent = 'Setor: ' + sector;
            document.getElementById('qrMachineToken').textContent = token.toUpperCase();
            
            // Limpa o canvas do QR anterior
            const canvasContainer = document.getElementById('qrcodeCanvas');
            canvasContainer.innerHTML = '';
            
            // Instancia o novo QR Code gerado localmente pelo browser
            setTimeout(() => {
                activeQrInstance = new QRCode(canvasContainer, {
                    text: token,
                    width: 180,
                    height: 180,
                    colorDark: "#0f1117",
                    colorLight: "#ffffff",
                    correctLevel: QRCode.CorrectLevel.H
                });
            }, 100);
        });
    }
});

function printQrEtiqueta() {
    const printContent = document.getElementById('printableQrArea').innerHTML;
    const originalContent = document.body.innerHTML;
    
    // Cria um frame temporário ou abre em nova aba para impressão estrita e limpa da etiqueta
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <html>
        <head>
            <title>Imprimir Etiqueta — CheckInd</title>
            <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
            <style>
                body {
                    font-family: 'Inter', sans-serif;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    margin: 0;
                    padding: 40px;
                    background-color: white;
                }
                .qr-container {
                    border: 3px double #000;
                    padding: 30px;
                    border-radius: 12px;
                    display: inline-flex;
                    flex-direction: column;
                    align-items: center;
                    gap: 15px;
                    width: 250px;
                    text-align: center;
                }
                .logo-text { font-size: 20px; font-weight: 800; text-transform: uppercase; margin: 0; }
                .logo-sub { background: #000; color: #fff; padding: 2px 10px; font-size: 10px; font-weight: bold; border-radius: 4px; display: inline-block; margin-top: 5px; }
                .qr-code-canvas { background: white; padding: 10px; border: 1px solid #ccc; display: block; margin: 10px 0; }
                .qr-code-canvas img { display: block; margin: 0 auto; width: 180px; height: 180px; }
                .machine-title { font-size: 16px; font-weight: 700; margin: 5px 0 2px 0; }
                .machine-sector { font-size: 11px; color: #555; margin-bottom: 8px; }
                .machine-token { font-family: monospace; font-size: 13px; font-weight: 700; border: 1px solid #000; padding: 4px 8px; background: #eee; }
            </style>
        </head>
        <body onload="window.print(); window.close();">
            <div class="qr-container">
                <div>
                    <div class="logo-text">TECHFORGE</div>
                    <div class="logo-sub">CHECKIND INDUSTRIAL</div>
                </div>
                <div class="qr-code-canvas">
                    \${document.getElementById('qrcodeCanvas').innerHTML}
                </div>
                <div>
                    <div class="machine-title">\${document.getElementById('qrMachineName').textContent}</div>
                    <div class="machine-sector">\${document.getElementById('qrMachineSector').textContent}</div>
                    <div class="machine-token">\${document.getElementById('qrMachineToken').textContent}</div>
                </div>
            </div>
        </body>
        </html>
    `);
    printWindow.document.close();
}
</script>
