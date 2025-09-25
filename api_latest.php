<?php
header('Content-Type: application/json');
$db_path = __DIR__ . '/dados_sensores.sqlite';
$leituras = [];

try {
    // Pega a string de conexão da variável de ambiente 'DATABASE_URL'
    $connection_string = getenv('DATABASE_URL');
    if ($connection_string === false) {
        die("Erro: Variavel de ambiente DATABASE_URL nao definida.");
    }
    $pdo = new PDO($connection_string);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Busca apenas as 5 mais recentes para o topo da página
    $stmt = $pdo->query('SELECT temperatura, umidade, data_hora FROM leituras ORDER BY id DESC LIMIT 5');
    $leituras = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Retorna array vazio em caso de erro
}

echo json_encode($leituras);
?>