<?php include __DIR__ . '/_header.php'; ?>
<table class="rpt">
    <thead>
        <tr>
            <th>Date</th><th>Reference</th><th>Account</th><th>Description</th>
            <th class="num">Debit</th><th class="num">Credit</th><th>Posted By</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $tDeb = 0; $tCred = 0;
        if (empty($data['rows'])): ?>
        <tr><td colspan="7" class="empty-msg">No journal activity in this period.</td></tr>
        <?php else: foreach ($data['rows'] as $r):
            $isVoided = ($r->status === 'Voided');
            if (!$isVoided) {
                $tDeb += $r->debit; 
                $tCred += $r->credit;
            }
        ?>
        <tr style="<?= $isVoided ? 'background: rgba(255, 59, 48, 0.05); opacity: 0.7;' : '' ?>">
            <td><?= date('M j, Y', strtotime($r->entry_date)) ?></td>
            <td>
                <?= htmlspecialchars($r->reference) ?>
                <?php if ($isVoided): ?>
                    <span style="background: rgba(255,59,48,0.12); color: #ff3b30; font-weight: 700; font-size: 10px; padding: 2px 6px; border-radius: 4px; margin-left: 4px;">VOIDED</span>
                <?php endif; ?>
            </td>
            <td><?= htmlspecialchars($r->account_code) ?> — <?= htmlspecialchars($r->account_name) ?></td>
            <td>
                <?= htmlspecialchars($r->description) ?>
                <?php if ($isVoided): ?>
                    <span style="color: #ff3b30; font-weight: 700; font-size: 11px; margin-left: 4px;">[VOIDED]</span>
                <?php endif; ?>
            </td>
            <td class="num" style="<?= $isVoided ? 'text-decoration: line-through; opacity: 0.6;' : '' ?>"><?= $r->debit > 0 ? number_format($r->debit, 2) : '-' ?></td>
            <td class="num" style="<?= $isVoided ? 'text-decoration: line-through; opacity: 0.6;' : '' ?>"><?= $r->credit > 0 ? number_format($r->credit, 2) : '-' ?></td>
            <td><?= htmlspecialchars($r->posted_by ?? '') ?></td>
        </tr>
        <?php endforeach; endif; ?>
        <?php if (!empty($data['rows'])): ?>
        <tr class="total-row">
            <td colspan="4">Totals</td>
            <td class="num"><?= number_format($tDeb, 2) ?></td>
            <td class="num"><?= number_format($tCred, 2) ?></td>
            <td></td>
        </tr>
        <?php endif; ?>
    </tbody>
</table>
<?php include __DIR__ . '/_footer.php'; ?>
