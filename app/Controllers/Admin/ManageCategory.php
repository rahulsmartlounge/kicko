<?php
namespace App\Controllers;
use App\Models\CategoryModel;

class ManageCategory extends BaseController
{

    public function __construct()
    {
        $this->session = \Config\Services::session();
        $this->input = \Config\Services::request();
       // $this->CategoryModel = new CategoryModel();
    }

    public function index()
    {
        $template = view('common/header');
		$template.= view('common/leftmenu');
		$template.= view('category_add');
		$template.= view('common/footer');
        return $template;

        
    }

}