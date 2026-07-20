<?php
use App\Helpers\Helper;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= Helper::esc($title ?? 'Welcome | SmartHUB') ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <!-- Custom Premium Styles -->
    <link href="<?= Helper::asset('css/style.css?v=1.3') ?>" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100">

    <!-- Server-side Session Flash Notifications to JS Toaster -->
    <?php if ($session->hasFlash('success')): ?>
        <div class="server-flash d-none" data-type="success" data-message="<?= Helper::esc($session->getFlash('success')) ?>"></div>
    <?php endif; ?>
    <?php if ($session->hasFlash('error')): ?>
        <div class="server-flash d-none" data-type="error" data-message="<?= Helper::esc($session->getFlash('error')) ?>"></div>
    <?php endif; ?>

    <!-- Navigation Header -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark py-3">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2 fw-bold" href="<?= Helper::url('/') ?>">
                <i class="fas fa-graduation-cap text-warning fs-3"></i>
                <span>SmartHUB Digital Library</span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#guestNav" aria-controls="guestNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="guestNav">
                <ul class="navbar-nav ms-auto align-items-center gap-2">
                    <li class="nav-item">
                        <a class="nav-link <?= Helper::isActive('/') ?>" href="<?= Helper::url('/') ?>">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= Helper::isActive('/announcements') ?>" href="<?= Helper::url('/announcements') ?>">Announcements</a>
                    </li>
                    
                    <?php if ($session->get('user_id')): ?>
                        <li class="nav-item ms-2">
                            <a class="btn btn-warning btn-sm px-3 rounded-pill text-white" href="<?= Helper::url('/dashboard') ?>">
                                <i class="fas fa-desktop me-1"></i> Dashboard
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item ms-2">
                            <a class="btn btn-outline-light btn-sm px-3 rounded-pill" href="<?= Helper::url('/login') ?>">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-success btn-sm px-3 rounded-pill" href="<?= Helper::url('/register') ?>">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content Injection -->
    <main class="flex-grow-1">
        <?= $content ?>
    </main>

    <!-- Public Footer -->
    <footer class="bg-dark text-white py-4 mt-auto border-top border-secondary">
        <div class="container text-center">
            <p class="mb-1">&copy; <?= date('Y') ?> SmartHUB Academic Digital Library. All Rights Reserved.</p>
            <small class="text-muted">Designed for BIT 2 Internet and Web Development Assignment.</small>
        </div>
    </footer>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom Premium Script -->
    <script src="<?= Helper::asset('js/app.js') ?>"></script>
</body>
</html>
