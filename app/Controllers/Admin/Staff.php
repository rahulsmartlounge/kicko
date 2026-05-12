<?php
namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Models\Admin\StaffModel;

class Staff extends BaseController
{

	public function __construct()
	{
		$this->session = \Config\Services::session();
		$this->input = \Config\Services::request();
		$this->staffModel = new \App\Models\Admin\StaffModel();
	}

	public function index()
	{
		if (!$this->session->get('ad_uid')) {
			return redirect()->to(base_url('admin'));
		}
		//$getall['users'] = $this->staffModel->getAllStaff();
		$staff = $this->staffModel->getAllStaff();
		$data['user'] = $staff;
		$template = view('Admin/common/header');
		$template .= view('Admin/common/leftmenu');
		$template .= view('Admin/staff', $data);
		$template .= view('Admin/common/footer');
		$template .= view('Admin/page_scripts/staffjs');
		return $template;


	}
	public function addStaff($us_id = null)
	{
		if (!$this->session->get('ad_uid')) {
			return redirect()->to(base_url('admin'));
		}

		$data = [];
		if ($us_id) {
			$staff_val = $this->staffModel->getStaffByid($us_id);

			if (!$staff_val) {
				return redirect()->to('admin/staff')->with('error', 'Staff member not found');
			}
			// $data['staff'] = $staff_val;
			$data['staff'] = (array) $staff_val;

			// Load views
			$template = view('Admin/common/header');
			$template .= view('Admin/common/leftmenu');
			$template .= view('Admin/staff_add', $data);
			$template .= view('Admin/common/footer');
			$template .= view('Admin/page_scripts/staffjs');
			return $template;
		} else {
			// Load views
			$template = view('Admin/common/header');
			$template .= view('Admin/common/leftmenu');
			$template .= view('Admin/staff_add');
			$template .= view('Admin/common/footer');
			$template .= view('Admin/page_scripts/staffjs');
			return $template;
		}

	}
	public function createnew()
	{
		$us_id = $this->input->getPost('us_id');
		$staffname = $this->input->getPost('staffname');
		// $staffname 		= ucwords(strtolower(trim($staffname)));
		$staffemail = $this->input->getPost('staffemail');
		$staffotemail = $this->input->getPost('staffotemail');
		$mobile = $this->input->getPost('mobile');
		$password = $this->input->getPost('password');
		$oldpass = $this->input->getPost('old_password');
		$newPass = $this->input->getPost('new_password');

		// Validate name
		if (!preg_match('/^[a-zA-Z ]+$/', $staffname)) {
			return $this->response->setJSON(['status' => 'error', 'msg' => 'Please Enter Name Correctly.']);
		}

		// Validate emails 1
		if (!filter_var($staffemail, FILTER_VALIDATE_EMAIL)) {
			return $this->response->setJSON(['status' => 'error', 'msg' => 'Please Enter A Valid Primary Email.']);
		}
		// Validate emails 2 
		if (!filter_var($staffotemail, FILTER_VALIDATE_EMAIL)) {
			return $this->response->setJSON(['status' => 'error', 'msg' => 'Please Enter The Order Confirmation Email.']);
		}
		// Validate mobile
		if (!empty($mobile) && ctype_digit($mobile) && strlen($mobile) < 7) {
			return $this->response->setJSON(['status' => 'error', 'msg' => 'Phone Number Must Contain Minimum 7 Digits.']);
		}
		//validate password length

		// if (!empty($oldpass) && (strlen($oldpass) < 6 || strlen($oldpass) > 20)) {
		// 	return $this->response->setJSON([
		// 		'status' => 'error',
		// 		'msg' => 'Password must be between 6 to 20 characters.'
		// 	]);
		// }
		if (!empty($newPass) && (strlen($newPass) < 6 || strlen($newPass) > 20)) {
			return $this->response->setJSON([
				'status' => 'error',
				'msg' => 'Password Must be Between 6 to 20 Characters.'
			]);
		}


		$staffModel = new StaffModel();
		// INSERT
		if (empty($us_id)) {
			// Check if email already exists
			if ($staffModel->getStaffByEmail($staffemail)) {
				return $this->response->setJSON(['status' => 'error', 'msg' => 'Email already exists.']);
			}
			if (empty($password)) {
				return $this->response->setJSON(['status' => 'error', 'msg' => 'Password Field Cannot Be Left Empty. Kindly a Valid Password.']);
			} else if (!empty($password) && (strlen($password) < 6 || strlen($password) > 20)) {
				return $this->response->setJSON([
					'status' => 'error',
					'msg' => 'Password Must be Between 6 to 20 Characters.'
				]);
			}


			$data = [
				'us_Name' => $staffname,
				'us_Email' => $staffemail,
				'us_Email2' => $staffotemail,
				'us_Phone' => $mobile,
				'us_Status' => 1,
				'us_Role' => 2,
				'us_Password' => md5($password),
				'us_createdon' => date("Y-m-d H:i:s"),
				'us_createdby' => $this->session->get('ad_uid'),
				'us_modifyby' => $this->session->get('ad_uid'),
			];

			$staffModel->createStaff($data);
			return $this->response->setJSON(['status' => 1, 'msg' => 'Staff Created Successfully.', 'redirect' => base_url('staff')]);
		}

		// UPDATE
		$existing = $staffModel->getStaffById($us_id);
		if (empty($oldpass) && empty($newPass)) {
			$newPassword = $existing->us_Password;
		} else {

			if (!empty($oldpass) && $existing->us_Password !== md5($oldpass)) {
				return $this->response->setJSON([
					'status' => 'error',
					'msg' => 'Old Password is incorrect.'
				]);
			} else {

				if (empty($newPass) && md5($newPass) !== md5($oldpass) || md5($newPass) === md5($oldpass)) {
					return $this->response->setJSON([
						'status' => 'error',
						'msg' => 'Please check your new password.'
					]);
				} else {
					$newPassword = md5($newPass);
				}
			}
		}
		if (!$existing) {
			return $this->response->setJSON(['status' => 'error', 'msg' => 'Staff not found.']);
		}

		// Check if email changed and already exists for another user
		if ($staffemail !== $existing->us_Email && $staffModel->emailExistsExcept($staffemail, $us_id)) {
			return $this->response->setJSON(['status' => 'error', 'msg' => 'Email already exists.']);
		}
		// Use old password if input is empty, otherwise hash new password
		$data = [
			'us_Name' => $staffname,
			'us_Email' => $staffemail,
			'us_Email2' => $staffotemail,
			'us_Phone' => $mobile,
			'us_Status' => 1,
			'us_Role' => 2,
			'us_Password' => $newPassword,
			'us_modifyby' => $this->session->get('ad_uid'),
		];

		$staffModel->modifyStaff($us_id, $data);
		return $this->response->setJSON(['status' => 1, 'msg' => 'Staff Updated successfully.', 'redirect' => base_url('staff')]);
	}
	public function deleteStaff($us_id)
	{
		if ($us_id) {
			$modified_by = $this->session->get('ad_uid');
			$us_status = $this->staffModel->deleteStaffById(3, $us_id, $modified_by);

			if ($us_status) {
				echo json_encode([
					'success' => true,
					'msg' => 'Staff deleted successfully.'
				]);
			} else {
				echo json_encode([
					'success' => false,
					'msg' => 'Failed to delete staff.'
				]);
			}
		} else {
			echo json_encode([
				'success' => false,
				'msg' => 'Invalid request.'
			]);
		}
	}
	public function updateStatus()
	{

		$us_Id = $this->request->getPost('us_Id');
		$newStatus = $this->request->getPost('us_Status');
		$staffModel = new StaffModel();
		$staff = $staffModel->getStaffByid($us_Id);

		if (!$staff) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'Customer not found'
			]);
		}
		$update = $staffModel->updateStaff($us_Id, ['us_Status' => $newStatus]);
		if ($update) {
			return $this->response->setJSON([
				'success' => true,
				'message' => 'Status Updated Successfully!',
				'new_status' => $newStatus
			]);
		} else {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'Failed to update status'
			]);
		}
	}
	// Listing table data
	public function ajaxList()
	{
		$model = new \App\Models\Admin\StaffModel();
		$data = $model->getDatatables();
		$total = $model->countAll();
		$filtered = $model->countFiltered();

		foreach ($data as &$row) {
			// Default fallbacks
			$row['us_Name'] = $row['us_Name'] ?? 'N/A';
			$row['us_Email'] = $row['us_Email'] ?? 'N/A';
			$row['us_Phone'] = $row['us_Phone'] ?? 'N/A';
			$row['us_utype'] = $row['us_utype'] ?? 'N/A';


			// Status toggle switch
			$row['status_switch'] = '<div class="form-check form-switch">
			<input class="form-check-input checkactive"
				   type="checkbox"
				   id="statusSwitch-' . $row['us_Id'] . '"
				   value="' . $row['us_Id'] . '" ' . ($row['us_Status'] == 1 ? 'checked' : '') . '>
			<label class="form-check-label pl-0 label-check"
				   for="statusSwitch-' . $row['us_Id'] . '"></label>
		</div>';

			// Action buttons
			$row['actions'] = '<a href="' . base_url('admin/staff/add/' . $row['us_Id']) . '">
				<i class="bi bi-pencil-square"></i>
			</a>&nbsp;
			<i class="bi bi-trash text-danger icon-clickable"
			   onclick="confirmDelete(' . $row['us_Id'] . ')"></i>';
		}

		return $this->response->setJSON([
			'draw' => intval($this->request->getPost('draw')),
			'recordsTotal' => $total,
			'recordsFiltered' => $filtered,
			'data' => $data
		]);
	}


}


