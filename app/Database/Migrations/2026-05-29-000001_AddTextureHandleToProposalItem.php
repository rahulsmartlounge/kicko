<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTextureHandleToProposalItem extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('kico_proposal_item', [
            'texture_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'default'    => null,
                'after'      => 'unit',
            ],
            'handle_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'default'    => null,
                'after'      => 'texture_code',
            ],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('kico_proposal_item', ['texture_code', 'handle_type']);
    }
}
