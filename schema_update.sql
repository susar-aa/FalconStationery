-- Enable 3rd Party Cheque payment method in supplier_payments
ALTER TABLE `supplier_payments` MODIFY COLUMN `payment_method` VARCHAR(50) NOT NULL;
