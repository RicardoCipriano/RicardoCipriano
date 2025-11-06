<?php
// API de aniversários
// Usa config.php para conectar ao banco e retorna dados no formato esperado pelo frontend

header('Content-Type: application/json; charset=utf-8');

// CORS para desenvolvimento com Vite
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if ($origin === 'http://localhost:3000' || $origin === 'http://127.0.0.1:3000') {
    header('Access-Control-Allow-Origin: ' . $origin);
    header('Access-Control-Allow-Methods: GET, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
}
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// Carrega a configuração do banco; espera que config.php exista na raiz do projeto
// e forneça ou uma variável $pdo (PDO) ou constantes DB_HOST, DB_NAME, DB_USER, DB_PASS
$configPath = __DIR__ . '/../config.php';
if (file_exists($configPath)) {
    require_once $configPath;
}

try {
    // Usa mysqli $conn do config.php
    if (!isset($conn) || !($conn instanceof mysqli)) {
        throw new Exception('Conexão com o banco não disponível ($conn).');
    }

    // Parâmetro de escopo: today | month
    $scope = isset($_GET['scope']) ? strtolower(trim($_GET['scope'])) : 'month';

    $today = new DateTime('today');
    $month = (int)$today->format('m');
    $day = (int)$today->format('d');
    $md = $today->format('m-d');

    // Base URL para montar caminhos de imagem acessíveis via Apache
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $baseUrl = 'http://' . $host . '/aniversario/';

    // Tabela real: `pessoas` (id, nome_completo, setor, foto, data_nascimento DATE)
    if ($scope === 'today') {
        $stmt = $conn->prepare('SELECT id, nome_completo, setor, foto, data_nascimento FROM pessoas WHERE DATE_FORMAT(data_nascimento, "%m-%d") = ? ORDER BY nome_completo');
        $stmt->bind_param('s', $md);
    } elseif ($scope === 'month') {
        // Somente datas subsequentes do mês atual (excluir o dia de hoje)
        $stmt = $conn->prepare('SELECT id, nome_completo, setor, foto, data_nascimento FROM pessoas WHERE MONTH(data_nascimento) = ? AND DAY(data_nascimento) > ? ORDER BY DAY(data_nascimento), nome_completo');
        $stmt->bind_param('ii', $month, $day);
    } else {
        throw new Exception('Parâmetro scope inválido. Use "today" ou "month".');
    }

    if (!$stmt->execute()) {
        throw new Exception('Falha ao executar consulta: ' . $stmt->error);
    }
    $result = $stmt->get_result();
    $rows = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    $data = [];
    foreach ($rows as $row) {
        $dateStr = '';
        if (!empty($row['data_nascimento'])) {
            $dt = DateTime::createFromFormat('Y-m-d', $row['data_nascimento']);
            if ($dt) {
                $dateStr = $dt->format('d/m');
            }
        }
        // Monta URL apontando SOMENTE para uploads locais
        $photoPath = $row['foto'] ?? '';
        $photo = '';
        if (!empty($photoPath)) {
            $filename = basename($photoPath);
            if (!empty($filename)) {
                $photo = $baseUrl . 'uploads/' . $filename;
            }
        }
        $data[] = [
            'id' => (int)$row['id'],
            'name' => $row['nome_completo'] ?? '',
            'department' => $row['setor'] ?? '',
            'photo' => $photo,
            'date' => $dateStr,
        ];
    }

    echo json_encode([
        'success' => true,
        'data' => $data,
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}