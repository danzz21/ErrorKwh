<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0">
                <i class="bi bi-gear text-primary me-2"></i>
                Settings
            </h2>
            <p class="text-muted mb-0">
                Manage your account settings and preferences
            </p>
        </div>
        <div>
            <a href="<?= base_url('dashboard') ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
            </a>
        </div>
    </div>
    
    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <div class="row">
        <!-- Left Sidebar -->
        <div class="col-md-3 mb-4">
            <div class="card shadow">
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <a href="<?= base_url('settings') ?>" 
                           class="list-group-item list-group-item-action active">
                            <i class="bi bi-person-circle me-2"></i>Profile Settings
                        </a>
                        <a href="<?= base_url('settings/notifications') ?>" 
                           class="list-group-item list-group-item-action">
                            <i class="bi bi-bell me-2"></i>Notifications
                        </a>
                        <a href="<?= base_url('settings/appearance') ?>" 
                           class="list-group-item list-group-item-action">
                            <i class="bi bi-palette me-2"></i>Appearance
                        </a>
                        <a href="<?= base_url('auth/profile') ?>" 
                           class="list-group-item list-group-item-action">
                            <i class="bi bi-eye me-2"></i>Public Profile
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Account Info -->
            <div class="card shadow mt-4">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center" 
                             style="width: 80px; height: 80px;">
                            <i class="bi bi-person-fill text-white fs-3"></i>
                        </div>
                    </div>
                    <h5><?= $user['nama'] ?></h5>
                    <p class="text-muted mb-1"><?= $user['role'] ?></p>
                    <small class="text-muted">Member since <?= date('M Y', strtotime($user['created_at'])) ?></small>
                </div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-md-9">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="bi bi-person-circle me-2"></i>Profile Settings</h6>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('settings/update') ?>" method="post">
                        <?= csrf_field() ?>
                        
                        <div class="row">
                            <!-- Personal Information -->
                            <div class="col-md-6 mb-3">
                                <h6 class="border-bottom pb-2 mb-3">Personal Information</h6>
                                
                                <div class="mb-3">
                                    <label class="form-label">Full Name</label>
                                    <input type="text" name="nama" class="form-control" 
                                           value="<?= old('nama', $user['nama']) ?>" required>
                                    <?php if (isset($validation) && $validation->hasError('nama')): ?>
                                        <div class="text-danger small"><?= $validation->getError('nama') ?></div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Email Address</label>
                                    <input type="email" name="email" class="form-control" 
                                           value="<?= old('email', $user['email']) ?>" required>
                                    <?php if (isset($validation) && $validation->hasError('email')): ?>
                                        <div class="text-danger small"><?= $validation->getError('email') ?></div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Phone Number</label>
                                    <input type="text" name="telepon" class="form-control" 
                                           value="<?= old('telepon', $user['telepon']) ?>">
                                </div>
                            </div>
                            
                            <!-- Work Information -->
                            <div class="col-md-6 mb-3">
                                <h6 class="border-bottom pb-2 mb-3">Work Information</h6>
                                
                                <div class="mb-3">
                                    <label class="form-label">Unit Kerja</label>
                                    <input type="text" name="unit_kerja" class="form-control" 
                                           value="<?= old('unit_kerja', $user['unit_kerja']) ?>">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Lokasi PLN</label>
                                    <input type="text" name="lokasi_pln" class="form-control" 
                                           value="<?= old('lokasi_pln', $user['lokasi_pln']) ?>">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Role</label>
                                    <input type="text" class="form-control" 
                                           value="<?= $user['role'] ?>" disabled>
                                    <small class="text-muted">Role cannot be changed</small>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Password Change Section -->
                        <h6 class="border-bottom pb-2 mb-3 mt-4">Change Password</h6>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Current Password</label>
                                <input type="password" name="current_password" class="form-control">
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label class="form-label">New Password</label>
                                <input type="password" name="new_password" class="form-control">
                                <?php if (isset($validation) && $validation->hasError('new_password')): ?>
                                    <div class="text-danger small"><?= $validation->getError('new_password') ?></div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Confirm Password</label>
                                <input type="password" name="confirm_password" class="form-control">
                                <?php if (isset($validation) && $validation->hasError('confirm_password')): ?>
                                    <div class="text-danger small"><?= $validation->getError('confirm_password') ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            Leave password fields blank if you don't want to change your password.
                        </div>
                        
                        <div class="d-flex justify-content-between mt-4">
                            <a href="<?= base_url('dashboard') ?>" class="btn btn-secondary">
                                <i class="bi bi-x-circle me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-2"></i>Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Danger Zone -->
            <div class="card shadow border-danger mt-4">
                <div class="card-header bg-danger text-white">
                    <h6 class="mb-0"><i class="bi bi-exclamation-triangle me-2"></i>Danger Zone</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">Delete Account</h6>
                            <p class="text-muted mb-0">Once you delete your account, there is no going back.</p>
                        </div>
                        <button type="button" class="btn btn-outline-danger" 
                                onclick="confirmDelete()">
                            <i class="bi bi-trash me-2"></i>Delete Account
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete() {
    if (confirm('Are you sure you want to delete your account? This action cannot be undone.')) {
        alert('Account deletion feature is not implemented yet.');
    }
}
</script>
<?= $this->endSection() ?>