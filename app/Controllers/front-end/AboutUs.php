<?php
namespace App\Controllers;
use App\Controllers\BaseController;

use App\Models\ProductDisplayModel;

class AboutUs extends BaseController
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
        $data['title'] = 'AboutUs';

        $data['product'] = $this->productdisplayModel->getAllProducts();
        $template = view('common/header',$data);
		$template.= view('aboutus');
        $template.= view('common/footer');
        return $template;

        
    }
}