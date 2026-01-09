<?php

namespace App\Controllers;

use App\Models\KwhModel;
use Dompdf\Dompdf;
use Dompdf\Options;

class Export extends BaseController
{
    protected $kwhModel;
    
    public function __construct()
    {
        $this->kwhModel = new KwhModel();
        helper(['text', 'url']);
    }
    
   public function pdf($id = null)
{
    // Simple header check
    if ($this->request->getGet('type') == 'all') {
        return $this->exportAll();
    }
    
    if ($id) {
        return $this->exportSingle($id);
    }
    
    return redirect()->back()->with('error', 'Parameter tidak valid');
}
    
    private function exportSingle($id)
    {
        $data = $this->kwhModel->find($id);
        
        if (!$data) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }
        
        $html = $this->generateSingleReport($data);
        
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'Arial');
        
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        // Output PDF
        $dompdf->stream(
            'Laporan_KWH_' . $data['keterangan'] . '_' . date('Ymd_His') . '.pdf',
            ['Attachment' => true]
        );
        
        exit;
    }
    
    private function exportAll()
    {
        $data = $this->kwhModel->orderBy('created_at', 'DESC')->findAll();
        
        $html = $this->generateAllReport($data);
        
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'Arial');
        
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        
        // Output PDF
        $dompdf->stream(
            'Laporan_KWH_All_' . date('Ymd_His') . '.pdf',
            ['Attachment' => true]
        );
        
        exit;
    }
    
    private function generateSingleReport($data)
    {
        // Decode photos jika ada
        $photos = [];
        if (!empty($data['photos']) && $data['photos'] !== 'null') {
            $photos = json_decode($data['photos'], true);
        }
        
        // Decode blink data jika ada
        $blinkData = [];
        if (!empty($data['blink_data']) && $data['blink_data'] !== 'null') {
            $blinkData = json_decode($data['blink_data'], true);
        }
        
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Laporan KWH Error Meter</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    font-size: 12px;
                    line-height: 1.5;
                    margin: 20px;
                }
                .header {
                    text-align: center;
                    margin-bottom: 30px;
                    border-bottom: 2px solid #333;
                    padding-bottom: 10px;
                }
                .header h1 {
                    color: #1e3c72;
                    margin: 0;
                    font-size: 24px;
                }
                .header h2 {
                    color: #666;
                    margin: 5px 0;
                    font-size: 18px;
                }
                .info-box {
                    background: #f8f9fa;
                    border: 1px solid #dee2e6;
                    border-radius: 5px;
                    padding: 15px;
                    margin-bottom: 20px;
                }
                .info-row {
                    display: flex;
                    margin-bottom: 8px;
                }
                .info-label {
                    font-weight: bold;
                    width: 200px;
                    color: #495057;
                }
                .info-value {
                    color: #212529;
                }
                .result-box {
                    background: #e7f3ff;
                    border: 2px solid #0d6efd;
                    border-radius: 5px;
                    padding: 15px;
                    margin: 20px 0;
                }
                .result-title {
                    color: #0d6efd;
                    font-weight: bold;
                    margin-bottom: 10px;
                    font-size: 16px;
                }
                .result-grid {
                    display: grid;
                    grid-template-columns: repeat(3, 1fr);
                    gap: 15px;
                }
                .result-item {
                    text-align: center;
                    padding: 10px;
                    background: white;
                    border-radius: 5px;
                    border: 1px solid #ced4da;
                }
                .result-value {
                    font-size: 20px;
                    font-weight: bold;
                    color: #1e3c72;
                }
                .result-label {
                    font-size: 12px;
                    color: #6c757d;
                }
                .error-badge {
                    display: inline-block;
                    padding: 5px 15px;
                    border-radius: 20px;
                    font-weight: bold;
                    color: white;
                    margin-top: 5px;
                }
                .error-good { background: #198754; }
                .error-warning { background: #ffc107; }
                .error-bad { background: #dc3545; }
                .table {
                    width: 100%;
                    border-collapse: collapse;
                    margin: 20px 0;
                }
                .table th, .table td {
                    border: 1px solid #dee2e6;
                    padding: 8px;
                    text-align: center;
                }
                .table th {
                    background: #1e3c72;
                    color: white;
                    font-weight: bold;
                }
                .table tr:nth-child(even) {
                    background: #f8f9fa;
                }
                .photo-section {
                    margin: 30px 0;
                }
                .photo-grid {
                    display: grid;
                    grid-template-columns: repeat(3, 1fr);
                    gap: 10px;
                    margin-top: 10px;
                }
                .photo-item {
                    text-align: center;
                }
                .photo-label {
                    font-size: 11px;
                    color: #6c757d;
                    margin-top: 5px;
                }
                .footer {
                    margin-top: 40px;
                    text-align: center;
                    color: #6c757d;
                    font-size: 11px;
                    border-top: 1px solid #dee2e6;
                    padding-top: 10px;
                }
                .logo {
                    text-align: center;
                    margin-bottom: 20px;
                }
                .logo-text {
                    font-size: 14px;
                    color: #1e3c72;
                    font-weight: bold;
                }
                .timestamp {
                    text-align: right;
                    color: #6c757d;
                    font-size: 10px;
                    margin-bottom: 20px;
                }
            </style>
        </head>
        <body>
            <div class="logo">
                <div class="logo-text">PLN - DIVISI METROLOGI</div>
                <div style="font-size: 12px; color: #2a5298;">KWH ERROR METER CALCULATOR</div>
            </div>
            
            <div class="timestamp">
                Dicetak pada: ' . date('d/m/Y H:i:s') . '
            </div>
            
            <div class="header">
                <h1>LAPORAN PENGUKURAN ERROR METER KWH</h1>
                <h2>' . htmlspecialchars($data['keterangan']) . '</h2>
            </div>
            
            <div class="info-box">
                <div class="info-row">
                    <div class="info-label">ID Pengukuran:</div>
                    <div class="info-value">#' . $data['id'] . '</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Tanggal & Waktu:</div>
                    <div class="info-value">' . date('d/m/Y H:i:s', strtotime($data['created_at'])) . '</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Lokasi:</div>
                    <div class="info-value">' . htmlspecialchars($data['keterangan']) . '</div>
                </div>
            </div>
            
            <div style="margin: 20px 0;">
                <h3 style="color: #1e3c72; border-bottom: 1px solid #dee2e6; padding-bottom: 5px;">
                    PARAMETER METERAN
                </h3>
                <div class="info-box">
                    <div class="info-row">
                        <div class="info-label">Arus (A):</div>
                        <div class="info-value">' . number_format($data['arus'], 2) . ' A</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Tegangan (V):</div>
                        <div class="info-value">' . number_format($data['tegangan'], 2) . ' V</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Cos φ:</div>
                        <div class="info-value">' . number_format($data['cosphi'], 2) . '</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Constanta:</div>
                        <div class="info-value">' . number_format($data['constanta'], 0) . ' imp/kWh</div>
                    </div>
                </div>
            </div>
            
            <div style="margin: 20px 0;">
                <h3 style="color: #1e3c72; border-bottom: 1px solid #dee2e6; padding-bottom: 5px;">
                    DATA PENGUKURAN
                </h3>
                <div class="info-box">
                    <div class="info-row">
                        <div class="info-label">Jumlah Kedipan:</div>
                        <div class="info-value">' . $data['count'] . ' kali</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Durasi Pengukuran:</div>
                        <div class="info-value">' . number_format($data['duration'], 2) . ' detik</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Kedipan per Detik:</div>
                        <div class="info-value">' . ($data['duration'] > 0 ? number_format($data['count'] / $data['duration'], 3) : 0) . '/detik</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Kedipan Terpilih:</div>
                        <div class="info-value">Kedipan ke-' . $data['selected_blink'] . '</div>
                    </div>
                </div>
            </div>
            
            <div class="result-box">
                <div class="result-title">HASIL PERHITUNGAN ERROR</div>
                <div class="result-grid">
                    <div class="result-item">
                        <div class="result-value">' . number_format($data['p1_kw'], 3) . '</div>
                        <div class="result-label">P1 (kW Meter)</div>
                        <div style="font-size: 10px; color: #6c757d;">Dari perhitungan kedipan</div>
                    </div>
                    <div class="result-item">
                        <div class="result-value">' . number_format($data['p2_kw'], 3) . '</div>
                        <div class="result-label">P2 (kW Teoritis)</div>
                        <div style="font-size: 10px; color: #6c757d;">Dari rumus P = V × I × cosφ</div>
                    </div>
                    <div class="result-item">';
        
       $errorPercent = $data['error_percent'];
$classMeter = $data['class_meter'] ?? 1.0;
$statusFinal = $data['status_final'] ?? 'BELUM_DIHITUNG';
$absError = abs($errorPercent);

// Logika status final
if ($absError > 5) {
    $finalStatusClass = 'error-bad';
    $finalStatusText = 'BURUK';
} elseif ($absError < $classMeter) {
    $finalStatusClass = 'error-warning';
    $finalStatusText = 'TIDAK STABIL';
} else {
    $finalStatusClass = 'error-good';
    $finalStatusText = 'BAIK';
}
        
       $html .= '
<div class="info-row">
    <div class="info-label">Kelas Meter:</div>
    <div class="info-value">' . number_format($classMeter, 1) . '%</div>
</div>';

// Update bagian status final:
$html .= '
<div class="result-item">
    <div class="result-value">' . number_format($errorPercent, 2) . '%</div>
    <div class="result-label">ERROR METER</div>
    <div class="error-badge ' . $finalStatusClass . '">' . $finalStatusText . '</div>
    <div style="font-size: 10px; margin-top: 5px; color: #6c757d;">
        Kelas: ' . number_format($classMeter, 1) . '% | 
        Logika: Error ' . ($absError < $classMeter ? '&lt;' : '≥') . ' Kelas
    </div>
</div>';
        
        // Tampilkan detail blink data jika ada
        if (!empty($blinkData) && is_array($blinkData)) {
            $html .= '
            <div style="margin: 20px 0;">
                <h3 style="color: #1e3c72; border-bottom: 1px solid #dee2e6; padding-bottom: 5px;">
                    DETAIL KEDIPAN
                </h3>
                <table class="table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kedipan ke-</th>
                            <th>Waktu (detik)</th>
                            <th>Kedipan/detik</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>';
            
            foreach ($blinkData as $index => $blink) {
                $isSelected = ($index + 1) == $data['selected_blink'];
                $blinkRate = $blink['time'] > 0 ? number_format($blink['count'] / $blink['time'], 3) : 0;
                
                $html .= '
                        <tr>
                            <td>' . ($index + 1) . '</td>
                            <td>' . $blink['count'] . '</td>
                            <td>' . number_format($blink['time'], 1) . '</td>
                            <td>' . $blinkRate . '</td>
                            <td>' . ($isSelected ? '<strong>DIGUNAKAN</strong>' : '') . '</td>
                        </tr>';
            }
            
            $html .= '
                    </tbody>
                </table>
            </div>';
        }
        
        // Tampilkan foto jika ada
        if (!empty($photos) && is_array($photos)) {
            $html .= '
            <div class="photo-section">
                <h3 style="color: #1e3c72; border-bottom: 1px solid #dee2e6; padding-bottom: 5px;">
                    DOKUMENTASI FOTO (' . count($photos) . ' foto)
                </h3>
                <div class="photo-grid">';
            
            foreach ($photos as $index => $photo) {
                $photoPath = WRITEPATH . 'uploads/kwh/' . $photo;
                if (file_exists($photoPath)) {
                    $imageData = base64_encode(file_get_contents($photoPath));
                    $src = 'data:image/jpeg;base64,' . $imageData;
                    
                    $html .= '
                    <div class="photo-item">
                        <img src="' . $src . '" style="max-width: 180px; max-height: 120px; object-fit: contain;">
                        <div class="photo-label">Foto ' . ($index + 1) . '</div>
                    </div>';
                }
            }
            
            $html .= '
                </div>
            </div>';
        }
        
        // Footer
        $html .= '
            <div class="footer">
                <div>Laporan ini dicetak secara otomatis dari Sistem KWH Error Calculator</div>
                <div>PLN - Divisi Metrologi | Validasi: _________________________</div>
                <div>Halaman 1/1</div>
            </div>
        </body>
        </html>';
        
        return $html;
    }
    
    private function generateAllReport($data)
    {
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Laporan KWH Error Meter - Semua Data</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    font-size: 10px;
                    line-height: 1.3;
                    margin: 15px;
                }
                .header {
                    text-align: center;
                    margin-bottom: 20px;
                    border-bottom: 2px solid #333;
                    padding-bottom: 10px;
                }
                .header h1 {
                    color: #1e3c72;
                    margin: 0;
                    font-size: 20px;
                }
                .header h2 {
                    color: #666;
                    margin: 5px 0;
                    font-size: 16px;
                }
                .table {
                    width: 100%;
                    border-collapse: collapse;
                    margin: 10px 0;
                }
                .table th, .table td {
                    border: 1px solid #dee2e6;
                    padding: 6px;
                    text-align: center;
                    vertical-align: middle;
                }
                .table th {
                    background: #1e3c72;
                    color: white;
                    font-weight: bold;
                    font-size: 11px;
                    position: sticky;
                    top: 0;
                }
                .table tr:nth-child(even) {
                    background: #f8f9fa;
                }
                .table tr:hover {
                    background: #e9ecef;
                }
                .error-cell {
                    font-weight: bold;
                }
                .error-good { color: #198754; }
                .error-warning { color: #ffc107; }
                .error-bad { color: #dc3545; }
                .photo-cell {
                    text-align: center;
                }
                .photo-thumb {
                    width: 40px;
                    height: 40px;
                    object-fit: cover;
                    border: 1px solid #dee2e6;
                    border-radius: 3px;
                }
                .footer {
                    margin-top: 30px;
                    text-align: center;
                    color: #6c757d;
                    font-size: 9px;
                    border-top: 1px solid #dee2e6;
                    padding-top: 8px;
                }
                .logo {
                    text-align: center;
                    margin-bottom: 15px;
                }
                .logo-text {
                    font-size: 12px;
                    color: #1e3c72;
                    font-weight: bold;
                }
                .timestamp {
                    text-align: right;
                    color: #6c757d;
                    font-size: 9px;
                    margin-bottom: 15px;
                }
                .summary {
                    margin: 15px 0;
                    padding: 10px;
                    background: #f8f9fa;
                    border: 1px solid #dee2e6;
                    border-radius: 5px;
                }
                .summary-row {
                    display: flex;
                    justify-content: space-between;
                    margin-bottom: 5px;
                }
                .summary-label {
                    font-weight: bold;
                }
                .summary-value {
                    font-weight: bold;
                    color: #1e3c72;
                }
            </style>
        </head>
        <body>
            <div class="logo">
                <div class="logo-text">PLN - DIVISI METROLOGI</div>
                <div style="font-size: 11px; color: #2a5298;">LAPORAN SEMUA PENGUKURAN ERROR METER KWH</div>
            </div>
            
            <div class="timestamp">
                Dicetak pada: ' . date('d/m/Y H:i:s') . ' | Total Data: ' . count($data) . '
            </div>
            
            <div class="header">
                <h1>REKAPITULASI PENGUKURAN ERROR METER KWH</h1>
                <h2>Periode: ' . (!empty($data) ? date('d/m/Y', strtotime($data[count($data)-1]['created_at'])) : '') . ' - ' . (!empty($data) ? date('d/m/Y', strtotime($data[0]['created_at'])) : '') . '</h2>
            </div>
            
            <div class="summary">
                <div class="summary-row">
                    <div class="summary-label">Total Pengukuran:</div>
                    <div class="summary-value">' . count($data) . ' data</div>
                </div>
                <div class="summary-row">
                    <div class="summary-label">Status Baik (Error ≤ 2%):</div>
                    <div class="summary-value">' . $this->countByErrorRange($data, 0, 2) . ' data</div>
                </div>
                <div class="summary-row">
                    <div class="summary-label">Status Warning (Error 2-5%):</div>
                    <div class="summary-value">' . $this->countByErrorRange($data, 2, 5) . ' data</div>
                </div>
                <div class="summary-row">
                    <div class="summary-label">Status Buruk (Error > 5%):</div>
                    <div class="summary-value">' . $this->countByErrorRange($data, 5, 1000) . ' data</div>
                </div>
            </div>
            
            <table class="table">
                <thead>
                    <tr>
                        <th width="30">No</th>
                        <th width="80">ID</th>
                        <th width="120">Waktu</th>
                        <th width="150">Lokasi</th>
                        <th width="60">Arus (A)</th>
                        <th width="80">Constanta</th>
                        <th width="70">Kedipan</th>
                        <th width="70">Durasi (s)</th>
                        <th width="80">P1 (kW)</th>
                        <th width="80">P2 (kW)</th>
                        <th width="80">Error (%)</th>
                        <th width="70">Status</th>
                        <th width="100">Foto</th>
                        <th width="100">Operator</th>
                    </tr>
                </thead>
                <tbody>';
        
        $totalGood = 0;
        $totalWarning = 0;
        $totalBad = 0;
        
        foreach ($data as $index => $row) {
            $errorPercent = $row['error_percent'];
            $absError = abs($errorPercent);
            
            $statusClass = '';
            $statusText = '';
            
            if ($absError <= 2) {
                $statusClass = 'error-good';
                $statusText = 'BAIK';
                $totalGood++;
            } elseif ($absError <= 5) {
                $statusClass = 'error-warning';
                $statusText = 'WARNING';
                $totalWarning++;
            } else {
                $statusClass = 'error-bad';
                $statusText = 'BURUK';
                $totalBad++;
            }
            
            // Get photos count
            $photoCount = 0;
            if (!empty($row['photos']) && $row['photos'] !== 'null') {
                $photos = json_decode($row['photos'], true);
                $photoCount = is_array($photos) ? count($photos) : 0;
            }
            
            // Get first photo for thumbnail
            $firstPhoto = '';
            if ($photoCount > 0) {
                $photos = json_decode($row['photos'], true);
                $firstPhotoPath = WRITEPATH . 'uploads/kwh/' . $photos[0];
                if (file_exists($firstPhotoPath)) {
                    $imageData = base64_encode(file_get_contents($firstPhotoPath));
                    $firstPhoto = 'data:image/jpeg;base64,' . $imageData;
                }
            }
            
            $html .= '
                    <tr>
                        <td>' . ($index + 1) . '</td>
                        <td>#' . $row['id'] . '</td>
                        <td>' . date('d/m/Y H:i', strtotime($row['created_at'])) . '</td>
                        <td>' . htmlspecialchars(substr($row['keterangan'], 0, 30)) . (strlen($row['keterangan']) > 30 ? '...' : '') . '</td>
                        <td>' . number_format($row['arus'], 2) . '</td>
                        <td>' . number_format($row['constanta'], 0) . '</td>
                        <td>' . $row['count'] . '</td>
                        <td>' . number_format($row['duration'], 1) . '</td>
                        <td>' . number_format($row['p1_kw'], 3) . '</td>
                        <td>' . number_format($row['p2_kw'], 3) . '</td>
                        <td class="error-cell ' . $statusClass . '">' . number_format($errorPercent, 2) . '%</td>
                        <td class="' . $statusClass . '">' . $statusText . '</td>
                        <td class="photo-cell">';
            
            if ($firstPhoto) {
                $html .= '<img src="' . $firstPhoto . '" class="photo-thumb" alt="Foto">';
                if ($photoCount > 1) {
                    $html .= '<div style="font-size: 9px; color: #6c757d;">+' . ($photoCount - 1) . '</div>';
                }
            } else {
                $html .= '<span style="color: #6c757d;">-</span>';
            }
            
            $html .= '</td>
                        <td>Operator</td>
                    </tr>';
        }
        
        $html .= '
                </tbody>
            </table>
            
            <div class="summary">
                <div class="summary-row">
                    <div class="summary-label">STATISTIK:</div>
                    <div class="summary-value"></div>
                </div>
                <div class="summary-row">
                    <div class="summary-label">• Status Baik:</div>
                    <div class="summary-value">' . $totalGood . ' data (' . number_format(($totalGood / count($data)) * 100, 1) . '%)</div>
                </div>
                <div class="summary-row">
                    <div class="summary-label">• Status Warning:</div>
                    <div class="summary-value">' . $totalWarning . ' data (' . number_format(($totalWarning / count($data)) * 100, 1) . '%)</div>
                </div>
                <div class="summary-row">
                    <div class="summary-label">• Status Buruk:</div>
                    <div class="summary-value">' . $totalBad . ' data (' . number_format(($totalBad / count($data)) * 100, 1) . '%)</div>
                </div>
            </div>
            
            <div class="footer">
                <div>Laporan dicetak otomatis dari Sistem KWH Error Calculator</div>
                <div>PLN - Divisi Metrologi | Halaman 1/1</div>
                <div>Validasi: ___________________________ Tanggal: ' . date('d/m/Y') . '</div>
            </div>
        </body>
        </html>';
        
        return $html;
    }
    
    private function countByErrorRange($data, $min, $max)
    {
        $count = 0;
        foreach ($data as $row) {
            $absError = abs($row['error_percent']);
            if ($absError >= $min && $absError <= $max) {
                $count++;
            }
        }
        return $count;
    }
}