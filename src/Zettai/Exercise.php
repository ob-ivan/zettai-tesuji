<?php
/**
 * Рекорд одной задачи. Пока что пассивен.
**/
namespace Zettai;

class Exercise
{
    // const //
    
    const PROPERTY_TYPE    = __LINE__;
    const PROPERTY_DEFAULT = __LINE__;
    const PROPERTY_SCHEMA  = __LINE__;
    
    const TYPE_INTEGER  = __LINE__;
    const TYPE_STRING   = __LINE__;
    const TYPE_BOOLEAN  = __LINE__;
    const TYPE_KYOKU    = __LINE__;
    const TYPE_POSITION   = __LINE__;
    const TYPE_JSON     = __LINE__;
    const TYPE_PAI      = __LINE__;

    public static $KYOKUS = [
        'ton-1' => 1,
        'ton-2' => 1,
        'ton-3' => 1,
        'ton-4' => 1,
        'nan-1' => 1,
        'nan-2' => 1,
        'nan-3' => 1,
        'nan-4' => 1,
    ];
    
    public static $KAZES = [
        'ton' => 1,
        'nan' => 1,
        'sha' => 1,
        'pei' => 1,
    ];
    
    private static $FIELD_PROPERTIES = [
        'exercise_id' => [
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
        'content'     => [ 
            self::PROPERTY_TYPE    => self::TYPE_JSON,
            self::PROPERTY_DEFAULT => '{}',
            self::PROPERTY_SCHEMA  => [
                'kyoku'     => [
                    self::PROPERTY_TYPE    => self::TYPE_KYOKU,
                    self::PROPERTY_DEFAULT => 'ton-1',
                ],
                'position'    => [
                    self::PROPERTY_TYPE    => self::TYPE_POSITION,
                    self::PROPERTY_DEFAULT => 'ton',
                ],
                'turn'     => [
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
                'hand'     => [
                    self::PROPERTY_TYPE    => self::TYPE_PAI,
                    self::PROPERTY_DEFAULT => '',
                ],
                'draw'     => [
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
            ],
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
            Exception::EXERCISE_GET_FIELD_UNKNOWN
        );
    }
    
    public function __isset($name)
    {
        return isset(self::$FIELD_PROPERTIES[$name]);
    }
    
    public function getData()
    {
        $data = $this->data;
        foreach (self::$FIELD_PROPERTIES as $field => $properties) {
            if ($properties[self::PROPERTY_TYPE] === self::TYPE_JSON) {
                $data[$field] = json_encode($data[$field]);
            }
        }
        return $data;
    }
    
    // private //
    
    private function prepare ($value, $properties)
    {
        switch ($properties[self::PROPERTY_TYPE]) {
            case self::TYPE_BOOLEAN:
                return !! $value;
                
            case self::TYPE_KYOKU:
                if (! isset(self::$KYOKUS[$value])) {
                    return $properties[self::PROPERTY_DEFAULT];
                }
                return $value;
                
            case self::TYPE_INTEGER:
                return intval($value);
                
            case self::TYPE_POSITION:
                if (! isset(self::$KAZES[$value])) {
                    return $properties[self::PROPERTY_DEFAULT];
                }
                return $value;
                
            case self::TYPE_JSON:
                if (is_string ($value)) {
                    $decoded = json_decode($value);
                    if (is_object($decoded)) {
                        $unpacked = new ArrayObject($decoded);
                    } else {
                        $unpacked = [];
                    }
                } elseif (is_array ($value)) {
                    $unpacked = $value;
                }
                $prepared = [];
                foreach ($properties[self::PROPERTY_SCHEMA] as $field => $subproperties) {
                    if (isset($unpacked[$field])) {
                        $subvalue = $unpacked[$field];
                    } else {
                        $subvalue = $subproperties[self::PROPERTY_DEFAULT];
                    }
                    $prepared[$field] = self::prepare($subvalue, $subproperties);
                }
                return $prepared;
                
            case self::TYPE_PAI:
                return new Pai($value);
                
            case self::TYPE_STRING:
                return trim(strval($value));
            
            default:
                throw new Exception('Unknown type "' . $properties[self::PROPERTY_TYPE] . '"', Exception::EXERCISE_TYPE_UNKNOWN);
        }
    }
}
