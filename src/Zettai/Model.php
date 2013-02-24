<?php
namespace Zettai;

use Doctrine\DBAL\Connection;

/**
 * Класс, дающий доступ к извлечению и изменению данных в базе.
 *
 * Документация: https://github.com/ob-ivan/zettai-tesuji/wiki/Data-Model
**/
class Model
{
    private $db;
    
    public function __construct (Connection $db)
    {
        $this->db = $db;
    }
    
    // Mondai //
    
    /**
     *  @param  integer $mondai_id
     *  @return Mondai
    **/
    public function getMondai ($mondai_id)
    {
        // prepare
        $mondai_id  = intval ($mondai_id);
        
        // validate
        if (! ($mondai_id > 0)) {
            throw new Exception('Mondai id is empty', Exception::MODEL_MONDAI_ID_EMPTY);
        }
        
        // execute
        $row = $this->db->fetchAssoc('
            SELECT *
            FROM `mondai`
            WHERE `mondai_id` = :mondai_id
        ', [
            'mondai_id' => $mondai_id,
        ]);
        
        // convert to record
        if ($row) {
            return new Mondai($row);
        }
        return null;
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
        $rows = $this->db->fetchAll('
            SELECT
                `mondai_id`,
                `title`,
                `is_hidden`
            FROM `mondai`
            ' . ($includeHidden ? '' : ' WHERE `is_hidden` = 0 ') . '
            ORDER BY `mondai_id` ASC
            LIMIT ' . $offset . ', ' . $limit . '
        ');
        
        // convert to records
        $records = [];
        foreach ($rows as $row) {
            $records[] = new Mondai ($row);
        }
        return $records;
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
    
    public function setMondai (Mondai $mondai)
    {
        // validate
        if (! ($mondai->mondai_id > 0)) {
            throw new Exception('Mondai id is empty', Exception::MODEL_MONDAI_ID_EMPTY);
        }
        if (! (strlen($mondai->title) > 0)) {
            throw new Exception('Mondai title is empty', Exception::MODEL_MONDAI_TITLE_EMPTY);
        }
        
        return $this->db->update('mondai', $mondai->getData(), ['mondai_id' => $mondai->mondai_id]);
    }
}
