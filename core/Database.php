<?php

class Database
{
    // Backward-compatible default singleton instance (no name provided)
    private static $instance = null;
    // Multiple named instances storage
    private static $instances = [];

    private $connection;
    private $host;
    private $dbname;
    private $username;
    private $password;
    private $connectionName; // Store connection name for error messages

    private function __construct($name = null)
    {
        // Store connection name for error messages
        $this->connectionName = $name;

        // Resolve configuration keys based on connection name
        $prefix = 'DB_';
        if (!empty($name) && $name !== 'default') {
            $prefix = 'DB_' . strtoupper($name) . '_';
        }

        // Get database configuration from environment
        $this->host = Config::get($prefix . 'HOST');
        $this->dbname = Config::get($prefix . 'NAME');
        $this->username = Config::get($prefix . 'USER');
        $this->password = Config::get($prefix . 'PASS');

        // Don't connect immediately - use lazy connection
        // Connection will be established when actually needed
    }

    // Backward compatible method; supports optional named connection
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

    // Convenience alias similar to many frameworks
    public static function connection($name = 'default')
    {
        return self::getInstance($name);
    }

    /**
     * Ensure database connection is established
     * Validates configuration and connects only when needed
     */
    private function ensureConnected()
    {
        // If already connected, return early
        if ($this->connection !== null) {
            return;
        }

        // Determine prefix for error messages
        $prefix = 'DB_';
        if (!empty($this->connectionName) && $this->connectionName !== 'default') {
            $prefix = 'DB_' . strtoupper($this->connectionName) . '_';
        }

        // Validate required database configuration
        $missingConfigs = [];
        if (empty($this->host)) $missingConfigs[] = $prefix . 'HOST';
        if (empty($this->dbname)) $missingConfigs[] = $prefix . 'NAME';
        if (empty($this->username)) $missingConfigs[] = $prefix . 'USER';
        // Note: PASS can be empty for some database setups

        if (!empty($missingConfigs)) {
            $connectionInfo = $this->connectionName ? "connection '{$this->connectionName}'" : "default connection";
            $errorMsg = "Missing required database configuration for {$connectionInfo}: " . implode(', ', $missingConfigs) . 
                       ". Please check your .env file or ensure your model specifies a valid connection.";
            error_log("Database Configuration Error: " . $errorMsg);
            throw new Exception($errorMsg);
        }

        $this->connect();
    }

    private function connect()
    {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4";
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

    public function getConnection()
    {
        $this->ensureConnected();
        return $this->connection;
    }

    public function query($sql, $params = [])
    {
        $this->ensureConnected();
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            throw new Exception("Query failed: " . $e->getMessage());
        }
    }

    public function fetch($sql, $params = [])
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetch();
    }

    public function fetchAll($sql, $params = [])
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
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
        $setClause = [];
        foreach (array_keys($data) as $column) {
            $setClause[] = "{$column} = :{$column}";
        }
        $setClause = implode(', ', $setClause);
        
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

    // Prevent cloning
    private function __clone() {}

    // Prevent unserialization
    public function __wakeup()
    {
        throw new Exception("Cannot unserialize singleton");
    }
}
