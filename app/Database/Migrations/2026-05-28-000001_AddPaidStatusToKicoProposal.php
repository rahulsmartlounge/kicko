<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPaidStatusToKicoProposal extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('kico_proposal', [
            'paid_status' => [
                'type'    => 'TINYINT',
                'default' => 0,
                'after'   => 'status',
                'comment' => '0=unpaid, 1=paid',
            ],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('kico_proposal', 'paid_status');
    }
}
