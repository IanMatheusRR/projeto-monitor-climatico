<?php
// Obtém o nome do script a partir do caminho da URL
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$script_name = basename($path);

// Lista de scripts permitidos
$allowed_scripts = [
    'salvar_dados.php',
    'api_dados.php',
    'api_latest.php',
    'delete_all.php'
];

// Se o script solicitado estiver na lista, execute-o.
if (in_array($script_name, $allowed_scripts)) {
    require __DIR__ . '/' . $script_name;
} else {
    // Caso contrário, pode ser uma chamada para a raiz da API, mostre uma mensagem.
    echo "API Endpoint. Use um caminho especifico (ex: /api/api_latest.php).";
}