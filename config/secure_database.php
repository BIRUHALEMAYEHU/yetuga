<?php
/**
 * Secure Database Helper for Yetuga App
 * Prevents SQL injection using prepared statements
 */

require_once 'database.php';

/**
 * Secure database query with prepared statements
 * @param string $sql The SQL query with placeholders
 * @param array $params The parameters to bind
 * @param string $fetch_mode The fetch mode (fetch, fetchAll, rowCount)
 * @return mixed The query result
 */
function secureQuery($sql, $params = [], $fetch_mode = 'fetch') {
    try {
        $pdo = getDatabaseConnection();
        $stmt = $pdo->prepare($sql);
        
        if (!$stmt) {
            throw new Exception("Failed to prepare statement: " . $pdo->errorInfo()[2]);
        }
        
        $stmt->execute($params);
        
        switch ($fetch_mode) {
            case 'fetch':
                return $stmt->fetch(PDO::FETCH_ASSOC);
            case 'fetchAll':
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            case 'rowCount':
                return $stmt->rowCount();
            case 'lastInsertId':
                return $pdo->lastInsertId();
            default:
                return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        
    } catch (Exception $e) {
        error_log("Database query error: " . $e->getMessage());
        return false;
    }
}

/**
 * Secure insert operation
 * @param string $table The table name
 * @param array $data Associative array of column => value
 * @return int|false The inserted ID or false on failure
 */
function secureInsert($table, $data) {
    if (empty($data)) {
        return false;
    }
    
    $columns = array_keys($data);
    $placeholders = array_fill(0, count($columns), '?');
    
    $sql = "INSERT INTO {$table} (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $placeholders) . ")";
    $values = array_values($data);
    
    return secureQuery($sql, $values, 'lastInsertId');
}

/**
 * Secure update operation
 * @param string $table The table name
 * @param array $data Associative array of column => value
 * @param string $where_condition The WHERE condition with placeholders
 * @param array $where_params The WHERE parameters
 * @return int|false The number of affected rows or false on failure
 */
function secureUpdate($table, $data, $where_condition, $where_params = []) {
    if (empty($data)) {
        return false;
    }
    
    $set_clauses = [];
    foreach (array_keys($data) as $column) {
        $set_clauses[] = "{$column} = ?";
    }
    
    $sql = "UPDATE {$table} SET " . implode(', ', $set_clauses) . " WHERE {$where_condition}";
    $values = array_merge(array_values($data), $where_params);
    
    return secureQuery($sql, $values, 'rowCount');
}

/**
 * Secure delete operation
 * @param string $table The table name
 * @param string $where_condition The WHERE condition with placeholders
 * @param array $where_params The WHERE parameters
 * @return int|false The number of affected rows or false on failure
 */
function secureDelete($table, $where_condition, $where_params = []) {
    $sql = "DELETE FROM {$table} WHERE {$where_condition}";
    return secureQuery($sql, $where_params, 'rowCount');
}

/**
 * Secure select operation
 * @param string $table The table name
 * @param array $columns The columns to select (empty for all)
 * @param string $where_condition The WHERE condition with placeholders (optional)
 * @param array $where_params The WHERE parameters (optional)
 * @param string $order_by The ORDER BY clause (optional)
 * @param int $limit The LIMIT value (optional)
 * @param int $offset The OFFSET value (optional)
 * @return array|false The results or false on failure
 */
function secureSelect($table, $columns = [], $where_condition = '', $where_params = [], $order_by = '', $limit = null, $offset = null) {
    $cols = empty($columns) ? '*' : implode(', ', $columns);
    $sql = "SELECT {$cols} FROM {$table}";
    
    if (!empty($where_condition)) {
        $sql .= " WHERE {$where_condition}";
    }
    
    if (!empty($order_by)) {
        $sql .= " ORDER BY {$order_by}";
    }
    
    if ($limit !== null) {
        $sql .= " LIMIT ?";
        $where_params[] = $limit;
        
        if ($offset !== null) {
            $sql .= " OFFSET ?";
            $where_params[] = $offset;
        }
    }
    
    return secureQuery($sql, $where_params, 'fetchAll');
}

/**
 * Secure count operation
 * @param string $table The table name
 * @param string $where_condition The WHERE condition with placeholders (optional)
 * @param array $where_params The WHERE parameters (optional)
 * @return int|false The count or false on failure
 */
function secureCount($table, $where_condition = '', $where_params = []) {
    $sql = "SELECT COUNT(*) as count FROM {$table}";
    
    if (!empty($where_condition)) {
        $sql .= " WHERE {$where_condition}";
    }
    
    $result = secureQuery($sql, $where_params, 'fetch');
    return $result ? (int)$result['count'] : false;
}

/**
 * Secure search operation with LIKE
 * @param string $table The table name
 * @param array $search_columns The columns to search in
 * @param string $search_term The search term
 * @param array $additional_conditions Additional WHERE conditions (optional)
 * @param array $additional_params Additional parameters (optional)
 * @return array|false The search results or false on failure
 */
function secureSearch($table, $search_columns, $search_term, $additional_conditions = '', $additional_params = []) {
    if (empty($search_columns)) {
        return false;
    }
    
    $like_conditions = [];
    $search_params = [];
    
    foreach ($search_columns as $column) {
        $like_conditions[] = "{$column} LIKE ?";
        $search_params[] = "%{$search_term}%";
    }
    
    $where_condition = "(" . implode(' OR ', $like_conditions) . ")";
    
    if (!empty($additional_conditions)) {
        $where_condition .= " AND {$additional_conditions}";
        $search_params = array_merge($search_params, $additional_params);
    }
    
    return secureSelect($table, [], $where_condition, $search_params);
}

/**
 * Secure transaction wrapper
 * @param callable $callback The function to execute in transaction
 * @return mixed The result of the callback or false on failure
 */
function secureTransaction($callback) {
    try {
        $pdo = getDatabaseConnection();
        $pdo->beginTransaction();
        
        $result = $callback($pdo);
        
        if ($result === false) {
            $pdo->rollBack();
            return false;
        }
        
        $pdo->commit();
        return $result;
        
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log("Transaction error: " . $e->getMessage());
        return false;
    }
}

/**
 * Sanitize table and column names to prevent SQL injection
 * @param string $identifier The identifier to sanitize
 * @return string The sanitized identifier
 */
function sanitizeIdentifier($identifier) {
    // Only allow alphanumeric, underscore, and dot
    return preg_replace('/[^a-zA-Z0-9_.]/', '', $identifier);
}

/**
 * Build a safe WHERE clause from an associative array
 * @param array $conditions Associative array of column => value
 * @param array &$params Reference to parameters array
 * @return string The WHERE clause
 */
function buildWhereClause($conditions, &$params) {
    if (empty($conditions)) {
        return '';
    }
    
    $clauses = [];
    foreach ($conditions as $column => $value) {
        if (is_array($value)) {
            // Handle IN clauses
            $placeholders = str_repeat('?,', count($value) - 1) . '?';
            $clauses[] = sanitizeIdentifier($column) . " IN ({$placeholders})";
            $params = array_merge($params, $value);
        } else {
            $clauses[] = sanitizeIdentifier($column) . " = ?";
            $params[] = $value;
        }
    }
    
    return implode(' AND ', $clauses);
}
?>
