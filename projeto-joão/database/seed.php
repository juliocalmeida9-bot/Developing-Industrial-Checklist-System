<?php
/**
 * CheckInd — Seed de Dados Iniciais
 * Execute via: php database/seed.php
 * OU acesse http://localhost/checkind/database/seed.php (temporário)
 */

// Configuração da conexão
$host    = 'localhost';
$dbname  = 'checkind';
$user    = 'root';
$pass    = '';

try {
    $pdo = new PDO("mysql:host={$host};dbname={$dbname};charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage() . "\n");
}

echo "=== CheckInd — Seed de Dados ===\n\n";

// ------------------------------------------------------------
// 1. Gestor padrão
// ------------------------------------------------------------
$pdo->exec("DELETE FROM users");
$stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
$stmt->execute([
    'João Vitor',
    'joaoitor@gmail.com',
    password_hash('123456', PASSWORD_DEFAULT),
]);
echo "✓ Gestor criado: joaoitor@gmail.com / 123456\n";

// ------------------------------------------------------------
// 2. Funcionários de exemplo
// ------------------------------------------------------------
$pdo->exec("DELETE FROM api_tokens");
$pdo->exec("DELETE FROM checklist_responses");
$pdo->exec("DELETE FROM usage_logs");
$pdo->exec("DELETE FROM employees");

$stmt = $pdo->prepare("INSERT INTO employees (name, matricula, email, password, sector, active) VALUES (?, ?, ?, ?, ?, 1)");

$employees = [
    ['Carlos Mendes',  'OP001', 'carlos@techforge.com', 'op1234', 'Usinagem'],
    ['Ana Rodrigues',  'OP002', 'ana@techforge.com',    'op1234', 'Fundição'],
    ['Bruno Lima',     'OP003', 'bruno@techforge.com',  'op1234', 'Soldagem'],
    ['Fernanda Costa', 'OP004', 'fer@techforge.com',    'op1234', 'Usinagem'],
];

foreach ($employees as $emp) {
    $stmt->execute([$emp[0], $emp[1], $emp[2], password_hash($emp[3], PASSWORD_DEFAULT), $emp[4]]);
}
echo "✓ " . count($employees) . " funcionários criados (senha: op1234)\n";

// ------------------------------------------------------------
// 3. Máquinas com tokens únicos
// ------------------------------------------------------------
$pdo->exec("DELETE FROM machines");
$stmt = $pdo->prepare("INSERT INTO machines (name, model, sector, qr_code_token, active) VALUES (?, ?, ?, ?, 1)");

$machines = [
    ['Torno CNC Alpha',     'CNC-A200',   'Usinagem',  bin2hex(random_bytes(8))],
    ['Fresadora Beta',      'FRES-B100',  'Fundição',  bin2hex(random_bytes(8))],
    ['Solda MIG Gamma',     'MIG-G500',   'Soldagem',  bin2hex(random_bytes(8))],
    ['Prensa Hidráulica Delta', 'PHD-300', 'Usinagem', bin2hex(random_bytes(8))],
];

foreach ($machines as $maq) {
    $stmt->execute($maq);
}
echo "✓ " . count($machines) . " máquinas criadas\n";

// ------------------------------------------------------------
// 4. Itens de checklist
// ------------------------------------------------------------
$pdo->exec("DELETE FROM checklist_items");
$stmt = $pdo->prepare("INSERT INTO checklist_items (question, type, active) VALUES (?, ?, 1)");

$items = [
    // Entrada
    ['Os EPIs (luvas, óculos, capacete) estão corretos e em bom estado?', 'entry'],
    ['A máquina está sem vazamentos visíveis (óleo, fluido, gás)?',        'entry'],
    ['A área de trabalho está limpa e livre de obstáculos?',                'entry'],
    ['Os dispositivos de segurança (proteções, sensores) estão funcionando?','entry'],
    ['O nível de óleo/lubrificante está adequado?',                         'entry'],
    // Saída
    ['A máquina foi desligada e bloqueada corretamente?',                   'exit'],
    ['A máquina foi limpa após o uso (limalhas, rebarbas, resíduos)?',      'exit'],
    ['Os resíduos foram descartados nos coletores adequados?',              'exit'],
    ['Ferramentas e acessórios foram guardados nos locais corretos?',       'exit'],
    ['Alguma anomalia ou falha foi identificada durante o uso?',            'exit'],
];

foreach ($items as $item) {
    $stmt->execute($item);
}
echo "✓ " . count($items) . " itens de checklist criados\n";

echo "\n=== Seed concluído com sucesso! ===\n";
echo "Acesse o painel no endereço do seu servidor web local.\n";
echo "Login: joaoitor@gmail.com / 123456\n";
