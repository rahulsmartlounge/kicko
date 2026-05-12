<?php

namespace App\Models\Admin;

use CodeIgniter\Model;

class AddressModel extends Model
{
		public function getAllCustomer($cust_id)
		{
			return $this->db->table('customer c')
				->join('address s', 'c.cust_Id = s.add_CustId')
				->select('c.*, s.*')
				->where('c.cust_Status !=', 3)
				->where('s.add_Status !=', 3)
				->where('s.add_CustId', $cust_id) // Added condition
				->get()
				->getResultArray();
		}
		public function getCustomerByid($add_Id)
		{
			return $this->db->query("select * from address where add_Id = '".$add_Id."'")->getRow();
		}
		public function createcust($data) {
            return $this->db->table('address')->insert($data);
        }
		public function deleteCustById($add_status, $add_id, $modified_by)
		{
			return $this->db->query("update address set add_Status = '".$add_status."', add_modifyon=NOW(), add_modifyby='".$modified_by."' where add_Id = '".$add_id."'");
		}
		public function modifyaddress($add_id,$data) {
					
			$this->db->table('address')->where('add_Id',$add_id)->update($data);
			return $this->db->getLastQuery();
		}


}
