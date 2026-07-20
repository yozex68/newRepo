<?php use App\Helpers\Helper; ?>

<div class="bg-light py-4 border-bottom mb-5">
    <div class="container">
        <h1 class="h3 fw-bold mb-1 text-dark">Academic Subscription Services</h1>
        <p class="text-secondary small mb-0">Unlock external programmes access and higher daily download quotas.</p>
    </div>
</div>

<!-- Active Subscription Status Banner -->
<?php if ($activeSub): ?>
    <div class="alert bg-success text-white border-0 shadow-sm rounded-3 p-4 mb-5 animate-fade-in">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2 class="h5 fw-bold mb-1 text-white"><i class="fas fa-check-circle me-2"></i> Active Subscription: <?= Helper::esc($activeSub['plan_name']) ?></h2>
                <p class="small mb-0 opacity-75">
                    Plan rate: $<?= number_format($activeSub['plan_price'], 2) ?>/month. Maximum downloads quota: <?= ($activeSub['max_downloads'] > 0) ? $activeSub['max_downloads'] . ' files/day' : 'Unlimited downloads' ?>.
                </p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <span class="badge bg-dark px-3 py-2 rounded-pill small">Expires: <?= date('M d, Y', strtotime($activeSub['expires_at'])) ?></span>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="alert bg-warning text-dark border-0 shadow-sm rounded-3 p-4 mb-5 animate-fade-in">
        <h2 class="h5 fw-bold mb-1"><i class="fas fa-exclamation-triangle me-2"></i> Free / Guest Account Plan</h2>
        <p class="small mb-0 opacity-75">
            You are currently on a limited basic download quota (max 5 downloads/day) and restrained to your enrolled programme. Purchase a plan below to expand access!
        </p>
    </div>
<?php endif; ?>

<!-- Pricing Matrix Grid -->
<div class="row g-4 justify-content-center">
    <?php foreach ($plans as $p): ?>
        <?php 
        $isCurrent = ($activeSub && (int)$activeSub['subscription_plan_id'] === $p['id']);
        $isFree = ((int)$p['price'] == 0);
        
        $cardBorder = $isCurrent ? 'border-primary border-2' : '';
        $btnClass = $isCurrent ? 'btn-secondary disabled' : ($isFree ? 'btn-outline-primary' : 'btn-warning text-white');
        $btnText = $isCurrent ? 'Currently Active' : ($isFree ? 'Free Default' : 'Upgrade & Checkout');
        ?>
        <div class="col-md-4">
            <div class="card h-100 shadow-sm border-0 bg-white <?= $cardBorder ?> text-center p-4">
                <div class="card-body">
                    <h3 class="h5 fw-bold text-secondary mb-1"><?= Helper::esc($p['name']) ?></h3>
                    <div class="py-3">
                        <span class="display-5 fw-bold text-dark">$<?= number_format($p['price'], 2) ?></span>
                        <span class="text-muted small">/ <?= $p['duration_months'] ?>mo</span>
                    </div>
                    <p class="text-secondary small mb-4"><?= Helper::esc($p['description']) ?></p>
                    
                    <ul class="list-unstyled text-secondary small d-flex flex-column gap-2 mb-4 text-start">
                        <li>
                            <i class="fas fa-check text-success me-2"></i> 
                            <strong><?= ($p['max_downloads'] > 0) ? $p['max_downloads'] : 'Unlimited' ?></strong> downloads per day
                        </li>
                        <li>
                            <i class="fas fa-check text-success me-2"></i> 
                            <?= ($p['id'] == 1) ? 'Restrained boundary access' : (($p['id'] == 2) ? 'Access to 1 external major' : 'Full access to all university courses') ?>
                        </li>
                        <li>
                            <i class="fas fa-check text-success me-2"></i> Q&A Help Desk Support
                        </li>
                    </ul>
                </div>
                
                <div class="card-footer bg-transparent border-0 pt-0">
                    <form action="<?= Helper::url('/subscribe/checkout/' . $p['id']) ?>" method="POST">
                        <?= Helper::csrf_field() ?>
                        <button type="submit" class="btn w-100 py-2.5 fw-semibold <?= $btnClass ?>" <?= $isCurrent ? 'disabled' : '' ?>>
                            <?= $btnText ?>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
