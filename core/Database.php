<?php

class Database
{
    private static $instance = null;
    private static $instances = [];

    private $connection;
    private $host;
    private $dbname;
    private $username;
    private $password;
    private $connectionName;

    private function __construct($name = null)
    {
        $this->connectionName = $name;

        $prefix = 'DB_';
        if (!empty($name) && $name !== 'default') {
            $prefix = 'DB_' . strtoupper($name) . '_';
        }

        $this->host = Config::get($prefix . 'HOST');
        $this->dbname = Config::get($prefix . 'NAME');
        $this->username = Config::get($prefix . 'USER');
        $this->password = Config::get($prefix . 'PASS');
    }

    public static function getInstance($name = null)
    {
        if ($name === null) {
            if (self::$instance === null) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        $key = $name === 'default' ? 'default' : strtolower($name);

        if (!isset(self::$instances[$key])) {
            self::$instances[$key] = new self($key);
        }

        return self::$instances[$key];
    }

    public static function connection($name = 'default')
    {
        return self::getInstance($name);
    }

    private function ensureConnected()
    {
        if ($this->connection !== null) {
            return;
        }

        $prefix = 'DB_';
        if (!empty($this->connectionName) && $this->connectionName !== 'default') {
            $prefix = 'DB_' . strtoupper($this->connectionName) . '_';
        }

        $missingConfigs = [];

        if (empty($this->host)) $missingConfigs[] = $prefix . 'HOST';
        if (empty($this->dbname)) $missingConfigs[] = $prefix . 'NAME';
        if (empty($this->username)) $missingConfigs[] = $prefix . 'USER';

        if (!empty($missingConfigs)) {
            $connectionInfo = $this->connectionName ? "connection '{$this->connectionName}'" : "default connection";

            $errorMsg = "Missing required database configuration for {$connectionInfo}: "
                . implode(', ', $missingConfigs);

            throw new Exception($errorMsg);
        }

        $this->connect();
    }

private function connect()
{
    try {

        $host = "127.0.0.1";
        $port = "3907";

        $dsn = "mysql:host=$host;port=$port;dbname={$this->dbname};charset=utf8mb4";

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        $this->connection = new PDO($dsn, $this->username, $this->password, $options);

    } catch (PDOException $e) {
        throw new Exception("Database connection failed: " . $e->getMessage());
    }
}
    }
    public function getConnection()
    {
        $this->ensureConnected();
        return $this->connection;
    }

    public function query($sql, $params = [])
    {
        $this->ensureConnected();

        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);

        return $stmt;
    }

    public function fetch($sql, $params = [])
    {
        return $this->query($sql, $params)->fetch();
    }

    public function fetchAll($sql, $params = [])
    {
        return $this->query($sql, $params)->fetchAll();
    }

    public function insert($table, $data)
    {
        $columns = implode(',', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));

        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";

        $this->query($sql, $data);

        return $this->lastInsertId();
    }

    public function update($table, $data, $where, $whereParams = [])
    {
        $set = [];

        foreach ($data as $column => $value) {
            $set[] = "{$column} = :{$column}";
        }

        $setClause = implode(', ', $set);

        $sql = "UPDATE {$table} SET {$setClause} WHERE {$where}";

        $params = array_merge($data, $whereParams);

        return $this->query($sql, $params);
    }

    public function delete($table, $where, $params = [])
    {
        $sql = "DELETE FROM {$table} WHERE {$where}";

        return $this->query($sql, $params);
    }

    public function beginTransaction()
    {
        $this->ensureConnected();
        return $this->connection->beginTransaction();
    }

    public function commit()
    {
        $this->ensureConnected();
        return $this->connection->commit();
    }

    public function rollback()
    {
        $this->ensureConnected();
        return $this->connection->rollback();
    }

    public function lastInsertId()
    {
        $this->ensureConnected();
        return $this->connection->lastInsertId();
    }

    private function __clone() {}

    public function __wakeup()
    {
        throw new Exception("Cannot unserialize singleton");
    }
}