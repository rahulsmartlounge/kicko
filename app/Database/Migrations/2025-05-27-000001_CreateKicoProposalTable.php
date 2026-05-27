<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateKicoProposalTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'                => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'name'              => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'address'           => ['type' => 'TEXT',   'null' => true],
            'phone'             => ['type' => 'VARCHAR', 'constraint' => 30,  'null' => true],
            'email'             => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'project_number'    => ['type' => 'VARCHAR', 'constraint' => 80,  'null' => true],
            'date_of_proposal'  => ['type' => 'DATE',   'null' => true],
            'sales_manager'     => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'service_support'   => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'referred_by'       => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'location'          => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'latitude'          => ['type' => 'DECIMAL', 'constraint' => '10,7', 'null' => true],
            'longitude'         => ['type' => 'DECIMAL', 'constraint' => '10,7', 'null' => true],
            'sub_total_label'   => ['type' => 'VARCHAR', 'constraint' => 50,  'null' => true],
            'sub_total'         => ['type' => 'DECIMAL', 'constraint' => '14,2', 'null' => true, 'default' => 0.00],
            'gst_percent'       => ['type' => 'DECIMAL', 'constraint' => '5,2',  'null' => true, 'default' => 0.00],
            'discount'          => ['type' => 'DECIMAL', 'constraint' => '14,2', 'null' => true, 'default' => 0.00],
            'grand_total'       => ['type' => 'DECIMAL', 'constraint' => '14,2', 'null' => true, 'default' => 0.00],
            'grand_total_words' => ['type' => 'TEXT',   'null' => true],
            'pdf_file_name'     => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'pdf_url'           => ['type' => 'TEXT',   'null' => true],
            'status'            => ['type' => 'TINYINT', 'default' => 1],
            'created_at'        => ['type' => 'DATETIME', 'null' => true],
            'created_by'        => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'updated_at'        => ['type' => 'DATETIME', 'null' => true],
            'updated_by'        => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('kico_proposal', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('kico_proposal', true);
    }
}
