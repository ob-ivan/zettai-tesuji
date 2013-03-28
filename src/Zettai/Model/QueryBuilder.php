<?php
namespace Zettai\Model;

class QueryBuilder
{
    // const //
    
    const ORDER_BY_KEY_EXPRESSION = __LINE__;
    const ORDER_BY_KEY_DIRECTION  = __LINE__;
    
    // var //
    
    private $entity;
    private $service;
    
    private $select = [];
    private $where;
    private $orderBy = [];
    private $offset = 0;
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
        $this->select[] = Expression::create($expression);
        return $this;
    }
    
    public function where($expression)
    {
        $this->where = Expression::create($expression);
        return $this;
    }
    
    public function orderBy($expression, $direction)
    {
        $this->orderBy[] = [
            self::ORDER_BY_KEY_EXPRESSION => Expression::create($expression),
            self::ORDER_BY_KEY_DIRECTION  => $direction,
        ];
        return $this;
    }
    
    public function offset($offset)
    {
        $this->offset = $offset;
        return $this;
    }
    
    public function limit($limit)
    {
        $this->limit = $limit;
        return $this;
    }
    
    // public : fetching //
    
    public function fetchAll($parameters = [])
    {
        return $this->service->fetchAll($this->buildQuery(), $parameters);
    }
    
    public function fetchAssoc($parameters = [])
    {
        return $this->service->fetchAssoc($this->buildQuery(), $parameters);
    }
    
    public function fetchColumn($parameters = [])
    {
        return $this->service->fetchColumn($this->buildQuery(), $parameters);
    }
    
    // public : modifying //
    
    public function delete($filter)
    {
        $this->service->delete($this->getTableName(), $filter);
    }
    
    public function insert($data)
    {
        $this->service->insert($this->getTableName(), $data);
    }
    
    public function update($data, $filter)
    {
        $this->service->update($this->getTableName(), $data, $filter);
    }
    
    // private //
    
    private function buildQuery()
    {
        // SELECT
        $select = [];
        foreach ($this->select as $expression) {
            $select[] = $expression->toString();
        }
        
        // FROM
        $from = $this->getTableName();
        
        // WHERE
        $where = $this->where ? $this->where->toString() : '';
        
        // ORDER BY
        $orderBy = [];
        foreach ($this->orderBy as $orderByPair) {
            $orderBy[] =
                $orderByPair[self::ORDER_BY_KEY_EXPRESSION]->toString() . ' ' . 
                $orderByPair[self::ORDER_BY_KEY_DIRECTION];
        }
        
        // LIMIT
        $limit = $this->limit > 0 ? $this->offset . ', ' . $this->limit : '';
        
        // clauses
        $clauses = [];
        $clauses[] = 'SELECT ' . implode(', ', $select);
        $clauses[] = 'FROM ' . $from;
        if (! empty($where)) {
            $clauses[] = 'WHERE ' . $where;
        }
        if (! empty($orderBy)) {
            $clauses[] = 'ORDER BY ' . implode(', ', $orderBy);
        }
        if (! empty($limit)) {
            $clauses[] = 'LIMIT ' . $limit;
        }
        return implode(' ', $clauses);
    }
    
    private function getTableName()
    {
        return $this->service->getTableName($this->entity);
    }
}
