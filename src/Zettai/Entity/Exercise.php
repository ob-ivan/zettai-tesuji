<?php
namespace Zettai\Entity;

use Ob_Ivan\EviType\Value;

class Exercise extends HidableDictionary
{
    // public : EntityInterface //

    public function getTableName()
    {
        return 'exercise';
    }

    // protected : HideableDictionary //

    protected function getDatabaseViewName()
    {
        return 'database';
    }

    protected function getFieldList()
    {
        return array_merge(parent::getFieldList(), [
            'title',
            'content',
        ]);
    }

    protected function getPrimaryKeyName()
    {
        return 'exercise_id';
    }
}
