<?php use App\Helpers\Helper; ?>

<div class="row g-4 mb-4">
    <!-- Greeting Widget -->
    <div class="col-12 animate-fade-in">
        <div class="card bg-dark text-white border-0 shadow-sm overflow-hidden p-4 p-md-5 rounded-3" style="background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%) !important;">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <span class="badge bg-warning text-dark mb-2 px-3 py-1 fw-bold">Academic Workspace</span>
                    <h1 class="h2 fw-bold text-white mb-2">Welcome back, <?= Helper::esc($student['name']) ?>!</h1>
                    <p class="mb-0 opacity-75 small">
                        SmartHUB organizes learning materials dynamically matching your enrolled programme curriculum. Use filters or sidebar navigation to browse.
                    </p>
                </div>
                <div class="col-md-4 text-md-end mt-4 mt-md-0">
                    <a href="<?= Helper::url('/courses') ?>" class="btn btn-warning text-white px-4 py-2 fw-semibold">
                        <i class="fas fa-book-reader me-2"></i> Browse Courses
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Main Left Dashboard Body -->
    <div class="col-lg-8 d-flex flex-column gap-4">
        
        <!-- Bookmarked Materials Catalog -->
        <div class="card shadow-sm border-0 bg-white">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h2 class="h5 fw-bold mb-0 text-dark">
                    <i class="fas fa-bookmark text-warning me-2"></i> My Bookmarked Materials
                </h2>
                <span class="badge bg-light border text-dark"><?= count($bookmarks) ?> items</span>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($bookmarks)): ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($bookmarks as $b): ?>
                            <div class="list-group-item p-3 d-flex justify-content-between align-items-center bg-light-hover transition">
                                <div>
                                    <h3 class="h6 fw-bold text-dark mb-1"><?= Helper::esc($b['title']) ?></h3>
                                    <div class="text-secondary small">
                                        Course: <strong><?= Helper::esc($b['course_code']) ?> - <?= Helper::esc($b['course_name']) ?></strong>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <a href="<?= Helper::url('/materials/download/' . $b['id']) ?>" class="btn btn-sm btn-primary">
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                    <a href="<?= Helper::url('/materials/bookmark/' . $b['id']) ?>" class="btn btn-sm btn-outline-danger" title="Remove Bookmark">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5 text-secondary">
                        <i class="fas fa-bookmark fs-2 mb-2 text-muted"></i>
                        <p class="mb-0 small">No bookmarked materials yet. Mark resources while browsing courses.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Recent Uploads for Enrolled Programme -->
        <div class="card shadow-sm border-0 bg-white">
            <div class="card-header bg-white border-bottom py-3">
                <h2 class="h5 fw-bold mb-0 text-dark">
                    <i class="fas fa-folder-open text-primary me-2"></i> Recent Material Additions
                </h2>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($recentMaterials)): ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($recentMaterials as $m): ?>
                            <div class="list-group-item p-3 d-flex justify-content-between align-items-center bg-light-hover transition">
                                <div>
                                    <div class="fw-bold text-dark small mb-1"><?= Helper::esc($m['title']) ?></div>
                                    <div class="text-muted small" style="font-size: 0.75rem;">
                                        Course: <strong><?= Helper::esc($m['course_code']) ?></strong> | Uploaded: <?= date('Y-m-d', strtotime($m['created_at'])) ?>
                                    </div>
                                </div>
                                <a href="<?= Helper::url('/materials/download/' . $m['id']) ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-download"></i> Download
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5 text-secondary">
                        <i class="fas fa-bell-slash fs-2 mb-2 text-muted"></i>
                        <p class="mb-0 small">No new files have been uploaded to your major recently.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>

    <!-- Right Sidebar Widgets -->
    <div class="col-lg-4 d-flex flex-column gap-4">
        
        <!-- Programme Details Widget -->
        <div class="card shadow-sm border-0 bg-white">
            <div class="card-header bg-white border-bottom py-3">
                <h2 class="h5 fw-bold mb-0 text-dark"><i class="fas fa-university text-primary me-2"></i> My Roster Profile</h2>
            </div>
            <div class="card-body p-4 text-secondary small">
                <ul class="list-unstyled d-flex flex-column gap-3 mb-0">
                    <li class="d-flex justify-content-between">
                        <span>Current Program:</span>
                        <strong class="text-dark"><?= Helper::esc($student['programme_code'] ?? 'None') ?></strong>
                    </li>
                    <li class="d-flex justify-content-between">
                        <span>Major Faculty:</span>
                        <strong class="text-dark"><?= Helper::esc($student['faculty_name'] ?? 'None') ?></strong>
                    </li>
                    <li class="d-flex justify-content-between">
                        <span>Billing Status:</span>
                        <span class="badge bg-success"><?= Helper::esc($student['plan_name'] ?? 'Basic Free') ?></span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Announcements Stream Widget -->
        <div class="card shadow-sm border-0 bg-white">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h2 class="h5 fw-bold mb-0 text-dark"><i class="fas fa-bullhorn text-danger me-2"></i> Announcements</h2>
                <a href="<?= Helper::url('/announcements') ?>" class="text-decoration-none small">View all</a>
            </div>
            <div class="card-body p-4">
                <?php if (!empty($announcements)): ?>
                    <div class="d-flex flex-column gap-3">
                        <?php foreach ($announcements as $ann): ?>
                            <div class="border-bottom pb-2 mb-2 last-border-0">
                                <h3 class="h6 fw-bold text-dark mb-1"><?= Helper::esc($ann['title']) ?></h3>
                                <p class="text-secondary small mb-1 text-truncate-2" style="font-size: 0.8rem;"><?= Helper::esc($ann['content']) ?></p>
                                <span class="text-muted small" style="font-size: 0.7rem;"><i class="fas fa-clock"></i> <?= date('M d, Y', strtotime($ann['created_at'])) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted small text-center mb-0">No announcements posted.</p>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>
