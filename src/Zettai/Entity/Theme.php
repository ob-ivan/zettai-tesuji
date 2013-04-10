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
}
