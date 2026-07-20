<?php
use App\Helpers\Helper;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= Helper::esc($title ?? 'SmartHUB Digital Library') ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <!-- Custom Premium Styles -->
    <link href="<?= Helper::asset('css/style.css?v=1.3') ?>" rel="stylesheet">
</head>
<body>

    <!-- Server-side Session Flash Notifications to JS Toaster -->
    <?php if ($session->hasFlash('success')): ?>
        <div class="server-flash d-none" data-type="success" data-message="<?= Helper::esc($session->getFlash('success')) ?>"></div>
    <?php endif; ?>
    <?php if ($session->hasFlash('error')): ?>
        <div class="server-flash d-none" data-type="error" data-message="<?= Helper::esc($session->getFlash('error')) ?>"></div>
    <?php endif; ?>
    <?php if ($session->hasFlash('warning')): ?>
        <div class="server-flash d-none" data-type="warning" data-message="<?= Helper::esc($session->getFlash('warning')) ?>"></div>
    <?php endif; ?>
    <?php if ($session->hasFlash('info')): ?>
        <div class="server-flash d-none" data-type="info" data-message="<?= Helper::esc($session->getFlash('info')) ?>"></div>
    <?php endif; ?>

    <div id="app-layout">
        
        <!-- Sidebar Navigation -->
        <aside id="sidebar">
            <div class="sidebar-header">
                <i class="fas fa-graduation-cap text-warning fs-3"></i>
                <a href="<?= Helper::url('/dashboard') ?>" class="sidebar-brand">SmartHUB</a>
            </div>
            
            <ul class="sidebar-menu">
                <?php $role = $session->get('user_role'); ?>
                
                <li class="menu-item <?= Helper::isActive('/dashboard') ?>">
                    <a href="<?= Helper::url('/dashboard') ?>">
                        <i class="fas fa-th-large"></i> <span>Dashboard</span>
                    </a>
                </li>
                
                <?php if ($role === 'admin'): ?>
                    <!-- Admin Specific Navigation -->
                    <li class="menu-item <?= Helper::isActive('/admin/faculties') ?>">
                        <a href="<?= Helper::url('/admin/faculties') ?>">
                            <i class="fas fa-university"></i> <span>Faculties</span>
                        </a>
                    </li>
                    <li class="menu-item <?= Helper::isActive('/admin/programmes') ?>">
                        <a href="<?= Helper::url('/admin/programmes') ?>">
                            <i class="fas fa-scroll"></i> <span>Programmes</span>
                        </a>
                    </li>
                    <li class="menu-item <?= Helper::isActive('/admin/courses') ?>">
                        <a href="<?= Helper::url('/admin/courses') ?>">
                            <i class="fas fa-book"></i> <span>Courses</span>
                        </a>
                    </li>
                    <li class="menu-item <?= Helper::isActive('/admin/materials') ?>">
                        <a href="<?= Helper::url('/admin/materials') ?>">
                            <i class="fas fa-folder-open"></i> <span>Upload Materials</span>
                        </a>
                    </li>
                    <li class="menu-item <?= Helper::isActive('/admin/users') ?>">
                        <a href="<?= Helper::url('/admin/users') ?>">
                            <i class="fas fa-users-cog"></i> <span>Manage Users</span>
                        </a>
                    </li>
                    <li class="menu-item <?= Helper::isActive('/admin/subscriptions') ?>">
                        <a href="<?= Helper::url('/admin/subscriptions') ?>">
                            <i class="fas fa-receipt"></i> <span>Subscriptions</span>
                        </a>
                    </li>
                    <li class="menu-item <?= Helper::isActive('/admin/reports') ?>">
                        <a href="<?= Helper::url('/admin/reports') ?>">
                            <i class="fas fa-chart-line"></i> <span>Reports & Analytics</span>
                        </a>
                    </li>
                    <li class="menu-item <?= Helper::isActive('/admin/backup') ?>">
                        <a href="<?= Helper::url('/admin/backup') ?>">
                            <i class="fas fa-database"></i> <span>Database Backup</span>
                        </a>
                    </li>
                <?php else: ?>
                    <!-- Student / Guest Navigation -->
                    <li class="menu-item <?= Helper::isActive('/courses') ?>">
                        <a href="<?= Helper::url('/courses') ?>">
                            <i class="fas fa-book-reader"></i> <span>My Programme</span>
                        </a>
                    </li>
                    <li class="menu-item <?= Helper::isActive('/subscribe') ?>">
                        <a href="<?= Helper::url('/subscribe') ?>">
                            <i class="fas fa-credit-card"></i> <span>Subscription Plans</span>
                        </a>
                    </li>
                <?php endif; ?>
                
                <li class="menu-item <?= Helper::isActive('/questions') ?>">
                    <a href="<?= Helper::url('/questions') ?>">
                        <i class="fas fa-question-circle"></i> <span>Q&A Help Desk</span>
                    </a>
                </li>
                
                <li class="menu-item <?= Helper::isActive('/profile') ?>">
                    <a href="<?= Helper::url('/profile') ?>">
                        <i class="fas fa-user-circle"></i> <span>My Profile</span>
                    </a>
                </li>
            </ul>
            
            <div class="sidebar-footer">
                <a href="<?= Helper::url('/logout') ?>" class="btn btn-outline-danger w-100 btn-sm">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </aside>

        <!-- Main Content Area Wrapper -->
        <div id="main-content">
            
            <!-- Top Header Navbar -->
            <header id="top-navbar">
                <button id="sidebar-toggle" class="btn btn-light d-lg-none">
                    <i class="fas fa-bars"></i>
                </button>
                
                <div class="d-none d-md-flex align-items-center gap-2">
                    <span class="badge rounded-pill bg-light border text-dark">
                        Academic Year: <?= date('Y') ?>
                    </span>
                    <span class="badge rounded-pill <?= Helper::roleBadge($session->get('user_role')) ?>">
                        Role: <?= ucfirst($session->get('user_role')) ?>
                    </span>
                </div>
                
                <div class="d-flex align-items-center gap-4">
                    <!-- Theme toggler -->
                    <div class="dark-mode-toggle">
                        <i class="fas fa-moon text-secondary"></i>
                    </div>
                    
                    <!-- User info -->
                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center gap-2 text-decoration-none text-reset dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; font-weight: 700;">
                                <?= strtoupper(substr($session->get('user_name', 'U'), 0, 1)) ?>
                            </div>
                            <span class="d-none d-sm-inline fw-semibold"><?= Helper::esc($session->get('user_name')) ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                            <li><a class="dropdown-item" href="<?= Helper::url('/profile') ?>"><i class="fas fa-user me-2"></i> Profile</a></li>
                            <li><a class="dropdown-item" href="<?= Helper::url('/subscribe') ?>"><i class="fas fa-credit-card me-2"></i> Subscription</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="<?= Helper::url('/logout') ?>"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                        </ul>
                    </div>
                </div>
            </header>

            <!-- Inner Page Content Injector -->
            <main class="content-wrapper">
                <div class="animate-fade-in">
                    <?= $content ?>
                </div>
            </main>
            
        </div>
    </div>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom Premium Script -->
    <script src="<?= Helper::asset('js/app.js') ?>"></script>
</body>
</html>
