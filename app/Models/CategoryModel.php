<?php 
namespace App\Models;
use CodeIgniter\Model;

class CategoryModel extends Model
{
    protected $table = 'category';

    // Fix: removed the space from primaryKey
    protected $primaryKey = 'cat_Id';

    protected $allowedFields = []; // Add actual allowed fields if you plan to use insert/update

    public function getAllCategory()
    {
        $query = "
            SELECT * FROM (
    SELECT 
        c.cat_Id,
        c.cat_Name,
        (
            SELECT p.product_images
            FROM product p
            WHERE p.cat_Id = c.cat_Id 
              AND p.product_images IS NOT NULL
              AND p.product_images != ''
              AND p.pr_Status = 1
              AND c.cat_Status = 1
            ORDER BY RAND()
            LIMIT 1
        ) AS product_images
        FROM category c
    ) AS category_images
    WHERE product_images IS NOT NULL

        ";
        return $this->db->query($query)->getResultArray();
    }
    
    public function getAllProductUnderCategory($id){
        return $this->db->query("select * from product where cat_Id = '".$id."' and pr_Status = 1 ")->getResultArray();
    }
 
    public function getAllSubcategoryUnderCategory($id) {
    return $this->db->query("
        SELECT DISTINCT s.sub_Id, s.sub_Category_Name, p.product_images
        FROM subcategory s
        JOIN product p ON p.sub_Id = s.sub_Id
        WHERE p.cat_Id = '".$id."' AND p.pr_Status = 1 AND p.product_images!='' AND s.sub_Status = 1 ORDER BY RAND()
    ")->getResultArray();
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

public function getProductCountByCategory($catId)
{
    return $this->db->table('product')
        ->where('cat_Id', $catId)
        ->countAllResults();
}

    public function getProductsByModifiedDatePaginated($limit, $offset)
    {
        return $this->orderBy('pr_modifyon', 'DESC')
            ->findAll($limit, $offset);
    }

}

?>