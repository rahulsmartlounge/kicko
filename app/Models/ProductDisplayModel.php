<?php
namespace App\Models;
use CodeIgniter\Model;

class ProductDisplayModel extends Model
{
    protected $table = 'product';
    protected $primaryKey = 'pr_Id';
    protected $allowedFields = [
        'pr_Name',
        'pr_Description',
        'pr_Selling_Price',
        'product_images',
        'cat_Id',
        'sub_Id',
        'pr_Price',
        'pr_modifyon',
        'pr_Stock',
        'pr_Status',
        'pr_Reset_Stock',
        'pr_Discount_Type',
        'pr_Discount_Value'
    ];
    public function getAllProducts($limit = null, $offset = null)
    {
        $builder = $this->db->table('product as pd');
        $builder->select('
        pd.pr_Id,
        pd.pr_Name,
        pd.pr_Selling_Price,
        pd.pr_Discount_Value,
        pd.mrp,
        pd.pr_Status,
        pd.product_images,
        pd.pr_modifyon,
        AVG(rw.rating) AS ratings
    ');
        $builder->join('reviews as rw', 'rw.pr_Id = pd.pr_Id', 'left');
        $builder->where('pd.pr_Status', 1);
        $builder->groupBy('pd.pr_Id');
        $builder->orderBy('pd.pr_modifyon', 'DESC');

        if ($limit !== null && $offset !== null) {
            $builder->limit($limit, $offset);
        }

        return $builder->get()->getResultArray();
    }



    ///// view collection ////
// 	public function getSimilarProducts($cat_Id, $excludeId){
//     return $this->select('product.pr_Id, product.pr_Name, product.product_images, product.pr_Selling_Price, AVG(rw.rating) as avg_ratings')               ->join('reviews', 'reviews.pr_Id = product.pr_Id', 'left')
//                 ->where('product.cat_Id', $cat_Id)
//                 ->where('product.pr_Status', 1)
//                 ->where('product.pr_Id !=', $excludeId)
//                 ->join('reviews', 'reviews.pr_Id = product.pr_Id', 'left')
//                 ->groupBy('product.pr_Id, product.pr_Name, product.product_images, product.pr_Selling_Price')
//                 ->orderBy('product.pr_createdon', 'DESC')
//                 ->findAll(8);
// }
    public function getSimilarProducts($cat_Id, $excludeId)
    {
        return $this->select('product.pr_Id, product.pr_Name, product.product_images, product.pr_Selling_Price, AVG(reviews.rating) as avg_rating')
            ->join('reviews', 'reviews.pr_Id = product.pr_Id', 'left')
            ->where('product.cat_Id', $cat_Id)
            ->where('product.pr_Status', 1)
            ->where('product.pr_Id !=', $excludeId)
            ->groupBy('product.pr_Id, product.pr_Name, product.product_images, product.pr_Selling_Price')
            ->orderBy('product.pr_createdon', 'DESC')
            ->findAll(8);
    }

    public function getProductsByModifiedDate()
    {
        return $this->select('product.*, AVG(reviews.rating) AS ratings')
            ->join('reviews', 'reviews.pr_Id = product.pr_Id', 'left')
            ->where('product.pr_Status', 1)
            ->where('product.pr_createdon IS NOT NULL') // or any condition you want
            ->groupBy('product.pr_Id')
            ->orderBy('product.pr_createdon', 'DESC')
            ->findAll();

    }


    public function getAllProduct()
    {
        return $this->db->query("select pd.*, avg(rating) as ratings 
				from product as pd 
				left join reviews as rw on rw.pr_Id = pd.pr_Id 
				where pd.pr_Status = 1 group by pd.pr_Id")->getResultArray();
    }
    public function getProductsByCategoryName($cat_Id)
    {
        return $this->select('product.*, AVG(reviews.rating) AS ratings')
            ->join('reviews', 'reviews.pr_Id = product.pr_Id', 'left')
            ->where('product.cat_Id', $cat_Id)
            ->where('product.pr_Status', 1)
            ->groupBy('product.pr_Id')
            ->orderBy('product.pr_createdon', 'DESC')
            ->findAll();


    }

    public function getProductsBySubcategoryName($sub_Id)
    {
        return $this->select('product.*, AVG(reviews.rating) AS ratings')
            ->join('reviews', 'reviews.pr_Id = product.pr_Id', 'left')
            ->where('product.sub_Id', $sub_Id)
            ->where('product.pr_Status', 1)
            ->groupBy('product.pr_Id')
            ->orderBy('product.pr_createdon', 'DESC')
            ->findAll();


    }

    // public function searchProducts($keyword)
// {
//     $keyword = trim($keyword);
//     $keywordNoSpace = str_replace(' ', '', $keyword);

    //     return $this->select('product.*, AVG(reviews.rating) AS ratings')
//         ->join('reviews', 'reviews.pr_Id = product.pr_Id', 'left')
//         ->join('category', 'category.cat_Id = product.cat_Id', 'left')
//         ->join('subcategory', 'subcategory.sub_Id = product.sub_Id', 'left')
//         ->where('product.pr_Status', 1)
//         ->groupStart()
//             ->like('REPLACE(product.pr_Name, " ", "")', $keywordNoSpace) 
//             ->orlike('REPLACE(category.cat_Name, " ", "")', $keywordNoSpace) 
//             ->orlike('REPLACE(subcategory.sub_Category_Name, " ", "")', $keywordNoSpace) 
//             ->orLike('product.pr_Name', $keyword)                        
//             ->orLike('product.pr_Code', $keyword)
//             ->orLike('category.cat_Name', $keyword)                      
//             ->orLike('subcategory.sub_Category_Name', $keyword)          
//         ->groupEnd()
//         ->groupBy('product.pr_Id')
//         ->findAll();
// }
    public function searchProducts($keyword)
    {
        $keyword = strtolower(trim($keyword));
        $keywordNoSpace = str_replace(' ', '', $keyword);

        // Step 1: Get all active products with their ratings
        $products = $this->select('product.*,category.cat_Name, subcategory.sub_Category_Name, AVG(reviews.rating) AS ratings')
            ->join('reviews', 'reviews.pr_Id = product.pr_Id', 'left')
            ->join('category', 'category.cat_Id = product.cat_Id', 'left')
            ->join('subcategory', 'subcategory.sub_Id = product.sub_Id', 'left')
            ->where('product.pr_Status', 1)
            ->groupBy('product.pr_Id')
            ->findAll();

        $matched = [];

        foreach ($products as $product) {
            $name = strtolower(trim($product['pr_Name']));
            $code = strtolower(trim($product['pr_Code']));
            $cat = strtolower(trim($product['cat_Name']));
            $subcat = strtolower(trim($product['sub_Category_Name']));

            // Remove spaces for better matching
            $nameNoSpace = str_replace(' ', '', $name);
            $codeNoSpace = str_replace(' ', '', $code);
            $catNoSpace = str_replace(' ', '', $cat);
            $subcatNoSpace = str_replace(' ', '', $subcat);

            // Similarity scores
            similar_text($keyword, $name, $score1);
            similar_text($keywordNoSpace, $nameNoSpace, $score2);

            // Levenshtein distances
            $lev1 = levenshtein($keyword, $name);
            $lev2 = levenshtein($keywordNoSpace, $nameNoSpace);

            // Matching logic
            if (
                max($score1, $score2) >= 50 ||
                min($lev1, $lev2) <= 3 ||
                strpos($name, $keyword) !== false ||
                strpos($nameNoSpace, $keywordNoSpace) !== false ||
                strpos($code, $keyword) !== false ||
                strpos($cat, $keyword) !== false ||
                strpos($subcat, $keyword) !== false
            ) {
                $matched[] = $product;
            }
        }

        return $matched;
    }

    public function getProductsByModifiedDatePaginated($limit, $offset)
    {
        return $this->orderBy('pr_modifyon', 'DESC')
            ->where('pr_Status',1)
            ->findAll($limit, $offset);
    }



    public function getProductById($id)
    {

        $builder = $this->db->query("
        SELECT pd.*, AVG(rw.rating) AS avg_rating
        FROM product AS pd
        LEFT JOIN reviews AS rw ON rw.pr_Id = pd.pr_Id
        WHERE pd.pr_Status = 1 AND pd.pr_Id = ?
        GROUP BY pd.pr_Id
    ", [$id]);

        return $builder->getRowArray();
    }
    public function insertOrder($data)
    {
        $this->db->table('order_detail')->insert($data);
        return $this->db->insertID(); // return the inserted ID
    }


    public function getAllCategoriesAndSub()
    {
        $db = \Config\Database::connect();


        $categories = $db->table('category')
            ->select('category.cat_Id, category.cat_Name')
            ->join('product', 'product.cat_Id = category.cat_Id', 'inner')
            ->where('category.cat_Status', 1)
            ->where('product.pr_Status', 1)
            ->where('product.product_images!=', '')
            ->groupBy('category.cat_Id, category.cat_Name')
            ->orderBy('category.cat_Name', 'ASC')
            ->get()
            ->getResultArray();

        $subcategories = $db->table('subcategory')
            ->select('subcategory.sub_Id, subcategory.sub_Category_Name, subcategory.cat_Id')
            ->join('product', 'product.sub_Id = subcategory.sub_Id', 'inner')
            ->where('subcategory.sub_Status', 1)
            ->where('product.pr_Status', 1)
            ->where('product.product_images!=', '')
            ->groupBy('subcategory.sub_Id, subcategory.sub_Category_Name, subcategory.cat_Id')
            ->orderBy('subcategory.sub_Category_Name', 'ASC')
            ->get()
            ->getResultArray();


        // Map subcategories to categories
        $catMap = [];
        foreach ($categories as &$cat) {
            $cat['subcategories'] = [];
            $catMap[$cat['cat_Id']] = &$cat;
        }

        foreach ($subcategories as $sub) {
            if (isset($catMap[$sub['cat_Id']])) {
                $catMap[$sub['cat_Id']]['subcategories'][] = $sub;
            }
        }

        return $categories;


    }
    public function getProductsByCategory($categoryId)
    {
        return $this->db->table('product')
            ->where('cat_Id', $categoryId)
            ->where('pr_Status', 1)
            ->get()
            ->getResultArray();
    }

    public function getProductsBySubCategory($subCategoryId)
    {
        return $this->db->table('product')
            ->where('sub_Id', $subCategoryId)
            ->where('pr_Status', 1)
            ->get()
            ->getResultArray();
    }
    public function updateStockAfterOrder($pr_Id, $quantity)
    {
        $product = $this->find($pr_Id);
        if ($product && isset($product['pr_Stock'])) {
            $newStock = max(0, $product['pr_Stock'] - $quantity); // avoid negative stock
            $this->update($pr_Id, ['pr_Stock' => $newStock]);
        }
    }
    public function getProductsPaginated($limit, $offset)
    {
        return $this->orderBy('pr_Id', 'DESC')
        ->where('pr_Status',1)
        ->findAll($limit, $offset);
    }

    // public function searchProductsPaginated($keyword, $limit, $offset){
    //     return $this->like('pr_Name', $keyword)
    //                 ->orLike('pr_Description', $keyword)
    //                 ->orderBy('pr_Id', 'DESC')
    //                 ->findAll($limit, $offset);
    // }
public function searchProductsPaginated($keyword, $limit, $offset)
{
    $keyword = strtolower(trim($keyword));
    $keyword = $this->normalizeKeyword($keyword); 
    $keywordNoSpace = str_replace(' ', '', $keyword);

    // Step 1: Fetch all active products with their associated data
    $products = $this->select('product.*, category.cat_Name, subcategory.sub_Category_Name, AVG(reviews.rating) AS ratings')
        ->join('reviews', 'reviews.pr_Id = product.pr_Id', 'left')
        ->join('category', 'category.cat_Id = product.cat_Id', 'left')
        ->join('subcategory', 'subcategory.sub_Id = product.sub_Id', 'left')
        ->where('product.pr_Status', 1)
        ->groupBy('product.pr_Id')
        ->findAll();

    $matched = [];

    foreach ($products as $product) {
        $name   = strtolower(trim($product['pr_Name']));
        $code   = strtolower(trim($product['pr_Code']));
        $cat    = strtolower(trim($product['cat_Name']));
        $subcat = strtolower(trim($product['sub_Category_Name']));

        $nameNoSpace   = str_replace(' ', '', $name);
        $codeNoSpace   = str_replace(' ', '', $code);
        $catNoSpace    = str_replace(' ', '', $cat);
        $subcatNoSpace = str_replace(' ', '', $subcat);

        // Similarity scores
        similar_text($keyword, $name, $score1);
        similar_text($keywordNoSpace, $nameNoSpace, $score2);

        // Levenshtein distances
        $lev1 = levenshtein($keyword, $name);
        $lev2 = levenshtein($keywordNoSpace, $nameNoSpace);

        // Matching conditions
        if (
            max($score1, $score2) >= 50 ||
            min($lev1, $lev2) <= 3 ||
            strpos($name, $keyword) !== false ||
            strpos($nameNoSpace, $keywordNoSpace) !== false ||
            strpos($code, $keyword) !== false ||
            strpos($cat, $keyword) !== false ||
            strpos($subcat, $keyword) !== false
        ) {
            $matched[] = $product;
        }
    }

    // Step 3: Apply manual pagination
    return array_slice($matched, $offset, $limit);
}



    public function getPaginatedProductsByCategory($cat_id, $limit, $offset)
{

    return $this->db->table('product')
        ->where('cat_Id', $cat_id)
        ->where('pr_Status', 1)
        ->limit($limit, $offset)
        ->orderBy('pr_Id', 'DESC')
        ->get()
        ->getResultArray();
}
public function countSearchedProducts($keyword)
{
    $all = $this->searchProductsPaginated($keyword, PHP_INT_MAX, 0);
    return count($all);
}


private function normalizeKeyword($keyword)
{
    $keyword = strtolower(trim($keyword));
    $synonyms = [
        'sari' => 'saree',
        'kurta' => 'kurti',
        'kurthi'=> 'kurti',
        'lehnga' => 'lehenga',
        'pant' => 'pants',
        'shirt' => 'top',
        'jins'=> 'jeans',
        'churidar'=> 'chuidhar',
        // add more mappings as needed
    ];

    return $synonyms[$keyword] ?? $keyword;
}



}
?>