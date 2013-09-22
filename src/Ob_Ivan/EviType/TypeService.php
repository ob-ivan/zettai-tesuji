<?php
namespace Ob_Ivan\EviType;

use Ob_Ivan\EviType\Sort\Boolean\Builder            as BooleanBuilder;
use Ob_Ivan\EviType\Sort\Enum\Builder               as EnumBuilder;
use Ob_Ivan\EviType\Sort\Integer\Builder            as IntegerBuilder;
use Ob_Ivan\EviType\Sort\Map\Builder                as MapBuilder;
use Ob_Ivan\EviType\Sort\Product\Builder\Cartesian  as CartesianBuilder;
use Ob_Ivan\EviType\Sort\Product\Builder\Record     as RecordBuilder;
use Ob_Ivan\EviType\Sort\Sequence\Builder           as SequenceBuilder;
use Ob_Ivan\EviType\Sort\String\Builder             as StringBuilder;
use Ob_Ivan\EviType\Sort\Union\Builder              as UnionBuilder;

class TypeService implements TypeServiceInterface
{
    /**
     * @var TypeFactory
    **/
    private $factory;

    /**
     * @var [<string name> => <TypeInterface type>]
    **/
    private $registry = [];

    /**
     * @var [<string name> => <TypeInterface(self) producer>]
    **/
    private $types    = [];

    // public : ArrayAccess //

    public function offsetExists($offset)
    {
        return isset($this->types[$offset]) || isset($this->registry[$offset]);
    }

    public function offsetGet($offset)
    {
        if (! isset($this->types[$offset])) {
            if (! isset($this->registry[$offset])) {
                throw new Exception(
                    'Unknown type "' . $offset . '"',
                    Exception::TYPE_SERVICE_OFFSET_GET_OFFSET_UNKNOWN
                );
            }
            $this->set($offset, $this->registry[$offset]($this));
        }
        return $this->types[$offset];
    }

    public function offsetSet($offset, $value)
    {
        if (isset($this[$offset])) {
            throw new Exception(
                'Type "' . $offset . '" already exists',
                Exception::TYPE_SERVICE_OFFSET_SET_NAME_ALREADY_EXISTS
            );
        }
        $this->set($offset, $value);
    }

    public function offsetUnset($offset)
    {
        throw new Exception(
            'Unsetting types is not supported',
            Exception::TYPE_SERVICE_OFFSET_UNSET_UNSUPPORTED
        );
    }

    // public : TypeServiceInterface //

    /**
     * Регистрирует один или несколько производителей типов.
     *
     *  @param  string              $name
     *  @param  TypeInterface(self) $producer
     * OR
     *  @param  [string => TypeInterface(self)] $producerMap
     *
     *  @return self
    **/
    public function register($name, callable $producer = null)
    {
        if (is_array($name)) {
            foreach ($name as $typeName => $producer) {
                $this->register($typeName, $producer);
            }
        } else {
            if (isset($this[$name])) {
                throw new Exception(
                    'Type "' . $name . '" already exists',
                    Exception::TYPE_SERVICE_REGISTER_NAME_ALREADY_EXISTS
                );
            }
            $this->registry[$name] = $producer;
        }
        return $this;
    }

    // public : Service //

    public function __construct()
    {
        $this->factory = new TypeFactory();

        // Стандартные типы.
        foreach ($this->getDefaultTypes() as $name => $factory) {
            $this->register($name, $factory);
        }

        // Стандартные сорта.
        $this->factory->register([
            'enum'      => function () { return new EnumBuilder;        },
            'map'       => function () { return new MapBuilder;         },
            'product'   => function () { return new CartesianBuilder;   },
            'record'    => function () { return new RecordBuilder;      },
            'sequence'  => function () { return new SequenceBuilder;    },
            'union'     => function () { return new UnionBuilder;       },
        ]);
    }

    public function __call($name, $args)
    {
        return $this->factory->produce($name, $args);
    }

    public function __get($name)
    {
        return $this[$name];
    }

    public function __isset($name)
    {
        return isset($this[$name]);
    }

    // protected /

    /**
     * Return types to be registered on construction.
     *
     *  @return [<string name> => <BuilderInterface() factory>]
    **/
    protected function getDefaultTypes()
    {
        return [
            'null'      => function () { return (new NullBuilder   )->produce(); },
            'boolean'   => function () { return (new BooleanBuilder)->produce(); },
            'integer'   => function () { return (new IntegerBuilder)->produce(); },
            'string'    => function () { return (new StringBuilder )->produce(); },
        ];
    }

    protected function set($offset, $value)
    {
        if (! $value instanceof TypeInterface) {
            throw new Exception(
                'Value for offset "' . $offset . '" must implement TypeInterface',
                Exception::TYPE_SERVICE_SET_VALUE_WRONG_TYPE
            );
        }
        $this->types[$offset] = $value;
    }
}
