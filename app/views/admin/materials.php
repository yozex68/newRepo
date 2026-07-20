<?php use App\Helpers\Helper; ?>

<div class="row g-4">
    <!-- Left Column: Catalog list of uploaded resources -->
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 bg-white">
            <div class="card-header bg-white border-bottom py-3">
                <h1 class="h5 fw-bold mb-0 text-dark"><i class="fas fa-folder-open text-primary me-2"></i> Cataloged Materials Directory</h1>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($materials)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Resource Name</th>
                                    <th>Format / Type</th>
                                    <th>Course Code</th>
                                    <th>File Size</th>
                                    <th>Downloads</th>
                                    <th>Uploader</th>
                                    <th class="pe-3 text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($materials as $m): ?>
                                    <tr>
                                        <td class="ps-3">
                                            <div class="fw-bold text-dark mb-0"><?= Helper::esc($m['title']) ?></div>
                                            <small class="text-secondary text-truncate d-block" style="max-width: 200px;"><?= Helper::esc($m['description']) ?></small>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary"><?= strtoupper(Helper::esc($m['material_type'])) ?></span>
                                        </td>
                                        <td class="fw-semibold text-primary"><?= Helper::esc($m['course_code']) ?></td>
                                        <td class="text-secondary"><?= Helper::formatBytes($m['file_size']) ?></td>
                                        <td class="text-secondary font-monospace"><?= $m['downloads_count'] ?></td>
                                        <td class="text-secondary"><?= Helper::esc($m['uploader_name']) ?></td>
                                        <td class="pe-3 text-end">
                                            <a href="<?= Helper::url('/admin/materials/delete/' . $m['id']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this resource file permanently?')" title="Delete File">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center py-5 mb-0">No resource files uploaded yet. Upload one using the panel on the right.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Right Column: Upload Form -->
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 bg-white">
            <div class="card-header bg-white border-bottom py-3">
                <h2 class="h5 fw-bold mb-0 text-dark"><i class="fas fa-file-upload text-success me-2"></i> Upload Resource File</h2>
            </div>
            <div class="card-body p-4">
                <form action="<?= Helper::url('/admin/materials/upload') ?>" method="POST" enctype="multipart/form-data">
                    <?= Helper::csrf_field() ?>

                    <div class="mb-3">
                        <label for="course_id" class="form-label small fw-semibold text-secondary">Assign Course</label>
                        <select class="form-select text-dark" id="course_id" name="course_id" required>
                            <option value="">-- Choose Course --</option>
                            <?php foreach ($courses as $c): ?>
                                <option value="<?= $c['id'] ?>">[<?= Helper::esc($c['code']) ?>] <?= Helper::esc($c['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="title" class="form-label small fw-semibold text-secondary">Material Title</label>
                        <input type="text" class="form-control text-dark" id="title" name="title" placeholder="e.g. Week 3 Web Routing Notes" required>
                    </div>

                    <div class="mb-3">
                        <label for="material_type" class="form-label small fw-semibold text-secondary">Resource Format Category</label>
                        <select class="form-select text-dark" id="material_type" name="material_type" required>
                            <option value="">-- Select Type --</option>
                            <option value="note">Lecture Notes (PDF)</option>
                            <option value="book">Reference Textbook</option>
                            <option value="slide">Presentation Slides (PPT/PDF)</option>
                            <option value="assignment">Assignment Sheet</option>
                            <option value="past_paper">Past Examination Paper</option>
                            <option value="marking_scheme">Marking Scheme Guide</option>
                            <option value="practical">Practical/Lab Files</option>
                            <option value="video">Video Lecture (MP4)</option>
                            <option value="zip">ZIP Resource Archive</option>
                            <option value="image">Diagram / Schematic</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="material_file" class="form-label small fw-semibold text-secondary">Choose File</label>
                        <input type="file" class="form-control text-dark" id="material_file" name="material_file" required>
                        <div class="form-text small text-muted">Max file size: 50MB. Allowed: PDF, DOCX, PPT, ZIP, MP4, JPG, PNG.</div>
                    </div>

                    <div class="mb-4">
                        <label for="description" class="form-label small fw-semibold text-secondary">Brief Description / Notes</label>
                        <textarea class="form-control text-dark" id="description" name="description" rows="3" placeholder="Describe the topics covered in this resource..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2.5">
                        <i class="fas fa-cloud-upload-alt me-2"></i> Upload Material
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
