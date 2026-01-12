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
        
        /* Mode Selection */
        .mode-selector {
            cursor: pointer;
            transition: all 0.3s;
            border: 2px solid transparent;
        }
        
        .mode-selector:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .mode-selector.active {
            border-color: var(--primary);
            background-color: rgba(13, 110, 253, 0.1);
        }
        
        /* Mode Content */
        .mode-content {
            display: none;
        }
        
        .mode-content.active {
            display: block;
            animation: fadeIn 0.5s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        /* Phase Cards */
        .phase-card {
            transition: all 0.3s;
        }
        
        .phase-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
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
        
        /* History Table */
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
            
            .mode-selector .card-body {
                padding: 10px;
            }
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
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-0">
                    <i class="bi bi-lightning-charge-fill text-warning me-2"></i>
                    KWH Error Calculator - Fleksibel
                </h4>
                <small class="text-muted">
                    Operator: <?= $user_nama ?> | Role: <?= strtoupper($user_role) ?>
                </small>
            </div>
            <div>
                <a href="<?= base_url('dashboard') ?>" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-speedometer2 me-1"></i> Dashboard
                </a>
            </div>
        </div>
        
        <!-- Mode Selector Section -->
        <div class="card mb-3">
            <div class="card-header">
                <i class="bi bi-sliders me-2"></i>PILIH METODE PERHITUNGAN
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- P1 Mode Selection -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Metode P1 (kWh Meter):</label>
                        <div class="row g-2">
                            <div class="col-4">
                                <div class="card mode-selector text-center <?= ($p1_mode === 'kedipan') ? 'active' : '' ?>" 
                                     onclick="selectP1Mode('kedipan')">
                                    <div class="card-body p-2">
                                        <i class="bi bi-lightning-charge-fill text-primary fs-4"></i>
                                        <div class="small fw-bold">Kedipan</div>
                                        <small class="text-muted">Dari meter</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="card mode-selector text-center <?= ($p1_mode === 'manual') ? 'active' : '' ?>" 
                                     onclick="selectP1Mode('manual')">
                                    <div class="card-body p-2">
                                        <i class="bi bi-input-cursor-text text-success fs-4"></i>
                                        <div class="small fw-bold">Manual</div>
                                        <small class="text-muted">Input langsung</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="card mode-selector text-center <?= ($p1_mode === 'cosphi') ? 'active' : '' ?>" 
                                     onclick="selectP1Mode('cosphi')">
                                    <div class="card-body p-2">
                                        <i class="bi bi-calculator text-warning fs-4"></i>
                                        <div class="small fw-bold">Cos φ</div>
                                        <small class="text-muted">V × I × cosφ</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- P2 Mode Selection -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Metode P2 (Pembanding):</label>
                        <div class="row g-2">
                            <div class="col-4">
                                <div class="card mode-selector text-center <?= ($p2_mode === 'single_phase') ? 'active' : '' ?>" 
                                     onclick="selectP2Mode('single_phase')">
                                    <div class="card-body p-2">
                                        <i class="bi bi-1-circle text-primary fs-4"></i>
                                        <div class="small fw-bold">1 Phase</div>
                                        <small class="text-muted">V × I × cosφ</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="card mode-selector text-center <?= ($p2_mode === 'three_phase') ? 'active' : '' ?>" 
                                     onclick="selectP2Mode('three_phase')">
                                    <div class="card-body p-2">
                                        <i class="bi bi-3-circle text-success fs-4"></i>
                                        <div class="small fw-bold">3 Phase</div>
                                        <small class="text-muted">Pr + Ps + Pt</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="card mode-selector text-center <?= ($p2_mode === 'manual') ? 'active' : '' ?>" 
                                     onclick="selectP2Mode('manual')">
                                    <div class="card-body p-2">
                                        <i class="bi bi-input-cursor text-warning fs-4"></i>
                                        <div class="small fw-bold">Manual</div>
                                        <small class="text-muted">Input langsung</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Current Mode Info -->
                <div class="alert alert-info mt-3 mb-0 py-2">
                    <div class="row small">
                        <div class="col-md-6">
                            <i class="bi bi-lightning-charge me-1"></i>
                            <strong>P1:</strong> 
                            <span id="currentP1Mode">
                                <?= ($p1_mode === 'kedipan') ? 'Perhitungan dari Kedipan Meter' : 
                                    (($p1_mode === 'manual') ? 'Input Manual' : 'Perhitungan Cos φ') ?>
                            </span>
                        </div>
                        <div class="col-md-6">
                            <i class="bi bi-lightning me-1"></i>
                            <strong>P2:</strong> 
                            <span id="currentP2Mode">
                                <?= ($p2_mode === 'single_phase') ? 'Single Phase (V×I×cosφ)' : 
                                    (($p2_mode === 'three_phase') ? 'Three Phase (Pr+Ps+Pt)' : 'Input Manual') ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- P1 CONTENT AREA -->
        <div class="card mb-3">
            <div class="card-header">
                <i class="bi bi-lightning-charge-fill me-2"></i>PERHITUNGAN P1 (kWh METER)
                <span class="badge bg-primary float-end" id="p1ModeBadge">
                    <?= strtoupper($p1_mode) ?>
                </span>
            </div>
            <div class="card-body">
                <!-- P1 - KEDIPAN MODE -->
                <div id="p1KedipanContent" class="mode-content <?= ($p1_mode === 'kedipan') ? 'active' : '' ?>">
                    <div class="row g-3">
                        <!-- Parameter Input -->
                        <div class="col-md-8">
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Arus (A)</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="p1ArusKedipan" step="0.1" value="5">
                                        <span class="input-group-text">A</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Constanta</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="p1Constanta" step="1" value="1600">
                                        <span class="input-group-text">imp/kWh</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Class Meter</label>
                                    <div class="input-group">
                                        <select class="form-select" id="classMeter">
                                            <option value="1.0">Kelas 1.0%</option>
                                            <option value="0.5">Kelas 0.5%</option>
                                            <option value="0.2">Kelas 0.2%</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Kedipan Measurement -->
                            <div class="card mt-3">
                                <div class="card-header py-2 bg-light">
                                    <i class="bi bi-speedometer2 me-2"></i>Pengukuran Kedipan
                                </div>
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-md-4 text-center">
                                            <div class="tasbih-btn" onclick="countBlink()">
                                                <div id="tasbihCounter">0</div>
                                                <div style="font-size: 0.8rem;">KEDIPAN</div>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="row g-2">
                                                <div class="col-6">
                                                    <div class="text-center">
                                                        <div class="big-number text-primary" id="timerDisplay">0.0</div>
                                                        <small class="text-muted">Durasi (s)</small>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="text-center">
                                                        <div class="big-number text-success" id="blinkRateDisplay">0.0</div>
                                                        <small class="text-muted">Kedipan/s</small>
                                                    </div>
                                                </div>
                                                <div class="col-12 mt-2">
                                                    <button class="btn btn-success btn-sm w-100" onclick="toggleTimer()" id="timerBtn">
                                                        <i class="bi bi-play-fill me-2"></i> START
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Blink List -->
                                    <div class="mt-3">
                                        <div class="blink-list" id="blinkList">
                                            <div class="text-center text-muted py-3" id="noBlinkMessage">
                                                <i class="bi bi-inbox fs-1"></i>
                                                <p class="mt-2">Belum ada kedipan</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- P1 Result Preview -->
                        <div class="col-md-4">
                            <div class="card bg-light h-100">
                                <div class="card-body text-center">
                                    <h6 class="card-title text-muted">P1 Hasil</h6>
                                    <div class="big-number text-primary" id="p1KedipanResult">0.000</div>
                                    <small class="text-muted">kW</small>
                                    <div class="mt-3">
                                        <small class="text-muted">
                                            <i class="bi bi-info-circle me-1"></i>
                                            P1 = (3600 × kedipan/detik) / constanta
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- P1 - MANUAL MODE -->
                <div id="p1ManualContent" class="mode-content <?= ($p1_mode === 'manual') ? 'active' : '' ?>">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Input Nilai P1 Langsung</label>
                                <div class="input-group input-group-lg">
                                    <input type="number" class="form-control" id="p1ManualInput" step="0.001" value="15.000" 
                                           oninput="calculateP1Manual()">
                                    <span class="input-group-text">kW</span>
                                </div>
                                <div class="form-text">Masukkan nilai P1 langsung dari kWh meter</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h6 class="card-title text-muted">P1 Hasil</h6>
                                    <div class="big-number text-primary" id="p1ManualResult">15.000</div>
                                    <small class="text-muted">kW</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- P1 - COSPHI MODE -->
                <div id="p1CosphiContent" class="mode-content <?= ($p1_mode === 'cosphi') ? 'active' : '' ?>">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Arus (A)</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="p1ArusCosphi" step="0.1" value="5" 
                                               oninput="calculateP1Cosphi()">
                                        <span class="input-group-text">A</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Tegangan (V)</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="p1Tegangan" step="1" value="220" 
                                               oninput="calculateP1Cosphi()">
                                        <span class="input-group-text">V</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Cos φ</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="p1Cosphi" step="0.01" value="0.85" 
                                               oninput="calculateP1Cosphi()">
                                        <span class="input-group-text">PF</span>
                                    </div>
                                </div>
                            </div>
                            <div class="alert alert-info mt-3 mb-0 py-2">
                                <i class="bi bi-calculator me-2"></i>
                                <strong>Rumus:</strong> P1 = (V × I × cosφ) / 1000
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light h-100">
                                <div class="card-body text-center">
                                    <h6 class="card-title text-muted">P1 Hasil</h6>
                                    <div class="big-number text-primary" id="p1CosphiResult">0.935</div>
                                    <small class="text-muted">kW</small>
                                    <div class="mt-3">
                                        <small class="text-muted">
                                            <i class="bi bi-info-circle me-1"></i>
                                            (220 × 5 × 0.85) / 1000 = 0.935 kW
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- P2 CONTENT AREA -->
        <div class="card mb-3">
            <div class="card-header">
                <i class="bi bi-lightning me-2"></i>PERHITUNGAN P2 (PEMBANDING)
                <span class="badge bg-success float-end" id="p2ModeBadge">
                    <?= ($p2_mode === 'three_phase') ? '3 PHASE' : strtoupper($p2_mode) ?>
                </span>
            </div>
            <div class="card-body">
                <!-- P2 - SINGLE PHASE MODE -->
                <div id="p2SinglePhaseContent" class="mode-content <?= ($p2_mode === 'single_phase') ? 'active' : '' ?>">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Arus (A)</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="p2ArusSingle" step="0.1" value="5" 
                                               oninput="calculateP2SinglePhase()">
                                        <span class="input-group-text">A</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Tegangan (V)</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="p2TeganganSingle" step="1" value="220" 
                                               oninput="calculateP2SinglePhase()">
                                        <span class="input-group-text">V</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Cos φ</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="p2CosphiSingle" step="0.01" value="0.85" 
                                               oninput="calculateP2SinglePhase()">
                                        <span class="input-group-text">PF</span>
                                    </div>
                                </div>
                            </div>
                            <div class="alert alert-info mt-3 mb-0 py-2">
                                <i class="bi bi-calculator me-2"></i>
                                <strong>Rumus:</strong> P2 = (V × I × cosφ) / 1000
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light h-100">
                                <div class="card-body text-center">
                                    <h6 class="card-title text-muted">P2 Hasil</h6>
                                    <div class="big-number text-success" id="p2SinglePhaseResult">0.935</div>
                                    <small class="text-muted">kW</small>
                                    <div class="mt-3">
                                        <small class="text-muted">
                                            <i class="bi bi-info-circle me-1"></i>
                                            (220 × 5 × 0.85) / 1000 = 0.935 kW
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- P2 - THREE PHASE MODE -->
                <div id="p2ThreePhaseContent" class="mode-content <?= ($p2_mode === 'three_phase') ? 'active' : '' ?>">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="card border-primary phase-card">
                                        <div class="card-header py-2 bg-primary text-white">
                                            <i class="bi bi-lightning me-1"></i> Phase R (Pr)
                                        </div>
                                        <div class="card-body">
                                            <div class="input-group">
                                                <input type="number" class="form-control" id="prInput" step="0.001" value="4.675" 
                                                       oninput="calculateP2ThreePhase()">
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
                                                <input type="number" class="form-control" id="psInput" step="0.001" value="4.488" 
                                                       oninput="calculateP2ThreePhase()">
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
                                                <input type="number" class="form-control" id="ptInput" step="0.001" value="5.423" 
                                                       oninput="calculateP2ThreePhase()">
                                                <span class="input-group-text">kW</span>
                                            </div>
                                            <div class="form-text">Daya Phase T</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="alert alert-info mt-3 mb-0 py-2">
                                <div class="row small">
                                    <div class="col-md-6">
                                        <i class="bi bi-calculator me-2"></i>
                                        <strong>Rumus:</strong> P2 = Pr + Ps + Pt
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Total:</strong> <span id="p2ThreePhaseTotal">14.586</span> kW
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light h-100">
                                <div class="card-body text-center">
                                    <h6 class="card-title text-muted">P2 Hasil</h6>
                                    <div class="big-number text-success" id="p2ThreePhaseResult">14.586</div>
                                    <small class="text-muted">kW</small>
                                    <div class="mt-3">
                                        <small class="text-muted">
                                            <i class="bi bi-info-circle me-1"></i>
                                            4.675 + 4.488 + 5.423 = 14.586 kW
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- P2 - MANUAL MODE -->
                <div id="p2ManualContent" class="mode-content <?= ($p2_mode === 'manual') ? 'active' : '' ?>">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Input Nilai P2 Langsung</label>
                                <div class="input-group input-group-lg">
                                    <input type="number" class="form-control" id="p2ManualInput" step="0.001" value="14.586" 
                                           oninput="calculateP2Manual()">
                                    <span class="input-group-text">kW</span>
                                </div>
                                <div class="form-text">Masukkan nilai P2 langsung dari alat pembanding</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h6 class="card-title text-muted">P2 Hasil</h6>
                                    <div class="big-number text-success" id="p2ManualResult">14.586</div>
                                    <small class="text-muted">kW</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- HASIL PERHITUNGAN ERROR -->
        <div class="card mb-3">
            <div class="card-header">
                <i class="bi bi-graph-up me-2"></i>HASIL PERHITUNGAN ERROR
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
                                <div class="mt-1">
                                    <small class="text-muted" id="p1MethodBadge">Kedipan</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- P2 Result -->
                    <div class="col-md-3">
                        <div class="card bg-light border">
                            <div class="card-body text-center">
                                <h6 class="card-title text-muted">P2 (Pembanding)</h6>
                                <div class="big-number text-success" id="finalP2Result">0.000</div>
                                <small class="text-muted">kW</small>
                                <div class="mt-1">
                                    <small class="text-muted" id="p2MethodBadge">1 Phase</small>
                                </div>
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
                </div>
                
                <!-- Tombol Hitung -->
                <div class="row mt-4">
                    <div class="col-12">
                        <button class="btn btn-success w-100" onclick="calculateAll()">
                            <i class="bi bi-calculator-fill me-2"></i> HITUNG ERROR METER
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- SIMPAN DATA -->
        <div class="card mb-3">
            <div class="card-header">
                <i class="bi bi-save me-2"></i>SIMPAN DATA PENGUKURAN
            </div>
            <div class="card-body">
                <form method="post" action="<?= base_url('kwh/save') ?>" enctype="multipart/form-data" id="saveForm">
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
                        <input type="hidden" name="class_meter" id="inputClassMeter" value="1.0">
                        <input type="hidden" name="p1_mode" value="<?= $p1_mode ?>">
                        <input type="hidden" name="p2_mode" value="<?= $p2_mode ?>">
                        
                        <!-- P1 Data -->
                        <input type="hidden" name="arus" id="inputArus">
                        <input type="hidden" name="constanta" id="inputConstanta">
                        <input type="hidden" name="count" id="inputCount">
                        <input type="hidden" name="duration" id="inputDuration">
                        <input type="hidden" name="blink_data" id="inputBlinkData">
                        <input type="hidden" name="p1_manual" id="inputP1Manual">
                        <input type="hidden" name="tegangan" id="inputTegangan">
                        <input type="hidden" name="cosphi" id="inputCosphi">
                        
                        <!-- P2 Data -->
                        <input type="hidden" name="pr_input" id="inputPr">
                        <input type="hidden" name="ps_input" id="inputPs">
                        <input type="hidden" name="pt_input" id="inputPt">
                        <input type="hidden" name="p2_manual" id="inputP2Manual">
                        
                        <div class="col-12 mt-3">
                            <button type="submit" class="btn btn-primary w-100" id="submitBtn">
                                <i class="bi bi-check-lg me-2"></i> SIMPAN KE DATABASE
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- RIWAYAT PENGUKURAN -->
        <div class="card mb-5" id="riwayat">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-clock-history me-2"></i>RIWAYAT PENGUKURAN</span>
                <div>
                    <div class="btn-group btn-group-sm">
                        <a href="<?= base_url('kwh/export') ?>" class="btn btn-success">
                            <i class="bi bi-file-excel me-1"></i> CSV
                        </a>
                        <a href="<?= base_url('export/pdf') ?>" class="btn btn-danger" target="_blank">
                            <i class="bi bi-file-pdf me-1"></i> PDF
                        </a>
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
                                <th width="60">P1</th>
                                <th width="60">P2</th>
                                <th width="100">Metode</th>
                                <th width="80">Error</th>
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
                                    <td><?= number_format($log['p1_kw'], 3) ?></td>
                                    <td><?= number_format($log['p2_kw'], 3) ?></td>
                                    <td>
                                        <?php 
                                        $p1_mode = $log['p1_mode'] ?? 'kedipan';
                                        $p2_mode = $log['p2_mode'] ?? 'single_phase';
                                        
                                        $p1_badge = ($p1_mode === 'kedipan') ? 'primary' : 
                                                   (($p1_mode === 'manual') ? 'success' : 'warning');
                                        $p2_badge = ($p2_mode === 'single_phase') ? 'primary' : 
                                                   (($p2_mode === 'three_phase') ? 'success' : 'warning');
                                        ?>
                                        <span class="badge bg-<?= $p1_badge ?>">P1</span>
                                        <span class="badge bg-<?= $p2_badge ?>">P2</span>
                                    </td>
                                    <td><?= number_format($log['error_percent'], 2) ?>%</td>
                                    <td>
                                        <?php if (isset($log['error_percent']) && isset($log['class_meter'])): ?>
                                            <?php
                                            $statusFinal = $log['status_final'] ?? 'BELUM_DIHITUNG';
                                            
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
                                            <a href="<?= base_url('export/pdf/' . $log['id']) ?>" 
                                               class="btn btn-outline-danger btn-sm" 
                                               target="_blank"
                                               title="Export PDF">
                                                <i class="bi bi-file-pdf"></i>
                                            </a>
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
                                    <td colspan="10" class="text-center py-4 text-muted">
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
                    <a href="javascript:void(0)" onclick="modeAction('calculate')" class="nav-btn">
                        <div class="icon text-success">
                            <i class="bi bi-calculator-fill"></i>
                        </div>
                        <div class="label">Hitung</div>
                    </a>
                </div>
                <div class="col-3">
                    <a href="javascript:void(0)" onclick="modeAction('blink')" class="nav-btn">
                        <div class="icon text-primary">
                            <i class="bi bi-plus-circle-fill"></i>
                        </div>
                        <div class="label">Kedipan</div>
                    </a>
                </div>
                <div class="col-3">
                    <a href="javascript:void(0)" onclick="modeAction('timer')" class="nav-btn">
                        <div class="icon text-warning">
                            <i class="bi bi-play-circle-fill"></i>
                        </div>
                        <div class="label">Timer</div>
                    </a>
                </div>
                <div class="col-3">
                    <a href="javascript:void(0)" onclick="modeAction('save')" class="nav-btn">
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
        let currentP1Mode = '<?= $p1_mode ?>';
        let currentP2Mode = '<?= $p2_mode ?>';
        
        // Kedipan variables
        let blinkData = [];
        let timer = 0;
        let timerInterval = null;
        let isTimerRunning = false;
        let selectedBlink = 1;
        
        // ========== MODE SELECTION FUNCTIONS ==========
        function selectP1Mode(mode) {
            currentP1Mode = mode;
            
            // Update UI
            document.querySelectorAll('.mode-selector').forEach(el => {
                el.classList.remove('active');
            });
            event.target.closest('.mode-selector').classList.add('active');
            
            // Update badge
            document.getElementById('p1ModeBadge').textContent = mode.toUpperCase();
            document.getElementById('p1MethodBadge').textContent = 
                (mode === 'kedipan') ? 'Kedipan' : 
                (mode === 'manual') ? 'Manual' : 'Cos φ';
            
            // Update current mode display
            document.getElementById('currentP1Mode').textContent = 
                (mode === 'kedipan') ? 'Perhitungan dari Kedipan Meter' : 
                (mode === 'manual') ? 'Input Manual' : 'Perhitungan Cos φ';
            
            // Hide all P1 content
            document.querySelectorAll('#p1KedipanContent, #p1ManualContent, #p1CosphiContent')
                .forEach(el => el.classList.remove('active'));
            
            // Show selected P1 content
            document.getElementById(`p1${capitalizeFirst(mode)}Content`).classList.add('active');
            
            // Update URL and refresh
            updateModeURL();
        }
        
        function selectP2Mode(mode) {
            currentP2Mode = mode;
            
            // Update UI
            event.target.closest('.mode-selector').classList.add('active');
            
            // Update badge
            const displayMode = (mode === 'three_phase') ? '3 PHASE' : mode.toUpperCase();
            document.getElementById('p2ModeBadge').textContent = displayMode;
            document.getElementById('p2MethodBadge').textContent = 
                (mode === 'single_phase') ? '1 Phase' : 
                (mode === 'three_phase') ? '3 Phase' : 'Manual';
            
            // Update current mode display
            document.getElementById('currentP2Mode').textContent = 
                (mode === 'single_phase') ? 'Single Phase (V×I×cosφ)' : 
                (mode === 'three_phase') ? 'Three Phase (Pr+Ps+Pt)' : 'Input Manual';
            
            // Hide all P2 content
            document.querySelectorAll('#p2SinglePhaseContent, #p2ThreePhaseContent, #p2ManualContent')
                .forEach(el => el.classList.remove('active'));
            
            // Show selected P2 content
            const modeMap = {
                'single_phase': 'SinglePhase',
                'three_phase': 'ThreePhase',
                'manual': 'Manual'
            };
            document.getElementById(`p2${modeMap[mode]}Content`).classList.add('active');
            
            // Update URL and refresh
            updateModeURL();
        }
        
        function updateModeURL() {
            const url = new URL(window.location.href);
            url.searchParams.set('p1_mode', currentP1Mode);
            url.searchParams.set('p2_mode', currentP2Mode);
            
            // Update via fetch untuk smooth transition
            fetch(`<?= base_url('kwh/switchMode') ?>?p1_mode=${currentP1Mode}&p2_mode=${currentP2Mode}`)
                .then(response => {
                    if (response.ok) {
                        // Optional: bisa reload atau tidak
                        // window.history.pushState({}, '', url);
                    }
                });
        }
        
        function capitalizeFirst(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        }
        
        // ========== KEDIPAN FUNCTIONS ==========
        function startTimer() {
            if (isTimerRunning) return;
            
            isTimerRunning = true;
            const startTime = Date.now() - (timer * 1000);
            
            timerInterval = setInterval(() => {
                timer = (Date.now() - startTime) / 1000;
                document.getElementById('timerDisplay').textContent = timer.toFixed(1);
                
                // Update blink rate
                if (blinkData.length > 0) {
                    const lastBlink = blinkData[blinkData.length - 1];
                    const blinkRate = lastBlink.count / timer;
                    document.getElementById('blinkRateDisplay').textContent = blinkRate.toFixed(3);
                    
                    // Auto calculate P1 if in kedipan mode
                    if (currentP1Mode === 'kedipan') {
                        calculateP1Kedipan();
                    }
                }
            }, 100);
        }
        
        function stopTimer() {
            if (!isTimerRunning) return;
            
            isTimerRunning = false;
            clearInterval(timerInterval);
        }
        
        function toggleTimer() {
            if (isTimerRunning) {
                stopTimer();
                document.getElementById('timerBtn').innerHTML = '<i class="bi bi-play-fill me-2"></i> START';
                document.getElementById('timerBtn').className = 'btn btn-success btn-sm w-100';
            } else {
                startTimer();
                document.getElementById('timerBtn').innerHTML = '<i class="bi bi-pause-fill me-2"></i> STOP';
                document.getElementById('timerBtn').className = 'btn btn-danger btn-sm w-100';
            }
        }
        
        function countBlink() {
            blinkData.push({
                time: timer,
                count: blinkData.length + 1
            });
            
            updateBlinkList();
            
            // Animate tasbih button
            const tasbihBtn = document.querySelector('.tasbih-btn');
            tasbihBtn.style.transform = 'scale(0.95)';
            setTimeout(() => tasbihBtn.style.transform = 'scale(1)', 100);
            
            document.getElementById('tasbihCounter').textContent = blinkData.length;
            
            // Auto start timer if first blink
            if (!isTimerRunning && blinkData.length === 1) {
                startTimer();
            }
            
            // Calculate P1 if in kedipan mode
            if (currentP1Mode === 'kedipan') {
                calculateP1Kedipan();
            }
        }
        
        function updateBlinkList() {
            if (blinkData.length === 0) {
                document.getElementById('noBlinkMessage').style.display = 'block';
                document.getElementById('blinkList').innerHTML = '';
                return;
            }
            
            document.getElementById('noBlinkMessage').style.display = 'none';
            
            let html = '';
            blinkData.forEach((blink, index) => {
                const blinkRate = (blink.count / blink.time).toFixed(3);
                
                html += `
                    <div class="blink-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>Kedipan ${blink.count}</strong>
                                <div class="small">Waktu: ${blink.time.toFixed(1)} detik</div>
                            </div>
                            <div class="text-end">
                                <div class="small">${blinkRate}/detik</div>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            document.getElementById('blinkList').innerHTML = html;
        }
        
        // ========== CALCULATION FUNCTIONS ==========
        function calculateP1Kedipan() {
            if (blinkData.length === 0) return;
            
            const arus = parseFloat(document.getElementById('p1ArusKedipan').value) || 0;
            const constanta = parseFloat(document.getElementById('p1Constanta').value) || 1600;
            const classMeter = parseFloat(document.getElementById('classMeter').value) || 1.0;
            
            const lastBlink = blinkData[blinkData.length - 1];
            const count = lastBlink.count;
            const duration = lastBlink.time;
            
            if (duration === 0 || constanta === 0) {
                document.getElementById('p1KedipanResult').textContent = '0.000';
                return;
            }
            
            const blinkPerSecond = count / duration;
            const p1_kw = (3600 * blinkPerSecond) / constanta;
            
            document.getElementById('p1KedipanResult').textContent = p1_kw.toFixed(3);
            
            // Update hidden inputs
            document.getElementById('inputArus').value = arus;
            document.getElementById('inputConstanta').value = constanta;
            document.getElementById('inputCount').value = count;
            document.getElementById('inputDuration').value = duration.toFixed(1);
            document.getElementById('inputBlinkData').value = JSON.stringify(blinkData);
            document.getElementById('inputClassMeter').value = classMeter;
        }
        
        function calculateP1Manual() {
            const p1 = parseFloat(document.getElementById('p1ManualInput').value) || 0;
            document.getElementById('p1ManualResult').textContent = p1.toFixed(3);
            document.getElementById('inputP1Manual').value = p1;
        }
        
        function calculateP1Cosphi() {
            const arus = parseFloat(document.getElementById('p1ArusCosphi').value) || 0;
            const tegangan = parseFloat(document.getElementById('p1Tegangan').value) || 220;
            const cosphi = parseFloat(document.getElementById('p1Cosphi').value) || 0.85;
            
            const p1_kw = (tegangan * arus * cosphi) / 1000;
            
            document.getElementById('p1CosphiResult').textContent = p1_kw.toFixed(3);
            
            // Update hidden inputs
            document.getElementById('inputArus').value = arus;
            document.getElementById('inputTegangan').value = tegangan;
            document.getElementById('inputCosphi').value = cosphi;
        }
        
        function calculateP2SinglePhase() {
            const arus = parseFloat(document.getElementById('p2ArusSingle').value) || 0;
            const tegangan = parseFloat(document.getElementById('p2TeganganSingle').value) || 220;
            const cosphi = parseFloat(document.getElementById('p2CosphiSingle').value) || 0.85;
            
            const p2_kw = (tegangan * arus * cosphi) / 1000;
            
            document.getElementById('p2SinglePhaseResult').textContent = p2_kw.toFixed(3);
            
            // Update hidden inputs
            document.getElementById('inputArus').value = arus;
            document.getElementById('inputTegangan').value = tegangan;
            document.getElementById('inputCosphi').value = cosphi;
        }
        
        function calculateP2ThreePhase() {
            const pr = parseFloat(document.getElementById('prInput').value) || 0;
            const ps = parseFloat(document.getElementById('psInput').value) || 0;
            const pt = parseFloat(document.getElementById('ptInput').value) || 0;
            
            const p2_kw = pr + ps + pt;
            
            document.getElementById('p2ThreePhaseResult').textContent = p2_kw.toFixed(3);
            document.getElementById('p2ThreePhaseTotal').textContent = p2_kw.toFixed(3);
            
            // Update hidden inputs
            document.getElementById('inputPr').value = pr;
            document.getElementById('inputPs').value = ps;
            document.getElementById('inputPt').value = pt;
        }
        
        function calculateP2Manual() {
            const p2 = parseFloat(document.getElementById('p2ManualInput').value) || 0;
            document.getElementById('p2ManualResult').textContent = p2.toFixed(3);
            document.getElementById('inputP2Manual').value = p2;
        }
        
        function calculateAll() {
            let p1 = 0;
            let p2 = 0;
            const classMeter = parseFloat(document.getElementById('classMeter').value) || 1.0;
            
            // Calculate P1 based on current mode
            switch(currentP1Mode) {
                case 'kedipan':
                    calculateP1Kedipan();
                    p1 = parseFloat(document.getElementById('p1KedipanResult').textContent) || 0;
                    break;
                case 'manual':
                    calculateP1Manual();
                    p1 = parseFloat(document.getElementById('p1ManualResult').textContent) || 0;
                    break;
                case 'cosphi':
                    calculateP1Cosphi();
                    p1 = parseFloat(document.getElementById('p1CosphiResult').textContent) || 0;
                    break;
            }
            
            // Calculate P2 based on current mode
            switch(currentP2Mode) {
                case 'single_phase':
                    calculateP2SinglePhase();
                    p2 = parseFloat(document.getElementById('p2SinglePhaseResult').textContent) || 0;
                    break;
                case 'three_phase':
                    calculateP2ThreePhase();
                    p2 = parseFloat(document.getElementById('p2ThreePhaseResult').textContent) || 0;
                    break;
                case 'manual':
                    calculateP2Manual();
                    p2 = parseFloat(document.getElementById('p2ManualResult').textContent) || 0;
                    break;
            }
            
            // Calculate error
            let error_percent = 0;
            if (p2 !== 0) {
                error_percent = ((p1 - p2) / p2) * 100;
            }
            
            const absError = Math.abs(error_percent);
            
            // Determine final status
            let finalStatus = '';
            let finalStatusClass = '';
            let finalStatusText = '';
            let cardColor = '';
            
            if (absError > 5) {
                finalStatus = 'BURUK';
                finalStatusClass = 'bg-danger';
                finalStatusText = 'BURUK (Error > 5%)';
                cardColor = 'border-danger';
            } else if (absError < classMeter) {
                finalStatus = 'TIDAK STABIL';
                finalStatusClass = 'bg-warning';
                finalStatusText = 'TIDAK STABIL';
                cardColor = 'border-warning';
            } else {
                finalStatus = 'BAIK';
                finalStatusClass = 'bg-success';
                finalStatusText = 'BAIK';
                cardColor = 'border-success';
            }
            
            // Update display
            document.getElementById('finalP1Result').textContent = p1.toFixed(3);
            document.getElementById('finalP2Result').textContent = p2.toFixed(3);
            document.getElementById('finalErrorResult').textContent = error_percent.toFixed(2);
            document.getElementById('finalStatusResult').textContent = finalStatus;
            document.getElementById('finalStatusBadge').textContent = finalStatusText;
            document.getElementById('finalStatusBadge').className = `badge ${finalStatusClass}`;
            document.getElementById('finalClassMeterLabel').textContent = `Kelas: ${classMeter}%`;
            document.getElementById('finalErrorStatus').textContent = finalStatusText;
            document.getElementById('finalErrorStatus').className = `badge ${finalStatusClass}`;
            
            const finalStatusCard = document.getElementById('finalStatusCard');
            finalStatusCard.className = `card border ${cardColor}`;
            
            // Show success message
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${finalStatusClass.replace('bg-', '')} alert-dismissible fade show mt-3`;
            alertDiv.innerHTML = `
                <i class="bi bi-check-circle-fill me-2"></i>
                <strong>Perhitungan selesai!</strong> Error: ${error_percent.toFixed(2)}% | Status: ${finalStatus}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            const resultsCard = document.querySelector('.card .card-body');
            const existingAlert = resultsCard.querySelector('.alert');
            if (existingAlert) {
                existingAlert.remove();
            }
            resultsCard.insertBefore(alertDiv, resultsCard.querySelector('.row.mt-4'));
        }
        
        // ========== BOTTOM NAV ACTIONS ==========
        function modeAction(action) {
            switch(action) {
                case 'calculate':
                    calculateAll();
                    break;
                case 'blink':
                    if (currentP1Mode === 'kedipan') {
                        countBlink();
                    } else {
                        calculateAll();
                    }
                    break;
                case 'timer':
                    if (currentP1Mode === 'kedipan') {
                        toggleTimer();
                    } else {
                        calculateAll();
                    }
                    break;
                case 'save':
                    document.getElementById('saveForm').submit();
                    break;
            }
        }
        
        // ========== PHOTO FUNCTIONS ==========
        function takePhoto() {
            if (/Android|iPhone|iPad|iPod/i.test(navigator.userAgent)) {
                const cameraInput = document.createElement('input');
                cameraInput.type = 'file';
                cameraInput.accept = 'image/*';
                cameraInput.capture = 'environment';
                
                cameraInput.onchange = function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        addPhotoToPreview(file);
                    }
                };
                
                cameraInput.click();
            } else {
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
        
        function removePhotoFromPreview(photoId) {
            const element = document.getElementById(photoId);
            if (element) {
                element.remove();
            }
        }
        
        // Initialize on page load
        window.addEventListener('DOMContentLoaded', function() {
            // Set initial calculations based on current mode
            if (currentP1Mode === 'kedipan') {
                calculateP1Kedipan();
            } else if (currentP1Mode === 'manual') {
                calculateP1Manual();
            } else if (currentP1Mode === 'cosphi') {
                calculateP1Cosphi();
            }
            
            if (currentP2Mode === 'single_phase') {
                calculateP2SinglePhase();
            } else if (currentP2Mode === 'three_phase') {
                calculateP2ThreePhase();
            } else if (currentP2Mode === 'manual') {
                calculateP2Manual();
            }
            
            // Auto calculate all
            calculateAll();
        });
    </script>
</body>
</html>