<?php

abstract class Model
{
    protected $db;
    protected $table;
    protected $primaryKey = 'id';
    protected $fillable = [];
    protected $hidden = [];
    protected $attributes = [];
    // Name of the database connection this model should use; null => default
    protected $connection = null;
    // Avoid spamming warnings about missing default DB config
    private static $defaultConfigWarned = false;

    public function __construct()
    {
        if ($this->connection) {
            $this->db = Database::getInstance($this->connection);
        } else {
            // Warn once if default DB config seems missing (no host/name/user)
            if (!self::$defaultConfigWarned) {
                $missing = [];
                if (!Config::get('DB_HOST')) $missing[] = 'DB_HOST';
                if (!Config::get('DB_NAME')) $missing[] = 'DB_NAME';
                if (!Config::get('DB_USER')) $missing[] = 'DB_USER';

                if (!empty($missing)) {
                    error_log(
                        'Database default connection may be missing config: ' .
                        implode(', ', $missing) .
                        '. Models without $connection will fail when accessing DB.'
                    );
                    self::$defaultConfigWarned = true;
                }
            }
            $this->db = Database::getInstance();
        }
    }

    // Expose the resolved Database wrapper instance
    public function getDb()
    {
        return $this->db;
    }

    // Expose the table name for repositories/services
    public function getTable()
    {
        return $this->table;
    }

    // Expose the connection name (null => default)
    public function getConnectionName()
    {
        return $this->connection;
    }

    public function find($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id";
        $result = $this->db->fetch($sql, ['id' => $id]);
        
        if ($result) {
            return $this->newInstance($result);
        }
        
        return null;
    }

    public function findOrFail($id)
    {
        $result = $this->find($id);
        if (!$result) {
            throw new Exception("Record not found");
        }
        return $result;
    }

    public function all()
    {
        $sql = "SELECT * FROM {$this->table}";
        $results = $this->db->fetchAll($sql);
        
        return array_map(function($row) {
            return $this->newInstance($row);
        }, $results);
    }

    public function where($column, $operator, $value = null)
    {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }

        $sql = "SELECT * FROM {$this->table} WHERE {$column} {$operator} :value";
        $results = $this->db->fetchAll($sql, ['value' => $value]);
        
        return array_map(function($row) {
            return $this->newInstance($row);
        }, $results);
    }

    public function create($data)
    {
        $data = $this->filterFillable($data);
        $id = $this->db->insert($this->table, $data);
        
        return $this->find($id);
    }

    public function update($data)
    {
        $data = $this->filterFillable($data);
        $id = $this->getAttribute($this->primaryKey);
        
        $this->db->update($this->table, $data, "{$this->primaryKey} = :id", ['id' => $id]);
        
        return $this->find($id);
    }

    public function delete()
    {
        $id = $this->getAttribute($this->primaryKey);
        return $this->db->delete($this->table, "{$this->primaryKey} = :id", ['id' => $id]);
    }

    public function save()
    {
        $id = $this->getAttribute($this->primaryKey);
        
        if ($id) {
            return $this->update($this->attributes);
        } else {
            $newRecord = $this->create($this->attributes);
            $this->attributes = $newRecord->attributes;
            return $newRecord;
        }
    }

    public function getAttribute($key)
    {
        return $this->attributes[$key] ?? null;
    }

    public function setAttribute($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
    }

    public function toArray()
    {
        $array = $this->attributes;
        
        // Remove hidden attributes
        foreach ($this->hidden as $hidden) {
            unset($array[$hidden]);
        }
        
        return $array;
    }

    public function toJson()
    {
        return json_encode($this->toArray());
    }

    public function __get($key)
    {
        return $this->getAttribute($key);
    }

    public function __set($key, $value)
    {
        $this->setAttribute($key, $value);
    }

    public function __isset($key)
    {
        return isset($this->attributes[$key]);
    }

    public function __unset($key)
    {
        unset($this->attributes[$key]);
    }

    protected function newInstance($attributes = [])
    {
        $instance = new static();
        $instance->setAttributes($attributes);
        return $instance;
    }

    protected function filterFillable($data)
    {
        if (empty($this->fillable)) {
            return $data;
        }

        return array_intersect_key($data, array_flip($this->fillable));
    }

    // Static methods for querying
    public static function __callStatic($method, $arguments)
    {
        $instance = new static();
        return call_user_func_array([$instance, $method], $arguments);
    }
}
