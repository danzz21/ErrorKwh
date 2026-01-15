<?php

namespace App\Controllers;

use App\Models\KwhModel;
use App\Models\UserModel;
use Dompdf\Dompdf;
use Dompdf\Options;

class Export extends BaseController
{
    protected $kwhModel;
    protected $userModel;
    
    public function __construct()
    {
        $this->kwhModel = new KwhModel();
        $this->userModel = new UserModel();
        helper(['text', 'url', 'date', 'number']);
    }
    
    public function pdf($id = null)
    {
        if (!session()->has('user_id')) {
            return redirect()->to('/auth');
        }
        
        if ($id) {
            return $this->exportSingleOnePage($id);
        } else {
            return $this->exportAllOnePage();
        }
    }
    
    private function exportAllOnePage()
    {
        $userId = session()->get('user_id');
        $userRole = session()->get('user_role');
        
        $query = $this->kwhModel->orderBy('created_at', 'DESC');
        
        if ($userRole !== 'admin') {
            $query->where('user_id', $userId);
        }
        
        $kwhData = $query->findAll();
        
        if (empty($kwhData)) {
            return redirect()->back()->with('error', 'Tidak ada data untuk diexport');
        }
        
        $html = $this->generateAllDataOnePage($kwhData);
        $filename = 'Laporan_KWH_All_' . date('Ymd_His') . '.pdf';
        return $this->generatePdf($html, $filename, 'landscape');
    }
    
    private function exportSingleOnePage($id)
    {
        $data = $this->kwhModel->find($id);
        
        if (!$data) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }
        
        $userRole = session()->get('user_role');
        if ($userRole !== 'admin' && $data['user_id'] != session()->get('user_id')) {
            return redirect()->back()->with('error', 'Tidak memiliki akses');
        }
        
        $user = null;
        if (!empty($data['user_id'])) {
            $user = $this->userModel->find($data['user_id']);
        }
        
        $html = $this->generateSingleOnePage($data, $user);
        $filename = 'KWH_' . $data['id'] . '_' . date('Ymd_His') . '.pdf';
        return $this->generatePdf($html, $filename, 'landscape');
    }
    
 private function generateSingleOnePage($data, $user)
{
    $photos = [];
    if (!empty($data['photos']) && $data['photos'] !== 'null') {
        $photos = json_decode($data['photos'], true);
    }

    $errorPercent = $data['error_percent'] ?? 0;
    $absError = abs($errorPercent);
    $classMeter = $data['class_meter'] ?? 1.0;

    // Periksa mode perhitungan
    $calculationMode = $data['calculation_mode'] ?? 'mode1';
    
    if ($absError > 5) {
        $status = 'DI LUAR KELAS METER ';
        $statusColor = '#dc3545';
    } elseif (abs($absError - $classMeter) > 0.01) {
        $status = 'DI LUAR KELAS METER';
        $statusColor = '#ffc107';
    } else {
        $status = 'BAIK';
        $statusColor = '#198754';
    }

    $operatorName = $user['nama'] ?? 'Operator';
    $operatorNip  = $user['nip'] ?? '-';
    $operatorJob  = $user['jabatan'] ?? '-';

    // Tentukan apakah menampilkan parameter pengukuran atau strip
    $tegangan = '-';
    $arus = '-';
    $cosphi = '-';
    $constanta = '-';
    
    // Hanya tampilkan parameter jika menggunakan mode1 (kedipan)
    if ($calculationMode === 'mode1') {
        $tegangan = $data['tegangan'] . ' V';
        $arus = $data['arus'] . ' A';
        $cosphi = $data['cosphi'];
        $constanta = $data['constanta'] . ' imp/kWh';
    }

    return '
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
@page { size: A4 landscape; margin: 1cm; }
body {
    font-family: Arial, Helvetica, sans-serif;
    font-size: 12px;
    margin: 0;
    color: #2c2c2c;
}
table { width:100%; border-collapse: collapse; }
td { vertical-align: top; }
.header {
    text-align: center;
    padding: 10px 0 12px 0;
    margin-bottom: 14px;
    border-bottom: 4px double #0d47a1;
}
.header h1 {
    font-size: 20px;
    margin: 0;
    color: #0d47a1;
}
.header small {
    font-size: 11px;
    color: #555;
}
.section {
    border: 1px solid #dcdcdc;
    border-radius: 8px;
    padding: 14px;
    margin-bottom: 14px;
    background: #fdfdfd;
}
.section-title {
    font-weight: bold;
    font-size: 14px;
    color: #0d47a1;
    margin-bottom: 10px;
    padding-bottom: 6px;
    border-bottom: 2px solid #e6e6e6;
}
.row { margin-bottom: 8px; line-height: 1.5; }
.result {
    font-size: 32px;
    font-weight: bold;
    margin-bottom: 10px;
}
.badge {
    display: inline-block;
    padding: 8px 20px;
    border-radius: 18px;
    font-size: 12px;
    font-weight: bold;
    color: #fff;
}
.photo-box {
    height: 130px;
    border: 2px dashed #bbb;
    border-radius: 6px;
    text-align: center;
}
.photo-box img {
    max-height: 126px;
    max-width: 100%;
}
.footer {
    margin-top: 12px;
    padding-top: 8px;
    border-top: 2px solid #e0e0e0;
    font-size: 9px;
    text-align: center;
    color: #666;
}
td[width="50%"]:first-child {
    padding-right: 10px;
    border-right: 2px solid #eee;
}
td[width="50%"]:last-child {
    padding-left: 10px;
}
.mode-indicator {
    font-size: 10px;
    color: #666;
    font-style: italic;
    margin-bottom: 5px;
}
</style>
</head>

<body>

<div class="header">
    <h1>LAPORAN PENGUKURAN ERROR METER KWH</h1>
    <small>PT PLN (PERSERO)</small>
</div>

<table>
<tr>

<!-- KOLOM KIRI -->
<td width="50%">

    <div class="section">
        <div class="section-title">DATA PELANGGAN</div>
        <div class="row">Nama : <b>'.$data['nama_pelanggan'].'</b></div>
        <div class="row">ID Pelanggan : <b>'.$data['id_pelanggan'].'</b></div>
        <div class="row">Kelas Meter : <b>'.number_format($classMeter,1).' %</b></div>
        <div class="row">Mode Perhitungan : <b>'.($calculationMode === 'mode1' ? 'KEDIPAN' : '3 PHASE').'</b></div>
    </div>

    <div class="section">
        <div class="section-title">PERHITUNGAN ENERGI</div>
        <table>
            <tr>
                <td align="center">P1<br><b>'.$data['p1_kw'].' kW</b></td>
                <td align="center">P2<br><b>'.$data['p2_kw'].' kW</b></td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">PARAMETER PENGUKURAN</div>
        '.($calculationMode === 'mode1' ? '' : '<div class="mode-indicator">(Parameter tidak diperlukan untuk mode 3 Phase)</div>').'
        <div class="row">Tegangan : <b>'.$tegangan.'</b></div>
        <div class="row">Arus : <b>'.$arus.'</b></div>
        <div class="row">Cos φ : <b>'.$cosphi.'</b></div>
        <div class="row">Konstanta : <b>'.$constanta.'</b></div>
    </div>

</td>

<!-- KOLOM KANAN -->
<td width="50%">

    <div class="section">
        <div class="section-title">HASIL PENGUKURAN</div>

        <div class="result" style="color:'.$statusColor.'">
            '.number_format($errorPercent,2).' %
        </div>

        <span class="badge" style="background:'.$statusColor.'">'.$status.'</span>

        <div style="margin-top:10px; font-size:11px; color:#555;">
            <b>Rumus Error:</b><br>
            <span style="font-family: monospace;">
                Error (%) = ((P1 - P2) / P2) × 100
            </span>
        </div>
    </div>

    '.(!empty($photos) ? '
    <div class="section">
        <div class="section-title">DOKUMENTASI</div>
        <table><tr>'.$this->renderPhotoTable($photos).'</tr></table>
    </div>' : '').'

    <div class="section">
        <div class="section-title">PELAKSANA</div>
        <div class="row">Nama : <b>'.$operatorName.'</b></div>
        <div class="row">NIP : <b>'.$operatorNip.'</b></div>
        <div class="row">Jabatan : <b>'.$operatorJob.'</b></div>
        <div class="row">Tanggal : <b>'.date('d/m/Y H:i', strtotime($data['created_at'])).'</b></div>
    </div>

</td>
</tr>
</table>

<div class="footer">
    Dicetak '.date('d/m/Y H:i:s').' | ID Laporan #'.$data['id'].'
</div>

</body>
</html>';
}
    
    private function generatePhotoHtml($photos)
    {
        $html = '';
        $displayPhotos = array_slice($photos, 0, 2); // Maksimal 2 foto agar tidak terlalu besar
        
        foreach ($displayPhotos as $index => $photo) {
            $photoPath = WRITEPATH . 'uploads/kwh/' . $photo;
            if (file_exists($photoPath)) {
                try {
                    $imageData = base64_encode(file_get_contents($photoPath));
                    $src = 'data:image/jpeg;base64,' . $imageData;
                    $html .= '
                        <div class="photo-item">
                            <img src="' . $src . '" style="width: 100%; height: 100%; object-fit: contain; background: #f8f9fa;">
                            <div class="photo-number">' . ($index + 1) . '</div>
                        </div>';
                } catch (\Exception $e) {
                    $html .= '
                        <div class="photo-item" style="display: flex; align-items: center; justify-content: center; background: #f8f9fa;">
                            <div style="font-size: 10px; color: #999; text-align: center;">Foto ' . ($index + 1) . '</div>
                        </div>';
                }
            }
        }
        
        return $html;
    }
    
    private function getConclusionText($status, $absError, $classMeter)
    {
        if ($status === 'BAIK') {
            return 'Meter bekerja dengan akurat sesuai kelas meter ' . $classMeter . '%. Tidak diperlukan tindakan lebih lanjut.';
        } elseif ($status === 'DI LUAR KELAS METER') {
            return 'Meter tidak bekerja sesuai kelas yang ditentukan (Error ' . $absError . '% ≠ Kelas ' . $classMeter . '%). Disarankan penyesuaian atau kalibrasi ulang.';
        } else {
            return 'Meter menunjukkan error yang signifikan (' . $absError . '% > 5%). Perlu kalibrasi ulang segera untuk memastikan keakuratan pengukuran.';
        }
    }
    
    private function generateAllDataOnePage($kwhData)
    {
        $totalData = count($kwhData);
        $statistik = $this->hitungStatistikKomprehensif($kwhData);
        $recentData = array_slice($kwhData, 0, 5); // Hanya 5 data
        
        $currentTime = date('d/m/Y H:i:s');
        
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>LAPORAN STATISTIK KWH</title>
            <style>
                @page {
                    size: A4 landscape;
                    margin: 1cm;
                }
                body {
                    font-family: Arial, sans-serif;
                    font-size: 9px;
                    line-height: 1.2;
                    margin: 0;
                    padding: 0;
                    color: #333;
                }
                
                /* LAYOUT 2 KOLOM */
                .page-container {
                    display: flex;
                    width: 100%;
                    height: 100%;
                }
                
                /* KOLOM KIRI - 50% */
                .left-column {
                    width: 50%;
                    padding-right: 15px;
                    border-right: 1px solid #ddd;
                }
                
                /* KOLOM KANAN - 50% */
                .right-column {
                    width: 50%;
                    padding-left: 15px;
                }
                
                /* HEADER */
                .header {
                    text-align: center;
                    padding: 5px 0 10px 0;
                    border-bottom: 2px solid #1e3c72;
                    margin-bottom: 15px;
                }
                .header-title {
                    color: #1e3c72;
                    font-size: 13px;
                    font-weight: bold;
                    margin: 0 0 5px 0;
                }
                .header-subtitle {
                    font-size: 9px;
                    color: #666;
                }
                
                /* SECTION STYLE */
                .section {
                    margin-bottom: 15px;
                }
                
                /* SECTION TITLES */
                .section-title {
                    color: #1e3c72;
                    font-size: 11px;
                    font-weight: bold;
                    margin: 0 0 10px 0;
                    padding-bottom: 5px;
                    border-bottom: 1px solid #eee;
                }
                
                /* STATISTIK */
                .stats-grid {
                    display: grid;
                    grid-template-columns: repeat(2, 1fr);
                    gap: 10px;
                    margin-bottom: 15px;
                }
                .stat-item {
                    text-align: center;
                    padding: 10px;
                    background: #f8f9fa;
                    border-radius: 5px;
                    border: 1px solid #dee2e6;
                }
                .stat-label {
                    font-size: 9px;
                    color: #666;
                    margin-bottom: 5px;
                }
                .stat-value {
                    font-size: 12px;
                    font-weight: bold;
                    color: #1e3c72;
                }
                
                /* CHART */
                .chart-row {
                    display: flex;
                    align-items: center;
                    margin-bottom: 8px;
                }
                .chart-label {
                    width: 60px;
                    font-size: 9px;
                    color: #555;
                }
                .chart-bar {
                    flex: 1;
                    height: 15px;
                    background: #e9ecef;
                    border-radius: 3px;
                    margin: 0 10px;
                    overflow: hidden;
                }
                .chart-fill {
                    height: 100%;
                    border-radius: 3px;
                }
                .chart-percent {
                    width: 40px;
                    font-size: 9px;
                    font-weight: bold;
                    text-align: right;
                }
                
                /* DATA TABLE */
                .data-table {
                    width: 100%;
                    border-collapse: collapse;
                    font-size: 8px;
                    margin-top: 10px;
                }
                .data-table th {
                    background: #1e3c72;
                    color: white;
                    padding: 6px 3px;
                    text-align: center;
                    font-weight: bold;
                }
                .data-table td {
                    padding: 4px 3px;
                    border: 1px solid #dee2e6;
                    text-align: center;
                }
                .data-table tr:nth-child(even) {
                    background: #f9f9f9;
                }
                
                /* STATUS BADGE */
                .status-badge {
                    display: inline-block;
                    padding: 3px 6px;
                    border-radius: 3px;
                    font-size: 8px;
                    font-weight: bold;
                }
                .badge-good { background: #d4edda; color: #155724; }
                .badge-warning { background: #fff3cd; color: #856404; }
                .badge-bad { background: #f8d7da; color: #721c24; }
                
                /* PELAKSANA */
                .pelaksana-box {
                    background: #f8f9fa;
                    border: 1px solid #dee2e6;
                    border-radius: 5px;
                    padding: 12px;
                }
                .pelaksana-item {
                    margin-bottom: 8px;
                }
                .pelaksana-label {
                    font-size: 9px;
                    color: #666;
                    margin-bottom: 3px;
                }
                .pelaksana-value {
                    font-size: 10px;
                    color: #333;
                }
                .signature-area {
                    margin-top: 15px;
                    padding-top: 10px;
                    border-top: 1px solid #ddd;
                    text-align: center;
                }
                .signature-line {
                    border-top: 1px solid #333;
                    width: 70%;
                    margin: 15px auto 5px auto;
                }
                .signature-name {
                    font-size: 10px;
                    font-weight: bold;
                    color: #333;
                }
                
                /* REKAP */
                .rekap-grid {
                    display: grid;
                    grid-template-columns: repeat(3, 1fr);
                    gap: 8px;
                    margin-bottom: 15px;
                }
                .rekap-item {
                    text-align: center;
                    padding: 8px;
                    border-radius: 5px;
                }
                
                /* INFO BOX */
                .info-box {
                    font-size: 9px;
                    color: #555;
                    line-height: 1.3;
                }
                
                /* FOOTER */
                .footer {
                    margin-top: 15px;
                    padding-top: 10px;
                    border-top: 1px solid #ddd;
                    text-align: center;
                    font-size: 9px;
                    color: #666;
                }
            </style>
        </head>
        <body>
        
            <!-- HEADER -->
            <div class="header">
                <div class="header-title">LAPORAN STATISTIK PENGUKURAN ERROR METER KWH</div>
                <div class="header-subtitle">REKAP SEMUA DATA - PT PLN (PERSERO)</div>
            </div>
            
            <!-- LAYOUT 2 KOLOM -->
            <div class="page-container">
            
                <!-- KOLOM KIRI -->
                <div class="left-column">
                
                    <!-- STATISTIK UTAMA -->
                    <div class="section">
                        <div class="section-title">STATISTIK UTAMA</div>
                        <div class="stats-grid">
                            <div class="stat-item">
                                <div class="stat-label">TOTAL DATA</div>
                                <div class="stat-value">' . $statistik['total'] . '</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-label">ERROR RATA-RATA</div>
                                <div class="stat-value" style="color: ' . ($statistik['avg_error'] <= 2 ? '#198754' : ($statistik['avg_error'] <= 5 ? '#ffc107' : '#dc3545')) . ';">
                                    ' . number_format($statistik['avg_error'], 2) . '%
                                </div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-label">MODE KEDIPAN</div>
                                <div class="stat-value">' . $statistik['mode1'] . '</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-label">MODE 3 PHASE</div>
                                <div class="stat-value">' . $statistik['mode2'] . '</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- DISTRIBUSI STATUS -->
                    <div class="section">
                        <div class="section-title">DISTRIBUSI STATUS</div>
                        <div class="chart-row">
                            <div class="chart-label" style="color: #198754;">BAIK</div>
                            <div class="chart-bar">
                                <div class="chart-fill" style="width: ' . $statistik['percent_good'] . '%; background: #198754;"></div>
                            </div>
                            <div class="chart-percent" style="color: #198754;">' . number_format($statistik['percent_good'], 1) . '%</div>
                        </div>
                        <div class="chart-row">
                            <div class="chart-label" style="color: #ffc107;">DI LUAR KELAS</div>
                            <div class="chart-bar">
                                <div class="chart-fill" style="width: ' . $statistik['percent_warning'] . '%; background: #ffc107;"></div>
                            </div>
                            <div class="chart-percent" style="color: #ffc107;">' . number_format($statistik['percent_warning'], 1) . '%</div>
                        </div>
                        <div class="chart-row">
                            <div class="chart-label" style="color: #dc3545;">DILUAR KELAS METER</div>
                            <div class="chart-bar">
                                <div class="chart-fill" style="width: ' . $statistik['percent_bad'] . '%; background: #dc3545;"></div>
                            </div>
                            <div class="chart-percent" style="color: #dc3545;">' . number_format($statistik['percent_bad'], 1) . '%</div>
                        </div>
                        
                        <div class="info-box" style="margin-top: 10px;">
                            <strong>Total Data:</strong> ' . $statistik['total'] . '<br>
                            <strong>BAIK:</strong> ' . $statistik['good'] . ' data<br>
                            <strong>DI LUAR KELAS:</strong> ' . $statistik['warning'] . ' data<br>
                            <strong>BURUK:</strong> ' . $statistik['bad'] . ' data
                        </div>
                    </div>
                    
                    <!-- DATA TERBARU -->
                    <div class="section">
                        <div class="section-title">5 DATA TERBARU</div>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th width="15%">ID</th>
                                    <th width="25%">Waktu</th>
                                    <th width="25%">Pelanggan</th>
                                    <th width="20%">Error</th>
                                    <th width="15%">Status</th>
                                </tr>
                            </thead>
                            <tbody>';
        
        foreach ($recentData as $row) {
            $error = $row['error_percent'] ?? 0;
            $absError = abs($error);
            $classMeter = $row['class_meter'] ?? 1.0;
            
            $tolerance = 0.01;
            
            if ($absError > 5) {
                $status = 'BURUK';
                $statusClass = 'badge-bad';
            } elseif (abs($absError - $classMeter) > $tolerance) {
                $status = 'DI LUAR KELAS';
                $statusClass = 'badge-warning';
            } else {
                $status = 'BAIK';
                $statusClass = 'badge-good';
            }
            
            $namaPelanggan = isset($row['nama_pelanggan']) ? substr($row['nama_pelanggan'], 0, 12) . (strlen($row['nama_pelanggan']) > 12 ? '...' : '') : '-';
            
            $html .= '
                                <tr>
                                    <td>#' . $row['id'] . '</td>
                                    <td>' . date('d/m H:i', strtotime($row['created_at'])) . '</td>
                                    <td style="text-align: left;">' . htmlspecialchars($namaPelanggan) . '</td>
                                    <td style="font-weight: bold; color: ' . ($absError > 5 ? '#dc3545' : ($absError != $classMeter ? '#ffc107' : '#198754')) . ';">
                                        ' . number_format($error, 1) . '%
                                    </td>
                                    <td><span class="status-badge ' . $statusClass . '">' . $status . '</span></td>
                                </tr>';
        }
        
        $html .= '
                            </tbody>
                        </table>
                    </div>
                    
                </div>
                
                <!-- KOLOM KANAN -->
                <div class="right-column">
                
                    <!-- REKAP STATISTIK -->
                    <div class="section">
                        <div class="section-title">REKAP STATISTIK</div>
                        <div class="rekap-grid">
                            <div class="rekap-item" style="background: #d4edda;">
                                <div style="font-size: 9px; color: #155724; font-weight: bold; margin-bottom: 3px;">BAIK</div>
                                <div style="font-size: 14px; font-weight: bold; color: #198754;">' . $statistik['good'] . '</div>
                                <div style="font-size: 8px; color: #666;">' . number_format($statistik['percent_good'], 1) . '%</div>
                            </div>
                            <div class="rekap-item" style="background: #fff3cd;">
                                <div style="font-size: 9px; color: #856404; font-weight: bold; margin-bottom: 3px;">DI LUAR KELAS</div>
                                <div style="font-size: 14px; font-weight: bold; color: #ffc107;">' . $statistik['warning'] . '</div>
                                <div style="font-size: 8px; color: #666;">' . number_format($statistik['percent_warning'], 1) . '%</div>
                            </div>
                            <div class="rekap-item" style="background: #f8d7da;">
                                <div style="font-size: 9px; color: #721c24; font-weight: bold; margin-bottom: 3px;">BURUK</div>
                                <div style="font-size: 14px; font-weight: bold; color: #dc3545;">' . $statistik['bad'] . '</div>
                                <div style="font-size: 8px; color: #666;">' . number_format($statistik['percent_bad'], 1) . '%</div>
                            </div>
                        </div>
                        
                        <div class="info-box" style="margin-top: 10px;">
                            <div style="margin-bottom: 5px;">
                                <strong>Periode Pengukuran:</strong><br>
                                ' . date('d/m/Y', strtotime(end($kwhData)['created_at'])) . ' - ' . date('d/m/Y', strtotime($kwhData[0]['created_at'])) . '
                            </div>
                            <div style="margin-bottom: 5px;">
                                <strong>Error Minimum:</strong> ' . number_format($statistik['min_error'], 2) . '%<br>
                                <strong>Error Maksimum:</strong> ' . number_format($statistik['max_error'], 2) . '%
                            </div>
                            <div>
                                <strong>Mode Terbanyak:</strong> ' . ($statistik['mode1'] > $statistik['mode2'] ? 'KEDIPAN' : '3 PHASE') . '
                            </div>
                        </div>
                    </div>
                    
                    <!-- REKOMENDASI -->
                    <div class="section">
                        <div class="section-title">REKOMENDASI</div>
                        <div class="info-box">
                            <div style="padding: 8px; background: #e7f3ff; border-radius: 5px; margin-bottom: 8px;">
                                <strong style="color: #0d6efd;">PENCAPAIAN:</strong><br>
                                • Akurasi rata-rata ' . number_format($statistik['avg_error'], 2) . '%<br>
                                • ' . number_format($statistik['percent_good'], 1) . '% data dalam kondisi BAIK
                            </div>
                            <div style="padding: 8px; background: #fff3cd; border-radius: 5px; margin-bottom: 8px;">
                                <strong style="color: #ffc107;">PERHATIAN:</strong><br>
                                • ' . $statistik['bad'] . ' data BURUK (error >5%)<br>
                                • ' . $statistik['warning'] . ' data DI LUAR KELAS METER
                            </div>
                            <div style="padding: 8px; background: #d4edda; border-radius: 5px;">
                                <strong style="color: #198754;">TINDAKAN:</strong><br>
                                1. Kalibrasi data BURUK<br>
                                2. Penyesuaian data DI LUAR KELAS<br>
                                3. Monitoring rutin semua meter
                            </div>
                        </div>
                    </div>
                    
                    <!-- PELAKSANA -->
                    <div class="section">
                        <div class="section-title">PELAKSANA</div>
                        <div class="pelaksana-box">
                            <div class="pelaksana-item">
                                <div class="pelaksana-label">Nama</div>
                                <div class="pelaksana-value">' . htmlspecialchars(session()->get('nama') ?? 'Operator') . '</div>
                            </div>
                            <div class="pelaksana-item">
                                <div class="pelaksana-label">Jabatan</div>
                                <div class="pelaksana-value">' . htmlspecialchars(session()->get('jabatan') ?? 'Pelaksana') . '</div>
                            </div>
                            <div class="pelaksana-item">
                                <div class="pelaksana-label">Tanggal Cetak</div>
                                <div class="pelaksana-value">' . $currentTime . '</div>
                            </div>
                            
                            <div class="signature-area">
                                <div class="signature-line"></div>
                                <div class="signature-name">' . htmlspecialchars(session()->get('nama') ?? 'Operator') . '</div>
                                <div style="font-size: 9px; color: #666;">Pelaksana / Supervisor</div>
                            </div>
                        </div>
                    </div>
                    
                </div>
                
            </div>
            
            <!-- FOOTER -->
            <div class="footer">
                <strong>LAPORAN STATISTIK - SISTEM KWH ERROR CALCULATOR PT PLN</strong><br>
                ' . $currentTime . ' | Total Data: ' . $totalData . ' | Halaman 1/1
            </div>
            
        </body>
        </html>';
        
        return $html;
    }

    private function renderPhotoTable($photos)
{
    $html = '';
    $photos = array_slice($photos, 0, 2);

    foreach ($photos as $p) {
        $path = WRITEPATH.'uploads/kwh/'.$p;
        if (file_exists($path)) {
            $img = base64_encode(file_get_contents($path));
            $html .= '
            <td width="50%">
                <div class="photo-box">
                    <img src="data:image/jpeg;base64,'.$img.'">
                </div>
            </td>';
        }
    }
    return $html;
}

    
    private function hitungStatistikKomprehensif($kwhData)
    {
        $total = count($kwhData);
        $mode1 = 0;
        $mode2 = 0;
        $good = 0;
        $warning = 0;
        $bad = 0;
        $totalError = 0;
        $maxError = 0;
        $minError = 100;
        
        foreach ($kwhData as $row) {
            if (($row['calculation_mode'] ?? 'mode1') === 'mode2') {
                $mode2++;
            } else {
                $mode1++;
            }
            
            $error = abs($row['error_percent'] ?? 0);
            $classMeter = $row['class_meter'] ?? 1.0;
            
            $tolerance = 0.01;
            
            if ($error > 5) {
                $bad++;
            } elseif (abs($error - $classMeter) > $tolerance) {
                $warning++;
            } else {
                $good++;
            }
            
            $totalError += $error;
            if ($error > $maxError) $maxError = $error;
            if ($error < $minError) $minError = $error;
        }
        
        $avgError = $total > 0 ? $totalError / $total : 0;
        
        return [
            'total' => $total,
            'mode1' => $mode1,
            'mode2' => $mode2,
            'good' => $good,
            'warning' => $warning,
            'bad' => $bad,
            'percent_good' => $total > 0 ? ($good / $total) * 100 : 0,
            'percent_warning' => $total > 0 ? ($warning / $total) * 100 : 0,
            'percent_bad' => $total > 0 ? ($bad / $total) * 100 : 0,
            'avg_error' => $avgError,
            'max_error' => $maxError,
            'min_error' => $minError
        ];
    }
    
    private function generatePdf($html, $filename, $orientation = 'landscape')
    {
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'Arial');
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);
        
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', $orientation);
        $dompdf->render();
        
        $dompdf->stream($filename, ['Attachment' => true]);
        exit;
    }
}