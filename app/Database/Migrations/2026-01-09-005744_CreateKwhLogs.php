<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateKwhLogs extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'count' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0
            ],
            'duration_seconds' => [
                'type' => 'FLOAT',
                'null' => true
            ],
            'blink_per_second' => [
                'type' => 'FLOAT',
                'null' => true
            ],
            'keterangan' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true
            ],
            'is_auto' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1
            ],
            'idle_timeout' => [
                'type' => 'INT',
                'constraint' => 5,
                'default' => 15
            ],
            'created_at' => [
                'type' => 'DATETIME'
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true
            ]
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('kwh_logs');
    }

    public function down()
    {
        $this->forge->dropTable('kwh_logs');
    }
}