<?php
declare(strict_types=1);

require __DIR__ . '/../bootstrap.php';

use App\Controllers\ItemController;

/**
 * Endpoint de listagem: retorna todos os itens cadastrados.
 */
$controller = make_item_controller();

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET') {
    ItemController::jsonResponse(['success' => false, 'error' => 'Método não permitido.'], 405);
    exit;
}

$response = $controller->list();
ItemController::jsonResponse($response);