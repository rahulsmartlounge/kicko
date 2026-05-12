<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ReturnpolicyModel;

class Returnandrefundpolicy extends BaseController
{
    protected $session;
    protected $request;
    protected $returnpolicyModel;

    public function __construct()
    {
        $this->session = \Config\Services::session();
        $this->request = \Config\Services::request();
        $this->returnpolicyModel = new ReturnpolicyModel();
    }

    public function index()
    {
        $data['returnpolicy'] = $this->returnpolicyModel->getAll();

        $template  = view('common/header');
        $template .= view('returnpolicy', $data);
        $template .= view('common/footer');

        return $template;
    }
}
