<?php
header('Content-Type: application/json');
$db_path = __DIR__ . '/dados_sensores.sqlite';
$leituras = [];

try {
    $pdo = new PDO('sqlite:' . $db_path);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // SQL base. Puxamos os dados em ordem ASC para a linha do tempo do gráfico
    $sql = 'SELECT temperatura, umidade, data_hora FROM leituras';

    // Verifica se os filtros de data foram enviados via GET
    if (isset($_GET['start']) && !empty($_GET['start']) && isset($_GET['end']) && !empty($_GET['end'])) {
        // Adiciona a cláusula WHERE para filtrar pelo intervalo de datas
        $sql .= ' WHERE date(data_hora) BETWEEN :start AND :end';
    }

    $sql .= ' ORDER BY id ASC'; // Ordem cronológica para os gráficos

    $stmt = $pdo->prepare($sql);

    // Se os parâmetros existem, faz o bind deles na query
    if (isset($_GET['start']) && !empty($_GET['start']) && isset($_GET['end']) && !empty($_GET['end'])) {
        $stmt->bindValue(':start', $_GET['start'], PDO::PARAM_STR);
        $stmt->bindValue(':end', $_GET['end'], PDO::PARAM_STR);
    }
    
    $stmt->execute();
    $leituras = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Retorna array vazio em caso de erro
}

echo json_encode($leituras);
?>