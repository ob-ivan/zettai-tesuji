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
    
    public function getMondai ($mondai_id)
    {
        // prepare
        $mondai_id  = intval ($mondai['mondai_id']);
        
        // validate
        if (! ($mondai_id > 0)) {
            throw new Exception('Mondai id is empty', Exception::MODEL_MONDAI_ID_EMPTY);
        }
        
        // execute
        return $this->db->fetchAssoc('
            SELECT
                `mondai_id`,
                `title`,
                `content`
            FROM `mondai`
            WHERE `mondai_id` = :mondai_id
        ', [
            'mondai_id' => $mondai_id,
        ]);
    }
    
    public function getMondaiCount()
    {
        return $this->db->fetchColumn('
            SELECT COUNT(`mondai_id`)
            FROM `mondai`
        ');
    }
    
    public function getMondaiList ($offset = 0, $limit = 20)
    {
        return $this->db->fetchAll('
            SELECT
                `mondai_id`,
                `title`
            FROM `mondai`
            ORDER BY `mondai_id` ASC
            LIMIT :offset, :limit
        ', [
            'offset' => intval ($offset),
            'limit'  => intval ($limit),
        ]);
    }
    
    public function setMondai ($mondai)
    {
        // prepare
        $mondai_id  = intval ($mondai['mondai_id']);
        $title      = trim (strval ($mondai['title']));
        $content    = trim (strval ($mondai['content']));
        
        // validate
        if (! ($mondai_id > 0)) {
            throw new Exception('Mondai id is empty', Exception::MODEL_MONDAI_ID_EMPTY);
        }
        if (! (strlen($title) > 0)) {
            throw new Exception('Mondai title is empty', Exception::MODEL_MONDAI_TITLE_EMPTY);
        }
        if (! (strlen($content) > 0)) {
            throw new Exception('Mondai content is empty', Exception::MODEL_MONDAI_CONTENT_EMPTY);
        }
        
        // execute
        return $this->db->executeUpdate('
            REPLACE INTO `mondai` (
                `mondai_id`,
                `title`,
                `content`
            ) VALUES (
                :mondai_id,
                :title,
                :content
            )
        ', [
            'mondai_id' => $mondai_id,
            'title'     => $title,
            'content'   => $content,
        ]);
    }
}
