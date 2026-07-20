<?php use App\Helpers\Helper; ?>

<div class="row g-4">
    <!-- Left Column: Programmes Table List -->
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 bg-white">
            <div class="card-header bg-white border-bottom py-3">
                <h1 class="h5 fw-bold mb-0 text-dark"><i class="fas fa-scroll text-primary me-2"></i> Enrolled Programmes Directory</h1>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($programmes)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Code</th>
                                    <th>Programme Name</th>
                                    <th>Parent Faculty</th>
                                    <th>Description</th>
                                    <th class="pe-4 text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($programmes as $p): ?>
                                    <tr>
                                        <td class="ps-4 fw-bold text-primary"><?= Helper::esc($p['code']) ?></td>
                                        <td class="fw-semibold text-dark"><?= Helper::esc($p['name']) ?></td>
                                        <td>
                                            <span class="badge bg-secondary"><?= Helper::esc($p['faculty_name']) ?></span>
                                        </td>
                                        <td class="text-secondary small" style="max-width: 200px;"><?= Helper::esc($p['description'] ?: 'No description.') ?></td>
                                        <td class="pe-4 text-end">
                                            <div class="d-flex justify-content-end align-items-center gap-2">
                                                <a href="<?= Helper::url('/admin/programmes/delete/' . $p['id']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this programme and all its courses/materials?')" title="Delete Programme">
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
                    <p class="text-muted text-center py-5 mb-0">No academic programmes registered yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Right Column: Add Programme Form Panel -->
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 bg-white">
            <div class="card-header bg-white border-bottom py-3">
                <h2 class="h5 fw-bold mb-0 text-dark"><i class="fas fa-plus-circle text-success me-2"></i> Register Programme</h2>
            </div>
            <div class="card-body p-4">
                <form action="<?= Helper::url('/admin/programmes/create') ?>" method="POST">
                    <?= Helper::csrf_field() ?>

                    <div class="mb-3">
                        <label for="faculty_id" class="form-label small fw-semibold text-secondary">Assign Faculty</label>
                        <select class="form-select text-dark" id="faculty_id" name="faculty_id" required>
                            <option value="">-- Choose Faculty --</option>
                            <?php foreach ($faculties as $f): ?>
                                <option value="<?= $f['id'] ?>"><?= Helper::esc($f['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="name" class="form-label small fw-semibold text-secondary">Programme Name</label>
                        <input type="text" class="form-control text-dark" id="name" name="name" placeholder="e.g. Bachelor of Commerce" required>
                    </div>

                    <div class="mb-3">
                        <label for="code" class="form-label small fw-semibold text-secondary">Programme Code</label>
                        <input type="text" class="form-control text-dark" id="code" name="code" placeholder="e.g. BCOM" required>
                    </div>

                    <div class="mb-4">
                        <label for="description" class="form-label small fw-semibold text-secondary">Description</label>
                        <textarea class="form-control text-dark" id="description" name="description" rows="3" placeholder="Overview of the major's objectives..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2.5">
                        <i class="fas fa-save me-2"></i> Save Programme
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

