<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\ProposalModel;
use CodeIgniter\HTTP\ResponseInterface;

class OrderController extends BaseController
{
    private ProposalModel $model;

    public function __construct()
    {
        $this->model = new ProposalModel();
    }

    // POST /api/orders
    // Body: { "page": 1, "limit": 20, "name": "", "phone": "", "email": "", "projectNumber": "" }
    public function index(): ResponseInterface
    {
        try {
            $body = (array)($this->request->getJSON() ?? []);

            $page  = max(1, (int)($body['page']  ?? 1));
            $limit = min(100, max(1, (int)($body['limit'] ?? 20)));
            $offset = ($page - 1) * $limit;

            $filters = [];
            foreach (['name', 'phone', 'email'] as $field) {
                $val = $body[$field] ?? null;
                if ($val !== null && trim((string)$val) !== '') {
                    $filters[$field] = trim((string)$val);
                }
            }
            $pn = $body['projectNumber'] ?? null;
            if ($pn !== null && trim((string)$pn) !== '') {
                $filters['project_number'] = trim((string)$pn);
            }

            $total  = $this->model->countFiltered($filters);
            $orders = $this->model->getAllProposals($limit, $offset, $filters);

            return $this->response->setStatusCode(200)->setJSON([
                'status' => 'success',
                'data'   => [
                    'total'      => $total,
                    'page'       => $page,
                    'limit'      => $limit,
                    'totalPages' => (int)ceil($total / $limit),
                    'orders'     => $orders,
                ],
            ]);

        } catch (\Throwable $e) {
            log_message('error', 'OrderController::index — ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());

            $isDev = in_array(ENVIRONMENT, ['development', 'testing'], true);
            return $this->response->setStatusCode(500)->setJSON([
                'status'  => 'error',
                'message' => $isDev ? $e->getMessage() : 'Failed to retrieve orders',
            ]);
        }
    }

    // POST /api/orders/details
    // Body: { "id": 1 }
    public function show(): ResponseInterface
    {
        try {
            $body = (array)($this->request->getJSON() ?? []);
            $id   = isset($body['id']) ? (int)$body['id'] : 0;

            if ($id <= 0) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status'  => 'error',
                    'message' => 'Invalid or missing order ID',
                ]);
            }

            $proposal = $this->model->getProposalWithItems($id);

            if (!$proposal) {
                return $this->response->setStatusCode(404)->setJSON([
                    'status'  => 'error',
                    'message' => "Order #{$id} not found",
                ]);
            }

            return $this->response->setStatusCode(200)->setJSON([
                'status' => 'success',
                'data'   => $proposal,
            ]);

        } catch (\Throwable $e) {
            log_message('error', 'OrderController::show — ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());

            $isDev = in_array(ENVIRONMENT, ['development', 'testing'], true);
            return $this->response->setStatusCode(500)->setJSON([
                'status'  => 'error',
                'message' => $isDev ? $e->getMessage() : 'Failed to retrieve order',
            ]);
        }
    }
}
