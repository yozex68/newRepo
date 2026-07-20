<?php use App\Helpers\Helper; ?>

<div class="bg-light py-4 border-bottom">
    <div class="container">
        <h1 class="h3 fw-bold mb-0">System Announcements Board</h1>
        <p class="text-secondary small mb-0">Keep up to date with the latest curriculum changes and library system upgrades.</p>
    </div>
</div>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <?php if (!empty($announcements)): ?>
                <div class="d-flex flex-column gap-4">
                    <?php foreach ($announcements as $ann): ?>
                        <article class="card border-0 shadow-sm bg-white animate-fade-in">
                            <div class="card-body p-4 p-md-5">
                                <header class="mb-3 d-flex flex-wrap gap-2 justify-content-between align-items-center">
                                    <span class="badge bg-primary text-white rounded-pill px-3">
                                        <i class="fas fa-calendar-alt me-1"></i> <?= date('F d, Y \a\t H:i', strtotime($ann['created_at'])) ?>
                                    </span>
                                    <span class="text-muted small">
                                        <i class="fas fa-user-edit me-1"></i> Posted by: <strong><?= Helper::esc($ann['creator_name']) ?></strong>
                                    </span>
                                </header>
                                <h2 class="h4 fw-bold text-dark mb-3"><?= Helper::esc($ann['title']) ?></h2>
                                <p class="text-secondary mb-0" style="white-space: pre-line; line-height: 1.6; font-size: 0.95rem;">
                                    <?= Helper::esc($ann['content']) ?>
                                </p>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-5 bg-light rounded border border-dashed text-secondary">
                    <i class="fas fa-bullhorn fs-1 mb-3"></i>
                    <h3 class="h5 fw-bold">No Announcements</h3>
                    <p class="mb-0">Check back later for news, policy updates, and resources listing updates.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
