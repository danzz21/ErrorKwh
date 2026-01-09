<?php
namespace App\Controllers;

use App\Models\KwhModel;

class TestDb extends BaseController
{
    public function index()
    {
        $model = new KwhModel();
        
        // Test insert
        $data = [
            'count' => 10,
            'duration_seconds' => 5.0,
            'blink_per_second' => 2.0,
            'keterangan' => 'Test Data',
            'is_auto' => 1,
            'idle_timeout' => 15
        ];
        
        if ($model->save($data)) {
            echo "Database OK! Insert ID: " . $model->getInsertID();
        } else {
            echo "Database Error: " . print_r($model->errors(), true);
        }
    }
}