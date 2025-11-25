<?php

namespace App\Repositories\Contracts;

use App\Models\User;

/**
 * Define o contrato para o repositório de Usuários.
 * Qualquer classe de repositório de usuário DEVE implementar estes métodos.
 */
interface UserRepositoryInterface {
    
    /**
     * Encontra um usuário pelo seu email.
     * @param string $email O email do usuário.
     * @return User|null Retorna o objeto User ou null se não encontrar.
     */
    public function findByEmail(string $email): ?User;

    /**
     * Encontra um usuário pelo seu ID.
     * @param int $id O ID do usuário.
     * @return User|null Retorna o objeto User ou null se não encontrar.
     */
    public function findById(int $id): ?User;

    /**
     * Salva um novo usuário no banco de dados.
     * @param User $user O objeto User preenchido.
     * @return bool True se salvar com sucesso, False se falhar.
     */
    public function save(User $user): bool;

    /**
     * Deleta um usuário pelo seu ID.
     * @param int $id O ID do usuário a ser deletado.
     * @return bool True se deletar com sucesso, False se falhar.
     */
    public function delete(int $id): bool;

    /**
     * Pega todos os usuários (para o painel de admin).
     * @return array Um array com os dados de todos os usuários.
     */
    public function getAll(): array;
}