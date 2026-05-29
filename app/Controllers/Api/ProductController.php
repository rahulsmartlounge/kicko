<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class ProductController extends BaseController
{
    // POST /api/products
    // Body: { "page": 1, "limit": 20, "search": "", "categoryId": null, "subId": null }
    public function index(): ResponseInterface
    {
        try {
            $body  = (array)($this->request->getJSON() ?? []);
            $page  = max(1, (int)($body['page']  ?? 1));
            $limit = min(100, max(1, (int)($body['limit'] ?? 20)));
            $offset = ($page - 1) * $limit;

            $search     = trim((string)($body['search']     ?? ''));
            $categoryId = isset($body['categoryId']) && is_numeric($body['categoryId']) ? (int)$body['categoryId'] : null;
            $subId      = isset($body['subId'])      && is_numeric($body['subId'])      ? (int)$body['subId']      : null;

            $db = \Config\Database::connect();

            $base = $db->table('product as p')
                       ->select('p.pr_Id, p.pr_Name, p.pr_Code, p.pr_Description,
                                 p.mrp, p.pr_Selling_Price,
                                 p.pr_Discount_Type, p.pr_Discount_Value,
                                 p.pr_Stock, p.product_images,
                                 p.cat_Id, p.sub_Id, p.pr_Status,
                                 c.cat_Name, s.sub_Category_Name')
                       ->join('category c',    'c.cat_Id = p.cat_Id', 'left')
                       ->join('subcategory s', 's.sub_Id = p.sub_Id', 'left')
                       ->where('p.pr_Status !=', 3); // 3 = deleted

            if ($search !== '') {
                $base->groupStart()
                     ->like('p.pr_Name', $search)
                     ->orLike('p.pr_Code', $search)
                     ->orLike('p.pr_Description', $search)
                     ->orLike('c.cat_Name', $search)
                     ->groupEnd();
            }
            if ($categoryId !== null) {
                $base->where('p.cat_Id', $categoryId);
            }
            if ($subId !== null) {
                $base->where('p.sub_Id', $subId);
            }

            $total    = $base->countAllResults(false);
            $products = $base->limit($limit, $offset)->get()->getResultArray();

            // Decode product_images JSON for each row
            foreach ($products as &$p) {
                if (!empty($p['product_images'])) {
                    $decoded = json_decode($p['product_images'], true);
                    $p['product_images'] = is_array($decoded) ? $decoded : [];
                } else {
                    $p['product_images'] = [];
                }
            }
            unset($p);

            return $this->response->setStatusCode(200)->setJSON([
                'status' => 'success',
                'data'   => [
                    'total'      => $total,
                    'page'       => $page,
                    'limit'      => $limit,
                    'totalPages' => (int)ceil($total / $limit),
                    'products'   => $products,
                ],
            ]);

        } catch (\Throwable $e) {
            log_message('error', 'ProductController::index — ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            $isDev = in_array(ENVIRONMENT, ['development', 'testing'], true);
            return $this->response->setStatusCode(500)->setJSON([
                'status'  => 'error',
                'message' => $isDev ? $e->getMessage() : 'Failed to retrieve products',
            ]);
        }
    }

    // POST /api/products/details
    // Body: { "id": 5 }
    public function show(): ResponseInterface
    {
        try {
            $body = (array)($this->request->getJSON() ?? []);
            $id   = isset($body['id']) ? (int)$body['id'] : 0;

            if ($id <= 0) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status'  => 'error',
                    'message' => 'Invalid or missing product ID',
                ]);
            }

            $db      = \Config\Database::connect();
            $product = $db->table('product as p')
                          ->select('p.*, c.cat_Name, s.sub_Category_Name')
                          ->join('category c',    'c.cat_Id = p.cat_Id', 'left')
                          ->join('subcategory s', 's.sub_Id = p.sub_Id', 'left')
                          ->where('p.pr_Id', $id)
                          ->where('p.pr_Status !=', 3)
                          ->get()->getRowArray();

            if (!$product) {
                return $this->response->setStatusCode(404)->setJSON([
                    'status'  => 'error',
                    'message' => "Product #{$id} not found",
                ]);
            }

            if (!empty($product['product_images'])) {
                $decoded = json_decode($product['product_images'], true);
                $product['product_images'] = is_array($decoded) ? $decoded : [];
            } else {
                $product['product_images'] = [];
            }

            return $this->response->setStatusCode(200)->setJSON([
                'status' => 'success',
                'data'   => $product,
            ]);

        } catch (\Throwable $e) {
            log_message('error', 'ProductController::show — ' . $e->getMessage());
            $isDev = in_array(ENVIRONMENT, ['development', 'testing'], true);
            return $this->response->setStatusCode(500)->setJSON([
                'status'  => 'error',
                'message' => $isDev ? $e->getMessage() : 'Failed to retrieve product',
            ]);
        }
    }
}
