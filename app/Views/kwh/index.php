<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <title><?= $title ?> - PLN</title>
    
    <!-- Bootstrap 5 + Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <style>
        :root {
            --primary: #0d6efd;
            --success: #198754;
            --danger: #dc3545;
            --warning: #ffc107;
            --info: #0dcaf0;
            --dark: #212529;
        }
        
        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            min-height: 100vh;
            padding-bottom: 80px;
        }
        
        .app-container {
            max-width: 100%;
            padding: 0 15px;
        }
        
        /* Flash Messages */
        .flash-messages {
            position: fixed;
            top: 15px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 9999;
            width: 90%;
            max-width: 500px;
        }
        
        /* Tasbih Button */
        .tasbih-btn {
            width: 160px;
            height: 160px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--success) 0%, #157347 100%);
            border: 6px solid white;
            box-shadow: 0 15px 30px rgba(0,0,0,0.3);
            color: white;
            font-size: 3rem;
            font-weight: bold;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            margin: 15px auto;
            transition: all 0.1s;
            user-select: none;
            touch-action: manipulation;
        }
        
        @media (min-width: 768px) {
            .tasbih-btn {
                width: 180px;
                height: 180px;
                font-size: 3.5rem;
            }
        }
        
        .tasbih-btn:active {
            transform: scale(0.95);
        }
        
        /* Cards */
        .card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.15);
            margin-bottom: 15px;
            background: rgba(255, 255, 255, 0.95);
        }
        
        .card-header {
            background: linear-gradient(135deg, var(--primary) 0%, #0b5ed7 100%);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            padding: 12px 15px;
            font-weight: 600;
        }
        
        /* Input Groups */
        .input-group-custom .input-group-text {
            background: #f8f9fa;
            font-weight: 500;
        }
        
        /* Number Display */
        .big-number {
            font-family: 'Courier New', monospace;
            font-size: 2.5rem;
            font-weight: 800;
            text-align: center;
            margin: 5px 0;
        }
        
        .result-badge {
            font-size: 1.2rem;
            font-weight: bold;
            padding: 10px 15px;
            border-radius: 10px;
        }
        
        /* Blink List */
        .blink-list {
            max-height: 200px;
            overflow-y: auto;
            margin: 10px 0;
        }
        
        .blink-item {
            padding: 8px 12px;
            margin-bottom: 5px;
            border-radius: 8px;
            background: #f8f9fa;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .blink-item:hover {
            background: #e9ecef;
        }
        
        .blink-item.active {
            background: var(--primary);
            color: white;
        }
        
        /* Progress Bar */
        .progress-thin {
            height: 5px;
            border-radius: 3px;
        }
        
        /* Bottom Nav */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            box-shadow: 0 -2px 15px rgba(0,0,0,0.1);
            z-index: 1000;
            padding: 10px 0;
        }
        
        .nav-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-decoration: none;
            color: var(--dark);
            padding: 5px;
        }
        
        .nav-btn .icon {
            font-size: 1.4rem;
            margin-bottom: 3px;
        }
        
        .nav-btn .label {
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        /* Status Colors */
        .status-good { color: var(--success); }
        .status-warning { color: var(--warning); }
        .status-bad { color: var(--danger); }
        
        /* Photo Preview */
        .photo-preview-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
        }
        
        .photo-preview-item {
            position: relative;
            width: 100px;
            height: 100px;
        }
        
        .photo-preview-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid #dee2e6;
        }
        
        .remove-photo-btn {
            position: absolute;
            top: -8px;
            right: -8px;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
        }
        
        /* Responsive */
        @media (max-width: 576px) {
            .big-number {
                font-size: 2rem;
            }
            
            .tasbih-btn {
                width: 140px;
                height: 140px;
                font-size: 2.5rem;
            }
        }
        
        /* Animation */
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        .pulse {
            animation: pulse 0.5s ease-in-out;
        }
        
        /* Table in History */
        .history-table {
            max-height: 300px;
            overflow-y: auto;
        }
        
        .history-table thead th {
            position: sticky;
            top: 0;
            background: #f8f9fa;
            z-index: 10;
        }
    </style>
</head>
<body>
    <!-- Flash Messages -->
    <div class="flash-messages">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show shadow" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show shadow" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
    </div>

    <!-- Di bagian atas, tambahkan info user: -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">
            <i class="bi bi-lightning-charge-fill text-warning me-2"></i>
            KWH Error Calculator
        </h4>
        <small class="text-muted">
            Operator: <?= $user_nama ?> | 
            Role: <?= strtoupper($user_role) ?>
        </small>
    </div>
    <div>
        <a href="<?= base_url('dashboard') ?>" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-speedometer2 me-1"></i> Dashboard
        </a>
    </div>
</div>

    <!-- Main Content -->
    <div class="app-container">
        
        <!-- SECTION 1: INPUT PARAMETER METERAN -->
<div class="card mb-3">
    <div class="card-header">
        <i class="bi bi-gear me-2"></i>PARAMETER METERAN
    </div>
    <div class="card-body">
        <div class="row g-2">
            <!-- Arus (I) -->
            <div class="col-md-2 col-6">
                <label class="form-label small fw-bold">Arus (A)</label>
                <div class="input-group input-group-custom">
                    <input type="number" class="form-control" id="arusInput" step="0.1" value="5" required>
                    <span class="input-group-text">A</span>
                </div>
                <div class="form-text">Arus beban</div>
            </div>
            
            <!-- Tegangan (V) -->
            <div class="col-md-2 col-6">
                <label class="form-label small fw-bold">Tegangan (V)</label>
                <div class="input-group input-group-custom">
                    <input type="number" class="form-control" id="teganganInput" step="1" value="220">
                    <span class="input-group-text">V</span>
                </div>
                <div class="form-text">Default: 220V</div>
            </div>
            
            <!-- Cosphi -->
            <div class="col-md-2 col-6">
                <label class="form-label small fw-bold">Cos φ</label>
                <div class="input-group input-group-custom">
                    <input type="number" class="form-control" id="cosphiInput" step="0.01" min="0" max="1" value="0.85">
                    <span class="input-group-text">PF</span>
                </div>
                <div class="form-text">Power Factor</div>
            </div>
            
            <!-- Constanta -->
            <div class="col-md-2 col-6">
                <label class="form-label small fw-bold">Constanta</label>
                <div class="input-group input-group-custom">
                    <input type="number" class="form-control" id="constantaInput" step="1" value="1600" required>
                    <span class="input-group-text">imp/kWh</span>
                </div>
                <div class="form-text">CT meter</div>
            </div>
            
            <!-- Class Meter Select -->
<div class="col-md-4">
    <label class="form-label small fw-bold">Kelas Meter</label>
    <div class="input-group input-group-custom">
        <select class="form-select" id="classMeterInput" onchange="updateClassMeter()">
            <option value="1.0">Kelas 1 (1.0%) - Meter Umum</option>
            <option value="0.5">Kelas 0.5 (0.5%) - Meter Industri</option>
            <option value="0.2">Kelas 0.2 (0.2%) - Meter Presisi</option>
        </select>
        <span class="input-group-text"><i class="bi bi-lightning-charge"></i></span>
    </div>
    <div class="form-text">
        <small>
            <span class="badge bg-info">Logika:</span> 
            Error &lt; Kelas = <span class="text-danger">TIDAK STABIL</span> | 
            Error ≥ Kelas = <span class="text-success">BAIK</span> |
            Error > 5% = <span class="text-danger">BURUK</span>
        </small>
    </div>
</div>
        
        <!-- Info Panel Kelas Meter -->
        <div class="alert alert-info mt-3 py-2">
            <div class="row small">
                <div class="col-md-4">
                    <strong>Kelas 1.0%:</strong> Meter rumah tangga/umum
                </div>
                <div class="col-md-4">
                    <strong>Kelas 0.5%:</strong> Meter industri menengah
                </div>
                <div class="col-md-4">
                    <strong>Kelas 0.2%:</strong> Meter presisi tinggi
                </div>
            </div>
        </div>
    </div>
</div>

        <!-- SECTION 2: PENGUKURAN KEDIPAN -->
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-speedometer2 me-2"></i>PENGUKURAN KEDIPAN</span>
                <div>
                    <span class="badge bg-success me-2" id="statusBadge">SIAP</span>
                    <div class="form-check form-switch d-inline-block">
                        <input class="form-check-input" type="checkbox" id="autoModeSwitch" checked>
                        <label class="form-check-label text-white small" for="autoModeSwitch">Auto</label>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row align-items-center">
                    <!-- Tasbih Button -->
                    <div class="col-md-4 text-center">
                        <div class="tasbih-btn" id="tasbihBtn" onclick="countBlink()">
                            <div id="tasbihCounter">0</div>
                            <div style="font-size: 0.8rem;">KEDIPAN</div>
                        </div>
                        <p class="small text-muted mt-2">
                            <i class="bi bi-info-circle"></i> Klik / Spasi
                        </p>
                    </div>
                    
                    <!-- Timer & Controls -->
                    <div class="col-md-8">
                        <div class="row g-2">
                            <div class="col-6">
                                <div class="text-center">
                                    <div class="big-number text-primary" id="timerDisplay">0.0</div>
                                    <small class="text-muted">Durasi (detik)</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center">
                                    <div class="big-number text-success" id="blinkRateDisplay">0.0</div>
                                    <small class="text-muted">Kedipan/detik</small>
                                </div>
                            </div>
                            
                            <div class="col-12 mt-2">
                                <div class="d-grid gap-2">
                                    <button class="btn btn-success" onclick="toggleTimer()" id="timerBtn">
                                        <i class="bi bi-play-fill me-2"></i> START PENGUKURAN
                                    </button>
                                    <button class="btn btn-outline-danger btn-sm" onclick="resetBlinkData()">
                                        <i class="bi bi-trash me-1"></i> Reset Kedipan
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 3: PILIH KEDIPAN & HITUNG -->
        <div class="card mb-3">
            <div class="card-header">
                <i class="bi bi-list-check me-2"></i>PILIH KEDIPAN UNTUK PERHITUNGAN
            </div>
            <div class="card-body">
                <!-- Blink List -->
                <div id="blinkListContainer">
                    <div class="blink-list" id="blinkList">
                        <div class="text-center text-muted py-3" id="noBlinkMessage">
                            <i class="bi bi-inbox fs-1"></i>
                            <p class="mt-2">Belum ada kedipan</p>
                        </div>
                    </div>
                </div>
                
                <!-- Pilih Kedipan -->
                <div class="row g-2 mt-3">
                    <div class="col-md-6">
                        <label class="form-label">Gunakan data hingga kedipan ke:</label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="selectedBlinkInput" min="1" value="1" disabled>
                            <button class="btn btn-outline-primary" onclick="calculateSelected()">
                                <i class="bi bi-calculator"></i> Hitung
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex flex-column h-100 justify-content-end">
                            <button class="btn btn-success" onclick="saveData()">
                                <i class="bi bi-save me-2"></i> Simpan Perhitungan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

       <!-- SECTION 4: HASIL PERHITUNGAN ERROR -->
<div class="card mb-3">
    <div class="card-header">
        <i class="bi bi-graph-up me-2"></i>HASIL PERHITUNGAN ERROR
    </div>
    <div class="card-body">
        <div class="row g-3">
            <!-- P1 Calculation -->
            <div class="col-md-3">
                <div class="card bg-light border">
                    <div class="card-body text-center">
                        <h6 class="card-title text-muted">P1 (kwh meter)</h6>
                        <div class="big-number text-primary" id="p1Result">0.000</div>
                        <small class="text-muted">KW</small>
                    </div>
                </div>
            </div>
            
            <!-- P2 Calculation -->
            <div class="col-md-3">
                <div class="card bg-light border">
                    <div class="card-body text-center">
                        <h6 class="card-title text-muted">P2 (Pembanding)</h6>
                        <div class="big-number text-success" id="p2Result">0.935</div>
                        <small class="text-muted">KW</small>
                    </div>
                </div>
            </div>
            
            <!-- Error Result -->
            <div class="col-md-3">
                <div class="card bg-light border">
                    <div class="card-body text-center">
                        <h6 class="card-title text-muted">Error Meter</h6>
                        <div class="big-number" id="errorResult">0.00</div>
                        <small class="text-muted">%</small>
                        <div class="mt-2">
                            <span class="badge bg-secondary" id="errorStatus">-</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Status Final -->
            <div class="col-md-3">
                <div class="card border" id="finalStatusCard">
                    <div class="card-body text-center">
                        <h6 class="card-title">STATUS FINAL</h6>
                        <div class="big-number" id="finalStatusResult">-</div>
                        <small class="text-muted" id="classMeterLabel">Kelas: 1.0%</small>
                        <div class="mt-2">
                            <span class="badge" id="finalStatusBadge">-</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Detail Info dengan Class Meter -->
            <div class="col-12">
                <div class="alert alert-info py-2">
                    <div class="row small">
                        <div class="col-md-2">
                            <strong>Kelas Meter:</strong> <span id="detailClass">1.0</span>%
                        </div>
                        <div class="col-md-2">
                            <strong>Kedipan:</strong> <span id="detailCount">0</span>
                        </div>
                        <div class="col-md-2">
                            <strong>Waktu:</strong> <span id="detailTime">0.0</span> detik
                        </div>
                        <div class="col-md-2">
                            <strong>Kedipan/detik:</strong> <span id="detailRate">0.000</span>
                        </div>
                        <div class="col-md-2">
                            <strong>Constanta:</strong> <span id="detailConstanta">1600</span>
                        </div>
                        <div class="col-md-2">
                            <strong>Error Absolut:</strong> <span id="detailAbsError">0.00</span>%
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

        <!-- SECTION 5: SIMPAN DATA DENGAN CAMERA -->
        <div class="card mb-3">
            <div class="card-header">
                <i class="bi bi-save me-2"></i>SIMPAN DATA PENGUKURAN
            </div>
            <div class="card-body">
                <!-- SIMPAN ini saja di bagian form: -->
<form method="post" action="<?= base_url('kwh/save') ?>" enctype="multipart/form-data" id="saveForm">
    <input type="hidden" name="class_meter" id="inputClassMeter" value="1.0">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label">Keterangan/Lokasi:</label>
                            <input type="text" name="keterangan" class="form-control" placeholder="Contoh: Meteran RUMAH 001" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Upload Foto:</label>
                            <div class="input-group mb-2">
                                <button type="button" class="btn btn-outline-primary" onclick="takePhoto()">
                                    <i class="bi bi-camera-fill me-2"></i> Ambil Foto
                                </button>
                                <input type="file" name="photos[]" class="form-control" multiple accept="image/*" id="photoInput">
                            </div>
                            <div class="form-text">Klik "Ambil Foto" untuk foto langsung dari kamera</div>
                            
                            <!-- Photo Preview -->
                            <div class="photo-preview-container" id="photoPreview"></div>
                        </div>
                        
                        <!-- Hidden Inputs -->
                        <input type="hidden" name="count" id="inputCount">
                        <input type="hidden" name="duration" id="inputDuration">
                        <input type="hidden" name="arus" id="inputArus">
                        <input type="hidden" name="tegangan" id="inputTegangan">
                        <input type="hidden" name="cosphi" id="inputCosphi">
                        <input type="hidden" name="constanta" id="inputConstanta">
                        <input type="hidden" name="blink_data" id="inputBlinkData">
                        <input type="hidden" name="selected_blink" id="inputSelectedBlink">
                        
                        <div class="col-12 mt-3">
                            <button type="submit" class="btn btn-primary w-100" id="submitBtn">
                                <i class="bi bi-check-lg me-2"></i> SIMPAN KE DATABASE
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

      <!-- SECTION 6: RIWAYAT PENGUKURAN -->
<div class="card mb-5" id="riwayat">  <!-- TAMBAH ID DISINI -->
    <div class="card-header d-flex justify-content-between align-items-center">
    <span><i class="bi bi-clock-history me-2"></i>RIWAYAT PENGUKURAN</span>
    <div>
        <div class="btn-group btn-group-sm">
            <!-- Tombol Export CSV -->
            <a href="<?= base_url('kwh/export') ?>" class="btn btn-success">
                <i class="bi bi-file-excel me-1"></i> CSV
            </a>
            
            <!-- Tombol Export All PDF -->
            <a href="<?= base_url('export/pdf') ?>" class="btn btn-danger" target="_blank">
                <i class="bi bi-file-pdf me-1"></i> PDF
            </a>
            
            <!-- Tombol Hapus Semua -->
            <a href="<?= site_url('kwh/clearAll') ?>" 
               class="btn btn-warning"
               onclick="return confirm('Hapus SEMUA data?\\n\\n⚠️ PERINGATAN:\\n• Semua data akan dihapus permanen\\n• Foto-foto juga akan dihapus\\n• Tidak bisa dikembalikan!')">
                <i class="bi bi-trash me-1"></i> Clear All
            </a>
        </div>
    </div>
</div>
    <div class="card-body p-0">
        <div class="history-table">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="40">#</th>
                        <th width="120">Waktu</th>
                        <th>Lokasi</th>
                        <th width="100">Arus</th>
                        <th width="100">Constanta</th>
                        <th width="80">Status</th>
                        <th width="80">Foto</th>
                        <th width="90">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($logs)): ?>
                        <?php $no = 1; ?>
                        <?php foreach ($logs as $log): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= date('H:i', strtotime($log['created_at'])) ?></td>
                            <td><?= esc($log['keterangan']) ?></td>
                            <td><?= number_format($log['arus'], 2) ?> A</td>
                            <td><?= number_format($log['constanta'], 0) ?></td>
                           <!-- Di dalam loop tabel riwayat, update kolom status: -->
<td>
    <?php if (isset($log['error_percent']) && isset($log['class_meter'])): ?>
        <?php
        $absError = abs($log['error_percent']);
        $classMeter = $log['class_meter'];
        $statusFinal = $log['status_final'] ?? 'BELUM_DIHITUNG';
        
        // Tampilkan status final
        if ($statusFinal === 'BAIK') {
            echo '<span class="badge bg-success">BAIK</span>';
        } elseif ($statusFinal === 'TIDAK STABIL') {
            echo '<span class="badge bg-warning">TIDAK STABIL</span>';
        } elseif ($statusFinal === 'BURUK') {
            echo '<span class="badge bg-danger">BURUK</span>';
        } else {
            echo '<span class="badge bg-secondary">' . $statusFinal . '</span>';
        }
        ?>
        <br>
        <small class="text-muted">Kelas: <?= $classMeter ?>%</small>
    <?php endif; ?>
</td>
                            <td>
                                <?php if (!empty($log['photos']) && $log['photos'] !== 'null'): ?>
                                    <?php 
                                    $photos = json_decode($log['photos'], true);
                                    if (is_array($photos) && count($photos) > 0): 
                                    ?>
                                        <div class="d-flex align-items-center">
                                            <span class="badge bg-info me-1"><?= count($photos) ?></span>
                                            <i class="bi bi-image text-primary"></i>
                                        </div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <!-- Tombol Export Single PDF -->
                                    <a href="<?= base_url('export/pdf/' . $log['id']) ?>" 
                                       class="btn btn-outline-danger btn-sm" 
                                       target="_blank"
                                       title="Export PDF">
                                        <i class="bi bi-file-pdf"></i>
                                    </a>
                                    
                                    <!-- Tombol Hapus -->
                                    <button class="btn btn-outline-danger btn-sm" 
                                            onclick="if(confirm('Hapus data ini?')) window.location='<?= site_url('kwh/delete/' . $log['id']) ?>'"
                                            title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Belum ada data pengukuran
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
        
        <!-- Spacer for bottom nav -->
        <div style="height: 20px;"></div>
    </div>

    <!-- Bottom Navigation -->
    <nav class="bottom-nav">
        <div class="container">
            <div class="row text-center">
                <div class="col-3">
                    <a href="javascript:void(0)" onclick="countBlink()" class="nav-btn">
                        <div class="icon text-success">
                            <i class="bi bi-plus-circle-fill"></i>
                        </div>
                        <div class="label">Hitung</div>
                    </a>
                </div>
                <div class="col-3">
                    <a href="javascript:void(0)" onclick="toggleTimer()" class="nav-btn">
                        <div class="icon text-primary" id="navTimerIcon">
                            <i class="bi bi-play-circle-fill"></i>
                        </div>
                        <div class="label" id="navTimerLabel">Start</div>
                    </a>
                </div>
                <div class="col-3">
                    <a href="javascript:void(0)" onclick="calculateSelected()" class="nav-btn">
                        <div class="icon text-warning">
                            <i class="bi bi-calculator-fill"></i>
                        </div>
                        <div class="label">Hitung</div>
                    </a>
                </div>
                <div class="col-3">
                    <a href="javascript:void(0)" onclick="document.getElementById('saveForm').submit()" class="nav-btn">
                        <div class="icon text-info">
                            <i class="bi bi-save-fill"></i>
                        </div>
                        <div class="label">Simpan</div>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // ========== VARIABLES GLOBAL ==========
        let blinkData = [];
        let timer = 0;
        let timerInterval = null;
        let isTimerRunning = false;
        let isAutoMode = true;
        let selectedBlink = 1;
        
        // ========== ELEMENTS ==========
        const tasbihBtn = document.getElementById('tasbihBtn');
        const tasbihCounter = document.getElementById('tasbihCounter');
        const timerDisplay = document.getElementById('timerDisplay');
        const blinkRateDisplay = document.getElementById('blinkRateDisplay');
        const statusBadge = document.getElementById('statusBadge');
        const blinkList = document.getElementById('blinkList');
        const noBlinkMessage = document.getElementById('noBlinkMessage');
        const selectedBlinkInput = document.getElementById('selectedBlinkInput');
        
        // Result elements
        const p1Result = document.getElementById('p1Result');
        const p2Result = document.getElementById('p2Result');
        const errorResult = document.getElementById('errorResult');
        const errorCard = document.getElementById('errorCard');
        const errorStatus = document.getElementById('errorStatus');
        
        // Detail elements
        const detailCount = document.getElementById('detailCount');
        const detailTime = document.getElementById('detailTime');
        const detailRate = document.getElementById('detailRate');
        const detailConstanta = document.getElementById('detailConstanta');
        
        // Input elements
        const arusInput = document.getElementById('arusInput');
        const teganganInput = document.getElementById('teganganInput');
        const cosphiInput = document.getElementById('cosphiInput');
        const constantaInput = document.getElementById('constantaInput');
        const autoModeSwitch = document.getElementById('autoModeSwitch');
        
        // Form inputs
        const inputCount = document.getElementById('inputCount');
        const inputDuration = document.getElementById('inputDuration');
        const inputArus = document.getElementById('inputArus');
        const inputTegangan = document.getElementById('inputTegangan');
        const inputCosphi = document.getElementById('inputCosphi');
        const inputConstanta = document.getElementById('inputConstanta');
        const inputBlinkData = document.getElementById('inputBlinkData');
        const inputSelectedBlink = document.getElementById('inputSelectedBlink');
        
        // ========== TIMER FUNCTIONS ==========
        function startTimer() {
            if (isTimerRunning) return;
            
            isTimerRunning = true;
            const startTime = Date.now() - (timer * 1000);
            
            timerInterval = setInterval(() => {
                timer = (Date.now() - startTime) / 1000;
                timerDisplay.textContent = timer.toFixed(1);
                
                // Update blink rate
                if (blinkData.length > 0) {
                    const lastBlink = blinkData[blinkData.length - 1];
                    const blinkRate = lastBlink.count / timer;
                    blinkRateDisplay.textContent = blinkRate.toFixed(3);
                }
                
                // Update UI
                statusBadge.textContent = 'REC';
                statusBadge.className = 'badge bg-danger';
                
                const timerBtn = document.getElementById('timerBtn');
                const navTimerIcon = document.getElementById('navTimerIcon');
                const navTimerLabel = document.getElementById('navTimerLabel');
                
                timerBtn.innerHTML = '<i class="bi bi-pause-fill me-2"></i> STOP';
                timerBtn.className = 'btn btn-danger';
                
                navTimerIcon.innerHTML = '<i class="bi bi-pause-circle-fill"></i>';
                navTimerLabel.textContent = 'Stop';
                
            }, 100);
        }
        
        function stopTimer() {
            if (!isTimerRunning) return;
            
            isTimerRunning = false;
            clearInterval(timerInterval);
            timerInterval = null;
            
            statusBadge.textContent = 'SIAP';
            statusBadge.className = 'badge bg-success';
            
            const timerBtn = document.getElementById('timerBtn');
            const navTimerIcon = document.getElementById('navTimerIcon');
            const navTimerLabel = document.getElementById('navTimerLabel');
            
            timerBtn.innerHTML = '<i class="bi bi-play-fill me-2"></i> START PENGUKURAN';
            timerBtn.className = 'btn btn-success';
            
            navTimerIcon.innerHTML = '<i class="bi bi-play-circle-fill"></i>';
            navTimerLabel.textContent = 'Start';
        }
        
        function toggleTimer() {
            if (isTimerRunning) {
                stopTimer();
            } else {
                startTimer();
            }
        }
        
        // ========== BLINK COUNTING ==========
        function countBlink() {
            blinkData.push({
                time: timer,
                count: blinkData.length + 1
            });
            
            updateBlinkList();
            updateSelectedBlinkInput();
            
            tasbihBtn.classList.add('pulse');
            setTimeout(() => tasbihBtn.classList.remove('pulse'), 500);
            
            tasbihCounter.textContent = blinkData.length;
            
            // Auto start timer jika mode auto
            if (!isTimerRunning && isAutoMode && blinkData.length === 1) {
                startTimer();
            }
            
            // Vibrate jika didukung
            if ('vibrate' in navigator) {
                navigator.vibrate(50);
            }
            
            // Auto calculate
            if (blinkData.length > 0) {
                calculateSelected();
            }
        }
        
        // ========== CALCULATION FUNCTIONS ==========
        function calculateSelected() {
    if (blinkData.length === 0) return;
    
    selectedBlink = Math.min(selectedBlink, blinkData.length);
    selectedBlink = Math.max(1, selectedBlink);
    selectedBlinkInput.value = selectedBlink;
    
    const selectedData = blinkData[selectedBlink - 1];
    const count = selectedData.count;
    const duration = selectedData.time;
    
    const arus = parseFloat(arusInput.value) || 0;
    const tegangan = parseFloat(teganganInput.value) || 220;
    const cosphi = parseFloat(cosphiInput.value) || 0.85;
    const constanta = parseFloat(constantaInput.value) || 1600;
    const classMeter = parseFloat(document.getElementById('classMeterInput').value) || 1.0;
    
    if (arus === 0 || constanta === 0) {
        alert('Arus dan Constanta harus diisi!');
        return;
    }
    
    const blinkPerSecond = count / duration;
    const p1_kw = (3600 * blinkPerSecond) / constanta;
    const p2_kw = (tegangan * arus * cosphi) / 1000;
    
    let error_percent = 0;
    if (p2_kw !== 0) {
        error_percent = ((p1_kw - p2_kw) / p2_kw) * 100;
    }
    
    // Hitung status final berdasarkan logika baru
    const absError = Math.abs(error_percent);
    let finalStatus = '';
    let finalStatusClass = '';
    let finalStatusText = '';
    let cardColor = '';
    
    // LOGIKA BARU:
    // 1. Jika error > 5% -> SELALU BURUK (untuk semua kelas)
    // 2. Jika error < class_meter -> TIDAK STABIL
    // 3. Jika error >= class_meter -> BAIK
    
    if (absError > 5) {
        // SELALU BURUK jika error > 5%
        finalStatus = 'BURUK';
        finalStatusClass = 'bg-danger';
        finalStatusText = 'BURUK (Error > 5%)';
        cardColor = 'border-danger';
    } else if (absError < classMeter) {
        // Error < class meter -> TIDAK STABIL
        finalStatus = 'TIDAK STABIL';
        finalStatusClass = 'bg-warning';
        finalStatusText = 'TIDAK STABIL';
        cardColor = 'border-warning';
    } else {
        // Error >= class meter -> BAIK
        finalStatus = 'BAIK';
        finalStatusClass = 'bg-success';
        finalStatusText = 'BAIK';
        cardColor = 'border-success';
    }
    
    // Update UI dengan status lama (untuk referensi)
    let oldStatusClass = '';
    let oldStatusText = '';
    
    if (absError <= 2) {
        oldStatusClass = 'bg-success';
        oldStatusText = 'BAIK (lama)';
    } else if (absError <= 5) {
        oldStatusClass = 'bg-warning';
        oldStatusText = 'WARNING (lama)';
    } else {
        oldStatusClass = 'bg-danger';
        oldStatusText = 'BURUK (lama)';
    }
    
    updateResults({
        count: count,
        duration: duration,
        blink_per_second: blinkPerSecond,
        p1_kw: p1_kw,
        p2_kw: p2_kw,
        error_percent: error_percent,
        abs_error: absError,
        constanta: constanta,
        class_meter: classMeter,
        final_status: finalStatus,
        final_status_class: finalStatusClass,
        final_status_text: finalStatusText,
        card_color: cardColor,
        old_status_class: oldStatusClass,
        old_status_text: oldStatusText
    });
    
    updateFormInputs(count, duration, arus, tegangan, cosphi, constanta, classMeter);
}
        
        function calculateP2() {
            const arus = parseFloat(arusInput.value) || 0;
            const tegangan = parseFloat(teganganInput.value) || 220;
            const cosphi = parseFloat(cosphiInput.value) || 0.85;
            
            const p2_kw = (tegangan * arus * cosphi) / 1000;
            p2Result.textContent = p2_kw.toFixed(3);
        }
        
        function updateResults(data) {
    p1Result.textContent = data.p1_kw.toFixed(3);
    p2Result.textContent = data.p2_kw.toFixed(3);
    errorResult.textContent = data.error_percent.toFixed(2);
    
    // Update detail
    detailCount.textContent = data.count;
    detailTime.textContent = data.duration.toFixed(1);
    detailRate.textContent = data.blink_per_second.toFixed(3);
    detailConstanta.textContent = data.constanta;
    detailClass.textContent = data.class_meter;
    detailAbsError.textContent = data.abs_error.toFixed(2);
    
    // Update status lama
    errorStatus.textContent = data.old_status_text;
    errorStatus.className = `badge ${data.old_status_class}`;
    
    // Update status final (BARU)
    document.getElementById('finalStatusResult').textContent = data.final_status;
    document.getElementById('finalStatusBadge').textContent = data.final_status_text;
    document.getElementById('finalStatusBadge').className = `badge ${data.final_status_class}`;
    document.getElementById('classMeterLabel').textContent = `Kelas: ${data.class_meter}%`;
    
    // Update card color
    const finalStatusCard = document.getElementById('finalStatusCard');
    finalStatusCard.className = `card border ${data.card_color}`;
}
        
        // ========== DISPLAY FUNCTIONS ==========
        function updateBlinkList() {
            if (blinkData.length === 0) {
                noBlinkMessage.style.display = 'block';
                blinkList.innerHTML = '';
                return;
            }
            
            noBlinkMessage.style.display = 'none';
            
            let html = '';
            blinkData.forEach((blink, index) => {
                const isSelected = (index + 1) === selectedBlink;
                const blinkRate = (blink.count / blink.time).toFixed(3);
                
                html += `
                    <div class="blink-item ${isSelected ? 'active' : ''}" 
                         onclick="selectBlink(${index + 1})">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>Kedipan ${blink.count}</strong>
                                <div class="small">Waktu: ${blink.time.toFixed(1)} detik</div>
                            </div>
                            <div class="text-end">
                                <div class="small">${blinkRate}/detik</div>
                                ${isSelected ? '<i class="bi bi-check-circle"></i>' : ''}
                            </div>
                        </div>
                    </div>
                `;
            });
            
            blinkList.innerHTML = html;
        }
        
        function selectBlink(blinkNumber) {
            selectedBlink = blinkNumber;
            selectedBlinkInput.value = blinkNumber;
            updateBlinkList();
            calculateSelected();
        }
        
        function updateSelectedBlinkInput() {
            selectedBlinkInput.disabled = blinkData.length === 0;
            selectedBlinkInput.max = blinkData.length;
            
            if (blinkData.length > 0 && selectedBlink > blinkData.length) {
                selectedBlink = blinkData.length;
                selectedBlinkInput.value = blinkData.length;
            }
        }
        
        // ========== CAMERA FUNCTIONS ==========
        function takePhoto() {
            // Cek apakah di mobile device
            if (/Android|iPhone|iPad|iPod/i.test(navigator.userAgent)) {
                // Untuk mobile, gunakan input file dengan capture
                const cameraInput = document.createElement('input');
                cameraInput.type = 'file';
                cameraInput.accept = 'image/*';
                cameraInput.capture = 'environment'; // environment = kamera belakang
                
                cameraInput.onchange = function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        addPhotoToPreview(file);
                        addPhotoToFileInput(file);
                    }
                };
                
                cameraInput.click();
            } else {
                // Untuk desktop, gunakan webcam
                alert('Di desktop, gunakan file upload biasa atau pasang webcam');
                document.getElementById('photoInput').click();
            }
        }
        
        function addPhotoToPreview(file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('photoPreview');
                const photoId = 'photo_' + Date.now();
                
                const imgContainer = document.createElement('div');
                imgContainer.className = 'photo-preview-item';
                imgContainer.id = photoId;
                
                imgContainer.innerHTML = `
                    <img src="${e.target.result}" alt="Preview">
                    <button type="button" class="btn btn-danger btn-sm remove-photo-btn" onclick="removePhotoFromPreview('${photoId}')">
                        <i class="bi bi-x"></i>
                    </button>
                `;
                
                preview.appendChild(imgContainer);
            };
            reader.readAsDataURL(file);
        }
        
        function addPhotoToFileInput(file) {
            const input = document.getElementById('photoInput');
            const dt = new DataTransfer();
            
            // Tambah file yang sudah ada
            for (let i = 0; i < input.files.length; i++) {
                dt.items.add(input.files[i]);
            }
            
            // Tambah file baru
            dt.items.add(file);
            
            input.files = dt.files;
        }
        
        function removePhotoFromPreview(photoId) {
            const element = document.getElementById(photoId);
            if (element) {
                element.remove();
                
                // Juga hapus dari file input
                const input = document.getElementById('photoInput');
                const dt = new DataTransfer();
                const files = input.files;
                
                for (let i = 0; i < files.length; i++) {
                    if (files[i].name !== photoId) { // Simplified check
                        dt.items.add(files[i]);
                    }
                }
                
                input.files = dt.files;
            }
        }
        
        // Handle file input change
        document.getElementById('photoInput').addEventListener('change', function(e) {
            const files = e.target.files;
            const preview = document.getElementById('photoPreview');
            
            // Clear existing preview
            preview.innerHTML = '';
            
            // Add new previews
            for (let i = 0; i < files.length; i++) {
                addPhotoToPreview(files[i]);
            }
        });
        
        // ========== FORM FUNCTIONS ==========
       function updateFormInputs(count, duration, arus, tegangan, cosphi, constanta, classMeter) {
    inputCount.value = count;
    inputDuration.value = duration.toFixed(1);
    inputArus.value = arus;
    inputTegangan.value = tegangan;
    inputCosphi.value = cosphi;
    inputConstanta.value = constanta;
    document.getElementById('inputClassMeter').value = classMeter;
    inputBlinkData.value = JSON.stringify(blinkData);
    inputSelectedBlink.value = selectedBlink;
}
        
        function saveData() {
            if (blinkData.length === 0) {
                alert('Belum ada data kedipan!');
                return;
            }
            
            // Trigger form validation dan submit
            const keteranganInput = document.querySelector('input[name="keterangan"]');
            if (!keteranganInput.value.trim()) {
                keteranganInput.focus();
                alert('Isi keterangan/lokasi terlebih dahulu!');
                return;
            }
            
            // Update semua hidden inputs
            calculateSelected();
            
            // Submit form
            document.getElementById('saveForm').submit();
        }
        
        // Form submit handler
        document.getElementById('saveForm').addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('submitBtn');
            const originalText = submitBtn.innerHTML;
            
            // Show loading
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i> Menyimpan...';
            submitBtn.disabled = true;
            
            // Validation
            const arus = parseFloat(arusInput.value) || 0;
            const constanta = parseFloat(constantaInput.value) || 0;
            
            if (arus === 0) {
                e.preventDefault();
                alert('Arus harus diisi!');
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                return;
            }
            
            if (constanta === 0) {
                e.preventDefault();
                alert('Constanta harus diisi!');
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                return;
            }
            
            // Auto dismiss alerts setelah 5 detik
            setTimeout(() => {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(alert => {
                    const bsAlert = new bootstrap.Alert(alert);
                    setTimeout(() => bsAlert.close(), 5000);
                });
            }, 1000);
        });
        
        // ========== RESET FUNCTIONS ==========
        function resetBlinkData() {
            if (confirm('Hapus semua data kedipan?')) {
                blinkData = [];
                timer = 0;
                selectedBlink = 1;
                
                tasbihCounter.textContent = '0';
                timerDisplay.textContent = '0.0';
                blinkRateDisplay.textContent = '0.0';
                
                stopTimer();
                updateBlinkList();
                updateSelectedBlinkInput();
                
                p1Result.textContent = '0.000';
                errorResult.textContent = '0.00';
                errorCard.className = 'card border';
                errorStatus.textContent = '-';
                errorStatus.className = 'badge';
                
                statusBadge.textContent = 'SIAP';
                statusBadge.className = 'badge bg-success';
            }
        }
        function updateClassMeter() {
    const classMeterInput = document.getElementById('classMeterInput');
    const classMeterValue = parseFloat(classMeterInput.value);
    
    // Update hidden input
    document.getElementById('inputClassMeter').value = classMeterValue;
    
    // Update label display
    document.getElementById('classMeterLabel').textContent = `Kelas: ${classMeterValue}%`;
    document.getElementById('detailClass').textContent = classMeterValue;
    
    // Recalculate jika sudah ada data
    if (blinkData.length > 0) {
        calculateSelected();
    }
}
        
        // ========== EVENT LISTENERS ==========
        function init() {
            // Update P2 saat input berubah
            arusInput.addEventListener('input', calculateP2);
            teganganInput.addEventListener('input', calculateP2);
            cosphiInput.addEventListener('input', calculateP2);
            document.getElementById('classMeterInput').addEventListener('change', updateClassMeter);
            
            // Auto mode switch
            autoModeSwitch.addEventListener('change', function() {
                isAutoMode = this.checked;
            });
            
            // Keyboard shortcuts
            document.addEventListener('keydown', function(e) {
                if (e.code === 'Space') {
                    e.preventDefault();
                    countBlink();
                }
                if (e.code === 'Enter') {
                    e.preventDefault();
                    toggleTimer();
                }
                if (e.code === 'Escape') {
                    e.preventDefault();
                    stopTimer();
                }
                if (e.code === 'KeyS' && e.ctrlKey) {
                    e.preventDefault();
                    saveData();
                }
            });
            
            // Touch events for tasbih button
            tasbihBtn.addEventListener('touchstart', function(e) {
                e.preventDefault();
                this.style.transform = 'scale(0.95)';
            });
            
            tasbihBtn.addEventListener('touchend', function(e) {
                e.preventDefault();
                this.style.transform = 'scale(1)';
                countBlink();
            });
            
            // Initial calculations
            calculateP2();
        }
        
        // ========== INITIALIZE ==========
        window.addEventListener('DOMContentLoaded', init);
    </script>
</body>
</html>