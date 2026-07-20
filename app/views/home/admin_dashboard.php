<?php use App\Helpers\Helper; ?>

<div class="row g-4 mb-4">
    <div class="col-12">
        <h1 class="h3 fw-bold text-dark mb-1">Administrative Control Board</h1>
        <p class="text-secondary small mb-0">System performance counters, resource catalog statistics, and activity audits.</p>
    </div>
</div>

<!-- Key Metric Statistics Widget Counters -->
<div class="row g-3 mb-4">
    <!-- Total Students -->
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm p-3 h-100 bg-white">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-3 bg-success bg-opacity-10 p-3 text-success">
                    <i class="fas fa-user-graduate fs-4"></i>
                </div>
                <div>
                    <span class="text-muted small d-block">Students</span>
                    <strong class="fs-4 text-dark"><?= $totalStudents ?></strong>
                </div>
            </div>
        </div>
    </div>
    <!-- Total Courses -->
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm p-3 h-100 bg-white">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-3 bg-primary bg-opacity-10 p-3 text-primary">
                    <i class="fas fa-book fs-4"></i>
                </div>
                <div>
                    <span class="text-muted small d-block">Courses</span>
                    <strong class="fs-4 text-dark"><?= $totalCourses ?></strong>
                </div>
            </div>
        </div>
    </div>
    <!-- Total Materials -->
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm p-3 h-100 bg-white">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-3 bg-warning bg-opacity-10 p-3 text-warning">
                    <i class="fas fa-folder-open fs-4"></i>
                </div>
                <div>
                    <span class="text-muted small d-block">Materials</span>
                    <strong class="fs-4 text-dark"><?= $totalMaterials ?></strong>
                </div>
            </div>
        </div>
    </div>
    <!-- Total Downloads -->
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm p-3 h-100 bg-white">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-3 bg-danger bg-opacity-10 p-3 text-danger">
                    <i class="fas fa-download fs-4"></i>
                </div>
                <div>
                    <span class="text-muted small d-block">Downloads</span>
                    <strong class="fs-4 text-dark"><?= $totalDownloads ?></strong>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Type breakdown statistics widgets -->
<div class="row g-3 mb-4">
    <div class="col-4">
        <div class="card border-0 shadow-sm p-3 bg-white text-center">
            <span class="text-muted small d-block"><i class="fas fa-book me-1"></i> Reference Books</span>
            <strong class="fs-5 text-dark"><?= $totalBooks ?></strong>
        </div>
    </div>
    <div class="col-4">
        <div class="card border-0 shadow-sm p-3 bg-white text-center">
            <span class="text-muted small d-block"><i class="fas fa-file-pdf me-1"></i> Lecture Notes</span>
            <strong class="fs-5 text-dark"><?= $totalNotes ?></strong>
        </div>
    </div>
    <div class="col-4">
        <div class="card border-0 shadow-sm p-3 bg-white text-center">
            <span class="text-muted small d-block"><i class="fas fa-file-invoice me-1"></i> Past Papers</span>
            <strong class="fs-5 text-dark"><?= $totalPapers ?></strong>
        </div>
    </div>
</div>

<!-- Interactive Analytics Charts -->
<div class="row g-4 mb-4">
    <!-- Downloads Trends (Line Chart) -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm bg-white h-100">
            <div class="card-header bg-white border-bottom py-3">
                <h2 class="h6 fw-bold mb-0 text-dark"><i class="fas fa-chart-area text-primary me-2"></i> Downloads Trend (Last 7 Days)</h2>
            </div>
            <div class="card-body">
                <canvas id="downloadsTrendChart" height="240"></canvas>
            </div>
        </div>
    </div>
    <!-- Materials Distribution (Pie Chart) -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm bg-white h-100">
            <div class="card-header bg-white border-bottom py-3">
                <h2 class="h6 fw-bold mb-0 text-dark"><i class="fas fa-chart-pie text-success me-2"></i> Resource Formats Distribution</h2>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <canvas id="materialsTypeChart" height="240"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Recent Activity Log & Uploads (Left Body) -->
    <div class="col-lg-8 d-flex flex-column gap-4">
        <!-- Audit Trail Logs -->
        <div class="card shadow-sm border-0 bg-white">
            <div class="card-header bg-white border-bottom py-3">
                <h2 class="h5 fw-bold mb-0 text-dark"><i class="fas fa-shield-alt text-danger me-2"></i> Audit Trail Activity Log</h2>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">User</th>
                                <th>Action</th>
                                <th>Details</th>
                                <th>IP Address</th>
                                <th class="pe-3">Timestamp</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($auditLogs as $log): ?>
                                <tr>
                                    <td class="ps-3">
                                        <div class="fw-bold"><?= Helper::esc($log['user_name'] ?? 'System') ?></div>
                                        <div class="text-muted" style="font-size: 0.75rem;"><?= Helper::esc($log['user_email'] ?? 'cron/installer') ?></div>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary"><?= Helper::esc($log['action']) ?></span>
                                    </td>
                                    <td class="text-secondary" style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        <?= Helper::esc($log['details']) ?>
                                    </td>
                                    <td class="text-muted"><?= Helper::esc($log['ip_address']) ?></td>
                                    <td class="pe-3 text-muted"><?= date('Y-m-d H:i', strtotime($log['created_at'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Sidebar Widgets -->
    <div class="col-lg-4 d-flex flex-column gap-4">
        <!-- Recent Uploads List -->
        <div class="card shadow-sm border-0 bg-white">
            <div class="card-header bg-white border-bottom py-3">
                <h2 class="h6 fw-bold mb-0 text-dark"><i class="fas fa-file-upload text-warning me-2"></i> Recent Uploads</h2>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($recentUploads)): ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($recentUploads as $m): ?>
                            <div class="list-group-item p-3 bg-light-hover transition">
                                <div class="fw-bold text-dark small text-truncate"><?= Helper::esc($m['title']) ?></div>
                                <div class="text-secondary small d-flex justify-content-between mt-1" style="font-size: 0.75rem;">
                                    <span>Course: <strong><?= Helper::esc($m['course_code']) ?></strong></span>
                                    <span class="text-muted"><?= date('M d, Y', strtotime($m['created_at'])) ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4 text-muted small">No materials uploaded yet.</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Recent Registered Users -->
        <div class="card shadow-sm border-0 bg-white">
            <div class="card-header bg-white border-bottom py-3">
                <h2 class="h6 fw-bold mb-0 text-dark"><i class="fas fa-user-plus text-primary me-2"></i> Recent Registrations</h2>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <?php foreach ($recentUsers as $u): ?>
                        <div class="list-group-item p-3 bg-light-hover transition">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-bold text-dark small"><?= Helper::esc($u['name']) ?></div>
                                    <div class="text-muted small" style="font-size: 0.75rem;"><?= Helper::esc($u['email']) ?></div>
                                </div>
                                <span class="badge <?= Helper::roleBadge($u['role']) ?>"><?= ucfirst($u['role']) ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart JS Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    // 1. Download Trends Chart Setup
    const trendCtx = document.getElementById('downloadsTrendChart').getContext('2d');
    const trendData = <?= json_encode($downloadTrends) ?>;
    
    const trendLabels = trendData.map(item => item.date_label);
    const trendCounts = trendData.map(item => item.count);

    new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: trendLabels.length ? trendLabels : ['No Data'],
            datasets: [{
                label: 'Files Downloaded',
                data: trendCounts.length ? trendCounts : [0],
                borderColor: '#1e3a8a',
                backgroundColor: 'rgba(30, 58, 138, 0.05)',
                borderWidth: 3,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 }
                }
            }
        }
    });

    // 2. Material Formats Chart Setup
    const typeCtx = document.getElementById('materialsTypeChart').getContext('2d');
    const typeData = <?= json_encode($typeBreakdown) ?>;
    
    const typeLabels = typeData.map(item => item.material_type.toUpperCase());
    const typeCounts = typeData.map(item => item.count);

    new Chart(typeCtx, {
        type: 'doughnut',
        data: {
            labels: typeLabels.length ? typeLabels : ['Empty'],
            datasets: [{
                data: typeCounts.length ? typeCounts : [0],
                backgroundColor: [
                    '#ef4444', // Red
                    '#3b82f6', // Blue
                    '#f59e0b', // Gold
                    '#10b981', // Green
                    '#6366f1', // Indigo
                    '#8b5cf6', // Purple
                    '#ec4899', // Pink
                    '#14b8a6', // Teal
                    '#f97316', // Orange
                    '#64748b'  // Slate
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { boxWidth: 12, padding: 8 }
                }
            }
        }
    });
});
</script>
