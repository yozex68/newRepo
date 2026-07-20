<?php use App\Helpers\Helper; ?>

<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= Helper::url('/questions') ?>" class="text-decoration-none">Tickets Directory</a></li>
        <li class="breadcrumb-item active" aria-current="page">Ticket #<?= $question['id'] ?></li>
    </ol>
</nav>

<div class="row g-4">
    <!-- Ticket thread conversation -->
    <div class="col-lg-8">
        <!-- Main ticket question card -->
        <div class="card shadow-sm border-0 bg-white mb-4">
            <div class="card-header bg-dark text-white py-3 d-flex justify-content-between align-items-center">
                <span class="fw-semibold">Ticket #<?= $question['id'] ?></span>
                <span class="badge <?= ($question['status'] === 'pending') ? 'badge-blue' : 'badge-green' ?> fs-7 px-3 py-1">
                    <?= ucfirst($question['status']) ?>
                </span>
            </div>
            <div class="card-body p-4 p-md-5">
                <h1 class="h4 fw-bold text-dark mb-3"><?= Helper::esc($question['title']) ?></h1>
                <p class="text-secondary mb-4" style="white-space: pre-line; line-height: 1.6; font-size: 0.95rem;">
                    <?= Helper::esc($question['content']) ?>
                </p>
                <div class="border-top pt-3 d-flex align-items-center justify-content-between text-muted small">
                    <span>Opened by: <strong><?= Helper::esc($question['user_name']) ?></strong></span>
                    <span>Date: <strong><?= date('M d, Y \a\t H:i', strtotime($question['created_at'])) ?></strong></span>
                </div>
            </div>
        </div>

        <!-- Chat replies bubble section -->
        <h2 class="h5 fw-bold mb-3 text-dark"><i class="fas fa-comments text-primary me-2"></i> Conversation History</h2>
        
        <?php if (!empty($replies)): ?>
            <div class="d-flex flex-column gap-3 mb-4">
                <?php foreach ($replies as $r): ?>
                    <?php 
                    $isAdmin = ($r['user_role'] === 'admin');
                    $bubbleBg = $isAdmin ? 'bg-light border-start border-primary border-4' : 'bg-white border';
                    $alignClass = $isAdmin ? 'align-self-start' : 'align-self-end';
                    $roleLabel = $isAdmin ? '<span class="badge bg-danger ms-1 text-white">Admin</span>' : '<span class="badge bg-success ms-1 text-white">Student</span>';
                    ?>
                    <div class="card border-0 shadow-sm rounded-3 <?= $bubbleBg ?>" style="max-width: 85%;">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center mb-2 gap-3 border-bottom pb-1" style="font-size: 0.75rem;">
                                <span>
                                    <strong class="text-dark"><?= Helper::esc($r['user_name']) ?></strong> 
                                    <?= $roleLabel ?>
                                </span>
                                <span class="text-muted"><?= date('M d, Y H:i', strtotime($r['created_at'])) ?></span>
                            </div>
                            <p class="text-secondary mb-0 small" style="white-space: pre-line; line-height: 1.5;">
                                <?= Helper::esc($r['content']) ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-4 bg-white rounded-3 shadow-sm border mb-4 text-muted small">
                <i class="fas fa-comment-slash fs-3 mb-2"></i>
                <p class="mb-0">No responses have been posted on this thread yet.</p>
            </div>
        <?php endif; ?>

        <!-- Post reply message form -->
        <div class="card shadow-sm border-0 bg-white">
            <div class="card-header bg-white border-bottom py-3">
                <h3 class="h6 fw-bold mb-0 text-dark"><i class="fas fa-pen-nib text-success me-2"></i> Post Reply Response</h3>
            </div>
            <div class="card-body p-4">
                <form action="<?= Helper::url('/questions/' . $question['id'] . '/reply') ?>" method="POST">
                    <?= Helper::csrf_field() ?>
                    <div class="mb-3">
                        <textarea class="form-control" name="content" rows="4" placeholder="Write your response message here..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-paper-plane me-1"></i> Send Reply
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Ticket details sidebar -->
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 bg-white">
            <div class="card-header bg-white border-bottom py-3">
                <h2 class="h5 fw-bold mb-0 text-dark"><i class="fas fa-info-circle text-primary me-2"></i> Ticket Details</h2>
            </div>
            <div class="card-body p-4 text-secondary small">
                <ul class="list-unstyled d-flex flex-column gap-3 mb-0">
                    <li class="d-flex justify-content-between">
                        <span>Status:</span>
                        <span class="badge <?= ($question['status'] === 'pending') ? 'bg-primary' : 'bg-success' ?>"><?= ucfirst($question['status']) ?></span>
                    </li>
                    <li class="d-flex justify-content-between">
                        <span>Opened by:</span>
                        <span class="fw-semibold text-dark"><?= Helper::esc($question['user_name']) ?></span>
                    </li>
                    <li class="d-flex justify-content-between">
                        <span>Sender Email:</span>
                        <span class="fw-semibold text-dark"><?= Helper::esc($question['user_email']) ?></span>
                    </li>
                    <li class="d-flex justify-content-between">
                        <span>Date Opened:</span>
                        <span class="fw-semibold text-dark"><?= date('Y-m-d H:i', strtotime($question['created_at'])) ?></span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
