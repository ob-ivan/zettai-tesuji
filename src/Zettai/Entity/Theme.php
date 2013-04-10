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
}
