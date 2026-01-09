<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateKwhTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'keterangan' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'arus' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0,
            ],
            'tegangan' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 220,
            ],
            'cosphi' => [
                'type' => 'DECIMAL',
                'constraint' => '4,2',
                'default' => 0.85,
            ],
            'constanta' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 1600,
            ],
            'count' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'duration' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0,
            ],
            'blink_data' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'selected_blink' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 1,
            ],
            'p1_kw' => [
                'type' => 'DECIMAL',
                'constraint' => '10,4',
                'default' => 0,
            ],
            'p2_kw' => [
                'type' => 'DECIMAL',
                'constraint' => '10,4',
                'default' => 0,
            ],
            'error_percent' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0,
            ],
            'photos' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'is_auto' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'idle_timeout' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 15,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        
        $this->forge->addKey('id', true);
        $this->forge->createTable('kwh_data', true);
    }

    public function down()
    {
        $this->forge->dropTable('kwh_data', true);
    }
}