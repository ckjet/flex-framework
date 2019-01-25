<?php

namespace Hypilon\Database;

use Hypilon\DependencyInjection\Container;

class Model
{
    /**
     * @var string $table
     */
    protected $table;

    /**
     * @var array $attributes
     */
    protected $attributes;

    /**
     * @var string $key
     */
    protected $key;

    /**
     * @var MySQL $db
     */
    protected $db;

    /**
     * @var array $data
     */
    protected $data;

    public function __construct()
    {
        $this->db = Container::get('db');
    }

    public function get($attribute)
    {
        if (in_array($attribute, $this->attributes)) {
            return $this->data[$attribute];
        }
        return null;
    }

    public function set($attribute, $value)
    {
        if (in_array($attribute, $this->attributes)) {
            $this->data[$attribute] = $value;
        }
        return $this;
    }

    public function all()
    {
        return $this->data;
    }

    public function fromArray($data)
    {
        foreach ($data as $attribute => $value) {
            $this->set($attribute, $value);
        }
        return $this;
    }

    public function save()
    {
        if (!isset($this->data['id'])) {
            return $this;
        }
        $attributes = array_slice($this->attributes, 1);
        if (isset($this->data['id']) && $this->data['id'] > 0) {
            $i = 0;
            $query = "UPDATE `{$this->table}` SET";
            foreach ($attributes as $attribute) {
                $value = $this->data[$attribute] ?? '';
                $query .= " `{$attribute}` = '{$value}'" . ($i + 1 < sizeof($attributes) ? ',' : '');
                $i++;
            }
            $query .= " WHERE `{$this->key}` = :id";
            $this->db->execute($query, ['id' => $this->data['id']]);
            return $this;
        } else {
            $query = "INSERT INTO `{$this->table}` ";
            $query .= '(`' . join('`, `', $attributes)
                    . '`) VALUES (\'' . join('\', \'', array_slice($this->data, 1)) . '\')';
            $id = $this->db->execute($query);
            $this->data['id'] = $id;
            return $this;
        }
        return $this;
    }

    public static function findAll($returnArray = false)
    {
        $model = new static();
        $query = "SELECT * FROM `{$model->table}`";
        $results = $model->db->fetchAll($query);
        if($returnArray) {
            return $results;
        }
        $objects = [];
        foreach ($results as $result) {
            $object = new $model();
            self::fillModel($object, $result);
            $objects[] = $object;
        }
        return $objects;
    }

    public static function find($id, $key = false)
    {
        $model = new static();
        if (!$key) {
            $key = $model->key;
        }
        $query = "SELECT * FROM `{$model->table}` WHERE `{$key}` = :id";
        $result = $model->db->fetch($query, ['id' => $id]);
        self::fillModel($model, $result);
        return $model;
    }

    public static function findOneBy($condition = [], $sort = [])
    {
        $model = new static();
        $query = "SELECT * FROM `{$model->table}`";
        $query .= self::buildCondition($condition);
        $query .= self::buildSort($sort);
        return $model->db->fetch($query);
    }

    public static function findBy($condition = [], $sort = [])
    {
        $model = new static();
        $query = "SELECT * FROM `{$model->table}`";
        $query .= self::buildCondition($condition);
        $query .= self::buildSort($sort);
        $results = $model->db->fetchAll($query);
        $objects = [];
        foreach ($results as $result) {
            $object = new $model();
            self::fillModel($object, $result);
            $objects[] = $object;
        }
        return $objects;
    }

    public static function countBy($condition = [])
    {
        $model = new static();
        $query = "SELECT id FROM `{$model->table}`";
        $query .= self::buildCondition($condition);
        return $model->db->count($query);
    }

    private static function buildCondition($condition = [])
    {
        $conditionString = '';
        if (sizeof($condition)) {
            $conditionString .= ' WHERE';
            $i = 0;
            foreach ($condition as $field => $value) {
                if ($i > 0) {
                    $conditionString .= 'AND';
                }
                $conditionString .= " `{$field}` = '{$value}'";
                $i++;
            }
        }
        return $conditionString;
    }

    private static function buildSort($sort = [])
    {
        $sortString = '';
        if (sizeof($sort)) {
            $sortString .= ' ORDER BY';
            $i = 0;
            foreach ($sort as $field => $method) {
                $sortString .= " `{$field}` {$method}" . ($i + 1 < sizeof($sort) ? ',' : '');
                $i++;
            }
        }
        return $sortString;
    }

    private static function fillModel($model, $data)
    {
        foreach ($model->attributes as $attribute) {
            if (isset($data[$attribute])) {
                $model->data[$attribute] = $data[$attribute];
            }
        }
    }
}