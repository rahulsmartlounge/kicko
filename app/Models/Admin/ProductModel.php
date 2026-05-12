<?php
namespace App\Models\Admin;

use CodeIgniter\Model;

class ProductModel extends Model
{

    protected $table = 'product';
    protected $primaryKey = 'pr_Id';

    // Adjust to your table

    protected $allowedFields = ['pr_Name', 'pr_Code', 'mrp', 'pr_Selling_Price', 'pr_Discount_Value', 'pr_Discount_Type', 'pr_Stock', 'pr_Aval_Colors', 'pr_Size', 'pr_Status', 'pr_Reset_Stock'];// Adjust to your table




    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }


    public function getAllProducts()
    {
        return $this->db->table('product')
            ->select('product.*, subcategory.sub_Category_Name, category.cat_Name')
            ->join('subcategory', 'subcategory.sub_Id = product.sub_Id', 'left')
            ->join('category', 'category.cat_Id = product.cat_Id', 'left')
            ->where('product.pr_Status !=', 3)
            ->get()
            ->getResult();
    }

    public function isProductCodeExists($product_code, $excludeId = null)
    {
        $builder = $this->db->table('product');
        $builder->where('pr_Code', $product_code);
        $builder->where('pr_Status !=', 3);

        if ($excludeId) {
            $builder->where('pr_Id !=', $excludeId);
        }

        return $builder->get()->getRow();
    }


    public function isProductExists($productName, $excludeId = null)
    {
        $builder = $this->db->table('product');
        $builder->where('pr_Name', $productName);
        $builder->where('pr_Status !=', 3);

        if ($excludeId) {
            $builder->where('pr_Id !=', $excludeId);
        }

        return $builder->get()->getRow();
    }



    public function getProductByid($id)
    {
        return $this->db->table('product p')
            ->select('p.*, c.cat_Name, s.sub_Category_Name')
            ->join('category c', 'c.cat_Id = p.cat_id', 'left')
            ->join('subcategory s', 's.sub_Id = p.sub_id', 'left')
            ->where('p.pr_Id', $id)
            ->get()
            ->getRow();
    }


    public function productInsert($data){
        $data['pr_modifyon'] = date('Y-m-d H:i:s'); 
        return $this->db->table('product')->insert($data);
    }


    public function getAllCatandSub()
    {
        return $this->db->table('subcategory')
            ->select('subcategory.*, category.cat_Name')
            ->join('category', 'category.cat_Id = subcategory.cat_Id', 'left')
            ->where('subcategory.sub_Status !=', 3)
            ->get()
            ->getResult();
    }
    public function updateProduct($id, $data){
        $data['pr_modifyon'] = date('Y-m-d H:i:s'); 
        return $this->db->table('product')->where('pr_Id', $id)->update($data);
    }

    public function getSubcategoriesByCatId($catId)
    {
        return $this->db->table('subcategory')
            ->where('cat_Id', $catId)
            ->where('sub_Status !=', 3)
            ->get()
            ->getResult();
    }

    public function getAllCategories()
    {
        return $this->db->table('category')
            ->where('cat_Status !=', 3)
            ->get()
            ->getResult();
    }


    public function getProductImages($productId)
    {
        return $this->db->table('product')
            ->where('pr_Id', $productId)
            ->select('product_images')
            ->get()
            ->getRowArray()['product_images'] ?? '[]';
    }


    public function delete_image($product_id, $image_name)
    {
        $builder = $this->db->table('product');
        $product = $builder->where('pr_Id', $product_id)->get()->getRow();

        if ($product && $product->product_images) {

            $imageData = json_decode($product->product_images, true);

            if (!empty($imageData) && isset($imageData[0]['name'])) {

                $filteredImages = array_filter($imageData[0]['name'], function ($img) use ($image_name) {
                    return trim($img) !== trim($image_name);
                });


                $newImageData = [['name' => array_values($filteredImages)]];
                $builder->where('pr_Id', $product_id)
                    ->update(['product_images' => json_encode($newImageData)]);

                return true;
            }
        }

        return false;
    }





    public function updateProductImages($productId, $mediaJson)
    {
        return $this->db->table('product')
            ->where('pr_Id', $productId)->set(['product_images' => $mediaJson])->update();

    }

    public function deleteProductById($pr_status, $pr_id, $modified_by)
    {
        return $this->db->query("update product set pr_Status = '" . $pr_status . "', pr_modifyon=NOW(), pr_modifyby='" . $modified_by . "' where pr_Id = '" . $pr_id . "'");
    }


    public function updateProductVideo($productId, $video)
    {
        return $this->db->table('product')
            ->where('pr_Id', $productId)
            ->set(['product_video' => $video])
            ->update();

    }

    public function getVideo($productId)
    {
        return $this->db->table('product')
            ->select('product_video')
            ->where('pr_Id', $productId)
            ->get()
            ->getRow();
    }

    public function deleteProductVideo($productId)
    {
        return $this->db->table('product')
            ->where('pr_Id', $productId)
            ->update(['product_video' => null]);
    }

    // For DataTables////

    public function getDatatables()
    {
        $builder = $this->db->table('product p');
        $builder->select('p.*');
        $builder->where('p.pr_Status !=', 3);

        $postData = service('request')->getPost();
        if (!empty($postData['search']['value'])) {
            $search = preg_replace('/\s+/', '', $postData['search']['value']);
            $escaped = $this->db->escapeLikeString($search);

            $builder->groupStart()
                ->where("REPLACE(REPLACE(p.pr_Name, ' ', ''), CHAR(9), '') LIKE '%$escaped%'", null, false)
                ->groupEnd();
        }
        if (!empty($postData['order'])) {
            $columns = ['pr_Name', 'mrp', 'pr_Selling_Price', 'pr_Discount_Value', 'pr_Stock', 'pr_Status'];
            $orderColIndex = $postData['order'][0]['column'];
            $orderDir = $postData['order'][0]['dir'];
            $orderCol = $columns[$orderColIndex] ?? 'pr_modifyon';
            $builder->orderBy("p.$orderCol", $orderDir);
        } else {
            $builder->orderBy('p.pr_modifyon', 'DESC');
        }
        if (!empty($postData['length']) && $postData['length'] != -1) {
            $builder->limit($postData['length'], $postData['start']);
        }

        return $builder->get()->getResultArray();
    }


    public function countAll()
    {
        return $this->db->table('product')
            ->where('pr_Status !=', 3)
            ->countAllResults();
    }


    public function countFiltered()
    {
        $builder = $this->db->table('product p');
        $builder->where('p.pr_Status !=', 3);

        $postData = service('request')->getPost();
        if (!empty($postData['search']['value'])) {
            $search = preg_replace('/\s+/', '', $postData['search']['value']);
            $escaped = $this->db->escapeLikeString($search);

            $builder->groupStart()
                ->where("REPLACE(REPLACE(p.pr_Name, ' ', ''), CHAR(9), '') LIKE '%$escaped%'", null, false)
                ->groupEnd();
        }

        return $builder->countAllResults();
    }




    //Check if there exist any discount in sub-category
    public function isDiscountInSub($sub_id)
    {
        $discountInSub = "SELECT sub_Discount_Value, sub_Discount_Type FROM subcategory WHERE sub_Id = ?";
        return $this->db->query($discountInSub, [$sub_id])->getRow();
    }
    public function isDiscountInCat($cat_id)
    {
        $discountInCat = "SELECT cat_Discount_Value, cat_Discount_Type FROM category WHERE cat_Id  = ?";
        return $this->db->query($discountInCat, [$cat_id])->getRow();
    }


}

?>