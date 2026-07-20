<?php use App\Helpers\Helper; ?>

<div class="text-center mb-4">
    <i class="fas fa-user-plus text-success" style="font-size: 3rem;"></i>
    <h1 class="h3 fw-bold mt-2 text-white">Create Account</h1>
    <p class="text-white-50 small">Join SmartHUB Digital Library and catalog systems</p>
</div>

<form action="<?= Helper::url('/register') ?>" method="POST" id="register-form">
    <?= Helper::csrf_field() ?>

    <div class="row">
        <!-- Full Name -->
        <div class="col-md-6 mb-3">
            <label for="name" class="form-label text-white-50 small">Full Name</label>
            <input type="text" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" id="name" name="name" value="<?= Helper::esc($old['name'] ?? '') ?>" placeholder="John Doe" required>
            <?php if (isset($errors['name'])): ?>
                <div class="invalid-feedback text-danger"><?= $errors['name'] ?></div>
            <?php endif; ?>
        </div>

        <!-- Email Address -->
        <div class="col-md-6 mb-3">
            <label for="email" class="form-label text-white-50 small">Email Address</label>
            <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" id="email" name="email" value="<?= Helper::esc($old['email'] ?? '') ?>" placeholder="doe@student.university.edu" required>
            <?php if (isset($errors['email'])): ?>
                <div class="invalid-feedback text-danger"><?= $errors['email'] ?></div>
            <?php endif; ?>
        </div>
    </div>

    <div class="row">
        <!-- Phone Number -->
        <div class="col-md-6 mb-3">
            <label for="phone" class="form-label text-white-50 small">Phone Number</label>
            <input type="text" class="form-control <?= isset($errors['phone']) ? 'is-invalid' : '' ?>" id="phone" name="phone" value="<?= Helper::esc($old['phone'] ?? '') ?>" placeholder="+256700000000" required>
            <?php if (isset($errors['phone'])): ?>
                <div class="invalid-feedback text-danger"><?= $errors['phone'] ?></div>
            <?php endif; ?>
        </div>

        <!-- Account Type / Role -->
        <div class="col-md-6 mb-3">
            <label for="role" class="form-label text-white-50 small">Register As</label>
            <select class="form-select" id="role" name="role">
                <option value="guest" <?= (isset($old['role']) && $old['role'] === 'guest') ? 'selected' : '' ?>>Guest (Standard Registration)</option>
                <option value="student" <?= (isset($old['role']) && $old['role'] === 'student') ? 'selected' : '' ?>>University Student (Enrolled Programme)</option>
            </select>
        </div>
    </div>

    <!-- Programme Selector (conditional via JS) -->
    <div class="mb-3" id="programme-group" style="display: none;">
        <label for="programme_id" class="form-label text-white-50 small">Select Academic Programme</label>
        <select class="form-select <?= isset($errors['programme_id']) ? 'is-invalid' : '' ?>" id="programme_id" name="programme_id">
            <option value="">-- Choose Programme --</option>
            <?php foreach ($programmes as $p): ?>
                <option value="<?= $p['id'] ?>" <?= (isset($old['programme_id']) && (int)$old['programme_id'] === $p['id']) ? 'selected' : '' ?>>
                    [<?= Helper::esc($p['code']) ?>] <?= Helper::esc($p['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if (isset($errors['programme_id'])): ?>
            <div class="invalid-feedback text-danger"><?= $errors['programme_id'] ?></div>
        <?php endif; ?>
    </div>

    <div class="row">
        <!-- Password -->
        <div class="col-md-6 mb-3">
            <label for="password" class="form-label text-white-50 small">Password</label>
            <input type="password" class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" id="password" name="password" placeholder="Min 6 characters" required>
            <?php if (isset($errors['password'])): ?>
                <div class="invalid-feedback text-danger"><?= $errors['password'] ?></div>
            <?php endif; ?>
        </div>

        <!-- Confirm Password -->
        <div class="col-md-6 mb-4">
            <label for="confirm_password" class="form-label text-white-50 small">Confirm Password</label>
            <input type="password" class="form-control <?= isset($errors['confirm_password']) ? 'is-invalid' : '' ?>" id="confirm_password" name="confirm_password" placeholder="Verify password" required>
            <?php if (isset($errors['confirm_password'])): ?>
                <div class="invalid-feedback text-danger"><?= $errors['confirm_password'] ?></div>
            <?php endif; ?>
        </div>
    </div>

    <button type="submit" class="btn btn-success w-100 py-2.5 fw-bold shadow-sm mb-3">
        <i class="fas fa-user-plus me-2"></i> Register Account
    </button>

    <div class="text-center mt-2">
        <p class="text-white-50 small mb-0">Already registered?</p>
        <a href="<?= Helper::url('/login') ?>" class="text-primary text-decoration-none small fw-semibold">
            Sign In Here <i class="fas fa-arrow-right ms-1"></i>
        </a>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const roleSelect = document.getElementById('role');
    const progGroup = document.getElementById('programme-group');
    const progSelect = document.getElementById('programme_id');

    function toggleProgramme() {
        if (roleSelect.value === 'student') {
            progGroup.style.display = 'block';
            progSelect.setAttribute('required', 'required');
        } else {
            progGroup.style.display = 'none';
            progSelect.removeAttribute('required');
            progSelect.value = '';
        }
    }

    roleSelect.addEventListener('change', toggleProgramme);
    toggleProgramme(); // Initial state setup
});
</script>
