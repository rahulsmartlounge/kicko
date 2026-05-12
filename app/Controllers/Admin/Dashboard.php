<?php
namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Models\Admin\DashboardModel;


class Dashboard extends BaseController
{

	public function __construct()
	{
		$this->session = \Config\Services::session();
		$this->input = \Config\Services::request();
		$this->dashboardModel = new \App\Models\Admin\DashboardModel();

	}
	public function index()
	{
		if (!$this->session->get('ad_uid')) {
				return redirect()->to(base_url('admin'));
			}

		if (!$this->session->get('ad_uid')) {
			redirect()->to(base_url());
		}



		$latestOrderCount = $this->dashboardModel->getLatestOrderCount();
		$totalOrderCount = $this->dashboardModel->getTotalOrderCount();
		$totalCustomerCount = $this->dashboardModel->getTotalCustomerCount();
		$annualRevenue = $this->dashboardModel->getAnnualRevenue();

		$todaysOrders = $this->dashboardModel->getTodaysOrders();
		$latestProducts = $this->dashboardModel->getLatestProducts();
		// Decode images for each product
		foreach ($latestProducts as &$product) {
			$images = json_decode($product->product_images, true);
			if (!empty($images[0]['name'][0])) {
				$product->main_image = $images[0]['name'][0];
			} else {
				$product->main_image = null;
			}
		}




		$template = view('Admin/common/header');
		$template .= view('Admin/common/leftmenu');
		$template .= view('Admin/dashboard', [
			'latestOrderCount' => $latestOrderCount,
			'totalOrderCount' => $totalOrderCount,
			'totalCustomerCount' => $totalCustomerCount,
			'annualRevenue' => $annualRevenue,
			'todaysOrders' => $todaysOrders,
			'latestProducts' => $latestProducts
		]);
		$template .= view('Admin/common/footer');
		return $template;

	}



}