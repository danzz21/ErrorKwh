<?php

namespace App\Models;

use CodeIgniter\Model;

class KwhModel extends Model
{
    protected $table = 'kwh_data';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'keterangan', 'arus', 'tegangan', 'cosphi', 'constanta',
        'count', 'duration', 'blink_data', 'selected_blink','nama_pelanggan','id_pelanggan',
        'class_meter', 'p1_kw', 'p2_kw', 'error_percent','jabatan',
        'status_final', 'photos', 'user_id', 'created_at',
        'calculation_mode', 'pr_value', 'ps_value', 'pt_value'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
}