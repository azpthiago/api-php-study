<?php
declare(strict_types=1);

require __DIR__ . '/../bootstrap.php';

use App\Controllers\ItemController;

/**
 * Endpoint responsável por criar novos itens.
 * Aceita tanto `application/json` quanto formulários URL-encoded.
 */
$controller = make_item_controller();

$contentType = $_SERVER['CONTENT_TYPE'] ?? '';
$payload = str_starts_with($contentType, 'application/json')
    ? json_decode(file_get_contents('php://input') ?: '', true)
    : $_POST;

if (! is_array($payload)) {
    ItemController::jsonResponse(['success' => false, 'error' => 'JSON inválido.'], 400);
    exit;
}

$response = $controller->create($payload);

ItemController::jsonResponse($response, $response['success'] ? 201 : 400);