<!-- app/Views/tracking/leaflet.php -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    
    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
          crossorigin=""/>
    
    <style>
        body, html {
            height: 100%;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', sans-serif;
            overflow: hidden;
        }
        
        #map {
            height: 100vh;
            width: 100%;
        }
        
        .map-overlay {
            position: absolute;
            top: 10px;
            left: 10px;
            z-index: 1000;
            background: rgba(255, 255, 255, 0.95);
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            max-width: 300px;
            backdrop-filter: blur(5px);
        }
        
        .user-list {
            max-height: 300px;
            overflow-y: auto;
        }
        
        .user-item {
            padding: 8px;
            margin-bottom: 5px;
            border-radius: 5px;
            background: #f8f9fa;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .user-item:hover {
            background: #e9ecef;
        }
        
        .user-item.active {
            background: #0054a6;
            color: white;
        }
        
        .status-badge {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 5px;
        }
        
        .online { background-color: #28a745; }
        .idle { background-color: #ffc107; }
        .offline { background-color: #dc3545; }
        
        .legend {
            position: absolute;
            bottom: 20px;
            right: 20px;
            background: rgba(255, 255, 255, 0.95);
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            backdrop-filter: blur(5px);
        }
        
        .controls {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 1000;
        }
        
        .control-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: white;
            border: none;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }
        
        .leaflet-popup-content {
            min-width: 200px;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #0054a6;
        }
    </style>
</head>
<body>
    <!-- Map Container -->
    <div id="map"></div>
    
    <!-- Control Panel -->
    <div class="map-overlay">
        <h5><i class="bi bi-geo-alt-fill text-primary"></i> PLN Live Tracking</h5>
        
        <div class="form-check form-switch mb-3">
            <input class="form-check-input" type="checkbox" id="liveUpdate" checked>
            <label class="form-check-label" for="liveUpdate">Live Update</label>
        </div>
        
        <div class="mb-3">
            <div class="input-group input-group-sm">
                <input type="text" class="form-control" placeholder="Cari karyawan..." id="searchUser">
                <button class="btn btn-outline-primary" type="button">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </div>
        
        <h6>Karyawan Online: <span id="onlineCount">0</span></h6>
        <div class="user-list" id="userList"></div>
    </div>
    
    <!-- Legend -->
    <div class="legend">
        <h6><i class="bi bi-info-circle"></i> Legenda</h6>
        <div class="mb-1">
            <span class="status-badge online"></span> Online (<5m)
        </div>
        <div class="mb-1">
            <span class="status-badge idle"></span> Idle (5-15m)
        </div>
        <div class="mb-1">
            <span class="status-badge offline"></span> Offline (>15m)
        </div>
        <div class="mb-1">
            <i class="bi bi-geo-alt-fill text-primary"></i> Admin/Operator
        </div>
    </div>
    
    <!-- Control Buttons -->
    <div class="controls">
        <button class="control-btn" onclick="centerToMyLocation()" title="Lokasi Saya">
            <i class="bi bi-geo-alt text-primary"></i>
        </button>
        <button class="control-btn" onclick="zoomIn()" title="Zoom In">
            <i class="bi bi-plus-lg text-primary"></i>
        </button>
        <button class="control-btn" onclick="zoomOut()" title="Zoom Out">
            <i class="bi bi-dash-lg text-primary"></i>
        </button>
        <button class="control-btn" onclick="fitAllMarkers()" title="Lihat Semua">
            <i class="bi bi-fullscreen text-primary"></i>
        </button>
    </div>
    
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
            crossorigin=""></script>
    
    <!-- Leaflet Plugins -->
    <script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css">
    
    <script>
        let map;
        let markers = {};
        let markerCluster;
        let updateInterval;
        const defaultLat = <?= $default_lat ?>;
        const defaultLng = <?= $default_lng ?>;
        
        // Icon custom untuk Leaflet
        function createUserIcon(status, isAdmin = false) {
            let color;
            
            if (isAdmin) {
                color = '#0054a6'; // PLN Blue
            } else {
                switch(status) {
                    case 'online': color = '#28a745'; break;
                    case 'idle': color = '#ffc107'; break;
                    case 'offline': color = '#dc3545'; break;
                    default: color = '#6c757d';
                }
            }
            
            return L.divIcon({
                html: `<div style="
                    width: 25px;
                    height: 25px;
                    background: ${color};
                    border: 3px solid white;
                    border-radius: 50%;
                    box-shadow: 0 2px 5px rgba(0,0,0,0.3);
                "></div>`,
                iconSize: [25, 25],
                className: 'user-marker'
            });
        }
        
        // Initialize map
        function initMap() {
            // Initialize map dengan OpenStreetMap
            map = L.map('map').setView([defaultLat, defaultLng], 12);
            
            // Add OpenStreetMap tiles
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 19
            }).addTo(map);
            
            // Add Indonesian base map (alternatif)
            L.tileLayer('https://{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors, Tiles style by Humanitarian OpenStreetMap Team',
                maxZoom: 19
            }).addTo(map);
            
            // Initialize marker cluster
            markerCluster = L.markerClusterGroup({
                maxClusterRadius: 50,
                spiderfyOnMaxZoom: true,
                showCoverageOnHover: false,
                zoomToBoundsOnClick: true
            });
            map.addLayer(markerCluster);
            
            // Start live updates
            startLiveUpdates();
            
            // Update setiap 10 detik
            updateInterval = setInterval(updateMarkers, 10000);
            
            // Add map controls
            L.control.scale().addTo(map);
            
            // Try to get current location
            getCurrentLocation();
        }
        
        function getCurrentLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const pos = [position.coords.latitude, position.coords.longitude];
                        map.setView(pos, 15);
                        
                        // Add marker for current location
                        L.marker(pos, {
                            icon: L.divIcon({
                                html: `<div style="
                                    width: 15px;
                                    height: 15px;
                                    background: #0054a6;
                                    border: 3px solid white;
                                    border-radius: 50%;
                                    box-shadow: 0 2px 5px rgba(0,0,0,0.3);
                                "></div>`,
                                iconSize: [15, 15]
                            })
                        })
                        .addTo(map)
                        .bindPopup('<b>Lokasi Anda</b><br>Posisi saat ini')
                        .openPopup();
                    },
                    (error) => {
                        console.log('Geolocation error:', error);
                    }
                );
            }
        }
        
        function updateMarkers() {
            if (!document.getElementById('liveUpdate').checked) return;
            
            fetch('<?= base_url("tracking/getLiveLocations") ?>')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const onlineCount = data.data.length;
                        document.getElementById('onlineCount').textContent = onlineCount;
                        
                        // Clear old markers
                        markerCluster.clearLayers();
                        markers = {};
                        
                        // Add new markers
                        data.data.forEach(location => {
                            const position = [
                                parseFloat(location.latitude),
                                parseFloat(location.longitude)
                            ];
                            
                            // Determine status
                            const lastUpdate = new Date(location.timestamp);
                            const now = new Date();
                            const minutesDiff = (now - lastUpdate) / (1000 * 60);
                            
                            let status = 'online';
                            if (minutesDiff > 15) status = 'offline';
                            else if (minutesDiff > 5) status = 'idle';
                            
                            // Create marker
                            const marker = L.marker(position, {
                                icon: createUserIcon(status, location.role === 'admin')
                            });
                            
                            // Create popup content
                            const popupContent = `
                                <div class="leaflet-popup-content">
                                    <div class="d-flex align-items-center mb-2">
                                        ${location.foto ? 
                                            `<img src="<?= base_url('uploads/users/') ?>${location.foto}" 
                                                  class="user-avatar me-2">` :
                                            `<div class="user-avatar me-2 bg-light d-flex align-items-center justify-content-center">
                                                <i class="bi bi-person-fill text-secondary"></i>
                                            </div>`
                                        }
                                        <div>
                                            <h6 class="mb-0">${location.nama}</h6>
                                            <small class="text-muted">${location.nip}</small>
                                        </div>
                                    </div>
                                    <div class="mb-2">
                                        <strong>Unit:</strong> ${location.unit_kerja}<br>
                                        <strong>Status:</strong> <span class="badge bg-${status === 'online' ? 'success' : status === 'idle' ? 'warning' : 'danger'}">${status.toUpperCase()}</span><br>
                                        <strong>Update:</strong> ${formatTime(location.timestamp)}<br>
                                        <strong>Akurasi:</strong> ${location.accuracy ? location.accuracy + ' m' : 'N/A'}
                                    </div>
                                    <div class="text-center">
                                        <a href="<?= base_url('tracking/history/') ?>${location.user_id}" 
                                           class="btn btn-sm btn-primary">
                                            <i class="bi bi-clock-history"></i> Riwayat
                                        </a>
                                    </div>
                                </div>
                            `;
                            
                            marker.bindPopup(popupContent);
                            markerCluster.addLayer(marker);
                            markers[location.user_id] = marker;
                        });
                        
                        updateUserList(data.data);
                    }
                })
                .catch(error => {
                    console.error('Error fetching locations:', error);
                });
        }
        
        function updateUserList(users = []) {
            const userList = document.getElementById('userList');
            userList.innerHTML = '';
            
            users.forEach(user => {
                const lastUpdate = new Date(user.timestamp);
                const now = new Date();
                const minutesDiff = (now - lastUpdate) / (1000 * 60);
                
                let status = 'online';
                if (minutesDiff > 15) status = 'offline';
                else if (minutesDiff > 5) status = 'idle';
                
                const item = document.createElement('div');
                item.className = 'user-item';
                item.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="status-badge ${status}"></span>
                            <i class="bi bi-person-circle me-1"></i>
                            ${user.nama}
                        </div>
                        <small class="text-muted">${formatTime(user.timestamp)}</small>
                    </div>
                `;
                
                item.addEventListener('click', () => {
                    if (markers[user.user_id]) {
                        markers[user.user_id].openPopup();
                        map.setView(markers[user.user_id].getLatLng(), 15);
                    }
                });
                
                userList.appendChild(item);
            });
        }
        
        function formatTime(timestamp) {
            const date = new Date(timestamp);
            const now = new Date();
            const diffMs = now - date;
            const diffMins = Math.floor(diffMs / 60000);
            
            if (diffMins < 1) return 'Baru';
            if (diffMins < 60) return `${diffMins}m`;
            
            const diffHours = Math.floor(diffMins / 60);
            if (diffHours < 24) return `${diffHours}j`;
            
            return date.toLocaleDateString('id-ID');
        }
        
        function startLiveUpdates() {
            if (document.getElementById('liveUpdate').checked) {
                updateMarkers();
            }
        }
        
        function centerToMyLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        map.setView([
                            position.coords.latitude,
                            position.coords.longitude
                        ], 15);
                    },
                    () => {
                        alert('Tidak bisa mendapatkan lokasi saat ini');
                    }
                );
            }
        }
        
        function zoomIn() {
            map.zoomIn();
        }
        
        function zoomOut() {
            map.zoomOut();
        }
        
        function fitAllMarkers() {
            if (Object.keys(markers).length > 0) {
                const group = new L.featureGroup(Object.values(markers));
                map.fitBounds(group.getBounds().pad(0.1));
            }
        }
        
        // Event listener untuk live update toggle
        document.getElementById('liveUpdate').addEventListener('change', startLiveUpdates);
        
        // Search functionality
        document.getElementById('searchUser').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const userItems = document.querySelectorAll('.user-item');
            
            userItems.forEach(item => {
                const text = item.textContent.toLowerCase();
                item.style.display = text.includes(searchTerm) ? 'block' : 'none';
            });
        });
        
        // Initialize map when page loads
        window.addEventListener('DOMContentLoaded', initMap);
        
        // Cleanup interval saat page unload
        window.addEventListener('beforeunload', () => {
            if (updateInterval) {
                clearInterval(updateInterval);
            }
        });
    </script>
</body>
</html>