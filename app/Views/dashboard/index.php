<?= $this->extend('templates/header') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Welcome Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h3 class="mb-2">
                                <i class="bi bi-speedometer2 text-primary me-2"></i>
                                Selamat Datang, <?= $user['nama'] ?>!
                            </h3>
                            <p class="text-muted mb-0">
                                <i class="bi bi-building me-1"></i> <?= $user['unit_kerja'] ?> | 
                                <i class="bi bi-person-badge me-1 ms-2"></i> Role: <?= strtoupper($user['role']) ?>
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="badge bg-primary p-2">
                                <i class="bi bi-calendar-check me-1"></i>
                                <?= date('d F Y') ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="stat-card">
                <div class="stat-icon text-primary">
                    <i class="bi bi-lightning-charge-fill"></i>
                </div>
                <div class="stat-number"><?= $stats['total_data'] ?></div>
                <div class="stat-label">Total Pengukuran</div>
                <div class="mt-2">
                    <small class="text-muted">
                        <i class="bi bi-arrow-up-circle text-success me-1"></i>
                        Total semua data KWH
                    </small>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-3">
            <div class="stat-card">
                <div class="stat-icon text-success">
                    <i class="bi bi-person-fill"></i>
                </div>
                <div class="stat-number"><?= $stats['total_users'] ?></div>
                <div class="stat-label">Total User</div>
                <div class="mt-2">
                    <small class="text-muted">
                        <i class="bi bi-people-fill text-info me-1"></i>
                        User terdaftar di sistem
                    </small>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-3">
            <div class="stat-card">
                <div class="stat-icon text-warning">
                    <i class="bi bi-file-earmark-text-fill"></i>
                </div>
                <div class="stat-number"><?= $stats['my_data'] ?></div>
                <div class="stat-label">Pengukuran Saya</div>
                <div class="mt-2">
                    <small class="text-muted">
                        <i class="bi bi-person-check text-warning me-1"></i>
                        Data yang Anda input
                    </small>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-lightning-charge me-2"></i> Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 col-6 mb-3">
                            <a href="<?= base_url('kwh') ?>" class="btn btn-primary w-100 py-3">
                                <i class="bi bi-plus-circle-fill me-2" style="font-size: 1.2rem;"></i>
                                <div class="mt-2">Input Data Baru</div>
                            </a>
                        </div>
                        
                        <div class="col-md-3 col-6 mb-3">
                            <a href="<?= base_url('kwh#riwayat') ?>" class="btn btn-success w-100 py-3">
                                <i class="bi bi-clock-history me-2" style="font-size: 1.2rem;"></i>
                                <div class="mt-2">Lihat Riwayat</div>
                            </a>
                        </div>
                        
                        <div class="col-md-3 col-6 mb-3">
                            <a href="<?= base_url('export/pdf') ?>" target="_blank" class="btn btn-danger w-100 py-3">
                                <i class="bi bi-file-pdf me-2" style="font-size: 1.2rem;"></i>
                                <div class="mt-2">Export PDF</div>
                            </a>
                        </div>
                        
                        <div class="col-md-3 col-6 mb-3">
                            <a href="<?= base_url('auth/profile') ?>" class="btn btn-info w-100 py-3">
                                <i class="bi bi-person-circle me-2" style="font-size: 1.2rem;"></i>
                                <div class="mt-2">Edit Profile</div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Activities -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-activity me-2"></i> Aktivitas Terbaru
                    </h5>
                    <a href="<?= base_url('kwh') ?>" class="btn btn-sm btn-outline-primary">
                        Lihat Semua
                    </a>
                </div>
                <div class="card-body">
                    <?php if (!empty($recent_data)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Waktu</th>
                                        <th>Lokasi</th>
                                        <th>Arus</th>
                                        <th>Error</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_data as $data): ?>
                                    <tr>
                                        <td><?= date('H:i', strtotime($data['created_at'])) ?></td>
                                        <td><?= substr($data['keterangan'], 0, 30) ?><?= strlen($data['keterangan']) > 30 ? '...' : '' ?></td>
                                        <td><?= number_format($data['arus'], 2) ?> A</td>
                                        <td>
                                            <?php if (isset($data['error_percent'])): ?>
                                                <span class="badge bg-<?= 
                                                    abs($data['error_percent']) <= 2 ? 'success' : 
                                                    (abs($data['error_percent']) <= 5 ? 'warning' : 'danger')
                                                ?>">
                                                    <?= number_format($data['error_percent'], 2) ?>%
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (isset($data['status_final'])): ?>
                                                <?php
                                                if ($data['status_final'] === 'BAIK') {
                                                    echo '<span class="badge bg-success">BAIK</span>';
                                                } elseif ($data['status_final'] === 'TIDAK STABIL') {
                                                    echo '<span class="badge bg-warning">TIDAK STABIL</span>';
                                                } elseif ($data['status_final'] === 'BURUK') {
                                                    echo '<span class="badge bg-danger">BURUK</span>';
                                                }
                                                ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="<?= base_url('export/pdf/' . $data['id']) ?>" 
                                               class="btn btn-sm btn-outline-danger"
                                               target="_blank"
                                               title="Export PDF">
                                                <i class="bi bi-file-pdf"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                            Belum ada data pengukuran
                            <div class="mt-3">
                                <a href="<?= base_url('kwh') ?>" class="btn btn-primary">
                                    <i class="bi bi-plus-circle me-2"></i> Input Data Pertama
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- System Info -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-info-circle me-2"></i> Informasi Sistem
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between">
                            <span><i class="bi bi-calendar3 me-2 text-primary"></i> Versi Sistem</span>
                            <span class="badge bg-primary">v1.0.0</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span><i class="bi bi-database me-2 text-success"></i> Database</span>
                            <span>MySQL</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span><i class="bi bi-code-slash me-2 text-warning"></i> Framework</span>
                            <span>CodeIgniter 4</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span><i class="bi bi-shield-check me-2 text-danger"></i> Status</span>
                            <span class="badge bg-success">Online</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-bell me-2"></i> Notifikasi
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle-fill me-2"></i>
                        <strong>Update Sistem:</strong> Fitur login dan tracking user telah ditambahkan.
                    </div>
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        <strong>Sistem Aktif:</strong> Semua fungsi berjalan normal.
                    </div>
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <strong>Pengingat:</strong> Pastikan data yang diinput sudah valid.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.stat-card {
    background: white;
    border-radius: 10px;
    padding: 25px;
    text-align: center;
    box-shadow: 0 2px 15px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    border: 1px solid #e9ecef;
    height: 100%;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 20px rgba(0,0,0,0.12);
}

.stat-icon {
    font-size: 2.5rem;
    margin-bottom: 15px;
    opacity: 0.9;
}

.stat-number {
    font-size: 2.5rem;
    font-weight: bold;
    color: #0054a6;
    margin-bottom: 5px;
}

.stat-label {
    color: #6c757d;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.btn-action {
    border-radius: 10px;
    padding: 20px 15px;
    font-weight: 500;
    transition: all 0.3s;
}

.btn-action:hover {
    transform: scale(1.05);
}

.list-group-item {
    border: none;
    padding: 12px 0;
}
</style>
<?= $this->endSection() ?>