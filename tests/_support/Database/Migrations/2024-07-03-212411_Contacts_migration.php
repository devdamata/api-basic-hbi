<?php

namespace Tests\Support\Database\Migrations;

use CodeIgniter\Database\Migration;

class Contacts extends Migration
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
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => false,
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

        $this->forge->createTable('contacts', true, ['engine' => 'InnoDB']);
    }

    public function down(): void
    {
        $this->forge->dropTable('contacts', true);
    }
}
