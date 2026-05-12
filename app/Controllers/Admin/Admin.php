<?php
namespace App\Controllers;
use App\Models\Admin\AdminModel;

class Admin extends BaseController
{

    public function __construct()
    {
        $this->session = \Config\Services::session();
        $this->input = \Config\Services::request();
        $this->adminModel = new AdminModel();
    }

    public function index()
    {

		$data = [];
		$val =	$this->session->get('ad_uid');
		$staff_val = $this->adminModel->getdata($val);
		$data['staff'] = (array) $staff_val;
		
		// Load views
		$template = view('common/header');
		$template .= view('common/leftmenu');
		$template .= view('admin_update', $data);
		$template .= view('common/footer');
		$template .= view('page_scripts/adminjs');
		return $template;
    }
   public function createnew() {
		$us_id = $this->input->getPost('us_id');
		$staffname = $this->input->getPost('staffname');
		$staffemail = $this->input->getPost('staffemail');
		$staffotemail = $this->input->getPost('staffotemail');
		$mobile = $this->input->getPost('mobile');
		$oldpass	=	$this->input->getPost('old_password');
		$newPass = $this->input->getPost('new_password');
		    // Validate name
		if (!preg_match('/^[a-zA-Z0-9\s ]+$/', $staffname)) {
			return $this->response->setJSON(['status' => 'error', 'msg' => 'Please Enter Name Correctly.']);
		}
		// Validate email formats
			if (!filter_var($staffemail, FILTER_VALIDATE_EMAIL)) {
				return $this->response->setJSON([
					'status' => 'error',
					'msg' => 'Invalid Email Format.'
				]);
			}
			if (!ctype_digit($mobile) || strlen($mobile) !== 10) {
				return $this->response->setJSON([
					'status' => 'error',
					'msg' => 'Phone Number Must Contain Only 10 Digits.'
				]);
			}
			
				   // Allow only letters, numbers, @ and _
/* 		if (!preg_match('/^[a-zA-Z0-9@_]+$/', $newPassword)) {
			return $this->response->setJSON([
				'status' => 'error',
				'msg' => 'Password can only contain letters, numbers, @, and _.'
			]);
		} */
		$adminModel		=	new AdminModel;
		$existing = $adminModel->getStaffById($us_id);
		if(empty($oldpass)&& empty($newPass))
		{
			$newPassword	=	$existing->us_Password;
		}
		else{
		
			if (!empty($oldpass) && $existing->us_Password !== md5($oldpass)) {
				return $this->response->setJSON([
					'status' => 'error',
					'msg' => 'Password Not Matching With Old Password.'
				]);
			}
			else{
				
				if (empty($newPass) && md5($newPass) !== md5($oldpass)|| md5($newPass) === md5($oldpass)) {
				return $this->response->setJSON([
					'status' => 'error',
					'msg' => 'Please Check Your New Password.'
				]);
				}
				else{
				$newPassword	=	md5($newPass);
				}
			}
		}
		
		
		if($staffname && $staffemail && $mobile) {

				$data = [
				'us_Name'          => $staffname,
				'us_Email'         => $staffemail,
				'us_Email2'        => $staffotemail,
				'us_Phone'		   => $mobile,
				'us_Password'	   => $newPassword,
				'us_Status'		   => 1,
				'us_Role'		   => 1,
				'us_createdby'     => $this->session->get('ad_uid'),
				'us_modifyby'	   => $this->session->get('ad_uid'),     
			];	
				$modifyStaff = $this->adminModel->modifyAdmin($us_id,$data);
				//echo json_encode(array("status" => 1, "msg" => "Updated successfully."));	
				echo json_encode(array(
					"status" => 1,
					"msg" => "Updated Successfully.",
				));
			}
		else {
			return $this->response->setJSON([
				'status' => 'error',
				'message' => 'All Fields Are Required.'
			]);
		}
		
	}
}


