<?php
namespace Zettai;

use Doctrine\DBAL\Connection;

/**
 * Класс, дающий доступ к извлечению и изменению данных в базе.
 *
 * Методы для сущности "Задача" (Mondai):
 *  [mondai_id, title, content]? getMondai(int $mondaiId)
 *  [mondai_id => title] getMondaiList(int $offset, int $limit) --- сортировка по возрастанию номера.
 *  void setMondai([mondai_id, title, content] $mondai)
**/
class Model
{
    private $db;
    
    public function __construct (Connection $db)
    {
        $this->db = $db;
    }
    
    // Mondai //
    
    public function getMondai ($mondaiId)
    {
        return $this->db->fetchAssoc('
            SELECT
                mondai_id,
                title,
                content
            FROM mondai
            WHERE mondai_id = :mondai_id
        ', [
            'mondai_id' => intval ($mondaiId)
        ]);
    }
}
