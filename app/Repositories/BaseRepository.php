<?php

namespace App\Repositories;

abstract class BaseRepository
{
    protected $db;
    protected $table;
    protected $model;

    public function __construct()
    {
        // If a model class is specified, use it to determine connection and table
        if ($this->model) {
            $model = new $this->model();
            // Prefer the model's resolved DB connection
            $this->db = $model->getDb();
            // If repository's $table is not set, derive from model
            if (empty($this->table)) {
                $this->table = $model->getTable();
            }
        }
        // Fallback to default DB if model wasn't provided (or for raw repositories)
        if (!$this->db) {
            $this->db = \Database::getInstance();
        }
    }

    public function getAll($confirm = false, $maxRow = 5)
    {
        $sql = "SELECT * FROM {$this->table} ORDER BY id DESC";
        if (!$confirm) $sql .= " LIMIT " . $maxRow;;
        $results = $this->db->fetchAll($sql);
        return array_map(function ($row) {
            return $this->newModelInstance($row);
        }, $results);
    }

    public function findById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id";
        $result = $this->db->fetch($sql, ['id' => $id]);

        if ($result) {
            return $this->newModelInstance($result);
        }

        return null;
    }

    public function findBy($column, $value)
    {
        // Validate column name to prevent SQL injection
        if (!$this->isValidColumnName($column)) {
            throw new \InvalidArgumentException("Invalid column name: {$column}");
        }

        $sql = "SELECT * FROM {$this->table} WHERE `{$column}` = :value";
        $result = $this->db->fetch($sql, ['value' => $value]);

        if ($result) {
            return $this->newModelInstance($result);
        }

        return null;
    }

    public function findWhere($conditions)
    {
        $whereClause = [];
        $params = [];

        foreach ($conditions as $column => $value) {
            // Validate column name to prevent SQL injection
            if (!$this->isValidColumnName($column)) {
                throw new \InvalidArgumentException("Invalid column name: {$column}");
            }
            $whereClause[] = "`{$column}` = :{$column}";
            $params[$column] = $value;
        }

        $whereClause = implode(' AND ', $whereClause);
        $sql = "SELECT * FROM {$this->table} WHERE {$whereClause}";

        $results = $this->db->fetchAll($sql, $params);

        return array_map(function ($row) {
            return $this->newModelInstance($row);
        }, $results);
    }

    public function create($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');

        $id = $this->db->insert($this->table, $data);

        if ($id) {
            return $this->findById($id);
        }

        return null;
    }

    public function update($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');

        $result = $this->db->update($this->table, $data, "id = :id", ['id' => $id]);

        if ($result) {
            return $this->findById($id);
        }

        return null;
    }

    public function delete($id)
    {
        return $this->db->delete($this->table, "id = :id", ['id' => $id]);
    }

    public function count()
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table}";
        $result = $this->db->fetch($sql);
        return $result['count'];
    }

    public function countWhere($conditions)
    {
        $whereClause = [];
        $params = [];

        foreach ($conditions as $column => $value) {
            // Validate column name to prevent SQL injection
            if (!$this->isValidColumnName($column)) {
                throw new \InvalidArgumentException("Invalid column name: {$column}");
            }
            $whereClause[] = "`{$column}` = :{$column}";
            $params[$column] = $value;
        }

        $whereClause = implode(' AND ', $whereClause);
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE {$whereClause}";

        $result = $this->db->fetch($sql, $params);
        return $result['count'];
    }

    public function exists($id)
    {
        return $this->findById($id) !== null;
    }

    public function paginate($page = 1, $perPage = 15)
    {
        $offset = ($page - 1) * $perPage;

        $sql = "SELECT * FROM {$this->table} ORDER BY id DESC LIMIT :limit OFFSET :offset";
        $results = $this->db->fetchAll($sql, [
            'limit' => $perPage,
            'offset' => $offset
        ]);

        $items = array_map(function ($row) {
            return $this->newModelInstance($row);
        }, $results);

        $total = $this->count();
        $totalPages = ceil($total / $perPage);

        return [
            'data' => $items,
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'total_pages' => $totalPages,
            'has_next' => $page < $totalPages,
            'has_prev' => $page > 1
        ];
    }

    public function search($query, $columns = [])
    {
        if (empty($columns)) {
            $columns = ['name', 'email']; // Default search columns
        }

        $whereClause = [];
        $params = [];

        foreach ($columns as $index => $column) {
            // Validate column name to prevent SQL injection
            if (!$this->isValidColumnName($column)) {
                throw new \InvalidArgumentException("Invalid column name: {$column}");
            }
            $whereClause[] = "`{$column}` LIKE :search{$index}";
            $params["search{$index}"] = "%{$query}%";
        }

        $whereClause = implode(' OR ', $whereClause);
        $sql = "SELECT * FROM {$this->table} WHERE {$whereClause} ORDER BY id DESC";

        $results = $this->db->fetchAll($sql, $params);

        return array_map(function ($row) {
            return $this->newModelInstance($row);
        }, $results);
    }

    protected function newModelInstance($data)
    {
        if ($this->model) {
            $model = new $this->model();
            $model->setAttributes($data);
            return $model;
        }

        return (object) $data;
    }

    public function beginTransaction()
    {
        return $this->db->beginTransaction();
    }

    public function commit()
    {
        return $this->db->commit();
    }

    public function rollback()
    {
        return $this->db->rollback();
    }

    public function raw($sql, $params = [])
    {
        return $this->db->fetchAll($sql, $params);
    }

    public function rawFirst($sql, $params = [])
    {
        return $this->db->fetch($sql, $params);
    }

    /**
     * Validate column name to prevent SQL injection
     * Only allow alphanumeric characters, underscores, and backticks
     */
    protected function isValidColumnName($column)
    {
        // Remove backticks if present for validation
        $cleanColumn = str_replace('`', '', $column);

        // Check if column name contains only valid characters
        return preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $cleanColumn) === 1;
    }

    /**
     * Build a compact pages array with ellipsis markers compatible with views.
     */
    protected function buildPaginationPages(int $currentPage, int $totalPages): array
    {
        $pages = [];

        if ($totalPages <= 1) {
            return [['type' => 'page', 'page' => 1, 'is_current' => true]];
        }

        if ($totalPages <= 7) {
            for ($p = 1; $p <= $totalPages; $p++) {
                $pages[] = ['type' => 'page', 'page' => $p, 'is_current' => $p === $currentPage];
            }
            return $pages;
        }

        $pages[] = ['type' => 'page', 'page' => 1, 'is_current' => $currentPage === 1];

        if ($currentPage > 4) {
            $pages[] = ['type' => 'ellipsis'];
        }

        $start = max(2, $currentPage - 1);
        $end = min($totalPages - 1, $currentPage + 1);

        for ($p = $start; $p <= $end; $p++) {
            $pages[] = ['type' => 'page', 'page' => $p, 'is_current' => $p === $currentPage];
        }

        if ($currentPage < $totalPages - 3) {
            $pages[] = ['type' => 'ellipsis'];
        }

        $pages[] = ['type' => 'page', 'page' => $totalPages, 'is_current' => $currentPage === $totalPages];

        return $pages;
    }
}
