<?php
namespace App\Controllers\Api;
use App\Controllers\BaseController;
use App\Models\Admin\UsModel;

class Auth extends BaseController
{
	public function __construct()
	{

		$this->session = \Config\Services::session();
		$this->input = \Config\Services::request();
		$this->usModel = new \App\Models\Admin\UsModel();

	}
	public function appauth()
	{
		$email = $this->request->getPost('email');
		$password = md5($this->request->getPost('password'));
		
		// Authenticate user
		if ($email && $password) {
			
			$userLog = $this->usModel->getLoginAccount($email, $password);
			if ($userLog) {

				switch ($userLog->us_Status) {
					case '1':
						echo json_encode(['status' => 1, 'msg' => 'Login successfull', 'uid' => $userLog->us_Id]);
						break;
					case '2':
						echo json_encode(['status' => 0, 'msg' => 'Staff Access Restricted. Please Contact Admin.']);
						break;
					case '3':
						echo json_encode(['status' => 0, 'msg' => 'No Such Staff Member Exists.']);
						break;
				}
			} else {
				echo json_encode(['status' => 0, 'msg' => 'Invalid Credentials']);
			}
		} else {
			echo json_encode(['status' => 0, 'msg' => 'Login Credentials Are Mandatory']);
		}
		
	}
	public function registerUser() {
		$utype = $this->request->getPost('utype');
		$gst = $this->request->getPost('gst');
		$name = $this->request->getPost('name');
		$email = $this->request->getPost('email');
		$cntycode = $this->request->getPost('cntycode');
		$phone = $this->request->getPost('phone');
		$company = $this->request->getPost('company');
		$password = $this->request->getPost('password');
		
		if ($email && $password && $name && $phone) {
			$userLog = $this->usModel->checkEmail($email);
			if($userLog) {
				echo json_encode(['status' => 0, 'msg' => 'Email id already exist.']);
			}
			else {
				$data = [
					'us_utype'=>$utype,
					'us_Name' => $name,
					'us_Email' => $email,
					'us_Password' => md5($password),
					'us_Phone' => $cntycode.$phone,
					'us_Gst' => $gst,
					'us_Company' => $company,
					'us_Status' => 1,
					'us_Role' => 3,
				];
				$CreateUser = $this->usModel->createUser($data);
				echo json_encode(['status' => 1, 'msg' => 'User created successfully.']);
			}
		}
		else {
			echo json_encode(['status' => 0, 'msg' => 'All mandatory fileds are requird.']);
		}
		
	}
	public function logout()
	{
		$session = session();
		$session->remove(['ad_uid', 'ad_uname']); 
		return redirect()->to(base_url('admin'));
	}
}
?>