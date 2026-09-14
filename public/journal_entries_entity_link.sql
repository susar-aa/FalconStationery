ALTER TABLE journal_entries 
ADD COLUMN entity_type ENUM('Customer', 'Vendor') NULL DEFAULT NULL AFTER created_by,
ADD COLUMN entity_id INT(11) NULL DEFAULT NULL AFTER entity_type;

ALTER TABLE journal_entries ADD INDEX idx_entity (entity_type, entity_id);
