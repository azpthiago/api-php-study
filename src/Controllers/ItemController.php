<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\ItemService;

/**
 * Controller HTTP que encapsula validações de entrada e formatação das respostas JSON.
 * Mantém o serviço de domínio isolado de detalhes do protocolo.
 */
class ItemController {
    // Helper para padronizar respostas JSON.
    public static function jsonResponse(array $payload, int $status = 200): void {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        try {
            echo json_encode(
                $payload,
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR
            );
        } catch (\JsonException $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Falha ao codificar JSON.',
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Guarda serviço injetado via construtor.
    public function __construct(private readonly ItemService $service) {}

    /**
     * Cria um item a partir do payload recebido.
     *
     * @param array<string, mixed> $payload
     */
    public function create(array $payload): array {
        // Normaliza e valida os campos obrigatórios.
        $nome = trim((string) ($payload['nome'] ?? ''));
        $descricao = trim((string) ($payload['descricao'] ?? ''));

        if ($nome === '' || $descricao === '') {
            return [
                'success' => false,
                'error' => 'Os campos nome e descrição são obrigatórios.',
            ];
        }

        // Solicita ao serviço a criação do registro.
        $id = $this->service->create($nome, $descricao);

        // Retorna payload padronizado de sucesso.
        return [
            'success' => true,
            'data' => [
                'id' => $id,
                'nome' => $nome,
                'descricao' => $descricao,
            ],
        ];
    }

    // Lista itens existentes em formato de resposta JSON-friendly.
    public function list(): array {
        return [
            'success' => true,
            'data' => $this->service->list(),
        ];
    }

    /**
     * Atualiza um item com dados fornecidos no payload.
     *
     * @param array<string, mixed> $payload
     */
    public function update(int $id, array $payload): array {
        // Sanitiza entrada e garante obrigatoriedade dos campos.
        $nome = trim((string) ($payload['nome'] ?? ''));
        $descricao = trim((string) ($payload['descricao'] ?? ''));

        if ($nome === '' || $descricao === '') {
            return [
                'success' => false,
                'error' => 'Os campos nome e descrição são obrigatórios.',
            ];
        }

        // Pede ao serviço a atualização; retorna erro se nada mudou.
        $updated = $this->service->update($id, $nome, $descricao);

        if (! $updated) {
            return [
                'success' => false,
                'error' => 'Registro não encontrado ou dados idênticos informados.',
            ];
        }

        // Resposta padronizada com os novos dados.
        return [
            'success' => true,
            'data' => [
                'id' => $id,
                'nome' => $nome,
                'descricao' => $descricao,
            ],
        ];
    }

    // Remove um item e informa se a operação teve sucesso.
    public function delete(int $id): array {
        // Requisita o DELETE para o service enviando o ID como parâmetro.
        $removed = $this->service->delete($id);

        if (! $removed) {
            return [
                'success' => false,
                'error' => 'Registro não encontrado.',
            ];
        }

        return [
            'success' => true,
        ];
    }

    // Busca item específico por ID devolvendo mensagem amigável quando não encontrado.
    public function find(int $id): array {
        // Requisita a BUSCA para o service enviando o ID como parâmetro.
        $item = $this->service->find($id);

        if ($item === null) {
            return [
                'success' => false,
                'error' => 'Registro não encontrado.',
            ];
        }

        return [
            'success' => true,
            'data' => $item,
        ];
    }

}