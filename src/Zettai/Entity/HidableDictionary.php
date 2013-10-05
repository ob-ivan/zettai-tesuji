<?php
/**
 * Template class for implementing dictionary entities
 * with `is_hidden` attribute such as exercise, or record,
 *
 * Assumptions about underlying table:
 *  - There is a single field numeric primary key.
 *  - Primary key field is not an auto_increment.
 *  - There is an eviType type which represents records.
**/
namespace Zettai\Entity;

use Ob_Ivan\Model\Entity;
use Ob_Ivan\Model\Service;
use Ob_Ivan\EviType\TypeInterface;
use Ob_Ivan\EviType\Value;

abstract class HidableDictionary extends Entity
{
    /**
     * Record type.
     *
     *  @var TypeInterface
    **/
    private $type;

    // public : AbstractDictionary //

    /**
     * Store record type.
     *
     *  @param  Service         $service
     *  @param  TypeInterface   $type
    **/
    public function __construct(Service $service, TypeInterface $recordType)
    {
        parent::__construct($service);
        $this->type = $recordType;
    }

    public function delete($id)
    {
        // prepare
        $id = intval($id);

        // validate
        if (! ($id > 0)) {
            throw new Exception('Id is empty', Exception::ID_EMPTY);
        }

        // execute
        $this->queryBuilder()->delete([$this->getPrimaryKeyName() => $id]);
    }

    /**
     *  @param  integer     $id
     *  @return Value|null
    **/
    public function get($id)
    {
        // prepare
        $id  = intval($id);

        // validate
        if (! ($id > 0)) {
            throw new Exception('Id is empty', Exception::ID_EMPTY);
        }

        // execute
        $row = $this->queryBuilder_selectAll()
        ->where(function($expression) {
            return $expression->equals($this->getPrimaryKeyName(), ':id');
        })
        ->fetchAssoc(['id' => $id]);

        if (! $row) {
            return null;
        }

        // convert to record.
        return $this->type->from($this->getDatabaseViewName(), $row);
    }

    public function getList($offset = 0, $limit = 20, $includeHidden = false)
    {
        // prepare
        $offset = intval($offset);
        $limit  = intval($limit);

        // execute
        $qb = $this->queryBuilder_selectAll()
        ->orderBy($this->getPrimaryKeyName(), 'ASC')
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
            $records[] = $this->type->from($this->getDatabaseViewName(), $row);
        }
        return $records;
    }

    public function getNewId()
    {
        return $this->queryBuilder()
        ->select(function ($expr) {
            return $expr->max($this->getPrimaryKeyName());
        })
        ->fetchColumn() + 1;
    }

    public function getNextId($id, $includeHidden = false)
    {
        return $this->queryBuilder()
        ->select(function ($expr) {
            return $expr->min($this->getPrimaryKeyName());
        })
        ->where(function ($expr) use ($includeHidden) {
            $exprNext = $expr->greaterThan($this->getPrimaryKeyName(), ':id');
            if (! $includeHidden) {
                return $exprNext->addAnd($expr->equals('is_hidden', 0));
            }
            return $exprNext;
        })
        ->fetchColumn(['id' => $id]);
    }

    public function getPrevId($id, $includeHidden = false)
    {
        return $this->queryBuilder()
        ->select(function ($expr) {
            return $expr->max($this->getPrimaryKeyName());
        })
        ->where(function ($expr) use ($includeHidden) {
            $exprPrev = $expr->lessThan($this->getPrimaryKeyName(), ':id');
            if (! $includeHidden) {
                return $exprPrev->addAnd($expr->equals('is_hidden', 0));
            }
            return $exprPrev;
        })
        ->fetchColumn(['id' => $id]);
    }

    public function getPage($id, $per_page, $includeHidden = false)
    {
        return $this->queryBuilder()
        ->select(function ($expr) use ($per_page) {
            return $expr->ceil($expr->divide($expr->count(), $per_page));
        })
        ->where(function ($expr) use ($includeHidden) {
            $exprPrev = $expr->lessThanOrEqual($this->getPrimaryKeyName(), ':id');
            if (! $includeHidden) {
                return $exprPrev->addAnd($expr->equals('is_hidden', 0));
            }
            return $exprPrev;
        })
        ->fetchColumn(['id' => $id]);
    }

    public function set(Value $record)
    {
        $this->validateRecord($record);
        $row = $record->to($this->getDatabaseViewName());

        if ($this->get($record->id)) {
            return $this->queryBuilder()->update(
                $row,
                [$this->getPrimaryKeyName() => $record->id]
            );
        }
        return $this->queryBuilder()->insert($row);
    }

    // protected //

    /**
     * Return view name for exporting records to and importing them from
     * a database row.
     *
     *  @return string
    **/
    abstract protected function getDatabaseViewName();

    /**
     * Return field names.
     *
     *  @return [string]
    **/
    protected function getFieldList()
    {
        return [
            $this->getPrimaryKeyName(),
            'is_hidden',
        ];
    }

    /**
     * Return name of the primary key field.
     *
     *  @return string
    **/
    abstract protected function getPrimaryKeyName();

    protected function queryBuilder_selectAll()
    {
        $qb = $this->queryBuilder();
        foreach ($this->getFieldList() as $fieldName) {
            $qb->select($fieldName);
        }
        return $qb;
    }

    /**
     * Throw exception if there is anything wrong with the argument.
     *
     * Return true if everything is dune.
     *
     *  @param  Value       $record
     *  @throws Exception
     *  @return true
    **/
    protected function validateRecord(Value $record)
    {
        if (! $this->type->has($record)) {
            throw new Exception('Record does not belong to its type', Exception::RECORD_WRONG_TYPE);
        }
        if (! ($record->id > 0)) {
            throw new Exception('Id is empty', Exception::ID_EMPTY);
        }
    }
}
