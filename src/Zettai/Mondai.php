<?php
/**
 * Рекорд одной задачи. Пока что пассивен.
**/
namespace Zettai;

class Mondai
{
    // const //
    
    private static $fields = [
        'mondai_id' => 1,
        'title'     => 1,
        'is_hidden' => 1,
        'content'   => 1,
    ];
    
    // var //
    
    private $mondai_id  = 0;
    private $title      = '';
    private $is_hidden  = true;
    private $content    = '';
    
    // public //
    
    public function __construct(array $row)
    {
        if (isset($row['mondai_id'])) {
            $this->mondai_id = intval ($row['mondai_id']);
        }
        if (isset($row['title'])) {
            $this->title     = trim (strval ($row['title']));
        }
        if (isset($row['is_hidden'])) {
            $this->is_hidden = !! ($row['is_hidden']);
        }
        if (isset($row['content'])) {
            $this->content   = trim (strval ($row['content']));
        }
    }
    
    public function __get($name)
    {
        if (isset (self::$fields[$name])) {
            return $this->$name;
        }
        throw new Exception(
            'Unknown field "' . $name . '" for record "' . __CLASS__ . '"',
            Exception::MONDAI_GET_FIELD_UNKNOWN
        );
    }
    
    public function __isset($name)
    {
        return isset(self::$fields[$name]);
    }
}
