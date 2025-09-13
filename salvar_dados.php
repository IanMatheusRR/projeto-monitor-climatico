<?php
// Define o caminho para os arquivos de log e banco de dados
$log_file = __DIR__ . '/debug_log.txt';
$db_path = __DIR__ . '/dados_sensores.sqlite';

// Função para escrever no log com data e hora
function write_log($message) {
    global $log_file;
    // 'FILE_APPEND' garante que não vamos apagar o log a cada vez
    // 'LOCK_EX' previne que duas requisições escrevam ao mesmo tempo
    file_put_contents($log_file, date('Y-m-d H:i:s') . ' - ' . $message . PHP_EOL, FILE_APPEND | LOCK_EX);
}

// Inicia o log para esta requisição
write_log("================ Nova Requisição ================");
write_log("Método da Requisição: " . $_SERVER['REQUEST_METHOD']);

// Verifica se a requisição é do tipo POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    write_log("Requisição é POST. Verificando dados...");
    write_log("Dados recebidos (raw): " . file_get_contents('php://input'));
    write_log("Array \$_POST: " . print_r($_POST, true));

    $temperatura = filter_input(INPUT_POST, 'temperatura', FILTER_VALIDATE_FLOAT);
    $umidade = filter_input(INPUT_POST, 'umidade', FILTER_VALIDATE_FLOAT);

    if ($temperatura !== false && $umidade !== false) {
        write_log("Dados validados com sucesso: Temp=$temperatura, Umid=$umidade");
        
        try {
            write_log("Tentando conectar ao banco de dados em: " . $db_path);
            // Pega a string de conexão da variável de ambiente 'DATABASE_URL'
            $connection_string = getenv('DATABASE_URL');
            if ($connection_string === false) {
                die("Erro: Variavel de ambiente DATABASE_URL nao definida.");
            }
            $pdo = new PDO($connection_string);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            write_log("Conexão com o banco de dados bem-sucedida.");

            // Garante que a tabela existe
            $pdo->exec("CREATE TABLE IF NOT EXISTS leituras (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                temperatura REAL NOT NULL,
                umidade REAL NOT NULL,
                data_hora DATETIME DEFAULT CURRENT_TIMESTAMP
            )");
            write_log("Tabela 'leituras' verificada/criada.");

            $stmt = $pdo->prepare('INSERT INTO leituras (temperatura, umidade) VALUES (:temp, :umid)');
            write_log("Statement SQL preparado.");

            $stmt->bindValue(':temp', $temperatura);
            $stmt->bindValue(':umid', $umidade);
            
            $stmt->execute();
            write_log("!!! SUCESSO: Dados inseridos no banco de dados. !!!");
            
            http_response_code(200);
            echo "Dados salvos com sucesso.";

        } catch (PDOException $e) {
            // Se houver qualquer erro no bloco try, ele será capturado e logado aqui
            $error_message = "!!! ERRO PDO: " . $e->getMessage() . " !!!";
            write_log($error_message);
            http_response_code(500);
            echo $error_message;
        }
    } else {
        write_log("!!! ERRO: Dados de temperatura ou umidade inválidos ou não recebidos. !!!");
        http_response_code(400);
        echo "Dados invalidos.";
    }
} else {
    write_log("!!! ERRO: Requisição não era POST. Acesso negado. !!!");
    http_response_code(405);
    echo "Metodo nao permitido.";
}
write_log("================ Fim da Requisição ================\n");
?>