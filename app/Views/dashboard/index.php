<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0">
                <i class="bi bi-speedometer2 text-primary me-2"></i>
                Dashboard
            </h2>
            <p class="text-muted mb-0">
                Selamat datang, <strong><?= $user_nama ?></strong> | Role: <?= strtoupper($user_role) ?>
            </p>
        </div>
        <div>
            <span class="badge bg-primary"><?= date('l, d F Y') ?></span>
            <span class="badge bg-success"><?= date('H:i:s') ?></span>
        </div>
    </div>
    
    <?php if ($user_role === 'admin'): ?>
    <!-- ADMIN DASHBOARD -->
    <div class="row mb-4">
        <!-- Stat Cards -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-primary border-4 shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-primary text-uppercase mb-1">
                                Total Users
                            </div>
                            <div class="h5 mb-0 fw-bold text-gray-800">
                                <?= number_format($total_users) ?>
                            </div>
                            <div class="mt-2 mb-0 text-muted">
                                <span class="text-success me-2">
                                    <i class="bi bi-person-check me-1"></i>
                                    <?= number_format($active_users) ?> aktif
                                </span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-people fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-success border-4 shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-success text-uppercase mb-1">
                                KWH Data
                            </div>
                            <div class="h5 mb-0 fw-bold text-gray-800">
                                <?= number_format($total_kwh_data) ?>
                            </div>
                            <div class="mt-2 mb-0 text-muted">
                                <span class="text-success me-2">
                                    <i class="bi bi-calendar-day me-1"></i>
                                    <?= number_format($today_kwh_data) ?> hari ini
                                </span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-lightning-charge fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-info border-4 shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-info text-uppercase mb-1">
                                Online Now
                            </div>
                            <div class="h5 mb-0 fw-bold text-gray-800">
                                <?= number_format($online_users) ?>
                            </div>
                            <div class="mt-2 mb-0 text-muted">
                                <span class="text-success me-2">
                                    <i class="bi bi-wifi me-1"></i>
                                    Live Tracking
                                </span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-geo-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-warning border-4 shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-warning text-uppercase mb-1">
                                User Distribution
                            </div>
                            <div class="h5 mb-0 fw-bold text-gray-800">
                                <?= count($user_stats) ?> Roles
                            </div>
                            <div class="mt-2 mb-0 text-muted">
                                <?php foreach($user_stats as $stat): ?>
                                    <span class="badge bg-secondary me-1">
                                        <?= $stat['role'] ?>: <?= $stat['count'] ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-pie-chart fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="bi bi-lightning-charge me-2"></i> Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <a href="<?= base_url('users') ?>" class="btn btn-outline-primary w-100">
                                <i class="bi bi-people me-2"></i> Manage Users
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="<?= base_url('kwh/all') ?>" class="btn btn-outline-success w-100">
                                <i class="bi bi-database me-2"></i> View All Data
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="<?= base_url('tracking') ?>" class="btn btn-outline-info w-100">
                                <i class="bi bi-geo-alt me-2"></i> Live Tracking
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="<?= base_url('export/all') ?>" class="btn btn-outline-warning w-100">
                                <i class="bi bi-file-excel me-2"></i> Export Data
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Two Column Layout -->
    <div class="row">
        <!-- Recent KWH Activities -->
        <div class="col-xl-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="bi bi-clock-history me-2"></i> Recent KWH Activities</h6>
                    <a href="<?= base_url('kwh/all') ?>" class="btn btn-sm btn-light">View All</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>User</th>
                                    <th>Location</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($recent_kwh)): ?>
                                    <?php foreach($recent_kwh as $activity): ?>
                                    <tr>
                                        <td><?= date('H:i', strtotime($activity['created_at'])) ?></td>
                                        <td>
                                            <small><?= $activity['nama'] ?></small>
                                        </td>
                                        <td>
                                            <small class="text-muted"><?= substr($activity['keterangan'], 0, 20) ?>...</small>
                                        </td>
                                        <td>
                                            <?php if ($activity['status_final'] === 'BAIK'): ?>
                                                <span class="badge bg-success">BAIK</span>
                                            <?php elseif ($activity['status_final'] === 'TIDAK STABIL'): ?>
                                                <span class="badge bg-warning">TIDAK STABIL</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">BURUK</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="<?= base_url('kwh/view/' . $activity['id']) ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-3">
                                            <i class="bi bi-inbox fs-4 d-block mb-2"></i>
                                            No recent activities
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recent Locations -->
        <div class="col-xl-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="bi bi-geo-alt me-2"></i> Recent Locations</h6>
                    <a href="<?= base_url('tracking') ?>" class="btn btn-sm btn-light">View Map</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>User</th>
                                    <th>Location</th>
                                    <th>Accuracy</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($recent_locations)): ?>
                                    <?php foreach($recent_locations as $location): ?>
                                    <tr>
                                        <td><?= date('H:i', strtotime($location['timestamp'])) ?></td>
                                        <td>
                                            <small><?= $location['nama'] ?></small><br>
                                            <small class="text-muted"><?= $location['nip'] ?></small>
                                        </td>
                                        <td>
                                            <?php if ($location['address']): ?>
                                                <small><?= substr($location['address'], 0, 30) ?>...</small>
                                            <?php else: ?>
                                                <small class="text-muted">
                                                    <?= number_format($location['latitude'], 4) ?>, 
                                                    <?= number_format($location['longitude'], 4) ?>
                                                </small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($location['accuracy']): ?>
                                                <span class="badge bg-info"><?= round($location['accuracy']) ?>m</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">N/A</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="<?= base_url('tracking/history/' . $location['user_id']) ?>" 
                                               class="btn btn-sm btn-outline-info">
                                                <i class="bi bi-history"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-3">
                                            <i class="bi bi-geo fs-4 d-block mb-2"></i>
                                            No location data
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php else: ?>
    <!-- USER DASHBOARD -->
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card shadow h-100">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="bi bi-person-circle me-2"></i> My Stats</h6>
                </div>
                <div class="card-body text-center">
                    <div class="mb-4">
                        <i class="bi bi-lightning-charge text-warning" style="font-size: 3rem;"></i>
                        <h2 class="mt-3"><?= number_format($my_kwh_data) ?></h2>
                        <p class="text-muted">Total KWH Measurements</p>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="border rounded p-3">
                                <h4 class="text-success"><?= number_format($today_kwh_data) ?></h4>
                                <small class="text-muted">Today</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-3">
                                <h4 class="text-info">
                                    <?= $last_location ? 'Online' : 'Offline' ?>
                                </h4>
                                <small class="text-muted">Status</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-8 mb-4">
            <div class="card shadow h-100">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="bi bi-clock-history me-2"></i> My Recent Activities</h6>
                    <a href="<?= base_url('kwh') ?>" class="btn btn-sm btn-light">View All</a>
                </div>
                <div class="card-body">
                    <?php if (!empty($recent_activities)): ?>
                        <div class="list-group">
                            <?php foreach($recent_activities as $activity): ?>
                            <a href="#" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">
                                        <i class="bi bi-geo-alt me-2"></i>
                                        <?= $activity['keterangan'] ?>
                                    </h6>
                                    <small><?= date('H:i', strtotime($activity['created_at'])) ?></small>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        Arus: <?= $activity['arus'] ?>A | 
                                        Error: <?= number_format($activity['error_percent'], 2) ?>%
                                    </small>
                                    <?php if ($activity['status_final'] === 'BAIK'): ?>
                                        <span class="badge bg-success">BAIK</span>
                                    <?php elseif ($activity['status_final'] === 'TIDAK STABIL'): ?>
                                        <span class="badge bg-warning">TIDAK STABIL</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">BURUK</span>
                                    <?php endif; ?>
                                </div>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center text-muted py-5">
                            <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                            <p>No recent activities</p>
                            <a href="<?= base_url('kwh') ?>" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i> Start Measurement
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="bi bi-geo-alt me-2"></i> Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <a href="<?= base_url('kwh') ?>" class="btn btn-primary w-100">
                                <i class="bi bi-lightning-charge me-2"></i> New Measurement
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="<?= base_url('kwh#riwayat') ?>" class="btn btn-success w-100">
                                <i class="bi bi-clock-history me-2"></i> My History
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="<?= base_url('tracking/mobile') ?>" class="btn btn-info w-100">
                                <i class="bi bi-phone me-2"></i> Start Tracking
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="<?= base_url('auth/profile') ?>" class="btn btn-warning w-100">
                                <i class="bi bi-person-circle me-2"></i> My Profile
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Auto Refresh Script -->
<script>
    // Auto refresh dashboard every 30 seconds
    setTimeout(() => {
        window.location.reload();
    }, 30000);
    
    // Update time every second
    function updateTime() {
        const now = new Date();
        const timeElement = document.querySelector('.badge.bg-success');
        if (timeElement) {
            const timeString = now.toLocaleTimeString('id-ID');
            timeElement.textContent = timeString;
        }
    }
    
    setInterval(updateTime, 1000);
</script>
<?= $this->endSection() ?>