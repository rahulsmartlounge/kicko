<?php
namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Models\Admin\AddressModel;

class Customer_address extends BaseController
{
    public function __construct()
    {
        $this->session = \Config\Services::session();
        $this->input = \Config\Services::request();
        $this->addressModel = new \App\Models\Admin\AddressModel();
    }

    public function location($cust_id)
    { 
		 if (!$this->session->get('ad_uid')) {
				return redirect()->to(base_url('admin'));
			}  
			
        $customer = $this->addressModel->getAllCustomer($cust_id);
        $data['user'] = $customer;
        $data['add_CustId'] = $cust_id;
        $template = view('Admin/common/header');
		$template.= view('Admin/common/leftmenu');
		$template.= view('Admin/customer_address', $data);
		$template.= view('Admin/common/footer');
		$template.= view('Admin/page_scripts/addressjs');
        return $template;
        
    }
	public function view_address($cust_id, $add_id = null)
	{

		if (!$this->session->get('ad_uid')) {
			return redirect()->to(base_url('admin/customer_address'));
		}

		$data = [
			'add_CustId' => $cust_id,
			'add_id' => $add_id,
		];
		
		// If editing an address (i.e., $add_id is provided)
		
		if ($add_id) {
			
			$address = $this->addressModel->getCustomerByid($add_id);
			if (!$address) {
				return redirect()->to('admin/customer_address')->with('error', 'Customer address not found');
			}
			$data['address'] = (array) $address;
			// Load views
			$template  = view('Admin/common/header');
			$template .= view('Admin/common/leftmenu');
			$template .= view('Admin/customer_address_add', $data);
			$template .= view('Admin/common/footer');
			$template .= view('Admin/page_scripts/addressjs');
			return $template;
		}
		else
		{
			
			$template  = view('Admin/common/header');
			$template .= view('Admin/common/leftmenu');
			$template .= view('Admin/customer_address_add',$data);
			$template .= view('Admin/common/footer');
			$template .= view('Admin/page_scripts/addressjs');
			return $template;
		}
	}
	/*************************************/
	 public function createnew() {
		$cust_id 	= 	$this->input->getPost('cust_id');
		$add_id 	= 	$this->input->getPost('add_id');
		$custname 	= 	$this->input->getPost('custname');
		$mobile 	= 	$this->input->getPost('mobile');
		$hname		=	$this->input->getPost('hname');
		$street		=	$this->input->getPost('street');
		$landmark	=	$this->input->getPost('landmark');
		$city		=	$this->input->getPost('city');
		$pincode	=	$this->input->getPost('pincode');
		$state		=	$this->input->getPost('state');
		// Validate name
		if (!preg_match('/^[a-zA-Z ]+$/', $custname)) {
			return $this->response->setJSON(['status' => 'error', 'msg' => 'Please Enter Name Correctly.']);
		}
		if (empty($mobile)) {
			return $this->response->setJSON(['status' => 'error', 'msg' => 'Phone Number is Required.']);
		} elseif (!preg_match('/^[0-9+\s\-()]{7,25}$/', $mobile)) {
			return $this->response->setJSON(['status' => 'error', 'msg' => 'Phone Number is Invalid.']);
		}
		// Validate name
		if (!preg_match('/^[a-zA-Z0-9.,\/\-_ ]+$/', $hname)) {
			return $this->response->setJSON(['status' => 'error', 'msg' => 'Please Enter Housename/Building No. Correctly.']);
		}
		if (!preg_match('/^[a-zA-Z0-9.,\/\-_ ]+$/', $street)) {
			return $this->response->setJSON(['status' => 'error', 'msg' => 'Please Enter Street Name Correctly.']);
		}
		if (!preg_match('/^[a-zA-Z0-9.,\/\-_ ]+$/', $landmark)) {
			return $this->response->setJSON(['status' => 'error', 'msg' => 'Please Enter Landmark Correctly.']);
		}
		if (!preg_match('/^[a-zA-Z.,\/\-_ ]+$/', $city)) {
			return $this->response->setJSON(['status' => 'error', 'msg' => 'Please Enter City Correctly.']);
		}
		if (!preg_match('/^[a-zA-Z.,\/\-_ ]+$/', $state)) {
			return $this->response->setJSON(['status' => 'error', 'msg' => 'Please Enter State Correctly.']);
		}
		// Validate pincode
		if (empty($pincode)) {
			return $this->response->setJSON(['status' => 'error', 'msg' => 'Pincode is Required.']);
		} elseif (!ctype_digit($pincode) || strlen($pincode) > 10 || strlen($pincode) < 4) {
			return $this->response->setJSON(['status' => 'error', 'msg' => 'Please Enter a Valid Pincode.']);
		}
		
				$addressModel = new AddressModel();
				if($custname && $mobile && $hname && $street && $city && $pincode && $state){
					if (empty($add_id) && !empty($cust_id)){
						$data = [
						'add_Name'          	=> $custname,
						'add_BuldingNo'         => $hname,
						'add_Landmark'	    	=> $landmark,
						'add_Street'     		=> $street,
						'add_City'				=> $city,
						'add_State'				=> $state,
						'add_Pincode'			=> $pincode,
						'add_Phone'				=> $mobile,
						'add_CustId'			=> $cust_id,
						'add_Status'	   		=> 1,
						'add_createdon'    		=> date("Y-m-d H:i:s"),
						'add_createdby'    		=> $this->session->get('ad_uid'),
						'add_modifyby'     		=> $this->session->get('ad_uid'),
					];
						$Createaddress = $this->addressModel->createcust($data);
						//echo json_encode(array("status" => 1, "msg" => "Customer address Created successfully."));
						echo json_encode(array(
							"status" => 1,
							"msg" => "Customer address created successfully.",
							'redirect' => base_url('admin/customer/location/' .$cust_id)
							
						));
					}
					else {
					$data = [
						'add_Id'				=> $add_id,
						'add_CustId'			=> $cust_id,
						'add_Name'          	=> $custname,
						'add_BuldingNo'         => $hname,
						'add_Landmark'	    	=> $landmark,
						'add_Street'     		=> $street,
						'add_City'				=> $city,
						'add_State'				=> $state,
						'add_Pincode'			=> $pincode,
						'add_Phone'				=> $mobile,
						'add_CustId'			=> $cust_id,
						'add_Status'	   		=> 1,
						'add_createdon'    		=> date("Y-m-d H:i:s"),
						'add_createdby'    		=> $this->session->get('ad_uid'),
						'add_modifyby'     		=> $this->session->get('ad_uid'),
					];
				$modifyaddress = $this->addressModel->modifyaddress($add_id,$data);
				//echo json_encode(array("status" => 1, "msg" => "Customer address details updated successfully."));	
				echo json_encode(array(
					"status" => 1,
					"msg" => "Customer address details updated successfully.",
					"redirect" => base_url('admin/customer/location/' .$cust_id)
				));
			}
				}
				else
				{
					echo json_encode(array(
					"status" => 1,
					"msg" => "All mandatory fields are required.",
					//"redirect" => base_url('customer/location/' .$cust_id)
				));
			}
				
			} 
		/***************************************/
		 public function deleteAddress($add_id) {
		if ($add_id) {
			$modified_by = $this->session->get('ad_uid');
			$add_status = $this->addressModel->deleteCustById(3, $add_id, $modified_by);

			if ($add_status) {
				echo json_encode([
					'success' => true,
					'msg' => 'Address deleted successfully.'
				]);
			} else {
				echo json_encode([
					'success' => false,
					'msg' => 'Failed to delete address.'
				]);
			}
		} else {
			echo json_encode([
				'success' => false,
				'msg' => 'Invalid request.'
			]);
		}
	}
		/***************************************/
	}
	


