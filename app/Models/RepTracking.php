<?php

class RepTracking {
    private $db;

    public function __construct() {
        $this->db = new Database();

        // Failsafe DDL: Ensure original_grand_total column exists on invoices table
        try {
            $this->db->query("SHOW COLUMNS FROM invoices LIKE 'original_grand_total'");
            if (!$this->db->single()) {
                $this->db->query("ALTER TABLE invoices ADD COLUMN original_grand_total DECIMAL(15,2) NULL AFTER grand_total");
                $this->db->execute();
                $this->db->query("UPDATE invoices SET original_grand_total = COALESCE(grand_total, ROUND(total_amount - COALESCE(CASE WHEN global_discount_type = '%' THEN (total_amount * global_discount_val / 100) ELSE global_discount_val END, 0) + COALESCE(tax_amount, 0), 2)) WHERE original_grand_total IS NULL");
                $this->db->execute();
            }
        } catch (Exception $e) {
            // Silently catch errors if table/permissions differ
        }
    }

    public function getAllRoutes() {
        $this->db->query("
            SELECT r.*, COALESCE(e.first_name, u.username) as first_name, COALESCE(e.last_name, '') as last_name,
                COALESCE(inv.bill_count, 0) as bill_count,
                COALESCE(inv.rep_total_sales, inv.total_sales, 0.00) as rep_total_sales,
                COALESCE(inv.actual_total_sales, inv.total_sales, 0.00) as actual_total_sales,
                COALESCE(inv.total_sales, 0.00) as total_sales,
                COALESCE(pc.unfinalized_count, 0) as unfinalized_count,
                rb.name as binding_name
            FROM rep_daily_routes r
            LEFT JOIN users u ON r.user_id = u.id
            LEFT JOIN employees e ON u.email = e.email
            LEFT JOIN route_bindings rb ON r.route_binding_id = rb.id
            LEFT JOIN (
                SELECT rep_route_id, 
                       COUNT(*) as bill_count,
                       SUM(COALESCE(original_grand_total, grand_total, (total_amount - COALESCE(CASE WHEN global_discount_type = '%' THEN (total_amount * global_discount_val / 100) ELSE global_discount_val END, 0) + COALESCE(tax_amount, 0)))) as rep_total_sales,
                       SUM(total_amount - COALESCE(CASE WHEN global_discount_type = '%' THEN (total_amount * global_discount_val / 100) ELSE global_discount_val END, 0) + COALESCE(tax_amount, 0)) as actual_total_sales,
                       SUM(COALESCE(original_grand_total, grand_total, (total_amount - COALESCE(CASE WHEN global_discount_type = '%' THEN (total_amount * global_discount_val / 100) ELSE global_discount_val END, 0) + COALESCE(tax_amount, 0)))) as total_sales
                FROM invoices 
                WHERE status != 'Voided' AND rep_route_id IS NOT NULL
                GROUP BY rep_route_id
            ) inv ON inv.rep_route_id = r.id
            LEFT JOIN (
                SELECT route_id, COUNT(*) as unfinalized_count
                FROM pending_collections 
                WHERE status = 'Pending' AND route_id IS NOT NULL
                GROUP BY route_id
            ) pc ON pc.route_id = r.id
            WHERE r.id NOT IN (SELECT rep_route_id FROM deliveries)
            ORDER BY r.start_time DESC
        ");
        $rawRoutes = $this->db->resultSet() ?: [];
        
        $mergedRoutes = [];
        $constituentNames = [];
        $unboundRoutes = [];
        
        foreach ($rawRoutes as $route) {
            if ($route->is_merged_route == 1) {
                $mergedRoutes[$route->route_binding_id] = $route;
            }
        }
        
        foreach ($rawRoutes as $route) {
            $isConstituent = ($route->status === 'Bound' || $route->status === 'Bound Into Route') && $route->route_binding_id;
            if ($isConstituent) {
                $constituentNames[$route->route_binding_id][] = $route->route_name;
            } elseif ($route->is_merged_route == 0) {
                $unboundRoutes[] = $route;
            }
        }
        
        $finalRoutes = [];
        foreach ($unboundRoutes as $route) {
            $route->is_bound_group = false;
            $finalRoutes[] = $route;
        }
        
        foreach ($mergedRoutes as $bindingId => $mergedRoute) {
            $mergedRoute->is_bound_group = true;
            $names = isset($constituentNames[$bindingId]) ? $constituentNames[$bindingId] : [];
            $mergedRoute->constituent_routes_info = implode(' & ', $names);
            $finalRoutes[] = $mergedRoute;
        }
        
        // Sort final list by start_time descending
        usort($finalRoutes, function($a, $b) {
            return strtotime($b->start_time) - strtotime($a->start_time);
        });
        
        return $finalRoutes;
    }

    public function resolveAllBoundRouteIds($routeId) {
        $routeIds = [intval($routeId)];
        $this->db->query("SELECT route_binding_id, bound_to_route_id FROM rep_daily_routes WHERE id = :rid LIMIT 1");
        $this->db->bind(':rid', $routeId);
        $routeRow = $this->db->single();
        if ($routeRow) {
            if ($routeRow->route_binding_id) {
                $this->db->query("SELECT id FROM rep_daily_routes WHERE route_binding_id = :bid");
                $this->db->bind(':bid', $routeRow->route_binding_id);
                $boundRoutes = $this->db->resultSet() ?: [];
                foreach ($boundRoutes as $br) {
                    $routeIds[] = intval($br->id);
                }
            }
            if ($routeRow->bound_to_route_id) {
                $routeIds[] = intval($routeRow->bound_to_route_id);
            }
        }
        return array_unique(array_filter(array_map('intval', $routeIds)));
    }

    public function getRouteBills($routeId) {
        $routeIds = $this->resolveAllBoundRouteIds($routeId);
        $placeholders = [];
        foreach ($routeIds as $index => $id) {
            $placeholders[] = ":rid_" . $index;
        }
        $placeholdersStr = implode(',', $placeholders);

        $this->db->query("
            SELECT i.*, c.name as customer_name,
            (total_amount - COALESCE(CASE WHEN global_discount_type = '%' THEN (total_amount * global_discount_val / 100) ELSE global_discount_val END, 0) + COALESCE(tax_amount, 0)) as true_grand_total
            FROM invoices i
            JOIN customers c ON i.customer_id = c.id
            WHERE i.rep_route_id IN ($placeholdersStr)
            ORDER BY i.created_at ASC
        ");
        foreach ($routeIds as $index => $id) {
            $this->db->bind(":rid_" . $index, intval($id));
        }
        return $this->db->resultSet();
    }

    // UPDATED: Now fetches live high-level statistics for the printable report header block
    public function getRouteById($routeId) {
        $routeIds = $this->resolveAllBoundRouteIds($routeId);
        $placeholders = [];
        foreach ($routeIds as $index => $id) {
            $placeholders[] = ":rid_" . $index;
        }
        $placeholdersStr = implode(',', $placeholders);

        $this->db->query("
            SELECT r.*, COALESCE(e.first_name, u.username) as first_name, COALESCE(e.last_name, '') as last_name,
                (SELECT COUNT(*) FROM invoices WHERE rep_route_id IN ($placeholdersStr) AND status NOT IN ('Voided', 'Cancelled') AND (delivery_status IS NULL OR delivery_status != 'Cancelled')) as bill_count,
                (SELECT COALESCE(SUM(COALESCE(original_grand_total, grand_total, (total_amount - COALESCE(CASE WHEN global_discount_type = '%' THEN (total_amount * global_discount_val / 100) ELSE global_discount_val END, 0) + COALESCE(tax_amount, 0)))), 0) 
                 FROM invoices WHERE rep_route_id IN ($placeholdersStr) AND status NOT IN ('Voided', 'Cancelled') AND (delivery_status IS NULL OR delivery_status != 'Cancelled')) as rep_total_sales,
                (SELECT COALESCE(SUM(total_amount - COALESCE(CASE WHEN global_discount_type = '%' THEN (total_amount * global_discount_val / 100) ELSE global_discount_val END, 0) + COALESCE(tax_amount, 0)), 0) 
                 FROM invoices WHERE rep_route_id IN ($placeholdersStr) AND status NOT IN ('Voided', 'Cancelled') AND (delivery_status IS NULL OR delivery_status != 'Cancelled')) as actual_total_sales,
                (SELECT COALESCE(SUM(COALESCE(original_grand_total, grand_total, (total_amount - COALESCE(CASE WHEN global_discount_type = '%' THEN (total_amount * global_discount_val / 100) ELSE global_discount_val END, 0) + COALESCE(tax_amount, 0)))), 0) 
                 FROM invoices WHERE rep_route_id IN ($placeholdersStr) AND status NOT IN ('Voided', 'Cancelled') AND (delivery_status IS NULL OR delivery_status != 'Cancelled')) as total_sales
            FROM rep_daily_routes r
            LEFT JOIN users u ON r.user_id = u.id
            LEFT JOIN employees e ON u.email = e.email
            WHERE r.id = :rid
        ");
        $this->db->bind(':rid', $routeId);
        foreach ($routeIds as $index => $id) {
            $this->db->bind(":rid_" . $index, intval($id));
        }
        return $this->db->single();
    }

    /**
     * Reconcile route invoice totals against posted GL journal entries
     */
    public function getRouteGLReconciliation(int $routeId): array {
        $routeIds = $this->resolveAllBoundRouteIds($routeId);
        $placeholders = [];
        foreach ($routeIds as $index => $id) {
            $placeholders[] = ":rid_" . $index;
        }
        $placeholdersStr = implode(',', $placeholders);

        // 1. Invoice grand_total sum
        $this->db->query("
            SELECT 
                COUNT(id) as total_invoices,
                COALESCE(SUM(total_amount - COALESCE(CASE WHEN global_discount_type = '%' THEN (total_amount * global_discount_val / 100) ELSE global_discount_val END, 0) + COALESCE(tax_amount, 0)), 0.0) as invoice_sales_total
            FROM invoices 
            WHERE rep_route_id IN ($placeholdersStr) AND status NOT IN ('Voided', 'Cancelled') AND (delivery_status IS NULL OR delivery_status != 'Cancelled')
        ");
        foreach ($routeIds as $index => $id) {
            $this->db->bind(":rid_" . $index, intval($id));
        }
        $invRow = $this->db->single();
        $invoiceSalesTotal = floatval($invRow->invoice_sales_total ?? 0.0);
        $invoiceCount = intval($invRow->total_invoices ?? 0);

        // 2. GL Accounts Receivable (Debit sum on AR account 1200) for route invoices
        $this->db->query("
            SELECT COALESCE(SUM(inv_ar.ar_debit), 0.0) as gl_ar_debit
            FROM (
                SELECT i.id, MAX(t.debit) as ar_debit
                FROM invoices i
                JOIN journal_entries je ON i.journal_entry_id = je.id
                JOIN transactions t ON t.journal_entry_id = je.id
                JOIN chart_of_accounts coa ON t.account_id = coa.id
                WHERE i.rep_route_id IN ($placeholdersStr) 
                  AND i.status NOT IN ('Voided', 'Cancelled') AND (i.delivery_status IS NULL OR i.delivery_status != 'Cancelled')
                  AND coa.account_code = '1200'
                GROUP BY i.id
            ) inv_ar
        ");
        foreach ($routeIds as $index => $id) {
            $this->db->bind(":rid_" . $index, intval($id));
        }
        $arRow = $this->db->single();
        $glArTotal = floatval($arRow->gl_ar_debit ?? 0.0);

        // 3. GL Revenue (Credit sum on Sales account 4000 - Debit sum on Discounts 4050)
        $this->db->query("
            SELECT 
                COALESCE(SUM(inv_gl.gl_gross_sales), 0.0) as gl_gross_sales,
                COALESCE(SUM(inv_gl.gl_discounts), 0.0) as gl_discounts,
                COALESCE(SUM(inv_gl.gl_tax), 0.0) as gl_tax
            FROM (
                SELECT 
                    i.id,
                    MAX(CASE WHEN coa.account_code = '4000' THEN t.credit ELSE 0 END) as gl_gross_sales,
                    MAX(CASE WHEN coa.account_code = '4050' THEN t.debit ELSE 0 END) as gl_discounts,
                    MAX(CASE WHEN coa.account_code = '2110' THEN t.credit ELSE 0 END) as gl_tax
                FROM invoices i
                JOIN journal_entries je ON i.journal_entry_id = je.id
                JOIN transactions t ON t.journal_entry_id = je.id
                JOIN chart_of_accounts coa ON t.account_id = coa.id
                WHERE i.rep_route_id IN ($placeholdersStr) 
                  AND i.status NOT IN ('Voided', 'Cancelled') AND (i.delivery_status IS NULL OR i.delivery_status != 'Cancelled')
                GROUP BY i.id
            ) inv_gl
        ");
        foreach ($routeIds as $index => $id) {
            $this->db->bind(":rid_" . $index, intval($id));
        }
        $revRow = $this->db->single();
        $glGrossSales = floatval($revRow->gl_gross_sales ?? 0.0);
        $glDiscounts = floatval($revRow->gl_discounts ?? 0.0);
        $glTax = floatval($revRow->gl_tax ?? 0.0);
        $glNetSales = $glGrossSales - $glDiscounts + $glTax;

        $variance = round($invoiceSalesTotal - $glArTotal, 2);

        return [
            'route_id' => $routeId,
            'invoice_count' => $invoiceCount,
            'invoice_sales_total' => $invoiceSalesTotal,
            'gl_ar_total' => $glArTotal,
            'gl_gross_sales' => $glGrossSales,
            'gl_discounts' => $glDiscounts,
            'gl_tax' => $glTax,
            'gl_net_sales' => $glNetSales,
            'variance' => $variance,
            'is_reconciled' => abs($variance) < 0.01
        ];
    }

    public function getRouteLoadingItems($routeId) {
        $routeIds = $this->resolveAllBoundRouteIds($routeId);
        $placeholders = [];
        foreach ($routeIds as $index => $id) {
            $placeholders[] = ":rid_" . $index;
        }
        $placeholdersStr = implode(',', $placeholders);

        $this->db->query("
            SELECT ii.item_id,
                   ii.variation_option_id,
                   MAX(ii.description) as item_name,
                   SUM(ii.quantity) as total_qty,
                   AVG(ii.unit_price) as unit_price,
                   COALESCE(ic.name, 'Uncategorized') as category_name
            FROM invoice_items ii
            JOIN invoices i ON ii.invoice_id = i.id
            LEFT JOIN items it ON ii.item_id = it.id
            LEFT JOIN item_categories ic ON it.category_id = ic.id
            WHERE i.rep_route_id IN ($placeholdersStr) AND i.status != 'Voided'
            GROUP BY ii.item_id, COALESCE(ii.variation_option_id, 0), COALESCE(ic.id, 0), COALESCE(ic.name, 'Uncategorized')
            ORDER BY category_name ASC, item_name ASC
        ");
        foreach ($routeIds as $index => $id) {
            $this->db->bind(":rid_" . $index, intval($id));
        }
        return $this->db->resultSet();
    }

    public function getRouteFinalLoadingItems($routeId) {
        $routeIds = $this->resolveAllBoundRouteIds($routeId);
        $placeholders = [];
        foreach ($routeIds as $index => $id) {
            $placeholders[] = ":rid_" . $index;
        }
        $placeholdersStr = implode(',', $placeholders);

        $this->db->query("SELECT id FROM deliveries WHERE rep_route_id IN ($placeholdersStr) OR secondary_rep_route_id IN ($placeholdersStr) ORDER BY id DESC LIMIT 1");
        foreach ($routeIds as $index => $id) {
            $this->db->bind(":rid_" . $index, intval($id));
        }
        $latestDels = $this->db->resultSet();
        if (empty($latestDels)) {
            return [];
        }
        
        $deliveryIds = [];
        $delPlaceholders = [];
        foreach ($latestDels as $idx => $del) {
            $deliveryIds[] = intval($del->id);
            $delPlaceholders[] = ":del_id_" . $idx;
        }
        $delPlaceholdersStr = implode(',', $delPlaceholders);

        $this->db->query("
            SELECT dpi.item_id,
                   dpi.variation_option_id,
                   dpi.item_name,
                   SUM(dpi.required_qty) as required_qty,
                   SUM(dpi.loaded_qty) as pre_loaded_qty,
                   SUM(COALESCE(dpi.final_loaded_qty, dpi.required_qty)) as final_loaded_qty,
                   SUM(dpi.variance) as variance,
                   MAX(dpi.is_verified) as is_verified,
                   COALESCE(ic.name, 'Uncategorized') as category_name,
                   COALESCE(
                       (SELECT AVG(ii.unit_price) 
                        FROM invoice_items ii 
                        JOIN invoices i ON ii.invoice_id = i.id 
                        WHERE i.rep_route_id IN ($placeholdersStr) AND ii.item_id = dpi.item_id AND i.status != 'Voided'),
                       (SELECT price FROM items WHERE id = dpi.item_id),
                       0
                   ) as unit_price
            FROM delivery_picking_items dpi
            LEFT JOIN items it ON dpi.item_id = it.id
            LEFT JOIN item_categories ic ON it.category_id = ic.id
            WHERE dpi.delivery_id IN ($delPlaceholdersStr)
            GROUP BY dpi.item_id, COALESCE(dpi.variation_option_id, 0), dpi.item_name, COALESCE(ic.name, 'Uncategorized')
            HAVING (SUM(COALESCE(dpi.final_loaded_qty, dpi.required_qty)) > 0 OR SUM(dpi.required_qty) > 0)
            ORDER BY category_name ASC, dpi.item_name ASC
        ");
        foreach ($deliveryIds as $idx => $dId) {
            $this->db->bind(":del_id_" . $idx, $dId);
        }
        foreach ($routeIds as $index => $id) {
            $this->db->bind(":rid_" . $index, intval($id));
        }
        return $this->db->resultSet() ?: [];
    }

    /**
     * Validate whether coordinates are realistic and non-fallback
     */
    public function isValidRouteCoordinate($lat, $lng) {
        if ($lat === null || $lng === null) return false;
        $lat = (float) $lat;
        $lng = (float) $lng;
        if ($lat == 0.0 && $lng == 0.0) return false;
        // Check Katunayake dummy default fallback (7.1824, 79.8801)
        if (abs($lat - 7.1824) < 0.001 && abs($lng - 79.8801) < 0.001) return false;
        // Sri Lanka realistic bounding box check (lat 5.5 to 10.5, lng 79.0 to 82.5)
        if ($lat < 5.5 || $lat > 10.5 || $lng < 79.0 || $lng > 82.5) return false;
        return true;
    }

    /**
     * Chronological GPS path: day start → each invoice → day end.
     */
    public function getRoutePath($routeId) {
        $routeIds = [$routeId];
        $this->db->query("SELECT route_binding_id FROM rep_daily_routes WHERE id = :rid LIMIT 1");
        $this->db->bind(':rid', $routeId);
        $routeRow = $this->db->single();
        if ($routeRow && $routeRow->route_binding_id) {
            $this->db->query("SELECT id FROM rep_daily_routes WHERE route_binding_id = :bid");
            $this->db->bind(':bid', $routeRow->route_binding_id);
            $boundRoutes = $this->db->resultSet();
            foreach ($boundRoutes as $br) {
                $routeIds[] = intval($br->id);
            }
        }
        $routeIds = array_unique($routeIds);
        
        $waypoints = [];
        $seq = 1;
        $primaryRoute = null;

        foreach ($routeIds as $rid) {
            $route = $this->getRouteById($rid);
            if (!$route) {
                continue;
            }
            if ($rid == $routeId) {
                $primaryRoute = $route;
            }

            if ($this->isValidRouteCoordinate($route->start_lat, $route->start_lng)) {
                $waypoints[] = [
                    'type' => 'start',
                    'lat' => (float) $route->start_lat,
                    'lng' => (float) $route->start_lng,
                    'time' => $route->start_time,
                    'label' => 'Start (' . htmlspecialchars($route->route_name) . ')',
                    'detail' => $route->route_name,
                ];
            }

            $this->db->query("
                SELECT i.id, i.invoice_number, i.created_at, i.latitude, i.longitude, c.name as customer_name,
                c.latitude as cust_lat, c.longitude as cust_lng,
                (total_amount - COALESCE(CASE WHEN global_discount_type = '%' THEN (total_amount * global_discount_val / 100) ELSE global_discount_val END, 0) + COALESCE(tax_amount, 0)) as true_grand_total
                FROM invoices i
                JOIN customers c ON i.customer_id = c.id
                WHERE i.rep_route_id = :rid AND i.status != 'Voided'
                ORDER BY i.created_at ASC
            ");
            $this->db->bind(':rid', $rid);
            $bills = $this->db->resultSet() ?: [];

            foreach ($bills as $bill) {
                $lat = null;
                $lng = null;
                if ($this->isValidRouteCoordinate($bill->latitude, $bill->longitude)) {
                    $lat = (float) $bill->latitude;
                    $lng = (float) $bill->longitude;
                } elseif ($this->isValidRouteCoordinate($bill->cust_lat, $bill->cust_lng)) {
                    $lat = (float) $bill->cust_lat;
                    $lng = (float) $bill->cust_lng;
                }

                if ($lat !== null && $lng !== null) {
                    $waypoints[] = [
                        'type' => 'invoice',
                        'id' => (int) $bill->id,
                        'lat' => $lat,
                        'lng' => $lng,
                        'time' => $bill->created_at,
                        'label' => $bill->customer_name,
                        'customer_name' => $bill->customer_name,
                        'invoice_number' => $bill->invoice_number,
                        'detail' => 'Invoice #' . $bill->invoice_number,
                        'amount' => (float) $bill->true_grand_total,
                        'sequence' => $seq++,
                    ];
                }
            }

            $this->db->query("
                SELECT uv.id, uv.reason, uv.custom_reason, uv.visit_time, uv.latitude, uv.longitude, c.name as customer_name,
                c.latitude as cust_lat, c.longitude as cust_lng
                FROM unproductive_visits uv
                JOIN customers c ON uv.customer_id = c.id
                WHERE uv.rep_route_id = :rid
                ORDER BY uv.visit_time ASC
            ");
            $this->db->bind(':rid', $rid);
            $unprodVisits = $this->db->resultSet() ?: [];

            foreach ($unprodVisits as $uv) {
                $lat = null;
                $lng = null;
                if ($this->isValidRouteCoordinate($uv->latitude, $uv->longitude)) {
                    $lat = (float) $uv->latitude;
                    $lng = (float) $uv->longitude;
                } elseif ($this->isValidRouteCoordinate($uv->cust_lat, $uv->cust_lng)) {
                    $lat = (float) $uv->cust_lat;
                    $lng = (float) $uv->cust_lng;
                }

                if ($lat !== null && $lng !== null) {
                    $reasonStr = $uv->reason;
                    if ($reasonStr === 'Other' && !empty($uv->custom_reason)) {
                        $reasonStr .= ' (' . $uv->custom_reason . ')';
                    }
                    $waypoints[] = [
                        'type' => 'unproductive',
                        'id' => (int) $uv->id,
                        'lat' => $lat,
                        'lng' => $lng,
                        'time' => $uv->visit_time,
                        'label' => $uv->customer_name,
                        'customer_name' => $uv->customer_name,
                        'detail' => 'Reason: ' . $reasonStr,
                        'reason' => $reasonStr,
                        'custom_reason' => $uv->custom_reason
                    ];
                }
            }

            if ($this->isValidRouteCoordinate($route->end_lat, $route->end_lng)) {
                $waypoints[] = [
                    'type' => 'end',
                    'lat' => (float) $route->end_lat,
                    'lng' => (float) $route->end_lng,
                    'time' => $route->end_time,
                    'label' => 'End (' . htmlspecialchars($route->route_name) . ')',
                    'detail' => $route->status === 'Completed' ? 'Route completed' : 'Last recorded position',
                ];
            }
        }

        if (empty($primaryRoute)) {
            return null;
        }

        // Sort waypoints: Day Start first, Day End last, middle activity chronologically ascending
        $parseTime = function($item) use ($primaryRoute) {
            $t = $item['time'] ?? null;
            if (empty($t) || $t === 'N/A') {
                return 0;
            }
            if (preg_match('/^\d{2}:\d{2}(:\d{2})?$/', trim($t))) {
                $routeDate = (!empty($primaryRoute->start_time) && strlen($primaryRoute->start_time) >= 10) ? substr($primaryRoute->start_time, 0, 10) : date('Y-m-d');
                $t = $routeDate . ' ' . trim($t);
            }
            $ts = strtotime($t);
            return ($ts !== false && $ts > 0) ? $ts : 0;
        };

        usort($waypoints, function($a, $b) use ($parseTime) {
            if ($a['type'] === 'start' && $b['type'] !== 'start') return -1;
            if ($b['type'] === 'start' && $a['type'] !== 'start') return 1;
            if ($a['type'] === 'end' && $b['type'] !== 'end') return 1;
            if ($b['type'] === 'end' && $a['type'] !== 'end') return -1;

            $timeA = $parseTime($a);
            $timeB = $parseTime($b);
            if ($timeA === $timeB) {
                return ($a['id'] ?? 0) <=> ($b['id'] ?? 0);
            }
            return $timeA <=> $timeB;
        });

        // Re-assign sequences chronologically
        $seq = 1;
        foreach ($waypoints as &$wp) {
            if ($wp['type'] === 'invoice') {
                $wp['sequence'] = $seq++;
            }
        }

        return [
            'route_id' => (int) $routeId,
            'route_name' => $primaryRoute->route_name,
            'rep_name' => trim($primaryRoute->first_name . ' ' . $primaryRoute->last_name),
            'status' => $primaryRoute->status,
            'start_time' => $primaryRoute->start_time,
            'end_time' => $primaryRoute->end_time,
            'waypoints' => $waypoints,
            'point_count' => count($waypoints),
        ];
    }

    public function getRouteCollections($routeId) {
        $routeIds = [intval($routeId)];
        $this->db->query("SELECT route_binding_id FROM rep_daily_routes WHERE id = :rid LIMIT 1");
        $this->db->bind(':rid', $routeId);
        $routeRow = $this->db->single();
        if ($routeRow && $routeRow->route_binding_id) {
            $this->db->query("SELECT id FROM rep_daily_routes WHERE route_binding_id = :bid");
            $this->db->bind(':bid', $routeRow->route_binding_id);
            $boundRoutes = $this->db->resultSet();
            foreach ($boundRoutes as $br) {
                $routeIds[] = intval($br->id);
            }
        }
        $routeIds = array_unique($routeIds);
        
        $placeholders = [];
        foreach ($routeIds as $index => $id) {
            $placeholders[] = ":rid_" . $index;
        }
        $placeholdersStr = implode(',', $placeholders);

        $this->db->query("
            SELECT pc.id, pc.customer_id, pc.route_id as rep_route_id, pc.payment_method, pc.amount, 
                   pc.bank_name, pc.cheque_number, pc.cheque_date, pc.created_at, pc.status,
                   c.name as customer_name, pc.finalized_by, pc.notes,
                   pc.is_verified, pc.is_flagged, pc.adjusted_amount, pc.verification_notes,
                   pc.debit_account_id, pc.credit_account_id,
                   pc.journal_entry_id,
                   DATE(pc.created_at) as payment_date,
                   COALESCE(pc.cheque_number, pc.bank_name, '') as reference
            FROM pending_collections pc
            JOIN customers c ON pc.customer_id = c.id
            WHERE pc.route_id IN ($placeholdersStr)
            ORDER BY pc.created_at ASC
        ");
        foreach ($routeIds as $index => $id) {
            $this->db->bind(":rid_" . $index, intval($id));
        }
        return $this->db->resultSet() ?: [];
    }

    public function finalizePayments($paymentIds, $userId, $bankAllocations = [], $customDebitAccounts = [], $customCreditAccounts = []) {
        if (empty($paymentIds)) return true;

        $inTransaction = $this->db->inTransaction();
        if (!$inTransaction) {
            $this->db->beginTransaction();
        }

        try {
            // Resolve necessary account IDs based on codes
            $this->db->query("SELECT id, account_code FROM chart_of_accounts WHERE account_code IN ('1000', '1010', '1600', '1605', '1200')");
            $accounts = $this->db->resultSet();
            $accMap = [];
            foreach ($accounts as $a) {
                $accMap[$a->account_code] = $a->id;
            }

            $cashAcc = $accMap['1000'] ?? null;
            $chequeAcc = $accMap['1010'] ?? null;
            $tempBankAcc = $accMap['1605'] ?? $accMap['1600'] ?? null;
            $arAcc = $accMap['1200'] ?? null;

            if (!$arAcc) {
                throw new Exception("Accounts Receivable account (1200) not found in Chart of Accounts.");
            }

            foreach ($paymentIds as $pid) {
                // ACCT-1 FIX: Use SELECT ... FOR UPDATE to acquire a row-level lock
                // preventing race conditions if both api_finalize_collections and 
                // finalizeDelivery are called concurrently for the same pending_collection.
                $this->db->query("SELECT * FROM pending_collections WHERE id = :id AND status = 'Pending' FOR UPDATE");
                $this->db->bind(':id', $pid);
                $payment = $this->db->single();
                if (!$payment) continue;

                // Use adjusted_amount if set during verification, otherwise use original amount
                $amount = floatval($payment->adjusted_amount !== null ? $payment->adjusted_amount : $payment->amount);
                if ($amount <= 0) continue;
                $method = $payment->payment_method;

                // Map payment method to asset account
                $assetAccId = null;
                if ($method === 'Cash') {
                    $assetAccId = $cashAcc;
                } elseif ($method === 'Bank Transfer') {
                    // Read granular target bank allocation for this specific payment
                    $selectedBankAccId = isset($bankAllocations[$pid]) ? $bankAllocations[$pid] : null;
                    $assetAccId = !empty($selectedBankAccId) ? $selectedBankAccId : $tempBankAcc;
                } elseif ($method === 'Cheque') {
                    $assetAccId = $chequeAcc;
                }

                // Custom Debit Account override if specified
                if (isset($customDebitAccounts[$pid]) && !empty($customDebitAccounts[$pid])) {
                    $assetAccId = intval($customDebitAccounts[$pid]);
                } elseif (!empty($payment->debit_account_id)) {
                    $assetAccId = intval($payment->debit_account_id);
                }

                // Custom Credit Account override if specified
                $arAccId = $arAcc;
                if (isset($customCreditAccounts[$pid]) && !empty($customCreditAccounts[$pid])) {
                    $arAccId = intval($customCreditAccounts[$pid]);
                } elseif (!empty($payment->credit_account_id)) {
                    $arAccId = intval($payment->credit_account_id);
                }

                // Helper function to resolve ID or account_code to true chart_of_accounts.id
                $resolveAcc = function($val) {
                    if (empty($val)) return 0;
                    $this->db->query("SELECT id FROM chart_of_accounts WHERE id = :v1 OR account_code = :v2 LIMIT 1");
                    $this->db->bind(':v1', intval($val));
                    $this->db->bind(':v2', strval($val));
                    $row = $this->db->single();
                    return $row ? intval($row->id) : 0;
                };

                $resolvedAssetAccId = $resolveAcc($assetAccId);
                $resolvedArAccId = $resolveAcc($arAccId);

                // Hard validation: Ensure valid Debit and Credit GL accounts are resolved
                if (empty($resolvedAssetAccId) || $resolvedAssetAccId <= 0) {
                    throw new Exception("Collection Finalisation Failed for Payment #{$pid} ({$method}): No valid Debit/Asset GL account (Cash/Bank/Cheque) mapped.");
                }

                if (empty($resolvedArAccId) || $resolvedArAccId <= 0) {
                    throw new Exception("Collection Finalisation Failed for Payment #{$pid} ({$method}): No valid Credit GL account (Accounts Receivable) mapped.");
                }

                $assetAccId = $resolvedAssetAccId;
                $arAccId = $resolvedArAccId;

                // Verify accounts exist in Chart of Accounts
                $this->db->query("SELECT id FROM chart_of_accounts WHERE id IN (:aid1, :aid2)");
                $this->db->bind(':aid1', $assetAccId);
                $this->db->bind(':aid2', $arAccId);
                $accCheckRows = $this->db->resultSet() ?: [];
                $validAccCount = count($accCheckRows);
                $expectedAccCount = ($assetAccId == $arAccId) ? 1 : 2;
                if ($validAccCount < $expectedAccCount) {
                    throw new Exception("Collection Finalisation Failed for Payment #{$pid}: Mapped GL accounts (Debit: {$assetAccId}, Credit: {$arAccId}) do not exist in Chart of Accounts.");
                }

                // Create Journal Entry
                $this->db->query("INSERT INTO journal_entries (entry_date, reference, description, created_by, status) 
                                  VALUES (CURDATE(), :ref, :desc, :uid, 'Posted')");
                $refStr = "FINAL-PMT-" . $payment->id;
                $descStr = "Finalized Route Collection (" . $method . ") for Customer ID: " . $payment->customer_id;
                $this->db->bind(':ref', $refStr);
                $this->db->bind(':desc', $descStr);
                $this->db->bind(':uid', $userId);
                $this->db->execute();
                $jid = $this->db->lastInsertId();

                // Transaction 1: Debit Asset Account (Cash / Bank / Cheque)
                $this->db->query("INSERT INTO transactions (journal_entry_id, account_id, debit, credit) VALUES (:jid, :aid, :amount, 0)");
                $this->db->bind(':jid', $jid);
                $this->db->bind(':aid', $assetAccId);
                $this->db->bind(':amount', $amount);
                $this->db->execute();

                // ACCT-2 FIX: Use account-type-aware balance update
                $this->db->updateAccountBalance($assetAccId, $amount, 0);

                // Transaction 2: Credit Accounts Receivable (or custom credit account)
                $this->db->query("INSERT INTO transactions (journal_entry_id, account_id, debit, credit) VALUES (:jid, :aid, 0, :amount)");
                $this->db->bind(':jid', $jid);
                $this->db->bind(':aid', $arAccId);
                $this->db->bind(':amount', $amount);
                $this->db->execute();

                // ACCT-2 FIX: Use account-type-aware balance update
                $this->db->updateAccountBalance($arAccId, 0, $amount);

                // Insert into customer_payments officially to credit customer subledger
                $this->db->query("INSERT INTO customer_payments (customer_id, amount, unallocated_amount, payment_date, payment_method, reference, journal_entry_id, rep_route_id, created_by, status) 
                                  VALUES (:cid, :amt, :uamt, CURDATE(), :method, :ref, :jid, :rid, :uid, 'Active')");
                $this->db->bind(':cid', $payment->customer_id);
                $this->db->bind(':amt', $amount);
                $this->db->bind(':uamt', $amount);
                $this->db->bind(':method', $method);
                
                // Build the reference string
                $refText = '';
                if ($method === 'Cheque') {
                    $refText = "Cheque #: " . $payment->cheque_number . " (" . $payment->bank_name . ")";
                } elseif ($method === 'Bank Transfer') {
                    $refText = "Transfer (" . $payment->bank_name . ")";
                } else {
                    $refText = "Cash Collection";
                }
                $this->db->bind(':ref', $refText);
                $this->db->bind(':jid', $jid);
                $this->db->bind(':rid', $payment->route_id);
                $this->db->bind(':uid', $userId);
                $this->db->execute();

                // If it is a cheque, register it in the cheques table as well
                if ($method === 'Cheque') {
                    $this->db->query("INSERT INTO cheques (customer_id, bank_name, cheque_number, amount, banking_date, status, rep_route_id, created_by) 
                                      VALUES (:cid, :bn, :cn, :amt, :bdate, 'Pending', :rid, :uid)");
                    $this->db->bind(':cid', $payment->customer_id);
                    $this->db->bind(':bn', $payment->bank_name);
                    $this->db->bind(':cn', $payment->cheque_number);
                    $this->db->bind(':amt', $amount);
                    $this->db->bind(':bdate', $payment->cheque_date ?: date('Y-m-d'));
                    $this->db->bind(':rid', $payment->route_id);
                    $this->db->bind(':uid', $userId);
                    $this->db->execute();
                }

                // Settle customer invoices with newly finalized payment credit via FIFO
                require_once __DIR__ . '/Payment.php';
                $paymentModel = new Payment();
                $paymentModel->settleCustomerInvoicesWithCreditNonTransactional($payment->customer_id, $userId);

                // Link pending collection to generated journal entry and mark as Finalized
                $this->db->query("UPDATE pending_collections SET status = 'Finalized', journal_entry_id = :jid, finalized_by = :uid, finalized_at = NOW() WHERE id = :pid");
                $this->db->bind(':jid', $jid);
                $this->db->bind(':uid', $userId);
                $this->db->bind(':pid', $pid);
                $this->db->execute();
            }

            if (!$inTransaction) {
                $this->db->commit();
            }
            return true;
        } catch (Exception $e) {
            if (!$inTransaction) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    public function getRouteCustomers($routeId) {
        $sql = "
            SELECT c.id, c.name, c.phone, c.address, c.credit_limit,
                   (c.opening_balance + COALESCE(inv.total_billed, 0) - COALESCE(pmt.total_paid, 0) - COALESCE(cn.total_credited, 0)) AS outstanding
            FROM customers c
            LEFT JOIN (
                SELECT customer_id, 
                       SUM(total_amount - COALESCE(CASE WHEN global_discount_type = '%' THEN (total_amount * global_discount_val / 100) ELSE global_discount_val END, 0) + COALESCE(tax_amount, 0)) as total_billed
                FROM invoices 
                WHERE status != 'Voided'
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
            ORDER BY c.name ASC
        ";
        $this->db->query($sql);
        return $this->db->resultSet() ?: [];
    }

    public function getRouteMarketReturns($routeId) {
        $routeIds = $this->resolveAllBoundRouteIds($routeId);
        $placeholders = [];
        foreach ($routeIds as $index => $id) {
            $placeholders[] = ":rid_" . $index;
        }
        $placeholdersStr = implode(',', $placeholders);

        $sql = "
            SELECT cn.*, c.name as customer_name
            FROM credit_notes cn
            JOIN customers c ON cn.customer_id = c.id
            WHERE cn.rep_route_id IN ($placeholdersStr) AND cn.is_market_return = 1
            ORDER BY cn.created_at DESC
        ";
        $this->db->query($sql);
        foreach ($routeIds as $index => $id) {
            $this->db->bind(":rid_" . $index, intval($id));
        }
        return $this->db->resultSet() ?: [];
    }
}