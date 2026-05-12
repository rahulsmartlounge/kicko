<?php
namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Models\Admin\CustomerModel;
class Customer extends BaseController
{
	public function __construct()
	{
		$this->session = \Config\Services::session();
		$this->input = \Config\Services::request();
		$this->customerModel = new \App\Models\Admin\CustomerModel();
	}

	public function index()
	{
		if (!$this->session->get('ad_uid')) {
			return redirect()->to(base_url('admin'));
		}
		//$getall['users'] = $this->staffModel->getAllStaff();
		$customer = $this->customerModel->getAllCustomer();
		$data['user'] = $customer;
		$template = view('Admin/common/header');
		$template .= view('Admin/common/leftmenu');
		$template .= view('Admin/customers', $data);
		$template .= view('Admin/common/footer');
		$template .= view('Admin/page_scripts/customerjs');
		return $template;
	}
	public function view_cust($cust_Id = null)
	{

		if (!$this->session->get('ad_uid')) {
			return redirect()->to(base_url('admin'));
		}
		$data = [];
		if ($cust_Id) {
			$cust_val = $this->customerModel->findCustomerById($cust_Id);

			if (!$cust_val) {
				return redirect()->to('admin/customer')->with('error', 'Staff Member Not Found');
			}
			// $data['cust'] = $cust_val;
			$data['cust'] = (array) $cust_val;
			$template = view('Admin/common/header');
			$template .= view('Admin/common/leftmenu');
			$template .= view('Admin/customer_view', $data);
			$template .= view('Admin/common/footer');
			$template .= view('Admin/page_scripts/customerjs');
			return $template;
		} else {
			// Load views
			$template = view('Admin/common/header');
			$template .= view('Admin/common/leftmenu');
			$template .= view('Admin/customer_view');
			$template .= view('Admin/common/footer');
			$template .= view('Admin/page_scripts/customerjs');
			return $template;

		}
	}
	public function createnew()
	{


		$cust_id = $this->input->getPost('cust_id');
		// $custname = ;
		$custname = ucwords(strtolower(trim($this->input->getPost('custname'))));
		// $cust_phcode = $this->input->getPost('cust_phcode'); 
		$custemail = $this->input->getPost('custemail');
		$mobile = $this->input->getPost('mobile');
		$password = $this->input->getPost('userpassword');
		if (!preg_match('/^[a-zA-Z ]+$/', $custname)) {
			return $this->response->setJSON(['status' => 'error', 'msg' => 'Please Enter Name Correctly.']);
		}

		// Validate email formats
		if (!filter_var($custemail, FILTER_VALIDATE_EMAIL)) {
			return $this->response->setJSON([
				'status' => 'error',
				'msg' => 'Invalid Email Format.'
			]);
		}

		if (!preg_match('/^[0-9+\s\-()]{7,25}$/', $mobile)) {
			return $this->response->setJSON([
				'status' => 0,
				'msg' => 'Phone Number is Invalid'
			]);
		}



		$customerModel = new \App\Models\Admin\CustomerModel();
		if ($custname && $custemail) {
			if (empty($cust_id)) {
				//validate password length

				if (!empty($password) && (strlen($password) < 6 || strlen($password) > 15)) {
					return $this->response->setJSON([
						'status' => 'error',
						'msg' => 'Password Must Be Between 6 to 15 Characters.'
					]);
				}
				if (empty($password)) {
					return $this->response->setJSON([
						'status' => 'error',
						'msg' => 'Please enter a password.'
					]);
				}
				// INSERT
				// Check if email already exists
				if ($customerModel->getCustomerByEmail($custemail)) {
					return $this->response->setJSON(['status' => 'error', 'msg' => 'User Email Already Exists. Please Login To Continue.']);
				}
				$data = [
					'cust_Name' => $custname,
					'cust_Email' => $custemail,
					'cust_Phone' => $mobile,
					'cust_Password' => md5($password),
					'cust_Status' => 1,
					'cust_createdon' => date("Y-m-d H:i:s"),
					'cust_createdby' => $this->session->get('ad_uid'),
					'cust_modifyby' => $this->session->get('ad_uid'),
				];
				$CreateCust = $this->customerModel->createcust($data);
					if ($CreateCust) {
						echo json_encode(array(
						"status" => 1,
						"msg" => "Account Created Successfully.",
						"redirect" => base_url('customer')
					));
				}	

			} else {
				// UPDATE
				$existing = $customerModel->getCustomerById($cust_id);
				// Check if email changed and already exists for another user
				if ($custemail !== $existing->cust_Email && $customerModel->emailExistsExcept($custemail, $cust_id)) {
					return $this->response->setJSON(['status' => 'error', 'msg' => 'Email Already Exists.']);
				}
				// Compare hashed passwords (check if the passwords match)
				$data = [
					'cust_Name' => $custname,
					'cust_Email' => $custemail,
					'cust_Phone'	     => $mobile,
					'cust_createdon' => date("Y-m-d H:i:s"),
					'cust_createdby' => $this->session->get('ad_uid'),
					'cust_modifyby' => $this->session->get('ad_uid'),
				];

				$modifycust = $this->customerModel->modifycust($cust_id, $data);
					if($modifycust){
						echo json_encode(array(
						"status" => 1,
						"msg" => "Customer Details Updated Successfully.",
						"redirect" => base_url('customer')
					));
				}
			}
		} 
		else {
			return $this->response->setJSON([
				'status' => 'error',
				'msg' => 'All Mandatory Fields Are Required.'
			]);
		}

	}
	public function deleteCust($cust_id)
	{
		if ($cust_id) {
			$modified_by = $this->session->get('ad_uid');
			$us_status = $this->customerModel->deleteCustById(3, $cust_id, $modified_by);

			if ($us_status) {
				echo json_encode([
					'success' => true,
					'msg' => 'Customer Deleted Successfully.'
				]);
			} else {
				echo json_encode([
					'success' => false,
					'msg' => 'Failed To Delete Customer.'
				]);
			}
		} else {
			echo json_encode([
				'success' => false,
				'msg' => 'Invalid Request.'
			]);
		}
	}
	public function updateStatus()
	{

		$custId = $this->request->getPost('cust_Id');
		$newStatus = $this->request->getPost('cust_Status');

		$customerModel = new \App\Models\Admin\CustomerModel();
		$customer = $customerModel->getCustomerByid($custId);

		if (!$customer) {
			return $this->response->setJSON([
				'success' => 0,
				'message' => 'Customer Not Found'
			]);
		}
		$update = $customerModel->updateCustomer($custId, ['cust_Status' => $newStatus]);
		if ($update) {
			return $this->response->setJSON([
				'status' => 1,
				'message' => 'Customer Status Updated Successfully.'
			]);

		} else {
			return $this->response->setJSON([
				'success' => 0,
				'message' => 'Failed To Update Status'
			]);
		}
	}

	// Listing table data

	public function ajaxList()
	{
		$request = \Config\Services::request();
		$model = new \App\Models\Admin\CustomerModel();

		$data = $model->getDatatables();
		$total = $model->countAll();
		$filtered = $model->countFiltered();

		foreach ($data as &$row) {
			$row['cust_Name'] = $row['cust_Name'] ?? 'N/A';
			$row['cust_Email'] = $row['cust_Email'] ?? 'N/A';
			$row['cust_Phone'] = $row['cust_Phone'] ?? 'N/A';

			$row['status_switch'] = '<div class="form-check form-switch">
            <input class="form-check-input checkactive"
                   type="checkbox"
                   id="statusSwitch-' . $row['cust_Id'] . '"
                   value="' . $row['cust_Id'] . '" ' . ($row['cust_Status'] == 1 ? 'checked' : '') . '>
            <label class="form-check-label pl-0 label-check"
                   for="statusSwitch-' . $row['cust_Id'] . '"></label>
        </div>';

			$row['actions'] = '<a href="' . base_url('admin/customer/location/' . $row['cust_Id']) . '">
            <i class="bi bi-geo-alt text-primary ms-2"></i>
        </a>&nbsp;';
			$row['actions'] .= '<a href="' . base_url('admin/customer/view/' . $row['cust_Id']) . '">
            <i class="bi bi-pencil-square"></i>
        </a>&nbsp;';
			$row['actions'] .= '<i class="bi bi-trash text-danger icon-clickable" 
            onclick="confirmDelete(' . $row['cust_Id'] . ')"></i>';
		}

		return $this->response->setJSON([
			'draw' => intval($request->getPost('draw')),
			'recordsTotal' => $total,
			'recordsFiltered' => $filtered,
			'data' => $data
		]);
	}
}