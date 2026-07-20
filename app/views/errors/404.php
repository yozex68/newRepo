<?php use App\Helpers\Helper; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>404 Page Not Found | SmartHUB</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #0f172a;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            text-align: center;
        }
        .error-container {
            max-width: 500px;
            padding: 2rem;
        }
        .error-code {
            font-size: 8rem;
            font-weight: 800;
            color: #fbbf24;
            line-height: 1;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-code mb-3">404</div>
        <h1 class="h3 fw-bold mb-3">Resource Not Found</h1>
        <p class="text-secondary mb-4">
            The requested page does not exist, has been removed, or is temporarily unavailable.
        </p>
        <a href="<?= Helper::url('/') ?>" class="btn btn-warning px-4 text-white rounded-pill">
            <i class="fas fa-home me-2"></i> Go to Homepage
        </a>
    </div>
</body>
</html>
