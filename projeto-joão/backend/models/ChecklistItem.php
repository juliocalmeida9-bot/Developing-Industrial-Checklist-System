<?php
class ChecklistItem extends Model
{
    protected string $table = 'checklist_items';

    /** Retorna itens ativos de um tipo específico ('entry' ou 'exit') */
    public function findByType(string $type): array
    {
        return $this->findAll('type = ? AND active = 1', [$type], 'id ASC');
    }

    /** Retorna todos os itens agrupados por tipo */
    public function findAllGrouped(): array
    {
        $all   = $this->findAll('', [], 'type ASC, id ASC');
        $entry = array_filter($all, fn($i) => $i['type'] === 'entry');
        $exit  = array_filter($all, fn($i) => $i['type'] === 'exit');

        return [
            'entry' => array_values($entry),
            'exit'  => array_values($exit),
        ];
    }
}
