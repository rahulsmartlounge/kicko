<?php 
namespace App\Controllers;

use App\Models\SubcategoryModel;
use App\Models\ProductDisplayModel;
use App\Models\ReviewModel;
use CodeIgniter\Controller;

class Subcategory extends Controller
{
    protected $session;
    protected $request;
    protected $subcategoryModel;
    protected $productdisplayModel;

    public function __construct()
    {
        $this->session = \Config\Services::session();
        $this->request = \Config\Services::request();
        $this->subcategoryModel = new SubcategoryModel();
        $this->productdisplayModel = new ProductDisplayModel();
    }

    public function index()
    {
        return view('common/header')
            . view('subcategory_list') // can show category list page
            . view('common/footer')
            . view('pagescripts/subcategoryjs');
    }

    public function subcategoryProducts($subcatId, $catId)
    {
        $reviewModel = new ReviewModel();

        $data['cat_id'] = $catId;
        $data['subcat_id'] = $this->subcategoryModel->getAllSubcategory($subcatId);
        $data['categories'] = $this->productdisplayModel->getAllCategoriesAndSub();

        $limit = 12;
        $offset = 0;

        $products = $this->subcategoryModel->getProductsBySubcategoryPaginated($subcatId, $limit, $offset);

        $data['product'] = $this->attachRatings($products, $reviewModel);

        $data['similar'] = $this->subcategoryModel->getSimilarProducts($catId, $subcatId);
        $data['similar'] = $this->attachRatings($data['similar'], $reviewModel);

        return view('common/header', $data)
            . view('subcategory_list', $data)
            . view('common/footer')
            . view('pagescripts/subcategoryjs');
    }

    public function loadMoreSubcategoryProducts()
    {
        if ($this->request->isAJAX()) {
            $subcatId = $this->request->getGet('subcat_id');
            $page = (int) $this->request->getGet('page') ?? 1;
            $limit = 12;
            $offset = ($page - 1) * $limit;

            $reviewModel = new ReviewModel();
            $products = $this->subcategoryModel->getProductsBySubcategoryPaginated($subcatId, $limit, $offset);
            $productsWithRatings = $this->attachRatings($products, $reviewModel);

            return view('product/_subcategory_product_items', ['product' => $productsWithRatings]);
        }

        return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid request']);
    }

    private function attachRatings(array $products, ReviewModel $reviewModel)
    {
        if (empty($products)) return [];

        $productIds = array_column($products, 'pr_Id');
        $avgRatings = $reviewModel->getAverageRatingForProducts($productIds);

        $ratingsMap = [];
        foreach ($avgRatings as $rating) {
            $ratingsMap[$rating['pr_Id']] = round($rating['avg_rating'], 1);
        }

        foreach ($products as &$product) {
            $product['avg_rating'] = $ratingsMap[$product['pr_Id']] ?? 0;
        }

        return $products;
    }
}
