<?php
// Define o caminho para o arquivo do banco de dados
$db_path = __DIR__ . '/dados_sensores.sqlite';

// Inicializa a resposta como um array
$response = ['success' => false, 'message' => ''];

// Apenas permite que este script seja executado via método POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Pega a string de conexão da variável de ambiente 'DATABASE_URL'
        $connection_string = getenv('DATABASE_URL');
        if ($connection_string === false) {
            die("Erro: Variavel de ambiente DATABASE_URL nao definida.");
        }
        $pdo = new PDO($connection_string);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Executa o comando para deletar todos os registros da tabela
        // O VACUUM depois otimiza o arquivo do banco de dados
        $pdo->exec('DELETE FROM leituras; VACUUM;');

        $response['success'] = true;
        $response['message'] = 'Todos os dados foram excluídos com sucesso.';

    } catch (PDOException $e) {
        $response['message'] = 'Erro ao conectar ou limpar o banco de dados: ' . $e->getMessage();
        http_response_code(500); // Erro interno do servidor
    }
} else {
    $response['message'] = 'Método não permitido.';
    http_response_code(405); // Método não permitido
}

// Retorna a resposta como JSON
header('Content-Type: application/json');
echo json_encode($response);
?>