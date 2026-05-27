<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateKicoProposalItemTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'proposal_id' => ['type' => 'INT', 'unsigned' => true],
            'is_category' => ['type' => 'TINYINT', 'default' => 0, 'comment' => '1=section header, 0=normal row'],
            'sl_no'       => ['type' => 'VARCHAR', 'constraint' => 20,  'null' => true],
            'description' => ['type' => 'TEXT',   'null' => true],
            'qty'         => ['type' => 'DECIMAL', 'constraint' => '10,2', 'null' => true],
            'unit'        => ['type' => 'VARCHAR', 'constraint' => 50,  'null' => true],
            'rate'        => ['type' => 'DECIMAL', 'constraint' => '14,2', 'null' => true],
            'amount'      => ['type' => 'DECIMAL', 'constraint' => '14,2', 'null' => true],
            'amount_text' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'sort_order'  => ['type' => 'INT', 'default' => 0],
            'status'      => ['type' => 'TINYINT', 'default' => 1],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('proposal_id');
        $this->forge->addForeignKey('proposal_id', 'kico_proposal', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('kico_proposal_item', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('kico_proposal_item', true);
    }
}
