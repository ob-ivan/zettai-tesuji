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

    const TYPE_ABC      = __LINE__;
    const TYPE_BOOLEAN  = __LINE__;
    const TYPE_INTEGER  = __LINE__;
    const TYPE_JSON     = __LINE__;
    const TYPE_KYOKU    = __LINE__;
    const TYPE_POSITION = __LINE__;
    const TYPE_STRING   = __LINE__;
    const TYPE_TILE     = __LINE__;

    public static $ABCS = [
        'a' => 1,
        'b' => 1,
        'c' => 1,
    ];

    public static $KYOKUS = [
        'east-1'  => 1,
        'east-2'  => 1,
        'east-3'  => 1,
        'east-4'  => 1,
        'south-1' => 1,
        'south-2' => 1,
        'south-3' => 1,
        'south-4' => 1,
    ];

    public static $WINDS = [
        'east'  => 1,
        'south' => 1,
        'west'  => 1,
        'north' => 1,
    ];

    private static $DEFAULT = [
        self::TYPE_ABC      => 'a',
        self::TYPE_BOOLEAN  => false,
        self::TYPE_INTEGER  => 0,
        self::TYPE_JSON     => '{}',
        self::TYPE_KYOKU    => 'east-1',
        self::TYPE_POSITION => 'east',
        self::TYPE_STRING   => '',
        self::TYPE_TILE     => '5z',
    ];

    private static $FIELD_PROPERTIES = [
        'exercise_id' => [
            self::PROPERTY_TYPE    => self::TYPE_INTEGER,
        ],
        'title'     => [
            self::PROPERTY_TYPE    => self::TYPE_STRING,
        ],
        'is_hidden' => [
            self::PROPERTY_TYPE    => self::TYPE_BOOLEAN,
            self::PROPERTY_DEFAULT => true,
        ],
        'content'     => [
            self::PROPERTY_TYPE    => self::TYPE_JSON,
            self::PROPERTY_SCHEMA  => [
                'kyoku'     => [
                    self::PROPERTY_TYPE    => self::TYPE_KYOKU,
                ],
                'position'  => [
                    self::PROPERTY_TYPE    => self::TYPE_POSITION,
                ],
                'turn'      => [
                    self::PROPERTY_TYPE    => self::TYPE_INTEGER,
                    self::PROPERTY_DEFAULT => 1,
                ],
                'dora'      => [
                    self::PROPERTY_TYPE    => self::TYPE_TILE,
                ],
                'score'     => [
                    self::PROPERTY_TYPE    => self::TYPE_STRING,
                    self::PROPERTY_DEFAULT => '25000',
                ],
                'hand'      => [
                    self::PROPERTY_TYPE    => self::TYPE_TILE,
                    self::PROPERTY_DEFAULT => '',
                ],
                'draw'      => [
                    self::PROPERTY_TYPE    => self::TYPE_TILE,
                ],
                'is_answered' => [
                    self::PROPERTY_TYPE    => self::TYPE_BOOLEAN,
                    self::PROPERTY_DEFAULT => false,
                ],
                'answer' => [
                    self::PROPERTY_TYPE    => self::TYPE_JSON,
                    self::PROPERTY_DEFAULT => '{}',
                    self::PROPERTY_SCHEMA  => [
                        'a' => [
                            self::PROPERTY_TYPE    => self::TYPE_JSON,
                            self::PROPERTY_SCHEMA  => [
                                'discard' => [
                                    self::PROPERTY_TYPE    => self::TYPE_TILE,
                                ],
                                'comment' => [
                                    self::PROPERTY_TYPE    => self::TYPE_STRING,
                                ],
                            ],
                        ],
                        'b' => [
                            self::PROPERTY_TYPE    => self::TYPE_JSON,
                            self::PROPERTY_SCHEMA  => [
                                'discard' => [
                                    self::PROPERTY_TYPE    => self::TYPE_TILE,
                                ],
                                'comment' => [
                                    self::PROPERTY_TYPE    => self::TYPE_STRING,
                                ],
                            ],
                        ],
                        'c' => [
                            self::PROPERTY_TYPE    => self::TYPE_JSON,
                            self::PROPERTY_SCHEMA  => [
                                'discard' => [
                                    self::PROPERTY_TYPE    => self::TYPE_TILE,
                                ],
                                'comment' => [
                                    self::PROPERTY_TYPE    => self::TYPE_STRING,
                                ],
                            ],
                        ],
                    ],
                ],
                'best_answer' => [
                    self::PROPERTY_TYPE    => self::TYPE_ABC,
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
            $value = null;
            if (isset($row[$fieldName])) {
                $value = $row[$fieldName];
            } elseif (isset($properties[self::PROPERTY_DEFAULT])) {
                $value = $properties[self::PROPERTY_DEFAULT];
            } else {
                $value = self::$DEFAULT[$properties[self::PROPERTY_TYPE]];
            }
            $this->data[$fieldName] = self::prepare($value, $properties);
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
                $data[$field] = json_encode($data[$field],  JSON_UNESCAPED_UNICODE);
            }
        }
        return $data;
    }

    /**
     * Создаёт копию себя, отличающуюся на указанные значения полей.
     *
     *  @param  [key => value]  $modifications
     *  @return self
    **/
    public function modify(array $modifications)
    {
        $modified = new self([]);
        foreach ($this->data as $key => $value) {
            $modified->data[$key] = $value;
        }
        foreach ($modifications as $key => $value) {
            if (! isset(self::$FIELD_PROPERTIES[$key])) {
                throw new Exception('Field "' . $key . '" is unknown', Exception::EXERCISE_MODIFY_FIELD_UNKNOWN);
            }
            $modified->data[$key] = self::prepare($value, self::$FIELD_PROPERTIES[$key]);
        }
    }

    // private //

    private function prepare ($value, $properties)
    {
        switch ($properties[self::PROPERTY_TYPE]) {
            case self::TYPE_ABC:
                if (! isset(self::$ABCS[$value])) {
                    if (isset($properties[self::PROPERTY_DEFAULT])) {
                        return $properties[self::PROPERTY_DEFAULT];
                    }
                    return self::$DEFAULT[$properties[self::PROPERTY_TYPE]];
                }
                return $value;

            case self::TYPE_BOOLEAN:
                return !! $value;

            case self::TYPE_KYOKU:
                if (! isset(self::$KYOKUS[$value])) {
                    if (isset($properties[self::PROPERTY_DEFAULT])) {
                        return $properties[self::PROPERTY_DEFAULT];
                    }
                    return self::$DEFAULT[$properties[self::PROPERTY_TYPE]];
                }
                return $value;

            case self::TYPE_INTEGER:
                return intval($value);

            case self::TYPE_POSITION:
                if (! isset(self::$WINDS[$value])) {
                    if (isset($properties[self::PROPERTY_DEFAULT])) {
                        return $properties[self::PROPERTY_DEFAULT];
                    }
                    return self::$DEFAULT[$properties[self::PROPERTY_TYPE]];
                }
                return $value;

            case self::TYPE_JSON:
                if (is_string ($value)) {
                    $decoded = json_decode($value, true);
                    if (is_array($decoded)) {
                        $unpacked = $decoded;
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
                    } elseif (isset($subproperties[self::PROPERTY_DEFAULT])) {
                        $subvalue = $subproperties[self::PROPERTY_DEFAULT];
                    } else {
                        $subvalue = self::$DEFAULT[$subproperties[self::PROPERTY_TYPE]];
                    }
                    $prepared[$field] = self::prepare($subvalue, $subproperties);
                }
                return $prepared;

            case self::TYPE_TILE:
                return new Tile($value);

            case self::TYPE_STRING:
                return trim(strval($value));

            default:
                throw new Exception('Unknown type "' . $properties[self::PROPERTY_TYPE] . '"', Exception::EXERCISE_TYPE_UNKNOWN);
        }
    }
}
