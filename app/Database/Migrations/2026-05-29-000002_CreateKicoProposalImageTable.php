<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateKicoProposalImageTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'proposal_id' => ['type' => 'INT', 'unsigned' => true],
            'file_name'   => ['type' => 'VARCHAR', 'constraint' => 255],
            'file_path'   => ['type' => 'VARCHAR', 'constraint' => 500],
            'url'         => ['type' => 'VARCHAR', 'constraint' => 500],
            'is_360'      => ['type' => 'TINYINT', 'default' => 0, 'comment' => '1=360 image'],
            'sort_order'  => ['type' => 'INT', 'default' => 0],
            'status'      => ['type' => 'TINYINT', 'default' => 1],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('proposal_id');
        $this->forge->addForeignKey('proposal_id', 'kico_proposal', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('kico_proposal_image', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('kico_proposal_image', true);
    }
}
