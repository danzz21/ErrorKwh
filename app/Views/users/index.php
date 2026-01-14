<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-2">
                                <i class="bi bi-people-fill me-2"></i>
                                User Management
                            </h3>
                            <p class="text-muted mb-0">Kelola user dan akses sistem PLN KWH Calculator</p>
                        </div>
                        <a href="<?= base_url('auth/register') ?>" class="btn btn-primary">
                            <i class="bi bi-person-plus me-2"></i> Tambah User Baru
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success">
            <i class="bi bi-check-circle-fill me-2"></i>
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>
    
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>
    
    <!-- Stats -->
    <div class="row mb-4">
        <div class="col-md-3 col-6 mb-3">
            <div class="stat-card bg-primary text-white">
                <div class="stat-icon">
                    <i class="bi bi-people"></i>
                </div>
                <div class="stat-number"><?= count($users) ?></div>
                <div class="stat-label">Total User</div>
            </div>
        </div>
        
        <div class="col-md-3 col-6 mb-3">
            <div class="stat-card bg-success text-white">
                <div class="stat-icon">
                    <i class="bi bi-shield-check"></i>
                </div>
                <div class="stat-number">
                    <?= count(array_filter($users, function($user) { return $user['role'] === 'admin'; })) ?>
                </div>
                <div class="stat-label">Administrator</div>
            </div>
        </div>
        
        <div class="col-md-3 col-6 mb-3">
            <div class="stat-card bg-warning text-dark">
                <div class="stat-icon">
                    <i class="bi bi-lightning-charge"></i>
                </div>
                <div class="stat-number">
                    <?= count(array_filter($users, function($user) { 
                        return in_array($user['role'], ['pln_metrologi', 'pln_teknis']); 
                    })) ?>
                </div>
                <div class="stat-label">Staff Teknis</div>
            </div>
        </div>
        
        <div class="col-md-3 col-6 mb-3">
            <div class="stat-card bg-info text-white">
                <div class="stat-icon">
                    <i class="bi bi-eye"></i>
                </div>
                <div class="stat-number">
                    <?= count(array_filter($users, function($user) { return $user['role'] === 'pln_operasional'; })) ?>
                </div>
                <div class="stat-label">View Only</div>
            </div>
        </div>
    </div>
    
    <!-- Users Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-list-ul me-2"></i> Daftar User
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="50">#</th>
                                    <th>User</th>
                                    <th>Kontak</th>
                                    <th>Role</th>
                                    <th>Unit Kerja</th>
                                    <th>Status</th>
                                    <th>Terakhir Login</th>
                                    <th width="150">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($users)): ?>
                                    <?php $no = 1; ?>
                                    <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <?php if ($user['foto']): ?>
                                                    <img src="<?= base_url('uploads/users/' . $user['foto']) ?>" 
                                                         class="rounded-circle me-3" 
                                                         style="width: 40px; height: 40px; object-fit: cover;"
                                                         alt="User Photo">
                                                <?php else: ?>
                                                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-3" 
                                                         style="width: 40px; height: 40px;">
                                                        <i class="bi bi-person-fill text-secondary"></i>
                                                    </div>
                                                <?php endif; ?>
                                                <div>
                                                    <div class="fw-bold"><?= $user['nama'] ?></div>
                                                    <small class="text-muted">NIP: <?= $user['nip'] ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div><?= $user['email'] ?></div>
                                            <small class="text-muted"><?= $user['telepon'] ?></small>
                                        </td>
                                        <td>
                                            <?php
                                            $roleBadge = '';
                                            switch ($user['role']) {
                                                case 'admin':
                                                    $roleBadge = 'bg-danger';
                                                    $roleText = 'ADMIN';
                                                    break;
                                                case 'pln_metrologi':
                                                    $roleBadge = 'bg-warning';
                                                    $roleText = 'METROLOGI';
                                                    break;
                                                case 'pln_teknis':
                                                    $roleBadge = 'bg-primary';
                                                    $roleText = 'TEKNIS';
                                                    break;
                                                case 'pln_operasional':
                                                    $roleBadge = 'bg-info';
                                                    $roleText = 'OPERASIONAL';
                                                    break;
                                                default:
                                                    $roleBadge = 'bg-secondary';
                                                    $roleText = $user['role'];
                                            }
                                            ?>
                                            <span class="badge <?= $roleBadge ?>"><?= $roleText ?></span>
                                        </td>
                                        <td>
                                            <div><?= $user['unit_kerja'] ?></div>
                                            <small class="text-muted"><?= $user['lokasi_pln'] ?></small>
                                        </td>
                                        <td>
                                            <?php if ($user['is_active']): ?>
                                                <span class="badge bg-success">Aktif</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Nonaktif</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($user['last_login']): ?>
                                                <?= date('d/m/Y H:i', strtotime($user['last_login'])) ?>
                                            <?php else: ?>
                                                <span class="text-muted">Belum login</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="<?= base_url('users/edit/' . $user['id']) ?>" 
                                                   class="btn btn-outline-primary"
                                                   title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                
                                                <?php if ($user['id'] != session()->get('user_id')): ?>
                                                <a href="<?= base_url('users/delete/' . $user['id']) ?>" 
                                                   class="btn btn-outline-danger"
                                                   onclick="return confirm('Hapus user <?= $user['nama'] ?>?')"
                                                   title="Hapus">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                                <?php else: ?>
                                                <button class="btn btn-outline-secondary" disabled title="Tidak bisa hapus akun sendiri">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center py-4 text-muted">
                                            <i class="bi bi-people fs-1 d-block mb-3"></i>
                                            Belum ada user terdaftar
                                            <div class="mt-3">
                                                <a href="<?= base_url('auth/register') ?>" class="btn btn-primary">
                                                    <i class="bi bi-person-plus me-2"></i> Tambah User Pertama
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <small class="text-muted">
                        <i class="bi bi-info-circle me-1"></i>
                        Total <?= count($users) ?> user terdaftar dalam sistem
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.stat-card {
    border-radius: 10px;
    padding: 20px;
    text-align: center;
    height: 100%;
}

.stat-card .stat-icon {
    font-size: 2.5rem;
    margin-bottom: 15px;
}

.stat-card .stat-number {
    font-size: 2.5rem;
    font-weight: bold;
    margin-bottom: 5px;
}

.stat-card .stat-label {
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.table th {
    font-weight: 600;
    background: #f8f9fa;
}

.table td {
    vertical-align: middle;
}
</style>
<?= $this->endSection() ?>