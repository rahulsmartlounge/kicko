<?php

namespace App\Controllers;

use App\Models\ProductDisplayModel;
use CodeIgniter\Controller;
use App\Models\ReviewModel;


class ProductDetails extends BaseController
{

    public function __construct()
    {
        $this->session = \Config\Services::session();
        $this->input = \Config\Services::request();
        $this->productModel = new \App\Models\ProductDisplayModel();
    }

   public function index()
{
    $zd_uid = $this->session->get('zd_uid');
    $data = [];

    // Load categories for header
    $data['categories'] = $this->productdisplayModel->getAllCategoriesAndSub();

    // Load all products
    $products = $this->productdisplayModel->getAllProducts();
    $reviewModel = new ReviewModel();

    if (!empty($products)) {
        $productIds = array_column($products, 'pr_Id');

        // Get avg ratings from reviews table
        $avgRatings = $reviewModel->getAverageRatingForProducts($productIds);

        // Create a map: pr_Id => avg_rating
        $ratingsMap = [];
        foreach ($avgRatings as $rating) {
            $ratingsMap[$rating['pr_Id']] = round($rating['avg_rating'], 1);
        }

        // Attach avg_rating to each product
        foreach ($products as &$product) {
            $product['avg_rating'] = $ratingsMap[$product['pr_Id']] ?? 0;
        }
    }
	
    $data['product'] = $products;
	print_r($data);exit;

    return view('common/header', $data)
        . view('products_list', $data)
        . view('common/footer')
        . view('pagescripts/productjs');
}

}