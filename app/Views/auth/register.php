<?= $this->extend('templates/header') ?>

<?= $this->section('content') ?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">
            <div class="card shadow-lg">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="bi bi-person-plus me-2"></i> Tambah User Baru
                    </h4>
                    <small class="text-muted">Registrasi user baru untuk sistem PLN KWH Calculator</small>
                </div>
                <div class="card-body p-4">
                    <?php if (session()->getFlashdata('errors')): ?>
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <strong>Error Validation:</strong>
                            <ul class="mb-0 mt-2">
                                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                    <li><?= $error ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <?= session()->getFlashdata('error') ?>
                        </div>
                    <?php endif; ?>
                    
                    <form action="<?= base_url('auth/register') ?>" method="post" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        
                        <div class="row">
                            <!-- Kolom Kiri -->
                            <div class="col-md-6">
                                <h5 class="mb-3 text-primary">
                                    <i class="bi bi-person-badge me-2"></i> Data Pribadi
                                </h5>
                                
                                <div class="mb-3">
                                    <label for="nip" class="form-label">NIP <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="nip" 
                                           name="nip" 
                                           value="<?= old('nip') ?>" 
                                           placeholder="Contoh: PLN00123"
                                           required>
                                    <div class="form-text">Nomor Induk Pegawai PLN</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="nama" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="nama" 
                                           name="nama" 
                                           value="<?= old('nama') ?>" 
                                           placeholder="Nama lengkap"
                                           required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" 
                                           class="form-control" 
                                           id="email" 
                                           name="email" 
                                           value="<?= old('email') ?>" 
                                           placeholder="email@pln.co.id"
                                           required>
                                    <div class="form-text">Email resmi PLN</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="telepon" class="form-label">Nomor Telepon</label>
                                    <input type="tel" 
                                           class="form-control" 
                                           id="telepon" 
                                           name="telepon" 
                                           value="<?= old('telepon') ?>" 
                                           placeholder="0812-3456-7890">
                                </div>
                            </div>
                            
                            <!-- Kolom Kanan -->
                            <div class="col-md-6">
                                <h5 class="mb-3 text-primary">
                                    <i class="bi bi-building me-2"></i> Data Kepegawaian
                                </h5>
                                
                                <div class="mb-3">
                                    <label for="unit_kerja" class="form-label">Unit Kerja <span class="text-danger">*</span></label>
                                    <select class="form-select" id="unit_kerja" name="unit_kerja" required>
                                        <option value="">Pilih Unit Kerja</option>
                                        <option value="Divisi Metrologi" <?= old('unit_kerja') == 'Divisi Metrologi' ? 'selected' : '' ?>>Divisi Metrologi</option>
                                        <option value="Divisi Teknis" <?= old('unit_kerja') == 'Divisi Teknis' ? 'selected' : '' ?>>Divisi Teknis</option>
                                        <option value="Divisi Operasional" <?= old('unit_kerja') == 'Divisi Operasional' ? 'selected' : '' ?>>Divisi Operasional</option>
                                        <option value="Divisi Distribusi" <?= old('unit_kerja') == 'Divisi Distribusi' ? 'selected' : '' ?>>Divisi Distribusi</option>
                                        <option value="Divisi Transmisi" <?= old('unit_kerja') == 'Divisi Transmisi' ? 'selected' : '' ?>>Divisi Transmisi</option>
                                        <option value="IT Department" <?= old('unit_kerja') == 'IT Department' ? 'selected' : '' ?>>IT Department</option>
                                        <option value="Lainnya" <?= old('unit_kerja') == 'Lainnya' ? 'selected' : '' ?>>Lainnya</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="lokasi_pln" class="form-label">Lokasi PLN <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="lokasi_pln" 
                                           name="lokasi_pln" 
                                           value="<?= old('lokasi_pln') ?>" 
                                           placeholder="Contoh: PLN Jakarta Pusat"
                                           required>
                                    <div class="form-text">Contoh: PLN Wilayah Jakarta, PLN Area Bogor, dll.</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="role" class="form-label">Role/Peran <span class="text-danger">*</span></label>
                                    <select class="form-select" id="role" name="role" required>
                                        <option value="">Pilih Role</option>
                                        <option value="pln_teknis" <?= old('role') == 'pln_teknis' ? 'selected' : '' ?>>PLN Teknis (Input Data)</option>
                                        <option value="pln_metrologi" <?= old('role') == 'pln_metrologi' ? 'selected' : '' ?>>PLN Metrologi (Supervisor)</option>
                                        <option value="pln_operasional" <?= old('role') == 'pln_operasional' ? 'selected' : '' ?>>PLN Operasional (View Only)</option>
                                        <option value="admin" <?= old('role') == 'admin' ? 'selected' : '' ?>>Administrator (Full Access)</option>
                                    </select>
                                    <div class="form-text">
                                        <small>
                                            <strong>Admin:</strong> Full access | 
                                            <strong>Metrologi:</strong> Supervisi + input | 
                                            <strong>Teknis:</strong> Input data | 
                                            <strong>Operasional:</strong> View only
                                        </small>
                                    </div>
                                </div>
                                
                                <h5 class="mb-3 mt-4 text-primary">
                                    <i class="bi bi-shield-lock me-2"></i> Keamanan
                                </h5>
                                
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                    <input type="password" 
                                           class="form-control" 
                                           id="password" 
                                           name="password" 
                                           placeholder="Minimal 6 karakter"
                                           required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
                                    <input type="password" 
                                           class="form-control" 
                                           id="confirm_password" 
                                           name="confirm_password" 
                                           placeholder="Ulangi password"
                                           required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle-fill me-2"></i>
                                    <strong>Informasi:</strong> Password akan dienkripsi dan disimpan secara aman. User dapat mengubah password melalui menu profile setelah login.
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between mt-4">
                            <a href="<?= base_url('users') ?>" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-2"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-person-plus me-2"></i> Simpan User Baru
                            </button>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-muted small">
                    <i class="bi bi-shield-check me-1"></i> Sistem akan mengirimkan informasi login ke email user.
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Password confirmation validation
document.getElementById('password').addEventListener('input', validatePassword);
document.getElementById('confirm_password').addEventListener('input', validatePassword);

function validatePassword() {
    const password = document.getElementById('password').value;
    const confirm = document.getElementById('confirm_password').value;
    const confirmField = document.getElementById('confirm_password');
    
    if (confirm !== '' && password !== confirm) {
        confirmField.classList.add('is-invalid');
        confirmField.classList.remove('is-valid');
    } else if (confirm !== '') {
        confirmField.classList.remove('is-invalid');
        confirmField.classList.add('is-valid');
    } else {
        confirmField.classList.remove('is-invalid', 'is-valid');
    }
}

// Auto format phone number
document.getElementById('telepon').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    
    if (value.length > 3 && value.length <= 6) {
        value = value.replace(/(\d{3})(\d{0,3})/, '$1-$2');
    } else if (value.length > 6 && value.length <= 10) {
        value = value.replace(/(\d{3})(\d{3})(\d{0,4})/, '$1-$2-$3');
    } else if (value.length > 10) {
        value = value.replace(/(\d{3})(\d{3})(\d{4})/, '$1-$2-$3');
    }
    
    e.target.value = value;
});
</script>
<?= $this->endSection() ?> 