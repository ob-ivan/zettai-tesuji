<?php
namespace Zettai\Entity;

use Ob_Ivan\Model\Entity;
use Zettai\Type\TypeInterface;

class Theme extends Entity
{
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
        $row = $this->queryBuilder()
        ->select('theme_id')
        ->select('title')
        ->select('intro')
        ->select('min_exercise_id')
        ->select('max_exercise_id')
        ->select('advanced_percentage')
        ->select('intermediate_percentage')
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
}
