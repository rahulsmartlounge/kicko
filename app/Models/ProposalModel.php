<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Models\ProposalItemModel;

class ProposalModel extends Model
{
    protected $table         = 'kico_proposal';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;

    protected $allowedFields = [
        'name', 'address', 'phone', 'email',
        'project_number', 'date_of_proposal',
        'sales_manager', 'service_support', 'referred_by',
        'location', 'latitude', 'longitude',
        'sub_total_label', 'sub_total', 'gst_percent',
        'discount', 'grand_total', 'grand_total_words',
        'pdf_file_name', 'pdf_url',
        'status', 'created_at', 'created_by', 'updated_at', 'updated_by',
    ];

    public function getAllProposals(int $limit = 50, int $offset = 0, array $filters = []): array
    {
        $this->where('status', 1);
        $this->applyFilters($filters);
        return $this->orderBy('created_at', 'DESC')->findAll($limit, $offset);
    }

    public function countFiltered(array $filters = []): int
    {
        $this->where('status', 1);
        $this->applyFilters($filters);
        return $this->countAllResults();
    }

    private function applyFilters(array $filters): void
    {
        if (!empty($filters['name'])) {
            $this->like('name', $filters['name']);
        }
        if (!empty($filters['phone'])) {
            $this->like('phone', $filters['phone']);
        }
        if (!empty($filters['email'])) {
            $this->like('email', $filters['email']);
        }
        if (!empty($filters['project_number'])) {
            $this->like('project_number', $filters['project_number']);
        }
    }

    public function getProposalWithItems(int $id): ?array
    {
        $proposal = $this->find($id);
        if (!$proposal) return null;

        $proposal['items'] = (new ProposalItemModel())->getItemsByProposal($id);
        return $proposal;
    }
}
