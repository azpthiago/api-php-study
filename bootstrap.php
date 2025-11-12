<?php

declare(strict_types=1);

/**
 * Bootstrap leve responsável por montar a cadeia Database → Service → Controller.
 * Os arquivos são incluídos manualmente (sem Composer) e a função helper
 * `make_item_controller()` é reutilizada por todos os endpoints públicos.
 */
require __DIR__ . '/src/Database/Database.php';
require __DIR__ . '/src/Services/ItemService.php';
require __DIR__ . '/src/Controllers/ItemController.php';

use App\Controllers\ItemController;
use App\Database\Database;
use App\Services\ItemService;

/**
 * Cria a instância do controller já com banco + service injetados.
 */
function make_item_controller(): ItemController
{
    $connection = Database::getConnection();
    $service = new ItemService($connection);

    return new ItemController($service);
}