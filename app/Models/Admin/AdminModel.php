<?php 
namespace App\Models;

use CodeIgniter\Model;

class AdminModel extends Model {
	
        public function __construct() {
            $this->db = \Config\Database::connect();
        }
			public function getdata($val)
		{
			return $this->db->query("select * from user where us_Id = '".$val."'")->getRow();
		}
		public function modifyAdmin($us_id,$data) {
					
			$this->db->table('user')->where('us_Id',$us_id)->update($data);
			return $this->db->getLastQuery();
		}
		public function getStaffByid($id){
            return $this->db->table('user')->where('us_Id', $id)->get()->getRow(); 
		}
		public function emailExistsExcept($email, $excludeId)
		{
			$builder = $this->db->table('user');
			$builder->where('us_Email', $email);
			$builder->where('us_Id !=', $excludeId);
			$builder->where('us_Status !=', 3);
			$query = $builder->get();
			return $query->getNumRows() > 0;
		}
    }

?>
