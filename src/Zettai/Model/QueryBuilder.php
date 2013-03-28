<?php
namespace Zettai\Model;

class QueryBuilder
{
    // var //
    
    private $entity;
    private $service;
    
    private $select = [];
    private $where;
    private $orderBy = [];
    private $offset;
    private $limit;
    
    // public //
    
    public function __construct(ServiceInterface $service, EntityInterface $entity)
    {
        $this->service = $service;
        $this->entity  = $entity;
    }
    
    // public : clauses //
    
    /**
     *  @param  string | Expression(Expression)     $expression
     *  @return self
    **/
    public function select($expression)
    {
        // TODO
    }
    
    public function where($expression)
    {
        // TODO
    }
    
    public function orderBy($expression, $direction)
    {
        // TODO
    }
    
    public function offset($offset)
    {
        $this->offset = $offset;
    }
    
    public function limit($limit)
    {
        $this->limit = $limit;
    }
    
    // public : fetching //
    
    public function fetchAssoc($parameters = [])
    {
        // TODO
    }
    
    public function fetchAll($parameters = [])
    {
        // TODO
    }
    
    public function fetchColumn($parameters = [])
    {
        // TODO
    }
    
    // public : modifying //
    
    public function delete()
    {
        // TODO
    }
    
    public function insert()
    {
        // TODO
    }
    
    public function update()
    {
        // TODO
    }
}
