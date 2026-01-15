<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0">
                <i class="bi bi-clock-history text-primary me-2"></i>
                Location History
            </h2>
            <p class="text-muted mb-0">
                <?php if (isset($target_user)): ?>
                    User: <strong><?= $target_user['nama'] ?></strong> (<?= $target_user['nip'] ?>)
                <?php else: ?>
                    My Location History
                <?php endif; ?>
            </p>
        </div>
        <div>
            <a href="<?= base_url('tracking') ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Tracking
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
            <?php if (!empty($locations)): ?>
                <div class="table-responsive">
                    <table class="table table-hover table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>Date & Time</th>
                                <th>Location</th>
                                <th>Coordinates</th>
                                <th>Accuracy</th>
                                <th>Device Info</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($locations as $location): ?>
                                <tr>
                                    <td>
                                        <small><?= date('d/m/Y', strtotime($location['timestamp'])) ?></small><br>
                                        <small class="text-muted"><?= date('H:i:s', strtotime($location['timestamp'])) ?></small>
                                    </td>
                                    <td>
                                        <small><?= $location['address'] ?? 'No address' ?></small>
                                    </td>
                                    <td>
                                        <small>
                                            <?= number_format($location['latitude'], 6) ?>,<br>
                                            <?= number_format($location['longitude'], 6) ?>
                                        </small>
                                    </td>
                                    <td>
                                        <?php if ($location['accuracy']): ?>
                                            <span class="badge bg-info"><?= round($location['accuracy']) ?>m</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <small><?= $location['device_info'] ?? 'Web' ?></small>
                                    </td>
                                    <td>
                                        <?php 
                                            $timeDiff = time() - strtotime($location['timestamp']);
                                            if ($timeDiff < 300): // 5 menit
                                        ?>
                                            <span class="badge bg-success">Online</span>
                                        <?php elseif ($timeDiff < 1800): // 30 menit ?>
                                            <span class="badge bg-warning">Recent</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Offline</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Map Section -->
                <div class="mt-4">
                    <h6 class="mb-3"><i class="bi bi-map me-2"></i>Location Map</h6>
                    <div id="map" style="height: 400px; border-radius: 8px;"></div>
                </div>
                
                <!-- Map Script -->
                <script>
                    // Initialize map
                    var map = L.map('map').setView([-6.2088, 106.8456], 13);
                    
                    // Add tile layer
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '© OpenStreetMap contributors'
                    }).addTo(map);
                    
                    // Add markers
                    <?php foreach ($locations as $location): ?>
                        <?php if ($location['latitude'] && $location['longitude']): ?>
                            L.marker([<?= $location['latitude'] ?>, <?= $location['longitude'] ?>])
                                .addTo(map)
                                .bindPopup(
                                    '<b><?= date('H:i', strtotime($location['timestamp'])) ?></b><br>' +
                                    '<?= addslashes($location['address'] ?? "Location") ?>'
                                );
                        <?php endif; ?>
                    <?php endforeach; ?>
                    
                    // Fit bounds to show all markers
                    var group = new L.featureGroup(
                        <?php 
                            $markers = [];
                            foreach ($locations as $location) {
                                if ($location['latitude'] && $location['longitude']) {
                                    $markers[] = "[{$location['latitude']}, {$location['longitude']}]";
                                }
                            }
                            if (!empty($markers)) {
                                echo '[' . implode(', ', $markers) . ']';
                            } else {
                                echo '[]';
                            }
                        ?>
                    );
                    
                    if (group.getLayers().length > 0) {
                        map.fitBounds(group.getBounds());
                    }
                </script>
                
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-geo-alt fs-1 text-muted d-block mb-3"></i>
                    <h5 class="text-muted">No location history found</h5>
                    <p class="text-muted">Start tracking to see location history here</p>
                    <a href="<?= base_url('tracking/mobile') ?>" class="btn btn-primary mt-2">
                        <i class="bi bi-phone me-2"></i>Start Tracking
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Leaflet CSS & JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<?= $this->endSection() ?>