<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\Admin\Stock_Model;

class Stock extends BaseController
{
    protected $session;
    protected $stockModel;

    public function __construct()
    {
        $this->session = \Config\Services::session();
        $this->stockModel = new Stock_Model();
    }

    public function index()
    {
        if (!$this->session->get('ad_uid')) {
            return redirect()->to(base_url('/admin/'));
        }

        $allProducts = $this->stockModel->getAllProducts();
        $data['product'] = $allProducts;

        $template  = view('Admin/common/header');
        $template .= view('Admin/common/leftmenu');
        $template .= view('Admin/update_stock', $data);  // Display all products
        $template .= view('Admin/common/footer');
		

        return $template;
    }

    public function updateStockForm($pr_Id)
    {
        if (!$this->session->get('ad_uid')) {
            return redirect()->to(base_url('/admin/'));
        }

        $product = $this->stockModel->getProductByid($pr_Id);

        if (!$product) {
            return redirect()->to(base_url('admin/product'))->with('error', 'Product not found.');
        }

        $data['product'] = $product;

        $template  = view('Admin/common/header');
        $template .= view('Admin/common/leftmenu');
        $template .= view('Admin/update_stock', $data); // Form view
        $template .= view('Admin/common/footer');
		$template .= view('Admin/page_scripts/productjs.php');

        return $template;
    }

    public function updateStock($pr_Id)
    {
        if (!$this->session->get('ad_uid')) {
            return $this->response->setJSON([
                'status' => '0',
                'msg' => 'Unauthorized access.'
            ]);
        }

        $product = $this->stockModel->find($pr_Id);

        if (!$product) {
            return $this->response->setJSON([
                'status' => '0',
                'msg' => 'Product not found.'
            ]);
        }

        $data = [
            'pr_Stock' => $this->request->getPost('pr_Stock'),
            'pr_Reset_stock' => $this->request->getPost('pr_Reset_stock')
        ];

       if ($this->stockModel->updateProduct($pr_Id, $data)) {
    return $this->response->setJSON([
        'status' => '1',
        'msg'    => 'Stock Updated Successfully.',
        'pr_Id'  => $pr_Id // return this to use in JS
    ]);
} else {
    return $this->response->setJSON([
        'status' => '0',
        'msg'    => 'Failed to update stock.'
    ]);
}
    }
}
