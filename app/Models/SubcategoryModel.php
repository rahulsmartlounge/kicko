<?php 
namespace App\Models;
use CodeIgniter\Model;

class SubcategoryModel extends Model
{
    protected $table = 'product';

    protected $primaryKey = 'pr_Id';

    protected $allowedFields = []; 

    public function getAllSubcategory($id){
        return $this->db->query(" select * from subcategory where sub_Id = '".$id."' and sub_Status = 1")->getRowArray();
    }
    public function getAllProductUnderSubcategory($id){
        return $this->db->query(" select * from product where sub_Id = '".$id."' and pr_Status = 1")->getResultArray();
    }
     
public function getSimilarProducts($cat_Id, $excludeId){
    return $this->select('product.pr_Id, product.pr_Name, product.product_images, product.pr_Selling_Price, AVG(reviews.rating) as ratings')
                ->join('reviews', 'reviews.pr_Id = product.pr_Id', 'left')
                ->where('product.cat_Id', $cat_Id)
                ->where('product.pr_Status', 1)
                ->where('product.sub_Id !=', $excludeId)
                ->groupBy('product.pr_Id, product.pr_Name, product.product_images, product.pr_Selling_Price')
                ->orderBy('product.pr_createdon', 'DESC')
        
        
                ->findAll(8);
}

public function getProductsBySubcategoryPaginated($subcatId, $limit, $offset)
{
    return $this->db->table('product')
        ->where('sub_Id', $subcatId)
        ->where('pr_Status', 1) // ✅ Only active products
        ->orderBy('pr_Id', 'DESC')
        ->limit($limit, $offset) // ✅ (limit, offset)
        ->get()
        ->getResultArray();
}




}

?>