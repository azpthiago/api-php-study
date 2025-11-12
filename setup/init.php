<?php

declare(strict_types=1);

require __DIR__ . '/../src/Database/Database.php';

use App\Database\Database;

/**
 * Script de conveniência para preparar o banco SQLite em ambiente local.
 * Executa a criação da tabela `items` caso ainda não exista.
 */
$db = Database::getConnection();

$query = "
CREATE TABLE IF NOT EXISTS items (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nome TEXT NOT NULL,
    descricao TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
";

try {
    $db->exec($query);
    echo "Banco de dados e tabela criada com sucesso!";
} catch (\PDOException $e) {
    echo "Erro ao criar tabela: " . $e->getMessage();
}
