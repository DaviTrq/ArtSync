<?php

namespace App\Repositories\Contracts;

use App\Models\ScheduleEvent;

/**
 * Define o contrato para o repositório de eventos da agenda.
 */
interface ScheduleRepositoryInterface {
    
    /**
     * Busca todos os eventos de um usuário específico.
     * @param int $userId O ID do usuário.
     * @return array Um array de objetos ScheduleEvent.
     */
    public function getByUserId(int $userId): array;

    /**
     * Salva um novo evento no banco de dados.
     * @param ScheduleEvent $event O objeto ScheduleEvent preenchido.
     * @return bool True se salvar com sucesso, False se falhar.
     */
    public function save(ScheduleEvent $event): bool;

    /**
     * Atualiza um evento existente.
     * @param ScheduleEvent $event O objeto ScheduleEvent com os dados atualizados.
     * @return bool True se atualizar com sucesso, False se falhar.
     */
    public function update(ScheduleEvent $event): bool;

    /**
     * Deleta um evento específico, verificando se pertence ao usuário.
     * @param int $id O ID do evento a ser deletado.
     * @param int $userId O ID do usuário (para segurança).
     * @return bool True se deletar com sucesso, False se falhar.
     */
    public function delete(int $id, int $userId): bool;
}