<?php use App\Helpers\Helper; ?>

<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= Helper::url('/courses') ?>" class="text-decoration-none">My Courses</a></li>
        <li class="breadcrumb-item active" aria-current="page"><?= Helper::esc($course['code']) ?></li>
    </ol>
</nav>

<div class="card shadow-sm border-0 bg-white mb-4">
    <div class="card-body p-4 p-md-5">
        <div class="row align-items-center">
            <div class="col-md-8">
                <span class="badge bg-success mb-2 px-3"><?= Helper::esc($course['code']) ?></span>
                <h1 class="h2 fw-bold text-dark mb-2"><?= Helper::esc($course['name']) ?></h1>
                <p class="text-secondary mb-3"><?= Helper::esc($course['description'] ?: 'No course description available.') ?></p>
                <div class="d-flex align-items-center gap-3 text-secondary small">
                    <span><i class="fas fa-user-tie text-primary me-1"></i> Lecturer: <strong><?= Helper::esc($course['lecturer']) ?></strong></span>
                    <span><i class="fas fa-layer-group text-warning me-1"></i> <?= Helper::esc($course['year_name']) ?> - <?= Helper::esc($course['semester_name']) ?></span>
                </div>
            </div>
            <div class="col-md-4 text-md-end mt-4 mt-md-0">
                <span class="badge badge-blue fs-6 px-3 py-2 border">Enrolled: <?= Helper::esc($course['programme_code']) ?></span>
            </div>
        </div>
    </div>
</div>

<!-- Material Categories Lists -->
<div class="row g-4">
    <div class="col-12">
        <h2 class="h4 fw-bold mb-4 text-dark"><i class="fas fa-folder-open text-primary me-2"></i> Learning Resources</h2>

        <?php if (!empty($groupedMaterials)): ?>
            <div class="row g-4">
                <?php 
                $types = [
                    'note' => ['label' => 'Lecture Notes', 'icon' => 'fa-file-pdf text-danger'],
                    'book' => ['label' => 'Reference Books', 'icon' => 'fa-book text-primary'],
                    'slide' => ['label' => 'Lecture Slides', 'icon' => 'fa-file-powerpoint text-warning'],
                    'assignment' => ['label' => 'Assignments & Projects', 'icon' => 'fa-file-signature text-success'],
                    'past_paper' => ['label' => 'Past Papers', 'icon' => 'fa-file-invoice text-info'],
                    'marking_scheme' => ['label' => 'Marking Schemes', 'icon' => 'fa-check-double text-success'],
                    'practical' => ['label' => 'Practical Files', 'icon' => 'fa-file-code text-secondary'],
                    'video' => ['label' => 'Video Tutorials', 'icon' => 'fa-file-video text-info'],
                    'zip' => ['label' => 'ZIP Resources', 'icon' => 'fa-file-archive text-warning'],
                    'image' => ['label' => 'Images & Schematics', 'icon' => 'fa-file-image text-secondary']
                ];
                ?>

                <?php foreach ($types as $typeKey => $typeData): ?>
                    <?php if (isset($groupedMaterials[$typeKey])): ?>
                        <div class="col-md-6">
                            <div class="card h-100 border-0 shadow-sm bg-white">
                                <div class="card-header bg-light border-0 py-3 d-flex justify-content-between align-items-center">
                                    <h3 class="h6 fw-bold mb-0 text-dark">
                                        <i class="fas <?= $typeData['icon'] ?> me-2"></i> <?= $typeData['label'] ?>
                                    </h3>
                                    <span class="badge bg-secondary rounded-pill"><?= count($groupedMaterials[$typeKey]) ?></span>
                                </div>
                                <div class="card-body p-0">
                                    <div class="list-group list-group-flush">
                                        <?php foreach ($groupedMaterials[$typeKey] as $m): ?>
                                            <div class="list-group-item p-3 d-flex justify-content-between align-items-center bg-light-hover transition">
                                                <div class="pe-3 flex-grow-1 min-width-0">
                                                    <h4 class="h6 fw-bold text-dark mb-1 text-truncate" title="<?= Helper::esc($m['title']) ?>"><?= Helper::esc($m['title']) ?></h4>
                                                    <div class="text-secondary small d-flex flex-wrap gap-2 align-items-center">
                                                        <span>Size: <?= Helper::formatBytes($m['file_size']) ?></span>
                                                        <span class="text-muted">•</span>
                                                        <span>Downloads: <?= $m['downloads_count'] ?></span>
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-center gap-2">
                                                    <!-- Bookmark Trigger -->
                                                    <a href="<?= Helper::url('/materials/bookmark/' . $m['id']) ?>" class="btn btn-sm btn-light border" title="Bookmark Resource">
                                                        <i class="fa-bookmark <?= in_array($m['id'], $bookmarkedIds) ? 'fas text-warning' : 'far text-muted' ?>"></i>
                                                    </a>
                                                    <!-- Download Trigger -->
                                                    <a href="<?= Helper::url('/materials/download/' . $m['id']) ?>" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-download me-1"></i> Download
                                                    </a>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-5 bg-white rounded-3 shadow-sm border border-dashed text-secondary">
                <i class="fas fa-folder-open fs-1 mb-3"></i>
                <h3 class="h5 fw-bold">No Materials Uploaded</h3>
                <p class="mb-0">There are currently no files index-uploaded for this specific course. Check back later.</p>
            </div>
        <?php endif; ?>
    </div>
</div>
