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
        
        /* Required Field Indicator */
        .required-field::after {
            content: " *";
            color: #dc3545;
        }
        
        .form-control:required {
            border-left: 4px solid #dc3545;
        }
        
        .form-control:required:valid {
            border-left: 4px solid #198754;
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
        
        .history-table table {
            margin-bottom: 0;
        }
        
        .history-table thead th {
            position: sticky;
            top: 0;
            background: #f8f9fa;
            z-index: 10;
            vertical-align: middle;
        }
        
        .history-table tbody td {
            vertical-align: middle;
        }
        
        /* Mode Content */
        .mode-content {
            display: none;
        }
        
        .mode-content.active {
            display: block;
        }
        
        /* Mode 2 Styles */
        .phase-card {
            transition: all 0.3s;
        }
        
        .phase-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }
        
        /* Mode Switcher */
        .mode-switcher .btn {
            min-width: 140px;
        }
        
        /* Action Buttons */
        .btn-group-sm > .btn {
            padding: 0.25rem 0.5rem;
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

    <!-- Main Content -->
    <div class="app-container">
        <!-- Header User Info -->
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
        
        <!-- Mode Switcher -->
        <div class="card mb-3">
            <div class="card-body py-2">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="fw-bold">Mode Perhitungan:</span>
                        <span class="badge <?= ($mode === 'mode1') ? 'bg-primary' : 'bg-info' ?> ms-2">
                            <?= ($mode === 'mode1') ? 'MODE 1' : 'MODE 2' ?>
                        </span>
                    </div>
                    <div class="btn-group mode-switcher">
                        <a href="<?= base_url('kwh/switchMode?mode=mode1') ?>" 
                           class="btn <?= ($mode === 'mode1') ? 'btn-primary' : 'btn-outline-primary' ?>">
                            <i class="bi bi-1-circle me-1"></i> Mode 1 (Kedipan)
                        </a>
                        <a href="<?= base_url('kwh/switchMode?mode=mode2') ?>" 
                           class="btn <?= ($mode === 'mode2') ? 'btn-primary' : 'btn-outline-primary' ?>">
                            <i class="bi bi-2-circle me-1"></i> Mode 2 (3 Phase)
                        </a>
                    </div>
                </div>
                
                <?php if ($mode === 'mode2'): ?>
                <div class="alert alert-info mt-2 mb-0 py-2">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Mode 2 Aktif:</strong> 
                    Input manual P1 dan nilai Pr, Ps, Pt untuk perhitungan 3 phase
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- MODE 1 CONTENT (Kedipan) -->
        <div id="mode1Content" class="mode-content <?= ($mode === 'mode1') ? 'active' : '' ?>">
            <!-- SECTION 1: PARAMETER METERAN -->
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
                                    <span class="badge bg-info">Logika Baru:</span> 
                                    Error ≠ Kelas = <span class="text-danger">DI LUAR KELAS METER</span> | 
                                    Error = Kelas = <span class="text-success">BAIK</span>
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
                                <button class="btn btn-success" onclick="saveDataMode1()">
                                    <i class="bi bi-save me-2"></i> Simpan Perhitungan
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- MODE 2 CONTENT (3 Phase Input) -->
        <div id="mode2Content" class="mode-content <?= ($mode === 'mode2') ? 'active' : '' ?>">
            <!-- SECTION 1: INPUT DATA 3 PHASE -->
            <div class="card mb-3">
                <div class="card-header">
                    <i class="bi bi-lightning-charge me-2"></i>INPUT DATA 3 PHASE
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <!-- Class Meter -->
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Kelas Meter</label>
                            <div class="input-group input-group-custom">
                                <select class="form-select" id="classMeterInputMode2" onchange="calculateMode2()">
                                    <option value="1.0">Kelas 1 (1.0%) - Meter Umum</option>
                                    <option value="0.5">Kelas 0.5 (0.5%) - Meter Industri</option>
                                    <option value="0.2">Kelas 0.2 (0.2%) - Meter Presisi</option>
                                </select>
                                <span class="input-group-text"><i class="bi bi-lightning-charge"></i></span>
                            </div>
                        </div>
                        
                        <!-- P1 Input -->
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">P1 (kWh Meter)</label>
                            <div class="input-group input-group-custom">
                                <input type="number" class="form-control" id="p1InputMode2" step="0.001" value="15.000" oninput="calculateMode2()">
                                <span class="input-group-text">kW</span>
                            </div>
                            <div class="form-text">Nilai P1 dari kWh meter</div>
                        </div>
                        
                        <!-- P2 Total Display -->
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">P2 Total (Pr+Ps+Pt)</label>
                            <div class="input-group input-group-custom bg-light">
                                <input type="text" class="form-control bg-light" id="p2TotalDisplayMode2" value="14.586" readonly>
                                <span class="input-group-text">kW</span>
                            </div>
                            <div class="form-text">Otomatis terhitung</div>
                        </div>
                        
                        <!-- 3 Phase Inputs -->
                        <div class="col-12">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="card border-primary phase-card">
                                        <div class="card-header py-2 bg-primary text-white">
                                            <i class="bi bi-lightning me-1"></i> Phase R (Pr)
                                        </div>
                                        <div class="card-body">
                                            <div class="input-group">
                                                <input type="number" class="form-control" id="prInputMode2" step="0.001" value="4.675" oninput="calculateMode2()">
                                                <span class="input-group-text">kW</span>
                                            </div>
                                            <div class="form-text">Daya Phase R</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="card border-success phase-card">
                                        <div class="card-header py-2 bg-success text-white">
                                            <i class="bi bi-lightning me-1"></i> Phase S (Ps)
                                        </div>
                                        <div class="card-body">
                                            <div class="input-group">
                                                <input type="number" class="form-control" id="psInputMode2" step="0.001" value="4.488" oninput="calculateMode2()">
                                                <span class="input-group-text">kW</span>
                                            </div>
                                            <div class="form-text">Daya Phase S</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="card border-warning phase-card">
                                        <div class="card-header py-2 bg-warning text-dark">
                                            <i class="bi bi-lightning me-1"></i> Phase T (Pt)
                                        </div>
                                        <div class="card-body">
                                            <div class="input-group">
                                                <input type="number" class="form-control" id="ptInputMode2" step="0.001" value="5.423" oninput="calculateMode2()">
                                                <span class="input-group-text">kW</span>
                                            </div>
                                            <div class="form-text">Daya Phase T</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Info Panel -->
                        <div class="col-12">
                            <div class="alert alert-info mt-2 py-2">
                                <div class="row small">
                                    <div class="col-md-3">
                                        <strong>Rumus:</strong> P2 = Pr + Ps + Pt
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Contoh:</strong> 4.675 + 4.488 + 5.423
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Total:</strong> <span id="p2ExampleMode2">14.586</span> kW
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Error:</strong> ((P1 - P2) / P2) × 100%
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Tombol Hitung -->
                        <div class="col-12">
                            <button class="btn btn-success w-100" onclick="calculateMode2()">
                                <i class="bi bi-calculator me-2"></i> HITUNG ERROR METER
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- HASIL PERHITUNGAN ERROR (SAMA UNTUK KEDUA MODE) -->
        <div class="card mb-3">
            <div class="card-header">
                <i class="bi bi-graph-up me-2"></i>HASIL PERHITUNGAN ERROR
                <span class="badge <?= ($mode === 'mode1') ? 'bg-primary' : 'bg-info' ?> float-end">
                    <?= ($mode === 'mode1') ? 'MODE 1' : 'MODE 2' ?>
                </span>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <!-- P1 Result -->
                    <div class="col-md-3">
                        <div class="card bg-light border">
                            <div class="card-body text-center">
                                <h6 class="card-title text-muted">P1 (kWh Meter)</h6>
                                <div class="big-number text-primary" id="finalP1Result">0.000</div>
                                <small class="text-muted">kW</small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- P2 Result -->
                    <div class="col-md-3">
                        <div class="card bg-light border">
                            <div class="card-body text-center">
                                <h6 class="card-title text-muted" id="p2ResultLabel">
                                    <?= ($mode === 'mode1') ? 'P2 (Pembanding)' : 'P2 (Pr+Ps+Pt)' ?>
                                </h6>
                                <div class="big-number text-success" id="finalP2Result">
                                    <?= ($mode === 'mode1') ? '0.935' : '14.586' ?>
                                </div>
                                <small class="text-muted">kW</small>
                                <?php if ($mode === 'mode2'): ?>
                                <div class="mt-1">
                                    <small class="text-muted">3 Phase Total</small>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Error Result -->
                    <div class="col-md-3">
                        <div class="card bg-light border">
                            <div class="card-body text-center">
                                <h6 class="card-title text-muted">Error Meter</h6>
                                <div class="big-number" id="finalErrorResult">0.00</div>
                                <small class="text-muted">%</small>
                                <div class="mt-2">
                                    <span class="badge bg-secondary" id="finalErrorStatus">-</span>
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
                                <small class="text-muted" id="finalClassMeterLabel">Kelas: 1.0%</small>
                                <div class="mt-2">
                                    <span class="badge" id="finalStatusBadge">-</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Detail Info -->
                    <div class="col-12">
                        <div class="alert alert-info py-2">
                            <div class="row small">
                                <div class="col-md-2">
                                    <strong>Mode:</strong> <span id="detailMode"><?= ($mode === 'mode1') ? 'Kedipan' : '3 Phase' ?></span>
                                </div>
                                <div class="col-md-2">
                                    <strong>Kelas Meter:</strong> <span id="detailClass">1.0</span>%
                                </div>
                                <div class="col-md-2">
                                    <strong>P1:</strong> <span id="detailP1">0.000</span> kW
                                </div>
                                <div class="col-md-2">
                                    <strong>P2:</strong> <span id="detailP2">0.000</span> kW
                                </div>
                                <div class="col-md-2">
                                    <strong>Error:</strong> <span id="detailError">0.00</span>%
                                </div>
                                <div class="col-md-2">
                                    <strong>Status:</strong> <span id="detailStatus">-</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- SIMPAN DATA PENGUKURAN -->
        <div class="card mb-3">
            <div class="card-header">
                <i class="bi bi-save me-2"></i>SIMPAN DATA PENGUKURAN
            </div>
            <div class="card-body">
                <!-- Form untuk Mode 1 -->
                <form method="post" action="<?= base_url('kwh/save') ?>" enctype="multipart/form-data" id="saveFormMode1" 
                      style="<?= ($mode === 'mode1') ? '' : 'display:none;' ?>">
                    <input type="hidden" name="mode_type" value="mode1">
                    <div class="row g-2">
                        <!-- NAMA PELANGGAN -->
                        <div class="col-md-6">
                            <label class="form-label required-field">Nama Pelanggan:</label>
                            <input type="text" name="nama_pelanggan" class="form-control" 
                                   placeholder="Contoh: Bapak Budi Santoso" required>
                            <div class="form-text">Wajib diisi</div>
                        </div>
                        
                        <!-- ID PELANGGAN -->
                        <div class="col-md-6">
                            <label class="form-label required-field">ID Pelanggan / No. Meter:</label>
                            <input type="text" name="id_pelanggan" class="form-control" 
                                   placeholder="Contoh: 1234567890" required>
                            <div class="form-text">Wajib diisi</div>
                        </div>
                        
                        <!-- Keterangan/Lokasi -->
                        <div class="col-md-12">
                            <label class="form-label">Alamat / Lokasi:</label>
                            <input type="text" name="keterangan" class="form-control" 
                                   placeholder="Contoh: Jl. Merdeka No. 123, RT 01/RW 05">
                            <div class="form-text">Opsional</div>
                        </div>
                        
                        <!-- Upload Foto -->
                        <div class="col-md-12">
                            <label class="form-label">Upload Foto:</label>
                            <div class="input-group mb-2">
                                <button type="button" class="btn btn-outline-primary" onclick="takePhoto()">
                                    <i class="bi bi-camera-fill me-2"></i> Ambil Foto
                                </button>
                                <input type="file" name="photos[]" class="form-control" multiple accept="image/*" id="photoInputMode1">
                            </div>
                            <div class="form-text">Klik "Ambil Foto" untuk foto langsung dari kamera</div>
                            
                            <!-- Photo Preview -->
                            <div class="photo-preview-container" id="photoPreviewMode1"></div>
                        </div>
                        
                        <!-- Hidden Inputs Mode 1 -->
                        <input type="hidden" name="class_meter" id="inputClassMeterMode1">
                        <input type="hidden" name="count" id="inputCount">
                        <input type="hidden" name="duration" id="inputDuration">
                        <input type="hidden" name="arus" id="inputArus">
                        <input type="hidden" name="tegangan" id="inputTegangan">
                        <input type="hidden" name="cosphi" id="inputCosphi">
                        <input type="hidden" name="constanta" id="inputConstanta">
                        <input type="hidden" name="blink_data" id="inputBlinkData">
                        <input type="hidden" name="selected_blink" id="inputSelectedBlink">
                        
                        <div class="col-12 mt-3">
                            <button type="submit" class="btn btn-primary w-100" id="submitBtnMode1">
                                <i class="bi bi-check-lg me-2"></i> SIMPAN DATA KEDIPAN
                            </button>
                        </div>
                    </div>
                </form>
                
                <!-- Form untuk Mode 2 -->
                <form method="post" action="<?= base_url('kwh/save') ?>" enctype="multipart/form-data" id="saveFormMode2"
                      style="<?= ($mode === 'mode2') ? '' : 'display:none;' ?>">
                    <input type="hidden" name="mode_type" value="mode2">
                    <div class="row g-2">
                        <!-- NAMA PELANGGAN -->
                        <div class="col-md-6">
                            <label class="form-label required-field">Nama Pelanggan:</label>
                            <input type="text" name="nama_pelanggan" class="form-control" 
                                   placeholder="Contoh: PT. Industri Maju Jaya" required>
                            <div class="form-text">Wajib diisi</div>
                        </div>
                        
                        <!-- ID PELANGGAN -->
                        <div class="col-md-6">
                            <label class="form-label required-field">ID Pelanggan / No. Meter:</label>
                            <input type="text" name="id_pelanggan" class="form-control" 
                                   placeholder="Contoh: 0987654321" required>
                            <div class="form-text">Wajib diisi</div>
                        </div>
                        
                        <!-- Keterangan/Lokasi -->
                        <div class="col-md-12">
                            <label class="form-label">Alamat / Lokasi:</label>
                            <input type="text" name="keterangan" class="form-control" 
                                   placeholder="Contoh: Kawasan Industri Mekar Sari, Blok A-10">
                            <div class="form-text">Opsional</div>
                        </div>
                        
                        <!-- Upload Foto -->
                        <div class="col-md-12">
                            <label class="form-label">Upload Foto:</label>
                            <div class="input-group mb-2">
                                <button type="button" class="btn btn-outline-primary" onclick="takePhotoMode2()">
                                    <i class="bi bi-camera-fill me-2"></i> Ambil Foto
                                </button>
                                <input type="file" name="photos[]" class="form-control" multiple accept="image/*" id="photoInputMode2">
                            </div>
                            <div class="form-text">Klik "Ambil Foto" untuk foto langsung dari kamera</div>
                            
                            <!-- Photo Preview -->
                            <div class="photo-preview-container" id="photoPreviewMode2"></div>
                        </div>
                        
                        <!-- Hidden Inputs untuk Mode 2 -->
                        <input type="hidden" name="class_meter" id="inputClassMeterMode2">
                        <input type="hidden" name="p1_input" id="inputP1Mode2">
                        <input type="hidden" name="pr_input" id="inputPrMode2">
                        <input type="hidden" name="ps_input" id="inputPsMode2">
                        <input type="hidden" name="pt_input" id="inputPtMode2">
                        
                        <div class="col-12 mt-3">
                            <button type="submit" class="btn btn-primary w-100" id="submitBtnMode2">
                                <i class="bi bi-check-lg me-2"></i> SIMPAN DATA 3 PHASE
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- RIWAYAT PENGUKURAN (SUDAH DIPERBAIKI) -->
<div class="card mb-5" id="riwayat">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-clock-history me-2"></i>RIWAYAT PENGUKURAN</span>
        <a href="<?= base_url('kwh/export') ?>" class="btn btn-sm btn-outline-success">
            <i class="bi bi-download me-1"></i> Export Excel
        </a>
    </div>
    <div class="card-body p-0">
        <div class="history-table">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="40">#</th>
                        <th width="160">Tanggal & Waktu</th>
                        <th>Nama Pelanggan</th>
                        <th>ID Pelanggan</th>
                        <th>Lokasi</th>
                        <th width="80">P1 (kW)</th>
                        <th width="80">P2 (kW)</th>
                        <th width="80">Error</th>
                        <th width="100">Status</th>
                        <th width="80">Foto</th>
                        <th width="90">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($logs)): ?>
                        <?php $no = 1; ?>
                        <?php foreach ($logs as $log): ?>
                        <?php
                        // DEBUG: Uncomment untuk lihat data
                        // echo '<pre>'; print_r($log); echo '</pre>';
                        ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td>
                                <small class="text-muted d-block">
                                    <?= isset($log['created_at']) ? date('d/m/Y', strtotime($log['created_at'])) : '-' ?>
                                </small>
                                <strong>
                                    <?= isset($log['created_at']) ? date('H:i:s', strtotime($log['created_at'])) : '-' ?>
                                </strong>
                            </td>
                            <td>
                                <?= isset($log['nama_pelanggan']) ? esc($log['nama_pelanggan']) : 
                                   (isset($log['keterangan']) && strpos($log['keterangan'], 'Pelanggan:') !== false ? 
                                    substr($log['keterangan'], strpos($log['keterangan'], 'Pelanggan:') + 10) : '-') ?>
                            </td>
                            <td>
                                <?= isset($log['id_pelanggan']) ? esc($log['id_pelanggan']) : 
                                   (isset($log['keterangan']) && strpos($log['keterangan'], 'ID:') !== false ? 
                                    substr($log['keterangan'], strpos($log['keterangan'], 'ID:') + 3, 10) : '-') ?>
                            </td>
                            <td>
                                <div>
                                    <?= isset($log['keterangan']) ? esc($log['keterangan']) : '-' ?>
                                </div>
                                <?php if (isset($log['operator_nama'])): ?>
                                    <small class="text-muted">
                                        <i class="bi bi-person-circle"></i> <?= $log['operator_nama'] ?>
                                        <?php if (isset($log['unit_kerja'])): ?>
                                            | <?= $log['unit_kerja'] ?>
                                        <?php endif; ?>
                                    </small>
                                <?php endif; ?>
                            </td>
                            <td class="text-end fw-bold">
                                <?= isset($log['p1_kw']) ? number_format($log['p1_kw'], 3) : '0.000' ?>
                            </td>
                            <td class="text-end fw-bold">
                                <?= isset($log['p2_kw']) ? number_format($log['p2_kw'], 3) : '0.000' ?>
                            </td>
                            <td class="text-end">
                                <?php 
                                $error_value = isset($log['error_percent']) ? $log['error_percent'] : 0;
                                $error_class = 'text-secondary';
                                if ($error_value > 0) {
                                    $error_class = 'text-danger';
                                } elseif ($error_value < 0) {
                                    $error_class = 'text-success';
                                }
                                ?>
                                <span class="fw-bold <?= $error_class ?>">
                                    <?= number_format($error_value, 2) ?>%
                                </span>
                            </td>
                            <td>
                                <?php 
                                // LOGIKA STATUS YANG BENAR
                                $status_class = 'bg-secondary';
                                $status_text = '-';
                                
                                // Cek jika ada final_status langsung
                                if (isset($log['final_status']) && !empty($log['final_status'])) {
                                    if ($log['final_status'] === 'BAIK' || $log['final_status'] === 'baik') {
                                        $status_class = 'bg-success';
                                        $status_text = 'BAIK';
                                    } elseif ($log['final_status'] === 'LUAR_KELAS' || $log['final_status'] === 'luar_kelas') {
                                        $status_class = 'bg-danger';
                                        $status_text = 'LUAR KELAS';
                                    }
                                }
                                // Jika tidak ada final_status, hitung dari error dan class meter
                                elseif (isset($log['error_percent']) && isset($log['class_meter'])) {
                                    $absError = abs($log['error_percent']);
                                    $classMeter = $log['class_meter'];
                                    $tolerance = 0.0001;
                                    
                                    // LOGIKA APLIKASI: Error ≠ Kelas = LUAR KELAS, Error = Kelas = BAIK
                                    if (abs($absError - $classMeter) > $tolerance) {
                                        $status_class = 'bg-danger';
                                        $status_text = 'LUAR KELAS';
                                    } else {
                                        $status_class = 'bg-success';
                                        $status_text = 'BAIK';
                                    }
                                }
                                // Jika hanya ada error saja
                                elseif (isset($log['error_percent'])) {
                                    $absError = abs($log['error_percent']);
                                    // Default class meter 1.0% jika tidak ada
                                    $classMeter = 1.0;
                                    $tolerance = 0.0001;
                                    
                                    if (abs($absError - $classMeter) > $tolerance) {
                                        $status_class = 'bg-danger';
                                        $status_text = 'LUAR KELAS';
                                    } else {
                                        $status_class = 'bg-success';
                                        $status_text = 'BAIK';
                                    }
                                }
                                ?>
                                <span class="badge <?= $status_class ?>"><?= $status_text ?></span>
                            </td>
                            <td class="text-center">
                                <?php 
                                $photo_path = '';
                                if (isset($log['photo_path'])) {
                                    $photo_path = $log['photo_path'];
                                } elseif (isset($log['photos'])) {
                                    $photos = json_decode($log['photos'], true);
                                    if (is_array($photos) && !empty($photos)) {
                                        $photo_path = $photos[0];
                                    }
                                }
                                
                                if (!empty($photo_path)): 
                                    $full_path = base_url('uploads/' . $photo_path);
                                ?>
                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                            onclick="viewPhoto('<?= $full_path ?>')">
                                        <i class="bi bi-image"></i>
                                    </button>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <!-- TOMBOL PDF (ASLI) -->
                                    <?php if (isset($log['id'])): ?>
                                        <a href="<?= base_url('export/pdf/' . $log['id']) ?>" 
                                           class="btn btn-outline-primary" target="_blank">
                                            <i class="bi bi-file-pdf"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-danger" 
                                                onclick="deleteLog(<?= $log['id'] ?>)">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    <?php else: ?>
                                        <button type="button" class="btn btn-outline-primary disabled">
                                            <i class="bi bi-file-pdf"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger disabled">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="12" class="text-center py-4 text-muted">
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
                    <a href="javascript:void(0)" onclick="currentModeAction('count')" class="nav-btn">
                        <div class="icon text-success">
                            <i class="bi bi-plus-circle-fill"></i>
                        </div>
                        <div class="label">Hitung</div>
                    </a>
                </div>
                <div class="col-3">
                    <a href="javascript:void(0)" onclick="currentModeAction('timer')" class="nav-btn">
                        <div class="icon text-primary" id="navTimerIcon">
                            <i class="bi bi-play-circle-fill"></i>
                        </div>
                        <div class="label" id="navTimerLabel">Start</div>
                    </a>
                </div>
                <div class="col-3">
                    <a href="javascript:void(0)" onclick="currentModeAction('calculate')" class="nav-btn">
                        <div class="icon text-warning">
                            <i class="bi bi-calculator-fill"></i>
                        </div>
                        <div class="label">Hitung</div>
                    </a>
                </div>
                <div class="col-3">
                    <a href="javascript:void(0)" onclick="currentModeAction('save')" class="nav-btn">
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
        
        // Current mode
        const currentMode = '<?= $mode ?>';
        
        // ========== ELEMENTS ==========
        const tasbihBtn = document.getElementById('tasbihBtn');
        const tasbihCounter = document.getElementById('tasbihCounter');
        const timerDisplay = document.getElementById('timerDisplay');
        const blinkRateDisplay = document.getElementById('blinkRateDisplay');
        const statusBadge = document.getElementById('statusBadge');
        const blinkList = document.getElementById('blinkList');
        const noBlinkMessage = document.getElementById('noBlinkMessage');
        const selectedBlinkInput = document.getElementById('selectedBlinkInput');
        
        // ========== MODE 1 FUNCTIONS (KEDIPAN) ==========
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
        
        function calculateSelected() {
            if (blinkData.length === 0) return;
            
            selectedBlink = Math.min(selectedBlink, blinkData.length);
            selectedBlink = Math.max(1, selectedBlink);
            selectedBlinkInput.value = selectedBlink;
            
            const selectedData = blinkData[selectedBlink - 1];
            const count = selectedData.count;
            const duration = selectedData.time;
            
            const arus = parseFloat(document.getElementById('arusInput').value) || 0;
            const tegangan = parseFloat(document.getElementById('teganganInput').value) || 220;
            const cosphi = parseFloat(document.getElementById('cosphiInput').value) || 0.85;
            const constanta = parseFloat(document.getElementById('constantaInput').value) || 1600;
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
            
            const absError = Math.abs(error_percent);
            
            // Hitung status final dengan logika baru
            const finalStatusData = calculateFinalStatus(absError, classMeter);
            
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
                ...finalStatusData
            });
            
            updateMode1FormInputs(count, duration, arus, tegangan, cosphi, constanta, classMeter);
        }
        
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
                
                // Reset results
                updateResults({
                    p1_kw: 0,
                    p2_kw: 0,
                    error_percent: 0,
                    abs_error: 0,
                    class_meter: 1.0,
                    final_status: '-',
                    final_status_text: '-',
                    final_status_class: 'bg-secondary',
                    card_color: ''
                });
                
                statusBadge.textContent = 'SIAP';
                statusBadge.className = 'badge bg-success';
            }
        }
        
        // ========== MODE 2 FUNCTIONS (3 PHASE) ==========
        function calculateMode2() {
            // Get inputs
            const p1 = parseFloat(document.getElementById('p1InputMode2').value) || 0;
            const pr = parseFloat(document.getElementById('prInputMode2').value) || 0;
            const ps = parseFloat(document.getElementById('psInputMode2').value) || 0;
            const pt = parseFloat(document.getElementById('ptInputMode2').value) || 0;
            const classMeter = parseFloat(document.getElementById('classMeterInputMode2').value) || 1.0;
            
            // Calculate P2 total
            const p2 = pr + ps + pt;
            
            // Update P2 total display
            document.getElementById('p2TotalDisplayMode2').value = p2.toFixed(3);
            document.getElementById('p2ExampleMode2').textContent = p2.toFixed(3);
            
            // Calculate error
            let error_percent = 0;
            if (p2 !== 0) {
                error_percent = ((p1 - p2) / p2) * 100;
            }
            
            const absError = Math.abs(error_percent);
            
            // Hitung status final dengan logika baru
            const finalStatusData = calculateFinalStatus(absError, classMeter);
            
            updateResults({
                p1_kw: p1,
                p2_kw: p2,
                error_percent: error_percent,
                abs_error: absError,
                class_meter: classMeter,
                ...finalStatusData
            });
            
            updateMode2FormInputs(p1, pr, ps, pt, classMeter);
        }
        
        // ========== SHARED FUNCTIONS ==========
        function calculateFinalStatus(absError, classMeter) {
            let finalStatus = '';
            let finalStatusClass = '';
            let finalStatusText = '';
            let cardColor = '';
            
            // LOGIKA BARU:
            // 1. Jika error > class_meter ATAU error < class_meter = DI LUAR KELAS METER
            // 2. Jika error = class_meter = BAIK
            
            // Toleransi kecil untuk floating point comparison
            const tolerance = 0.0001;
            
            // Cek apakah error SAMA dengan class meter (dalam toleransi)
            if (Math.abs(absError - classMeter) > tolerance) {
                // Error TIDAK SAMA dengan class meter (lebih besar ATAU lebih kecil)
                finalStatus = 'LUAR_KELAS';
                finalStatusClass = 'bg-danger';
                finalStatusText = 'DI LUAR KELAS METER';
                cardColor = 'border-danger';
            } else {
                // Error SAMA dengan class meter (dalam batas toleransi)
                finalStatus = 'BAIK';
                finalStatusClass = 'bg-success';
                finalStatusText = 'BAIK';
                cardColor = 'border-success';
            }
            
            return {
                final_status: finalStatus,
                final_status_class: finalStatusClass,
                final_status_text: finalStatusText,
                card_color: cardColor
            };
        }
        
        function updateResults(data) {
            // Update main display
            document.getElementById('finalP1Result').textContent = data.p1_kw.toFixed(3);
            document.getElementById('finalP2Result').textContent = data.p2_kw.toFixed(3);
            document.getElementById('finalErrorResult').textContent = data.error_percent.toFixed(2);
            
            // Update detail info
            document.getElementById('detailMode').textContent = (currentMode === 'mode1') ? 'Kedipan' : '3 Phase';
            document.getElementById('detailClass').textContent = data.class_meter;
            document.getElementById('detailP1').textContent = data.p1_kw.toFixed(3);
            document.getElementById('detailP2').textContent = data.p2_kw.toFixed(3);
            document.getElementById('detailError').textContent = data.error_percent.toFixed(2);
            document.getElementById('detailStatus').textContent = data.final_status;
            
            // Update status final
            document.getElementById('finalStatusResult').textContent = data.final_status;
            document.getElementById('finalStatusBadge').textContent = data.final_status_text;
            document.getElementById('finalStatusBadge').className = `badge ${data.final_status_class}`;
            document.getElementById('finalClassMeterLabel').textContent = `Kelas: ${data.class_meter}%`;
            
            // Update card color
            const finalStatusCard = document.getElementById('finalStatusCard');
            finalStatusCard.className = `card border ${data.card_color}`;
            
            // Update error status
            document.getElementById('finalErrorStatus').textContent = data.final_status_text;
            document.getElementById('finalErrorStatus').className = `badge ${data.final_status_class}`;
        }
        
        function updateClassMeter() {
            const classMeterInput = document.getElementById('classMeterInput');
            const classMeterValue = parseFloat(classMeterInput.value);
            
            // Update hidden input
            document.getElementById('inputClassMeterMode1').value = classMeterValue;
            
            // Update label display
            document.getElementById('finalClassMeterLabel').textContent = `Kelas: ${classMeterValue}%`;
            document.getElementById('detailClass').textContent = classMeterValue;
            
            // Recalculate jika sudah ada data
            if (blinkData.length > 0) {
                calculateSelected();
            }
        }
        
        // ========== FORM FUNCTIONS ==========
        function updateMode1FormInputs(count, duration, arus, tegangan, cosphi, constanta, classMeter) {
            document.getElementById('inputCount').value = count;
            document.getElementById('inputDuration').value = duration.toFixed(1);
            document.getElementById('inputArus').value = arus;
            document.getElementById('inputTegangan').value = tegangan;
            document.getElementById('inputCosphi').value = cosphi;
            document.getElementById('inputConstanta').value = constanta;
            document.getElementById('inputClassMeterMode1').value = classMeter;
            document.getElementById('inputBlinkData').value = JSON.stringify(blinkData);
            document.getElementById('inputSelectedBlink').value = selectedBlink;
        }
        
        function updateMode2FormInputs(p1, pr, ps, pt, classMeter) {
            document.getElementById('inputClassMeterMode2').value = classMeter;
            document.getElementById('inputP1Mode2').value = p1;
            document.getElementById('inputPrMode2').value = pr;
            document.getElementById('inputPsMode2').value = ps;
            document.getElementById('inputPtMode2').value = pt;
        }
        
        function validateSaveData() {
            const currentMode = '<?= $mode ?>';
            
            if (currentMode === 'mode1') {
                const namaPelanggan = document.querySelector('#saveFormMode1 input[name="nama_pelanggan"]');
                const idPelanggan = document.querySelector('#saveFormMode1 input[name="id_pelanggan"]');
                
                if (!namaPelanggan.value.trim()) {
                    namaPelanggan.focus();
                    alert('Nama Pelanggan wajib diisi!');
                    return false;
                }
                
                if (!idPelanggan.value.trim()) {
                    idPelanggan.focus();
                    alert('ID Pelanggan / No. Meter wajib diisi!');
                    return false;
                }
                
                if (blinkData.length === 0) {
                    alert('Belum ada data kedipan!');
                    return false;
                }
                
                return true;
            } else {
                const namaPelanggan = document.querySelector('#saveFormMode2 input[name="nama_pelanggan"]');
                const idPelanggan = document.querySelector('#saveFormMode2 input[name="id_pelanggan"]');
                const p1 = parseFloat(document.getElementById('p1InputMode2').value) || 0;
                const pr = parseFloat(document.getElementById('prInputMode2').value) || 0;
                const ps = parseFloat(document.getElementById('psInputMode2').value) || 0;
                const pt = parseFloat(document.getElementById('ptInputMode2').value) || 0;
                
                if (!namaPelanggan.value.trim()) {
                    namaPelanggan.focus();
                    alert('Nama Pelanggan wajib diisi!');
                    return false;
                }
                
                if (!idPelanggan.value.trim()) {
                    idPelanggan.focus();
                    alert('ID Pelanggan / No. Meter wajib diisi!');
                    return false;
                }
                
                if (p1 === 0 || pr === 0 || ps === 0 || pt === 0) {
                    alert('Semua nilai P1, Pr, Ps, Pt harus diisi dan lebih dari 0!');
                    return false;
                }
                
                return true;
            }
        }
        
        function saveDataMode1() {
            // Validasi data
            if (!validateSaveData()) {
                return;
            }
            
            // Update semua hidden inputs
            calculateSelected();
            
            // Submit form
            document.getElementById('saveFormMode1').submit();
        }
        
        function saveDataMode2() {
            // Validasi data
            if (!validateSaveData()) {
                return;
            }
            
            // Submit form
            document.getElementById('saveFormMode2').submit();
        }
        
        // ========== CAMERA FUNCTIONS ==========
        function takePhoto() {
            takePhotoGeneric('photoInputMode1', 'photoPreviewMode1');
        }
        
        function takePhotoMode2() {
            takePhotoGeneric('photoInputMode2', 'photoPreviewMode2');
        }
        
        function takePhotoGeneric(inputId, previewId) {
            // Cek apakah di mobile device
            if (/Android|iPhone|iPad|iPod/i.test(navigator.userAgent)) {
                const cameraInput = document.createElement('input');
                cameraInput.type = 'file';
                cameraInput.accept = 'image/*';
                cameraInput.capture = 'environment';
                
                cameraInput.onchange = function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        addPhotoToPreview(file, previewId);
                        addPhotoToFileInput(file, inputId);
                    }
                };
                
                cameraInput.click();
            } else {
                document.getElementById(inputId).click();
            }
        }
        
        function addPhotoToPreview(file, previewId) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById(previewId);
                const photoId = 'photo_' + Date.now();
                
                const imgContainer = document.createElement('div');
                imgContainer.className = 'photo-preview-item';
                imgContainer.id = photoId;
                
                imgContainer.innerHTML = `
                    <img src="${e.target.result}" alt="Preview">
                    <button type="button" class="btn btn-danger btn-sm remove-photo-btn" onclick="removePhotoFromPreview('${photoId}', '${previewId}')">
                        <i class="bi bi-x"></i>
                    </button>
                `;
                
                preview.appendChild(imgContainer);
            };
            reader.readAsDataURL(file);
        }
        
        function addPhotoToFileInput(file, inputId) {
            const input = document.getElementById(inputId);
            const dt = new DataTransfer();
            
            // Tambah file yang sudah ada
            for (let i = 0; i < input.files.length; i++) {
                dt.items.add(input.files[i]);
            }
            
            // Tambah file baru
            dt.items.add(file);
            
            input.files = dt.files;
        }
        
        function removePhotoFromPreview(photoId, previewId) {
            const element = document.getElementById(photoId);
            if (element) {
                element.remove();
                
                // Juga hapus dari file input
                const inputId = previewId === 'photoPreviewMode1' ? 'photoInputMode1' : 'photoInputMode2';
                const input = document.getElementById(inputId);
                const dt = new DataTransfer();
                const files = input.files;
                
                for (let i = 0; i < files.length; i++) {
                    if (files[i].name !== photoId) {
                        dt.items.add(files[i]);
                    }
                }
                
                input.files = dt.files;
            }
        }
        
        // ========== RIWAYAT FUNCTIONS ==========
        function viewDetail(id) {
            window.location.href = '<?= base_url("kwh/detail/") ?>' + id;
        }
        
        function deleteLog(id) {
            if (confirm('Hapus data pengukuran ini?')) {
                window.location.href = '<?= base_url("kwh/delete/") ?>' + id;
            }
        }
        
        function viewPhoto(photoUrl) {
            window.open(photoUrl, '_blank');
        }
        
        // ========== BOTTOM NAV ACTIONS ==========
        function currentModeAction(action) {
            if (currentMode === 'mode1') {
                switch(action) {
                    case 'count': countBlink(); break;
                    case 'timer': toggleTimer(); break;
                    case 'calculate': calculateSelected(); break;
                    case 'save': saveDataMode1(); break;
                }
            } else {
                switch(action) {
                    case 'count': calculateMode2(); break;
                    case 'timer': calculateMode2(); break;
                    case 'calculate': calculateMode2(); break;
                    case 'save': saveDataMode2(); break;
                }
            }
        }
        
        // ========== EVENT LISTENERS ==========
        document.addEventListener('keydown', function(e) {
            if (currentMode === 'mode1') {
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
            }
            
            if (e.code === 'KeyS' && e.ctrlKey) {
                e.preventDefault();
                currentModeAction('save');
            }
        });
        
        // Touch events for tasbih button
        if (tasbihBtn) {
            tasbihBtn.addEventListener('touchstart', function(e) {
                e.preventDefault();
                this.style.transform = 'scale(0.95)';
            });
            
            tasbihBtn.addEventListener('touchend', function(e) {
                e.preventDefault();
                this.style.transform = 'scale(1)';
                countBlink();
            });
        }
        
        // Auto calculate on page load
        window.addEventListener('DOMContentLoaded', function() {
            if (currentMode === 'mode2') {
                calculateMode2();
            }
        });
    </script>
</body>
</html>