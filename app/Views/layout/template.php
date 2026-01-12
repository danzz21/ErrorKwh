<?php
// Di bagian atas file, sebelum HTML
$isLoggedIn = session()->get('isLoggedIn');
$userRole = session()->get('role');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> - PLN KWH Calculator</title>
    
    <!-- Bootstrap 5 + Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <!-- Custom CSS -->
    <style>
        :root {
            --pln-blue: #0054a6;
            --pln-yellow: #ffd100;
            --pln-red: #e31837;
        }
        
        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            background: #f8f9fa;
            min-height: 100vh;
        }
        
        .navbar-pln {
            background: linear-gradient(135deg, var(--pln-blue) 0%, #003d7a 100%);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .navbar-brand {
            font-weight: bold;
            color: white !important;
        }
        
        .pln-logo {
            background: var(--pln-yellow);
            color: var(--pln-blue);
            padding: 5px 10px;
            border-radius: 5px;
            font-weight: bold;
        }
        
        .main-content {
            padding: 20px;
            min-height: calc(100vh - 56px);
        }
        
        .nav-link {
            color: #00e9ddff;
            padding: 12px 15px;
            border-radius: 5px;
            margin: 2px 10px;
            transition: all 0.2s;
        }
        
        .nav-link:hover {
            background: #e9ecef;
            color: var(--pln-blue);
        }
        
        .nav-link.active {
            background: var(--pln-blue);
            color: white;
        }
        
        .user-avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--pln-yellow);
        }
        
        .card {
            border-radius: 10px;
            border: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            margin-bottom: 20px;
        }
        
        .card-header {
            background: linear-gradient(135deg, var(--pln-blue) 0%, #004a8f 100%);
            color: white;
            border-radius: 10px 10px 0 0 !important;
            font-weight: 600;
        }
        
        .flash-messages {
            position: fixed;
            top: 70px;
            right: 20px;
            z-index: 9999;
            max-width: 400px;
        }
        
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: transform 0.2s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-icon {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: var(--pln-blue);
        }
        
        .stat-label {
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        /* Dashboard menu cards */
        .menu-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            height: 100%;
            border: 2px solid transparent;
            text-decoration: none;
            color: inherit;
            display: block;
        }
        
        .menu-card:hover {
            transform: translateY(-10px);
            border-color: var(--pln-yellow);
            box-shadow: 0 10px 20px rgba(0,0,0,0.12);
        }
        
        .menu-icon {
            font-size: 3rem;
            margin-bottom: 15px;
            color: var(--pln-blue);
        }
        
        .menu-title {
            font-weight: 600;
            font-size: 1.2rem;
            margin-bottom: 10px;
            color: var(--pln-blue);
        }
        
        .menu-desc {
            color: #6c757d;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
   <!-- Di bagian navbar, update menu: -->
<nav class="navbar navbar-expand-lg navbar-pln navbar-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?= base_url('dashboard') ?>">
            <span class="pln-logo me-2">PLN</span>
            KWH Error Calculator
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <!-- Left Menu -->
            <ul class="navbar-nav me-auto">
                <?php if (session()->get('isLoggedIn')): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= current_url() == base_url('dashboard') ? 'active' : '' ?>" 
                           href="<?= base_url('dashboard') ?>">
                            <i class="bi bi-speedometer2 me-1"></i> Dashboard
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link <?= strpos(current_url(), base_url('kwh')) !== false ? 'active' : '' ?>" 
                           href="<?= base_url('kwh') ?>">
                            <i class="bi bi-lightning-charge me-1"></i> KWH Calculator
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link <?= strpos(current_url(), base_url('tracking')) !== false ? 'active' : '' ?>" 
                           href="<?= base_url('tracking/leaflet') ?>">
                            <i class="bi bi-geo-alt me-1"></i> Live Tracking
                        </a>
                    </li>
                    
                    <!-- ADMIN MENU - Dropdown -->
                    <?php if (session()->get('role') === 'admin'): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-gear me-1"></i> Admin
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="<?= base_url('users') ?>">
                                    <i class="bi bi-people me-2"></i> User Management
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?= base_url('kwh/all') ?>">
                                    <i class="bi bi-database me-2"></i> All KWH Data
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?= base_url('tracking') ?>">
                                    <i class="bi bi-map me-2"></i> Tracking Management
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?= base_url('reports') ?>">
                                    <i class="bi bi-graph-up me-2"></i> Reports
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="<?= base_url('export/all') ?>">
                                    <i class="bi bi-file-excel me-2"></i> Export All Data
                                </a>
                            </li>
                        </ul>
                    </li>
                    <?php endif; ?>
                    
                    <!-- USER MENU -->
                    <li class="nav-item">
                        <a class="nav-link <?= current_url() == base_url('auth/profile') ? 'active' : '' ?>" 
                           href="<?= base_url('auth/profile') ?>">
                            <i class="bi bi-person-circle me-1"></i> Profile
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
            
            <!-- Right User Dropdown -->
            <ul class="navbar-nav ms-auto">
                <?php if (session()->get('isLoggedIn')): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                            <?php if (session()->get('foto')): ?>
                                <img src="<?= base_url('uploads/users/' . session()->get('foto')) ?>" 
                                     class="user-avatar me-2" 
                                     alt="User Photo">
                            <?php else: ?>
                                <div class="user-avatar me-2 bg-light d-flex align-items-center justify-content-center">
                                    <i class="bi bi-person-fill text-secondary"></i>
                                </div>
                            <?php endif; ?>
                            <div>
                                <div class="fw-bold"><?= session()->get('nama') ?></div>
                                <small class="text-light"><?= session()->get('role') ?> | 
                                    <?= session()->get('unit_kerja') ?>
                                </small>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="<?= base_url('auth/profile') ?>">
                                    <i class="bi bi-person-circle me-2"></i> My Profile
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?= base_url('kwh') ?>">
                                    <i class="bi bi-clock-history me-2"></i> My History
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <?php if (session()->get('role') === 'admin'): ?>
                                <li>
                                    <a class="dropdown-item" href="<?= base_url('settings') ?>">
                                        <i class="bi bi-sliders me-2"></i> System Settings
                                    </a>
                                </li>
                            <?php endif; ?>
                            <li>
                                <a class="dropdown-item text-danger" href="<?= base_url('auth/logout') ?>">
                                    <i class="bi bi-box-arrow-right me-2"></i> Logout
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
    
    <!-- Main Content -->
    <div class="main-content">
        <!-- Flash Messages -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show shadow" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show shadow" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <!-- Content akan diisi di sini -->
        <?= $this->renderSection('content') ?>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Auto dismiss alerts
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                setTimeout(() => bsAlert.close(), 5000);
            });
        }, 3000);
    </script>
    
    <?= $this->renderSection('scripts') ?>
</body>
</html>