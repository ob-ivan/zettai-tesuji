<?php
namespace Zettai\Entity;

use Ob_Ivan\Model\Entity;
use Zettai\Type\TypeInterface;

class Theme extends Entity
{
    // var //

    /**
     * Тип рекордов.
     *
     *  @var TypeInterface
    **/
    private $type;

    // public : EntityInterface //

    public function getTableName()
    {
        return 'theme';
    }

    // public : Entity //

    public function __construct(Service $service, TypeInterface $recordType)
    {
        parent::__construct($service);

        $this->type = $recordType;
    }

    // public : Theme //

    public function delete($theme_id)
    {
        // prepare
        $theme_id = intval($theme_id);

        // validate
        if (! ($theme_id > 0)) {
            throw new Exception('Theme id is empty', Exception::THEME_ID_EMPTY);
        }

        // execute
        $this->queryBuilder()->delete(['theme_id' => $theme_id]);
    }

    /**
     *  @param  integer $theme_id
     *  @return Value
    **/
    public function get($theme_id)
    {
        // prepare
        $theme_id  = intval($theme_id);

        // validate
        if (! ($theme_id > 0)) {
            throw new Exception('Theme id is empty', Exception::THEME_ID_EMPTY);
        }

        // execute
        $row = $this->queryBuilder_selectAll()
        ->where(function($expression) {
            return $expression->equals('theme_id', ':theme_id');
        })
        ->fetchAssoc(['theme_id' => $theme_id]);

        // convert to record, if possible.
        if (! $row) {
            return null;
        }
        return $this->type->from($row);
    }

    public function getList($offset = 0, $limit = 20)
    {
        // prepare
        $offset = intval($offset);
        $limit  = intval($limit);

        // execute
        $rows = $this->queryBuilder_selectAll()
        ->orderBy('theme_id', 'ASC')
        ->offset($offset)
        ->limit($limit)
        ->fetchAll();

        // convert to records
        $records = [];
        foreach ($rows as $row) {
            $records[] = $this->type->from($row);
        }
        return $records;
    }

    public function getNewId()
    {
        return $this->queryBuilder()
        ->select(function ($expr) {
            return $expr->max('theme_id');
        })
        ->fetchColumn() + 1;
    }

    public function getNextId($theme_id)
    {
        return $this->queryBuilder()
        ->select(function ($expr) {
            return $expr->min('theme_id');
        })
        ->where(function ($expr) {
            return $expr->greaterThan('theme_id', ':theme_id');
        })
        ->fetchColumn(['theme_id' => $theme_id]);
    }

    // protected //

    protected function queryBuilder_selectAll()
    {
        return $this->queryBuilder()
        ->select('theme_id')
        ->select('title')
        ->select('intro')
        ->select('min_exercise_id')
        ->select('max_exercise_id')
        ->select('advanced_percentage')
        ->select('intermediate_percentage');
    }
}
