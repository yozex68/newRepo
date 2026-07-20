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
                                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editModal<?= $f['id'] ?>" title="Edit Faculty">
                                                    <i class="fas fa-edit"></i>
                                                </button>
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

<?php if (!empty($faculties)): ?>
    <?php foreach ($faculties as $f): ?>
        <!-- Edit Faculty Modal -->
        <div class="modal fade" id="editModal<?= $f['id'] ?>" tabindex="-1" aria-labelledby="editModalLabel<?= $f['id'] ?>" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg bg-white">
                    <div class="modal-header bg-primary text-white py-3">
                        <h5 class="modal-title fw-bold" id="editModalLabel<?= $f['id'] ?>"><i class="fas fa-edit me-2"></i> Edit Faculty</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="<?= Helper::url('/admin/faculties/edit/' . $f['id']) ?>" method="POST">
                        <?= Helper::csrf_field() ?>
                        <div class="modal-body p-4">
                            <div class="mb-3">
                                <label for="edit_name<?= $f['id'] ?>" class="form-label small fw-semibold text-secondary">Faculty Name</label>
                                <input type="text" class="form-control text-dark" id="edit_name<?= $f['id'] ?>" name="name" value="<?= Helper::esc($f['name']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_desc<?= $f['id'] ?>" class="form-label small fw-semibold text-secondary">Description</label>
                                <textarea class="form-control text-dark" id="edit_desc<?= $f['id'] ?>" name="description" rows="3"><?= Helper::esc($f['description']) ?></textarea>
                            </div>
                        </div>
                        <div class="modal-footer bg-light p-3 border-top">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary px-4">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
