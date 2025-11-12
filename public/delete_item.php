<?php
declare(strict_types=1);

require __DIR__ . '/../bootstrap.php';

use App\Controllers\ItemController;

/**
 * Endpoint responsável por remover um item existente.
 * Aceita `POST` (form ou JSON) e `DELETE` com query string.
 */
$controller = make_item_controller();
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($method !== 'DELETE' && $method !== 'POST') {
    ItemController::jsonResponse(['success' => false, 'error' => 'Método não permitido.'], 405);
    exit;
}

$id = null;

if ($method === 'POST') {
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
    $rawBody = file_get_contents('php://input') ?: '';

    if (str_starts_with($contentType, 'application/json')) {
        $payload = json_decode($rawBody, true);
        if (is_array($payload) && isset($payload['id'])) {
            $id = (int) $payload['id'];
        }
    } else {
        $id = isset($_POST['id']) ? (int) $_POST['id'] : null;
    }
} else {
    $id = isset($_GET['id']) ? (int) $_GET['id'] : null;
}

if (! $id) {
    ItemController::jsonResponse(['success' => false, 'error' => 'ID não fornecido.'], 400);
    exit;
}

$response = $controller->delete($id);
$status = $response['success'] ? 200 : 404;

ItemController::jsonResponse($response, $status);