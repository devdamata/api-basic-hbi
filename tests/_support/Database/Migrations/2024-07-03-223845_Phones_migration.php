<?php

namespace Tests\Support\Database\Migrations;

use CodeIgniter\Database\Migration;

class Phones extends Migration
{
    protected $DBGroup = 'tests';

    public function up(): void
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

    public function down(): void
    {
        $this->forge->dropTable('phones', true);
    }
}
