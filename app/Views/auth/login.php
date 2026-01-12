<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg">
                <div class="card-header text-center">
                    <h4 class="mb-0">
                        <span class="pln-logo">PLN</span> KWH Calculator
                    </h4>
                    <small class="text-muted">Login ke Sistem</small>
                </div>
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <i class="bi bi-lightning-charge-fill text-warning" style="font-size: 3rem;"></i>
                        <h3 class="mt-3">Login Sistem</h3>
                        <p class="text-muted">Masukkan NIP dan password Anda</p>
                    </div>
                    
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <?= session()->getFlashdata('error') ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            <?= session()->getFlashdata('success') ?>
                        </div>
                    <?php endif; ?>
                    
                    <form action="<?= base_url('auth/login') ?>" method="post">
                        <?= csrf_field() ?>
                        
                        <div class="mb-3">
                            <label for="nip" class="form-label">
                                <i class="bi bi-person-badge me-1"></i> NIP
                            </label>
                            <input type="text" 
                                   class="form-control form-control-lg <?= (session()->getFlashdata('error')) ? 'is-invalid' : '' ?>" 
                                   id="nip" 
                                   name="nip" 
                                   value="<?= old('nip') ?>" 
                                   placeholder="Masukkan NIP Anda"
                                   required>
                            <div class="invalid-feedback">
                                Masukkan NIP yang valid
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="password" class="form-label">
                                <i class="bi bi-key me-1"></i> Password
                            </label>
                            <div class="input-group">
                                <input type="password" 
                                       class="form-control form-control-lg <?= (session()->getFlashdata('error')) ? 'is-invalid' : '' ?>" 
                                       id="password" 
                                       name="password" 
                                       placeholder="Masukkan password"
                                       required>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">
                                    <i class="bi bi-eye" id="toggleIcon"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-box-arrow-in-right me-2"></i> Login
                            </button>
                        </div>
                        
                        <div class="text-center mt-4">
                            <small class="text-muted">
                                <i class="bi bi-info-circle me-1"></i>
                                Gunakan NIP dan password yang diberikan oleh Administrator
                            </small>
                        </div>
                    </form>
                    
                </div>
                <div class="card-footer text-center py-3">
                    <small class="text-muted">
                        &copy; <?= date('Y') ?> PLN - Divisi Metrologi
                    </small>
                </div>
            </div>
            
            <div class="text-center mt-4">
                <div class="row">
                    <div class="col-md-4">
                        <div class="text-muted small">
                            <i class="bi bi-shield-check text-success"></i> Secure Login
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-muted small">
                            <i class="bi bi-clock-history text-primary"></i> 24/7 Access
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-muted small">
                            <i class="bi bi-file-earmark-text text-warning"></i> Full Audit Trail
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Function untuk toggle password visibility
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('toggleIcon');
    
    if (passwordInput && toggleIcon) {
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.className = 'bi bi-eye-slash';
        } else {
            passwordInput.type = 'password';
            toggleIcon.className = 'bi bi-eye';
        }
    }
}

// Auto focus on NIP field
document.addEventListener('DOMContentLoaded', function() {
    const nipField = document.getElementById('nip');
    if (nipField) {
        nipField.focus();
    }
    
    // HAPUS atau COMMENT bagian ini jika tidak diperlukan:
    /*
    // Auto dismiss alerts - HAPUS KARENA TIDAK ADA ALERT DI LOGIN
    setTimeout(() => {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            const bsAlert = new bootstrap.Alert(alert);
            setTimeout(() => bsAlert.close(), 5000);
        });
    }, 3000);
    
    // Close sidebar on mobile - HAPUS KARENA TIDAK ADA SIDEBAR DI LOGIN
    document.addEventListener('click', function(e) {
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        
        if (window.innerWidth < 768 && 
            sidebar && sidebar.classList.contains('active') &&
            !sidebar.contains(e.target) &&
            !e.target.closest('.navbar-toggler')) {
            sidebar.classList.remove('active');
        }
    });
    */
});

// Auto-fill admin credentials button
function fillAdminCredentials() {
    const nipField = document.getElementById('nip');
    const passwordField = document.getElementById('password');
    
    if (nipField && passwordField) {
        nipField.value = 'ADMIN001';
        passwordField.value = 'admin123';
        
        // Optional: show notification
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-info mt-3';
        alertDiv.innerHTML = '<i class="bi bi-info-circle me-2"></i>Admin credentials filled. Click Login to continue.';
        document.querySelector('form').prepend(alertDiv);
        
        // Auto remove alert after 3 seconds
        setTimeout(() => {
            alertDiv.remove();
        }, 3000);
    }
}
</script>

<style>
.card {
    border-radius: 15px;
    border: none;
}

.form-control-lg {
    border-radius: 8px;
    padding: 15px;
}

.btn-lg {
    border-radius: 8px;
    padding: 12px;
}

.input-group .btn-outline-secondary {
    border-radius: 0 8px 8px 0;
}

.pln-logo {
    background: linear-gradient(135deg, #ffd100 0%, #ffed4e 100%);
    color: #0054a6;
    padding: 5px 15px;
    border-radius: 8px;
    font-weight: bold;
    font-size: 1.2rem;
}

/* Custom alert styling */
.alert {
    border-radius: 8px;
    border: none;
}
</style>
<?= $this->endSection() ?>