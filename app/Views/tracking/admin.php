<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-geo-alt me-2"></i>Live Tracking</h2>
        <?php if ($user_role === 'admin'): ?>
            <a href="<?= base_url('tracking/admin') ?>" class="btn btn-primary">
                <i class="bi bi-shield-check me-2"></i>Admin View
            </a>
        <?php endif; ?>
    </div>
    
    <div class="card shadow">
        <div class="card-body text-center py-5">
            <i class="bi bi-geo-alt-fill text-primary" style="font-size: 4rem;"></i>
            <h3 class="mt-3">Live Tracking System</h3>
            <p class="text-muted">Track real-time locations of PLN personnel</p>
            
            <div class="row mt-4 justify-content-center">
                <div class="col-md-4 mb-3">
                    <a href="<?= base_url('tracking/mobile') ?>" class="btn btn-primary btn-lg w-100">
                        <i class="bi bi-phone me-2"></i>Mobile Tracker
                    </a>
                </div>
                <div class="col-md-4 mb-3">
                    <a href="<?= base_url('tracking/history') ?>" class="btn btn-success btn-lg w-100">
                        <i class="bi bi-clock-history me-2"></i>My History
                    </a>
                </div>
                <div class="col-md-4 mb-3">
                    <a href="<?= base_url('tracking/map') ?>" class="btn btn-info btn-lg w-100">
                        <i class="bi bi-map me-2"></i>View Map
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>