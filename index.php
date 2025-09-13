<?php
// Obtém o caminho da URL que foi requisitada
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Roteador: Decide qual arquivo carregar com base na URL
switch ($request_uri) {
    case '/':
    case '/index.php':
        // Se for a página principal, carrega o HTML do dashboard
        require __DIR__ . '/dashboard.html';
        break;
    
    case '/salvar_dados.php':
        require __DIR__ . '/salvar_dados.php';
        break;
        
    case '/api_dados.php':
        require __DIR__ . '/api_dados.php';
        break;
        
    case '/api_latest.php':
        require __DIR__ . '/api_latest.php';
        break;
        
    case '/delete_all.php':
        require __DIR__ . '/delete_all.php';
        break;

    default:
        // Se a rota não for encontrada, retorna um erro 404
        http_response_code(404);
        echo "<h1>404 Not Found</h1>";
        echo "A pagina '$request_uri' nao foi encontrada.";
        break;
}