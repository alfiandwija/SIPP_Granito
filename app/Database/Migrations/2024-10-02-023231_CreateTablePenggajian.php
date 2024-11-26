<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTablePenggajian extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'no_penggajian' => [
                'type'  => 'INT',
                'constraint'    => 11,
                'auto_increment'    => true
            ],
            'tgl'  => [
                'type'  => 'datetime',
            ],
            'gaji_pokok'  => [
                'type'  => 'INT',
            ],
            'gaji'  => [
                'type'  => 'INT',
            ],
            'hari_kerja'  => [
                'type'  => 'INT',
            ],
            'masuk'  => [
                'type'  => 'INT',
            ],
            'alpa'  => [
                'type'  => 'INT',
            ],
            'id_user'  => [
                'type'  => 'INT',
            ],
            'terlambat'  => [
                'type'  => 'INT',
            ],
            'sakit'  => [
                'type'  => 'INT',
            ],
            'status' => [
                'type' => 'int'
            ]
        ]);
        $this->forge->addKey('no_penggajian', true);
        $this->forge->addForeignKey('id_user', 'user', 'id_user');
        $this->forge->createTable('penggajian', true);
    }

    public function down()
    {
        $this->forge->dropTable('penggajian', true);
    }
}
