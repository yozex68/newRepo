<?php
use App\Helpers\Helper;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= Helper::esc($title ?? 'Authenticate | SmartHUB') ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <!-- Custom Premium Styles -->
    <link href="<?= Helper::asset('css/style.css?v=1.3') ?>" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 50%, #0d9488 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 2rem 0;
        }
        .auth-card {
            border: 1px solid rgba(255,255,255,0.1);
            background: rgba(15, 23, 42, 0.8) !important;
            backdrop-filter: blur(15px);
            border-radius: 1.5rem;
            color: #ffffff;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.4), 0 10px 10px -5px rgba(0, 0, 0, 0.4);
            max-width: 480px;
            width: 100%;
        }
        .auth-card .form-control {
            background-color: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            color: #ffffff;
        }
        .auth-card .form-control:focus {
            background-color: rgba(255,255,255,0.1);
            border-color: var(--color-green);
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.25);
        }
        .auth-card .form-select {
            background-color: rgba(15, 23, 42, 0.95);
            border: 1px solid rgba(255,255,255,0.1);
            color: #ffffff;
        }
    </style>
</head>
<body>

    <!-- Server-side Session Flash Notifications to JS Toaster -->
    <?php if ($session->hasFlash('success')): ?>
        <div class="server-flash d-none" data-type="success" data-message="<?= Helper::esc($session->getFlash('success')) ?>"></div>
    <?php endif; ?>
    <?php if ($session->hasFlash('error')): ?>
        <div class="server-flash d-none" data-type="error" data-message="<?= Helper::esc($session->getFlash('error')) ?>"></div>
    <?php endif; ?>

    <div class="container d-flex justify-content-center">
        <div class="auth-card p-4 p-md-5 animate-fade-in shadow-lg">
            <?= $content ?>
        </div>
    </div>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom Premium Script -->
    <script src="<?= Helper::asset('js/app.js') ?>"></script>
</body>
</html>
