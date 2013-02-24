<?php
/**
 * Рекорд одной задачи. Пока что пассивен.
**/
namespace Zettai;

class Mondai
{
    // const //
    
    const PROPERTY_TYPE    = __LINE__;
    const PROPERTY_DEFAULT = __LINE__;
    
    const TYPE_INTEGER  = __LINE__;
    const TYPE_STRING   = __LINE__;
    const TYPE_BOOLEAN  = __LINE__;
    const TYPE_KYOKU    = __LINE__;
    const TYPE_JIKAZE   = __LINE__;
    const TYPE_PAI      = __LINE__;

    private static $KYOKU = [
        'ton-1' => 1,
        'ton-2' => 1,
        'ton-3' => 1,
        'ton-4' => 1,
        'nan-1' => 1,
        'nan-2' => 1,
        'nan-3' => 1,
        'nan-4' => 1,
    ];
    
    private static $JIKAZE = [
        'ton' => 1,
        'nan' => 1,
        'sha' => 1,
        'pei' => 1,
    ];
    
    private static $FIELD_PROPERTIES = [
        'mondai_id' => [
            self::PROPERTY_TYPE    => self::TYPE_INTEGER,
            self::PROPERTY_DEFAULT => 0,
        ],
        'title'     => [
            self::PROPERTY_TYPE    => self::TYPE_STRING,
            self::PROPERTY_DEFAULT => '',
        ],
        'is_hidden' => [
            self::PROPERTY_TYPE    => self::TYPE_BOOLEAN,
            self::PROPERTY_DEFAULT => false,
        ],
        'content'     => [ // DEPRECATED
            self::PROPERTY_TYPE    => self::TYPE_STRING,
            self::PROPERTY_DEFAULT => '',
        ],
        'kyoku'     => [
            self::PROPERTY_TYPE    => self::TYPE_KYOKU,
            self::PROPERTY_DEFAULT => 'ton-1',
        ],
        'jikaze'    => [
            self::PROPERTY_TYPE    => self::TYPE_JIKAZE,
            self::PROPERTY_DEFAULT => 'ton',
        ],
        'junme'     => [
            self::PROPERTY_TYPE    => self::TYPE_INTEGER,
            self::PROPERTY_DEFAULT => '1',
        ],
        'dora'      => [
            self::PROPERTY_TYPE    => self::TYPE_PAI,
            self::PROPERTY_DEFAULT => '5z',
        ],
        'mochiten'  => [
            self::PROPERTY_TYPE    => self::TYPE_STRING,
            self::PROPERTY_DEFAULT => '25000',
        ],
        'tehai'     => [
            self::PROPERTY_TYPE    => self::TYPE_STRING,
            self::PROPERTY_DEFAULT => '',
        ],
        'tsumo'     => [
            self::PROPERTY_TYPE    => self::TYPE_PAI,
            self::PROPERTY_DEFAULT => '5z',
        ],
        'kiri_a'    => [
            self::PROPERTY_TYPE    => self::TYPE_PAI,
            self::PROPERTY_DEFAULT => '5z',
        ],
        'kiri_b'    => [
            self::PROPERTY_TYPE    => self::TYPE_PAI,
            self::PROPERTY_DEFAULT => '5z',
        ],
        'kiri_c'    => [
            self::PROPERTY_TYPE    => self::TYPE_PAI,
            self::PROPERTY_DEFAULT => '5z',
        ],
    ];
    
    // var //
    
    private $data = [];
    
    // public //
    
    public function __construct(array $row)
    {
        foreach (self::$FIELD_PROPERTIES as $fieldName => $properties) {
            if (isset($row[$fieldName])) {
                $this->data[$fieldName] = self::prepare($row[$fieldName], $properties);
            }
        }
    }
    
    public function __get($name)
    {
        if (isset(self::$FIELD_PROPERTIES[$name])) {
            if (isset($this->data[$name])) {
                return $this->data[$name];
            }
            return self::$FIELD_PROPERTIES[$name][self::PROPERTY_DEFAULT];
        }
        throw new Exception(
            'Unknown field "' . $name . '" for record "' . __CLASS__ . '"',
            Exception::MONDAI_GET_FIELD_UNKNOWN
        );
    }
    
    public function __isset($name)
    {
        return isset(self::$FIELD_PROPERTIES[$name]);
    }
    
    // private //
    
    private function prepare ($value, $properties)
    {
        switch ($properties[self::PROPERTY_TYPE]) {
            case self::TYPE_BOOLEAN:
                return !! $value;
                
            case self::TYPE_KYOKU:
                if (! isset(self::$KYOKU[$value])) {
                    return $properties[self::PROPERTY_DEFAULT];
                }
                return $value;
                
            case self::TYPE_INTEGER:
                return intval($value);
                
            case self::TYPE_JIKAZE:
                if (! isset(self::$JIKAZE[$value])) {
                    return $properties[self::PROPERTY_DEFAULT];
                }
                return $value;
                
            case self::TYPE_STRING:
                return trim(strval($value));
            
            default:
                throw new Exception('Unknown type "' . $type . '"', Exception::MONDAI_TYPE_UNKNOWN);
        }
    }
}
