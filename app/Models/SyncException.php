<?php
declare(strict_types=1);

require_once __DIR__ . '/../../core/Database.php';

class SyncException {
    private Database $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Log a sync exception to the database
     */
    public static function log(
        int $userId, 
        string $category, 
        string $entityType, 
        ?string $localId, 
        ?string $uuid, 
        ?int $routeId, 
        string $errorMessage, 
        array $payload
    ): int {
        $db = new Database();
        
        // Ensure sync_exceptions table exists
        try {
            $db->query("CREATE TABLE IF NOT EXISTS sync_exceptions (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                category ENUM('unmapped_product', 'unknown_customer', 'route_not_found', 'duplicate_event', 'coa_not_configured', 'stock_conflict', 'other') NOT NULL DEFAULT 'other',
                entity_type VARCHAR(50) NOT NULL,
                local_id VARCHAR(100) NULL,
                uuid VARCHAR(255) NULL,
                route_id INT NULL,
                error_message TEXT NOT NULL,
                payload LONGTEXT NOT NULL,
                status ENUM('pending', 'resolved', 'ignored') NOT NULL DEFAULT 'pending',
                resolution_notes TEXT NULL,
                resolved_by INT NULL,
                resolved_at DATETIME NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                KEY idx_user_id (user_id),
                KEY idx_category (category),
                KEY idx_status (status),
                KEY idx_entity_type (entity_type),
                KEY idx_uuid (uuid)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
            $db->execute();
        } catch (Exception $e) {}

        $validCategories = ['unmapped_product', 'unknown_customer', 'route_not_found', 'duplicate_event', 'coa_not_configured', 'stock_conflict', 'other'];
        if (!in_array($category, $validCategories, true)) {
            $category = 'other';
        }

        $db->query("INSERT INTO sync_exceptions (user_id, category, entity_type, local_id, uuid, route_id, error_message, payload, status) 
                    VALUES (:uid, :cat, :etype, :lid, :uuid, :rid, :msg, :payload, 'pending')");
        $db->bind(':uid', $userId);
        $db->bind(':cat', $category);
        $db->bind(':etype', $entityType);
        $db->bind(':lid', $localId);
        $db->bind(':uuid', $uuid);
        $db->bind(':rid', $routeId);
        $db->bind(':msg', $errorMessage);
        $db->bind(':payload', json_encode($payload));
        $db->execute();

        $insertId = intval($db->lastInsertId());
        error_log("Sync Exception Logged [#{$insertId}]: User={$userId}, Category={$category}, Entity={$entityType}, Msg={$errorMessage}");
        return $insertId;
    }

    /**
     * Retrieve pending sync exceptions
     */
    public function getPendingExceptions(?string $category = null, int $limit = 100): array {
        $sql = "SELECT se.*, u.username as rep_username 
                FROM sync_exceptions se 
                LEFT JOIN users u ON se.user_id = u.id 
                WHERE se.status = 'pending'";
        if (!empty($category)) {
            $sql .= " AND se.category = :category";
        }
        $sql .= " ORDER BY se.created_at DESC LIMIT " . intval($limit);
        
        $this->db->query($sql);
        if (!empty($category)) {
            $this->db->bind(':category', $category);
        }
        return $this->db->resultSet() ?: [];
    }

    /**
     * Get exception count by category
     */
    public function getCategoryCounts(): array {
        $this->db->query("SELECT category, COUNT(*) as count FROM sync_exceptions WHERE status = 'pending' GROUP BY category");
        $rows = $this->db->resultSet() ?: [];
        $counts = [
            'unmapped_product' => 0,
            'unknown_customer' => 0,
            'route_not_found' => 0,
            'duplicate_event' => 0,
            'coa_not_configured' => 0,
            'stock_conflict' => 0,
            'other' => 0,
            'total' => 0
        ];
        foreach ($rows as $r) {
            $counts[$r->category] = intval($r->count);
            $counts['total'] += intval($r->count);
        }
        return $counts;
    }

    /**
     * Get exception by ID
     */
    public function getById(int $id) {
        $this->db->query("SELECT se.*, u.username as rep_username FROM sync_exceptions se LEFT JOIN users u ON se.user_id = u.id WHERE se.id = :id LIMIT 1");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    /**
     * Mark exception resolved
     */
    public function resolveException(int $id, int $resolvedBy, string $notes = 'Resolved and replayed'): bool {
        $this->db->query("UPDATE sync_exceptions SET status = 'resolved', resolution_notes = :notes, resolved_by = :rby, resolved_at = NOW() WHERE id = :id");
        $this->db->bind(':notes', $notes);
        $this->db->bind(':rby', $resolvedBy);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * Mark exception ignored
     */
    public function ignoreException(int $id, int $resolvedBy, string $notes = 'Ignored by admin'): bool {
        $this->db->query("UPDATE sync_exceptions SET status = 'ignored', resolution_notes = :notes, resolved_by = :rby, resolved_at = NOW() WHERE id = :id");
        $this->db->bind(':notes', $notes);
        $this->db->bind(':rby', $resolvedBy);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}
