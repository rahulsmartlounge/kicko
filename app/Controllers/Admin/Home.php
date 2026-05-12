<?php


namespace App\Controllers\admin;
use App\Controllers\BaseController;

class Home extends BaseController
{
    public function index(): string
    {
        return view('Admin/login');
       
    }

	
}
