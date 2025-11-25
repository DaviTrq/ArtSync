<?php

namespace App\Repositories\Contracts;

use App\Models\PortfolioItem;

/**
 * Define o contrato para o repositório de itens do portfólio.
 * Qualquer classe de repositório de portfólio DEVE implementar estes métodos.
 */
interface PortfolioRepositoryInterface {
    
    /**
     * Busca todos os itens do portfólio de um usuário.
     * @param int $userId O ID do usuário.
     * @return array Um array de objetos PortfolioItem.
     */
    public function getByUserId(int $userId): array;

    /**
     * Busca um único item pelo seu ID, verificando se pertence ao usuário.
     * @param int $id O ID do item.
     * @param int $userId O ID do usuário.
     * @return PortfolioItem|null O objeto PortfolioItem ou null se não for encontrado.
     */
    public function find(int $id, int $userId): ?PortfolioItem;

    /**
     * Salva um novo item de portfólio no banco de dados.
     * @param PortfolioItem $item O objeto PortfolioItem preenchido.
     * @return bool True se salvar com sucesso, False se falhar.
     */
    public function save(PortfolioItem $item): bool;

    /**
     * Deleta um item de portfólio do banco de dados.
     * @param int $id O ID do item a ser deletado.
     * @param int $userId O ID do usuário (para segurança).
     * @return bool True se deletar com sucesso, False se falhar.
     */
    public function delete(int $id, int $userId): bool;
}