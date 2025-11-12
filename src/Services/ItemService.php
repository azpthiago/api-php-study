<?php
declare(strict_types=1);

namespace App\Services;

use PDO;
use PDOStatement;
use RuntimeException;

/**
 * Camada de domínio responsável por executar as operações CRUD sobre a entidade Item.
 * Recebe uma conexão PDO preparada pelo bootstrap e expõe métodos usados pelo controller.
 */
class ItemService
{
    public function __construct(private readonly PDO $database)
    {
    }

    /**
     * Persiste um novo item e retorna o identificador criado.
     */
    public function create(string $nome, string $descricao): int
    {
        $statement = $this->prepare('INSERT INTO items (nome, descricao) VALUES (:nome, :descricao)');
        $statement->bindValue(':nome', $nome, PDO::PARAM_STR);
        $statement->bindValue(':descricao', $descricao, PDO::PARAM_STR);
        $statement->execute();

        return (int) $this->database->lastInsertId();
    }

    /**
     * Recupera todos os itens ordenados por data de criação.
     *
     * @return array<int, array<string, mixed>>
     */
    public function list(): array
    {
        $statement = $this->database->query('SELECT * FROM items ORDER BY created_at DESC');

        if ($statement === false) {
            throw new RuntimeException('Falha ao consultar itens.');
        }

        return $statement->fetchAll();
    }

    /**
     * Localiza item por ID, retornando null quando inexistente.
     */
    public function find(int $id): ?array
    {
        $statement = $this->prepare('SELECT * FROM items WHERE id = :id');
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();
        $item = $statement->fetch();

        return $item !== false ? $item : null;
    }

    /**
     * Atualiza registros existentes; retorna verdadeiro se algo foi modificado.
     */
    public function update(int $id, string $nome, string $descricao): bool
    {
        $statement = $this->prepare('UPDATE items SET nome = :nome, descricao = :descricao WHERE id = :id');
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->bindValue(':nome', $nome, PDO::PARAM_STR);
        $statement->bindValue(':descricao', $descricao, PDO::PARAM_STR);

        $statement->execute();

        return $statement->rowCount() > 0;
    }

    /**
     * Remove item pelo ID informado.
     */
    public function delete(int $id): bool
    {
        $statement = $this->prepare('DELETE FROM items WHERE id = :id');
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();

        return $statement->rowCount() > 0;
    }

    /**
     * Pequeno helper para montar prepared statements com mensagem amigável.
     */
    private function prepare(string $sql): PDOStatement
    {
        $statement = $this->database->prepare($sql);

        if (! $statement) {
            throw new RuntimeException('Falha ao preparar statement.');
        }

        return $statement;
    }
}

