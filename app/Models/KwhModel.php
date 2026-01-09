<?php

namespace App\Models;

use CodeIgniter\Model;

class KwhModel extends Model
{
    protected $table = 'kwh_data';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    
    protected $allowedFields = [
        'keterangan', 'arus', 'tegangan', 'cosphi', 'constanta',
        'count', 'duration', 'blink_data', 'selected_blink',
        'p1_kw', 'p2_kw', 'error_percent', 'class_meter', 'status_final',
        'photos', 'is_auto', 'idle_timeout', 'created_at'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
}