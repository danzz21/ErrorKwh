<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?> - PLN Mobile Tracker</title>
    
    <!-- Bootstrap 5 + Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <!-- Leaflet CSS untuk map -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    
    <style>
        :root {
            --pln-blue: #0054a6;
            --pln-yellow: #ffd100;
            --online: #28a745;
            --offline: #dc3545;
            --idle: #ffc107;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 0;
            margin: 0;
        }
        
        .container {
            max-width: 500px;
            background: white;
            min-height: 100vh;
            padding: 0;
            box-shadow: 0 0 30px rgba(0,0,0,0.1);
        }
        
        /* Header */
        .tracker-header {
            background: linear-gradient(135deg, var(--pln-blue) 0%, #003d7a 100%);
            color: white;
            padding: 20px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .tracker-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="%23003d7a" fill-opacity="0.2" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,112C672,96,768,96,864,112C960,128,1056,160,1152,160C1248,160,1344,128,1392,112L1440,96L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>');
            background-size: cover;
            opacity: 0.3;
        }
        
        .tracker-header h1 {
            font-weight: 700;
            margin-bottom: 5px;
            position: relative;
            z-index: 1;
        }
        
        .user-info {
            font-size: 14px;
            opacity: 0.9;
            position: relative;
            z-index: 1;
        }
        
        /* Status Card */
        .status-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin: -40px 15px 20px 15px;
            box-shadow: 0 10px 30px rgba(0,84,166,0.15);
            position: relative;
            z-index: 10;
            border: 1px solid rgba(0,0,0,0.05);
        }
        
        .status-indicator {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 10px;
            vertical-align: middle;
        }
        
        .status-online { background: var(--online); box-shadow: 0 0 10px var(--online); }
        .status-offline { background: var(--offline); }
        .status-idle { background: var(--idle); }
        .status-error { background: #6c757d; }
        
        .status-text {
            font-size: 18px;
            font-weight: 600;
            color: #333;
        }
        
        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-bottom: 20px;
        }
        
        .stat-item {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            transition: all 0.3s;
        }
        
        .stat-item:hover {
            background: #e9ecef;
            transform: translateY(-2px);
        }
        
        .stat-label {
            font-size: 12px;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .stat-value {
            font-size: 18px;
            font-weight: 700;
            color: var(--pln-blue);
            margin-top: 5px;
        }
        
        /* Map Container */
        .map-container {
            height: 250px;
            width: 100%;
            border-radius: 15px;
            overflow: hidden;
            margin-bottom: 20px;
            border: 2px solid var(--pln-blue);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        #trackerMap {
            height: 100%;
            width: 100%;
        }
        
        /* Control Buttons */
        .btn-tracker {
            padding: 18px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 16px;
            transition: all 0.3s;
            margin-bottom: 10px;
            border: none;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .btn-start {
            background: linear-gradient(135deg, var(--online) 0%, #218838 100%);
            color: white;
        }
        
        .btn-start:hover {
            background: linear-gradient(135deg, #218838 0%, #1e7e34 100%);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40,167,69,0.4);
        }
        
        .btn-stop {
            background: linear-gradient(135deg, var(--offline) 0%, #c82333 100%);
            color: white;
        }
        
        .btn-stop:hover {
            background: linear-gradient(135deg, #c82333 0%, #bd2130 100%);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(220,53,69,0.4);
        }
        
        .btn-stop:disabled {
            opacity: 0.5;
            transform: none !important;
            box-shadow: none !important;
        }
        
        /* Activity Log */
        .log-container {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            border: 1px solid rgba(0,0,0,0.05);
        }
        
        .log-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .log-box {
            height: 200px;
            overflow-y: auto;
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
            font-size: 12px;
        }
        
        .log-entry {
            padding: 5px 0;
            border-bottom: 1px solid #e9ecef;
            line-height: 1.5;
        }
        
        .log-success { color: var(--online); }
        .log-error { color: var(--offline); }
        .log-warning { color: var(--idle); }
        .log-info { color: var(--pln-blue); }
        
        /* Battery Indicator */
        .battery-container {
            position: relative;
            width: 60px;
            height: 30px;
            border: 2px solid #333;
            border-radius: 4px;
            margin: 0 auto;
        }
        
        .battery-level {
            position: absolute;
            top: 2px;
            left: 2px;
            bottom: 2px;
            background: var(--online);
            border-radius: 2px;
            transition: width 0.5s;
        }
        
        .battery-tip {
            position: absolute;
            right: -6px;
            top: 8px;
            width: 4px;
            height: 12px;
            background: #333;
            border-radius: 0 2px 2px 0;
        }
        
        /* Loading Animation */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid var(--pln-blue);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Toast Notification */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            max-width: 350px;
        }
        
        .toast {
            background: white;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 10px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
            border-left: 5px solid var(--pln-blue);
            display: none;
            animation: slideIn 0.3s ease-out;
        }
        
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        
        /* Connection Status */
        .connection-status {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0,0,0,0.8);
            color: white;
            padding: 10px 20px;
            border-radius: 20px;
            font-size: 12px;
            display: none;
            z-index: 1000;
        }
        
        .connection-online { background: rgba(40,167,69,0.9); }
        .connection-offline { background: rgba(220,53,69,0.9); }
    </style>
</head>
<body>
    <!-- Toast Notifications -->
    <div class="toast-container" id="toastContainer"></div>
    
    <!-- Connection Status -->
    <div class="connection-status" id="connectionStatus"></div>
    
    <!-- Main Container -->
    <div class="container">
        <!-- Header -->
        <div class="tracker-header">
            <h1><i class="bi bi-geo-alt-fill"></i> PLN TRACKER</h1>
            <div class="user-info">
                <i class="bi bi-person-circle"></i> <?= esc($user_nama) ?>
                <span class="mx-2">•</span>
                ID: <?= $user_id ?>
            </div>
        </div>
        
        <!-- Status Card -->
        <div class="status-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <span class="status-indicator" id="statusIndicator"></span>
                    <span class="status-text" id="statusText">READY</span>
                </div>
                <div class="text-end">
                    <div class="stat-label">SESSION</div>
                    <div class="stat-value" id="sessionTime">00:00</div>
                </div>
            </div>
            
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-label">Akurasi</div>
                    <div class="stat-value" id="accuracyValue">-</div>
                </div>
                <div class="stat-item">
                    <div class="stat-label">Update</div>
                    <div class="stat-value" id="updateValue">-</div>
                </div>
                <div class="stat-item">
                    <div class="stat-label">Baterai</div>
                    <div class="stat-value">
                        <div class="battery-container">
                            <div class="battery-level" id="batteryLevel" style="width: 0%"></div>
                            <div class="battery-tip"></div>
                        </div>
                        <small id="batteryText">0%</small>
                    </div>
                </div>
                <div class="stat-item">
                    <div class="stat-label">Jarak</div>
                    <div class="stat-value" id="distanceValue">-</div>
                </div>
            </div>
        </div>
        
        <!-- Map -->
        <div class="map-container">
            <div id="trackerMap"></div>
        </div>
        
        <!-- Control Buttons -->
        <div class="px-3">
            <button class="btn-tracker btn-start" id="startBtn" onclick="startTracking()">
                <i class="bi bi-play-circle"></i> START TRACKING
            </button>
            
            <button class="btn-tracker btn-stop" id="stopBtn" onclick="stopTracking()" disabled>
                <i class="bi bi-stop-circle"></i> STOP TRACKING
            </button>
        </div>
        
        <!-- Activity Log -->
        <div class="log-container">
            <div class="log-header">
                <h5 class="mb-0"><i class="bi bi-clock-history"></i> Activity Log</h5>
                <button class="btn btn-sm btn-outline-secondary" onclick="clearLog()">
                    <i class="bi bi-trash"></i> Clear
                </button>
            </div>
            
            <div class="log-box" id="activityLog">
                <div class="log-entry log-info">Sistem siap digunakan</div>
                <div class="log-entry log-info">Tekan START untuk memulai tracking</div>
            </div>
            
            <div class="mt-3 text-center">
                <small class="text-muted">
                    <i class="bi bi-info-circle"></i> 
                    Lokasi akan dikirim otomatis setiap 30 detik
                </small>
            </div>
        </div>
        
        <!-- Hidden Fields -->
        <input type="hidden" id="csrfToken" value="<?= csrf_hash() ?>">
        <input type="hidden" id="userId" value="<?= $user_id ?>">
        <input type="hidden" id="trackingToken" value="<?= $tracking_token ?>">
        <input type="hidden" id="baseUrl" value="<?= base_url() ?>">
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Leaflet JS untuk map -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <!-- Tracker Script -->
   <!-- Tracker Script -->
<script>
    // ====== KONFIGURASI ======
    const CONFIG = {
    userId: document.getElementById('userId').value,
    token: document.getElementById('trackingToken').value,
    baseUrl: document.getElementById('baseUrl').value,
    
    // API Endpoints - GUNAKAN ROUTE BARU
    endpoints: {
        updateLocation: 'tracking-api/update',
        heartbeat: 'tracking-api/heartbeat',
        markOffline: 'tracking-api/mark-offline',
        updateBattery: 'tracking/updateBattery',
        checkStatus: 'tracking/checkStatus',
        startTracking: 'tracking/start',
        stopTracking: 'tracking/stop'
    },
        
        // Interval waktu (dalam milidetik)
        intervals: {
            locationUpdate: 30000,    // 30 detik
            heartbeat: 25000,         // 25 detik
            statusCheck: 10000        // 10 detik
        },
        
        // Threshold untuk status
        thresholds: {
            accuracyWarning: 50,      // meter
            batteryWarning: 20,       // persen
            maxRetry: 3               // maksimal retry
        }
    };

    // ====== VARIABEL GLOBAL ======
    let tracker = {
        // State tracking
        isTracking: false,
        isOnline: navigator.onLine,
        retryCount: 0,
        sessionStart: null,
        sessionTimer: null,
        
        // Geolocation
        watchId: null,
        currentPosition: null,
        lastPosition: null,
        
        // Intervals
        intervals: {
            location: null,
            heartbeat: null,
            status: null
        },
        
        // Map
        map: null,
        marker: null,
        
        // Battery
        battery: {
            level: 0,
            charging: false,
            lastUpdate: null
        },
        
        // Stats
        stats: {
            updatesSent: 0,
            updatesFailed: 0,
            distanceTraveled: 0,
            lastUpdateTime: null
        }
    };

    // ====== INITIALIZATION ======
    document.addEventListener('DOMContentLoaded', function() {
        initializeTracker();
        setupEventListeners();
        checkInitialStatus();
    });

    function initializeTracker() {
        addLog('Tracker initialized', 'info');
        
        // Initialize map
        initMap();
        
        // Check battery status
        initBattery();
        
        // Setup network monitoring
        updateConnectionStatus();
        
        // Generate session ID
        tracker.sessionId = generateSessionId();
        
        addLog('Session ID: ' + tracker.sessionId, 'info');
    }

    function initMap() {
        // Default center (PLN Pusat Jakarta)
        const defaultCenter = [-6.2088, 106.8456];
        
        // Create map
        tracker.map = L.map('trackerMap').setView(defaultCenter, 13);
        
        // Add OpenStreetMap tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(tracker.map);
        
        // Add marker
        tracker.marker = L.marker(defaultCenter, {
            draggable: false,
            title: 'Your Location'
        }).addTo(tracker.map)
          .bindPopup('Lokasi akan muncul saat tracking dimulai')
          .openPopup();
        
        addLog('Map initialized', 'info');
    }

    function initBattery() {
        if ('getBattery' in navigator) {
            navigator.getBattery().then(function(battery) {
                updateBatteryUI(battery);
                
                // Add event listeners
                battery.addEventListener('levelchange', function() {
                    updateBatteryUI(battery);
                    sendBatteryStatus(battery);
                });
                
                battery.addEventListener('chargingchange', function() {
                    updateBatteryUI(battery);
                    sendBatteryStatus(battery);
                });
                
                // Initial battery status
                sendBatteryStatus(battery);
            });
        } else if ('battery' in navigator) {
            navigator.battery.then(updateBatteryUI);
        } else {
            addLog('Battery API not supported', 'warning');
        }
    }

    // ====== TRACKING FUNCTIONS ======
    function startTracking() {
        if (!navigator.geolocation) {
            showToast('Browser tidak mendukung Geolocation API', 'error');
            return;
        }
        
        if (!tracker.isOnline) {
            showToast('Tidak ada koneksi internet', 'error');
            return;
        }
        
        // Start session
        tracker.sessionStart = new Date();
        startSessionTimer();
        
        // Request high accuracy location
        tracker.watchId = navigator.geolocation.watchPosition(
            handlePositionSuccess,
            handlePositionError,
            {
                enableHighAccuracy: true,
                maximumAge: 10000,
                timeout: 15000
            }
        );
        
        // Start intervals
        startIntervals();
        
        // Update UI
        updateStatus('active', 'Sedang melacak...');
        document.getElementById('startBtn').disabled = true;
        document.getElementById('stopBtn').disabled = false;
        
        // Send initial heartbeat
        sendHeartbeat();
        
        // Mark as online
        markOnline();
        
        addLog('Tracking dimulai', 'success');
        showToast('Tracking dimulai', 'success');
    }

    function stopTracking() {
        // Clear geolocation watch
        if (tracker.watchId) {
            navigator.geolocation.clearWatch(tracker.watchId);
            tracker.watchId = null;
        }
        
        // Clear all intervals
        stopIntervals();
        
        // Stop session timer
        stopSessionTimer();
        
        // Update UI
        updateStatus('idle', 'Tracking dihentikan');
        document.getElementById('startBtn').disabled = false;
        document.getElementById('stopBtn').disabled = true;
        
        // Mark as offline
        markOffline();
        
        // Reset stats
        tracker.stats.updatesSent = 0;
        tracker.stats.updatesFailed = 0;
        
        addLog('Tracking dihentikan', 'warning');
        showToast('Tracking dihentikan', 'warning');
    }

    function startIntervals() {
        // Location update interval
        tracker.intervals.location = setInterval(() => {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    handlePositionSuccess,
                    (error) => addLog('Interval update gagal: ' + error.message, 'error'),
                    { enableHighAccuracy: false, timeout: 5000 }
                );
            }
        }, CONFIG.intervals.locationUpdate);
        
        // Heartbeat interval
        tracker.intervals.heartbeat = setInterval(sendHeartbeat, CONFIG.intervals.heartbeat);
        
        // Status check interval
        tracker.intervals.status = setInterval(checkServerStatus, CONFIG.intervals.statusCheck);
    }

    function stopIntervals() {
        Object.values(tracker.intervals).forEach(interval => {
            if (interval) clearInterval(interval);
        });
        tracker.intervals = { location: null, heartbeat: null, status: null };
    }

    // ====== GEOLOCATION HANDLERS ======
    function handlePositionSuccess(position) {
        tracker.currentPosition = position;
        tracker.lastPosition = tracker.lastPosition || position;
        
        // Update UI dengan position
        updatePositionUI(position);
        
        // Update map
        updateMap(position);
        
        // Kirim ke server
        sendLocationToServer(position);
        
        // Hitung jarak tempuh
        calculateDistance(position);
        
        // Reset retry count
        tracker.retryCount = 0;
    }

    function handlePositionError(error) {
        console.error('Geolocation error:', error);
        
        let errorMessage = 'GPS Error: ';
        switch(error.code) {
            case error.PERMISSION_DENIED:
                errorMessage += 'Akses lokasi ditolak';
                showToast('Izinkan akses lokasi di pengaturan browser', 'error');
                break;
            case error.POSITION_UNAVAILABLE:
                errorMessage += 'Informasi lokasi tidak tersedia';
                break;
            case error.TIMEOUT:
                errorMessage += 'Request timeout';
                tracker.retryCount++;
                if (tracker.retryCount < CONFIG.thresholds.maxRetry) {
                    addLog('Mencoba lagi... (' + tracker.retryCount + ')', 'warning');
                    return;
                }
                break;
            default:
                errorMessage += 'Error tidak diketahui';
        }
        
        addLog(errorMessage, 'error');
        updateStatus('error', 'GPS Error');
        
        if (tracker.retryCount >= CONFIG.thresholds.maxRetry) {
            addLog('GPS tidak tersedia, switching ke mode manual', 'warning');
            updateStatus('idle', 'GPS tidak tersedia');
        }
    }

    // ====== SERVER COMMUNICATION ======
    async function sendLocationToServer(position) {
        const locationData = {
            user_id: CONFIG.userId,
            token: CONFIG.token,
            session_id: tracker.sessionId,
            latitude: position.coords.latitude,
            longitude: position.coords.longitude,
            accuracy: position.coords.accuracy,
            altitude: position.coords.altitude || null,
            speed: position.coords.speed || null,
            heading: position.coords.heading || null,
            timestamp: new Date().toISOString(),
            battery_level: tracker.battery.level,
            is_charging: tracker.battery.charging,
            device_info: navigator.userAgent
        };
        
        console.log('Sending location:', locationData);
        console.log('URL:', CONFIG.baseUrl + CONFIG.endpoints.updateLocation);
        
        try {
            const response = await fetch(CONFIG.baseUrl + CONFIG.endpoints.updateLocation, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(locationData)
            });
            
            console.log('Response status:', response.status);
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            const text = await response.text();
            console.log('Response text:', text);
            
            let result;
            try {
                result = JSON.parse(text);
            } catch (e) {
                console.error('JSON parse error:', e);
                addLog('Server returned invalid JSON', 'error');
                return false;
            }
            
            console.log('Response JSON:', result);
            
            if (result.success || result.status === 'success') {
                tracker.stats.updatesSent++;
                tracker.stats.lastUpdateTime = new Date();
                
                addLog(
                    `Lokasi terkirim: ${position.coords.latitude.toFixed(6)}, ${position.coords.longitude.toFixed(6)}`,
                    'success'
                );
                
                // Update last update time
                document.getElementById('updateValue').textContent = 
                    new Date().toLocaleTimeString('id-ID').slice(0,5);
                
                return true;
            } else {
                tracker.stats.updatesFailed++;
                addLog(`Gagal mengirim: ${result.error || result.message}`, 'error');
                return false;
            }
        } catch (error) {
            console.error('Fetch error:', error);
            tracker.stats.updatesFailed++;
            addLog(`Network error: ${error.message}`, 'error');
            updateConnectionStatus(false);
            return false;
        }
    }

    async function sendHeartbeat() {
        if (!tracker.isTracking) return;
        
        try {
            console.log('Sending heartbeat...');
            const response = await fetch(CONFIG.baseUrl + CONFIG.endpoints.heartbeat, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    user_id: CONFIG.userId,
                    token: CONFIG.token,
                    session_id: tracker.sessionId
                })
            });
            
            console.log('Heartbeat response status:', response.status);
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            const result = await response.json();
            console.log('Heartbeat response:', result);
            
            if (result.status === 'success') {
                addLog('Heartbeat updated', 'info');
                updateConnectionStatus(true);
            } else {
                addLog('Heartbeat failed: ' + (result.message || 'Unknown'), 'warning');
            }
        } catch (error) {
            console.error('Heartbeat error:', error);
            addLog('Heartbeat error: ' + error.message, 'error');
            updateConnectionStatus(false);
        }
    }

    async function markOnline() {
        try {
            console.log('Marking online...');
            await fetch(CONFIG.baseUrl + CONFIG.endpoints.startTracking, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            addLog('Status online dikirim', 'info');
        } catch (error) {
            console.error('Mark online error:', error);
            addLog('Gagal mengirim status online', 'error');
        }
    }

    async function markOffline() {
        try {
            console.log('Marking offline...');
            await fetch(CONFIG.baseUrl + CONFIG.endpoints.markOffline, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    user_id: CONFIG.userId,
                    token: CONFIG.token
                })
            });
            addLog('Status offline dikirim', 'info');
        } catch (error) {
            console.error('Mark offline error:', error);
        }
    }

    async function sendBatteryStatus(battery) {
        if (!tracker.isTracking) return;
        
        try {
            await fetch(CONFIG.baseUrl + CONFIG.endpoints.updateBattery, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    user_id: CONFIG.userId,
                    token: CONFIG.token,
                    battery_level: Math.round(battery.level * 100),
                    is_charging: battery.charging,
                    csrf_test_name: CONFIG.csrfToken
                })
            });
        } catch (error) {
            // Silent fail untuk battery updates
        }
    }

    async function checkServerStatus() {
        try {
            const response = await fetch(CONFIG.baseUrl + CONFIG.endpoints.checkStatus + '/' + CONFIG.userId);
            const result = await response.json();
            
            if (result.success) {
                // Update status berdasarkan server
                if (result.status === 'active' && !tracker.isTracking) {
                    addLog('Server mendeteksi Anda aktif', 'info');
                }
            }
        } catch (error) {
            // Silent fail untuk status check
        }
    }

    // ====== UI UPDATES ======
    function updateStatus(status, message) {
        const indicator = document.getElementById('statusIndicator');
        const text = document.getElementById('statusText');
        
        // Remove all classes
        indicator.className = 'status-indicator';
        text.textContent = message;
        
        tracker.isTracking = status === 'active';
        
        switch(status) {
            case 'active':
                indicator.classList.add('status-online');
                break;
            case 'idle':
                indicator.classList.add('status-idle');
                break;
            case 'error':
                indicator.classList.add('status-error');
                break;
            default:
                indicator.classList.add('status-offline');
        }
    }

    function updatePositionUI(position) {
        // Update accuracy
        const accuracy = Math.round(position.coords.accuracy);
        document.getElementById('accuracyValue').textContent = accuracy + 'm';
        
        // Warning jika accuracy buruk
        if (accuracy > CONFIG.thresholds.accuracyWarning) {
            document.getElementById('accuracyValue').style.color = 'var(--offline)';
        } else {
            document.getElementById('accuracyValue').style.color = 'var(--online)';
        }
        
        // Update time
        document.getElementById('updateValue').textContent = 
            new Date().toLocaleTimeString('id-ID').slice(0,5);
    }

    function updateMap(position) {
        const latLng = [position.coords.latitude, position.coords.longitude];
        
        // Update marker position
        tracker.marker.setLatLng(latLng);
        
        // Update popup
        tracker.marker.bindPopup(
            `<b>Lokasi Anda</b><br>
            Lat: ${position.coords.latitude.toFixed(6)}<br>
            Lng: ${position.coords.longitude.toFixed(6)}<br>
            Akurasi: ${Math.round(position.coords.accuracy)}m`
        );
        
        // Pan map to location (jika pertama kali atau jauh)
        if (!tracker.map.getBounds().contains(latLng)) {
            tracker.map.setView(latLng, 16);
        }
    }

    function updateBatteryUI(battery) {
        const level = Math.round(battery.level * 100);
        const isCharging = battery.charging;
        
        // Update tracker state
        tracker.battery.level = level;
        tracker.battery.charging = isCharging;
        tracker.battery.lastUpdate = new Date();
        
        // Update battery level bar
        const batteryLevel = document.getElementById('batteryLevel');
        batteryLevel.style.width = level + '%';
        
        // Update color based on level
        if (level <= CONFIG.thresholds.batteryWarning) {
            batteryLevel.style.background = 'var(--offline)';
        } else if (level <= 50) {
            batteryLevel.style.background = 'var(--idle)';
        } else {
            batteryLevel.style.background = 'var(--online)';
        }
        
        // Update text
        const batteryText = document.getElementById('batteryText');
        batteryText.textContent = level + '% ' + (isCharging ? '⚡' : '');
        
        // Warning jika baterai rendah
        if (level <= CONFIG.thresholds.batteryWarning && !isCharging) {
            batteryText.style.color = 'var(--offline)';
            batteryText.style.fontWeight = 'bold';
            
            if (level <= 10 && tracker.isTracking) {
                showToast('Baterai rendah! Segera charge', 'error');
            }
        } else {
            batteryText.style.color = '';
            batteryText.style.fontWeight = '';
        }
    }

    function updateConnectionStatus(isOnline) {
        tracker.isOnline = isOnline !== undefined ? isOnline : navigator.onLine;
        const statusEl = document.getElementById('connectionStatus');
        
        if (!tracker.isOnline) {
            statusEl.textContent = ' TIDAK TERKONEKSI ';
            statusEl.className = 'connection-status connection-offline';
            statusEl.style.display = 'block';
            
            if (tracker.isTracking) {
                addLog('Koneksi terputus', 'error');
                updateStatus('error', 'Koneksi terputus');
            }
        } else {
            statusEl.style.display = 'none';
            
            if (tracker.isTracking) {
                updateStatus('active', 'Sedang melacak...');
            }
        }
    }

    function calculateDistance(newPosition) {
        if (!tracker.lastPosition) {
            tracker.lastPosition = newPosition;
            return;
        }
        
        // Haversine formula untuk menghitung jarak
        const R = 6371e3; // meters
        const φ1 = tracker.lastPosition.coords.latitude * Math.PI/180;
        const φ2 = newPosition.coords.latitude * Math.PI/180;
        const Δφ = (newPosition.coords.latitude - tracker.lastPosition.coords.latitude) * Math.PI/180;
        const Δλ = (newPosition.coords.longitude - tracker.lastPosition.coords.longitude) * Math.PI/180;
        
        const a = Math.sin(Δφ/2) * Math.sin(Δφ/2) +
                Math.cos(φ1) * Math.cos(φ2) *
                Math.sin(Δλ/2) * Math.sin(Δλ/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        const distance = R * c;
        
        tracker.stats.distanceTraveled += distance;
        
        // Update UI
        let displayDistance;
        if (distance < 1000) {
            displayDistance = Math.round(distance) + ' m';
        } else {
            displayDistance = (distance / 1000).toFixed(2) + ' km';
        }
        
        document.getElementById('distanceValue').textContent = displayDistance;
        tracker.lastPosition = newPosition;
    }

    function startSessionTimer() {
        if (tracker.sessionTimer) clearInterval(tracker.sessionTimer);
        
        tracker.sessionTimer = setInterval(() => {
            if (!tracker.sessionStart) return;
            
            const now = new Date();
            const diff = Math.floor((now - tracker.sessionStart) / 1000);
            const minutes = Math.floor(diff / 60);
            const seconds = diff % 60;
            
            document.getElementById('sessionTime').textContent = 
                minutes.toString().padStart(2, '0') + ':' + 
                seconds.toString().padStart(2, '0');
        }, 1000);
    }

    function stopSessionTimer() {
        if (tracker.sessionTimer) {
            clearInterval(tracker.sessionTimer);
            tracker.sessionTimer = null;
        }
        document.getElementById('sessionTime').textContent = '00:00';
    }

    // ====== UTILITY FUNCTIONS ======
    function addLog(message, type = 'info') {
        const logBox = document.getElementById('activityLog');
        const entry = document.createElement('div');
        entry.className = 'log-entry log-' + type;
        entry.innerHTML = `
            <span style="color: #666">${new Date().toLocaleTimeString('id-ID')}:</span>
            ${message}
        `;
        
        logBox.prepend(entry);
        
        // Keep only last 50 entries
        if (logBox.children.length > 50) {
            logBox.removeChild(logBox.lastChild);
        }
        
        // Auto scroll to top
        logBox.scrollTop = 0;
    }

    function showToast(message, type = 'info') {
        const container = document.getElementById('toastContainer');
        const toast = document.createElement('div');
        toast.className = 'toast';
        toast.innerHTML = `
            <div style="display: flex; align-items: start;">
                <i class="bi bi-${getToastIcon(type)} me-2" style="font-size: 1.2rem;"></i>
                <div>
                    <strong>${message}</strong>
                    <div style="font-size: 0.8rem; opacity: 0.8">${new Date().toLocaleTimeString('id-ID')}</div>
                </div>
            </div>
        `;
        
        // Set border color based on type
        const colors = {
            success: 'var(--online)',
            error: 'var(--offline)',
            warning: 'var(--idle)',
            info: 'var(--pln-blue)'
        };
        toast.style.borderLeftColor = colors[type] || colors.info;
        
        container.appendChild(toast);
        
        // Show toast
        toast.style.display = 'block';
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(100%)';
            setTimeout(() => container.removeChild(toast), 300);
        }, 5000);
    }

    function getToastIcon(type) {
        switch(type) {
            case 'success': return 'check-circle-fill';
            case 'error': return 'exclamation-circle-fill';
            case 'warning': return 'exclamation-triangle-fill';
            default: return 'info-circle-fill';
        }
    }

    function clearLog() {
        if (confirm('Hapus semua log?')) {
            document.getElementById('activityLog').innerHTML = 
                '<div class="log-entry log-info">Log telah dihapus</div>';
        }
    }

    function generateSessionId() {
        return 'sess_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
    }

    function checkInitialStatus() {
        // Check geolocation support
        if (!navigator.geolocation) {
            showToast('Browser tidak mendukung Geolocation', 'error');
            updateStatus('error', 'Geolocation tidak didukung');
        }
        
        // Check network status
        updateConnectionStatus();
        
        // Check if already tracking (from previous session)
        setTimeout(() => {
            addLog('Sistem siap digunakan', 'info');
            showToast('Selamat datang di PLN Tracker', 'success');
        }, 1000);
    }

    // ====== EVENT LISTENERS ======
    function setupEventListeners() {
        // Network status changes
        window.addEventListener('online', () => {
            addLog('Koneksi internet tersedia', 'success');
            updateConnectionStatus(true);
            showToast('Terhubung ke internet', 'success');
        });
        
        window.addEventListener('offline', () => {
            addLog('Koneksi internet terputus', 'error');
            updateConnectionStatus(false);
            showToast('Koneksi internet terputus', 'error');
        });
        
        // Page visibility
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                addLog('Aplikasi berjalan di background', 'warning');
            } else {
                addLog('Aplikasi aktif kembali', 'info');
                if (tracker.isTracking) {
                    sendHeartbeat(); // Immediate heartbeat on resume
                }
            }
        });
        
        // Before page unload (tab/window close)
        window.addEventListener('beforeunload', (event) => {
            if (tracker.isTracking) {
                // Try to send offline status via Beacon API
                const data = new Blob([JSON.stringify({
                    user_id: CONFIG.userId,
                    token: CONFIG.token
                })], { type: 'application/json' });
                
                if (navigator.sendBeacon) {
                    navigator.sendBeacon(
                        CONFIG.baseUrl + CONFIG.endpoints.markOffline, 
                        data
                    );
                }
                
                // Optional: Show confirmation
                event.preventDefault();
                event.returnValue = 'Anda sedang dalam sesi tracking. Yakin ingin keluar?';
                return event.returnValue;
            }
        });
        
        // Battery low warning
        window.addEventListener('batterylow', () => {
            showToast('Baterai rendah! Segera charge', 'error');
        });
        
        // Handle back button on mobile
        window.addEventListener('popstate', () => {
            if (tracker.isTracking) {
                if (confirm('Anda sedang tracking. Yakin ingin keluar?')) {
                    stopTracking();
                } else {
                    history.pushState(null, null, window.location.href);
                }
            }
        });
        
        // Prevent accidental refresh
        window.addEventListener('beforeunload', (e) => {
            if (tracker.isTracking) {
                e.preventDefault();
                e.returnValue = '';
            }
        });
    }

    // ====== DEBUG FUNCTIONS (optional) ======
    window.debugTracker = {
        getState: () => ({ ...tracker, CONFIG }),
        forceUpdate: () => {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(handlePositionSuccess);
            }
        },
        simulateError: () => {
            addLog('Simulated error for testing', 'error');
            updateStatus('error', 'Testing Error');
        },
        clearStats: () => {
            tracker.stats = {
                updatesSent: 0,
                updatesFailed: 0,
                distanceTraveled: 0,
                lastUpdateTime: null
            };
            addLog('Stats cleared', 'info');
        }
    };

    // Expose untuk debugging di console
    console.log('PLN Tracker loaded. Type debugTracker to access debug functions.');
</script>
</body>
</html>