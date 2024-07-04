<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Phones extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_contact' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'phone' => [
                'type'       => 'VARCHAR',
                'constraint' => 40,
                'null'       => false,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ]
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('id_contact', 'contacts', 'id');

        $this->forge->createTable('phones', true, ['engine' => 'InnoDB']);
    }

    public function down()
    {
        $this->forge->dropTable('phone', true);
    }
}
