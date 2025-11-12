<?php

declare(strict_types=1);

namespace App\Database;

use PDO;
use PDOException;
use RuntimeException;

/**
 * Gerencia a conexão PDO com o banco SQLite utilizado pela aplicação.
 *
 * Ao invés de abrir múltiplas conexões, a classe fornece um singleton simples
 * (adequado para CLI/servidor embutido). Também garante que a pasta `db/`
 * exista antes de inicializar o arquivo `database.sqlite`.
 */
final class Database
{
    private static ?PDO $connection = null;

    /**
     * Recupera a conexão ativa ou cria uma nova quando necessário.
     */
    public static function getConnection(): PDO
    {
        $databaseDir = __DIR__ . '/../../db';

        if (! is_dir($databaseDir)) {
            mkdir($databaseDir, 0777, true);
        }

        if (self::$connection === null) {
            $databasePath = $databaseDir . '/database.sqlite';

            try {
                self::$connection = new PDO('sqlite:' . $databasePath);
                self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                self::$connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            } catch (PDOException $e) {
                throw new RuntimeException('Erro ao conectar ao banco de dados.', 0, $e);
            }
        }

        return self::$connection;
    }
}