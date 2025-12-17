<?php
/**
 * JSON DATABASE CLASS
 * Simula um banco de dados usando arquivos JSON
 * Compatível com a interface PDO para fácil migração
 */

class JSONDatabase {
    private $dataPath;
    private $tables = [];
    private $lastInsertId = 0;
    private $lastQuery = '';
    private $lastParams = [];
    private $lastStatement = null;
    
    // Tabelas disponíveis
    private $availableTables = [
        'users',
        'admin_users',
        'tours',
        'services',
        'blog_posts',
        'blog_categories',
        'testimonials',
        'faqs',
        'hero_slides',
        'hero_slide_items',
        'features',
        'gallery_photos',
        'page_content',
        'page_sections',
        'page_section_items',
        'site_settings',
        'seo_metadata',
        'section_backgrounds',
        'contact_submissions',
        'bookings',
        'clients',
        'virtual_tour_locations',
        'getyourguide_widgets',
    ];
    
    public function __construct($dataPath) {
        $this->dataPath = rtrim($dataPath, '/\\') . '/';
        $this->initializeTables();
    }
    
    /**
     * Inicializar ou carregar todas as tabelas
     */
    private function initializeTables() {
        foreach ($this->availableTables as $table) {
            $filepath = $this->dataPath . $table . '.json';
            
            if (file_exists($filepath)) {
                $json = file_get_contents($filepath);
                $this->tables[$table] = json_decode($json, true) ?: [];
            } else {
                // Criar arquivo vazio
                $this->tables[$table] = [];
                $this->saveTable($table);
            }
        }
    }
    
    /**
     * Salvar tabela em arquivo JSON
     */
    private function saveTable($table) {
        $filepath = $this->dataPath . $table . '.json';
        file_put_contents($filepath, json_encode($this->tables[$table], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
    
    /**
     * Simula PDO::query()
     */
    public function query($sql) {
        $this->lastQuery = $sql;
        $this->lastParams = [];
        $stmt = new JSONStatement($this, $sql, [], true);
        // Executar automaticamente para query()
        $stmt->execute([]);
        return $stmt;
    }
    
    /**
     * Simula PDO::prepare()
     */
    public function prepare($sql) {
        $this->lastQuery = $sql;
        $this->lastStatement = new JSONStatement($this, $sql, [], false);
        return $this->lastStatement;
    }
    
    /**
     * Executar SQL diretamente (para compatibilidade)
     */
    public function execute($sql, $params = []) {
        $stmt = $this->prepare($sql);
        return $stmt->execute($params);
    }
    
    /**
     * Obter o ID da última inserção
     */
    public function lastInsertId() {
        return $this->lastInsertId;
    }
    
    /**
     * Definir o ID da última inserção
     */
    public function setLastInsertId($id) {
        $this->lastInsertId = $id;
    }
    
    /**
     * Executar query SELECT
     */
    public function executeSelect($sql, $params = []) {
        // Parsear SQL SELECT
        preg_match('/FROM\s+(\w+)/i', $sql, $matches);
        if (!$matches) return [];
        
        $table = $matches[1];
        if (!isset($this->tables[$table])) return [];
        
        $data = $this->tables[$table];
        
        // Aplicar WHERE
        if (preg_match('/WHERE\s+(.+?)(?:ORDER|GROUP|LIMIT|$)/i', $sql, $matches)) {
            $whereClause = $matches[1];
            $data = $this->applyWhere($data, $whereClause, $params);
        }
        
        // Aplicar ORDER BY
        if (preg_match('/ORDER\s+BY\s+(\w+)\s+(ASC|DESC)?/i', $sql, $matches)) {
            $orderBy = $matches[1];
            $direction = strtoupper($matches[2] ?? 'ASC');
            $data = $this->applyOrderBy($data, $orderBy, $direction);
        }
        
        // Aplicar LIMIT
        if (preg_match('/LIMIT\s+(\d+)(?:,\s*(\d+))?/i', $sql, $matches)) {
            $limit = $matches[1];
            $offset = $matches[2] ?? 0;
            $data = array_slice($data, $offset, $limit);
        }
        
        return $data;
    }
    
    /**
     * Aplicar cláusula WHERE
     */
    private function applyWhere($data, $whereClause, $params = []) {
        // Suporta: column = ?, column > ?, column < ?, column IN (?), etc
        $filtered = [];
        $paramIndex = 0;
        
        foreach ($data as $row) {
            if ($this->matchesWhere($row, $whereClause, $params, $paramIndex)) {
                $filtered[] = $row;
            }
        }
        
        return $filtered;
    }
    
    /**
     * Verificar se linha corresponde ao WHERE
     */
    private function matchesWhere($row, $whereClause, $params, &$paramIndex) {
        // Casos simples: column = ?
        if (preg_match_all('/(\w+)\s*=\s*\?/i', $whereClause, $matches)) {
            foreach ($matches[1] as $column) {
                if (!isset($params[$paramIndex]) || ($row[$column] ?? null) != $params[$paramIndex]) {
                    return false;
                }
                $paramIndex++;
            }
            return true;
        }
        
        // Outros casos simples
        if (strpos($whereClause, '?') === false) {
            // Sem parâmetros - avaliar como expressão
            return true;
        }
        
        return true;
    }
    
    /**
     * Aplicar ORDER BY
     */
    private function applyOrderBy($data, $column, $direction = 'ASC') {
        usort($data, function($a, $b) use ($column, $direction) {
            $valA = $a[$column] ?? '';
            $valB = $b[$column] ?? '';
            
            if ($direction === 'DESC') {
                return $valB <=> $valA;
            }
            return $valA <=> $valB;
        });
        return $data;
    }
    
    /**
     * Executar INSERT
     */
    public function executeInsert($sql, $params = []) {
        preg_match('/INTO\s+(\w+)\s*\(([^)]+)\)/i', $sql, $matches);
        if (!$matches) return false;
        
        $table = $matches[1];
        $columns = array_map('trim', explode(',', $matches[2]));
        
        if (!isset($this->tables[$table])) {
            $this->tables[$table] = [];
        }
        
        // Criar registro
        $record = [];
        foreach ($columns as $i => $column) {
            $record[$column] = $params[$i] ?? null;
        }
        
        // Gerar ID
        $maxId = 0;
        foreach ($this->tables[$table] as $row) {
            if (isset($row['id']) && $row['id'] > $maxId) {
                $maxId = $row['id'];
            }
        }
        $record['id'] = $maxId + 1;
        
        // Adicionar timestamp se existir
        if (in_array('created_at', $columns) && !isset($record['created_at'])) {
            $record['created_at'] = date('Y-m-d H:i:s');
        }
        
        $this->tables[$table][] = $record;
        $this->setLastInsertId($record['id']);
        $this->saveTable($table);
        
        return true;
    }
    
    /**
     * Executar UPDATE
     */
    public function executeUpdate($sql, $params = []) {
        preg_match('/UPDATE\s+(\w+)\s+SET\s+(.+?)\s+WHERE/i', $sql, $matches);
        if (!$matches) return false;
        
        $table = $matches[1];
        $setPart = $matches[2];
        
        // Parse SET clause
        $setColumns = [];
        preg_match_all('/(\w+)\s*=\s*\?/i', $setPart, $setMatches);
        foreach ($setMatches[1] as $i => $column) {
            $setColumns[$column] = $params[$i];
        }
        
        // Parse WHERE clause
        preg_match('/WHERE\s+(.+)$/i', $sql, $whereMatches);
        $whereClause = $whereMatches[1] ?? '';
        
        // Encontrar parâmetro WHERE (normalmente no final)
        $whereValue = end($params);
        
        if (!isset($this->tables[$table])) {
            return false;
        }
        
        // Atualizar registros
        $updated = false;
        foreach ($this->tables[$table] as &$row) {
            if (preg_match('/id\s*=\s*\?/i', $whereClause) && $row['id'] == $whereValue) {
                foreach ($setColumns as $column => $value) {
                    $row[$column] = $value;
                }
                if (!isset($row['updated_at']) || strpos($sql, 'updated_at') !== false) {
                    $row['updated_at'] = date('Y-m-d H:i:s');
                }
                $updated = true;
            }
        }
        
        if ($updated) {
            $this->saveTable($table);
        }
        
        return $updated;
    }
    
    /**
     * Executar DELETE
     */
    public function executeDelete($sql, $params = []) {
        preg_match('/DELETE\s+FROM\s+(\w+)\s+WHERE/i', $sql, $matches);
        if (!$matches) return false;
        
        $table = $matches[1];
        
        preg_match('/WHERE\s+(.+)$/i', $sql, $whereMatches);
        $whereClause = $whereMatches[1] ?? '';
        $whereValue = end($params);
        
        if (!isset($this->tables[$table])) {
            return false;
        }
        
        $originalCount = count($this->tables[$table]);
        
        // Deletar registros
        $this->tables[$table] = array_filter($this->tables[$table], function($row) use ($whereClause, $whereValue) {
            if (preg_match('/id\s*=\s*\?/i', $whereClause) && $row['id'] == $whereValue) {
                return false;
            }
            return true;
        });
        
        if (count($this->tables[$table]) < $originalCount) {
            // Reindexar array
            $this->tables[$table] = array_values($this->tables[$table]);
            $this->saveTable($table);
            return true;
        }
        
        return false;
    }
    
    /**
     * Obter tabela inteira
     */
    public function getTable($table) {
        return $this->tables[$table] ?? [];
    }
    
    /**
     * Contar registros
     */
    public function countRecords($table) {
        return count($this->tables[$table] ?? []);
    }
    
    /**
     * Encontrar registro por ID
     */
    public function findById($table, $id) {
        $records = $this->tables[$table] ?? [];
        foreach ($records as $record) {
            if (($record['id'] ?? null) == $id) {
                return $record;
            }
        }
        return null;
    }
    
    /**
     * Inserir novo registro
     */
    public function insert($table, $data) {
        if (!isset($this->tables[$table])) {
            $this->tables[$table] = [];
        }
        
        // Gerar ID
        $maxId = 0;
        foreach ($this->tables[$table] as $row) {
            if (isset($row['id']) && $row['id'] > $maxId) {
                $maxId = $row['id'];
            }
        }
        $data['id'] = $maxId + 1;
        
        // Adicionar timestamps
        if (!isset($data['created_at'])) {
            $data['created_at'] = date('Y-m-d H:i:s');
        }
        if (!isset($data['updated_at'])) {
            $data['updated_at'] = date('Y-m-d H:i:s');
        }
        
        $this->tables[$table][] = $data;
        $this->saveTable($table);
        $this->setLastInsertId($data['id']);
        
        return $data['id'];
    }
    
    /**
     * Atualizar registro
     */
    public function update($table, $id, $data) {
        if (!isset($this->tables[$table])) {
            return false;
        }
        
        // Adicionar timestamp de atualização
        if (!isset($data['updated_at'])) {
            $data['updated_at'] = date('Y-m-d H:i:s');
        }
        
        foreach ($this->tables[$table] as &$record) {
            if (($record['id'] ?? null) == $id) {
                foreach ($data as $key => $value) {
                    $record[$key] = $value;
                }
                $this->saveTable($table);
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Deletar registro
     */
    public function delete($table, $id) {
        if (!isset($this->tables[$table])) {
            return false;
        }
        
        $originalCount = count($this->tables[$table]);
        
        $this->tables[$table] = array_filter($this->tables[$table], function($record) use ($id) {
            return ($record['id'] ?? null) != $id;
        });
        
        if (count($this->tables[$table]) < $originalCount) {
            $this->tables[$table] = array_values($this->tables[$table]);
            $this->saveTable($table);
            return true;
        }
        
        return false;
    }
}

/**
 * JSONStatement - Simula PDOStatement
 */
class JSONStatement {
    private $database;
    private $sql;
    private $params;
    private $isQuery;
    private $result;
    private $resultIndex = 0;
    
    public function __construct($database, $sql, $params, $isQuery) {
        $this->database = $database;
        $this->sql = $sql;
        $this->params = $params;
        $this->isQuery = $isQuery;
        $this->result = [];
    }
    
    public function execute($params = []) {
        $this->params = $params;
        
        if (stripos($this->sql, 'SELECT') === 0) {
            $this->result = $this->database->executeSelect($this->sql, $this->params);
        } elseif (stripos($this->sql, 'INSERT') === 0) {
            return $this->database->executeInsert($this->sql, $this->params);
        } elseif (stripos($this->sql, 'UPDATE') === 0) {
            return $this->database->executeUpdate($this->sql, $this->params);
        } elseif (stripos($this->sql, 'DELETE') === 0) {
            return $this->database->executeDelete($this->sql, $this->params);
        }
        
        return true;
    }
    
    public function fetch($fetchMode = null) {
        if ($this->resultIndex < count($this->result)) {
            return $this->result[$this->resultIndex++];
        }
        return false;
    }
    
    public function fetchAll($fetchMode = null) {
        return $this->result;
    }
    
    public function fetchColumn($column = 0) {
        if (isset($this->result[0])) {
            $row = $this->result[0];
            if (is_numeric($column)) {
                return array_values($row)[$column] ?? null;
            }
            return $row[$column] ?? null;
        }
        return false;
    }
    
    public function rowCount() {
        return count($this->result);
    }
}

?>
