<?php
namespace Zettai\Model;

use Zettai\Exercise as Record;

class Exercise implements EntityInterface
{
    // var //
    
    private $service;
    
    // public : EntityInterface //
    
    public function __construct(Service $service)
    {
        $this->service = $service;
    }
    
    // public : Exercise //
    
    /**
     *  @param  integer $exercise_id
     *  @return Record
    **/
    public function get($exercise_id)
    {
        // prepare
        $exercise_id  = intval($exercise_id);

        // validate
        if (! ($exercise_id > 0)) {
            throw new Exception('Exercise id is empty', Exception::EXERCISE_ID_EMPTY);
        }

        // execute
        $row = $this->db->fetchAssoc('
            SELECT
                `exercise_id`,
                `title`,
                `is_hidden`,
                `content`
            FROM `exercise`
            WHERE `exercise_id` = :exercise_id
        ', [
            'exercise_id' => $exercise_id,
        ]);

        // convert to record
        if ($row) {
            return new Record($row);
        }
        return null;
    }

    public function getCount($includeHidden = false)
    {
        return $this->db->fetchColumn('
            SELECT COUNT(`exercise_id`)
            FROM `exercise`
            ' . ($includeHidden ? '' : ' WHERE `is_hidden` = 0 ') . '
        ');
    }

    public function getList ($offset = 0, $limit = 20, $includeHidden = false)
    {
        // prepare
        $offset = intval ($offset);
        $limit  = intval ($limit);

        // execute
        $rows = $this->db->fetchAll('
            SELECT
                `exercise_id`,
                `title`,
                `is_hidden`
            FROM `exercise`
            ' . ($includeHidden ? '' : ' WHERE `is_hidden` = 0 ') . '
            ORDER BY `exercise_id` ASC
            LIMIT ' . $offset . ', ' . $limit . '
        ');

        // convert to records
        $records = [];
        foreach ($rows as $row) {
            $records[] = new Record ($row);
        }
        return $records;
    }

    public function getNewId()
    {
        return $this->db->fetchColumn('
            SELECT MAX(`exercise_id`) + 1
            FROM `exercise`
        ');
    }

    public function getNextId($exercise_id)
    {
        return $this->db->fetchColumn('
            SELECT MIN(`exercise_id`)
            FROM `exercise`
            WHERE `exercise_id` > :exercise_id
            AND `is_hidden` = 0
        ', [
            'exercise_id' => $exercise_id,
        ]);
    }

    public function delete($exercise_id)
    {
        // prepare
        $exercise_id  = intval ($exercise_id);

        // validate
        if (! ($exercise_id > 0)) {
            throw new Exception('Exercise id is empty', Exception::MODEL_EXERCISE_ID_EMPTY);
        }

        // execute
        $this->db->delete('exercise', ['exercise_id' => $exercise_id]);
    }

    public function set(Record $exercise)
    {
        // validate
        if (! ($exercise->exercise_id > 0)) {
            throw new Exception('Exercise id is empty', Exception::MODEL_EXERCISE_ID_EMPTY);
        }
        if (! (strlen($exercise->title) > 0)) {
            throw new Exception('Exercise title is empty', Exception::MODEL_EXERCISE_TITLE_EMPTY);
        }

        if ($this->getExercise($exercise->exercise_id)) {
            return $this->db->update('exercise', $exercise->getData(), ['exercise_id' => $exercise->exercise_id]);
        }
        return $this->db->insert('exercise', $exercise->getData());
    }
}
