<?php
namespace Zettai\Entity;

use Ob_Ivan\EviType\Value;

class Theme extends HidableDictionary
{
    // public : EntityInterface //

    public function getTableName()
    {
        return 'theme';
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
            'intro',
            'min_exercise_id',
            'max_exercise_id',
            'advanced_percent',
            'intermediate_percent',
        ]);
    }

    protected function getPrimaryKeyName()
    {
        return 'theme_id';
    }

    protected function validateRecord(Value $record)
    {
        parent::validateRecord($record);

        if (! (strlen($record->title) > 0)) {
            throw new Exception('Theme title is empty', Exception::THEME_TITLE_EMPTY);
        }
    }
}
