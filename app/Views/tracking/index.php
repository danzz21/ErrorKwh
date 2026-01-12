<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0">
                <i class="bi bi-geo-alt-fill text-primary me-2"></i>
                Live Tracking
            </h2>
            <p class="text-muted mb-0">
                Monitor lokasi karyawan PLN secara real-time
            </p>
        </div>
        <div>
            <a href="<?= base_url('tracking/leaflet') ?>" class="btn btn-primary me-2">
                <i class="bi bi-map me-1"></i> View Map
            </a>
            <a href="<?= base_url('tracking/mobile-tracker') ?>" class="btn btn-success" target="_blank">
                <i class="bi bi-phone me-1"></i> Mobile Tracker
            </a>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="bi bi-map me-2"></i> Peta Tracking</h6>
                    <div class="d-flex align-items-center">
                        <div class="spinner-border spinner-border-sm text-light me-2" id="mapLoading" style="display: none;" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <small>Auto refresh setiap 10 detik</small>
                    </div>
                </div>
                <div class="card-body p-0" style="height: 500px;">
                    <div id="map" style="height: 100%; width: 100%; border-radius: 5px;"></div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="bi bi-people me-2"></i> Online Users</h6>
                    <span class="badge bg-light text-dark" id="onlineCount">0</span>
                </div>
                <div class="card-body p-0">
                    <div id="userList" style="max-height: 350px; overflow-y: auto;">
                        <div class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2 text-muted">Loading users...</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card shadow">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i> Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="<?= base_url('tracking/history') ?>" class="btn btn-outline-primary">
                            <i class="bi bi-clock-history me-2"></i> My History
                        </a>
                        <a href="<?= base_url('tracking/mobile-tracker') ?>" class="btn btn-outline-success" target="_blank">
                            <i class="bi bi-phone me-2"></i> Open Mobile Tracker
                        </a>
                        <button onclick="refreshData()" class="btn btn-outline-warning">
                            <i class="bi bi-arrow-clockwise me-2"></i> Refresh Now
                        </button>
                        <?php if (session()->get('role') === 'admin'): ?>
                        <a href="<?= base_url('tracking/admin') ?>" class="btn btn-outline-danger">
                            <i class="bi bi-gear me-2"></i> Admin Panel
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include Leaflet CSS & JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<!-- Leaflet MarkerCluster untuk grouping markers -->
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css" />
<script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>

<!-- Tracker Script -->
<script>
    let map;
    let markers = {};
    let markerCluster;
    let refreshInterval;
    
    // Initialize map
    function initMap() {
        console.log('Initializing map...');
        
        try {
            // Default center to Jakarta
            map = L.map('map').setView([-6.2088, 106.8456], 12);
            
            // Add OpenStreetMap tiles
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors',
                maxZoom: 19
            }).addTo(map);
            
            // Initialize marker cluster
            markerCluster = L.markerClusterGroup({
                maxClusterRadius: 50,
                spiderfyOnMaxZoom: true,
                showCoverageOnHover: true,
                zoomToBoundsOnClick: true
            });
            map.addLayer(markerCluster);
            
            console.log('Map initialized successfully');
            
            // Load initial data
            refreshData();
            
            // Auto update dengan interval yang lebih baik
            startAutoRefresh();
            
        } catch (error) {
            console.error('Error initializing map:', error);
            showNotification('Gagal memuat peta: ' + error.message, 'danger');
        }
    }
    
    function startAutoRefresh() {
        // Clear existing interval
        if (refreshInterval) {
            clearInterval(refreshInterval);
        }
        
        // Set interval dengan delay untuk menghindari race condition
        refreshInterval = setInterval(() => {
            refreshData();
        }, 10000); // 10 detik
        
        console.log('Auto refresh started');
    }
    
    function refreshData() {
        console.log('Refreshing data...');
        
        // Update map dan user list secara sequential
        updateMap();
        
        // Delay sedikit untuk update user list
        setTimeout(() => {
            updateUserList();
        }, 1000);
    }
    
    function updateMap() {
        console.log('Updating map...');
        
        // Show loading indicator
        document.getElementById('mapLoading').style.display = 'inline-block';
        
        fetch('<?= base_url("tracking/getLiveLocations") ?>')
            .then(response => {
                console.log('Map API response status:', response.status);
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Map data received:', data);
                
                if (data.success) {
                    // Clear hanya markers yang sudah tidak online
                    const currentUserIds = new Set();
                    data.data.forEach(user => {
                        if (user.user_id) {
                            currentUserIds.add(parseInt(user.user_id));
                        }
                    });
                    
                    // Hapus markers untuk user yang sudah offline
                    Object.keys(markers).forEach(userId => {
                        if (!currentUserIds.has(parseInt(userId))) {
                            if (markers[userId]) {
                                markerCluster.removeLayer(markers[userId]);
                                delete markers[userId];
                            }
                        }
                    });
                    
                    // Update atau tambah markers untuk user online
                    data.data.forEach(location => {
                        if (location.latitude && location.longitude) {
                            const userId = location.user_id;
                            
                            // Pastikan data user valid
                            const userName = location.nama || `User ${userId}`;
                            const userNip = location.nip || '-';
                            const userUnit = location.unit_kerja || '-';
                            
                            // Determine marker color based on status
                            let markerColor = 'blue';
                            let statusText = 'Online';
                            
                            const lastUpdate = new Date(location.timestamp || location.last_seen);
                            const now = new Date();
                            const minutesDiff = Math.floor((now - lastUpdate) / (1000 * 60));
                            
                            if (minutesDiff > 5) {
                                markerColor = 'orange';
                                statusText = 'Idle';
                            }
                            if (minutesDiff > 15) {
                                markerColor = 'gray';
                                statusText = 'Offline';
                            }
                            
                            // Custom icon
                            const icon = L.divIcon({
                                html: `
                                    <div style="
                                        background-color: ${markerColor};
                                        width: 30px;
                                        height: 30px;
                                        border-radius: 50%;
                                        border: 3px solid white;
                                        box-shadow: 0 2px 5px rgba(0,0,0,0.3);
                                        display: flex;
                                        align-items: center;
                                        justify-content: center;
                                        color: white;
                                        font-weight: bold;
                                        font-size: 12px;
                                    ">
                                        <i class="bi bi-person"></i>
                                    </div>
                                `,
                                className: 'custom-marker',
                                iconSize: [30, 30],
                                iconAnchor: [15, 15]
                            });
                            
                            if (markers[userId]) {
                                // Update existing marker
                                markers[userId].setLatLng([location.latitude, location.longitude]);
                                markers[userId].setIcon(icon);
                                markers[userId].setPopupContent(`
                                    <div style="min-width: 200px;">
                                        <h6 class="fw-bold mb-1">${userName}</h6>
                                        <p class="mb-1">
                                            <i class="bi bi-person-badge me-1"></i>
                                            ${userNip}
                                        </p>
                                        <p class="mb-1">
                                            <i class="bi bi-building me-1"></i>
                                            ${userUnit}
                                        </p>
                                        <p class="mb-1">
                                            <i class="bi bi-clock me-1"></i>
                                            ${lastUpdate.toLocaleTimeString()}
                                        </p>
                                        <p class="mb-0">
                                            <i class="bi bi-geo-alt me-1"></i>
                                            ${parseFloat(location.latitude).toFixed(6)}, ${parseFloat(location.longitude).toFixed(6)}
                                        </p>
                                        <hr class="my-2">
                                        <small class="text-muted">
                                            <i class="bi bi-info-circle me-1"></i>
                                            Status: ${statusText} (${minutesDiff}m ago)
                                        </small>
                                    </div>
                                `);
                            } else {
                                // Create new marker
                                const marker = L.marker([location.latitude, location.longitude], { icon })
                                    .bindPopup(`
                                        <div style="min-width: 200px;">
                                            <h6 class="fw-bold mb-1">${userName}</h6>
                                            <p class="mb-1">
                                                <i class="bi bi-person-badge me-1"></i>
                                                ${userNip}
                                            </p>
                                            <p class="mb-1">
                                                <i class="bi bi-building me-1"></i>
                                                ${userUnit}
                                            </p>
                                            <p class="mb-1">
                                                <i class="bi bi-clock me-1"></i>
                                                ${lastUpdate.toLocaleTimeString()}
                                            </p>
                                            <p class="mb-0">
                                                <i class="bi bi-geo-alt me-1"></i>
                                                ${parseFloat(location.latitude).toFixed(6)}, ${parseFloat(location.longitude).toFixed(6)}
                                            </p>
                                            <hr class="my-2">
                                            <small class="text-muted">
                                                <i class="bi bi-info-circle me-1"></i>
                                                Status: ${statusText} (${minutesDiff}m ago)
                                            </small>
                                        </div>
                                    `)
                                    .on('click', function() {
                                        map.setView([location.latitude, location.longitude], 15);
                                    });
                                
                                markerCluster.addLayer(marker);
                                markers[userId] = marker;
                            }
                        }
                    });
                    
                    // Update online count
                    document.getElementById('onlineCount').textContent = Object.keys(markers).length;
                    
                    // Fit bounds jika ada markers
                    const markerLayers = markerCluster.getLayers();
                    if (markerLayers.length > 0) {
                        const group = new L.featureGroup(markerLayers);
                        map.fitBounds(group.getBounds(), { 
                            padding: [50, 50],
                            maxZoom: 15 
                        });
                    }
                    
                    console.log('Markers updated. Total:', Object.keys(markers).length);
                    
                } else {
                    console.error('Map API error:', data.error);
                    showNotification('Gagal memuat peta: ' + (data.error || 'Unknown error'), 'danger');
                }
            })
            .catch(error => {
                console.error('Error updating map:', error);
                showNotification('Error loading map: ' + error.message, 'danger');
            })
            .finally(() => {
                document.getElementById('mapLoading').style.display = 'none';
            });
    }
    
    function updateUserList() {
        console.log('Updating user list...');
        
        fetch('<?= base_url("tracking/getLiveLocations") ?>')
            .then(response => {
                console.log('User list API response status:', response.status);
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('User list data:', data);
                
                if (data.success) {
                    const userList = document.getElementById('userList');
                    
                    // Clear loading spinner
                    userList.innerHTML = '';
                    
                    if (data.data.length === 0) {
                        userList.innerHTML = `
                            <div class="text-center py-4">
                                <i class="bi bi-people display-4 text-muted mb-3"></i>
                                <p class="text-muted">Tidak ada user online</p>
                                <small class="text-muted">Last update: ${new Date().toLocaleTimeString('id-ID')}</small>
                            </div>
                        `;
                        return;
                    }
                    
                    // Filter unique users (by user_id) untuk menghindari duplikat
                    const uniqueUsers = [];
                    const seenUserIds = new Set();
                    
                    data.data.forEach(user => {
                        if (!seenUserIds.has(user.user_id)) {
                            seenUserIds.add(user.user_id);
                            uniqueUsers.push(user);
                        }
                    });
                    
                    console.log('Unique users:', uniqueUsers.length);
                    
                    // Render user list
                    uniqueUsers.forEach(user => {
                        const lastUpdate = new Date(user.timestamp || user.last_seen);
                        const now = new Date();
                        const minutesDiff = Math.floor((now - lastUpdate) / (1000 * 60));
                        
                        let statusColor = 'success';
                        let statusText = 'Online';
                        let statusIcon = 'bi-check-circle';
                        
                        if (minutesDiff > 5) {
                            statusColor = 'warning';
                            statusText = 'Idle';
                            statusIcon = 'bi-clock-history';
                        }
                        if (minutesDiff > 15) {
                            statusColor = 'secondary';
                            statusText = 'Offline';
                            statusIcon = 'bi-x-circle';
                        }
                        
                        // Pastikan data user valid
                        const userName = user.nama || `User ${user.user_id}`;
                        const userUnit = user.unit_kerja || '-';
                        const userNip = user.nip || '-';
                        
                        const item = document.createElement('div');
                        item.className = 'd-flex justify-content-between align-items-center p-3 border-bottom';
                        item.style.cursor = 'pointer';
                        item.onclick = () => {
                            if (user.latitude && user.longitude) {
                                if (markers[user.user_id]) {
                                    markers[user.user_id].openPopup();
                                    map.setView([user.latitude, user.longitude], 15);
                                }
                            }
                        };
                        
                        item.innerHTML = `
                            <div class="d-flex align-items-center">
                                <div class="position-relative me-3">
                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" 
                                         style="width: 40px; height: 40px;">
                                        <i class="bi bi-person fs-5"></i>
                                    </div>
                                    <span class="position-absolute top-0 start-100 translate-middle p-1 bg-${statusColor} border border-light rounded-circle">
                                        <i class="bi ${statusIcon} text-white" style="font-size: 8px;"></i>
                                    </span>
                                </div>
                                <div>
                                    <div class="fw-bold text-truncate" style="max-width: 150px;" title="${userName}">
                                        ${userName}
                                    </div>
                                    <small class="text-muted" title="${userUnit}">
                                        ${userUnit.length > 20 ? userUnit.substring(0, 20) + '...' : userUnit}
                                    </small>
                                    <br>
                                    <small class="text-muted">${userNip}</small>
                                </div>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-${statusColor} mb-1">${statusText}</span><br>
                                <small class="text-muted" title="${lastUpdate.toLocaleString()}">
                                    ${minutesDiff}m ago
                                </small>
                            </div>
                        `;
                        
                        userList.appendChild(item);
                    });
                    
                    // Update online count
                    document.getElementById('onlineCount').textContent = uniqueUsers.length;
                    
                    // Add refresh button at bottom
                    const refreshDiv = document.createElement('div');
                    refreshDiv.className = 'p-2 border-top';
                    refreshDiv.innerHTML = `
                        <button class="btn btn-sm btn-outline-primary w-100" onclick="updateUserList()">
                            <i class="bi bi-arrow-clockwise me-1"></i> Refresh List
                        </button>
                        <small class="text-muted d-block mt-1 text-center">
                            Updated: ${new Date().toLocaleTimeString('id-ID')}
                        </small>
                    `;
                    userList.appendChild(refreshDiv);
                    
                } else {
                    console.error('User list API error:', data.error);
                    
                    const userList = document.getElementById('userList');
                    userList.innerHTML = `
                        <div class="text-center py-3">
                            <i class="bi bi-exclamation-triangle text-warning display-4 mb-3"></i>
                            <p class="text-danger">Gagal memuat data</p>
                            <p class="text-muted small">${data.error || 'Unknown error'}</p>
                            <button class="btn btn-sm btn-primary" onclick="updateUserList()">
                                <i class="bi bi-arrow-clockwise"></i> Coba Lagi
                            </button>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error updating user list:', error);
                
                const userList = document.getElementById('userList');
                userList.innerHTML = `
                    <div class="text-center py-3">
                        <i class="bi bi-wifi-off text-danger display-4 mb-3"></i>
                        <p class="text-danger">Koneksi error</p>
                        <p class="text-muted small">${error.message}</p>
                        <button class="btn btn-sm btn-primary" onclick="updateUserList()">
                            <i class="bi bi-arrow-clockwise"></i> Coba Lagi
                        </button>
                    </div>
                `;
            });
    }
    
    function showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        `;
        
        const typeText = type === 'danger' ? 'Error!' : 
                        type === 'success' ? 'Success!' : 'Info';
        
        notification.innerHTML = `
            <strong>${typeText}</strong>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(notification);
        
        // Auto remove after 3 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 3000);
    }
    
    // Check if user is logged in
    function checkAuth() {
        fetch('<?= base_url("tracking/getLiveLocations") ?>')
            .then(response => {
                if (response.status === 401 || response.status === 403) {
                    // Redirect to login
                    window.location.href = '<?= base_url("auth/login") ?>';
                }
            })
            .catch(error => {
                console.error('Auth check failed:', error);
            });
    }
    
    // Handle visibility change
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            // Pause auto refresh ketika tab tidak aktif
            if (refreshInterval) {
                clearInterval(refreshInterval);
                refreshInterval = null;
                console.log('Auto refresh paused');
            }
        } else {
            // Resume auto refresh ketika tab aktif kembali
            startAutoRefresh();
            refreshData(); // Immediate refresh
            console.log('Auto refresh resumed');
        }
    });
    
    // Initialize when page loads
    window.addEventListener('DOMContentLoaded', () => {
        console.log('Page loaded, initializing...');
        
        try {
            initMap();
            checkAuth();
            
            // Check connection
            if (!navigator.onLine) {
                showNotification('Anda offline. Beberapa fitur mungkin tidak berfungsi.', 'warning');
            }
            
            // Listen for connection changes
            window.addEventListener('online', () => {
                showNotification('Koneksi pulih', 'success');
                refreshData();
            });
            
            window.addEventListener('offline', () => {
                showNotification('Anda offline', 'warning');
            });
            
            console.log('Initialization complete');
            
        } catch (error) {
            console.error('Initialization error:', error);
            showNotification('Gagal memuat aplikasi: ' + error.message, 'danger');
        }
    });
</script>

<style>
    .custom-marker {
        background: transparent !important;
        border: none !important;
    }
    
    .leaflet-popup-content {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    
    .leaflet-popup-content h6 {
        color: #0054a6;
        border-bottom: 2px solid #0054a6;
        padding-bottom: 5px;
    }
    
    #userList::-webkit-scrollbar {
        width: 5px;
    }
    
    #userList::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    #userList::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 10px;
    }
    
    #userList::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
</style>
<?= $this->endSection() ?>