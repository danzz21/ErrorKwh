<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0">
                <i class="bi bi-database text-primary me-2"></i>
                All KWH Data
            </h2>
            <p class="text-muted mb-0">
                Total Data: <strong><?= number_format(count($kwh_data)) ?></strong>
            </p>
        </div>
        <div>
            <a href="<?= base_url('kwh') ?>" class="btn btn-outline-secondary me-2">
                <i class="bi bi-arrow-left me-2"></i>Back to Calculator
            </a>
            <a href="<?= base_url('kwh/export') ?>" class="btn btn-success">
                <i class="bi bi-file-excel me-2"></i>Export CSV
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
    
    <!-- Data Table -->
    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-sm">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Tanggal</th>
                            <th>Pelanggan</th>
                            <th>Operator</th>
                            <th>Lokasi</th>
                            <th>P1 (kW)</th>
                            <th>P2 (kW)</th>
                            <th>Error (%)</th>
                            <th>Kelas</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($kwh_data)): ?>
                            <?php foreach ($kwh_data as $data): ?>
                                <tr>
                                    <td><?= $data['id'] ?></td>
                                    <td>
                                        <small><?= date('d/m/Y', strtotime($data['created_at'])) ?></small><br>
                                        <small class="text-muted"><?= date('H:i', strtotime($data['created_at'])) ?></small>
                                    </td>
                                    <td>
                                        <strong><?= $data['nama_pelanggan'] ?? '-' ?></strong><br>
                                        <small class="text-muted"><?= $data['id_pelanggan'] ?? '-' ?></small>
                                    </td>
                                    <td>
                                        <?= $data['operator_nama'] ?? '-' ?><br>
                                        <small class="text-muted"><?= $data['unit_kerja'] ?? '-' ?></small>
                                    </td>
                                    <td>
                                        <small><?= substr($data['keterangan'] ?? '-', 0, 30) ?>...</small>
                                    </td>
                                    <td><?= number_format($data['p1_kw'], 3) ?></td>
                                    <td><?= number_format($data['p2_kw'], 3) ?></td>
                                    <td><?= number_format($data['error_percent'], 2) ?>%</td>
                                    <td><?= number_format($data['class_meter'], 1) ?>%</td>
                                    <td>
                                        <?php if ($data['status_final'] === 'BAIK'): ?>
                                            <span class="badge bg-success">BAIK</span>
                                        <?php elseif ($data['status_final'] === 'LUAR_KELAS'): ?>
                                            <span class="badge bg-danger">LUAR KELAS</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary"><?= $data['status_final'] ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="<?= base_url('kwh/view/' . $data['id']) ?>" 
                                               class="btn btn-outline-primary" title="View">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="<?= base_url('kwh/delete/' . $data['id']) ?>" 
                                               class="btn btn-outline-danger" 
                                               onclick="return confirm('Delete this data?')" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="11" class="text-center py-4">
                                    <i class="bi bi-inbox fs-4 d-block mb-2"></i>
                                    <p class="text-muted">No data available</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>