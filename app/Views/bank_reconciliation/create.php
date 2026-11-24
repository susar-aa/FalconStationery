<?php
/**
 * Bank Reconciliation Create View - Curtiss & Falcon Stationery ERP
 * SF Pro Display, CSRF Protection, Seamless Auto-Account Load
 */
$bankAccounts = $data['bankAccounts'] ?? [];
$selectedAccountId = $data['selectedAccountId'] ?? 0;
$csrfToken = $_SESSION['csrf_token'] ?? '';
?>

<style>
.erp-create-workspace {
    height: calc(100vh - 58px);
    display: flex;
    flex-direction: column;
    overflow-y: auto;
    background: #f8fafc;
    font-family: 'SF Pro Display', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    color: #1e293b;
    padding: 24px;
    box-sizing: border-box;
}

.create-card-container {
    max-width: 680px;
    margin: 0 auto;
    width: 100%;
}

.erp-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
    padding: 28px;
}

.erp-form-title-strip {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 14px;
    border-bottom: 1px solid #f1f5f9;
}
.erp-form-title-strip h2 {
    font-size: 18px;
    font-weight: 700;
    color: #0f172a;
    margin: 0;
}

.erp-form-group {
    margin-bottom: 18px;
}
.erp-form-group label {
    display: block;
    font-size: 12.5px;
    font-weight: 600;
    color: #334155;
    margin-bottom: 6px;
}
.erp-input, .erp-select {
    width: 100%;
    height: 38px;
    padding: 0 12px;
    font-size: 13.5px;
    font-family: inherit;
    color: #0f172a;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    box-sizing: border-box;
    outline: none;
    transition: all 0.15s ease;
}
.erp-input:focus, .erp-select:focus {
    border-color: #0284c7;
    box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.12);
}

.erp-grid-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}

.num-mono {
    font-family: ui-monospace, 'SF Mono', Menlo, Monaco, Consolas, monospace;
    font-variant-numeric: tabular-nums;
}

.erp-notice-box {
    padding: 12px 16px;
    border-radius: 8px;
    font-size: 13px;
    line-height: 1.45;
    margin-bottom: 18px;
    display: flex;
    align-items: flex-start;
    gap: 10px;
}
.notice-info {
    background: #f0f9ff;
    color: #0369a1;
    border: 1px solid #bae6fd;
}
.notice-warning {
    background: #fffbeb;
    color: #b45309;
    border: 1px solid #fde68a;
}

.erp-form-actions {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 12px;
    margin-top: 24px;
    padding-top: 16px;
    border-top: 1px solid #f1f5f9;
}
.btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 9px 20px;
    background: #0284c7;
    color: #ffffff;
    font-size: 13.5px;
    font-weight: 600;
    border-radius: 8px;
    border: none;
    cursor: pointer;
    box-shadow: 0 1px 2px rgba(2, 132, 199, 0.2);
    transition: all 0.15s ease;
}
.btn-primary:hover {
    background: #0369a1;
}
.btn-secondary {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    background: #ffffff;
    color: #475569;
    font-size: 13px;
    font-weight: 600;
    border-radius: 8px;
    border: 1px solid #cbd5e1;
    text-decoration: none;
    transition: all 0.15s ease;
}
.btn-secondary:hover {
    background: #f8fafc;
    color: #0f172a;
}
</style>

<div class="erp-create-workspace">
    <div class="create-card-container">
        <!-- Back navigation link -->
        <div style="margin-bottom: 14px;">
            <a href="<?= APP_URL ?>/bankreconciliation" class="btn-secondary" style="border:none; padding: 4px 8px; background: transparent; font-size: 12.5px;">
                <i class="ph ph-arrow-left"></i> Back to Reconciliations
            </a>
        </div>

        <div class="erp-card">
            <div class="erp-form-title-strip">
                <div>
                    <h2>Setup Bank Reconciliation Period</h2>
                    <div style="font-size: 12.5px; color: #64748b; margin-top: 2px;">
                        Carry forward confirmed opening balance and match physical bank statements
                    </div>
                </div>
                <div style="font-size: 24px; color: #0284c7;">
                    <i class="ph ph-bank"></i>
                </div>
            </div>

            <!-- Dynamic Balance & Status Notice -->
            <div id="noticeContainer" style="display: none;"></div>

            <form action="<?= APP_URL ?>/bankreconciliation/store" method="POST" id="createRecForm">
                <!-- CSRF Token (Mandatory Rule 3) -->
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken); ?>">

                <div class="erp-form-group">
                    <label for="accountSelect">Target Bank Account <span style="color: #dc2626;">*</span></label>
                    <select name="account_id" id="accountSelect" class="erp-select" required>
                        <option value="">-- Select Bank Account --</option>
                        <?php foreach ($bankAccounts as $acc): ?>
                            <option value="<?= $acc->id; ?>" <?= ($selectedAccountId == $acc->id) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($acc->account_code . ' - ' . $acc->account_name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="erp-grid-2">
                    <div class="erp-form-group">
                        <label for="startDateInput">Statement Start Date <span style="color: #dc2626;">*</span></label>
                        <input type="date" name="statement_date_start" id="startDateInput" class="erp-input num-mono" value="<?= date('Y-m-01'); ?>" required>
                    </div>
                    <div class="erp-form-group">
                        <label for="endDateInput">Statement End Date <span style="color: #dc2626;">*</span></label>
                        <input type="date" name="statement_date_end" id="endDateInput" class="erp-input num-mono" value="<?= date('Y-m-t'); ?>" required>
                    </div>
                </div>

                <div class="erp-grid-2">
                    <div class="erp-form-group">
                        <label for="openingBalInput">Opening Balance (LKR) <span style="color: #dc2626;">*</span></label>
                        <input type="number" step="0.01" name="statement_opening_balance" id="openingBalInput" class="erp-input num-mono" placeholder="0.00" required>
                        <div id="obSourceNote" style="font-size: 11.5px; color: #64748b; margin-top: 5px;"></div>
                    </div>
                    <div class="erp-form-group">
                        <label for="closingBalInput">Statement Closing Balance (LKR) <span style="color: #dc2626;">*</span></label>
                        <input type="number" step="0.01" name="statement_closing_balance" id="closingBalInput" class="erp-input num-mono" placeholder="0.00" required>
                        <div style="font-size: 11.5px; color: #64748b; margin-top: 5px;">Ending balance from physical bank statement</div>
                    </div>
                </div>

                <div class="erp-form-actions">
                    <a href="<?= APP_URL ?>/bankreconciliation" class="btn-secondary">Cancel</a>
                    <button type="submit" class="btn-primary">
                        <i class="ph ph-check-square-offset"></i> Initialize Reconciliation Workspace
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const accSelect = document.getElementById('accountSelect');
    const startInput = document.getElementById('startDateInput');
    const endInput = document.getElementById('endDateInput');
    const obInput = document.getElementById('openingBalInput');
    const obNote = document.getElementById('obSourceNote');
    const noticeContainer = document.getElementById('noticeContainer');

    function fetchAccountDetails(accountId) {
        if (!accountId) {
            noticeContainer.style.display = 'none';
            obNote.textContent = '';
            return;
        }

        fetch('<?= APP_URL ?>/bankreconciliation/get_account_info_ajax?account_id=' + accountId)
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    obInput.value = data.opening_balance.toFixed(2);
                    if (data.suggested_start) startInput.value = data.suggested_start;
                    if (data.suggested_end) endInput.value = data.suggested_end;

                    obNote.textContent = data.message || '';

                    if (data.has_draft && data.draft_warning) {
                        noticeContainer.className = 'erp-notice-box notice-warning';
                        noticeContainer.innerHTML = '<i class="ph ph-warning-circle" style="font-size:18px; flex-shrink:0;"></i><div><strong>Unfinalized Previous Draft:</strong> ' + data.draft_warning + '</div>';
                        noticeContainer.style.display = 'flex';
                    } else if (data.has_finalized) {
                        noticeContainer.className = 'erp-notice-box notice-info';
                        noticeContainer.innerHTML = '<i class="ph ph-check-circle" style="font-size:18px; flex-shrink:0;"></i><div><strong>Prior Month Confirmed:</strong> Automatically carried forward closing balance from ' + data.prev_rec_number + ' (Ending ' + data.prev_end_date + ').</div>';
                        noticeContainer.style.display = 'flex';
                    } else {
                        noticeContainer.className = 'erp-notice-box notice-info';
                        noticeContainer.innerHTML = '<i class="ph ph-info" style="font-size:18px; flex-shrink:0;"></i><div><strong>Initial Setup:</strong> ' + (data.message || 'Starting initial reconciliation for this bank account.') + '</div>';
                        noticeContainer.style.display = 'flex';
                    }
                }
            })
            .catch(err => {
                console.error("Account info fetch error:", err);
            });
    }

    accSelect.addEventListener('change', function() {
        fetchAccountDetails(this.value);
    });

    // Auto-fetch if account was pre-selected via query param
    if (accSelect.value) {
        fetchAccountDetails(accSelect.value);
    }
});
</script>
