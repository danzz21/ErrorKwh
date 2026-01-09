<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCalcFieldsToKwhLogs extends Migration
{
    public function up()
    {
        // Hapus dulu table jika ada (untuk fresh install)
        $this->forge->dropTable('kwh_logs', true);
        
        // Buat table baru dengan field lengkap
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'count' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'duration_seconds' => ['type' => 'FLOAT', 'null' => true],
            'blink_per_second' => ['type' => 'FLOAT', 'null' => true],
            
            // Input Manual User
            'arus' => ['type' => 'FLOAT', 'null' => true], // Ampere
            'tegangan' => ['type' => 'FLOAT', 'default' => 220], // Volt
            'cosphi' => ['type' => 'FLOAT', 'default' => 0.85], // Power Factor
            'constanta' => ['type' => 'FLOAT', 'null' => true], // Constanta meter
            
            // Hasil Perhitungan
            'p1_kw' => ['type' => 'FLOAT', 'null' => true], // P1 = (3600 * blink_per_second) / constanta
            'p2_kw' => ['type' => 'FLOAT', 'null' => true], // P2 = (V * I * cosphi) / 1000
            'error_percent' => ['type' => 'FLOAT', 'null' => true], // ((P1 - P2) / P2) * 100
            
            // Data Kedipan Detil
            'blink_data' => ['type' => 'TEXT', 'null' => true], // JSON: [{time: 1.5, count: 1}, ...]
            'selected_blink' => ['type' => 'INT', 'null' => true], // Kedipan keberapa yang dipilih
            
            'keterangan' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'is_auto' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'idle_timeout' => ['type' => 'INT', 'constraint' => 5, 'default' => 15],
            
            'created_at' => ['type' => 'DATETIME'],
            'updated_at' => ['type' => 'DATETIME', 'null' => true]
        ]);
        
        $this->forge->addKey('id', true);
        $this->forge->createTable('kwh_logs');
    }

    public function down()
    {
        $this->forge->dropTable('kwh_logs');
    }
}