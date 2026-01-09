<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPhotosToKwhLogs extends Migration
{
    public function up()
    {
        // Tambah kolom untuk foto
        $this->forge->addColumn('kwh_logs', [
            'photos' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'error_percent',
                'comment' => 'JSON array of photo filenames'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('kwh_logs', 'photos');
    }
}