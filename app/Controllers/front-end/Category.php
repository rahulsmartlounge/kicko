<?php 
namespace App\Controllers;

use App\Models\CategoryModel;
use CodeIgniter\Controller;
use App\Models\ReviewModel;
use App\Models\ProductDisplayModel;

class Category extends Controller
{
    protected $CategoryModel;
    protected $session;
    protected $request;
    protected $productdisplayModel;
    protected $categories;

    public function __construct()
    {
        $this->session = \Config\Services::session();
        $this->request = \Config\Services::request();
        $this->categoryModel = new CategoryModel();
    }

   
   public function index()
{
    $data = [];  
   
    return view('common/header', $data)
        . view('category_list', $data)
        . view('common/footer')
        . view('pagescripts/category_listjs');
}
public function category_list()
{
    $category_id = 1; // or fetch dynamically if you have logic
    $perPage = 12;

    $productModel = new ProductDisplayModel();

    $data['categories'] = $productModel->getAllCategoriesAndSub();
    $data['category'] = $this->categoryModel->getAllCategory();
    $data['category_id'] = $category_id;

    // Load first batch of products
    $data['product'] = $productModel->getPaginatedProductsByCategory($category_id, $perPage, 0);

    return view('common/header', $data)
        . view('category_list', $data)
        . view('common/footer')
        . view('pagescripts/category_listjs');
}

public function catProducts($id = null)
{
    $this->productdisplayModel = new ProductDisplayModel();
    $this->categories = $this->productdisplayModel->getAllCategoriesAndSub();
    $reviewModel = new ReviewModel();
    $this->pager = \Config\Services::pager();

    $data['categories'] = $this->categories;
    $data['cat_id'] = $id;

    $category = $this->categoryModel->where('cat_Id', $id)->first();
    if ($category) {
        $data['cat_Name'] = $category['cat_Name'];
    }

    // Pagination setup
    // $perPage = 12;
    // $page = (int) $this->request->getGet('page') ?? 1;
    // $offset = ($page - 1) * $perPage;

$page = (int) ($this->request->getGet('page') ?? 1);
$page = max($page, 1); // ensures $page is always at least 1
$perPage = 12;
$offset = ($page - 1) * $perPage;


    // Get paginated products
    $products = $this->categoryModel->getPaginatedProductsByCategory($id, $perPage, $offset);
    $total = $this->categoryModel->getProductCountByCategory($id);

    // Ratings logic
    if (!empty($products)) {
        $productIds = array_column($products, 'pr_Id');
        $avgRatings = $reviewModel->getAverageRatingForProducts($productIds);

        $ratingsMap = [];
        foreach ($avgRatings as $rating) {
            $ratingsMap[$rating['pr_Id']] = round($rating['avg_rating'], 1);
        }

        foreach ($products as &$product) {
            $product['avg_rating'] = $ratingsMap[$product['pr_Id']] ?? 0;
        }
    }

  $data['product'] = $products;
$data['pager'] = $this->pager->makeLinks($page, $perPage, $total);
$data['subcategory'] = $this->categoryModel->getAllSubcategoryUnderCategory($id);

if ($this->request->isAJAX()) {
    return view('product/_cat_product_items', ['product' => $products]).
     view('pagescripts/category_listjs');
}

return view('common/header', $data)
    . view('cat_products', $data)
    . view('common/footer')
    . view('pagescripts/category_listjs');
}

public function loadMoreByDate()
{
    $page = (int) $this->request->getGet('page');
    $page = ($page >= 1) ? $page : 1;

    $limit = 12;
    $offset = ($page - 1) * $limit;

    $products = $this->CategoryModel->getProductsByModifiedDatePaginated($limit, $offset);

    if (!empty($products)) {
        return view('product/_cat_product_items', [
            'product' => $products,
            'currentBatch' => $page
        ]).view('pagescripts/category_listjs');
    } else {
        return ''; // No more products
    }
}

// Load search results with pagination
public function loadMoreSearch()
{
    $page = $this->request->getGet('page');
    $cat_id = $this->request->getGet('id');
    $perPage = 12;
    $offset = ($page - 1) * $perPage;

    $model = new CategoryModel();
    $products = $model->getPaginatedProductsByCategory($cat_id, $perPage, $offset); // Fix function name

    if (empty($products)) {
        return '';
    }

    return view('product/_cat_product_items', ['product' => $products]).
     view('pagescripts/category_listjs');
}



    




  
}
