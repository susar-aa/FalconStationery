-- Ensure deliveries route keys are indexed for sub-millisecond route resolution
ALTER TABLE `deliveries` 
  ADD INDEX `idx_deliveries_rep_route_id` (`rep_route_id`),
  ADD INDEX `idx_deliveries_secondary_rep_route_id` (`secondary_rep_route_id`);

-- Indexes for Supplier Center and Opening Balance performance
ALTER TABLE `journal_entries` ADD INDEX `idx_je_reference` (`reference`);
ALTER TABLE `vendors` ADD INDEX `idx_vendors_status_name` (`status`, `name`);

