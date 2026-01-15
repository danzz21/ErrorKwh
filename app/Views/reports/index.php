<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0">
                <i class="bi bi-bar-chart text-primary me-2"></i>
                Reports Dashboard
            </h2>
            <p class="text-muted mb-0">
                Generate and view various reports
            </p>
        </div>
        <div>
            <button class="btn btn-success" onclick="printReport()">
                <i class="bi bi-printer me-2"></i>Print Current Report
            </button>
        </div>
    </div>
    
    <!-- Stats Cards -->
    <?php if ($user_role === 'admin'): ?>
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card border-start border-primary border-4 shadow h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Total KWH Data</h6>
                            <h3 class="mb-0"><?= number_format($total_kwh ?? 0) ?></h3>
                        </div>
                        <div class="bg-primary rounded-circle p-3">
                            <i class="bi bi-lightning-charge text-white fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-3">
            <div class="card border-start border-success border-4 shadow h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Total Users</h6>
                            <h3 class="mb-0"><?= number_format($total_users ?? 0) ?></h3>
                        </div>
                        <div class="bg-success rounded-circle p-3">
                            <i class="bi bi-people text-white fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-3">
            <div class="card border-start border-info border-4 shadow h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Total Tracks</h6>
                            <h3 class="mb-0"><?= number_format($total_tracks ?? 0) ?></h3>
                        </div>
                        <div class="bg-info rounded-circle p-3">
                            <i class="bi bi-geo-alt text-white fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Report Types -->
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="bi bi-lightning-charge me-2"></i>KWH Reports</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted">Generate reports for KWH measurement data</p>
                    
                    <form action="<?= base_url('reports/kwh') ?>" method="get" class="mb-4">
                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Start Date</label>
                                <input type="date" name="start_date" class="form-control" 
                                       value="<?= $start_date ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">End Date</label>
                                <input type="date" name="end_date" class="form-control" 
                                       value="<?= $end_date ?>">
                            </div>
                        </div>
                        
                        <?php if ($user_role === 'admin'): ?>
                        <div class="mb-3">
                            <label class="form-label">Filter by User (Optional)</label>
                            <select name="user_id" class="form-select">
                                <option value="">All Users</option>
                                <?php if (isset($users) && is_array($users)): ?>
                                    <?php foreach ($users as $user): ?>
                                        <option value="<?= $user['id'] ?>">
                                            <?= $user['nama'] ?> (<?= $user['nip'] ?>)
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <?php endif; ?>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search me-2"></i>View Report
                            </button>
                            <a href="<?= base_url('reports/export?type=kwh&start_date=' . $start_date . '&end_date=' . $end_date) ?>" 
                               class="btn btn-success">
                                <i class="bi bi-file-excel me-2"></i>Export to Excel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0"><i class="bi bi-geo-alt me-2"></i>Tracking Reports</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted">Generate reports for location tracking data</p>
                    
                    <form action="<?= base_url('reports/tracking') ?>" method="get" class="mb-4">
                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Start Date</label>
                                <input type="date" name="start_date" class="form-control" 
                                       value="<?= $start_date ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">End Date</label>
                                <input type="date" name="end_date" class="form-control" 
                                       value="<?= $end_date ?>">
                            </div>
                        </div>
                        
                        <?php if ($user_role === 'admin'): ?>
                        <div class="mb-3">
                            <label class="form-label">Filter by User (Optional)</label>
                            <select name="user_id" class="form-select">
                                <option value="">All Users</option>
                                <?php if (isset($users) && is_array($users)): ?>
                                    <?php foreach ($users as $user): ?>
                                        <option value="<?= $user['id'] ?>">
                                            <?= $user['nama'] ?> (<?= $user['nip'] ?>)
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <?php endif; ?>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search me-2"></i>View Report
                            </button>
                            <a href="<?= base_url('reports/export?type=tracking&start_date=' . $start_date . '&end_date=' . $end_date) ?>" 
                               class="btn btn-success">
                                <i class="bi bi-file-excel me-2"></i>Export to Excel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Reports -->
    <div class="card shadow">
        <div class="card-header bg-info text-white">
            <h6 class="mb-0"><i class="bi bi-clock-history me-2"></i>Quick Reports</h6>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <a href="<?= base_url('reports/kwh?start_date=' . date('Y-m-d') . '&end_date=' . date('Y-m-d')) ?>" 
                       class="btn btn-outline-primary w-100">
                        <i class="bi bi-calendar-day me-2"></i>Today's KWH
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="<?= base_url('reports/tracking?start_date=' . date('Y-m-d') . '&end_date=' . date('Y-m-d')) ?>" 
                       class="btn btn-outline-success w-100">
                        <i class="bi bi-calendar-day me-2"></i>Today's Tracking
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="<?= base_url('reports/kwh?start_date=' . date('Y-m-01') . '&end_date=' . date('Y-m-t')) ?>" 
                       class="btn btn-outline-primary w-100">
                        <i class="bi bi-calendar-month me-2"></i>This Month KWH
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="<?= base_url('reports/tracking?start_date=' . date('Y-m-01') . '&end_date=' . date('Y-m-t')) ?>" 
                       class="btn btn-outline-success w-100">
                        <i class="bi bi-calendar-month me-2"></i>This Month Tracking
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function printReport() {
    window.print();
}
</script>

<style>
@media print {
    .container-fluid {
        padding: 0 !important;
    }
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    .btn, .form-control, .form-select {
        display: none !important;
    }
    .card-header {
        background-color: #fff !important;
        color: #000 !important;
        border-bottom: 2px solid #000 !important;
    }
}
</style>
<?= $this->endSection() ?>