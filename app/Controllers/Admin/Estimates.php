<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ProposalModel;
use App\Models\ProposalItemModel;

class Estimates extends BaseController
{
    private ProposalModel $model;

    public function __construct()
    {
        $this->session = \Config\Services::session();
        $this->model   = new ProposalModel();
    }

    private function authCheck(): bool
    {
        return (bool)$this->session->get('ad_uid');
    }

    // GET admin/estimates
    public function index()
    {
        if (!$this->authCheck()) {
            return redirect()->to(base_url('admin'));
        }

        return view('Admin/common/header')
             . view('Admin/common/leftmenu')
             . view('Admin/estimates')
             . view('Admin/common/footer')
             . view('Admin/page_scripts/estimatesjs');
    }

    // POST admin/estimates/list  — DataTables server-side
    public function ajaxList()
    {
        if (!$this->authCheck()) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        }

        $colMap = [
            1 => 'name',
            2 => 'phone',
            3 => 'email',
            4 => 'project_number',
            5 => 'date_of_proposal',
            6 => 'grand_total',
            7 => 'created_at',
        ];

        $orderColIdx = (int)($this->request->getPost('order')[0]['column'] ?? 7);
        $orderDir    = $this->request->getPost('order')[0]['dir'] ?? 'desc';
        $orderBy     = $colMap[$orderColIdx] ?? 'created_at';
        $start       = (int)($this->request->getPost('start')  ?? 0);
        $length      = (int)($this->request->getPost('length') ?? 10);
        $search      = trim((string)($this->request->getPost('search')['value'] ?? ''));

        $result = $this->model->getDatatables($search, $start, $length, $orderBy, $orderDir);

        $rows = [];
        foreach ($result['data'] as $row) {
            $rows[] = [
                'id'               => $row['id'],
                'name'             => $row['name']           ?? 'N/A',
                'phone'            => $row['phone']          ?? 'N/A',
                'email'            => $row['email']          ?? 'N/A',
                'project_number'   => $row['project_number'] ?? 'N/A',
                'date_of_proposal' => $row['date_of_proposal']
                                      ? date('d M Y', strtotime($row['date_of_proposal'])) : 'N/A',
                'grand_total'      => $row['grand_total'] !== null
                                      ? '₹ ' . number_format((float)$row['grand_total'], 2) : 'N/A',
                'paid_status'      => (int)($row['paid_status'] ?? 0),
                'pdf_url'          => $row['pdf_url'] ?? null,
                'created_at'       => $row['created_at']
                                      ? date('d M Y, h:i A', strtotime($row['created_at'])) : 'N/A',
            ];
        }

        return $this->response->setJSON([
            'draw'            => intval($this->request->getPost('draw')),
            'recordsTotal'    => $result['total'],
            'recordsFiltered' => $result['filtered'],
            'data'            => $rows,
        ]);
    }

    // POST admin/estimates/markPaid  — Body: { "id": 1 }
    public function markPaid()
    {
        if (!$this->authCheck()) {
            return $this->response->setStatusCode(401)->setJSON(['status' => false, 'message' => 'Unauthorized']);
        }

        $body = (array)($this->request->getJSON() ?? []);
        $id   = isset($body['id']) ? (int)$body['id'] : 0;

        if ($id <= 0) {
            return $this->response->setStatusCode(400)->setJSON(['status' => false, 'message' => 'Invalid ID']);
        }

        $proposal = $this->model->find($id);
        if (!$proposal) {
            return $this->response->setStatusCode(404)->setJSON(['status' => false, 'message' => "Estimate #{$id} not found"]);
        }

        if ((int)($proposal['paid_status'] ?? 0) === 1) {
            return $this->response->setStatusCode(200)->setJSON(['status' => true, 'message' => 'Already marked as paid']);
        }

        $ok = $this->model->markAsPaid($id);
        if (!$ok) {
            return $this->response->setStatusCode(500)->setJSON(['status' => false, 'message' => 'Failed to update paid status']);
        }

        return $this->response->setStatusCode(200)->setJSON(['status' => true, 'message' => 'Marked as paid successfully']);
    }

    // GET admin/estimates/view/{id}
    public function view(int $id)
    {
        if (!$this->authCheck()) {
            return redirect()->to(base_url('admin'));
        }

        if ($id <= 0) {
            return redirect()->to(base_url('admin/estimates'));
        }

        // AJAX detail fetch
        if ($this->request->isAJAX()) {
            try {
                $proposal = $this->model->find($id);
                if (!$proposal) {
                    return $this->response->setStatusCode(404)->setJSON([
                        'status' => false, 'message' => "Estimate #{$id} not found",
                    ]);
                }
                $items = (new ProposalItemModel())->getItemsByProposal($id);
                return $this->response->setJSON([
                    'status' => true,
                    'data'   => ['proposal' => $proposal, 'items' => $items],
                ]);
            } catch (\Throwable $e) {
                log_message('error', 'Admin\Estimates::view AJAX — ' . $e->getMessage());
                return $this->response->setStatusCode(500)->setJSON([
                    'status' => false, 'message' => 'Failed to load estimate',
                ]);
            }
        }

        // Page load
        $proposal = $this->model->find($id);
        if (!$proposal) {
            return redirect()->to(base_url('admin/estimates'));
        }

        return view('Admin/common/header')
             . view('Admin/common/leftmenu')
             . view('Admin/estimate_view', ['proposal_id' => $id])
             . view('Admin/common/footer')
             . view('Admin/page_scripts/estimate_viewjs', ['proposal_id' => $id]);
    }
}
