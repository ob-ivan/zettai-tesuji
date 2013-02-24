<?php
namespace Zettai;

use Doctrine\DBAL\Connection;

/**
 * Класс, дающий доступ к извлечению и изменению данных в базе.
 *
 * Документация: https://github.com/ob-ivan/zettai-tesuji/wiki/Модель-данных
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
        $mondai_id  = intval ($mondai_id);
        
        // validate
        if (! ($mondai_id > 0)) {
            throw new Exception('Mondai id is empty', Exception::MODEL_MONDAI_ID_EMPTY);
        }
        
        // execute
        return $this->db->fetchAssoc('
            SELECT
                `mondai_id`,
                `title`,
                `content`,
                `is_hidden`
            FROM `mondai`
            WHERE `mondai_id` = :mondai_id
        ', [
            'mondai_id' => $mondai_id,
        ]);
    }
    
    public function getMondaiCount($includeHidden = false)
    {
        return $this->db->fetchColumn('
            SELECT COUNT(`mondai_id`)
            FROM `mondai`
            ' . ($includeHidden ? '' : ' WHERE `is_hidden` = 0 ') . '
        ');
    }
    
    public function getMondaiList ($offset = 0, $limit = 20, $includeHidden = false)
    {
        // prepare
        $offset = intval ($offset);
        $limit  = intval ($limit);
        
        // execute
        return $this->db->fetchAll('
            SELECT
                `mondai_id`,
                `title`,
                `is_hidden`
            FROM `mondai`
            ' . ($includeHidden ? '' : ' WHERE `is_hidden` = 0 ') . '
            ORDER BY `mondai_id` ASC
            LIMIT ' . $offset . ', ' . $limit . '
        ');
    }
    
    public function getMondaiNextId()
    {
        return $this->db->fetchColumn('
            SELECT MAX(`mondai_id`) + 1
            FROM `mondai`
        ');
    }
    
    public function deleteMondai ($mondai_id)
    {
        // prepare
        $mondai_id  = intval ($mondai_id);
        
        // validate
        if (! ($mondai_id > 0)) {
            throw new Exception('Mondai id is empty', Exception::MODEL_MONDAI_ID_EMPTY);
        }
        
        // execute
        $this->db->delete ('mondai', ['mondai_id' => $mondai_id]);
    }
    
    public function setMondai ($mondai)
    {
        // prepare
        $mondai_id  = intval ($mondai['mondai_id']);
        $title      = trim (strval ($mondai['title']));
        $content    = trim (strval ($mondai['content']));
        $is_hidden  = intval (!! (isset($mondai['is_hidden']) ? $mondai['is_hidden'] : true));
        
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
                `content`,
                `is_hidden`
            ) VALUES (
                :mondai_id,
                :title,
                :content,
                :is_hidden
            )
        ', [
            'mondai_id' => $mondai_id,
            'title'     => $title,
            'content'   => $content,
            'is_hidden' => $is_hidden,
        ]);
    }
}
