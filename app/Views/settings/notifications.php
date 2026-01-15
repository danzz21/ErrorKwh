<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-bell me-2"></i>Notification Settings</h2>
        <a href="<?= base_url('settings') ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to Settings
        </a>
    </div>
    
    <div class="card shadow">
        <div class="card-body">
            <p class="text-muted">Notification settings coming soon...</p>
        </div>
    </div>
</div>
<?= $this->endSection() ?>  