<?php 
namespace App\Models\Admin;

use CodeIgniter\Model;

class CategoryModel extends Model {
	
        public function __construct() {
            $this->db = \Config\Database::connect();
        }
    
       
        public function getAllCategory() {
            return $this->db->query("SELECT * FROM category WHERE cat_Status <> 3")->getResultArray();
        }
         
    
public function isCategoryExists($categoryName, $excludeId = null) {
    $builder = $this->db->table('category');
    $builder->where('cat_Name', $categoryName);
    $builder->where('cat_Status !=', 3); // Ignore soft-deleted categories

    if ($excludeId) {
        $builder->where('cat_Id !=', $excludeId);
    }

    return $builder->get()->getRow();
}
	public function getCategoryByid($catId){

			return $this->db->query("select * from category where cat_Id = '".$catId."'")->getRow();
    }
	 
 public function categoryInsert($data) {
 	return $this->db->table('category')->insert($data);
	 }
	
	

public function updateCategory($catId, $data)
{
    $this->db->table('category')
        ->where('cat_Id', $catId)
        ->update($data);

    $category = $this->db->table('category')
        ->select('cat_Discount_Value, cat_Discount_Type')
        ->where('cat_Id', $catId)
		->where('cat_Status', 1)
        ->get()
        ->getRow();

    if (!$category) {
        return false;
    }

    $cat_Discount_Value = $category->cat_Discount_Value;
    $cat_Discount_Type = $category->cat_Discount_Type;

	 $products = $this->db->table('product')
							->where('cat_Id', $catId)
							->where('pr_Status', 1)
							->groupStart()
								->where('discount_from !=',1 )
								->where('discount_from !=',2 )
								->orwhere('pr_Discount_Value', '')
								->orWhere('pr_Discount_Value', 0)
								->orWhere('pr_Discount_Value IS NULL', null, false)
							->groupEnd()
							->get()
							->getResult();

    if ($cat_Discount_Value != null && $cat_Discount_Value != '' && $cat_Discount_Value != '0' && !empty($cat_Discount_Type) && $cat_Discount_Value !='0') {
       
			foreach ($products as $pr) {
			$mrp = $pr->mrp;

			if ($cat_Discount_Type === '%') {
				$cat_selling_price = $mrp - ($mrp * $cat_Discount_Value / 100);
			} elseif ($cat_Discount_Type === 'Rs') {
				$cat_selling_price = $mrp - $cat_Discount_Value;
			} 

			$this->db->table('product')
				->where('pr_Id', $pr->pr_Id)
				->update([
					'pr_Selling_Price' => $cat_selling_price,
					'pr_Discount_Value' => $cat_Discount_Value,
					'pr_Discount_Type' => $cat_Discount_Type,
					'discount_from' => "3",
					'pr_modifyon' => date('Y-m-d H:i:s')
				]);
		}
    }else{
		foreach ($products as $pr) {
		$mrp = $pr->mrp;
		$this->db->table('product')
				->where('pr_Id', $pr->pr_Id)
				->update([
					'pr_Discount_Value' => "0",
					'pr_Selling_Price' => $mrp,
					'discount_from' => "0",
					'pr_modifyon' => date('Y-m-d H:i:s')
				]);
			}
	}
    return true;
}

	// delete category
	
		public function deleteCategoryById($cat_id, $modified_by)
		{
			$subcategory = $this->db->table('subcategory')
									->where('cat_Id ' , $cat_id)
									->where('sub_Status !=', 3)
									->select('sub_Id ')
									->get()
									->getResult();
			$product = $this->db->table('product')
								->where('cat_Id' , $cat_id)
								->where('pr_Status' , 1)
								->select('pr_Id')
								->get()
								->getResult();
									
			if(empty($subcategory) && empty($product)){
				return $this->db->table('category')
					->where('cat_Id', $cat_id)
					->update([
						'cat_Status'   => 3,
						'cat_modifyon' => date('Y-m-d H:i:s'),
						'cat_modifyby' => $modified_by
					]);
			}else{
				return false;
			}			
		}
	
		
	//**************************Data table */
				
	protected $table = 'category';
    protected $primaryKey = 'cat_Id';
    protected $allowedFields = ['cat_Name', 'cat_Discount_Value','cat_Discount_Type','cat_Status']; // Adjust to your table

    // For DataTables

     public function getDatatables(){
	$postData = service('request')->getPost();
	$searchValue = '';
	if (!empty($postData['search']['value'])) {
		// Remove all whitespace (space, tab, newline)
		$searchValue = preg_replace('/\s+/', '', $postData['search']['value']);
	}

	$builder = $this->db->table('category c');
	$builder->select('c.*');
	$builder->where('c.cat_Status !=', 3);

	if (!empty($searchValue)) {
		$builder->groupStart();
		$escaped = $this->db->escapeLikeString($searchValue);
		$builder->where("REPLACE(REPLACE(c.cat_Name, ' ', ''), '\t', '') LIKE '%$escaped%'", null, false);
		$builder->groupEnd();
	}

	if (!empty($postData['length']) && $postData['length'] != -1) {
		$builder->limit($postData['length'], $postData['start']);
	}

	if (!empty($postData['order'])) {
		$columns = ['c.cat_Id', 'c.cat_Name', 'c.cat_Discount_Value', 'c.cat_Discount_Type', 'c.cat_Status'];
		$orderCol = $columns[$postData['order'][0]['column']];
		$orderDir = $postData['order'][0]['dir'];
		$builder->orderBy($orderCol, $orderDir);
	} else {
		$builder->orderBy('c.cat_Id', 'DESC');
	}

	return $builder->get()->getResultArray();
   }


	public function countAll()
	{
		return $this->db->table('category')
			->where('cat_Status !=', 3)
			->countAllResults();
	}

	public function countFiltered(){
	$postData = service('request')->getPost();
	$searchValue = '';
	if (!empty($postData['search']['value'])) {
		// Remove all whitespace (space, tab, newline)
		$searchValue = preg_replace('/\s+/', '', $postData['search']['value']);
	}

	$sql = "SELECT COUNT(*) as total 
			FROM category c 
			WHERE c.cat_Status != 3";

	if (!empty($searchValue)) {
		$escaped = $this->db->escapeLikeString($searchValue);
		$sql .= " AND REPLACE(REPLACE(c.cat_Name, ' ', ''), '\t', '') LIKE '%$escaped%'";
	}

	$query = $this->db->query($sql);
	return $query->getRow()->total;
   }


    }

    

?>