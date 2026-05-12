<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\Admin\ProfileModel;

class Profile extends BaseController
{
	protected $ProfileModel;
	
	public function __construct()
	{
		$this->session = \Config\Services::session();
		$this->input = \Config\Services::request();
		$this->ProfileModel = new \App\Models\Admin\ProfileModel();
        $us_Id = $this->session->get('zd_uid');
		
	}

	public function index()
{
   if (!$this->session->get('ad_uid')) {
				return redirect()->to(base_url('admin'));
			}
	
    $template = view('Admin/common/header');
    $template .= view('Admin/common/leftmenu');
    $template .= view('Admin/admin_update');
    $template .= view('Admin/common/footer');
    $template .= view('Admin/page_scripts/profilejs');
  
  return $template;
}

   public function edit_admin()
   {
    if (!$this->session->get('ad_uid')) {
				return redirect()->to(base_url('admin'));
			}
	   $us_Id = $this->session->ad_uid;
     $admin = $this->ProfileModel->getProfileById($us_Id); 
	 
	

    $data['user'] = (array) $admin; 

    $template = view('Admin/common/header');
    $template .= view('Admin/common/leftmenu');
    $template .= view('Admin/profile', $data);
    $template .= view('Admin/common/footer');
    $template .= view('Admin/page_scripts/profilejs');

    return $template;
}
public function update()
{
    $us_Id = $this->session->get('ad_uid'); 

    $name  = ucwords(strtolower($this->request->getPost('us_Name')));
    $email = $this->request->getPost('us_Email');
    $phone = $this->request->getPost('us_Phone'); 

    $data = [
        'us_Name'  => $name,
        'us_Email' => $email,
        'us_Phone' => $phone, 
    ];
 if (!preg_match('/^[a-zA-Z ]+$/', $name)) {
			return $this->response->setJSON(['status' => 'error', 'msg' => 'Please Enter Name Correctly.']);
		}
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
				return $this->response->setJSON([
					'status' => 'error',
					'msg' => 'Invalid Email Format.'
				]);
			}
    $model = new \App\Models\Admin\ProfileModel();

    if ($model->updateProfile($us_Id, $data)) {
        // Update session value
        $this->session->set('ad_name', $name);

        return $this->response->setJSON([
            'status'   => 1,
            'msg'      => 'Profile Updated Successfully.',
            'ad_name'  => $name
        ]);
    } else {
        return $this->response->setJSON([
            'status' => 0,
            'msg'    => 'Failed To Update Profile.'
        ]);
    }
}



public function change_password()
{
    $model = new \App\Models\Admin\ProfileModel();
    $current_password = $this->request->getPost('current_password');
    $new_password = $this->request->getPost('new_password');
    $confirm_password = $this->request->getPost('confirm_password');
    $us_Id = $this->session->get('ad_uid'); 

    if (!$current_password || !$new_password || !$confirm_password) {
        return $this->response->setJSON(['status' => 0, 'msg' => 'All Fields Are Required']);
    }

    if ($new_password !== $confirm_password) {
        return $this->response->setJSON(['status' => 0, 'msg' => 'New Password and Confirmation Do Not Match']);
    }

    if (!empty($new_password) && (strlen($new_password) < 6 || strlen($new_password) > 15)) {
        return $this->response->setJSON([
            'status' => 'error',
            'msg' => 'New Password must be at least 6 characters long!'
        ]);
    }

    $result = $model->change_passwordNow($us_Id, $current_password, $new_password);
    return $this->response->setJSON($result);
}



}
