<?php
namespace Ob_Ivan\EviType;

class Type implements TypeInterface
{
    // var //

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
     *  @var ValueService
    **/
    private $valueSevice = [];

    /**
     * @var ViewService
    **/
    private $viewService;

    // public //

    public function __construct(TypeSortInterface $sort, array $arguments = null)
    {
        $this->sort         = $sort;
        $this->arguments    = $arguments;

        $this->valueService = new ValueService($this);
        $this->viewService  = $this->sort->view($this);
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
                $internal = $view->from($presentation);
                if ($internal) {
                    $value = $this->valueService->produce($internal);
                    break;
                }
            }
            $this->fromCache[$presentation] = $value;
        }
        return $this->fromCache[$presentation];
    }

    public function to($viewName, $internal)
    {
        if (! isset($this->viewService[$viewName])) {
            throw new Exception(
                'Unknown view "' . $viewName . '"',
                Exception::TYPE_TO_VIEW_NAME_UNKNOWN
            );
        }
        $presentation = $this->viewService[$viewName]->to($internal);
        if (is_null($presentation)) {
            throw new Exception(
                'Value cannot be converted to view "' . $viewName . '"',
                Exception::TYPE_TO_PRESENTATION_IS_NULL
            );
        }
        return $presentation;
    }
}
