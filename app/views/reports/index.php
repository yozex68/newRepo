<?php use App\Helpers\Helper; ?>

<div class="row g-4 mb-4">
    <div class="col-12">
        <h1 class="h3 fw-bold text-dark mb-1">Academic Reports & System Analytics</h1>
        <p class="text-secondary small mb-0">Generate audit logs, materials catalogs, student lists, and export files to Excel or printer-friendly layouts.</p>
    </div>
</div>

<div class="card shadow-sm border-0 bg-white">
    <div class="card-header bg-white border-bottom py-3">
        <!-- Navigation Tabs -->
        <ul class="nav nav-tabs card-header-tabs border-bottom-0" id="reportTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-bold text-secondary" id="downloads-tab" data-bs-toggle="tab" data-bs-target="#downloads" type="button" role="tab" aria-controls="downloads" aria-selected="true">
                    <i class="fas fa-download me-1"></i> Downloads activity
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold text-secondary" id="students-tab" data-bs-toggle="tab" data-bs-target="#students" type="button" role="tab" aria-controls="students" aria-selected="false">
                    <i class="fas fa-user-graduate me-1"></i> Student Roster
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold text-secondary" id="courses-tab" data-bs-toggle="tab" data-bs-target="#courses" type="button" role="tab" aria-controls="courses" aria-selected="false">
                    <i class="fas fa-book me-1"></i> Course Curriculum
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold text-secondary" id="materials-tab" data-bs-toggle="tab" data-bs-target="#materials" type="button" role="tab" aria-controls="materials" aria-selected="false">
                    <i class="fas fa-folder-open me-1"></i> Materials Inventory
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold text-secondary" id="subscriptions-tab" data-bs-toggle="tab" data-bs-target="#subscriptions" type="button" role="tab" aria-controls="subscriptions" aria-selected="false">
                    <i class="fas fa-receipt me-1"></i> Subscription Billings
                </button>
            </li>
        </ul>
    </div>

    <!-- Tabs Body -->
    <div class="card-body p-0">
        <div class="tab-content" id="reportTabsContent">
            
            <!-- 1. Downloads tab -->
            <div class="tab-pane fade show active p-4" id="downloads" role="tabpanel" aria-labelledby="downloads-tab">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="h6 fw-bold mb-0 text-dark">File Download Transactions</h2>
                    <div>
                        <a href="<?= Helper::url('/admin/reports/export?format=excel&report=downloads') ?>" class="btn btn-sm btn-success me-2"><i class="fas fa-file-excel me-1"></i> Export Excel</a>
                        <a href="<?= Helper::url('/admin/reports/export?format=pdf&report=downloads') ?>" target="_blank" class="btn btn-sm btn-primary"><i class="fas fa-print me-1"></i> Print PDF</a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                        <thead class="table-light">
                            <tr>
                                <th>Date/Time</th>
                                <th>Student Name</th>
                                <th>Email</th>
                                <th>File Name</th>
                                <th>Format</th>
                                <th>Course Code</th>
                                <th>IP Address</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($downloads as $d): ?>
                                <tr>
                                    <td><?= date('Y-m-d H:i', strtotime($d['downloaded_at'])) ?></td>
                                    <td class="fw-bold"><?= Helper::esc($d['user_name']) ?></td>
                                    <td><?= Helper::esc($d['user_email']) ?></td>
                                    <td class="text-dark fw-semibold"><?= Helper::esc($d['file_title']) ?></td>
                                    <td><span class="badge bg-secondary"><?= strtoupper(Helper::esc($d['material_type'])) ?></span></td>
                                    <td class="text-primary font-monospace"><?= Helper::esc($d['course_code']) ?></td>
                                    <td class="text-muted"><?= Helper::esc($d['ip_address']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 2. Students Roster tab -->
            <div class="tab-pane fade p-4" id="students" role="tabpanel" aria-labelledby="students-tab">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="h6 fw-bold mb-0 text-dark">Active Students Database</h2>
                    <div>
                        <a href="<?= Helper::url('/admin/reports/export?format=excel&report=students') ?>" class="btn btn-sm btn-success me-2"><i class="fas fa-file-excel me-1"></i> Export Excel</a>
                        <a href="<?= Helper::url('/admin/reports/export?format=pdf&report=students') ?>" target="_blank" class="btn btn-sm btn-primary"><i class="fas fa-print me-1"></i> Print PDF</a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                        <thead class="table-light">
                            <tr>
                                <th>Student Name</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Programme Enrolled</th>
                                <th>Subscription Plan</th>
                                <th>Created At</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $s): ?>
                                <tr>
                                    <td class="fw-bold"><?= Helper::esc($s['name']) ?></td>
                                    <td><?= Helper::esc($s['email']) ?></td>
                                    <td>
                                        <span class="badge <?= ($s['status'] === 'active') ? 'bg-success' : 'bg-danger' ?>"><?= ucfirst($s['status']) ?></span>
                                    </td>
                                    <td class="text-primary fw-semibold"><?= Helper::esc($s['programme_name'] ?? 'None') ?></td>
                                    <td><span class="badge badge-blue"><?= Helper::esc($s['plan_name'] ?? 'Basic Student') ?></span></td>
                                    <td class="text-muted"><?= date('Y-m-d', strtotime($s['created_at'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 3. Courses tab -->
            <div class="tab-pane fade p-4" id="courses" role="tabpanel" aria-labelledby="courses-tab">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="h6 fw-bold mb-0 text-dark">Course Syllabi Placement</h2>
                    <div>
                        <a href="<?= Helper::url('/admin/reports/export?format=excel&report=courses') ?>" class="btn btn-sm btn-success me-2"><i class="fas fa-file-excel me-1"></i> Export Excel</a>
                        <a href="<?= Helper::url('/admin/reports/export?format=pdf&report=courses') ?>" target="_blank" class="btn btn-sm btn-primary"><i class="fas fa-print me-1"></i> Print PDF</a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                        <thead class="table-light">
                            <tr>
                                <th>Course Code</th>
                                <th>Course Title</th>
                                <th>Lecturer</th>
                                <th>Programme</th>
                                <th>Materials Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $courseModel = new App\Models\Course();
                            $courseDetails = $courseModel->getWithDetails();
                            foreach ($courseDetails as $c): 
                                $matCount = count((new App\Models\Material())->query("SELECT id FROM materials WHERE course_id = :cid", ['cid' => $c['id']]));
                            ?>
                                <tr>
                                    <td class="fw-bold text-success"><?= Helper::esc($c['code']) ?></td>
                                    <td class="text-dark fw-semibold"><?= Helper::esc($c['name']) ?></td>
                                    <td><?= Helper::esc($c['lecturer']) ?></td>
                                    <td><span class="badge badge-blue"><?= Helper::esc($c['programme_code']) ?></span></td>
                                    <td><span class="badge bg-secondary"><?= $matCount ?> files</span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 4. Materials inventory tab -->
            <div class="tab-pane fade p-4" id="materials" role="tabpanel" aria-labelledby="materials-tab">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="h6 fw-bold mb-0 text-dark">Resources Materials Inventory</h2>
                    <div>
                        <a href="<?= Helper::url('/admin/reports/export?format=excel&report=materials') ?>" class="btn btn-sm btn-success me-2"><i class="fas fa-file-excel me-1"></i> Export Excel</a>
                        <a href="<?= Helper::url('/admin/reports/export?format=pdf&report=materials') ?>" target="_blank" class="btn btn-sm btn-primary"><i class="fas fa-print me-1"></i> Print PDF</a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                        <thead class="table-light">
                            <tr>
                                <th>Material Title</th>
                                <th>Type</th>
                                <th>Course Code</th>
                                <th>File Size</th>
                                <th>Downloads Count</th>
                                <th>Uploader</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($materials as $m): ?>
                                <tr>
                                    <td class="fw-semibold text-dark"><?= Helper::esc($m['title']) ?></td>
                                    <td><span class="badge bg-secondary"><?= strtoupper(Helper::esc($m['material_type'])) ?></span></td>
                                    <td class="text-primary font-monospace"><?= Helper::esc($m['course_code']) ?></td>
                                    <td><?= Helper::formatBytes($m['file_size']) ?></td>
                                    <td class="fw-bold"><?= $m['downloads_count'] ?></td>
                                    <td><?= Helper::esc($m['uploader_name']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 5. Subscriptions tab -->
            <div class="tab-pane fade p-4" id="subscriptions" role="tabpanel" aria-labelledby="subscriptions-tab">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="h6 fw-bold mb-0 text-dark">Premium Billings History</h2>
                    <div>
                        <a href="<?= Helper::url('/admin/reports/export?format=excel&report=subscriptions') ?>" class="btn btn-sm btn-success me-2"><i class="fas fa-file-excel me-1"></i> Export Excel</a>
                        <a href="<?= Helper::url('/admin/reports/export?format=pdf&report=subscriptions') ?>" target="_blank" class="btn btn-sm btn-primary"><i class="fas fa-print me-1"></i> Print PDF</a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                        <thead class="table-light">
                            <tr>
                                <th>User Name</th>
                                <th>Email</th>
                                <th>Plan Tier</th>
                                <th>Amount Paid</th>
                                <th>Status</th>
                                <th>Starts At</th>
                                <th>Expires At</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($subscriptions as $s): ?>
                                <tr>
                                    <td class="fw-bold"><?= Helper::esc($s['user_name']) ?></td>
                                    <td><?= Helper::esc($s['user_email']) ?></td>
                                    <td><span class="badge badge-blue"><?= Helper::esc($s['plan_name']) ?></span></td>
                                    <td class="fw-bold text-dark">$<?= number_format($s['plan_price'], 2) ?></td>
                                    <td>
                                        <span class="badge <?= ($s['status'] === 'active') ? 'bg-success' : 'bg-secondary' ?>"><?= ucfirst($s['status']) ?></span>
                                    </td>
                                    <td><?= date('Y-m-d', strtotime($s['starts_at'])) ?></td>
                                    <td><?= date('Y-m-d', strtotime($s['expires_at'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
