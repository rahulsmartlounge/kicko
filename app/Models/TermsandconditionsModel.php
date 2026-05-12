<?php 
namespace App\Models;

use CodeIgniter\Model;

class TermsandconditionsModel extends Model {
	
        public function __construct() {
            $this->db = \Config\Database::connect();
        }
       
        public function termsandconditionsInsert($data) {
            return $this->db->table('termsandconditions')->insert($data);
        }
       
        public function getAll() {
            return $this->db->query("SELECT * FROM termsandconditions WHERE cat_Status <> 3")->getResultArray();
        }
         public function getReturnpolicyByid($id){
            return $this->db->table('termsandconditions')->where('cat_Id', $id)->get()->getRow(); 
    }
}