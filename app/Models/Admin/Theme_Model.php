<?php 
namespace App\Models\Admin;

use CodeIgniter\Model;

class Theme_Model extends Model {
	
        public function __construct() {
            $this->db = \Config\Database::connect();
        }
        public function getAllBanners() {
			return $this->db->table('themes')
			->where('theme_Status !=', 3)
			->get()
			->getResultArray();
        }
        /* public function createBanner($data) {
            return $this->db->table('theme')->insert($data);
        } */
		//  public function fetchTheme() 
		//  {
		// 	return $this->db->table('themes')
		// 	->where('theme_Status ==', 1)
		// 	->get()
		// 	->getResultArray();
        // }
		public function fetchTheme() 
		{
			return $this->db->table('themes')
			->where('theme_Status', 1)
			->get()
			->getResultArray();
		}
		public function insert_data($data)
		{
			return $this->db->table('themes')->insert($data);
		}

		public function modifyThemes($id, $data)
		{
			return $this->db->table('themes')->where('theme_Id', $id)->update($data);
		}
		public function getUpdateAllStatus()
		{
			return $this->db->query("UPDATE themes SET theme_Status = '2' WHERE theme_Status = '1'");
		}

		public function getThemesByid($id)
		{
            
			return $this->db->table('themes')->where('theme_Id', $id)->get()->getRow();
		}

		 /* public function insert_data($data) {
            return $this->db->table('themes')->insert($data);
        }*/
		
		public function getThemeByid($id){
            return $this->db->table('themes')->where('theme_Id', $id)->get()->getRow(); 
		} 
		public function getThemeStatusByid($themeId){
            return $this->db->table('themes')->where('theme_Id', $themeId)->get()->getRow(); 
		}
		public function deactivateAllThemesExcept($themeId)
		{
			return $this->db->table('themes')
				->where('theme_Id !=', $themeId)
				->where('theme_Status', 1) // Only update if current status is 1
				->update(['theme_Status' => 2]);
		}

		public function updateTheme($themeId, $data)
		{
			return $this->db->table('themes')->where('theme_Id', $themeId) ->update($data);
		}
        public function deleteBannerById($theme_Status, $theme_id, $modified_by)
		{
			return $this->db->query("update themes set theme_Status = '".$theme_Status."', theme_modifyon=NOW(), theme_modifyby='".$modified_by."' where theme_Id = '".$theme_id."'");
		}
		/* public function modifyThemes($theme_id,$data) {
					
			$this->db->table('themes')->where('theme_Id',$theme_id)->update($data);
			return $this->db->getLastQuery();
		} */
  /****************************************************************************************************************/
	protected $table = 'themes';
		protected $primaryKey = 'theme_Id';
		protected $allowedFields = ['theme_Name', 'theme_Description', 'theme_Status']; // Adjust to your table
	//   public function getDatatables()

	public function getDatatables() {
		$builder = $this->db->table('themes t');
		$builder->select('t.*');
		$builder->where('t.theme_Status !=', 3);

		$postData = service('request')->getPost();

		if (!empty($postData['search']['value'])) {
			// Remove all whitespace (spaces, tabs, newlines)
			$search = preg_replace('/\s+/', '', $postData['search']['value']);
			$escaped = $this->db->escapeLikeString($search);

			$builder->groupStart()
				->where("REPLACE(REPLACE(t.theme_Name, ' ', ''), CHAR(9), '') LIKE '%$escaped%'", null, false)
				->orWhere("REPLACE(REPLACE(t.theme_Description, ' ', ''), CHAR(9), '') LIKE '%$escaped%'", null, false)
				->groupEnd();
		}

		if (!empty($postData['length']) && $postData['length'] != -1) {
			$builder->limit($postData['length'], $postData['start']);
		}

		if (!empty($postData['order'])) {
			$columns = ['t.theme_Id', 't.theme_Name', 't.theme_Description', 't.theme_Status'];
			$orderCol = $columns[$postData['order'][0]['column']] ?? 't.theme_Id';
			$orderDir = $postData['order'][0]['dir'] ?? 'DESC';
			$builder->orderBy($orderCol, $orderDir);
		}

		return $builder->get()->getResultArray();
	}


	public function countFiltered() {
		$builder = $this->db->table('themes t');
		$builder->where('t.theme_Status !=', 3);

		$postData = service('request')->getPost();
		if (!empty($postData['search']['value'])) {
			$search = preg_replace('/\s+/', '', $postData['search']['value']);
			$escaped = $this->db->escapeLikeString($search);

			$builder->groupStart()
				->where("REPLACE(REPLACE(t.theme_Name, ' ', ''), CHAR(9), '') LIKE '%$escaped%'", null, false)
				->orWhere("REPLACE(REPLACE(t.theme_Description, ' ', ''), CHAR(9), '') LIKE '%$escaped%'", null, false)
				->groupEnd();
		}

		return $builder->countAllResults();
    }





  /***************************************************************************************************/
  }      

?>