<?php 
namespace App\Models;

use CodeIgniter\Model;

class PrivacypolicyModel extends Model {
	
        public function __construct() {
            $this->db = \Config\Database::connect();
        }
       
        public function privacypolicyInsert($data) {
            return $this->db->table('privacypolicy')->insert($data);
        }
       
        public function getAll() {
            return $this->db->query("SELECT * FROM privacypolicy WHERE cat_Status <> 3")->getResultArray();
        }
         public function getprivacypolicyByid($id){
            return $this->db->table('privacypolicy')->where('cat_Id', $id)->get()->getRow(); 
    }
}