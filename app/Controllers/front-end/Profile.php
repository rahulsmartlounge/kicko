<?php
namespace App\Controllers;
use App\Models\UserModel;
use App\Models\AddressProfileModel;
use App\Models\OrderModel;
use App\Models\ProfileModel;
use App\Models\ProductDisplayModel;

class Profile extends BaseController
{
    protected $productdisplayModel;
    protected $categories;

    public function __construct()
    {
        $this->session = \Config\Services::session();
        $this->input = \Config\Services::request();
    }

    public function index()
    {

        $userId = session()->get('zd_uid');

        // If not logged in and JS is bypassed    
        if (!$userId) {
            if ($this->request->isAJAX()) {
                return view('weblogin'); // For modal
            } else {
                return redirect()->to(base_url());
            }
        }

        $this->productdisplayModel = new ProductDisplayModel();
        $this->categories = $this->productdisplayModel->getAllCategoriesAndSub();

        $data['categories'] = $this->categories;
        $data['title'] = 'Profile';

        $data['product'] = $this->productdisplayModel->getAllProducts();

        $userModel = new UserModel();
        $addressModel = new AddressProfileModel();
        $orderModel = new OrderModel();

        $user = $userModel->find($userId);

        // MERGE instead of overwrite
        $data = array_merge($data, [
            'user' => $user,
            'addresses' => $addressModel->getUserAddresses($userId),
            'orders' => $orderModel->getOrdersByUser($userId),
        ]);

        $template = view('common/header', $data);
        $template .= view('profile');
        $template .= view('common/footer');
        $template .= view('pagescripts/profilejs');

        return $template;
    }

    public function editProfile()
    {
        $profileModel = new ProfileModel();

        $id = session()->get('zd_uid');
        $newName = $this->request->getPost('profilename');
        $data = [
            'cust_Name' => $newName,
            'cust_Email' => $this->request->getPost('email'),
            'cust_Phone' => $this->request->getPost('phone') ?: '',

        ];
        $updatedUser = $profileModel->updateUserProfile($id, $data);

        if ($updatedUser) {
            session()->set('zd_uname', $newName);
            return $this->response->setJSON([
                'status' => 'success',
                'msg' => 'Profile Updated Successfully.'
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'msg' => 'Failed To Update Profile.'
            ]);
        }
    }

    public function setDefaultAddress()
    {
        $userId = session()->get('zd_uid');
        $add_Id = $this->request->getPost('add_Id');

        $addressModel = new AddressProfileModel();

        if ($addressModel->setAsDefault($userId, $add_Id)) {
            return $this->response->setJSON(['status' => 'success']);
        } else {
            return $this->response->setJSON(['status' => 'error']);
        }
    }


    public function addAddress()
    {

        $data = $this->request->getPost();

        // echo $data; exit();
        $email = trim($data['newEmail'] ?? '');

        if (empty($email)) {
            return $this->response->setJSON([
                'status' => 'error',
                'msg' => 'Email is required.'
            ]);
        }

        if (!preg_match('/^[a-zA-Z0-9._%+-]+@(?:[a-zA-Z0-9-]+\.)+[a-zA-Z]{2,}$/', $email)) {
            return $this->response->setJSON([
                'status' => 'error',
                'msg' => 'Invalid email format.'
            ]);
        }
        if (isset($data['new_phcode'])) {
            $data['add_phcode'] = $data['new_phcode'];
            unset($data['new_phcode']); // optional cleanup
        }
        $addressModel = new AddressProfileModel();
        $addressModel->addAddress(session()->get('zd_uid'), $data);

        return $this->response->setJSON(['status' => 'success']);
    }



    public function editAddress()
    {
        $data = $this->request->getPost();
        $email = trim($data['add_Email'] ?? '');
        $pincode = trim($data['add_Pincode']);
        if (empty($email)) {
            return $this->response->setJSON([
                'status' => '0',
                'msg' => 'Email is required.'
            ]);
        }

        // Strict email format
        if (!preg_match('/^[a-zA-Z0-9._%+-]+@(?:[a-zA-Z0-9-]+\.)+[a-zA-Z]{2,}$/', $email)) {
            return $this->response->setJSON([
                'status' => '0',
                'msg' => 'Invalid email format.'
            ]);
        }

        if (empty($pincode)) {
            return $this->response->setJSON([
                'status' => '0',
                'msg' => 'Pincode is Required.'
            ]);
        } else if (!is_numeric($pincode) || strlen($pincode) < 4 || strlen($pincode) > 10) {
            return $this->response->setJSON([
                'status' => '0',
                'msg' => 'Enter a Valid Pincode.'
            ]);
        }

        $addressModel = new AddressProfileModel();
        $result = $addressModel->updateAddress($data);

        if ($result === true || (is_array($result) && $result['status'] === '1')) {
            // Fetch updated address details
            $addressModel = new AddressProfileModel();
            $updated = $addressModel->find($data['add_Id']); // Use your actual primary key column

            if ($updated) {
                return $this->response->setJSON([
                    'status' => '1',
                    'updated_address' => [
                        'add_Id' => $updated['add_Id'],
                        'add_Name' => $updated['add_Name'],
                        'add_BuldingNo' => $updated['add_BuldingNo'],
                        'add_Street' => $updated['add_Street'],
                        'add_Landmark' => $updated['add_Landmark'],
                        'add_City' => $updated['add_City'],
                        'add_State' => $updated['add_State'],
                        'add_Pincode' => $updated['add_Pincode'],
                        'add_Phone' => $updated['add_Phone'],
                        'add_Email' => $updated['add_Email'],
                        'add_Default' => $updated['add_Default']
                    ]
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => '0',
                    'msg' => 'Address updated but failed to fetch data.'
                ]);
            }
        }

    }



    public function deleteAddress()
    {
        $add_Id = $this->request->getPost('add_Id');
        $modified_by = session()->get('zd_uid');

        $addressModel = new AddressProfileModel();
        $addressModel->deleteAddress(3, $add_Id, $modified_by);

        return redirect()->to(base_url('profile#address'))->with('message', 'Address Deleted Successfully.');
    }

    public function getAddress()
    {
        $addId = $this->request->getPost('add_Id');
        $userId = session()->get('zd_uid');

        $addressModel = new AddressProfileModel();
        $address = $addressModel->where(['add_Id' => $addId, 'add_CustId' => $userId])->first();

        if ($address) {
            return $this->response->setJSON(['status' => 'success', 'data' => $address]);
        } else {
            return $this->response->setJSON(['status' => 'error', 'msg' => 'Address Not Found.']);
        }
    }

    public function changePassword()
    {
        $custId = session()->get('zd_uid');

        $oldPassword = $this->request->getPost('oldPassword');
        $newPassword = $this->request->getPost('newPassword');
        $confirmPassword = $this->request->getPost('confirmPassword');

        // Validation: check empty fields
        if (empty($oldPassword)) {
            return $this->response->setJSON(['status' => 0, 'msg' => 'Please Enter The Old Password.']);
        }
        if (empty($newPassword)) {
            return $this->response->setJSON(['status' => 0, 'msg' => 'Please Enter A New Password.']);
        }
        if (empty($confirmPassword)) {
            return $this->response->setJSON(['status' => 0, 'msg' => 'Please Confirm Your New Password.']);
        }

        // Check new password matches confirm password
        if ($newPassword !== $confirmPassword) {
            return $this->response->setJSON(['status' => 0, 'msg' => 'New Password And Confirm Password Do Not Match.']);
        }
        if (!empty($new_password) && (strlen($new_password) < 6 || strlen($new_password) > 15)) {
            return $this->response->setJSON([
                'status' => 'error',
                'msg' => 'Password Must Be Between 6 To 15 Characters.'
            ]);
        }

        // Call model to change password
        $profileModel = new ProfileModel();
        $result = $profileModel->changePassword($custId, $oldPassword, $newPassword);

        return $this->response->setJSON($result);
    }


}
