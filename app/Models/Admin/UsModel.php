<?php 
namespace App\Models\Admin;

use CodeIgniter\Model;

class UsModel extends Model {

	public function __construct() {
		$this->db = \Config\Database::connect();
	}
	public function getLoginAccount($email, $password) {
		return $this->db->query("select * from user where us_Email = '".$email."' and us_Password = '".$password."'")->getRow();

	}
	public function checkEmail($email) {
		return $this->db->query("select us_Id from user where us_Email = '".$email."'")->getRow();
	}
	public function createUser($data) {
		return $this->db->table('user')->insert($data);
	}
}
?>
