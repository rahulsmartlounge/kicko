<?php 
namespace App\Models;

use CodeIgniter\Model;

class DeliveryModel extends Model {
	
        public function __construct() {
            $this->db = \Config\Database::connect();
        }
       
        public function deliveryInsert($data) {
            return $this->db->table('delivery')->insert($data);
        }
       
        public function getAll() {
            return $this->db->query("SELECT * FROM delivery WHERE cat_Status <> 3")->getResultArray();
        }
         public function getDeliveryByid($id){
            return $this->db->table('delivery')->where('cat_Id', $id)->get()->getRow(); 
    }
}