<?php use App\Helpers\Helper; ?>

<div class="row g-4 mb-4">
    <!-- User Directory -->
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 bg-white">
            <div class="card-header bg-white border-bottom py-3">
                <h1 class="h5 fw-bold mb-0 text-dark"><i class="fas fa-users text-primary me-2"></i> User Directory</h1>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($users)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">ID</th>
                                    <th>Name</th>
                                    <th>Email / Phone</th>
                                    <th>Registered Programme</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th class="pe-3 text-end">Update Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $u): ?>
                                    <?php 
                                    $userPhone = (new App\Models\User())->getPhone($u);
                                    ?>
                                    <tr>
                                        <td class="ps-3 text-secondary">#<?= $u['id'] ?></td>
                                        <td>
                                            <div class="fw-bold text-dark"><?= Helper::esc($u['name']) ?></div>
                                            <span class="text-muted small" style="font-size: 0.75rem;">Registered: <?= date('Y-m-d', strtotime($u['created_at'])) ?></span>
                                        </td>
                                        <td>
                                            <div class="text-secondary"><?= Helper::esc($u['email']) ?></div>
                                            <div class="text-muted" style="font-size: 0.75rem;"><i class="fas fa-phone-alt"></i> <?= Helper::esc($userPhone) ?></div>
                                        </td>
                                        <td>
                                            <?php if ($u['role'] === 'student'): ?>
                                                <span class="badge badge-blue"><?= Helper::esc($u['programme_code'] ?? 'Not set') ?></span>
                                            <?php else: ?>
                                                <span class="text-muted small">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge <?= Helper::roleBadge($u['role']) ?>"><?= ucfirst($u['role']) ?></span>
                                        </td>
                                        <td>
                                            <span class="badge <?= ($u['status'] === 'active') ? 'bg-success' : 'bg-danger' ?>">
                                                <?= ucfirst($u['status']) ?>
                                            </span>
                                        </td>
                                        <td class="pe-3 text-end">
                                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#userModal<?= $u['id'] ?>">
                                                <i class="fas fa-user-edit me-1"></i> Edit Status
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php foreach ($users as $u): ?>
                        <!-- Edit User Role & Status Modal -->
                        <div class="modal fade" id="userModal<?= $u['id'] ?>" tabindex="-1" aria-labelledby="userModalLabel<?= $u['id'] ?>" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow-lg bg-white text-dark">
                                    <div class="modal-header bg-primary text-white py-3">
                                        <h5 class="modal-title fw-bold" id="userModalLabel<?= $u['id'] ?>"><i class="fas fa-user-edit me-2"></i> Update User Status</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="<?= Helper::url('/admin/users/update-role/' . $u['id']) ?>" method="POST">
                                        <?= Helper::csrf_field() ?>
                                        <div class="modal-body p-4">
                                            <div class="mb-3">
                                                <p class="small text-secondary mb-1">Modifying details for:</p>
                                                <h6 class="fw-bold text-dark mb-0"><?= Helper::esc($u['name']) ?> (<?= Helper::esc($u['email']) ?>)</h6>
                                            </div>
                                            <div class="mb-3">
                                                <label for="role<?= $u['id'] ?>" class="form-label small fw-semibold text-secondary">Account Role</label>
                                                <select class="form-select text-dark" id="role<?= $u['id'] ?>" name="role" required>
                                                    <option value="guest" <?= ($u['role'] === 'guest') ? 'selected' : '' ?>>Guest (No downloads)</option>
                                                    <option value="student" <?= ($u['role'] === 'student') ? 'selected' : '' ?>>Student (Program restricted)</option>
                                                    <option value="admin" <?= ($u['role'] === 'admin') ? 'selected' : '' ?>>Administrator (Full access)</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label for="status<?= $u['id'] ?>" class="form-label small fw-semibold text-secondary">Account Status</label>
                                                <select class="form-select text-dark" id="status<?= $u['id'] ?>" name="status" required>
                                                    <option value="active" <?= ($u['status'] === 'active') ? 'selected' : '' ?>>Active</option>
                                                    <option value="inactive" <?= ($u['status'] === 'inactive') ? 'selected' : '' ?>>Inactive / Blocked</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer bg-light p-3 border-top">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary px-4">Update Details</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted text-center py-4 mb-0">No registered users in the database.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Grant External Programme Permission Form -->
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 bg-white mb-4">
            <div class="card-header bg-white border-bottom py-3">
                <h2 class="h5 fw-bold mb-0 text-dark"><i class="fas fa-key text-warning me-2"></i> Grant Cross-Major Access</h2>
            </div>
            <div class="card-body p-4">
                <form action="<?= Helper::url('/admin/users/grant-permission') ?>" method="POST">
                    <?= Helper::csrf_field() ?>

                    <div class="mb-3">
                        <label for="user_id" class="form-label small fw-semibold text-secondary">Select Student</label>
                        <select class="form-select text-dark" id="user_id" name="user_id" required>
                            <option value="">-- Choose Student --</option>
                            <?php foreach ($users as $u): ?>
                                <?php if ($u['role'] === 'student'): ?>
                                    <option value="<?= $u['id'] ?>"><?= Helper::esc($u['name']) ?> (<?= Helper::esc($u['programme_code'] ?? 'None') ?>)</option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="programme_id" class="form-label small fw-semibold text-secondary">Access to Programme</label>
                        <select class="form-select text-dark" id="programme_id" name="programme_id" required>
                            <option value="">-- Choose Programme --</option>
                            <?php foreach ($programmes as $p): ?>
                                <option value="<?= $p['id'] ?>">[<?= Helper::esc($p['code']) ?>] <?= Helper::esc($p['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2.5">
                        <i class="fas fa-unlock-alt me-2"></i> Grant Access Permission
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Current Cross-Major Permissions Directory -->
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0 bg-white">
            <div class="card-header bg-white border-bottom py-3">
                <h2 class="h5 fw-bold mb-0 text-dark"><i class="fas fa-lock-open text-success me-2"></i> Active Cross-Major Access Permissions</h2>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($permissions)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Student Name</th>
                                    <th>Enrolled Email</th>
                                    <th>Allowed Programme</th>
                                    <th>Granted By Admin</th>
                                    <th>Authorized On</th>
                                    <th class="pe-3 text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($permissions as $p): ?>
                                    <tr>
                                        <td class="ps-3 fw-bold text-dark"><?= Helper::esc($p['student_name']) ?></td>
                                        <td><?= Helper::esc($p['student_email']) ?></td>
                                        <td>
                                            <span class="badge badge-gold">[<?= Helper::esc($p['programme_code']) ?>] <?= Helper::esc($p['programme_name']) ?></span>
                                        </td>
                                        <td class="text-secondary"><?= Helper::esc($p['admin_name']) ?></td>
                                        <td class="text-muted"><?= date('Y-m-d H:i', strtotime($p['created_at'])) ?></td>
                                        <td class="pe-3 text-end">
                                            <a href="<?= Helper::url('/admin/users/revoke-permission/' . $p['id']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to revoke this cross-major permission?')" title="Revoke Permission">
                                                <i class="fas fa-ban me-1"></i> Revoke
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center py-4 mb-0">No cross-major permissions are currently active.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
