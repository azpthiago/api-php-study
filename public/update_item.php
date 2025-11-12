<?php
declare(strict_types=1);

require __DIR__ . '/../bootstrap.php';

use App\Controllers\ItemController;

/**
 * Endpoint responsável por atualizar um item existente.
 * Aceita payloads JSON (POST/PUT) e formulários URL-encoded (POST).
 */
$controller = make_item_controller();
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($method !== 'POST' && $method !== 'PUT') {
    ItemController::jsonResponse(['success' => false, 'error' => 'Método não permitido.'], 405);
    exit;
}

$contentType = $_SERVER['CONTENT_TYPE'] ?? '';
$rawBody = file_get_contents('php://input') ?: '';
$isJson = str_starts_with($contentType, 'application/json');

if ($isJson) {
    $payload = json_decode($rawBody, true);
} elseif ($method === 'POST') {
    $payload = $_POST;
} else {
    // PUT tradicional (application/x-www-form-urlencoded) não popula $_POST.
    parse_str($rawBody, $payload);
}

if (!is_array($payload)) {
    ItemController::jsonResponse(['success' => false, 'error' => 'Payload inválido.'], 400);
    exit;
}

$id = isset($payload['id']) ? (int) $payload['id'] : null;

if ($id === null) {
    ItemController::jsonResponse(['success' => false, 'error' => 'ID não fornecido.'], 400);
    exit;
}

$response = $controller->update($id, $payload);
$status = $response['success'] ? 200 : 400;

ItemController::jsonResponse($response, $status);