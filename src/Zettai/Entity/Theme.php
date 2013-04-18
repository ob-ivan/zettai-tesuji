<?php
namespace Zettai\Entity;

use Ob_Ivan\Model\Entity;
use Ob_Ivan\Model\Service;
use Zettai\Type\TypeInterface;
use Zettai\Type\Value;

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

    public function getList($offset = 0, $limit = 20, $includeHidden = false)
    {
        // prepare
        $offset = intval($offset);
        $limit  = intval($limit);

        // execute
        $qb = $this->queryBuilder_selectAll()
        ->orderBy('theme_id', 'ASC')
        ->offset($offset)
        ->limit($limit);
        if (! $includeHidden) {
            $qb->where(function ($expr) {
                return $expr->equals('is_hidden', 0);
            });
        }
        $rows = $qb->fetchAll();

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

    public function getNextId($theme_id, $includeHidden = false)
    {
        return $this->queryBuilder()
        ->select(function ($expr) {
            return $expr->min('theme_id');
        })
        ->where(function ($expr) use ($includeHidden) {
            $exprNext = $expr->greaterThan('theme_id', ':theme_id');
            if (! $includeHidden) {
                return $exprNext->addAnd($expr->equals('is_hidden', 0));
            }
            return $exprNext;
        })
        ->fetchColumn(['theme_id' => $theme_id]);
    }

    public function getPrevId($theme_id, $includeHidden = false)
    {
        return $this->queryBuilder()
        ->select(function ($expr) {
            return $expr->max('theme_id');
        })
        ->where(function ($expr) use ($includeHidden) {
            $exprPrev = $expr->lessThan('theme_id', ':theme_id');
            if (! $includeHidden) {
                return $exprPrev->addAnd($expr->equals('is_hidden', 0));
            }
            return $exprPrev;
        })
        ->fetchColumn(['theme_id' => $theme_id]);
    }

    public function getPage($theme_id, $per_page, $includeHidden = false)
    {
        return $this->queryBuilder()
        ->select(function ($expr) use ($per_page) {
            return $expr->ceil($expr->divide($expr->count(), $per_page));
        })
        ->where(function ($expr) use ($includeHidden) {
            $exprPrev = $expr->lessThanOrEqual('theme_id', ':theme_id');
            if (! $includeHidden) {
                return $exprPrev->addAnd($expr->equals('is_hidden', 0));
            }
            return $exprPrev;
        })
        ->fetchColumn(['theme_id' => $theme_id]);
    }

    public function set(Value $theme)
    {
        // validate
        if (! $this->type->has($
        if (! ($theme->theme_id > 0)) {
            throw new Exception('Theme id is empty', Exception::THEME_ID_EMPTY);
        }
        if (! (strlen($theme->title) > 0)) {
            throw new Exception('Theme title is empty', Exception::THEME_TITLE_EMPTY);
        }

        if ($this->get($theme->theme_id)) {
            return $this->queryBuilder()->update($theme->toDatabase(), ['theme_id' => $theme->theme_id]);
        }
        return $this->queryBuilder()->insert($theme->toDatabase());
    }

    // protected //

    protected function queryBuilder_selectAll()
    {
        return $this->queryBuilder()
        ->select('theme_id')
        ->select('title')
        ->select('is_hidden')
        ->select('intro')
        ->select('min_exercise_id')
        ->select('max_exercise_id')
        ->select('advanced_percentage')
        ->select('intermediate_percentage');
    }
}
