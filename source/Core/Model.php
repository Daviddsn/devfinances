<?php


namespace Source\Core;
use PDOException;
use Source\Support\Message;
use stdClass;


/**
 * Class Model
 * @package Source\Core
 */
abstract class Model
{

    /** @var object|null */
    protected ?object  $data;

    /** @var PDOException|null */
    protected ?PDOException $fail = null;

    /** @var string|null */
    protected ?string $query = null;

    /** @var mixed */
    protected  $params = null;

    /** @var string|null */
    protected  ?string $order = null;

    /** @var string|null */
    protected  ?string $limit = null;

    /** @var string|null */
    protected  ?string $offset = null;

    /**
     * @var string
     */
    protected static string $entity;
    /**
     * @var array
     */
    protected static array $protected;
    /**
     * @var array
     */
    protected static array $required;


    /**
     * @var Message|null
     */
    protected ?Message $message;

    /**
     * Model constructor.
     * @param string $entity
     * @param array $protected
     * @param array $required
     */
    public function __construct(string $entity,array $protected, array $required)
    {
        self::$entity = $entity;
        self::$protected = array_merge($protected,['created_at','updated_at']);
        self::$required = $required;
        $this->message = new Message();
    }


    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        if (empty($this->data)){
            $this->data = new stdClass();
        }

        $this->data->$name = $value;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        return ($this->data->$name ?? null);
    }

    /**
     * @param $name
     * @return bool
     */
    public function __isset($name): bool
    {
        return isset($this->data->$name);
    }

    /**
     * @return object|null
     */
    public function data(): ?object
    {
        return $this->data;
    }

    /**
     * @return PDOException|null
     */
    public function fail(): ?PDOException
    {
        return $this->fail;
    }

    /**
     * @return Message|null
     */
    public function message(): ?Message
    {
        return $this->message;
    }

    /**
     * @param string|null $terms
     * @param string|null $params
     * @param string $columns
     * @return Model|mixed
     */
    public function find(?string $terms = null ,?string $params = null,string $columns = "*") :Model
    {
        if($terms){
            $this->query = "SELECT {$columns} FROM ".static::$entity." WHERE {$terms}";
            parse_str($params,$this->params);
            return $this;
        }
        $this->query = "SELECT {$columns} FROM ".static::$entity;
        return $this;
    }

    /**
     * @param int $id
     * @param string $columns
     * @return mixed|Model|null
     */
    public function findById(int $id,string $columns = "*") :?Model
    {
        $find = $this->find("id =:id","id={$id}",$columns);
        return $find->fetch();

    }


    /**
     * @param string $orderBy
     * @return $this
     */
    public function order(string $orderBy) :Model
    {
        $this->order = " ORDER BY {$orderBy}";
        return $this;
    }


    /**
     * @param int $limit
     * @return $this
     */
    public function limit(int $limit) :Model
    {
        $this->limit = " LIMIT {$limit}";
        return $this;
    }


    /**
     * @param int $offset
     * @return $this
     */
    public function offset(int $offset) :Model
    {
        $this->offset = " OFFSET {$offset}";
        return $this;
    }

    /**
     * @param bool $all
     * @return array|mixed|null
     */
    public function fetch(bool $all = false)
    {
        try {
           $stmt =Connect::getInstance()->prepare($this->query . $this->order . $this->limit . $this->offset);
           $stmt->execute($this->params);
           if (!$stmt->rowCount()){
               return null;
           }
           if ($all){
               return $stmt->fetchAll(\PDO::FETCH_CLASS,static::class);
           }

           return $stmt->fetchObject(static::class);

        }catch (PDOException $exception){
            $this->fail = $exception;
            return null;
        }
    }

    /**
     * @param string $key
     * @return int
     */
    public function count(string $key = "id") :int
    {
        $stmt =Connect::getInstance()->prepare($this->query);
        $stmt->execute($this->params);
        return $stmt->rowCount();
    }

    /**
     * @param array $data
     * @return int|null
     */
    protected function create(array $data) :?int
    {

        try {
            $columns = implode(",",array_keys($data));
            $values = ":".implode(",:",array_keys($data));

            $stmt = Connect::getInstance()->prepare("INSERT INTO ".static::$entity." ({$columns}) VALUES ({$values})");
            $stmt->execute($this->filter($data));

            return Connect::getInstance()->lastInsertId();
        }catch (PDOException $exception){
            $this->fail = $exception;
            return null;
        }


    }


    /**
     * @param array $data
     * @param string $terms
     * @param string $params
     * @return int|null
     */
    protected function update(array $data,string $terms,string $params) :?int
    {
        try {
            $dataSet = [];
            foreach ($data as $bind => $value){
                $dataSet[] = "{$bind} =:{$bind}";
            }

            $dataSet = implode(",",$dataSet);
            parse_str($params,$params);

            $stmt = Connect::getInstance()->prepare("UPDATE ".static::$entity." SET {$dataSet} WHERE {$terms}");
            $stmt->execute($this->filter(array_merge($data,$params)));

            return ($stmt->rowCount() ?? 1);
        }catch (PDOException $exception){
            $this->fail = $exception;
            return null;
        }

    }


    /**
     * @param string $key
     * @param string $value
     * @return bool
     */
    public function delete(string $key,string $value) :bool
    {
        try {

           $stmt = Connect::getInstance()->prepare("DELETE FROM ".static::$entity ." WHERE {$key} =:key");
           $stmt->bindValue("key",$value,\PDO::PARAM_STR);
           $stmt->execute();

          return true;

        }catch (PDOException $exception){
            $this->fail = $exception;
            return false;
}
    }

    /**
     * @return array|null
     */
    protected function safe() :?array
    {
        $safe = (array)$this->data;
        foreach (static::$protected as $unset) {
            unset($safe[$unset]);
        }


        return $safe;
    }

    /**
     * @param array $data
     * @return array|null
     */
    protected function filter(array  $data) :?array
    {
        $filter = [];
        foreach ($data as $key => $value){
            $filter[$key] = (is_null($value) ? null : filter_var($value, FILTER_DEFAULT));
        }

        return $filter;
    }

    /**
     * @return bool
     */
    protected function required() :bool
    {
        $data =  (array)$this->data;
        foreach (static::$required as $field){
            if (empty($data[$field])){
                return false;
            }
        }
        return true;
    }

}