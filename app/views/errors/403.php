<?php use App\Helpers\Helper; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>403 Forbidden | SmartHUB</title>
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
            color: #ef4444;
            line-height: 1;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-code mb-3">403</div>
        <h1 class="h3 fw-bold mb-3">Access Denied / Forbidden</h1>
        <p class="text-secondary mb-4">
            You do not have administrative clearance to access this resource, or your session CSRF validation has failed. 
        </p>
        <a href="<?= Helper::url('/dashboard') ?>" class="btn btn-primary px-4 rounded-pill">
            <i class="fas fa-arrow-left me-2"></i> Return to Dashboard
        </a>
    </div>
</body>
</html>
