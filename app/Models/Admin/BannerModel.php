<?php 
namespace App\Models;

use CodeIgniter\Model;

class BannerModel extends Model {
	
        public function __construct() {
            $this->db = \Config\Database::connect();
        }
        public function getAllBanners() {
			return $this->db->table('theme')
			->where('the_Status !=', 3)
			->where('the_CatId IS NULL', null, false)
			->where('the_SubId IS NULL', null, false)
			->get()
			->getResultArray();
        }
        public function createBanner($data) {
            return $this->db->table('theme')->insert($data);
        }
		public function getThemesByid($id){
            return $this->db->table('theme')->where('the_Id', $id)->get()->getRow(); 
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
  /****************************************************************************************************************/
   protected $table = 'theme';
    protected $primaryKey = 'the_Id';
    protected $allowedFields = ['the_Name', 'the_Home_Banner', 'the_Status']; // Adjust to your table

    // For DataTables
   public function getDatatables()
{
    $builder = $this->db->table('theme t');
    
    // Select required fields including category and subcategory names
    $builder->select('t.*');
	
    // Only fetch rows where either category or subcategory exists
    $builder->where('t.the_Status !=', 3);

    // Add search logic if required
    $postData = service('request')->getPost();
    if (!empty($postData['search']['value'])) {
        $builder->groupStart()
                ->like('t.the_Name', $postData['search']['value'])
                ->groupEnd();
    }

    // Add pagination (limit and offset)
    if (!empty($postData['length']) && $postData['length'] != -1) {
        $builder->limit($postData['length'], $postData['start']);
    }

    // Apply ordering if provided
    if (!empty($postData['order'])) {
        $columns = ['t.the_Id', 't.the_Name', 't.the_Home_Banner','t.the_Status'];
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

    // Only fetch rows where either category or subcategory or products exists
    $builder->where('t.the_Status !=', 3);
 
    $postData = service('request')->getPost();
    if (!empty($postData['search']['value'])) {
        $builder->groupStart()
                ->like('t.the_Name', $postData['search']['value'])
                ->groupEnd();
    }
    return $builder->countAllResults();
}




  /***************************************************************************************************/
  }      

?>