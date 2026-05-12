<?php
namespace App\Controllers;
use App\Controllers\BaseController;
use App\Models\TermsandconditionsModel;
use App\Models\ProductDisplayModel;
class Termsandconditions extends BaseController
{
    protected $productdisplayModel;
    protected $categories;

    public function __construct()
    {
        $this->session = \Config\Services::session();
        $this->input = \Config\Services::request();
        
    }

    public function index()
    {

        $this->productdisplayModel = new ProductDisplayModel();
        $this->categories = $this->productdisplayModel->getAllCategoriesAndSub();
		$data['categories'] = $this->categories;

        $data['product'] = $this->productdisplayModel->getAllProducts();
        $template = view('common/header',$data);
		$template.= view('termsandconditions');
        $template.= view('common/footer');
       
        return $template;

        
    }
}