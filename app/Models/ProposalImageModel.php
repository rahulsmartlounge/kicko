<?php

namespace App\Models;

use CodeIgniter\Model;

class ProposalImageModel extends Model
{
    protected $table         = 'kico_proposal_image';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;

    protected $allowedFields = [
        'proposal_id', 'file_name', 'file_path', 'url',
        'is_360', 'sort_order', 'status', 'created_at',
    ];

    public function getImagesByProposal(int $proposalId): array
    {
        return $this->where('proposal_id', $proposalId)
                    ->where('status', 1)
                    ->orderBy('sort_order', 'ASC')
                    ->findAll();
    }

    public function get360UrlsByProposal(int $proposalId): array
    {
        $rows = $this->where('proposal_id', $proposalId)
                     ->where('is_360', 1)
                     ->where('status', 1)
                     ->orderBy('sort_order', 'ASC')
                     ->findAll();

        return array_column($rows, 'url');
    }
}
