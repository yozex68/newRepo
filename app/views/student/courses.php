<?php use App\Helpers\Helper; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1">Academic Curriculum Courses</h1>
        <p class="text-secondary small mb-0">Enrolled Programme: <span class="badge bg-blue fw-semibold"><?= Helper::esc($student['programme_code']) ?> - <?= Helper::esc($student['programme_name']) ?></span></p>
    </div>
</div>

<div class="row g-4">
    <!-- Main Programme Curriculum -->
    <div class="col-lg-8">
        <?php if (!empty($curriculum)): ?>
            <div class="accordion d-flex flex-column gap-3" id="curriculumAccordion">
                <?php $yearIndex = 0; ?>
                <?php foreach ($curriculum as $yearName => $semestersList): ?>
                    <?php $yearIndex++; ?>
                    <div class="card border-0 shadow-sm overflow-hidden">
                        <div class="card-header bg-dark text-white py-3" id="headingYear<?= $yearIndex ?>">
                            <h2 class="h5 fw-bold mb-0">
                                <i class="fas fa-calendar-alt text-warning me-2"></i> <?= Helper::esc($yearName) ?>
                            </h2>
                        </div>
                        
                        <div class="card-body p-4 bg-white">
                            <div class="row g-4">
                                <?php foreach ($semestersList as $semesterName => $coursesList): ?>
                                    <div class="col-md-6">
                                        <h3 class="h6 fw-bold border-bottom pb-2 text-primary mb-3">
                                            <i class="fas fa-bookmark text-success me-1"></i> <?= Helper::esc($semesterName) ?>
                                        </h3>
                                        <div class="d-flex flex-column gap-2">
                                            <?php foreach ($coursesList as $c): ?>
                                                <a href="<?= Helper::url('/courses/' . $c['id']) ?>" class="list-group-item list-group-item-action p-3 border rounded-3 d-flex justify-content-between align-items-center bg-light-hover transition">
                                                    <div>
                                                        <div class="fw-bold text-dark small"><?= Helper::esc($c['code']) ?></div>
                                                        <div class="text-secondary small fw-medium text-truncate" style="max-width: 200px;"><?= Helper::esc($c['name']) ?></div>
                                                    </div>
                                                    <i class="fas fa-chevron-right text-muted small"></i>
                                                </a>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-5 bg-white rounded-3 shadow-sm border border-dashed text-secondary">
                <i class="fas fa-book-open fs-1 mb-3"></i>
                <h3 class="h5 fw-bold">No Enrolled Courses Found</h3>
                <p class="mb-0">No classes have been registered under your current programme yet.</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- External Programmes Access Control -->
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 bg-white">
            <div class="card-header bg-white border-bottom py-3">
                <h2 class="h5 fw-bold mb-0 text-dark"><i class="fas fa-lock-open text-warning me-2"></i> External Class Access</h2>
            </div>
            <div class="card-body p-4">
                <p class="text-secondary small">
                    You have administrative clearance to access resource files from these other majors:
                </p>

                <?php if (!empty($externalCourses)): ?>
                    <div class="d-flex flex-column gap-3 mt-3">
                        <?php foreach ($externalCourses as $progName => $coursesList): ?>
                            <div class="border rounded-3 p-3 bg-light">
                                <h3 class="h6 fw-bold text-primary mb-2 border-bottom pb-1"><i class="fas fa-project-diagram me-1"></i> <?= Helper::esc($progName) ?></h3>
                                <div class="list-group list-group-flush">
                                    <?php foreach ($coursesList as $c): ?>
                                        <a href="<?= Helper::url('/courses/' . $c['id']) ?>" class="list-group-item list-group-item-action px-0 py-2 bg-transparent text-secondary small d-flex justify-content-between align-items-center">
                                            <span><strong><?= Helper::esc($c['code']) ?></strong> - <?= Helper::esc($c['name']) ?></span>
                                            <i class="fas fa-chevron-right text-muted" style="font-size: 0.7rem;"></i>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4 bg-light rounded border border-dashed text-secondary mt-3">
                        <i class="fas fa-lock fs-2 mb-2"></i>
                        <p class="small mb-0">No external programme permissions granted yet.</p>
                        <a href="<?= Helper::url('/subscribe') ?>" class="btn btn-outline-primary btn-sm rounded-pill mt-3 px-3">Upgrade Plan</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
