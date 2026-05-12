<?php

namespace App\Controllers;
use App\Models\CustomerLoginModel;


class ForgotPassword extends BaseController
{
    public function __construct()
    {
        $this->session = \Config\Services::session();
        $this->input = \Config\Services::request();
        $this->customerLoginModel = new CustomerLoginModel();
        $this->customerModel = new CustomerLoginModel();
    }


    // $email = $this->request->getGet('email');

    // if (!$email) {
    //     return redirect()->to(base_url('/'))->with('error', 'Invalid reset link.');
    // }

    // // Fetch customer by email
    // $customer = $this->customerModel
    //     ->where('cust_Email', $email)
    //     ->get()
    //     ->getRow();

    // if (!$customer || empty($customer->reset_token) || empty($customer->reset_token_expiry)) {
    //     return view('forgot_expired'); // Show expired view if invalid
    // }

    // // Check if token expired
    // $expiryTime = strtotime($customer->reset_token_expiry);
    // if ($expiryTime < time()) {
    //     return view('forgot_expired'); // Show expired message
    // }

    // // Still valid: show reset password view
    // return view('forgot_password', [
    //     'email' => $email,
    //     'token' => $customer->reset_token
    // ]);
    public function index()
{
   
}
public function checkingToken($token = null)
{
    // print_r($token);exit();
    if (!$token) {
        return view('forgot_expired');
    }

    $cust = $this->customerModel->getCustomerByValidToken($token);
    if (!$cust) {
        return view('forgot_expired');
    }

    return view('forgot_password', [
        'email' => $cust->cust_Email,
        'token' => $token
    ]);
}




 public function resetPassword()
{
    $new_password = $this->request->getPost('new_reset_password');
    $confirm_password = $this->request->getPost('confirm_reset_password');
    $token = $this->request->getPost('token');

    if (empty($new_password) || empty($confirm_password)) {
        return $this->response->setJSON([
            'status' => 0,
            'msg' => 'Both Fields are Required.'
        ]);
    }

    if (strlen($new_password) < 6 || strlen($new_password) > 15) {
        return $this->response->setJSON([
            'status' => 0,
            'msg' => 'Password must be 6-15 characters long.'
        ]);
    }

    if ($new_password !== $confirm_password) {
        return $this->response->setJSON([
            'status' => 0,
            'msg' => 'New Password does not match Confirm Password.'
        ]);
    }

    // Validate token using model
    $cust = $this->customerModel->getCustomerByValidToken($token);

    if (!$cust) {
        return $this->response->setJSON([
            'status' => 0,
            'msg' => 'This reset link has expired or is invalid. Please request a new one.'
        ]);
    }

    // Hash and update password using your model function
    $hashedPassword = md5($new_password); // You can switch to `password_hash` if needed
    $this->customerModel->resetPasswordNow($hashedPassword, $cust->cust_Email);

    return $this->response->setJSON([
        'status' => 1,
        'msg' => 'Password Updated Successfully.',
        'redirect' => base_url('/')
    ]);
}

	public function forgotPassword()
{
    $token = $this->request->getGet('token');

    if (!$token) {
        return view('forgot_expired');
    }

    $cust = $this->db->table('customer')
        ->where('reset_token', $token)
        ->where('reset_token_expiry >=', date("Y-m-d H:i:s"))
        ->get()
        ->getRow();

    if (!$cust) {
        return view('forgot_expired');
    }

    // Valid token — load reset password form
    return view('forgot_password', [
        'email' => $cust->cust_Email,
        'token' => $token
    ]);
}

}
