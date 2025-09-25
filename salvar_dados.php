<?php
// Apenas permite que este script seja executado via método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    die("Metodo nao permitido.");
}

// Pega e valida os dados da requisição
$temperatura = filter_input(INPUT_POST, 'temperatura', FILTER_VALIDATE_FLOAT);
$umidade = filter_input(INPUT_POST, 'umidade', FILTER_VALIDATE_FLOAT);

if ($temperatura === false || $umidade === false) {
    http_response_code(400); // Bad Request
    die("Dados invalidos ou ausentes.");
}

// Tenta conectar ao banco e inserir os dados
try {
    // Pega a string de conexão da variável de ambiente 'DATABASE_URL' configurada no Render
    $connection_string = getenv('DATABASE_URL');
    if ($connection_string === false) {
        // Se a variável não estiver configurada no Render, o script falha aqui.
        throw new Exception("Variavel de ambiente DATABASE_URL nao definida.");
    }
    
    // Cria a conexão PDO
    $pdo = new PDO($connection_string);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Prepara e executa a instrução SQL para inserir os dados
    $stmt = $pdo->prepare('INSERT INTO leituras (temperatura, umidade) VALUES (:temp, :umid)');
    
    // --- LINHAS CORRIGIDAS ---
    $stmt->bindValue(':temp', $temperatura);
    $stmt->bindValue(':umid', $umidade); // Corrigido para usar a variável $umidade
    
    $stmt->execute(); // Corrigido, sem a letra 'a'
    // -----------------------
    
    // Se chegou até aqui, tudo deu certo
    http_response_code(200);
    echo "Dados salvos com sucesso.";

} catch (PDOException $e) {
    // Captura qualquer erro relacionado ao banco de dados
    http_response_code(500); // Internal Server Error
    echo "!!! ERRO PDO: " . $e->getMessage() . " !!!";

} catch (Exception $e) {
    // Captura outros erros
    http_response_code(500);
    echo "!!! ERRO GERAL: " . $e->getMessage() . " !!!";
}
?>