<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ProductDisplayModel;
class ReturnAndRefundPolicy extends BaseController
{
    protected $productdisplayModel;
    protected $categories;
    public function __construct()
    {
        $this->session = \Config\Services::session();
        $this->request = \Config\Services::request();
    }

    public function index()
    {
        $this->productdisplayModel = new ProductDisplayModel();
        $this->categories = $this->productdisplayModel->getAllCategoriesAndSub();
		$data['categories'] = $this->categories;
        $data['product'] = $this->productdisplayModel->getAllProducts();
        
        $template  = view('common/header',$data);
        $template .= view('returnpolicy');
        $template .= view('common/footer');

        return $template;
    }
}
