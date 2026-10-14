-- Ensure deliveries route keys are indexed for sub-millisecond route resolution
ALTER TABLE `deliveries` 
  ADD INDEX `idx_deliveries_rep_route_id` (`rep_route_id`),
  ADD INDEX `idx_deliveries_secondary_rep_route_id` (`secondary_rep_route_id`);
