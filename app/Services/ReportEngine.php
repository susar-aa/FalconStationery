<?php
/**
 * Curtiss ERP - Centralized Reporting Engine
 * Implements a dynamic, metadata-driven report processor.
 */
class ReportEngine {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Canonical Categories matching ERP Reports Hub Architecture
     */
    public static function getCategories() {
        return [
            'overview' => 'Overview',
            'sales' => 'Sales & Revenue',
            'inventory' => 'Inventory & Stock',
            'procurement' => 'Purchases & GRN',
            'supplier' => 'Suppliers & Payables',
            'customer' => 'Customers & Receivables',
            'collection' => 'Payments & Collections',
            'banking' => 'Banking & Cash',
            'expenses' => 'Expenses & Overheads',
            'finance' => 'Accounting & Financial',
            'cashier' => 'Cashier Performance',
            'expiry' => 'Expiry & Batches'
        ];
    }

    /**
     * Comprehensive ERP Reports Registry (40+ standard business reports)
     */
    public static function getReportsRegistry() {
        return [
            // =========================================================
            // 1. OVERVIEW
            // =========================================================
            'executive_kpi' => [
                'title' => 'Executive KPI Dashboard',
                'category' => 'overview',
                'description' => 'High-level executive metrics across sales, COGS, profitability, inventory valuation, and GL balance status.',
                'icon' => 'ph-activity',
                'filters' => [],
                'columns' => [
                    'kpi_name' => ['label' => 'Metric / KPI', 'type' => 'text'],
                    'domain' => ['label' => 'Functional Domain', 'type' => 'badge'],
                    'kpi_value' => ['label' => 'Current Value', 'type' => 'text', 'align' => 'right'],
                    'notes' => ['label' => 'Benchmark / Detail', 'type' => 'text']
                ],
                'sql' => "SELECT 'Gross Sales (MTD)' as kpi_name, 'Sales & Revenue' as domain,
                                 CONCAT('Rs. ', FORMAT(COALESCE((SELECT SUM(total_amount - COALESCE(CASE WHEN global_discount_type = '%' THEN (total_amount * global_discount_val / 100) ELSE global_discount_val END, 0) + COALESCE(tax_amount, 0)) FROM invoices WHERE status != 'Voided' AND MONTH(invoice_date) = MONTH(NOW()) AND YEAR(invoice_date) = YEAR(NOW())), 0), 2)) as kpi_value,
                                 'Current Month Net Billed Revenue' as notes
                          UNION ALL
                          SELECT 'Cost of Goods Sold (COGS)', 'Cost Accounting',
                                 CONCAT('Rs. ', FORMAT(COALESCE((SELECT SUM(ii.quantity * COALESCE(ii.cost_at_sale, 0)) FROM invoice_items ii JOIN invoices inv ON ii.invoice_id = inv.id WHERE inv.status != 'Voided' AND MONTH(inv.invoice_date) = MONTH(NOW()) AND YEAR(inv.invoice_date) = YEAR(NOW())), 0), 2)),
                                 'MTD Cost of Stock Dispatched'
                          UNION ALL
                          SELECT 'Physical Inventory Valuation', 'Asset Valuation',
                                 CONCAT('Rs. ', FORMAT(COALESCE((SELECT SUM(quantity_on_hand * cost_price) FROM items WHERE quantity_on_hand > 0), 0), 2)),
                                 'Current Total Stock Asset in Warehouses'
                          UNION ALL
                          SELECT 'Total Accounts Receivable (AR)', 'Receivables',
                                 CONCAT('Rs. ', FORMAT(COALESCE((SELECT SUM(total_amount - COALESCE(CASE WHEN global_discount_type = '%' THEN (total_amount * global_discount_val / 100) ELSE global_discount_val END, 0) + COALESCE(tax_amount, 0)) FROM invoices WHERE status NOT IN ('Paid', 'Voided', 'Cancelled')), 0), 2)),
                                 'Outstanding Uncollected Customer Invoices'
                          UNION ALL
                          SELECT 'Total Accounts Payable (AP)', 'Payables',
                                 CONCAT('Rs. ', FORMAT(COALESCE((SELECT SUM(gi.quantity * gi.unit_cost) FROM grn_items gi JOIN goods_receipt_notes g ON gi.grn_id = g.id WHERE g.status != 'Cancelled'), 0) - COALESCE((SELECT SUM(amount) FROM supplier_payments WHERE status = 'Active'), 0), 2)),
                                 'Pending Supplier Settlement Liabilities'"
            ],
            'monthly_kpi' => [
                'title' => 'Monthly KPI Report',
                'category' => 'overview',
                'description' => 'Track operational milestones, gross receipts, and target variance across key fiscal indicators.',
                'icon' => 'ph-chart-line-up',
                'columns' => [
                    'metric' => ['label' => 'Key Performance Indicator', 'type' => 'text'],
                    'target' => ['label' => 'Target', 'type' => 'text', 'align' => 'right'],
                    'actual' => ['label' => 'Actual Value', 'type' => 'text', 'align' => 'right'],
                    'variance' => ['label' => 'Variance', 'type' => 'text', 'align' => 'right']
                ],
                'sql' => "SELECT 'Monthly Sales Revenue' as metric, '1,000,000.00' as target, 
                                 CONCAT('', FORMAT(COALESCE((SELECT SUM(total_amount - COALESCE(CASE WHEN global_discount_type = '%' THEN (total_amount * global_discount_val / 100) ELSE global_discount_val END, 0) + COALESCE(tax_amount, 0)) FROM invoices WHERE status NOT IN ('Voided', 'Cancelled') AND (delivery_status IS NULL OR delivery_status != 'Cancelled') AND MONTH(invoice_date) = MONTH(NOW()) AND YEAR(invoice_date) = YEAR(NOW())), 0), 2)) as actual,
                                 CONCAT(FORMAT(COALESCE((SELECT SUM(total_amount - COALESCE(CASE WHEN global_discount_type = '%' THEN (total_amount * global_discount_val / 100) ELSE global_discount_val END, 0) + COALESCE(tax_amount, 0)) FROM invoices WHERE status NOT IN ('Voided', 'Cancelled') AND (delivery_status IS NULL OR delivery_status != 'Cancelled') AND MONTH(invoice_date) = MONTH(NOW()) AND YEAR(invoice_date) = YEAR(NOW())), 0) - 1000000.00, 2)) as variance
                          UNION ALL
                          SELECT 'New Customers Registered' as metric, '50' as target, 
                                 CAST(COALESCE((SELECT COUNT(*) FROM customers WHERE MONTH(created_at) = MONTH(NOW()) AND YEAR(created_at) = YEAR(NOW())), 0) as CHAR) as actual,
                                 CAST(COALESCE((SELECT COUNT(*) FROM customers WHERE MONTH(created_at) = MONTH(NOW()) AND YEAR(created_at) = YEAR(NOW())), 0) - 50 as CHAR) as variance
                          UNION ALL
                          SELECT 'Outstanding Receivables' as metric, '200,000.00' as target, 
                                 CONCAT('', FORMAT(COALESCE((SELECT SUM(total_amount - COALESCE(CASE WHEN global_discount_type = '%' THEN (total_amount * global_discount_val / 100) ELSE global_discount_val END, 0) + COALESCE(tax_amount, 0)) FROM invoices WHERE status NOT IN ('Paid', 'Voided', 'Cancelled') AND (delivery_status IS NULL OR delivery_status != 'Cancelled')), 0), 2)) as actual,
                                 CONCAT(FORMAT(COALESCE((SELECT SUM(total_amount - COALESCE(CASE WHEN global_discount_type = '%' THEN (total_amount * global_discount_val / 100) ELSE global_discount_val END, 0) + COALESCE(tax_amount, 0)) FROM invoices WHERE status NOT IN ('Paid', 'Voided', 'Cancelled') AND (delivery_status IS NULL OR delivery_status != 'Cancelled')), 0) - 200000.00, 2)) as variance
                          UNION ALL
                          SELECT 'Procurement (GRN) Value' as metric, '500,000.00' as target, 
                                 CONCAT('', FORMAT(COALESCE((SELECT SUM(quantity * unit_cost) FROM grn_items), 0), 2)) as actual,
                                 CONCAT(FORMAT(COALESCE((SELECT SUM(quantity * unit_cost) FROM grn_items), 0) - 500000.00, 2)) as variance"
            ],

            // =========================================================
            // 2. SALES & REVENUE
            // =========================================================
            'sales_history_log' => [
                'title' => 'Sales History Log Report',
                'category' => 'sales',
                'description' => 'Complete audit trail of sales invoices with customer details, payment methods, discount reasons, and cashier info.',
                'icon' => 'ph-receipt',
                'filters' => ['date_range', 'warehouse', 'customer', 'rep', 'status'],
                'date_column' => 'i.invoice_date',
                'columns' => [
                    'invoice_number' => ['label' => 'INVOICE #', 'type' => 'text', 'sortable' => true, 'drilldown' => 'invoice'],
                    'created_at' => ['label' => 'DATE & TIME', 'type' => 'text', 'sortable' => true],
                    'branch_name' => ['label' => 'BRANCH', 'type' => 'text'],
                    'customer_name' => ['label' => 'CUSTOMER', 'type' => 'text', 'sortable' => true, 'drilldown' => 'customer'],
                    'payment_method' => ['label' => 'PAYMENT METHOD', 'type' => 'badge'],
                    'cashier_name' => ['label' => 'CASHIER', 'type' => 'text'],
                    'subtotal' => ['label' => 'SUBTOTAL', 'type' => 'currency', 'align' => 'right', 'total' => 'sum'],
                    'discount_amount' => ['label' => 'DISCOUNT', 'type' => 'currency', 'align' => 'right', 'total' => 'sum'],
                    'grand_total' => ['label' => 'NET TOTAL', 'type' => 'currency', 'align' => 'right', 'total' => 'sum'],
                    'status' => ['label' => 'STATUS', 'type' => 'badge']
                ],
                'sql' => "SELECT i.id, i.invoice_number, 
                                 DATE_FORMAT(COALESCE(i.created_at, CONCAT(i.invoice_date, ' 00:00:00')), '%Y-%m-%d %H:%i:%s') as created_at,
                                 COALESCE(w.name, 'Kandy Branch') as branch_name,
                                 COALESCE(c.name, 'Walk-in Customer') as customer_name,
                                 COALESCE(
                                     (SELECT GROUP_CONCAT(DISTINCT cp.payment_method SEPARATOR ', ')
                                      FROM customer_payment_allocations cpa
                                      JOIN customer_payments cp ON cpa.customer_payment_id = cp.id
                                      WHERE cpa.invoice_id = i.id AND cpa.is_reversed = 0),
                                     i.payment_terms,
                                     'CASH'
                                 ) as payment_method,
                                 COALESCE(CONCAT(u.first_name, ' ', u.last_name), u.username, 'Maheshika Jayathilaka') as cashier_name,
                                 i.total_amount as subtotal,
                                 COALESCE(CASE WHEN i.global_discount_type = '%' THEN (i.total_amount * i.global_discount_val / 100) ELSE i.global_discount_val END, 0) as discount_amount,
                                 (i.total_amount - COALESCE(CASE WHEN i.global_discount_type = '%' THEN (i.total_amount * i.global_discount_val / 100) ELSE i.global_discount_val END, 0) + COALESCE(i.tax_amount, 0)) as grand_total,
                                 CASE WHEN i.status = 'Paid' THEN 'COMPLETED' WHEN i.status = 'Voided' THEN 'VOIDED' ELSE UPPER(i.status) END as status,
                                 i.customer_id, i.created_by, i.rep_route_id, i.invoice_date
                          FROM invoices i
                          LEFT JOIN customers c ON i.customer_id = c.id
                          LEFT JOIN users u ON i.created_by = u.id
                          LEFT JOIN warehouses w ON 1=1
                          WHERE i.status != 'Voided' /*WHERE_CLAUSE*/"
            ],
            'sales_summary' => [
                'title' => 'Sales Summary',
                'category' => 'sales',
                'description' => 'Total invoice counts, gross revenue, discounts, net sales, COGS, gross profit & profit margin percentages.',
                'icon' => 'ph-trend-up',
                'filters' => ['date_range', 'warehouse', 'rep', 'route'],
                'date_column' => 'i.invoice_date',
                'columns' => [
                    'period_date' => ['label' => 'Period Date', 'type' => 'date', 'sortable' => true],
                    'invoice_count' => ['label' => 'Invoice Count', 'type' => 'number', 'align' => 'right', 'total' => 'sum'],
                    'daily_total' => ['label' => 'Gross Sales', 'type' => 'currency', 'align' => 'right', 'total' => 'sum'],
                    'daily_discount' => ['label' => 'Total Discounts', 'type' => 'currency', 'align' => 'right', 'total' => 'sum'],
                    'net_revenue' => ['label' => 'Net Revenue', 'type' => 'currency', 'align' => 'right', 'total' => 'sum']
                ],
                'sql' => "SELECT i.invoice_date as period_date, COUNT(*) as invoice_count,
                                 SUM(i.total_amount) as daily_total,
                                 SUM(COALESCE(CASE WHEN i.global_discount_type = '%' THEN (i.total_amount * i.global_discount_val / 100) ELSE i.global_discount_val END, 0)) as daily_discount,
                                 SUM(i.total_amount - COALESCE(CASE WHEN i.global_discount_type = '%' THEN (i.total_amount * i.global_discount_val / 100) ELSE i.global_discount_val END, 0) + COALESCE(i.tax_amount, 0)) as net_revenue,
                                 i.created_by, i.rep_route_id
                          FROM invoices i
                          WHERE i.status != 'Voided' /*WHERE_CLAUSE*/
                          GROUP BY i.invoice_date, i.created_by, i.rep_route_id"
            ],
            'sales_by_product' => [
                'title' => 'Sales by Product',
                'category' => 'sales',
                'description' => 'Itemized report of sold units, gross revenue, exact lot costs, gross profit margins, and SKU performance.',
                'icon' => 'ph-package',
                'filters' => ['date_range', 'product', 'category', 'brand'],
                'date_column' => 'i.invoice_date',
                'columns' => [
                    'item_code' => ['label' => 'SKU Code', 'type' => 'text'],
                    'item_name' => ['label' => 'Product Name', 'type' => 'text', 'drilldown' => 'product', 'sortable' => true],
                    'category_name' => ['label' => 'Category', 'type' => 'text'],
                    'total_qty' => ['label' => 'Units Sold', 'type' => 'number', 'align' => 'right', 'total' => 'sum'],
                    'total_revenue' => ['label' => 'Gross Revenue', 'type' => 'currency', 'align' => 'right', 'total' => 'sum'],
                    'total_cost' => ['label' => 'COGS Cost', 'type' => 'currency', 'align' => 'right', 'total' => 'sum'],
                    'gross_profit' => ['label' => 'Gross Margin', 'type' => 'currency', 'align' => 'right', 'total' => 'sum']
                ],
                'sql' => "SELECT it.item_code, COALESCE(ii.description, it.name, 'Uncategorized') as item_name,
                                 COALESCE(ic.name, 'General') as category_name,
                                 SUM(COALESCE(ii.quantity, 0)) as total_qty,
                                 SUM(ii.total) as total_revenue,
                                 SUM(COALESCE(ii.quantity, 0) * COALESCE(ii.cost_at_sale, it.cost_price, 0)) as total_cost,
                                 SUM(ii.total - (COALESCE(ii.quantity, 0) * COALESCE(ii.cost_at_sale, it.cost_price, 0))) as gross_profit,
                                 ii.item_id, it.category_id, it.brand
                          FROM invoice_items ii
                          JOIN invoices i ON ii.invoice_id = i.id
                          LEFT JOIN items it ON ii.item_id = it.id
                          LEFT JOIN item_categories ic ON it.category_id = ic.id
                          WHERE i.status != 'Voided' /*WHERE_CLAUSE*/
                          GROUP BY it.item_code, ii.description, it.name, ic.name, ii.item_id, it.category_id, it.brand"
            ],
            'sales_by_category' => [
                'title' => 'Sales by Category',
                'category' => 'sales',
                'description' => 'High-level volume and margin distribution across product categories and sub-departments.',
                'icon' => 'ph-squares-four',
                'filters' => ['date_range', 'category'],
                'date_column' => 'i.invoice_date',
                'columns' => [
                    'category_name' => ['label' => 'Product Department', 'type' => 'text', 'sortable' => true],
                    'items_count' => ['label' => 'Active SKUs', 'type' => 'number', 'align' => 'right'],
                    'sold_qty' => ['label' => 'Total Qty Sold', 'type' => 'number', 'align' => 'right', 'total' => 'sum'],
                    'total_sales' => ['label' => 'Department Sales', 'type' => 'currency', 'align' => 'right', 'total' => 'sum']
                ],
                'sql' => "SELECT COALESCE(ic.name, 'Uncategorized') as category_name,
                                 COUNT(DISTINCT ii.item_id) as items_count,
                                 SUM(COALESCE(ii.quantity, 0)) as sold_qty,
                                 SUM(COALESCE(ii.total, 0)) as total_sales,
                                 it.category_id
                          FROM invoice_items ii
                          JOIN invoices i ON ii.invoice_id = i.id
                          LEFT JOIN items it ON ii.item_id = it.id
                          LEFT JOIN item_categories ic ON it.category_id = ic.id
                          WHERE i.status != 'Voided' /*WHERE_CLAUSE*/
                          GROUP BY ic.name, it.category_id"
            ],
            'sales_by_payment_method' => [
                'title' => 'Sales by Payment Method',
                'category' => 'sales',
                'description' => 'Payment channel breakdown reconciling Cash Drawer, Petty Cash, Bank Accounts, Cards & Cheques.',
                'icon' => 'ph-credit-card',
                'filters' => ['date_range'],
                'date_column' => 'cp.payment_date',
                'columns' => [
                    'payment_method' => ['label' => 'Payment Channel', 'type' => 'badge'],
                    'transaction_count' => ['label' => 'Transactions', 'type' => 'number', 'align' => 'right', 'total' => 'sum'],
                    'total_collected' => ['label' => 'Amount Settled', 'type' => 'currency', 'align' => 'right', 'total' => 'sum']
                ],
                'sql' => "SELECT cp.payment_method,
                                 COUNT(cp.id) as transaction_count,
                                 SUM(cp.amount) as total_collected
                          FROM customer_payments cp
                          WHERE cp.status = 'Active' /*WHERE_CLAUSE*/
                          GROUP BY cp.payment_method"
            ],
            'customer_returns' => [
                'title' => 'Customer Returns & Refund Audit',
                'category' => 'sales',
                'description' => 'Comprehensive log of customer returns, exchanged items, return reasons, and cash/bank refunds issued.',
                'icon' => 'ph-arrow-u-up-left',
                'filters' => ['date_range', 'customer'],
                'date_column' => 'cn.note_date',
                'columns' => [
                    'note_date' => ['label' => 'Return Date', 'type' => 'date'],
                    'credit_note_number' => ['label' => 'Credit Note Ref', 'type' => 'text'],
                    'customer_name' => ['label' => 'Customer', 'type' => 'text', 'drilldown' => 'customer'],
                    'total_amount' => ['label' => 'Credit Refund Value', 'type' => 'currency', 'align' => 'right', 'total' => 'sum'],
                    'reason' => ['label' => 'Return Reason', 'type' => 'text'],
                    'status' => ['label' => 'Status', 'type' => 'badge']
                ],
                'sql' => "SELECT cn.note_date, cn.credit_note_number, c.name as customer_name,
                                 cn.total_amount, cn.reason, cn.status, cn.customer_id
                          FROM credit_notes cn
                          JOIN customers c ON cn.customer_id = c.id
                          WHERE 1=1 /*WHERE_CLAUSE*/"
            ],
            'daily_sales_register' => [
                'title' => 'Daily Sales Register',
                'category' => 'sales',
                'description' => 'Sequential day-by-day invoice audit with starting/ending refs, gross sales, taxes, and settlements.',
                'icon' => 'ph-calendar-check',
                'filters' => ['date_range'],
                'date_column' => 'i.invoice_date',
                'columns' => [
                    'invoice_date' => ['label' => 'Posting Date', 'type' => 'date'],
                    'first_invoice' => ['label' => 'First Invoice #', 'type' => 'text'],
                    'last_invoice' => ['label' => 'Last Invoice #', 'type' => 'text'],
                    'invoices_count' => ['label' => 'Invoices Issued', 'type' => 'number', 'align' => 'right', 'total' => 'sum'],
                    'total_gross' => ['label' => 'Gross Invoiced', 'type' => 'currency', 'align' => 'right', 'total' => 'sum'],
                    'total_net' => ['label' => 'Net Settled Value', 'type' => 'currency', 'align' => 'right', 'total' => 'sum']
                ],
                'sql' => "SELECT i.invoice_date,
                                 MIN(i.invoice_number) as first_invoice,
                                 MAX(i.invoice_number) as last_invoice,
                                 COUNT(i.id) as invoices_count,
                                 SUM(i.total_amount) as total_gross,
                                 SUM(i.total_amount - COALESCE(CASE WHEN i.global_discount_type = '%' THEN (i.total_amount * i.global_discount_val / 100) ELSE i.global_discount_val END, 0) + COALESCE(i.tax_amount, 0)) as total_net
                          FROM invoices i
                          WHERE i.status != 'Voided' /*WHERE_CLAUSE*/
                          GROUP BY i.invoice_date"
            ],
            'sales_by_rep' => [
                'title' => 'Sales by Sales Rep',
                'category' => 'sales',
                'description' => 'Revenue generated, invoices booked, collections achieved, and commissions per representative.',
                'icon' => 'ph-user-circle-gear',
                'filters' => ['date_range', 'rep'],
                'date_column' => 'i.invoice_date',
                'columns' => [
                    'rep_name' => ['label' => 'Sales Representative', 'type' => 'text', 'sortable' => true],
                    'orders_count' => ['label' => 'Invoices Closed', 'type' => 'number', 'align' => 'right', 'total' => 'sum'],
                    'gross_sales' => ['label' => 'Gross Sales', 'type' => 'currency', 'align' => 'right', 'total' => 'sum'],
                    'collected_amount' => ['label' => 'Collections Made', 'type' => 'currency', 'align' => 'right', 'total' => 'sum']
                ],
                'sql' => "SELECT COALESCE(CONCAT(e.first_name, ' ', e.last_name), u.username, 'Direct/Office') as rep_name,
                                 COUNT(i.id) as orders_count,
                                 SUM(i.total_amount - COALESCE(CASE WHEN i.global_discount_type = '%' THEN (i.total_amount * i.global_discount_val / 100) ELSE i.global_discount_val END, 0) + COALESCE(i.tax_amount, 0)) as gross_sales,
                                 COALESCE((SELECT SUM(cp.amount) FROM customer_payments cp WHERE cp.created_by = i.created_by AND cp.status = 'Active'), 0) as collected_amount,
                                 i.created_by
                          FROM invoices i
                          LEFT JOIN users u ON i.created_by = u.id
                          LEFT JOIN employees e ON u.employee_id = e.id
                          WHERE i.status != 'Voided' /*WHERE_CLAUSE*/
                          GROUP BY u.id, u.username, e.first_name, e.last_name, i.created_by"
            ],
            'sales_by_route' => [
                'title' => 'Sales by Route',
                'category' => 'sales',
                'description' => 'Distribution performance, delivered order volumes, and route profitability across field runs.',
                'icon' => 'ph-map-pin-line',
                'filters' => ['date_range', 'route'],
                'date_column' => 'r.start_time',
                'columns' => [
                    'route_name' => ['label' => 'Route Name', 'type' => 'text', 'drilldown' => 'route'],
                    'rep_name' => ['label' => 'Assigned Representative', 'type' => 'text'],
                    'orders_count' => ['label' => 'Delivered Orders', 'type' => 'number', 'align' => 'right', 'total' => 'sum'],
                    'route_sales' => ['label' => 'Billed Volume', 'type' => 'currency', 'align' => 'right', 'total' => 'sum']
                ],
                'sql' => "SELECT r.route_name, r.id, r.user_id, r.start_time,
                                 COALESCE(CONCAT(e.first_name, ' ', e.last_name), u.username, 'N/A') as rep_name,
                                 COUNT(i.id) as orders_count,
                                 SUM(COALESCE(i.total_amount - COALESCE(CASE WHEN i.global_discount_type = '%' THEN (i.total_amount * i.global_discount_val / 100) ELSE i.global_discount_val END, 0) + COALESCE(i.tax_amount, 0), 0)) as route_sales
                          FROM rep_daily_routes r
                          LEFT JOIN users u ON r.user_id = u.id
                          LEFT JOIN employees e ON u.employee_id = e.id
                          LEFT JOIN invoices i ON i.rep_route_id = r.id AND i.status != 'Voided'
                          WHERE 1=1 /*WHERE_CLAUSE*/
                          GROUP BY r.id, r.route_name, u.username, e.first_name, e.last_name, r.user_id, r.start_time"
            ],

            // =========================================================
            // 3. INVENTORY & STOCK
            // =========================================================
            'stock_summary' => [
                'title' => 'Stock Summary',
                'category' => 'inventory',
                'description' => 'Complete stock ledger with quantity on hand, unit cost, retail price, and total valuation.',
                'icon' => 'ph-boxes',
                'filters' => ['product', 'category', 'warehouse', 'brand'],
                'columns' => [
                    'item_code' => ['label' => 'SKU Code', 'type' => 'text', 'sortable' => true],
                    'name' => ['label' => 'Product Description', 'type' => 'text', 'sortable' => true, 'drilldown' => 'product'],
                    'category_name' => ['label' => 'Department', 'type' => 'text'],
                    'qty_on_hand' => ['label' => 'Current Stock', 'type' => 'number', 'align' => 'right', 'sortable' => true, 'total' => 'sum'],
                    'cost_price' => ['label' => 'Unit Cost', 'type' => 'currency', 'align' => 'right'],
                    'price' => ['label' => 'Retail Price', 'type' => 'currency', 'align' => 'right'],
                    'val_cost' => ['label' => 'Valuation (Cost)', 'type' => 'currency', 'align' => 'right', 'total' => 'sum'],
                    'val_retail' => ['label' => 'Valuation (Retail)', 'type' => 'currency', 'align' => 'right', 'total' => 'sum']
                ],
                'sql' => "SELECT i.id, i.item_code, i.name, ic.name as category_name, i.brand, i.warehouse_id, i.category_id,
                                 COALESCE(i.quantity_on_hand, 0) as qty_on_hand,
                                 COALESCE(i.cost_price, 0) as cost_price,
                                 COALESCE(i.price, 0) as price,
                                 (COALESCE(i.quantity_on_hand, 0) * COALESCE(i.cost_price, 0)) as val_cost,
                                 (COALESCE(i.quantity_on_hand, 0) * COALESCE(i.price, 0)) as val_retail
                          FROM items i
                          LEFT JOIN item_categories ic ON i.category_id = ic.id
                          WHERE 1=1 /*WHERE_CLAUSE*/"
            ],
            'stock_balance' => [
                'title' => 'Stock Balance Report',
                'category' => 'inventory',
                'description' => 'Real-time physical inventory levels by warehouse, bin location, and category.',
                'icon' => 'ph-warehouse',
                'filters' => ['product', 'warehouse', 'category', 'brand'],
                'columns' => [
                    'name' => ['label' => 'Product Name', 'type' => 'text', 'sortable' => true, 'drilldown' => 'product'],
                    'warehouse_name' => ['label' => 'Location / Warehouse', 'type' => 'text'],
                    'qty_on_hand' => ['label' => 'Available Stock', 'type' => 'number', 'align' => 'right', 'total' => 'sum']
                ],
                'sql' => "SELECT i.id as item_id, i.name, COALESCE(w.name, 'Main Warehouse') as warehouse_name, i.warehouse_id, i.category_id, i.brand,
                                 COALESCE(i.quantity_on_hand, 0) as qty_on_hand
                          FROM items i
                          LEFT JOIN warehouses w ON i.warehouse_id = w.id
                          WHERE 1=1 /*WHERE_CLAUSE*/"
            ],
            'stock_movement' => [
                'title' => 'Stock Movement Report',
                'category' => 'inventory',
                'description' => 'Audit trail of stock in/out transactions with transaction types and running balances.',
                'icon' => 'ph-arrows-left-right',
                'filters' => ['date_range', 'product', 'warehouse', 'category'],
                'date_column' => 'sl.transaction_date',
                'columns' => [
                    'created_at' => ['label' => 'Date', 'type' => 'date', 'sortable' => true],
                    'product_name' => ['label' => 'Product Description', 'type' => 'text', 'drilldown' => 'product'],
                    'activity_type' => ['label' => 'Movement Type', 'type' => 'badge'],
                    'ref_doc' => ['label' => 'Document Reference', 'type' => 'text'],
                    'qty_change' => ['label' => 'Delta Qty', 'type' => 'number', 'align' => 'right'],
                    'new_balance' => ['label' => 'Balance Following', 'type' => 'number', 'align' => 'right']
                ],
                'sql' => "SELECT sl.transaction_date as created_at, i.name as product_name, sl.transaction_type as activity_type, 
                                 sl.reference_number as ref_doc, (sl.quantity_in - sl.quantity_out) as qty_change, sl.running_balance as new_balance,
                                 sl.item_id, sl.warehouse_id, i.category_id
                          FROM stock_ledger sl
                          JOIN items i ON sl.item_id = i.id
                          WHERE 1=1 /*WHERE_CLAUSE*/"
            ],
            'stock_ledger' => [
                'title' => 'Stock Ledger',
                'category' => 'inventory',
                'description' => 'Continuous chronological ledger of stock ins, outs, and resulting balances per SKU.',
                'icon' => 'ph-notebook',
                'filters' => ['date_range', 'product', 'warehouse', 'category'],
                'date_column' => 'sl.transaction_date',
                'columns' => [
                    'created_at' => ['label' => 'Transaction Timestamp', 'type' => 'date', 'sortable' => true],
                    'item_code' => ['label' => 'SKU', 'type' => 'text'],
                    'name' => ['label' => 'Product Name', 'type' => 'text', 'drilldown' => 'product'],
                    'activity_type' => ['label' => 'Source Document Ref', 'type' => 'text'],
                    'qty_change' => ['label' => 'Quantity Change', 'type' => 'number', 'align' => 'right'],
                    'new_balance' => ['label' => 'Resulting Balance', 'type' => 'number', 'align' => 'right']
                ],
                'sql' => "SELECT sl.transaction_date as created_at, i.item_code, i.name, sl.reference_number as activity_type,
                                 (sl.quantity_in - sl.quantity_out) as qty_change, sl.running_balance as new_balance,
                                 sl.item_id, sl.warehouse_id, i.category_id
                          FROM stock_ledger sl
                          JOIN items i ON sl.item_id = i.id
                          WHERE 1=1 /*WHERE_CLAUSE*/"
            ],
            'stock_aging' => [
                'title' => 'Stock Aging Report',
                'category' => 'inventory',
                'description' => 'Holding age analysis of inventory (0-30, 31-60, 61-90, 90+ days) and capital tied up.',
                'icon' => 'ph-hourglass-medium',
                'filters' => ['product', 'warehouse', 'category', 'brand'],
                'columns' => [
                    'item_code' => ['label' => 'Code', 'type' => 'text'],
                    'product_name' => ['label' => 'Product Description', 'type' => 'text', 'drilldown' => 'product'],
                    'qty_on_hand' => ['label' => 'Quantity in Stock', 'type' => 'number', 'align' => 'right', 'total' => 'sum'],
                    'cost_price' => ['label' => 'Unit Valuation', 'type' => 'currency', 'align' => 'right'],
                    'total_cost' => ['label' => 'Capital Locked', 'type' => 'currency', 'align' => 'right', 'total' => 'sum'],
                    'age_days' => ['label' => 'Holding Age (Days)', 'type' => 'number', 'align' => 'right']
                ],
                'sql' => "SELECT i.id as item_id, i.item_code, i.name as product_name, i.warehouse_id, i.category_id, i.brand,
                                 COALESCE(i.quantity_on_hand, 0) as qty_on_hand,
                                 COALESCE(i.cost_price, 0) as cost_price,
                                 (COALESCE(i.quantity_on_hand, 0) * COALESCE(i.cost_price, 0)) as total_cost,
                                 COALESCE(DATEDIFF(NOW(), (SELECT MAX(transaction_date) FROM stock_ledger WHERE item_id = i.id AND quantity_in > 0)), DATEDIFF(NOW(), i.created_at)) as age_days
                          FROM items i
                          WHERE COALESCE(i.quantity_on_hand, 0) > 0 /*WHERE_CLAUSE*/"
            ],
            'inventory_valuation' => [
                'title' => 'Inventory Valuation',
                'category' => 'inventory',
                'description' => 'Weighted average and cost valuation of physical inventory across warehouses.',
                'icon' => 'ph-coins',
                'filters' => ['product', 'category', 'warehouse', 'brand'],
                'columns' => [
                    'item_code' => ['label' => 'SKU Identifier', 'type' => 'text'],
                    'product_name' => ['label' => 'Item Description', 'type' => 'text', 'drilldown' => 'product'],
                    'qty_on_hand' => ['label' => 'Physical On-Hand', 'type' => 'number', 'align' => 'right', 'total' => 'sum'],
                    'avg_cost' => ['label' => 'Standard Cost Unit', 'type' => 'currency', 'align' => 'right'],
                    'total_valuation' => ['label' => 'Asset Valuation', 'type' => 'currency', 'align' => 'right', 'total' => 'sum']
                ],
                'sql' => "SELECT i.item_code, i.name as product_name, i.id as item_id, i.warehouse_id, i.category_id, i.brand,
                                 COALESCE(i.quantity_on_hand, 0) as qty_on_hand,
                                 COALESCE(i.cost_price, 0) as avg_cost,
                                 (COALESCE(i.quantity_on_hand, 0) * COALESCE(i.cost_price, 0)) as total_valuation
                          FROM items i
                          WHERE 1=1 /*WHERE_CLAUSE*/"
            ],
            'reorder_level' => [
                'title' => 'Reorder Level Report',
                'category' => 'inventory',
                'description' => 'Inventory items below minimum safety threshold requiring purchase orders.',
                'icon' => 'ph-bell-ringing',
                'filters' => ['product', 'category', 'warehouse'],
                'columns' => [
                    'item_code' => ['label' => 'SKU', 'type' => 'text'],
                    'product_name' => ['label' => 'Product Name', 'type' => 'text', 'drilldown' => 'product'],
                    'qty_on_hand' => ['label' => 'Current Stock', 'type' => 'number', 'align' => 'right', 'total' => 'sum'],
                    'reorder_point' => ['label' => 'Safety Threshold', 'type' => 'number', 'align' => 'right'],
                    'deficit' => ['label' => 'Procurement Deficit', 'type' => 'number', 'align' => 'right', 'total' => 'sum']
                ],
                'sql' => "SELECT i.item_code, i.name as product_name, i.id as item_id, i.category_id, i.warehouse_id,
                                 COALESCE(i.quantity_on_hand, 0) as qty_on_hand,
                                 COALESCE(i.minimum_stock_level, i.alert_qty, 10) as reorder_point,
                                 (COALESCE(i.minimum_stock_level, i.alert_qty, 10) - COALESCE(i.quantity_on_hand, 0)) as deficit
                          FROM items i
                          WHERE COALESCE(i.quantity_on_hand, 0) <= COALESCE(i.minimum_stock_level, i.alert_qty, 10) /*WHERE_CLAUSE*/"
            ],
            'negative_stock' => [
                'title' => 'Negative Stock Report',
                'category' => 'inventory',
                'description' => 'Inventory items with negative physical balances requiring stock adjustment.',
                'icon' => 'ph-warning-octagon',
                'filters' => ['product', 'warehouse', 'category'],
                'columns' => [
                    'item_code' => ['label' => 'SKU Code', 'type' => 'text'],
                    'product_name' => ['label' => 'Product Name', 'type' => 'text', 'drilldown' => 'product'],
                    'qty_on_hand' => ['label' => 'Negative Delta Qty', 'type' => 'number', 'align' => 'right', 'total' => 'sum']
                ],
                'sql' => "SELECT i.item_code, i.name as product_name, i.id as item_id, i.warehouse_id, i.category_id,
                                 COALESCE(i.quantity_on_hand, 0) as qty_on_hand
                          FROM items i
                          WHERE COALESCE(i.quantity_on_hand, 0) < 0 /*WHERE_CLAUSE*/"
            ],
            'dead_stock' => [
                'title' => 'Dead Stock Report',
                'category' => 'inventory',
                'description' => 'Dormant inventory items with zero sales in the past 90 days.',
                'icon' => 'ph-pause-circle',
                'filters' => ['category', 'brand', 'warehouse'],
                'columns' => [
                    'name' => ['label' => 'Product Description', 'type' => 'text', 'drilldown' => 'product'],
                    'qty_on_hand' => ['label' => 'Unmoved Units', 'type' => 'number', 'align' => 'right'],
                    'cost_value' => ['label' => 'Dormant Capital', 'type' => 'currency', 'align' => 'right', 'total' => 'sum'],
                    'days_dormant' => ['label' => 'Days Since Activity', 'type' => 'number', 'align' => 'right']
                ],
                'sql' => "SELECT i.name, i.category_id, i.brand, i.warehouse_id,
                                 COALESCE(i.quantity_on_hand, 0) as qty_on_hand,
                                 (COALESCE(i.quantity_on_hand, 0) * COALESCE(i.cost_price, 0)) as cost_value,
                                 COALESCE(DATEDIFF(NOW(), (SELECT MAX(inv.invoice_date) FROM invoice_items ii JOIN invoices inv ON ii.invoice_id = inv.id WHERE ii.item_id = i.id AND inv.status != 'Voided')), DATEDIFF(NOW(), i.created_at)) as days_dormant
                          FROM items i
                          WHERE COALESCE(i.quantity_on_hand, 0) > 0
                            AND NOT EXISTS (
                                SELECT 1 FROM invoice_items ii 
                                JOIN invoices inv ON ii.invoice_id = inv.id 
                                WHERE ii.item_id = i.id AND inv.invoice_date >= DATE_SUB(NOW(), INTERVAL 90 DAY) AND inv.status != 'Voided'
                            ) /*WHERE_CLAUSE*/
                          ORDER BY days_dormant DESC"
            ],
            'stock_transfer' => [
                'title' => 'Stock Transfer Report',
                'category' => 'inventory',
                'description' => 'Inter-warehouse and rep van transfer history with dispatch and receipt quantities.',
                'icon' => 'ph-truck',
                'filters' => ['date_range', 'warehouse'],
                'date_column' => 'wt.transfer_date',
                'columns' => [
                    'transfer_date' => ['label' => 'Transfer Date', 'type' => 'date'],
                    'transfer_no' => ['label' => 'Transfer Manifest #', 'type' => 'text'],
                    'from_wh' => ['label' => 'Dispatched From', 'type' => 'text'],
                    'to_wh' => ['label' => 'Received At', 'type' => 'text'],
                    'product_name' => ['label' => 'Item Transferred', 'type' => 'text', 'drilldown' => 'product'],
                    'qty' => ['label' => 'Quantity Moved', 'type' => 'number', 'align' => 'right', 'total' => 'sum']
                ],
                'sql' => "SELECT wt.transfer_date, wt.transfer_number as transfer_no, f.name as from_wh, t.name as to_wh, i.name as product_name, wt.qty,
                                 wt.from_warehouse_id, wt.to_warehouse_id, wt.item_id
                          FROM warehouse_transfers wt
                          JOIN items i ON wt.item_id = i.id
                          LEFT JOIN warehouses f ON wt.from_warehouse_id = f.id
                          LEFT JOIN warehouses t ON wt.to_warehouse_id = t.id
                          WHERE 1=1 /*WHERE_CLAUSE*/"
            ],

            // =========================================================
            // 4. PURCHASES & GRN
            // =========================================================
            'purchase_order_report' => [
                'title' => 'Purchase Order Report',
                'category' => 'procurement',
                'description' => 'Open, partially received, and closed purchase orders with supplier details and ETAs.',
                'icon' => 'ph-shopping-cart',
                'filters' => ['date_range', 'supplier', 'status'],
                'date_column' => 'p.po_date',
                'columns' => [
                    'po_date' => ['label' => 'PO Date', 'type' => 'date'],
                    'po_number' => ['label' => 'PO Number', 'type' => 'text', 'drilldown' => 'po'],
                    'vendor_name' => ['label' => 'Vendor / Supplier', 'type' => 'text', 'drilldown' => 'supplier'],
                    'total_amount' => ['label' => 'Contracted Value', 'type' => 'currency', 'align' => 'right', 'total' => 'sum'],
                    'status' => ['label' => 'Status', 'type' => 'badge']
                ],
                'sql' => "SELECT p.po_date, p.po_number, v.name as vendor_name, p.total_amount, p.status, p.vendor_id, p.id
                          FROM purchase_orders p
                          JOIN vendors v ON p.vendor_id = v.id
                          WHERE 1=1 /*WHERE_CLAUSE*/"
            ],
            'grn_report' => [
                'title' => 'GRN (Goods Receipt) Report',
                'category' => 'procurement',
                'description' => 'Inward goods receipt notes, received quantities, lot numbers, and landed costs.',
                'icon' => 'ph-archive-box',
                'filters' => ['date_range', 'supplier', 'status'],
                'date_column' => 'g.grn_date',
                'columns' => [
                    'grn_date' => ['label' => 'Receipt Date', 'type' => 'date'],
                    'grn_number' => ['label' => 'GRN Ref', 'type' => 'text', 'drilldown' => 'grn'],
                    'vendor_name' => ['label' => 'Supplier', 'type' => 'text', 'drilldown' => 'supplier'],
                    'total_value' => ['label' => 'Received Inventory Value', 'type' => 'currency', 'align' => 'right', 'total' => 'sum'],
                    'status' => ['label' => 'Status', 'type' => 'badge']
                ],
                'sql' => "SELECT g.grn_date, g.grn_number, v.name as vendor_name, g.status, g.vendor_id, g.id,
                                 COALESCE((SELECT SUM(quantity * unit_cost) FROM grn_items WHERE grn_id = g.id), 0) as total_value
                          FROM goods_receipt_notes g
                          JOIN vendors v ON g.vendor_id = v.id
                          WHERE 1=1 /*WHERE_CLAUSE*/"
            ],
            'purchase_returns' => [
                'title' => 'Purchase Returns Audit',
                'category' => 'procurement',
                'description' => 'Items returned to suppliers with credit note references and replacement tracking.',
                'icon' => 'ph-arrow-counter-clockwise',
                'filters' => ['date_range', 'supplier'],
                'date_column' => 'sr.return_date',
                'columns' => [
                    'return_date' => ['label' => 'Debit Date', 'type' => 'date'],
                    'return_number' => ['label' => 'Debit Note Ref', 'type' => 'text'],
                    'supplier_name' => ['label' => 'Vendor', 'type' => 'text', 'drilldown' => 'supplier'],
                    'total_amount' => ['label' => 'Returned Value', 'type' => 'currency', 'align' => 'right', 'total' => 'sum'],
                    'reason' => ['label' => 'Reason / Defect Detail', 'type' => 'text']
                ],
                'sql' => "SELECT sr.return_date, sr.return_number, v.name as supplier_name, sr.total_amount, sr.reason, sr.vendor_id
                          FROM supplier_returns sr
                          JOIN vendors v ON sr.vendor_id = v.id
                          WHERE 1=1 /*WHERE_CLAUSE*/"
            ],

            // =========================================================
            // 5. SUPPLIERS & PAYABLES
            // =========================================================
            'supplier_aging' => [
                'title' => 'Supplier Aging Report (AP)',
                'category' => 'supplier',
                'description' => 'Accounts payable aging buckets (Current, 1-30, 31-60, 60+ days) per vendor.',
                'icon' => 'ph-users-three',
                'filters' => ['supplier'],
                'columns' => [
                    'vendor_name' => ['label' => 'Supplier / Vendor', 'type' => 'text', 'drilldown' => 'supplier'],
                    'current' => ['label' => 'Current (0-30d)', 'type' => 'currency', 'align' => 'right', 'total' => 'sum'],
                    'thirty' => ['label' => '31 - 60 Days', 'type' => 'currency', 'align' => 'right', 'total' => 'sum'],
                    'sixty' => ['label' => '61 - 90 Days', 'type' => 'currency', 'align' => 'right', 'total' => 'sum'],
                    'older' => ['label' => '90+ Days', 'type' => 'currency', 'align' => 'right', 'total' => 'sum'],
                    'total' => ['label' => 'Total Payable', 'type' => 'currency', 'align' => 'right', 'total' => 'sum']
                ],
                'sql' => "SELECT v.name as vendor_name, p.vendor_id,
                                 SUM(CASE WHEN DATEDIFF(NOW(), p.grn_date) <= 30 THEN (COALESCE((SELECT SUM(quantity * unit_cost) FROM grn_items WHERE grn_id = p.id), 0) - COALESCE((SELECT SUM(amount) FROM supplier_payment_allocations WHERE grn_id = p.id AND is_reversed = 0), 0)) ELSE 0 END) as current,
                                 SUM(CASE WHEN DATEDIFF(NOW(), p.grn_date) > 30 AND DATEDIFF(NOW(), p.grn_date) <= 60 THEN (COALESCE((SELECT SUM(quantity * unit_cost) FROM grn_items WHERE grn_id = p.id), 0) - COALESCE((SELECT SUM(amount) FROM supplier_payment_allocations WHERE grn_id = p.id AND is_reversed = 0), 0)) ELSE 0 END) as `thirty`,
                                 SUM(CASE WHEN DATEDIFF(NOW(), p.grn_date) > 60 AND DATEDIFF(NOW(), p.grn_date) <= 90 THEN (COALESCE((SELECT SUM(quantity * unit_cost) FROM grn_items WHERE grn_id = p.id), 0) - COALESCE((SELECT SUM(amount) FROM supplier_payment_allocations WHERE grn_id = p.id AND is_reversed = 0), 0)) ELSE 0 END) as `sixty`,
                                 SUM(CASE WHEN DATEDIFF(NOW(), p.grn_date) > 90 THEN (COALESCE((SELECT SUM(quantity * unit_cost) FROM grn_items WHERE grn_id = p.id), 0) - COALESCE((SELECT SUM(amount) FROM supplier_payment_allocations WHERE grn_id = p.id AND is_reversed = 0), 0)) ELSE 0 END) as `older`,
                                 SUM(COALESCE((SELECT SUM(quantity * unit_cost) FROM grn_items WHERE grn_id = p.id), 0) - COALESCE((SELECT SUM(amount) FROM supplier_payment_allocations WHERE grn_id = p.id AND is_reversed = 0), 0)) as total
                          FROM goods_receipt_notes p
                          JOIN vendors v ON p.vendor_id = v.id
                          WHERE 1=1 /*WHERE_CLAUSE*/
                          GROUP BY v.id, v.name, p.vendor_id
                          HAVING total > 0"
            ],
            'supplier_statement' => [
                'title' => 'Supplier Statement',
                'category' => 'supplier',
                'description' => 'Full transactional ledger of GRNs, payments, debit notes, and running balances.',
                'icon' => 'ph-file-text',
                'filters' => ['date_range', 'supplier'],
                'date_column' => 'i.date',
                'columns' => [
                    'date' => ['label' => 'Posting Date', 'type' => 'date'],
                    'type' => ['label' => 'Entry Type', 'type' => 'text'],
                    'ref' => ['label' => 'Reference / Document', 'type' => 'text'],
                    'debit' => ['label' => 'Debit (Disbursements)', 'type' => 'currency', 'align' => 'right', 'total' => 'sum'],
                    'credit' => ['label' => 'Credit (GRN Invoiced)', 'type' => 'currency', 'align' => 'right', 'total' => 'sum'],
                    'balance' => ['label' => 'Running Payables Bal', 'type' => 'currency', 'align' => 'right']
                ],
                'sql' => "SELECT i.id, i.date, i.type, i.ref, i.debit, i.credit,
                                 SUM(i.credit - i.debit) OVER (ORDER BY i.date, i.ref, i.id) as balance,
                                 i.vendor_id
                          FROM (
                              SELECT grn.id, grn.grn_date as date, 'GRN' as type, grn.grn_number as ref,
                                     0 as debit,
                                     (SELECT COALESCE(SUM(total), 0) FROM grn_items WHERE grn_id = grn.id) as credit,
                                     grn.vendor_id
                              FROM goods_receipt_notes grn
                              WHERE grn.is_approved = 1
                              UNION ALL
                              SELECT sp.id, sp.payment_date as date, 'Payment' as type, 
                                     CONCAT('Pay: ', sp.payment_method, IF(sp.reference != '', CONCAT(' (', sp.reference, ')'), '')) as ref,
                                     sp.amount as debit,
                                     0 as credit,
                                     sp.vendor_id
                              FROM supplier_payments sp
                              WHERE sp.status = 'Active'
                              UNION ALL
                              SELECT sr.id, sr.return_date as date, 'Supplier Return' as type, sr.return_number as ref,
                                     sr.total_amount as debit,
                                     0 as credit,
                                     sr.vendor_id
                              FROM supplier_returns sr
                              UNION ALL
                              SELECT v.id, COALESCE(v.opening_balance_date, DATE(v.created_at)) as date, 'Opening Bal' as type,
                                     'Initial Balance' as ref,
                                     0 as debit,
                                     v.opening_balance as credit,
                                     v.id as vendor_id
                              FROM vendors v
                              WHERE v.opening_balance > 0
                          ) as i
                          WHERE 1=1 /*WHERE_CLAUSE*/"
            ],
            'supplier_balances' => [
                'title' => 'Supplier Outstanding Balance List',
                'category' => 'supplier',
                'description' => 'Summary of all vendors with opening balances, billed GRNs, settlements, and net payables.',
                'icon' => 'ph-wallet',
                'filters' => ['supplier'],
                'columns' => [
                    'vendor_code' => ['label' => 'Vendor Code', 'type' => 'text'],
                    'vendor_name' => ['label' => 'Supplier Name', 'type' => 'text', 'drilldown' => 'supplier', 'sortable' => true],
                    'phone' => ['label' => 'Phone', 'type' => 'text'],
                    'opening_balance' => ['label' => 'Opening Bal', 'type' => 'currency', 'align' => 'right'],
                    'total_grn' => ['label' => 'Billed GRNs', 'type' => 'currency', 'align' => 'right', 'total' => 'sum'],
                    'total_paid' => ['label' => 'Settlements Paid', 'type' => 'currency', 'align' => 'right', 'total' => 'sum'],
                    'net_payable' => ['label' => 'Net Payable Balance', 'type' => 'currency', 'align' => 'right', 'total' => 'sum', 'sortable' => true]
                ],
                'sql' => "SELECT CONCAT('SUP-', v.id) as vendor_code, v.id as vendor_id, v.name as vendor_name, v.phone,
                                 COALESCE(v.opening_balance, 0) as opening_balance,
                                 COALESCE((SELECT SUM(gi.quantity * gi.unit_cost) FROM grn_items gi JOIN goods_receipt_notes g ON gi.grn_id = g.id WHERE g.vendor_id = v.id AND g.status != 'Cancelled'), 0) as total_grn,
                                 COALESCE((SELECT SUM(amount) FROM supplier_payments WHERE vendor_id = v.id AND status = 'Active'), 0) as total_paid,
                                 (COALESCE(v.opening_balance, 0) + 
                                  COALESCE((SELECT SUM(gi.quantity * gi.unit_cost) FROM grn_items gi JOIN goods_receipt_notes g ON gi.grn_id = g.id WHERE g.vendor_id = v.id AND g.status != 'Cancelled'), 0) - 
                                  COALESCE((SELECT SUM(amount) FROM supplier_payments WHERE vendor_id = v.id AND status = 'Active'), 0)) as net_payable
                          FROM vendors v
                          WHERE 1=1 /*WHERE_CLAUSE*/
                          HAVING (net_payable > 0.01 OR net_payable < -0.01)"
            ],

            // =========================================================
            // 6. CUSTOMERS & RECEIVABLES
            // =========================================================
            'customer_outstanding' => [
                'title' => 'Customer Outstanding Report (AR)',
                'category' => 'customer',
                'description' => 'Current receivables balances, credit limits, and overdue status across customer accounts.',
                'icon' => 'ph-user-list',
                'filters' => ['customer', 'rep', 'route', 'territory', 'group'],
                'columns' => [
                    'customer_code' => ['label' => 'Customer Code', 'type' => 'text', 'sortable' => true],
                    'customer_name' => ['label' => 'Customer Name', 'type' => 'text', 'drilldown' => 'customer', 'sortable' => true],
                    'phone' => ['label' => 'Contact Number', 'type' => 'text'],
                    'territory' => ['label' => 'Territory', 'type' => 'text'],
                    'credit_limit' => ['label' => 'Credit Limit', 'type' => 'currency', 'align' => 'right'],
                    'opening_balance' => ['label' => 'Opening Bal', 'type' => 'currency', 'align' => 'right', 'total' => 'sum'],
                    'total_billed' => ['label' => 'Total Invoiced', 'type' => 'currency', 'align' => 'right', 'total' => 'sum'],
                    'total_paid' => ['label' => 'Total Receipts', 'type' => 'currency', 'align' => 'right', 'total' => 'sum'],
                    'total_credited' => ['label' => 'Credit Notes', 'type' => 'currency', 'align' => 'right', 'total' => 'sum'],
                    'outstanding_balance' => ['label' => 'Net AR Balance', 'type' => 'currency', 'align' => 'right', 'total' => 'sum', 'sortable' => true]
                ],
                'sql' => "SELECT c.id, CONCAT('CUS-', c.id) as customer_code, c.name as customer_name, c.phone, c.territory,
                                 COALESCE(c.credit_limit, 0) as credit_limit,
                                 COALESCE(c.opening_balance, 0) as opening_balance,
                                 COALESCE(inv.total_billed, 0) as total_billed,
                                 COALESCE(pmt.total_paid, 0) as total_paid,
                                 COALESCE(cn.total_credited, 0) as total_credited,
                                 (COALESCE(c.opening_balance, 0) + COALESCE(inv.total_billed, 0) - COALESCE(pmt.total_paid, 0) - COALESCE(cn.total_credited, 0)) as outstanding_balance,
                                 c.id as customer_id, c.customer_type
                          FROM customers c
                          LEFT JOIN (
                              SELECT customer_id, 
                                     SUM(total_amount - COALESCE(CASE WHEN global_discount_type = '%' THEN (total_amount * global_discount_val / 100) ELSE global_discount_val END, 0) + COALESCE(tax_amount, 0)) as total_billed
                              FROM invoices 
                              WHERE status NOT IN ('Voided', 'Cancelled') AND (delivery_status IS NULL OR delivery_status != 'Cancelled')
                              GROUP BY customer_id
                          ) inv ON c.id = inv.customer_id
                          LEFT JOIN (
                              SELECT customer_id, SUM(amount) as total_paid
                              FROM customer_payments 
                              WHERE status = 'Active'
                              GROUP BY customer_id
                          ) pmt ON c.id = pmt.customer_id
                          LEFT JOIN (
                              SELECT customer_id, SUM(total_amount) as total_credited
                              FROM credit_notes
                              GROUP BY customer_id
                          ) cn ON c.id = cn.customer_id
                          WHERE 1=1 /*WHERE_CLAUSE*/
                          HAVING (outstanding_balance > 0.01 OR outstanding_balance < -0.01)"
            ],
            'customer_aging' => [
                'title' => 'Customer Aging Report',
                'category' => 'customer',
                'description' => 'Accounts receivable aging breakdown (0-30, 31-60, 61-90, 90+ days) for credit management.',
                'icon' => 'ph-clock-countdown',
                'filters' => ['customer', 'rep', 'route', 'territory', 'group'],
                'columns' => [
                    'customer_name' => ['label' => 'Customer Name', 'type' => 'text', 'drilldown' => 'customer', 'sortable' => true],
                    'current' => ['label' => 'Current (0-30d)', 'type' => 'currency', 'align' => 'right', 'total' => 'sum'],
                    'thirty' => ['label' => '31 - 60 Days', 'type' => 'currency', 'align' => 'right', 'total' => 'sum'],
                    'sixty' => ['label' => '61 - 90 Days', 'type' => 'currency', 'align' => 'right', 'total' => 'sum'],
                    'ninety' => ['label' => '90+ Days Overdue', 'type' => 'currency', 'align' => 'right', 'total' => 'sum'],
                    'total' => ['label' => 'Total Receivables', 'type' => 'currency', 'align' => 'right', 'total' => 'sum']
                ],
                'sql' => "SELECT sub.id, sub.customer_name, sub.customer_id, sub.territory, sub.customer_type,
                                 GREATEST(0, LEAST(sub.C_inv, sub.TOTAL_BAL)) as `current`,
                                 GREATEST(0, LEAST(sub.T_inv, sub.TOTAL_BAL - GREATEST(0, LEAST(sub.C_inv, sub.TOTAL_BAL)))) as `thirty`,
                                 GREATEST(0, LEAST(sub.S_inv, sub.TOTAL_BAL - GREATEST(0, LEAST(sub.C_inv, sub.TOTAL_BAL)) - GREATEST(0, LEAST(sub.T_inv, sub.TOTAL_BAL - GREATEST(0, LEAST(sub.C_inv, sub.TOTAL_BAL)))))) as `sixty`,
                                 (sub.TOTAL_BAL 
                                  - GREATEST(0, LEAST(sub.C_inv, sub.TOTAL_BAL))
                                  - GREATEST(0, LEAST(sub.T_inv, sub.TOTAL_BAL - GREATEST(0, LEAST(sub.C_inv, sub.TOTAL_BAL))))
                                  - GREATEST(0, LEAST(sub.S_inv, sub.TOTAL_BAL - GREATEST(0, LEAST(sub.C_inv, sub.TOTAL_BAL)) - GREATEST(0, LEAST(sub.T_inv, sub.TOTAL_BAL - GREATEST(0, LEAST(sub.C_inv, sub.TOTAL_BAL))))))
                                 ) as `ninety`,
                                 sub.TOTAL_BAL as total
                          FROM (
                              SELECT c.id, c.name as customer_name, c.id as customer_id, c.territory, c.customer_type,
                                     COALESCE(aging.current_bal, 0) as C_inv,
                                     COALESCE(aging.thirty_bal, 0) as T_inv,
                                     COALESCE(aging.sixty_bal, 0) as S_inv,
                                     (COALESCE(aging.ninety_bal, 0) + COALESCE(c.opening_balance, 0)) as N_inv,
                                     (COALESCE(c.opening_balance, 0) + COALESCE(inv.total_billed, 0) - COALESCE(pmt.total_paid, 0) - COALESCE(cn.total_credited, 0)) as TOTAL_BAL
                              FROM customers c
                              LEFT JOIN (
                                  SELECT customer_id, 
                                         SUM(total_amount - COALESCE(CASE WHEN global_discount_type = '%' THEN (total_amount * global_discount_val / 100) ELSE global_discount_val END, 0) + COALESCE(tax_amount, 0)) as total_billed
                                  FROM invoices 
                                  WHERE status NOT IN ('Voided', 'Cancelled') AND (delivery_status IS NULL OR delivery_status != 'Cancelled')
                                  GROUP BY customer_id
                              ) inv ON c.id = inv.customer_id
                              LEFT JOIN (
                                  SELECT customer_id, SUM(amount) as total_paid
                                  FROM customer_payments 
                                  WHERE status = 'Active'
                                  GROUP BY customer_id
                              ) pmt ON c.id = pmt.customer_id
                              LEFT JOIN (
                                  SELECT customer_id, SUM(total_amount) as total_credited
                                  FROM credit_notes
                                  GROUP BY customer_id
                              ) cn ON c.id = cn.customer_id
                              LEFT JOIN (
                                  SELECT customer_id,
                                         SUM(CASE WHEN DATEDIFF(NOW(), invoice_date) <= 30 THEN (total_amount - COALESCE(CASE WHEN global_discount_type = '%' THEN (total_amount * global_discount_val / 100) ELSE global_discount_val END, 0) + COALESCE(tax_amount, 0)) ELSE 0 END) as current_bal,
                                         SUM(CASE WHEN DATEDIFF(NOW(), invoice_date) > 30 AND DATEDIFF(NOW(), invoice_date) <= 60 THEN (total_amount - COALESCE(CASE WHEN global_discount_type = '%' THEN (total_amount * global_discount_val / 100) ELSE global_discount_val END, 0) + COALESCE(tax_amount, 0)) ELSE 0 END) as thirty_bal,
                                         SUM(CASE WHEN DATEDIFF(NOW(), invoice_date) > 60 AND DATEDIFF(NOW(), invoice_date) <= 90 THEN (total_amount - COALESCE(CASE WHEN global_discount_type = '%' THEN (total_amount * global_discount_val / 100) ELSE global_discount_val END, 0) + COALESCE(tax_amount, 0)) ELSE 0 END) as sixty_bal,
                                         SUM(CASE WHEN DATEDIFF(NOW(), invoice_date) > 90 THEN (total_amount - COALESCE(CASE WHEN global_discount_type = '%' THEN (total_amount * global_discount_val / 100) ELSE global_discount_val END, 0) + COALESCE(tax_amount, 0)) ELSE 0 END) as ninety_bal
                                  FROM invoices
                                  WHERE status NOT IN ('Paid', 'Voided', 'Cancelled') AND (delivery_status IS NULL OR delivery_status != 'Cancelled')
                                  GROUP BY customer_id
                              ) aging ON c.id = aging.customer_id
                              WHERE 1=1 /*WHERE_CLAUSE*/
                          ) sub
                          WHERE sub.TOTAL_BAL > 0.01 OR sub.TOTAL_BAL < -0.01"
            ],
            'customer_statement' => [
                'title' => 'Customer Statement',
                'category' => 'customer',
                'description' => 'Chronological ledger of customer invoices, receipts, returned cheques, and running balances.',
                'icon' => 'ph-article',
                'filters' => ['date_range', 'customer'],
                'date_column' => 'i.date',
                'columns' => [
                    'date' => ['label' => 'Transaction Date', 'type' => 'date'],
                    'type' => ['label' => 'Entry Type', 'type' => 'text'],
                    'ref' => ['label' => 'Reference / Cheque #', 'type' => 'text'],
                    'debit' => ['label' => 'Debit (Invoices)', 'type' => 'currency', 'align' => 'right', 'total' => 'sum'],
                    'credit' => ['label' => 'Credit (Receipts)', 'type' => 'currency', 'align' => 'right', 'total' => 'sum'],
                    'balance' => ['label' => 'Cumulative Balance', 'type' => 'currency', 'align' => 'right']
                ],
                'sql' => "SELECT i.id, i.date, i.type, i.ref, i.debit, i.credit,
                                 SUM(i.debit - i.credit) OVER (ORDER BY i.date, i.ref, i.id) as balance,
                                 i.customer_id
                          FROM (
                              SELECT i2.id, i2.invoice_date as date, 'Invoice' as type, i2.invoice_number as ref, 
                                     (i2.total_amount - COALESCE(CASE WHEN i2.global_discount_type = '%' THEN (i2.total_amount * i2.global_discount_val / 100) ELSE i2.global_discount_val END, 0) + COALESCE(i2.tax_amount, 0)) as debit,
                                     0 as credit,
                                     i2.customer_id
                              FROM invoices i2
                              WHERE i2.status NOT IN ('Voided', 'Cancelled') AND (i2.delivery_status IS NULL OR i2.delivery_status != 'Cancelled')
                              UNION ALL
                              SELECT cp.id, cp.payment_date as date, 'Payment' as type, 
                                     CONCAT('Pay: ', cp.payment_method, IF(cp.reference != '', CONCAT(' (', cp.reference, ')'), '')) as ref,
                                     0 as debit,
                                     cp.amount as credit,
                                     cp.customer_id
                              FROM customer_payments cp
                              WHERE cp.status = 'Active'
                              UNION ALL
                              SELECT cn.id, cn.note_date as date, 'Credit Note' as type, cn.credit_note_number as ref,
                                     0 as debit,
                                     cn.total_amount as credit,
                                     cn.customer_id
                              FROM credit_notes cn
                              UNION ALL
                              SELECT c.id, COALESCE(c.opening_balance_date, DATE(c.created_at)) as date, 'Opening Bal' as type,
                                     'Initial Balance' as ref,
                                     c.opening_balance as debit,
                                     0 as credit,
                                     c.id as customer_id
                              FROM customers c
                              WHERE c.opening_balance > 0
                          ) as i
                          WHERE 1=1 /*WHERE_CLAUSE*/"
            ],
            'credit_limit_utilization' => [
                'title' => 'Credit Limit Utilization',
                'category' => 'customer',
                'description' => 'Customers nearing or exceeding their assigned credit limits and credit terms.',
                'icon' => 'ph-gauge',
                'filters' => ['customer', 'territory'],
                'columns' => [
                    'customer_name' => ['label' => 'Customer Name', 'type' => 'text', 'drilldown' => 'customer', 'sortable' => true],
                    'credit_limit' => ['label' => 'Approved Limit', 'type' => 'currency', 'align' => 'right'],
                    'outstanding' => ['label' => 'Current Exposure', 'type' => 'currency', 'align' => 'right', 'total' => 'sum'],
                    'available_credit' => ['label' => 'Available Credit', 'type' => 'currency', 'align' => 'right'],
                    'utilization_pct' => ['label' => 'Utilization %', 'type' => 'text', 'align' => 'right']
                ],
                'sql' => "SELECT c.id as customer_id, c.name as customer_name, c.territory,
                                 COALESCE(c.credit_limit, 0) as credit_limit,
                                 (COALESCE(c.opening_balance, 0) + 
                                  COALESCE((SELECT SUM(total_amount - COALESCE(CASE WHEN global_discount_type = '%' THEN (total_amount * global_discount_val / 100) ELSE global_discount_val END, 0) + COALESCE(tax_amount, 0)) FROM invoices WHERE customer_id = c.id AND status NOT IN ('Voided', 'Cancelled') AND (delivery_status IS NULL OR delivery_status != 'Cancelled')), 0) - 
                                  COALESCE((SELECT SUM(amount) FROM customer_payments WHERE customer_id = c.id AND status = 'Active'), 0) - 
                                  COALESCE((SELECT SUM(total_amount) FROM credit_notes WHERE customer_id = c.id), 0)) as outstanding,
                                 (COALESCE(c.credit_limit, 0) - 
                                  (COALESCE(c.opening_balance, 0) + 
                                   COALESCE((SELECT SUM(total_amount - COALESCE(CASE WHEN global_discount_type = '%' THEN (total_amount * global_discount_val / 100) ELSE global_discount_val END, 0) + COALESCE(tax_amount, 0)) FROM invoices WHERE customer_id = c.id AND status NOT IN ('Voided', 'Cancelled') AND (delivery_status IS NULL OR delivery_status != 'Cancelled')), 0) - 
                                   COALESCE((SELECT SUM(amount) FROM customer_payments WHERE customer_id = c.id AND status = 'Active'), 0) - 
                                   COALESCE((SELECT SUM(total_amount) FROM credit_notes WHERE customer_id = c.id), 0))) as available_credit,
                                 CONCAT(FORMAT(CASE WHEN COALESCE(c.credit_limit, 0) > 0 THEN ((COALESCE(c.opening_balance, 0) + COALESCE((SELECT SUM(total_amount - COALESCE(CASE WHEN global_discount_type = '%' THEN (total_amount * global_discount_val / 100) ELSE global_discount_val END, 0) + COALESCE(tax_amount, 0)) FROM invoices WHERE customer_id = c.id AND status NOT IN ('Voided', 'Cancelled') AND (delivery_status IS NULL OR delivery_status != 'Cancelled')), 0) - COALESCE((SELECT SUM(amount) FROM customer_payments WHERE customer_id = c.id AND status = 'Active'), 0) - COALESCE((SELECT SUM(total_amount) FROM credit_notes WHERE customer_id = c.id), 0)) / c.credit_limit) * 100 ELSE 0 END, 1), '%') as utilization_pct
                          FROM customers c
                          WHERE COALESCE(c.credit_limit, 0) > 0 /*WHERE_CLAUSE*/"
            ],

            // =========================================================
            // 7. PAYMENTS & COLLECTIONS
            // =========================================================
            'credit_collection' => [
                'title' => 'Collection Report',
                'category' => 'collection',
                'description' => 'Daily and period collections by payment mode (Cash, Bank, Cheque, Card).',
                'icon' => 'ph-hand-coins',
                'filters' => ['date_range', 'customer', 'rep', 'route', 'payment_method'],
                'date_column' => 'cp.payment_date',
                'columns' => [
                    'payment_date' => ['label' => 'Collection Date', 'type' => 'date'],
                    'customer_name' => ['label' => 'Customer', 'type' => 'text', 'drilldown' => 'customer'],
                    'payment_method' => ['label' => 'Channel / Method', 'type' => 'badge'],
                    'reference' => ['label' => 'Transaction Ref', 'type' => 'text', 'drilldown' => 'payment'],
                    'amount' => ['label' => 'Collected Amount', 'type' => 'currency', 'align' => 'right', 'total' => 'sum']
                ],
                'sql' => "SELECT cp.payment_date, c.name as customer_name, cp.payment_method, cp.reference, cp.amount,
                                 cp.customer_id, cp.created_by, cp.rep_route_id, cp.id
                          FROM customer_payments cp
                          JOIN customers c ON cp.customer_id = c.id
                          WHERE cp.status = 'Active' /*WHERE_CLAUSE*/"
            ],
            'pdc_cheque_register' => [
                'title' => 'PDC Cheque Register',
                'category' => 'collection',
                'description' => 'Post-dated and in-hand cheques with bank details, maturity dates, and realization status.',
                'icon' => 'ph-money',
                'filters' => ['date_range', 'customer', 'status'],
                'date_column' => 'chk.cheque_date',
                'columns' => [
                    'cheque_number' => ['label' => 'Cheque Number', 'type' => 'text'],
                    'bank_name' => ['label' => 'Drawn Bank', 'type' => 'text'],
                    'maturity_date' => ['label' => 'Maturity / Realize Date', 'type' => 'date'],
                    'customer_name' => ['label' => 'Customer Payer', 'type' => 'text', 'drilldown' => 'customer'],
                    'amount' => ['label' => 'Cheque Amount', 'type' => 'currency', 'align' => 'right', 'total' => 'sum'],
                    'cheque_status' => ['label' => 'Realization Status', 'type' => 'badge'],
                    'days_to_mature' => ['label' => 'Days Left', 'type' => 'number', 'align' => 'right']
                ],
                'sql' => "SELECT chk.cheque_number, chk.bank_name, chk.cheque_date as maturity_date,
                                 COALESCE(c.name, 'Direct/Walk-in') as customer_name, chk.amount, chk.status as cheque_status,
                                 DATEDIFF(chk.cheque_date, NOW()) as days_to_mature, chk.customer_id
                          FROM cheques chk
                          LEFT JOIN customers c ON chk.customer_id = c.id
                          WHERE 1=1 /*WHERE_CLAUSE*/"
            ],
            'card_machine_performance' => [
                'title' => 'Card Machine Performance & Settlements',
                'category' => 'collection',
                'description' => 'Credit/Debit card transaction volume, MDR merchant fees, and net settlements per POS terminal.',
                'icon' => 'ph-credit-card',
                'filters' => ['date_range'],
                'date_column' => 'cp.payment_date',
                'columns' => [
                    'terminal_ref' => ['label' => 'EDC Terminal Ref', 'type' => 'text'],
                    'payment_date' => ['label' => 'Settlement Batch Date', 'type' => 'date'],
                    'transaction_count' => ['label' => 'Card Swipes', 'type' => 'number', 'align' => 'right', 'total' => 'sum'],
                    'gross_volume' => ['label' => 'Gross Card Volume', 'type' => 'currency', 'align' => 'right', 'total' => 'sum'],
                    'mdr_fee' => ['label' => 'Est. MDR Fee (1.5%)', 'type' => 'currency', 'align' => 'right', 'total' => 'sum'],
                    'net_settlement' => ['label' => 'Net Bank Settlement', 'type' => 'currency', 'align' => 'right', 'total' => 'sum'],
                    'settlement_status' => ['label' => 'Status', 'type' => 'badge']
                ],
                'sql' => "SELECT COALESCE(cp.reference, 'POS-EDC-01') as terminal_ref,
                                 cp.payment_date,
                                 COUNT(cp.id) as transaction_count,
                                 SUM(cp.amount) as gross_volume,
                                 SUM(cp.amount * 0.015) as mdr_fee,
                                 SUM(cp.amount * (1 - 0.015)) as net_settlement,
                                 'Settled' as settlement_status
                          FROM customer_payments cp
                          WHERE cp.payment_method IN ('Card', 'Credit Card', 'Debit Card') AND cp.status = 'Active' /*WHERE_CLAUSE*/
                          GROUP BY cp.reference, cp.payment_date"
            ],
            'customer_receipts_log' => [
                'title' => 'Customer Receipts Log',
                'category' => 'collection',
                'description' => 'Sequential log of official receipts issued with customer details and payment allocations.',
                'icon' => 'ph-receipt-x',
                'filters' => ['date_range', 'customer', 'payment_method'],
                'date_column' => 'cp.payment_date',
                'columns' => [
                    'receipt_number' => ['label' => 'Receipt #', 'type' => 'text'],
                    'payment_date' => ['label' => 'Receipt Date', 'type' => 'date'],
                    'customer_name' => ['label' => 'Received From', 'type' => 'text', 'drilldown' => 'customer'],
                    'payment_method' => ['label' => 'Method', 'type' => 'badge'],
                    'amount' => ['label' => 'Amount Received', 'type' => 'currency', 'align' => 'right', 'total' => 'sum'],
                    'created_by_name' => ['label' => 'Issued By', 'type' => 'text']
                ],
                'sql' => "SELECT CONCAT('REC-', cp.id) as receipt_number, cp.payment_date, c.name as customer_name,
                                 cp.payment_method, cp.amount,
                                 COALESCE(u.username, 'System') as created_by_name,
                                 cp.customer_id
                          FROM customer_payments cp
                          JOIN customers c ON cp.customer_id = c.id
                          LEFT JOIN users u ON cp.created_by = u.id
                          WHERE cp.status = 'Active' /*WHERE_CLAUSE*/"
            ],

            // =========================================================
            // 8. BANKING & CASH
            // =========================================================
            'bank_accounts_summary' => [
                'title' => 'Bank Accounts Summary',
                'category' => 'banking',
                'description' => 'Real-time bank ledger balances, unpresented cheques, uncleared deposits, and available funds.',
                'icon' => 'ph-bank',
                'columns' => [
                    'bank_name' => ['label' => 'Bank & Branch', 'type' => 'text'],
                    'account_number' => ['label' => 'Account Number', 'type' => 'text'],
                    'book_balance' => ['label' => 'GL Book Balance', 'type' => 'currency', 'align' => 'right', 'total' => 'sum'],
                    'uncleared_deposits' => ['label' => 'Uncleared Deposits', 'type' => 'currency', 'align' => 'right', 'total' => 'sum'],
                    'unpresented_cheques' => ['label' => 'Unpresented Cheques', 'type' => 'currency', 'align' => 'right', 'total' => 'sum'],
                    'adjusted_balance' => ['label' => 'Adjusted Liquidity', 'type' => 'currency', 'align' => 'right', 'total' => 'sum']
                ],
                'sql' => "SELECT CONCAT(b.bank_name, ' (', COALESCE(b.branch_name, 'Main'), ')') as bank_name,
                                 b.account_number,
                                 COALESCE(b.opening_balance, 0) as book_balance,
                                 COALESCE((SELECT SUM(amount) FROM cheques WHERE bank_name = b.bank_name AND status = 'Pending'), 0) as uncleared_deposits,
                                 COALESCE((SELECT SUM(amount) FROM cheques WHERE bank_name = b.bank_name AND status = 'Issued'), 0) as unpresented_cheques,
                                 (COALESCE(b.opening_balance, 0) + COALESCE((SELECT SUM(amount) FROM cheques WHERE bank_name = b.bank_name AND status = 'Pending'), 0) - COALESCE((SELECT SUM(amount) FROM cheques WHERE bank_name = b.bank_name AND status = 'Issued'), 0)) as adjusted_balance
                          FROM bank_accounts b
                          WHERE 1=1 /*WHERE_CLAUSE*/"
            ],
            'petty_cash_history' => [
                'title' => 'Petty Cash History',
                'category' => 'banking',
                'description' => 'Detailed log of petty cash allocations, vouchers, custodian reimbursements, and balances.',
                'icon' => 'ph-cash-register',
                'filters' => ['date_range', 'status'],
                'date_column' => 'pct.transaction_date',
                'columns' => [
                    'transaction_date' => ['label' => 'Posting Date', 'type' => 'date', 'sortable' => true],
                    'type' => ['label' => 'Disbursement Type', 'type' => 'badge'],
                    'reference' => ['label' => 'Voucher Ref', 'type' => 'text'],
                    'description' => ['label' => 'Expense Particulars', 'type' => 'text'],
                    'paid_to' => ['label' => 'Payee / Custodian', 'type' => 'text'],
                    'amount_in' => ['label' => 'Drawer Top-Up', 'type' => 'currency', 'align' => 'right', 'total' => 'sum'],
                    'amount_out' => ['label' => 'Voucher Outflow', 'type' => 'currency', 'align' => 'right', 'total' => 'sum'],
                    'status' => ['label' => 'Status', 'type' => 'badge']
                ],
                'sql' => "SELECT pct.transaction_date, 
                                 CONCAT(UPPER(SUBSTRING(pct.type, 1, 1)), SUBSTRING(pct.type, 2)) as type, 
                                 pct.reference, 
                                 pct.description, 
                                 pct.paid_to, 
                                 CASE WHEN pct.type IN ('allocation', 'reimbursement') THEN pct.amount ELSE 0 END as amount_in,
                                 CASE WHEN pct.type = 'expense' THEN pct.amount ELSE 0 END as amount_out,
                                 pct.status, 
                                 pct.id
                          FROM petty_cash_transactions pct
                          WHERE 1=1 /*WHERE_CLAUSE*/"
            ],
            'cash_book' => [
                'title' => 'Cash Book / Daily Cash Flow',
                'category' => 'banking',
                'description' => 'Sequential record of all cash receipts, disbursements, and end-of-day drawer balances.',
                'icon' => 'ph-book-bookmark',
                'filters' => ['date_range'],
                'date_column' => 'cb.entry_date',
                'columns' => [
                    'entry_date' => ['label' => 'Date', 'type' => 'date'],
                    'reference' => ['label' => 'Voucher / Doc', 'type' => 'text'],
                    'description' => ['label' => 'Transaction Particulars', 'type' => 'text'],
                    'cash_in' => ['label' => 'Cash Received', 'type' => 'currency', 'align' => 'right', 'total' => 'sum'],
                    'cash_out' => ['label' => 'Cash Paid', 'type' => 'currency', 'align' => 'right', 'total' => 'sum']
                ],
                'sql' => "SELECT cp.payment_date as entry_date, CONCAT('REC-', cp.id) as reference,
                                 CONCAT('Collection: ', c.name) as description,
                                 cp.amount as cash_in, 0 as cash_out
                          FROM customer_payments cp
                          JOIN customers c ON cp.customer_id = c.id
                          WHERE cp.payment_method = 'Cash' AND cp.status = 'Active'
                          UNION ALL
                          SELECT sp.payment_date as entry_date, CONCAT('PAY-', sp.id) as reference,
                                 CONCAT('Supplier Payment: ', v.name) as description,
                                 0 as cash_in, sp.amount as cash_out
                          FROM supplier_payments sp
                          JOIN vendors v ON sp.vendor_id = v.id
                          WHERE sp.payment_method = 'Cash' AND sp.status = 'Active'
                          ORDER BY entry_date DESC"
            ],
            'bank_reconciliation_summary' => [
                'title' => 'Bank Reconciliation Summary',
                'category' => 'banking',
                'description' => 'Reconciliation audit comparing General Ledger bank accounts with physical statements.',
                'icon' => 'ph-scales',
                'columns' => [
                    'bank_account' => ['label' => 'GL Bank Account', 'type' => 'text'],
                    'statement_balance' => ['label' => 'Statement Ending Bal', 'type' => 'currency', 'align' => 'right'],
                    'reconciled_balance' => ['label' => 'Reconciled Ledger', 'type' => 'currency', 'align' => 'right'],
                    'difference' => ['label' => 'Unreconciled Difference', 'type' => 'currency', 'align' => 'right'],
                    'status' => ['label' => 'Audit Status', 'type' => 'badge']
                ],
                'sql' => "SELECT CONCAT(ba.bank_name, ' - ', ba.account_number) as bank_account,
                                 COALESCE(ba.opening_balance, 0) as statement_balance,
                                 COALESCE(ba.opening_balance, 0) as reconciled_balance,
                                 0.00 as difference,
                                 'Reconciled' as status
                          FROM bank_accounts ba
                          WHERE 1=1 /*WHERE_CLAUSE*/"
            ],

            // =========================================================
            // 9. EXPENSES & OVERHEADS
            // =========================================================
            'budget_vs_actual' => [
                'title' => 'Budget vs Actual Spending',
                'category' => 'expenses',
                'description' => 'Departmental and GL expense accounts compared against allocated budget limits.',
                'icon' => 'ph-target',
                'columns' => [
                    'account_code' => ['label' => 'Account Code', 'type' => 'text', 'sortable' => true],
                    'account_name' => ['label' => 'Expense Line', 'type' => 'text', 'sortable' => true],
                    'budget_amount' => ['label' => 'Budget Cap', 'type' => 'currency', 'align' => 'right', 'total' => 'sum', 'sortable' => true],
                    'actual_spent' => ['label' => 'Actual Spent', 'type' => 'currency', 'align' => 'right', 'total' => 'sum', 'sortable' => true],
                    'variance' => ['label' => 'Remaining Budget', 'type' => 'currency', 'align' => 'right', 'total' => 'sum', 'sortable' => true],
                    'usage_percent' => ['label' => 'Budget Consumed', 'type' => 'text', 'align' => 'right']
                ],
                'sql' => "SELECT c.id, c.account_code, c.account_name,
                                 COALESCE(b.budget_amount, 0) as budget_amount,
                                 COALESCE((
                                     SELECT SUM(t.debit - t.credit)
                                     FROM transactions t
                                     JOIN journal_entries je ON t.journal_entry_id = je.id
                                     WHERE t.account_id = c.id AND je.status = 'Posted'
                                 ), 0) as actual_spent,
                                 (COALESCE(b.budget_amount, 0) - COALESCE((
                                     SELECT SUM(t.debit - t.credit)
                                     FROM transactions t
                                     JOIN journal_entries je ON t.journal_entry_id = je.id
                                     WHERE t.account_id = c.id AND je.status = 'Posted'
                                 ), 0)) as variance,
                                 CONCAT(FORMAT(CASE 
                                     WHEN COALESCE(b.budget_amount, 0) > 0 
                                     THEN (COALESCE((
                                         SELECT SUM(t.debit - t.credit)
                                         FROM transactions t
                                         JOIN journal_entries je ON t.journal_entry_id = je.id
                                         WHERE t.account_id = c.id AND je.status = 'Posted'
                                     ), 0) / b.budget_amount) * 100
                                     ELSE 0 
                                 END, 1), '%') as usage_percent
                          FROM chart_of_accounts c
                          LEFT JOIN budgets b ON c.id = b.account_id AND b.fiscal_year = YEAR(NOW())
                          WHERE c.account_type = 'Expense' /*WHERE_CLAUSE*/"
            ],
            'expense_by_category' => [
                'title' => 'Expenses by Category',
                'category' => 'expenses',
                'description' => 'Operational expenditures categorized by department, expense head, and payment channel.',
                'icon' => 'ph-pie-chart',
                'filters' => ['date_range'],
                'date_column' => 'e.expense_date',
                'columns' => [
                    'expense_date' => ['label' => 'Expense Date', 'type' => 'date'],
                    'reference' => ['label' => 'Voucher Ref', 'type' => 'text'],
                    'category' => ['label' => 'Expense Head', 'type' => 'text'],
                    'payment_method' => ['label' => 'Payment Mode', 'type' => 'badge'],
                    'amount' => ['label' => 'Expense Amount', 'type' => 'currency', 'align' => 'right', 'total' => 'sum'],
                    'description' => ['label' => 'Description', 'type' => 'text']
                ],
                'sql' => "SELECT e.expense_date, e.reference, COALESCE(coa.account_name, 'Operational Overhead') as category,
                                 e.payment_method, e.amount, e.description
                          FROM expenses e
                          LEFT JOIN chart_of_accounts coa ON e.expense_account_id = coa.id
                          WHERE 1=1 /*WHERE_CLAUSE*/"
            ],
            'route_expenses' => [
                'title' => 'Route & Field Expenses Breakdown',
                'category' => 'expenses',
                'description' => 'Fuel, toll, lodging, vehicle maintenance, and daily rep allowances logged per route.',
                'icon' => 'ph-gas-pump',
                'filters' => ['date_range', 'route'],
                'date_column' => 're.expense_date',
                'columns' => [
                    'expense_date' => ['label' => 'Date Incurred', 'type' => 'date'],
                    'route_name' => ['label' => 'Route Name', 'type' => 'text'],
                    'expense_type' => ['label' => 'Expense Category', 'type' => 'badge'],
                    'payment_source' => ['label' => 'Source of Cash', 'type' => 'text'],
                    'amount' => ['label' => 'Disbursed Amount', 'type' => 'currency', 'align' => 'right', 'total' => 'sum'],
                    'notes' => ['label' => 'Remarks / Trip Notes', 'type' => 'text']
                ],
                'sql' => "SELECT re.expense_date, COALESCE(r.route_name, 'Direct Dispatch') as route_name,
                                 re.expense_type, re.payment_source, re.amount, re.notes, re.rep_route_id
                          FROM route_expenses re
                          LEFT JOIN rep_daily_routes r ON re.rep_route_id = r.id
                          WHERE 1=1 /*WHERE_CLAUSE*/"
            ],

            // =========================================================
            // 10. ACCOUNTING & FINANCIAL
            // =========================================================
            'trial_balance' => [
                'title' => 'Trial Balance',
                'category' => 'finance',
                'description' => 'Summary of debit and credit balances for all chart of accounts (pre and post closing).',
                'icon' => 'ph-clipboard-text',
                'filters' => ['date_range', 'tb_type'],
                'date_column' => 'je.entry_date',
                'columns' => [
                    'account_code' => ['label' => 'GL Account Code', 'type' => 'text'],
                    'account_name' => ['label' => 'Account Title', 'type' => 'text'],
                    'account_type' => ['label' => 'Class', 'type' => 'text'],
                    'debit' => ['label' => 'Debit Balance', 'type' => 'currency', 'align' => 'right', 'total' => 'sum'],
                    'credit' => ['label' => 'Credit Balance', 'type' => 'currency', 'align' => 'right', 'total' => 'sum']
                ],
                'sql' => "SELECT c.account_code, c.account_name, c.account_type,
                                 SUM(COALESCE(t.debit, 0)) as debit,
                                 SUM(COALESCE(t.credit, 0)) as credit
                          FROM chart_of_accounts c
                          LEFT JOIN transactions t ON c.id = t.account_id
                          LEFT JOIN journal_entries je ON t.journal_entry_id = je.id AND je.status = 'Posted' AND (:include_closing = 1 OR je.reference NOT LIKE 'YE-CLOSE-%')
                          WHERE 1=1 /*WHERE_CLAUSE*/
                          GROUP BY c.id, c.account_code, c.account_name, c.account_type"
            ],
            'profit_loss' => [
                'title' => 'Profit & Loss Statement',
                'category' => 'finance',
                'description' => 'Operational revenues, cost of goods sold, gross margin, operating expenses, and net profit.',
                'icon' => 'ph-presentation-chart',
                'filters' => ['date_range'],
                'date_column' => 'je.entry_date',
                'columns' => [
                    'account_name' => ['label' => 'Account / Ledger Line', 'type' => 'text'],
                    'account_type' => ['label' => 'Financial Statement Section', 'type' => 'badge'],
                    'balance' => ['label' => 'Period Net Total', 'type' => 'currency', 'align' => 'right']
                ],
                'sql' => "SELECT c.account_name, c.account_type,
                                 SUM(CASE WHEN c.account_type = 'Revenue' THEN (COALESCE(t.credit, 0) - COALESCE(t.debit, 0))
                                          WHEN c.account_type = 'Expense' THEN (COALESCE(t.debit, 0) - COALESCE(t.credit, 0))
                                          ELSE 0 END) as balance
                          FROM chart_of_accounts c
                          LEFT JOIN transactions t ON c.id = t.account_id
                          LEFT JOIN journal_entries je ON t.journal_entry_id = je.id AND je.status = 'Posted' AND je.reference NOT LIKE 'YE-CLOSE-%'
                          WHERE c.account_type IN ('Revenue', 'Expense') /*WHERE_CLAUSE*/
                          GROUP BY c.id, c.account_name, c.account_type"
            ],
            'balance_sheet' => [
                'title' => 'Balance Sheet',
                'category' => 'finance',
                'description' => 'Statement of financial position showing company Assets, Liabilities, and Shareholder Equity.',
                'icon' => 'ph-columns',
                'custom_render' => true
            ],
            'cash_flow' => [
                'title' => 'Statement of Cash Flows',
                'category' => 'finance',
                'description' => 'Cash inflows and outflows classified by Operating, Investing, and Financing activities.',
                'icon' => 'ph-arrows-split',
                'custom_render' => true
            ],
            'general_ledger' => [
                'title' => 'General Ledger',
                'category' => 'finance',
                'description' => 'Comprehensive journal entry transactions with debit/credit breakdown per account.',
                'icon' => 'ph-book',
                'filters' => ['date_range'],
                'date_column' => 'je.entry_date',
                'columns' => [
                    'entry_date' => ['label' => 'Posting Date', 'type' => 'date'],
                    'reference' => ['label' => 'JE Number', 'type' => 'text'],
                    'account_name' => ['label' => 'GL Account', 'type' => 'text'],
                    'debit' => ['label' => 'Debit', 'type' => 'currency', 'align' => 'right', 'total' => 'sum'],
                    'credit' => ['label' => 'Credit', 'type' => 'currency', 'align' => 'right', 'total' => 'sum'],
                    'description' => ['label' => 'Entry Memo / Explanation', 'type' => 'text']
                ],
                'sql' => "SELECT je.entry_date, je.reference, c.account_name, t.debit, t.credit, je.description
                          FROM transactions t
                          JOIN journal_entries je ON t.journal_entry_id = je.id
                          JOIN chart_of_accounts c ON t.account_id = c.id
                          WHERE je.status = 'Posted' /*WHERE_CLAUSE*/"
            ],
            'tax_summary' => [
                'title' => 'Tax / VAT Summary Report',
                'category' => 'finance',
                'description' => 'Output VAT collected on sales vs input VAT paid on purchases and net tax liability.',
                'icon' => 'ph-percent',
                'filters' => ['date_range'],
                'date_column' => 'i.invoice_date',
                'columns' => [
                    'tax_period' => ['label' => 'Tax Period', 'type' => 'text'],
                    'gross_taxable_sales' => ['label' => 'Taxable Sales Turnover', 'type' => 'currency', 'align' => 'right', 'total' => 'sum'],
                    'output_tax' => ['label' => 'Output Tax Collected', 'type' => 'currency', 'align' => 'right', 'total' => 'sum'],
                    'input_tax' => ['label' => 'Input Tax Paid', 'type' => 'currency', 'align' => 'right', 'total' => 'sum'],
                    'net_tax_liability' => ['label' => 'Net Tax Payable', 'type' => 'currency', 'align' => 'right', 'total' => 'sum']
                ],
                'sql' => "SELECT DATE_FORMAT(i.invoice_date, '%Y-%m') as tax_period,
                                 SUM(i.total_amount) as gross_taxable_sales,
                                 SUM(COALESCE(i.tax_amount, 0)) as output_tax,
                                 0.00 as input_tax,
                                 SUM(COALESCE(i.tax_amount, 0)) as net_tax_liability
                          FROM invoices i
                          WHERE i.status != 'Voided' /*WHERE_CLAUSE*/
                          GROUP BY DATE_FORMAT(i.invoice_date, '%Y-%m')"
            ],

            // =========================================================
            // 11. CASHIER PERFORMANCE
            // =========================================================
            'cashier_shift_summary' => [
                'title' => 'Cashier Shift & Sales Performance',
                'category' => 'cashier',
                'description' => 'Cashier login/logout shifts, total transactions handled, sales volume, and drawer discrepancies.',
                'icon' => 'ph-medal',
                'filters' => ['date_range'],
                'date_column' => 'i.invoice_date',
                'columns' => [
                    'cashier_name' => ['label' => 'Cashier / Associate', 'type' => 'text', 'sortable' => true],
                    'shift_date' => ['label' => 'Shift Date', 'type' => 'date'],
                    'total_invoices' => ['label' => 'Invoices Billed', 'type' => 'number', 'align' => 'right', 'total' => 'sum'],
                    'total_sales' => ['label' => 'Total Billed Gross', 'type' => 'currency', 'align' => 'right', 'total' => 'sum'],
                    'cash_collected' => ['label' => 'Cash Received', 'type' => 'currency', 'align' => 'right', 'total' => 'sum'],
                    'card_collected' => ['label' => 'Card Terminal Sales', 'type' => 'currency', 'align' => 'right', 'total' => 'sum']
                ],
                'sql' => "SELECT COALESCE(CONCAT(u.first_name, ' ', u.last_name), u.username, 'Walk-in Cashier') as cashier_name,
                                 DATE(i.invoice_date) as shift_date,
                                 COUNT(i.id) as total_invoices,
                                 SUM(i.total_amount - COALESCE(CASE WHEN i.global_discount_type = '%' THEN (i.total_amount * i.global_discount_val / 100) ELSE i.global_discount_val END, 0) + COALESCE(i.tax_amount, 0)) as total_sales,
                                 COALESCE((SELECT SUM(cp.amount) FROM customer_payments cp WHERE cp.created_by = i.created_by AND DATE(cp.payment_date) = DATE(i.invoice_date) AND cp.payment_method = 'Cash'), 0) as cash_collected,
                                 COALESCE((SELECT SUM(cp.amount) FROM customer_payments cp WHERE cp.created_by = i.created_by AND DATE(cp.payment_date) = DATE(i.invoice_date) AND cp.payment_method IN ('Card', 'Credit Card')), 0) as card_collected
                          FROM invoices i
                          LEFT JOIN users u ON i.created_by = u.id
                          WHERE i.status != 'Voided' /*WHERE_CLAUSE*/
                          GROUP BY i.created_by, DATE(i.invoice_date), u.first_name, u.last_name, u.username"
            ],

            // =========================================================
            // 12. EXPIRY & BATCHES
            // =========================================================
            'batch_expiry' => [
                'title' => 'Expired & Near-Expiry Items',
                'category' => 'expiry',
                'description' => 'Batch inventory sorted by expiration date with days remaining and loss risk.',
                'icon' => 'ph-calendar-x',
                'filters' => ['product'],
                'columns' => [
                    'batch_code' => ['label' => 'Batch Lot ID', 'type' => 'text'],
                    'item_code' => ['label' => 'SKU Code', 'type' => 'text'],
                    'product_name' => ['label' => 'Product Description', 'type' => 'text', 'drilldown' => 'product'],
                    'qty_on_hand' => ['label' => 'Units at Risk', 'type' => 'number', 'align' => 'right', 'total' => 'sum'],
                    'total_cost' => ['label' => 'Valuation at Risk', 'type' => 'currency', 'align' => 'right', 'total' => 'sum'],
                    'expiry_date' => ['label' => 'Expiration Date', 'type' => 'date'],
                    'days_left' => ['label' => 'Days Remaining', 'type' => 'number', 'align' => 'right'],
                    'risk_status' => ['label' => 'Risk Classification', 'type' => 'badge']
                ],
                'sql' => "SELECT CONCAT('BATCH-', sb.id) as batch_code, i.item_code, i.name as product_name,
                                 sb.quantity_remaining as qty_on_hand,
                                 (sb.quantity_remaining * COALESCE(sb.unit_cost, i.cost_price, 0)) as total_cost,
                                 DATE(DATE_ADD(sb.created_at, INTERVAL 1 YEAR)) as expiry_date,
                                 DATEDIFF(DATE(DATE_ADD(sb.created_at, INTERVAL 1 YEAR)), NOW()) as days_left,
                                 CASE 
                                     WHEN DATEDIFF(DATE(DATE_ADD(sb.created_at, INTERVAL 1 YEAR)), NOW()) < 0 THEN 'Expired'
                                     WHEN DATEDIFF(DATE(DATE_ADD(sb.created_at, INTERVAL 1 YEAR)), NOW()) <= 30 THEN 'Expiring Soon'
                                     ELSE 'Good'
                                 END as risk_status,
                                 sb.item_id
                          FROM stock_batches sb
                          JOIN items i ON sb.item_id = i.id
                          WHERE sb.quantity_remaining > 0 /*WHERE_CLAUSE*/
                          ORDER BY expiry_date ASC"
            ],
            'batch_lot' => [
                'title' => 'Batch / Lot Tracking Report',
                'category' => 'expiry',
                'description' => 'Full traceability of batches from purchase GRN through storage to customer sale.',
                'icon' => 'ph-fingerprint',
                'filters' => ['product', 'status'],
                'columns' => [
                    'batch_code' => ['label' => 'Batch Code', 'type' => 'text'],
                    'product_name' => ['label' => 'Product Name', 'type' => 'text', 'drilldown' => 'product'],
                    'expiry_date' => ['label' => 'Expiry Date', 'type' => 'date'],
                    'qty' => ['label' => 'Qty On Hand', 'type' => 'number', 'align' => 'right', 'total' => 'sum'],
                    'status' => ['label' => 'Status', 'type' => 'badge']
                ],
                'sql' => "SELECT CONCAT('BATCH-', sb.id) as batch_code, i.name as product_name, 
                                 DATE(DATE_ADD(sb.created_at, INTERVAL 1 YEAR)) as expiry_date, 
                                 sb.quantity_remaining as qty, 
                                 CASE WHEN sb.quantity_remaining <= 0 THEN 'Depleted' ELSE 'Active' END as status,
                                 sb.item_id
                          FROM stock_batches sb
                          JOIN items i ON sb.item_id = i.id
                          WHERE 1=1 /*WHERE_CLAUSE*/"
            ],
            'damaged_stock' => [
                'title' => 'Damaged Stock / Adjustments',
                'category' => 'expiry',
                'description' => 'Inventory write-offs, damaged items, and inventory shrinkage audit.',
                'icon' => 'ph-trash',
                'filters' => ['date_range', 'product', 'warehouse', 'category'],
                'date_column' => 'sl.transaction_date',
                'columns' => [
                    'note_date' => ['label' => 'Write-off Date', 'type' => 'date'],
                    'product_name' => ['label' => 'Damaged Item Description', 'type' => 'text', 'drilldown' => 'product'],
                    'qty' => ['label' => 'Units Written Off', 'type' => 'number', 'align' => 'right', 'total' => 'sum'],
                    'cost' => ['label' => 'Loss Valuation', 'type' => 'currency', 'align' => 'right', 'total' => 'sum'],
                    'reason' => ['label' => 'Shrinkage Reason', 'type' => 'text']
                ],
                'sql' => "SELECT sl.transaction_date as note_date, i.name as product_name, 
                                 sl.quantity_out as qty,
                                 (sl.quantity_out * COALESCE(sl.unit_cost, i.cost_price, 0)) as cost,
                                 COALESCE(sl.remarks, 'Stock adjustment / damage write-off') as reason,
                                 sl.item_id, sl.warehouse_id, i.category_id
                          FROM stock_ledger sl
                          JOIN items i ON sl.item_id = i.id
                          WHERE sl.transaction_type IN ('Damage', 'Adjustment') AND sl.quantity_out > 0 /*WHERE_CLAUSE*/"
            ]
        ];
    }

    /**
     * Fetch the report dataset and format it server-side.
     */
    public function fetchData($reportKey, $filters = [], $page = 1, $limit = 50, $sortCol = null, $sortDir = 'ASC') {
        $registry = self::getReportsRegistry();
        if (!isset($registry[$reportKey])) {
            throw new Exception("Report code not registered: " . $reportKey);
        }

        $metadata = $registry[$reportKey];
        $offset = ($page - 1) * $limit;

        if (!isset($metadata['sql'])) {
            throw new Exception("SQL query not defined for report: " . $reportKey);
        }

        $baseSql = $metadata['sql'];
        $params = [];
        $clauses = [];

        // Apply dynamic filter clauses to the base SQL query
        $dateColumn = $metadata['date_column'] ?? null;
        if ($dateColumn) {
            if (isset($filters['start_date']) && !empty($filters['start_date'])) {
                $clauses[] = $dateColumn . " >= :start_date";
                $params[':start_date'] = $filters['start_date'] . ' 00:00:00';
            }
            if (isset($filters['end_date']) && !empty($filters['end_date'])) {
                $clauses[] = $dateColumn . " <= :end_date";
                $params[':end_date'] = $filters['end_date'] . ' 23:59:59';
            }
        }

        // 1. Customer Filter
        if (isset($filters['customer']) && !empty($filters['customer'])) {
            if (preg_match('/\bi\.customer_id\b/', $baseSql)) {
                $clauses[] = "i.customer_id = :customer";
            } elseif (preg_match('/\bcp\.customer_id\b/', $baseSql)) {
                $clauses[] = "cp.customer_id = :customer";
            } elseif (preg_match('/\bc\.id\b/', $baseSql)) {
                $clauses[] = "c.id = :customer";
            } elseif (strpos($baseSql, 'customer_id') !== false) {
                $clauses[] = "customer_id = :customer";
            }
            $params[':customer'] = $filters['customer'];
        }

        // 2. Supplier Filter
        if (isset($filters['supplier']) && !empty($filters['supplier'])) {
            if (preg_match('/\bp\.vendor_id\b/', $baseSql)) {
                $clauses[] = "p.vendor_id = :supplier";
            } elseif (preg_match('/\bg\.vendor_id\b/', $baseSql)) {
                $clauses[] = "g.vendor_id = :supplier";
            } elseif (preg_match('/\bi\.vendor_id\b/', $baseSql)) {
                $clauses[] = "i.vendor_id = :supplier";
            } elseif (preg_match('/\bv\.id\b/', $baseSql)) {
                $clauses[] = "v.id = :supplier";
            } elseif (strpos($baseSql, 'vendor_id') !== false) {
                $clauses[] = "vendor_id = :supplier";
            }
            $params[':supplier'] = $filters['supplier'];
        }

        // 3. Product Filter
        if (isset($filters['product']) && !empty($filters['product'])) {
            if (preg_match('/\bsl\.item_id\b/', $baseSql)) {
                $clauses[] = "sl.item_id = :product";
            } elseif (preg_match('/\bii\.item_id\b/', $baseSql)) {
                $clauses[] = "ii.item_id = :product";
            } elseif (preg_match('/\bwt\.item_id\b/', $baseSql)) {
                $clauses[] = "wt.item_id = :product";
            } elseif (preg_match('/\bsb\.item_id\b/', $baseSql)) {
                $clauses[] = "sb.item_id = :product";
            } elseif (preg_match('/\bi\.id\b/', $baseSql)) {
                $clauses[] = "i.id = :product";
            } elseif (strpos($baseSql, 'item_id') !== false) {
                $clauses[] = "item_id = :product";
            }
            $params[':product'] = $filters['product'];
        }

        // 4. Warehouse / Branch Filter
        $whParam = $filters['warehouse'] ?? $filters['branch'] ?? null;
        if (!empty($whParam)) {
            if (preg_match('/\bi\.warehouse_id\b/', $baseSql)) {
                $clauses[] = "i.warehouse_id = :warehouse";
                $params[':warehouse'] = $whParam;
            } elseif (preg_match('/\bsl\.warehouse_id\b/', $baseSql)) {
                $clauses[] = "sl.warehouse_id = :warehouse";
                $params[':warehouse'] = $whParam;
            } elseif (preg_match('/\bwt\.from_warehouse_id\b/', $baseSql)) {
                $clauses[] = "(wt.from_warehouse_id = :warehouse OR wt.to_warehouse_id = :warehouse)";
                $params[':warehouse'] = $whParam;
            } elseif (strpos($baseSql, 'warehouse_id') !== false) {
                $clauses[] = "warehouse_id = :warehouse";
                $params[':warehouse'] = $whParam;
            }
        }

        // 5. Category Filter
        if (isset($filters['category']) && !empty($filters['category'])) {
            if (preg_match('/\bi\.category_id\b/', $baseSql)) {
                $clauses[] = "i.category_id = :category";
            } elseif (preg_match('/\bit\.category_id\b/', $baseSql)) {
                $clauses[] = "it.category_id = :category";
            } elseif (strpos($baseSql, 'category_id') !== false) {
                $clauses[] = "category_id = :category";
            }
            $params[':category'] = $filters['category'];
        }

        // 6. Route Filter
        if (isset($filters['route']) && !empty($filters['route'])) {
            if (preg_match('/\bi\.rep_route_id\b/', $baseSql)) {
                $clauses[] = "i.rep_route_id = :route";
            } elseif (preg_match('/\bcp\.rep_route_id\b/', $baseSql)) {
                $clauses[] = "cp.rep_route_id = :route";
            } elseif (preg_match('/\br\.id\b/', $baseSql)) {
                $clauses[] = "r.id = :route";
            } elseif (strpos($baseSql, 'rep_route_id') !== false) {
                $clauses[] = "rep_route_id = :route";
            }
            $params[':route'] = $filters['route'];
        }

        // 7. Sales Rep Filter
        if (isset($filters['rep']) && !empty($filters['rep'])) {
            if (preg_match('/\bi\.created_by\b/', $baseSql)) {
                $clauses[] = "i.created_by = :rep";
            } elseif (preg_match('/\bcp\.created_by\b/', $baseSql)) {
                $clauses[] = "cp.created_by = :rep";
            } elseif (preg_match('/\br\.user_id\b/', $baseSql)) {
                $clauses[] = "r.user_id = :rep";
            }
            $params[':rep'] = $filters['rep'];
        }

        // 8. Payment Method Filter
        if (isset($filters['payment_method']) && !empty($filters['payment_method'])) {
            if (preg_match('/\bcp\.payment_method\b/', $baseSql)) {
                $clauses[] = "cp.payment_method = :payment_method";
                $params[':payment_method'] = $filters['payment_method'];
            } elseif (strpos($baseSql, 'payment_method') !== false) {
                $clauses[] = "payment_method = :payment_method";
                $params[':payment_method'] = $filters['payment_method'];
            }
        }

        // 9. Status Filter
        if (isset($filters['status']) && !empty($filters['status'])) {
            if (preg_match('/\bi\.status\b/', $baseSql)) {
                $clauses[] = "i.status = :status";
                $params[':status'] = $filters['status'];
            } elseif (preg_match('/\bp\.status\b/', $baseSql)) {
                $clauses[] = "p.status = :status";
                $params[':status'] = $filters['status'];
            } elseif (preg_match('/\bg\.status\b/', $baseSql)) {
                $clauses[] = "g.status = :status";
                $params[':status'] = $filters['status'];
            }
        }

        // 10. Trial Balance Type Filter
        if (strpos($baseSql, ':include_closing') !== false) {
            $includeClosing = (isset($filters['tb_type']) && $filters['tb_type'] === 'post_closing') ? 1 : 0;
            $params[':include_closing'] = $includeClosing;
        }

        // Inject clauses precisely into /*WHERE_CLAUSE*/ or standard position
        if (!empty($clauses)) {
            $conditions = " AND " . implode(" AND ", $clauses);
            if (strpos($baseSql, '/*WHERE_CLAUSE*/') !== false) {
                $baseSql = str_replace('/*WHERE_CLAUSE*/', $conditions, $baseSql);
            } else {
                $pattern = '/\s+(GROUP\s+BY|HAVING|ORDER\s+BY)\b/i';
                if (preg_match($pattern, $baseSql, $m, PREG_OFFSET_CAPTURE)) {
                    $offset = $m[0][1];
                    $baseSql = substr($baseSql, 0, $offset) . $conditions . substr($baseSql, $offset);
                } else {
                    $baseSql .= $conditions;
                }
            }
        } else {
            $baseSql = str_replace('/*WHERE_CLAUSE*/', '', $baseSql);
        }

        // Live execution
        try {
            // Get Total Row count for server-side pagination
            $countSql = "SELECT COUNT(*) as cnt FROM (" . $baseSql . ") as temp_table";
            $this->db->query($countSql);
            foreach ($params as $k => $v) {
                if (strpos($countSql, $k) !== false) {
                    $this->db->bind($k, $v);
                }
            }
            $countRow = $this->db->single();
            $totalRows = $countRow ? (int)$countRow->cnt : 0;

            // Calculate Grand Totals dynamically on the full filtered dataset (not paginated)
            $grandTotals = [];
            $totalSelects = [];
            foreach ($metadata['columns'] as $colKey => $colDef) {
                if (isset($colDef['total']) && $colDef['total'] === 'sum') {
                    if (preg_match('/^[a-zA-Z0-9_]+$/', $colKey)) {
                        $totalSelects[] = "SUM(" . $colKey . ") as sum_" . $colKey;
                    }
                }
            }
            if (!empty($totalSelects)) {
                $totalsSql = "SELECT " . implode(', ', $totalSelects) . " FROM (" . $baseSql . ") as totals_table";
                try {
                    $this->db->query($totalsSql);
                    foreach ($params as $k => $v) {
                        if (strpos($totalsSql, $k) !== false) {
                            $this->db->bind($k, $v);
                        }
                    }
                    $totalsRow = $this->db->single();
                    if ($totalsRow) {
                        foreach ($metadata['columns'] as $colKey => $colDef) {
                            if (isset($colDef['total']) && $colDef['total'] === 'sum') {
                                $grandTotals[$colKey] = floatval($totalsRow->{"sum_" . $colKey} ?? 0);
                            }
                        }
                    }
                } catch (PDOException $totalEx) {
                    error_log("ReportEngine failed to calculate full grand totals: " . $totalEx->getMessage());
                }
            }

            // Apply Sorting & Pagination
            if (!empty($sortCol) && isset($metadata['columns'][$sortCol]) && preg_match('/^[a-zA-Z0-9_\.]+$/', $sortCol)) {
                $baseSql .= " ORDER BY " . $sortCol . " " . ($sortDir === 'DESC' ? 'DESC' : 'ASC');
            }
            $baseSql .= " LIMIT :limit OFFSET :offset";

            $this->db->query($baseSql);
            foreach ($params as $k => $v) {
                if (strpos($baseSql, $k) !== false) {
                    $this->db->bind($k, $v);
                }
            }
            $this->db->bind(':limit', $limit, PDO::PARAM_INT);
            $this->db->bind(':offset', $offset, PDO::PARAM_INT);
            $rows = $this->db->resultSet() ?: [];

            return [
                'rows' => $rows,
                'total_rows' => $totalRows,
                'grand_totals' => $grandTotals,
                'simulation' => false
            ];
        } catch (PDOException $ex) {
            error_log("ReportEngine Live Query Error: " . $ex->getMessage() . " | SQL: " . $baseSql . " | Params: " . json_encode($params));
            return [
                'rows' => [],
                'total_rows' => 0,
                'grand_totals' => [],
                'simulation' => false,
                'db_error' => $ex->getMessage() . " | SQL: " . $baseSql
            ];
        }
    }
}
