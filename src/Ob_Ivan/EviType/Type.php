<?php
namespace Ob_Ivan\EviType;

class Type implements TypeInterface
{
    private $arguments;

    /**
     *  @var [<string presentation> => <Value value>]
    **/
    private $fromCache = [];

    /**
     * @var TypeSort
    **/
    private $sort;

    /**
     * @var ViewService
    **/
    private $viewService;

    public function __construct(TypeSortInterface $sort, array $arguments = null)
    {
        $this->sort         = $sort;
        $this->arguments    = $arguments;

        $this->viewService  = $sort->view($this);
    }

    public function __call($name, $arguments) {
        return $this->sort->call($this, $name, $arguments);
    }

    public function __get($name) {
        if ($name === 'view') {
            return $this->viewService;
        }
        throw new Exception(
            'Unknown field "' . $name . '"',
            Exception::TYPE_GET_NAME_UNKNOWN
        );
    }

    public function from($presentation)
    {
        if (! array_key_exists($presentation, $this->fromCache)) {
            $value = null;
            foreach ($this->viewService as $viewName => $view) {
                $value = $view->from($presentation);
                if ($value) {
                    if (! $value instanceof Value) {
                        throw new Exception(
                            'Value from view "' . $viewName . '" must be an instance of Value',
                            Exception::TYPE_FROM_VALUE_WRONG_TYPE
                        );
                    }
                    break;
                }
            }
            $this->fromCache[$presentation] = $value;
        }
        return $this->fromCache[$presentation];
    }
}
