<?php use App\Helpers\Helper; ?>

<div class="row g-4">
    <!-- Left Column: Courses list -->
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 bg-white">
            <div class="card-header bg-white border-bottom py-3">
                <h1 class="h5 fw-bold mb-0 text-dark"><i class="fas fa-book text-primary me-2"></i> Course Catalog Directory</h1>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($courses)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Code</th>
                                    <th>Course Title</th>
                                    <th>Lecturer</th>
                                    <th>Programme</th>
                                    <th>Curriculum Placement</th>
                                    <th class="pe-4 text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($courses as $c): ?>
                                    <tr>
                                        <td class="ps-4 fw-bold text-success"><?= Helper::esc($c['code']) ?></td>
                                        <td class="fw-semibold text-dark"><?= Helper::esc($c['name']) ?></td>
                                        <td><?= Helper::esc($c['lecturer']) ?></td>
                                        <td>
                                            <span class="badge badge-blue"><?= Helper::esc($c['programme_code']) ?></span>
                                        </td>
                                        <td class="text-secondary small">
                                            <?= Helper::esc($c['year_name']) ?> - <?= Helper::esc($c['semester_name']) ?>
                                        </td>
                                        <td class="pe-4 text-end">
                                            <div class="d-flex justify-content-end align-items-center gap-2">
                                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editModal<?= $c['id'] ?>" title="Edit Course">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <a href="<?= Helper::url('/admin/courses/delete/' . $c['id']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this course and all its files?')" title="Delete Course">
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
                    <p class="text-muted text-center py-5 mb-0">No courses cataloged yet. Register one on the right panel.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Right Column: Add Course Form Panel -->
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 bg-white">
            <div class="card-header bg-white border-bottom py-3">
                <h2 class="h5 fw-bold mb-0 text-dark"><i class="fas fa-plus-circle text-success me-2"></i> Register New Course</h2>
            </div>
            <div class="card-body p-4">
                <form action="<?= Helper::url('/admin/courses/create') ?>" method="POST">
                    <?= Helper::csrf_field() ?>

                    <div class="mb-3">
                        <label for="programme_id" class="form-label small fw-semibold text-secondary">Assign Major Programme</label>
                        <select class="form-select text-dark" id="programme_id" name="programme_id" required>
                            <option value="">-- Choose Programme --</option>
                            <?php foreach ($programmes as $p): ?>
                                <option value="<?= $p['id'] ?>">[<?= Helper::esc($p['code']) ?>] <?= Helper::esc($p['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="year_id" class="form-label small fw-semibold text-secondary">Year of Study</label>
                            <select class="form-select text-dark" id="year_id" name="year_id" required>
                                <option value="">-- Year --</option>
                                <?php foreach ($years as $y): ?>
                                    <option value="<?= $y['id'] ?>"><?= Helper::esc($y['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="semester_id" class="form-label small fw-semibold text-secondary">Semester</label>
                            <select class="form-select text-dark" id="semester_id" name="semester_id" required>
                                <option value="">-- Sem --</option>
                                <?php foreach ($semesters as $s): ?>
                                    <option value="<?= $s['id'] ?>"><?= Helper::esc($s['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="name" class="form-label small fw-semibold text-secondary">Course Title</label>
                        <input type="text" class="form-control text-dark" id="name" name="name" placeholder="e.g. Internet and Web Development" required>
                    </div>

                    <div class="mb-3">
                        <label for="code" class="form-label small fw-semibold text-secondary">Course Code</label>
                        <input type="text" class="form-control text-dark" id="code" name="code" placeholder="e.g. BIT-2101" required>
                    </div>

                    <div class="mb-3">
                        <label for="lecturer" class="form-label small fw-semibold text-secondary">Assigned Lecturer</label>
                        <input type="text" class="form-control text-dark" id="lecturer" name="lecturer" placeholder="e.g. Dr. Jane Smith" required>
                    </div>

                    <div class="mb-4">
                        <label for="description" class="form-label small fw-semibold text-secondary">Brief Description</label>
                        <textarea class="form-control text-dark" id="description" name="description" rows="3" placeholder="Syllabus overview..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2.5">
                        <i class="fas fa-save me-2"></i> Save Course
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($courses)): ?>
    <?php foreach ($courses as $c): ?>
        <!-- Edit Course Modal -->
        <div class="modal fade" id="editModal<?= $c['id'] ?>" tabindex="-1" aria-labelledby="editModalLabel<?= $c['id'] ?>" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg bg-white">
                    <div class="modal-header bg-primary text-white py-3">
                        <h5 class="modal-title fw-bold" id="editModalLabel<?= $c['id'] ?>"><i class="fas fa-edit me-2"></i> Edit Course</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="<?= Helper::url('/admin/courses/edit/' . $c['id']) ?>" method="POST">
                        <?= Helper::csrf_field() ?>
                        <div class="modal-body p-4">
                            <div class="mb-3">
                                <label for="edit_programme_id<?= $c['id'] ?>" class="form-label small fw-semibold text-secondary">Programme Association</label>
                                <select class="form-select text-dark" id="edit_programme_id<?= $c['id'] ?>" name="programme_id" required>
                                    <?php foreach ($programmes as $p): ?>
                                        <option value="<?= $p['id'] ?>" <?= ($p['id'] == $c['programme_id']) ? 'selected' : '' ?>><?= Helper::esc($p['code']) ?> - <?= Helper::esc($p['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="row g-2 mb-3">
                                <div class="col-md-6">
                                    <label for="edit_year_id<?= $c['id'] ?>" class="form-label small fw-semibold text-secondary">Year Placement</label>
                                    <select class="form-select text-dark" id="edit_year_id<?= $c['id'] ?>" name="year_id" required>
                                        <?php foreach ($years as $y): ?>
                                            <option value="<?= $y['id'] ?>" <?= ($y['id'] == $c['year_id']) ? 'selected' : '' ?>><?= Helper::esc($y['name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="edit_semester_id<?= $c['id'] ?>" class="form-label small fw-semibold text-secondary">Semester Placement</label>
                                    <select class="form-select text-dark" id="edit_semester_id<?= $c['id'] ?>" name="semester_id" required>
                                        <?php foreach ($semesters as $s): ?>
                                            <option value="<?= $s['id'] ?>" <?= ($s['id'] == $c['semester_id']) ? 'selected' : '' ?>><?= Helper::esc($s['name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="edit_code<?= $c['id'] ?>" class="form-label small fw-semibold text-secondary">Course Code</label>
                                <input type="text" class="form-control text-dark" id="edit_code<?= $c['id'] ?>" name="code" value="<?= Helper::esc($c['code']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_name<?= $c['id'] ?>" class="form-label small fw-semibold text-secondary">Course Name</label>
                                <input type="text" class="form-control text-dark" id="edit_name<?= $c['id'] ?>" name="name" value="<?= Helper::esc($c['name']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_lecturer<?= $c['id'] ?>" class="form-label small fw-semibold text-secondary">Lecturer Name</label>
                                <input type="text" class="form-control text-dark" id="edit_lecturer<?= $c['id'] ?>" name="lecturer" value="<?= Helper::esc($c['lecturer']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_desc<?= $c['id'] ?>" class="form-label small fw-semibold text-secondary">Description / Outline</label>
                                <textarea class="form-control text-dark" id="edit_desc<?= $c['id'] ?>" name="description" rows="3"><?= Helper::esc($c['description']) ?></textarea>
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

