<?php
// Obtém a 'ação' a ser executada a partir de um parâmetro na URL
// Ex: index.php?action=salvar_dados
$action = $_GET['action'] ?? 'show_dashboard';

// Roteador: Decide qual arquivo carregar com base na ação
switch ($action) {
    case 'salvar_dados':
        require __DIR__ . '/salvar_dados.php';
        break;
        
    case 'api_dados':
        require __DIR__ . '/api_dados.php';
        break;
        
    case 'api_latest':
        require __DIR__ . '/api_latest.php';
        break;
        
    case 'delete_all':
        require __DIR__ . '/delete_all.php';
        break;
    
    case 'show_dashboard':
    default:
        // Se nenhuma ação for especificada (ou for a ação padrão),
        // carrega o HTML do dashboard
        require __DIR__ . '/dashboard.html';
        break;
}