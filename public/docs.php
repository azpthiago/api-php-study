<?php
/**
 * Documento JSON simples listando os endpoints disponíveis.
 * Útil para testar rapidamente a API sem precisar abrir o README.
 */
header('Content-Type: application/json; charset=utf-8');

echo json_encode([
    'message' => 'API PHP - CRUD de Items',
    'endpoints' => [
        ['method' => 'GET', 'path' => '/read_item.php', 'description' => 'Lista todos os itens'],
        ['method' => 'POST', 'path' => '/create_item.php', 'description' => 'Cria um novo item (campos nome, descricao)'],
        ['method' => 'POST|PUT', 'path' => '/update_item.php', 'description' => 'Atualiza um item existente (id, nome, descricao)'],
        ['method' => 'POST|DELETE', 'path' => '/delete_item.php', 'description' => 'Remove um item (id)'],
    ],
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

// JSON_UNESCAPED_SLASHES mantém barras como "/".
// JSON_PRETTY_PRINT deixa o retorno legível.
// JSON_UNESCAPED_UNICODE evita escaping de caracteres acentuados.