-- Verification check on live DB schema compatibility
SHOW COLUMNS FROM `petty_cash_transactions` LIKE 'status';
SHOW COLUMNS FROM `goods_receipt_notes` LIKE 'status';
SHOW COLUMNS FROM `cheques` LIKE 'status';