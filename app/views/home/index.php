<?php use App\Helpers\Helper; ?>

<div class="bg-light py-5 border-bottom">
    <div class="container py-5 text-center">
        <h1 class="display-4 fw-bold text-dark mb-3">SmartHUB Digital Academic Library</h1>
        <p class="lead text-secondary mb-4 mx-auto" style="max-width: 700px;">
            A centralized digital material repository structured around your university curriculum. Access high-quality lecture notes, textbooks, past exam papers, assignments, and practical files tailored specifically to your degree.
        </p>
        
        <?php if ($session->get('user_id')): ?>
            <a href="<?= Helper::url('/dashboard') ?>" class="btn btn-primary btn-lg px-4 fs-6 fw-semibold shadow-sm">
                <i class="fas fa-desktop me-2"></i> Go to Dashboard
            </a>
        <?php else: ?>
            <div class="d-flex justify-content-center gap-3">
                <a href="<?= Helper::url('/login') ?>" class="btn btn-primary btn-lg px-4 fs-6 fw-semibold shadow-sm">
                    <i class="fas fa-sign-in-alt me-2"></i> Sign In
                </a>
                <a href="<?= Helper::url('/register') ?>" class="btn btn-outline-dark btn-lg px-4 fs-6 fw-semibold shadow-sm">
                    <i class="fas fa-user-plus me-2"></i> Create Account
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="container py-5">
    <div class="row g-4 justify-content-center">
        <div class="col-md-4 text-center">
            <div class="p-4 border rounded-3 bg-white h-100 shadow-sm">
                <i class="fas fa-university text-primary fs-1 mb-3"></i>
                <h3 class="h5 fw-bold">Curriculum Aligned</h3>
                <p class="text-secondary small mb-0">Organized structurally from Faculty down to specific Semester courses to keep materials accessible and organized.</p>
            </div>
        </div>
        <div class="col-md-4 text-center">
            <div class="p-4 border rounded-3 bg-white h-100 shadow-sm">
                <i class="fas fa-key text-success fs-1 mb-3"></i>
                <h3 class="h5 fw-bold">Academic Boundaries</h3>
                <p class="text-secondary small mb-0">Students enjoy unrestricted access to their enrolled programmes, keeping resources relevant to their specific majors.</p>
            </div>
        </div>
        <div class="col-md-4 text-center">
            <div class="p-4 border rounded-3 bg-white h-100 shadow-sm">
                <i class="fas fa-shield-alt text-warning fs-1 mb-3"></i>
                <h3 class="h5 fw-bold">Secure Access</h3>
                <p class="text-secondary small mb-0">Powered by modern password hashing, encrypted phone fields, CSRF filters, and parameterized SQL prevention.</p>
            </div>
        </div>
    </div>

    <!-- Recent System Announcements -->
    <div class="mt-5">
        <div class="d-flex align-items-center justify-content-between mb-4 border-bottom pb-2">
            <h2 class="h4 fw-bold mb-0">Recent Announcements</h2>
            <a href="<?= Helper::url('/announcements') ?>" class="text-primary text-decoration-none fw-semibold small">
                View All <i class="fas fa-arrow-right ms-1"></i>
            </a>
        </div>

        <div class="row g-4">
            <?php if (!empty($announcements)): ?>
                <?php foreach ($announcements as $ann): ?>
                    <div class="col-md-4">
                        <div class="card h-100 shadow-sm border-0 bg-white">
                            <div class="card-body p-4">
                                <span class="badge bg-secondary mb-2" style="font-size: 0.75rem;">
                                    <i class="fas fa-calendar-alt me-1"></i> <?= date('M d, Y', strtotime($ann['created_at'])) ?>
                                </span>
                                <h3 class="card-title h5 fw-bold text-dark mb-2"><?= Helper::esc($ann['title']) ?></h3>
                                <p class="card-text text-secondary small text-truncate-3"><?= Helper::esc($ann['content']) ?></p>
                            </div>
                            <div class="card-footer bg-light border-0 py-3 px-4 d-flex justify-content-between align-items-center">
                                <span class="text-muted small"><i class="fas fa-user-edit me-1"></i> <?= Helper::esc($ann['creator_name']) ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-4 bg-light rounded border border-dashed text-secondary">
                    <i class="fas fa-bullhorn fs-2 mb-2"></i>
                    <p class="mb-0">No active system announcements have been posted yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
