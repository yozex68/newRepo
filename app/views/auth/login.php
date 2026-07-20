<?php use App\Helpers\Helper; ?>

<div class="text-center mb-4">
    <i class="fas fa-graduation-cap text-warning" style="font-size: 3rem;"></i>
    <h1 class="h3 fw-bold mt-2 text-white">Sign In to SmartHUB</h1>
    <p class="text-secondary small">Access your academic program files and library catalog</p>
</div>

<form action="<?= Helper::url('/login') ?>" method="POST">
    <?= Helper::csrf_field() ?>

    <div class="mb-3">
        <label for="email" class="form-label text-white-50 small">University / Register Email</label>
        <div class="input-group">
            <span class="input-group-text bg-dark border-secondary text-secondary"><i class="fas fa-envelope"></i></span>
            <input type="email" class="form-control" id="email" name="email" value="<?= Helper::esc($email ?? '') ?>" placeholder="name@smarthub.edu" required autofocus>
        </div>
    </div>

    <div class="mb-4">
        <div class="d-flex justify-content-between mb-1">
            <label for="password" class="form-label text-white-50 small mb-0">Password</label>
        </div>
        <div class="input-group">
            <span class="input-group-text bg-dark border-secondary text-secondary"><i class="fas fa-lock"></i></span>
            <input type="password" class="form-control" id="password" name="password" placeholder="••••••••" required>
        </div>
    </div>

    <button type="submit" class="btn btn-primary w-100 py-2.5 fw-bold shadow-sm mb-3">
        <i class="fas fa-sign-in-alt me-2"></i> Log In
    </button>

    <div class="text-center mt-3">
        <p class="text-white-50 small mb-0">Don't have an account yet?</p>
        <a href="<?= Helper::url('/register') ?>" class="text-success text-decoration-none small fw-semibold">
            Create an Account <i class="fas fa-arrow-right ms-1"></i>
        </a>
    </div>
</form>
