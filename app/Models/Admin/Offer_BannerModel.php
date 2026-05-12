<?php 
namespace App\Models;

use CodeIgniter\Model;

class Offer_BannerModel extends Model {
	
        public function __construct() {
            $this->db = \Config\Database::connect();
        }
        public function getAllBanners()
		{
			return $this->db->table('theme t')
			->select('t.*, c.cat_Name AS category_name, s.sub_Category_Name AS subcategory_name')
			->join('category c', 'c.cat_Id = t.the_CatId', 'left')
			->join('subcategory s', 's.sub_Id = t.the_SubId', 'left')
			->where('t.the_Status !=', 3)
			->groupStart()
				->where('t.the_CatId IS NOT NULL', null, false)
				->orWhere('t.the_SubId IS NOT NULL', null, false)
			->groupEnd()
			->get()
			->getResultArray();
		}
        public function createBanner($data) {
            return $this->db->table('theme')->insert($data);
        }
		public function getAllCategories()
		{
			return $this->db->table('category')
				->where('cat_Status !=', 3)
				->get()
				->getResult();
		}
		public function getSubcategoriesByCatId($cat_id)
		{
			return $this->db->table('subcategory') // Use your actual table name
			->where('cat_id', $cat_id)
			->where('sub_Status', 1) // Only active subcategories
			->get()
			->getResultArray(); // Return as array
		}
		public function getProductByCategoryAndSubcategory($cat_id, $sub_id)
		{
			return $this->db->table('product')
			->select('pr_Id, pr_Name')  
			->where('cat_id', $cat_id)
			->where('sub_id', $sub_id)
			->where('pr_Status', 1) // Only active products
			->get()
			->getResult(); // Returns array of objects
		}
		public function getThemeByid($id){
             return $this->db->table('theme')->where('the_Id', $id)->get()->getRow(); 
		}
		public function updateTheme($id, $data)
		{
			return $this->db->table('theme')->where('the_Id', $id) ->update($data);
		}
        public function deleteBannerById($the_status, $the_id, $modified_by)
		{
			return $this->db->query("update theme set the_Status = '".$the_status."', the_modifyon=NOW(), the_modifyby='".$modified_by."' where the_Id = '".$the_id."'");
		}
		public function modifyBanner($the_id,$data) {
					
			$this->db->table('theme')->where('the_Id',$the_id)->update($data);
			return $this->db->getLastQuery();
		}
		

  ////////////////////////////////////////////////////////
    protected $table = 'theme';
    protected $primaryKey = 'the_Id';
    protected $allowedFields = ['the_Name', 'the_Offer_Banner', 'the_Status']; // Adjust to your table

    // For DataTables
   public function getDatatables()
{
    $builder = $this->db->table('theme t');
    
    // Select required fields including category and subcategory names
    $builder->select('t.*, c.cat_Name as category_name, s.sub_Category_Name as subcategory_name, p.pr_Name as product_name');

    // Join category and subcategory tables
    $builder->join('category c', 'c.cat_Id = t.the_CatId', 'left');
    $builder->join('subcategory s', 's.sub_Id = t.the_SubId', 'left');
	$builder->join('product p', ' p.pr_Id = t.the_PrId', 'left'); 

    // Only fetch rows where either category or subcategory exists
    $builder->where('t.the_Status !=', 3);
    $builder->groupStart()
            ->where('t.the_CatId IS NOT NULL')
            ->orWhere('t.the_SubId IS NOT NULL')
			->orWhere('t.the_PrId IS NOT NULL')
            ->groupEnd();
     

    // Add search logic if required
    $postData = service('request')->getPost();
    if (!empty($postData['search']['value'])) {
        $builder->groupStart()
                ->like('t.the_Name', $postData['search']['value'])
                ->orLike('c.cat_Name', $postData['search']['value'])
                ->orLike('s.sub_Category_Name', $postData['search']['value'])
				->orLike('p.pr_Name', $postData['search']['value'])
                ->groupEnd();
    }

    // Add pagination (limit and offset)
    if (!empty($postData['length']) && $postData['length'] != -1) {
        $builder->limit($postData['length'], $postData['start']);
    }

    // Apply ordering if provided
    if (!empty($postData['order'])) {
        $columns = ['t.the_Id', 't.the_Name', 'c.cat_Name', 's.sub_Category_Name', 'p.pr_Name','t.the_Offer_Banner','t.the_Status'];
        $orderCol = $columns[$postData['order'][0]['column']];
        $orderDir = $postData['order'][0]['dir'];
        $builder->orderBy($orderCol, $orderDir);
    }

    // Execute the query and return the result
    return $builder->get()->getResultArray();
}


	public function countAll()
	{
		return $this->db->table('theme')
			->where('the_Status !=', 3)
			->countAllResults();
	}

	public function countFiltered()
{
    $builder = $this->db->table('theme t');
    
    // Join category and subcategory tables
    $builder->join('category c', 'c.cat_Id = t.the_CatId', 'left');
    $builder->join('subcategory s', 's.sub_Id = t.the_SubId', 'left');
	$builder->join('product p', ' p.pr_Id = t.the_PrId', 'left');  

    // Only fetch rows where either category or subcategory or products exists
    $builder->where('t.the_Status !=', 3);
    $builder->groupStart()
            ->where('t.the_CatId IS NOT NULL')
            ->orWhere('t.the_SubId IS NOT NULL')
			->orWhere('t.the_PrId IS NOT NULL')
            ->groupEnd();

    $postData = service('request')->getPost();
    if (!empty($postData['search']['value'])) {
        $builder->groupStart()
                ->like('t.the_Name', $postData['search']['value'])
                ->orLike('c.cat_Name', $postData['search']['value'])
                ->orLike('s.sub_Category_Name', $postData['search']['value'])
				->orLike('p.pr_Name', $postData['search']['value'])
                ->groupEnd();
    }
    return $builder->countAllResults();
}




  ///////////////////////////////////////////////////////

}
?>