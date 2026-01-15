<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0">
                <i class="bi bi-eye text-primary me-2"></i>
                Detail KWH Data
            </h2>
            <p class="text-muted mb-0">
                ID: <strong>#<?= $kwh['id'] ?></strong> | 
                Pelanggan: <strong><?= $kwh['nama_pelanggan'] ?? '-' ?></strong>
            </p>
        </div>
        <div>
            <a href="<?= base_url('kwh/all') ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to All Data
            </a>
        </div>
    </div>
    
    <!-- Data Card -->
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i> Measurement Details</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-sm">
                                <tr>
                                    <th width="40%">Tanggal/Time</th>
                                    <td><?= date('d/m/Y H:i:s', strtotime($kwh['created_at'])) ?></td>
                                </tr>
                                <tr>
                                    <th>Pelanggan</th>
                                    <td><?= $kwh['nama_pelanggan'] ?? '-' ?></td>
                                </tr>
                                <tr>
                                    <th>ID Pelanggan</th>
                                    <td><?= $kwh['id_pelanggan'] ?? '-' ?></td>
                                </tr>
                                <tr>
                                    <th>Lokasi</th>
                                    <td><?= $kwh['keterangan'] ?? '-' ?></td>
                                </tr>
                                <tr>
                                    <th>Operator</th>
                                    <td><?= $user['nama'] ?? '-' ?></td>
                                </tr>
                                <tr>
                                    <th>Mode</th>
                                    <td>
                                        <?= ($kwh['calculation_mode'] === 'mode2') ? '3 Phase' : 'Kedipan' ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm">
                                <tr>
                                    <th width="40%">Arus (A)</th>
                                    <td><?= number_format($kwh['arus'], 2) ?></td>
                                </tr>
                                <tr>
                                    <th>Tegangan (V)</th>
                                    <td><?= number_format($kwh['tegangan'], 2) ?></td>
                                </tr>
                                <tr>
                                    <th>Cos φ</th>
                                    <td><?= number_format($kwh['cosphi'], 2) ?></td>
                                </tr>
                                <tr>
                                    <th>Constanta</th>
                                    <td><?= number_format($kwh['constanta'], 0) ?></td>
                                </tr>
                                <tr>
                                    <th>Kedipan</th>
                                    <td><?= $kwh['count'] ?></td>
                                </tr>
                                <tr>
                                    <th>Durasi (s)</th>
                                    <td><?= number_format($kwh['duration'], 2) ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0"><i class="bi bi-calculator me-2"></i> Calculation Results</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <h1>
                            <?php if ($kwh['status_final'] === 'BAIK'): ?>
                                <span class="badge bg-success p-3">BAIK</span>
                            <?php elseif ($kwh['status_final'] === 'LUAR_KELAS'): ?>
                                <span class="badge bg-danger p-3">LUAR KELAS</span>
                            <?php else: ?>
                                <span class="badge bg-secondary p-3"><?= $kwh['status_final'] ?></span>
                            <?php endif; ?>
                        </h1>
                    </div>
                    
                    <table class="table table-sm">
                        <tr>
                            <th>P1 (kW)</th>
                            <td><?= number_format($kwh['p1_kw'], 3) ?></td>
                        </tr>
                        <tr>
                            <th>P2 (kW)</th>
                            <td><?= number_format($kwh['p2_kw'], 3) ?></td>
                        </tr>
                        <tr>
                            <th>Error (%)</th>
                            <td><?= number_format($kwh['error_percent'], 2) ?>%</td>
                        </tr>
                        <tr>
                            <th>Kelas Meter</th>
                            <td><?= number_format($kwh['class_meter'], 1) ?>%</td>
                        </tr>
                        <?php if ($kwh['calculation_mode'] === 'mode2'): ?>
                            <tr>
                                <th>Pr (kW)</th>
                                <td><?= number_format($kwh['pr_value'] ?? 0, 3) ?></td>
                            </tr>
                            <tr>
                                <th>Ps (kW)</th>
                                <td><?= number_format($kwh['ps_value'] ?? 0, 3) ?></td>
                            </tr>
                            <tr>
                                <th>Pt (kW)</th>
                                <td><?= number_format($kwh['pt_value'] ?? 0, 3) ?></td>
                            </tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Photos Section -->
    <?php if (!empty($photos)): ?>
        <div class="card shadow">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0"><i class="bi bi-images me-2"></i> Photos</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php foreach ($photos as $photo): ?>
                        <div class="col-md-3 mb-3">
                            <div class="border rounded p-2 text-center">
                                <a href="<?= base_url('kwh/viewPhoto/' . $photo) ?>" target="_blank">
                                    <img src="<?= base_url('kwh/viewPhoto/' . $photo) ?>" 
                                         alt="Photo" 
                                         class="img-fluid rounded" 
                                         style="max-height: 150px;">
                                </a>
                                <small class="d-block mt-2"><?= $photo ?></small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>