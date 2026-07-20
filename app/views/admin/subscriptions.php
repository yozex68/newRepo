<?php use App\Helpers\Helper; ?>

<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0 bg-white">
            <div class="card-header bg-white border-bottom py-3">
                <h1 class="h5 fw-bold mb-0 text-dark"><i class="fas fa-receipt text-primary me-2"></i> Subscriptions Billing Logs</h1>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($subscriptions)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Student Name</th>
                                    <th>Email</th>
                                    <th>Subscription Plan</th>
                                    <th>Amount Paid</th>
                                    <th>Status</th>
                                    <th>Billing Starts</th>
                                    <th>Billing Expires</th>
                                    <th class="pe-3">Billing Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($subscriptions as $s): ?>
                                    <?php 
                                    $statusBadge = match($s['status']) {
                                        'active' => 'bg-success text-white',
                                        'expired' => 'bg-secondary text-white',
                                        default => 'bg-danger text-white'
                                    };
                                    ?>
                                    <tr>
                                        <td class="ps-3 fw-bold text-dark"><?= Helper::esc($s['user_name']) ?></td>
                                        <td class="text-secondary"><?= Helper::esc($s['user_email']) ?></td>
                                        <td>
                                            <span class="badge badge-blue"><?= Helper::esc($s['plan_name']) ?></span>
                                        </td>
                                        <td class="fw-semibold text-dark">$<?= number_format($s['plan_price'], 2) ?></td>
                                        <td>
                                            <span class="badge <?= $statusBadge ?>"><?= ucfirst($s['status']) ?></span>
                                        </td>
                                        <td class="text-muted"><?= date('Y-m-d H:i', strtotime($s['starts_at'])) ?></td>
                                        <td class="text-muted"><?= date('Y-m-d H:i', strtotime($s['expires_at'])) ?></td>
                                        <td class="pe-3 text-muted"><?= date('Y-m-d H:i', strtotime($s['created_at'])) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center py-5 mb-0">No active or historic subscriptions found in the system billing logs.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
