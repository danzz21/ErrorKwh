<?= $this->extend('templates/header') ?>

<?= $this->section('content') ?>
<div class="container py-4">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow-lg">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="bi bi-person-circle me-2"></i> Edit Profile
                    </h4>
                    <small class="text-muted">Perbarui informasi pribadi Anda</small>
                </div>
                <div class="card-body p-4">
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
                    
                    <form action="<?= base_url('auth/profile') ?>" method="post" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        
                        <div class="text-center mb-4">
                            <?php if ($user['foto']): ?>
                                <img src="<?= base_url('uploads/users/' . $user['foto']) ?>" 
                                     class="rounded-circle mb-3" 
                                     style="width: 120px; height: 120px; object-fit: cover; border: 3px solid #0054a6;"
                                     alt="Profile Photo"
                                     id="profilePreview">
                            <?php else: ?>
                                <div class="rounded-circle mx-auto bg-light d-flex align-items-center justify-content-center mb-3" 
                                     style="width: 120px; height: 120px; border: 3px solid #0054a6;">
                                    <i class="bi bi-person-fill text-secondary" style="font-size: 3rem;"></i>
                                </div>
                            <?php endif; ?>
                            
                            <div class="mb-3">
                                <label for="foto" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-camera me-1"></i> Ganti Foto
                                </label>
                                <input type="file" 
                                       class="form-control d-none" 
                                       id="foto" 
                                       name="foto"
                                       accept="image/*"
                                       onchange="previewImage(this)">
                                <div class="form-text small">Max 2MB, format: JPG, PNG</div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">NIP</label>
                                <input type="text" class="form-control" value="<?= $user['nip'] ?>" readonly>
                                <div class="form-text">Tidak dapat diubah</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Role</label>
                                <input type="text" class="form-control" value="<?= strtoupper($user['role']) ?>" readonly>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control <?= session()->getFlashdata('errors.nama') ? 'is-invalid' : '' ?>" 
                                   id="nama" 
                                   name="nama" 
                                   value="<?= old('nama', $user['nama']) ?>" 
                                   required>
                            <?php if (session()->getFlashdata('errors.nama')): ?>
                                <div class="invalid-feedback">
                                    <?= session()->getFlashdata('errors.nama') ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" 
                                       class="form-control <?= session()->getFlashdata('errors.email') ? 'is-invalid' : '' ?>" 
                                       id="email" 
                                       name="email" 
                                       value="<?= old('email', $user['email']) ?>" 
                                       required>
                                <?php if (session()->getFlashdata('errors.email')): ?>
                                    <div class="invalid-feedback">
                                        <?= session()->getFlashdata('errors.email') ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="telepon" class="form-label">Telepon</label>
                                <input type="tel" 
                                       class="form-control <?= session()->getFlashdata('errors.telepon') ? 'is-invalid' : '' ?>" 
                                       id="telepon" 
                                       name="telepon" 
                                       value="<?= old('telepon', $user['telepon']) ?>">
                                <?php if (session()->getFlashdata('errors.telepon')): ?>
                                    <div class="invalid-feedback">
                                        <?= session()->getFlashdata('errors.telepon') ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="unit_kerja" class="form-label">Unit Kerja</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="unit_kerja" 
                                       name="unit_kerja" 
                                       value="<?= old('unit_kerja', $user['unit_kerja']) ?>">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="lokasi_pln" class="form-label">Lokasi PLN</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="lokasi_pln" 
                                       name="lokasi_pln" 
                                       value="<?= old('lokasi_pln', $user['lokasi_pln']) ?>">
                            </div>
                        </div>
                        
                        <hr class="my-4">
                        
                        <h5 class="mb-3">
                            <i class="bi bi-shield-lock me-2"></i> Ubah Password
                        </h5>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Password Baru</label>
                                <input type="password" 
                                       class="form-control" 
                                       id="password" 
                                       name="password"
                                       placeholder="Kosongkan jika tidak ingin mengubah">
                                <div class="form-text">Minimal 6 karakter</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="confirm_password" class="form-label">Konfirmasi Password</label>
                                <input type="password" 
                                       class="form-control" 
                                       id="confirm_password" 
                                       name="confirm_password"
                                       placeholder="Konfirmasi password baru">
                            </div>
                        </div>
                        
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <strong>Perhatian:</strong> Isi kolom password hanya jika ingin mengubah password. Jika tidak, biarkan kosong.
                        </div>
                        
                        <div class="d-flex justify-content-between mt-4">
                            <a href="<?= base_url('dashboard') ?>" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-2"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-2"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-muted">
                    <small>
                        <i class="bi bi-info-circle me-1"></i>
                        Terakhir login: <?= $user['last_login'] ? date('d/m/Y H:i', strtotime($user['last_login'])) : 'Belum pernah login' ?>
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function previewImage(input) {
    const preview = document.getElementById('profilePreview');
    const file = input.files[0];
    const reader = new FileReader();
    
    reader.onload = function(e) {
        if (!preview) {
            // Create new image if doesn't exist
            const div = document.querySelector('.text-center.mb-4');
            const newImg = document.createElement('img');
            newImg.id = 'profilePreview';
            newImg.className = 'rounded-circle mb-3';
            newImg.style = 'width: 120px; height: 120px; object-fit: cover; border: 3px solid #0054a6;';
            newImg.alt = 'Profile Photo';
            newImg.src = e.target.result;
            
            const oldDiv = div.querySelector('.rounded-circle.mx-auto');
            if (oldDiv) {
                oldDiv.replaceWith(newImg);
            }
        } else {
            preview.src = e.target.result;
        }
    }
    
    if (file) {
        reader.readAsDataURL(file);
    }
}

// Password validation
document.getElementById('password').addEventListener('input', validatePassword);
document.getElementById('confirm_password').addEventListener('input', validatePassword);

function validatePassword() {
    const password = document.getElementById('password').value;
    const confirm = document.getElementById('confirm_password').value;
    
    if (password !== '' && confirm !== '' && password !== confirm) {
        document.getElementById('confirm_password').classList.add('is-invalid');
        document.getElementById('confirm_password').classList.remove('is-valid');
    } else if (confirm !== '' && password === confirm) {
        document.getElementById('confirm_password').classList.remove('is-invalid');
        document.getElementById('confirm_password').classList.add('is-valid');
    } else {
        document.getElementById('confirm_password').classList.remove('is-invalid', 'is-valid');
    }
}
</script>
<?= $this->endSection() ?>