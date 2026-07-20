<?php use App\Helpers\Helper; ?>

<div class="row g-4">
    <!-- Left Column: Faculty Directory List -->
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 bg-white">
            <div class="card-header bg-white border-bottom py-3">
                <h1 class="h5 fw-bold mb-0 text-dark"><i class="fas fa-university text-primary me-2"></i> Faculty Directory</h1>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($faculties)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">ID</th>
                                    <th>Faculty Name</th>
                                    <th>Programmes</th>
                                    <th>Description</th>
                                    <th class="pe-4 text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($faculties as $f): ?>
                                    <tr>
                                        <td class="ps-4 fw-bold text-primary">#<?= $f['id'] ?></td>
                                        <td class="fw-semibold text-dark"><?= Helper::esc($f['name']) ?></td>
                                        <td>
                                            <span class="badge bg-secondary"><?= $f['programmes_count'] ?> majors</span>
                                        </td>
                                        <td class="text-secondary small" style="max-width: 250px;"><?= Helper::esc($f['description'] ?: 'No description provided.') ?></td>
                                        <td class="pe-4 text-end">
                                            <div class="d-flex justify-content-end align-items-center gap-2">
                                                <a href="<?= Helper::url('/admin/faculties/delete/' . $f['id']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this faculty and all its programmes/courses?')" title="Delete Faculty">
                                                    <i class="fas fa-trash-alt"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center py-5 mb-0">No academic faculties registered. Use the panel on the right to register one.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Right Column: Add Faculty Form Panel -->
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 bg-white">
            <div class="card-header bg-white border-bottom py-3">
                <h2 class="h5 fw-bold mb-0 text-dark"><i class="fas fa-plus-circle text-success me-2"></i> Add New Faculty</h2>
            </div>
            <div class="card-body p-4">
                <form action="<?= Helper::url('/admin/faculties/create') ?>" method="POST">
                    <?= Helper::csrf_field() ?>

                    <div class="mb-3">
                        <label for="name" class="form-label small fw-semibold text-secondary">Faculty Name</label>
                        <input type="text" class="form-control text-dark" id="name" name="name" placeholder="e.g. Faculty of Engineering" required>
                    </div>

                    <div class="mb-4">
                        <label for="description" class="form-label small fw-semibold text-secondary">Description</label>
                        <textarea class="form-control text-dark" id="description" name="description" rows="4" placeholder="Brief outline of disciplines studied under this faculty..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2.5">
                        <i class="fas fa-save me-2"></i> Register Faculty
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

