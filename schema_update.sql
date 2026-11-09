UPDATE chart_of_accounts SET account_name = 'Salary Advance' WHERE account_code = '1650';

INSERT INTO chart_of_accounts (account_code, account_name, account_type, account_category, balance, is_active)
SELECT '1650', 'Salary Advance', 'Asset', 'Current Asset', 0.00, 1
WHERE NOT EXISTS (SELECT 1 FROM chart_of_accounts WHERE account_code = '1650')
LIMIT 1;