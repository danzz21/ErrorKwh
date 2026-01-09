<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <title><?= $title ?> - PLN</title>
    
    <!-- Bootstrap 5 CSS + Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <style>
        :root {
            --primary: #0d6efd;
            --secondary: #6c757d;
            --success: #198754;
            --danger: #dc3545;
            --warning: #ffc107;
            --info: #0dcaf0;
            --dark: #212529;
        }
        
        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding-bottom: 70px; /* Space for bottom navbar */
        }
        
        /* Touch-friendly buttons */
        .btn-touch {
            min-height: 50px;
            font-size: 1.1rem;
            padding: 12px 20px;
            border-radius: 12px;
            margin: 5px 0;
        }
        
        .btn-circle {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin: 0 10px;
        }
        
        /* Main Counter Button - TASBIH DIGITAL */
        .counter-main-btn {
            width: 180px;
            height: 180px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--success) 0%, #157347 100%);
            border: 6px solid white;
            box-shadow: 0 15px 30px rgba(0,0,0,0.2), 
                        inset 0 8px 25px rgba(255,255,255,0.3);
            color: white;
            font-size: 3.5rem;
            font-weight: bold;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.15s;
            margin: 20px auto;
            user-select: none;
            touch-action: manipulation;
        }
        
        @media (min-width: 768px) {
            .counter-main-btn {
                width: 220px;
                height: 220px;
                font-size: 4rem;
            }
        }
        
        .counter-main-btn:active {
            transform: scale(0.95);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .counter-main-btn .counter-label {
            font-size: 1rem;
            margin-top: 5px;
            opacity: 0.9;
        }
        
        /* Display Numbers */
        .display-number {
            font-family: 'Courier New', monospace;
            font-weight: 800;
            font-size: 3.5rem;
            color: var(--dark);
            text-align: center;
            margin: 10px 0;
        }
        
        @media (min-width: 768px) {
            .display-number {
                font-size: 4rem;
            }
        }
        
        .timer-display {
            font-family: 'Courier New', monospace;
            font-size: 2.5rem;
            color: var(--dark);
            text-align: center;
        }
        
        /* Cards */
        .card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            overflow: hidden;
        }
        
        .card-header {
            background: linear-gradient(135deg, var(--primary) 0%, #0b5ed7 100%);
            color: white;
            border-bottom: none;
            padding: 15px 20px;
            font-weight: 600;
        }
        
        /* Status Indicator */
        .status-badge {
            padding: 8px 15px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        /* Progress Bar for Idle */
        .idle-progress {
            height: 8px;
            border-radius: 4px;
            background: #e9ecef;
            overflow: hidden;
        }
        
        .idle-progress-bar {
            height: 100%;
            background: linear-gradient(90deg, var(--success), #157347);
            transition: width 0.3s;
        }
        
        /* Blink Animation */
        .blink-dot {
            display: inline-block;
            width: 12px;
            height: 12px;
            background: var(--danger);
            border-radius: 50%;
            margin-right: 8px;
            animation: blink 1s infinite;
        }
        
        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.3; }
        }
        
        /* Bottom Navbar (Mobile) */
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
        
        .nav-icon {
            font-size: 1.5rem;
            display: block;
            margin-bottom: 5px;
        }
        
        .nav-label {
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        /* Responsive Table */
        .table-responsive {
            max-height: 400px;
            overflow-y: auto;
        }
        
        /* Dark mode support */
        @media (prefers-color-scheme: dark) {
            .card {
                background-color: #2d3748;
                color: #e2e8f0;
            }
            
            .display-number {
                color: #e2e8f0;
            }
        }
        
        /* Vibration effect */
        @keyframes vibrate {
            0% { transform: translateX(0); }
            25% { transform: translateX(-2px); }
            75% { transform: translateX(2px); }
            100% { transform: translateX(0); }
        }
        
        .vibrate {
            animation: vibrate 0.1s linear 3;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="container-fluid bg-dark bg-gradient text-white py-3">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-2">
                    <i class="bi bi-lightning-charge-fill fs-3"></i>
                </div>
                <div class="col-8 text-center">
                    <h1 class="h4 mb-0">KWH METER COUNTER</h1>
                    <small class="opacity-75">Tasbih Digital - PLN Project</small>
                </div>
                <div class="col-2 text-end">
                    <span class="blink-dot"></span>
                </div>
            </div>
        </div>
    </div>
<!-- Alert Messages -->
<div class="container mt-2">
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
</div>
    <!-- Main Content -->
    <div class="container py-3">
        <!-- Status Bar -->
        <div class="row mb-3">
            <div class="col-6">
                <div class="card bg-light">
                    <div class="card-body py-2">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-clock-history me-2"></i>
                            <div>
                                <small class="d-block text-muted">Mode</small>
                                <span class="fw-bold" id="statusText">SIAP</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="card bg-light">
                    <div class="card-body py-2">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-hourglass-split me-2"></i>
                            <div>
                                <small class="d-block text-muted">Idle Timeout</small>
                                <span class="fw-bold" id="idleCountdown">15s</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tasbih Digital Button -->
        <div class="card">
            <div class="card-header text-center">
                <i class="bi bi-plus-circle me-2"></i>TASBIH DIGITAL - Klik / Tekan
            </div>
            <div class="card-body text-center py-4">
                <div class="counter-main-btn" id="tasbihBtn" onclick="countBlink()">
                    <div id="mainCounter">0</div>
                    <div class="counter-label">KEDIPAN</div>
                </div>
                
                <div class="mt-3">
                    <small class="text-muted">
                        <i class="bi bi-space"></i> Tekan Spasi atau klik tombol untuk menghitung kedipan
                    </small>
                </div>
                
                <!-- Timer Display -->
                <div class="mt-4">
                    <h5 class="text-muted mb-2">DURASI PENGUKURAN</h5>
                    <div class="timer-display" id="timerDisplay">0.0</div>
                    <small class="text-muted">detik</small>
                </div>
            </div>
        </div>

        <!-- Control Buttons -->
        <div class="row g-2 mb-3">
            <div class="col-6">
                <button class="btn btn-success btn-touch w-100" onclick="toggleTimer()" id="timerBtn">
                    <i class="bi bi-play-fill me-2"></i> START
                </button>
            </div>
            <div class="col-6">
                <button class="btn btn-danger btn-touch w-100" onclick="resetAll()">
                    <i class="bi bi-arrow-clockwise me-2"></i> RESET
                </button>
            </div>
        </div>

        <!-- Results Panel -->
        <div class="card">
            <div class="card-header">
                <i class="bi bi-graph-up me-2"></i>HASIL PENGUKURAN
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-4">
                        <div class="display-number" id="resultCount">0</div>
                        <small class="text-muted">Kedipan</small>
                    </div>
                    <div class="col-4">
                        <div class="display-number" id="resultTime">0.0</div>
                        <small class="text-muted">Detik</small>
                    </div>
                    <div class="col-4">
                        <div class="display-number" id="resultRate">0.0</div>
                        <small class="text-muted">Kedipan/s</small>
                    </div>
                </div>
                
                <!-- Idle Progress -->
                <div class="mt-3">
                    <div class="d-flex justify-content-between mb-1">
                        <small>Idle Timer:</small>
                        <small><span id="idleProgressText">15</span>s tersisa</small>
                    </div>
                    <div class="idle-progress">
                        <div class="idle-progress-bar" id="idleProgressBar" style="width: 100%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Save Form -->
        <div class="card">
            <div class="card-header">
                <i class="bi bi-save me-2"></i>SIMPAN DATA
            </div>
            <div class="card-body">
                <form id="saveForm" action="<?= base_url('kwh/save') ?>" method="post">
                    <div class="row g-2">
                        <div class="col-12 mb-2">
                            <label class="form-label">Keterangan (Opsional):</label>
                            <input type="text" name="keterangan" class="form-control" placeholder="Contoh: Meteran No. 123">
                        </div>
                        
                        <div class="col-6 mb-2">
                            <label class="form-label">Idle Timeout:</label>
                            <select name="idle_timeout" class="form-select" id="idleTimeoutSelect">
                                <option value="5">5 detik</option>
                                <option value="10">10 detik</option>
                                <option value="15" selected>15 detik</option>
                                <option value="30">30 detik</option>
                            </select>
                        </div>
                        
                        <div class="col-6 mb-2">
                            <label class="form-label">Mode:</label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="is_auto" id="autoMode" checked style="transform: scale(1.2);">
                                <label class="form-check-label fw-bold" for="autoMode">Auto Mode</label>
                            </div>
                        </div>
                        
                        <input type="hidden" name="count" id="inputCount">
                        <input type="hidden" name="duration" id="inputDuration">
                        
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary btn-touch w-100">
                                <i class="bi bi-check-lg me-2"></i> SIMPAN KE DATABASE
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- History Section -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-list-check me-2"></i>RIWAYAT</span>
                <div>
                    <button class="btn btn-sm btn-outline-secondary me-1" onclick="refreshHistory()">
                        <i class="bi bi-arrow-clockwise"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger" onclick="clearAllData()">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Waktu</th>
                                <th>Kedipan</th>
                                <th>Durasi</th>
                                <th>Rate</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="historyTable">
                            <?php if (!empty($logs)): ?>
                                <?php $no = 1; ?>
                                <?php foreach ($logs as $log): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= date('H:i', strtotime($log['created_at'])) ?></td>
                                    <td><span class="badge bg-primary"><?= $log['count'] ?></span></td>
                                    <td><?= number_format($log['duration_seconds'], 1) ?>s</td>
                                    <td>
                                        <span class="badge bg-<?= ($log['blink_per_second'] > 1 ? 'warning' : 'success') ?>">
                                            <?= number_format($log['blink_per_second'], 2) ?>/s
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-danger" onclick="deleteRecord(<?= $log['id'] ?>)">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        <i class="bi bi-inbox fs-1"></i>
                                        <p class="mt-2">Belum ada data</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Navigation (Mobile) -->
    <nav class="bottom-nav d-md-none">
        <div class="container">
            <div class="row text-center">
                <div class="col-3">
                    <a href="javascript:void(0)" onclick="countBlink()" class="text-decoration-none text-success">
                        <i class="bi bi-plus-circle-fill nav-icon"></i>
                        <div class="nav-label">Hitung</div>
                    </a>
                </div>
                <div class="col-3">
                    <a href="javascript:void(0)" onclick="toggleTimer()" class="text-decoration-none text-primary">
                        <i class="bi bi-play-circle-fill nav-icon" id="navTimerIcon"></i>
                        <div class="nav-label" id="navTimerLabel">Start</div>
                    </a>
                </div>
                <div class="col-3">
                    <a href="javascript:void(0)" onclick="resetAll()" class="text-decoration-none text-danger">
                        <i class="bi bi-x-circle-fill nav-icon"></i>
                        <div class="nav-label">Reset</div>
                    </a>
                </div>
                <div class="col-3">
                    <a href="javascript:void(0)" onclick="saveData()" class="text-decoration-none text-info">
                        <i class="bi bi-save-fill nav-icon"></i>
                        <div class="nav-label">Simpan</div>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Variables
        let count = 0;
        let timer = 0;
        let timerInterval = null;
        let isTimerRunning = false;
        let idleTimer = null;
        let idleTimeout = 15; // seconds
        let idleCounter = 0;
        let isAutoMode = true;
        
        // Elements
        const tasbihBtn = document.getElementById('tasbihBtn');
        const mainCounter = document.getElementById('mainCounter');
        const timerDisplay = document.getElementById('timerDisplay');
        const timerBtn = document.getElementById('timerBtn');
        const statusText = document.getElementById('statusText');
        const idleProgressBar = document.getElementById('idleProgressBar');
        const idleCountdown = document.getElementById('idleCountdown');
        const idleProgressText = document.getElementById('idleProgressText');
        const idleTimeoutSelect = document.getElementById('idleTimeoutSelect');
        
        const resultCount = document.getElementById('resultCount');
        const resultTime = document.getElementById('resultTime');
        const resultRate = document.getElementById('resultRate');
        
        const inputCount = document.getElementById('inputCount');
        const inputDuration = document.getElementById('inputDuration');
        
        // Initialize
        function init() {
            updateIdleTimeout();
            updateDisplay();
            
            // Check if device is mobile for vibration
            if ('vibrate' in navigator) {
                // Enable vibration
            }
        }
        
        // Update idle timeout from select
        function updateIdleTimeout() {
            idleTimeout = parseInt(idleTimeoutSelect.value);
            idleCountdown.textContent = idleTimeout + 's';
            idleProgressText.textContent = idleTimeout;
            resetIdleTimer();
        }
        
        // Count blink - Main function
        function countBlink() {
            // Add count
            count++;
            
            // Vibrate if available (mobile)
            if ('vibrate' in navigator) {
                navigator.vibrate(50);
            }
            
            // Add visual feedback
            tasbihBtn.classList.add('vibrate');
            setTimeout(() => tasbihBtn.classList.remove('vibrate'), 300);
            
            // Auto start timer on first click if auto mode
            if (!isTimerRunning && isAutoMode && count === 1) {
                startTimer();
            }
            
            // Reset idle timer
            resetIdleTimer();
            
            // Update display
            updateDisplay();
        }
        
        // Timer functions
        function startTimer() {
            if (!isTimerRunning) {
                isTimerRunning = true;
                
                // Update UI
                timerBtn.innerHTML = '<i class="bi bi-pause-fill me-2"></i> STOP';
                timerBtn.classList.remove('btn-success');
                timerBtn.classList.add('btn-warning');
                
                statusText.textContent = 'MENGUKUR';
                statusText.className = 'fw-bold text-success';
                
                // Update bottom nav
                document.getElementById('navTimerIcon').className = 'bi bi-pause-circle-fill nav-icon';
                document.getElementById('navTimerLabel').textContent = 'Stop';
                
                timerInterval = setInterval(() => {
                    timer += 0.1;
                    updateDisplay();
                    
                    // Auto stop jika tidak ada aktivitas dalam idleTimeout
                    idleCounter += 0.1;
                    if (isAutoMode && idleCounter >= idleTimeout) {
                        stopTimer();
                    }
                    
                    // Update idle progress bar
                    const progress = ((idleTimeout - idleCounter) / idleTimeout) * 100;
                    idleProgressBar.style.width = Math.max(0, progress) + '%';
                    
                    // Update idle countdown
                    const remaining = Math.max(0, Math.ceil(idleTimeout - idleCounter));
                    idleProgressText.textContent = remaining;
                    
                    // Warning when about to timeout
                    if (remaining <= 3) {
                        idleProgressBar.style.background = 'linear-gradient(90deg, var(--warning), #ff6b00)';
                    }
                }, 100);
            }
        }
        
        function stopTimer() {
            if (isTimerRunning) {
                isTimerRunning = false;
                clearInterval(timerInterval);
                
                // Update UI
                timerBtn.innerHTML = '<i class="bi bi-play-fill me-2"></i> START';
                timerBtn.classList.remove('btn-warning');
                timerBtn.classList.add('btn-success');
                
                statusText.textContent = 'SELESAI';
                statusText.className = 'fw-bold text-info';
                
                // Update bottom nav
                document.getElementById('navTimerIcon').className = 'bi bi-play-circle-fill nav-icon';
                document.getElementById('navTimerLabel').textContent = 'Start';
                
                idleProgressBar.style.background = 'linear-gradient(90deg, var(--success), #157347)';
            }
        }
        
        function toggleTimer() {
            if (isTimerRunning) {
                stopTimer();
            } else {
                startTimer();
            }
        }
        
        function resetAll() {
            count = 0;
            timer = 0;
            isTimerRunning = false;
            clearInterval(timerInterval);
            
            // Update UI
            timerBtn.innerHTML = '<i class="bi bi-play-fill me-2"></i> START';
            timerBtn.classList.remove('btn-warning');
            timerBtn.classList.add('btn-success');
            
            statusText.textContent = 'SIAP';
            statusText.className = 'fw-bold';
            
            // Update bottom nav
            document.getElementById('navTimerIcon').className = 'bi bi-play-circle-fill nav-icon';
            document.getElementById('navTimerLabel').textContent = 'Start';
            
            resetIdleTimer();
            updateDisplay();
        }
        
        // Idle timer functions
        function resetIdleTimer() {
            idleCounter = 0;
            idleProgressBar.style.width = '100%';
            idleProgressText.textContent = idleTimeout;
            idleProgressBar.style.background = 'linear-gradient(90deg, var(--success), #157347)';
        }
        
        // Update display
        function updateDisplay() {
            // Update main displays
            mainCounter.textContent = count;
            timerDisplay.textContent = timer.toFixed(1);
            
            // Update result panel
            resultCount.textContent = count;
            resultTime.textContent = timer.toFixed(1);
            
            // Calculate blink rate
            const blinkRate = timer > 0 ? (count / timer) : 0;
            resultRate.textContent = blinkRate.toFixed(2);
            
            // Update hidden form inputs
            inputCount.value = count;
            inputDuration.value = timer.toFixed(1);
            
            // Update main button
            tasbihBtn.querySelector('#mainCounter').textContent = count;
        }
        
        // Save data function
        function saveData() {
            if (count === 0) {
                alert('Belum ada data untuk disimpan!');
                return;
            }
            
            document.getElementById('saveForm').submit();
        }
        
        // Delete record
        function deleteRecord(id) {
            if (confirm('Hapus data ini?')) {
                window.location.href = "<?= base_url('kwh/delete/') ?>" + id;
            }
        }
        
        // Clear all data
        function clearAllData() {
            if (confirm('Hapus SEMUA data riwayat?')) {
                window.location.href = "<?= base_url('kwh/clearAll') ?>";
            }
        }
        
        // Refresh history
        function refreshHistory() {
            window.location.reload();
        }
        
        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Space untuk hitung
            if (e.code === 'Space') {
                e.preventDefault();
                countBlink();
            }
            
            // Enter untuk start/stop timer
            if (e.code === 'Enter') {
                e.preventDefault();
                toggleTimer();
            }
            
            // Escape untuk reset semua
            if (e.code === 'Escape') {
                resetAll();
            }
            
            // R untuk reset (alternative)
            if (e.code === 'KeyR' && e.ctrlKey) {
                e.preventDefault();
                resetAll();
            }
            
            // S untuk save (alternative)
            if (e.code === 'KeyS' && e.ctrlKey) {
                e.preventDefault();
                saveData();
            }
        });
        
        // Event listeners
        idleTimeoutSelect.addEventListener('change', updateIdleTimeout);
        document.getElementById('autoMode').addEventListener('change', function() {
            isAutoMode = this.checked;
        });
        
        // Touch events for mobile
        tasbihBtn.addEventListener('touchstart', function(e) {
            e.preventDefault();
            this.style.transform = 'scale(0.95)';
        });
        
        tasbihBtn.addEventListener('touchend', function(e) {
            e.preventDefault();
            this.style.transform = 'scale(1)';
            countBlink();
        });
        
        // Prevent context menu on mobile
        document.addEventListener('contextmenu', function(e) {
            if (e.target === tasbihBtn) {
                e.preventDefault();
            }
        });
        
        // Initialize on load
        window.addEventListener('DOMContentLoaded', init);
        
        // Handle orientation change
        window.addEventListener('orientationchange', function() {
            setTimeout(() => {
                window.scrollTo(0, 0);
            }, 100);
        });
    </script>
</body>
</html>