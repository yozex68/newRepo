<?php use App\Helpers\Helper; ?>

<div class="row g-4">
    <!-- Profile Info Form Card -->
    <div class="col-md-7">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom py-3">
                <h2 class="h5 fw-bold mb-0 text-dark"><i class="fas fa-id-card text-primary me-2"></i> Update Profile Information</h2>
            </div>
            <div class="card-body p-4">
                <form action="<?= Helper::url('/profile') ?>" method="POST">
                    <?= Helper::csrf_field() ?>

                    <div class="mb-3">
                        <label for="name" class="form-label small fw-semibold text-secondary">Full Name</label>
                        <input type="text" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" id="name" name="name" value="<?= Helper::esc($user['name']) ?>" required>
                        <?php if (isset($errors['name'])): ?>
                            <div class="invalid-feedback"><?= $errors['name'] ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label small fw-semibold text-secondary">Email Address</label>
                            <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" id="email" name="email" value="<?= Helper::esc($user['email']) ?>" required>
                            <?php if (isset($errors['email'])): ?>
                                <div class="invalid-feedback"><?= $errors['email'] ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label small fw-semibold text-secondary">Mobile Number (Encrypted)</label>
                            <input type="text" class="form-control <?= isset($errors['phone']) ? 'is-invalid' : '' ?>" id="phone" name="phone" value="<?= Helper::esc($user['phone']) ?>" required>
                            <?php if (isset($errors['phone'])): ?>
                                <div class="invalid-feedback"><?= $errors['phone'] ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if ($user['role'] === 'student'): ?>
                        <div class="row mt-2">
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-semibold text-secondary">Registered Programme</label>
                                <input type="text" class="form-control bg-light" value="<?= Helper::esc($user['programme_name'] ?? 'Not set') ?>" disabled>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-semibold text-secondary">Billing Subscription Plan</label>
                                <input type="text" class="form-control bg-light" value="<?= Helper::esc($user['plan_name'] ?? 'Basic Free') ?>" disabled>
                            </div>
                        </div>
                    <?php endif; ?>

                    <button type="submit" class="btn btn-primary px-4 mt-3">
                        <i class="fas fa-save me-2"></i> Save Changes
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Password Change Card -->
    <div class="col-md-5">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom py-3">
                <h2 class="h5 fw-bold mb-0 text-dark"><i class="fas fa-key text-warning me-2"></i> Change Account Password</h2>
            </div>
            <div class="card-body p-4">
                <form action="<?= Helper::url('/profile/password') ?>" method="POST">
                    <?= Helper::csrf_field() ?>

                    <div class="mb-3">
                        <label for="old_password" class="form-label small fw-semibold text-secondary">Current Password</label>
                        <input type="password" class="form-control <?= isset($password_errors['old_password']) ? 'is-invalid' : '' ?>" id="old_password" name="old_password" required>
                        <?php if (isset($password_errors['old_password'])): ?>
                            <div class="invalid-feedback"><?= $password_errors['old_password'] ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="new_password" class="form-label small fw-semibold text-secondary">New Password</label>
                        <input type="password" class="form-control <?= isset($password_errors['new_password']) ? 'is-invalid' : '' ?>" id="new_password" name="new_password" placeholder="Min 6 characters" required>
                        <?php if (isset($password_errors['new_password'])): ?>
                            <div class="invalid-feedback"><?= $password_errors['new_password'] ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-4">
                        <label for="confirm_password" class="form-label small fw-semibold text-secondary">Confirm New Password</label>
                        <input type="password" class="form-control <?= isset($password_errors['confirm_password']) ? 'is-invalid' : '' ?>" id="confirm_password" name="confirm_password" required>
                        <?php if (isset($password_errors['confirm_password'])): ?>
                            <div class="invalid-feedback"><?= $password_errors['confirm_password'] ?></div>
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="btn btn-warning px-4 text-white">
                        <i class="fas fa-lock me-2"></i> Update Password
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
