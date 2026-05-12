<?php
namespace App\Models\Admin;

use CodeIgniter\Model;

class Stock_Model extends Model
{
    protected $table = 'product';
    protected $primaryKey = 'pr_Id';
    protected $allowedFields = [
        'pr_Name', 'pr_Code', 'mrp', 'pr_Selling_Price', 
        'pr_Discount_Value', 'pr_Stock', 'pr_Status', 'pr_Reset_stock'
    ];

    public function __construct()
    {
        parent::__construct(); // always call the parent constructor
        $this->db = \Config\Database::connect();
    }

    public function getAllProducts()
    {
        return $this->db->table($this->table)
                        ->where('pr_Status !=', 3)
                        ->get()
                        ->getResult(); // returns array of objects
    }

    public function getProductByid($pr_Id)
    {
        $sql = "SELECT * FROM product WHERE pr_Id = ?";
        return $this->db->query($sql, [$pr_Id])->getRowArray(); // ✅ fixed method name
    }

    public function updateProduct($pr_Id, $data)
    {
        return $this->db->table($this->table)
                        ->where('pr_Id', $pr_Id)
                        ->update($data);
    }
}
