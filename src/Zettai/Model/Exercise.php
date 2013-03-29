<?php
namespace Zettai\Model;

use Zettai\Exercise as Record;

class Exercise extends Entity
{
    // public : EntityInterface //
    
    public function getTableName()
    {
        return 'exercise';
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
        $row = $this->queryBuilder()
        ->select('exercise_id')
        ->select('title')
        ->select('is_hidden')
        ->select('content')
        ->where(function($expression) {
            return $expression->equals('exercise_id', ':exercise_id');
        })
        ->fetchAssoc(['exercise_id' => $exercise_id]);

        // convert to record
        if ($row) {
            return new Record($row);
        }
        return null;
    }

    public function getCount($includeHidden = false)
    {
        $qb = $this->queryBuilder()
        ->select(function($expression) {
            return $expression->count('exercise_id');
        });
        if (! $includeHidden) {
            $qb->where(function($expr) {
                return $expr->equals('is_hidden', 0);
            });
        }
        return $qb->fetchColumn();
    }

    public function getList ($offset = 0, $limit = 20, $includeHidden = false)
    {
        // prepare
        $offset = intval ($offset);
        $limit  = intval ($limit);

        // execute
        $qb = $this->queryBuilder()
        ->select('exercise_id')
        ->select('title')
        ->select('is_hidden')
        ->orderBy('exercise_id', 'ASC')
        ->offset($offset)
        ->limit($limit);
        if (! $includeHidden) {
            $qb->where(function($expr) {
                return $expr->equals('is_hidden', 0);
            });
        }
        $rows = $qb->fetchAll();

        // convert to records
        $records = [];
        foreach ($rows as $row) {
            $records[] = new Record ($row);
        }
        return $records;
    }

    public function getNewId()
    {
        return $this->queryBuilder()
        ->select(function ($expr) {
            return $expr->max('exercise_id');
        })
        ->fetchColumn() + 1;
    }

    public function getNextId($exercise_id, $includeHidden = false)
    {
        return $this->queryBuilder()
        ->select(function ($expr) {
            return $expr->min('exercise_id');
        })
        ->where(function ($expr) use ($includeHidden) {
            $exprNext = $expr->greaterThan('exercise_id', ':exercise_id');
            if (! $includeHidden) {
                return $exprNext->addAnd($expr->equals('is_hidden', 0));
            }
            return $exprNext;
        })
        ->fetchColumn(['exercise_id' => $exercise_id]);
    }

    public function getPrevId($exercise_id, $includeHidden = false)
    {
        return $this->queryBuilder()
        ->select(function ($expr) {
            return $expr->max('exercise_id');
        })
        ->where(function ($expr) use ($includeHidden) {
            $exprPrev = $expr->lessThan('exercise_id', ':exercise_id');
            if (! $includeHidden) {
                return $exprPrev->addAnd($expr->equals('is_hidden', 0));
            }
            return $exprPrev;
        })
        ->fetchColumn(['exercise_id' => $exercise_id]);
    }

    public function getPage($exercise_id, $per_page, $includeHidden = false)
    {
        return $this->queryBuilder()
        ->select(function ($expr) use ($per_page) {
            return $expr->ceil($expr->divide($expr->count(), $per_page));
        })
        ->where(function ($expr) use ($includeHidden) {
            $exprPrev = $expr->lessThanOrEqual('exercise_id', ':exercise_id');
            if (! $includeHidden) {
                return $exprPrev->addAnd($expr->equals('is_hidden', 0));
            }
            return $exprPrev;
        })
        ->fetchColumn(['exercise_id' => $exercise_id]);
    }
    
    public function delete($exercise_id)
    {
        // prepare
        $exercise_id  = intval ($exercise_id);

        // validate
        if (! ($exercise_id > 0)) {
            throw new Exception('Exercise id is empty', Exception::EXERCISE_ID_EMPTY);
        }

        // execute
        $this->queryBuilder()->delete(['exercise_id' => $exercise_id]);
    }

    public function set(Record $exercise)
    {
        // validate
        if (! ($exercise->exercise_id > 0)) {
            throw new Exception('Exercise id is empty', Exception::EXERCISE_ID_EMPTY);
        }
        if (! (strlen($exercise->title) > 0)) {
            throw new Exception('Exercise title is empty', Exception::EXERCISE_TITLE_EMPTY);
        }

        if ($this->get($exercise->exercise_id)) {
            return $this->queryBuilder()->update($exercise->getData(), ['exercise_id' => $exercise->exercise_id]);
        }
        return $this->queryBuilder()->insert($exercise->getData());
    }
}
