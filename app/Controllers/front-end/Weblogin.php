<?php

namespace App\Controllers;
use App\Models\CustomerLoginModel;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Weblogin extends BaseController
{
	public function __construct()
	{
		$this->session = \Config\Services::session();
		$this->input = \Config\Services::request();
		$this->customerLoginModel = new CustomerLoginModel();
	}

	public function index()
	{
		if ($this->session->has('zd_uid')) {
			return redirect()->to(base_url('dashboard'));
		}
	}
	public function customerAuthen()
	{
		$email = $this->request->getPost('login_email');
		$password = md5($this->request->getPost('login_password'));
		$recaptcha = $this->request->getPost('g-recaptcha-response');

		if (!$recaptcha) {
			echo json_encode([
				'status' => 0,
				'msg' => 'Please complete the reCAPTCHA.'
			]);
			return;
		}

		// Verify reCAPTCHA v2
		$secretKey = '6Le-VXcrAAAAAKSXShzC3A8GxolszKELxQ1S-9q9';
		$verifyURL = 'https://www.google.com/recaptcha/api/siteverify';

		$response = file_get_contents($verifyURL . '?secret=' . $secretKey . '&response=' . $recaptcha);
		$responseData = json_decode($response);

		if (!$responseData->success) {
			echo json_encode([
				'status' => 0,
				'msg' => 'reCAPTCHA verification failed.'
			]);
			return;
		}

if ($email && $password) {
    $userLog = $this->customerLoginModel->getLoginAccount($email, $password);

    if ($userLog) {
        if ($userLog->cust_Status == 1) {
            // Active user — log in
            $this->session->set([
                'zd_uid' => $userLog->cust_Id,
                'zd_uname' => $userLog->cust_Name,
                'role' => 'user',
            ]);
            echo json_encode([
                "status" => 1,
                "msg" => null
            ]);
        } else {
            // Inactive account
            echo json_encode([
                "status" => 0,
                "msg" => "Account Inactive — Reach out to Support for Help."
            ]);
        }
    } else {
        echo json_encode([
            "status" => 0,
            "msg" => "Invalid Username or Password. Please Try Again!"
        ]);
    }
} else {
    echo json_encode([
        "status" => 0,
        "msg" => "Login Credentials are Mandatory"
    ]);
}

	}

	public function webForgotEmailSend()
	{
		date_default_timezone_set('Asia/Kolkata');
		//$forgotCustEmail = $this->request->getPost("forgotCustEmail");
		$forgotCustEmail = strtolower(trim($this->request->getPost("forgotCustEmail")));

		if (!$forgotCustEmail || !filter_var($forgotCustEmail, FILTER_VALIDATE_EMAIL)) {
			return $this->response->setJSON([
				"status" => 0,
				"msg" => "Enter a valid Email Address."
			]);
		}

		$emailExist = $this->customerLoginModel->getEmailExist($forgotCustEmail);

		if (!$emailExist) {
			return $this->response->setJSON([
				"status" => 0,
				"msg" => "Email Doesn't Exist."
			]);
		}

		$token = bin2hex(random_bytes(32));
		$expiry = date("Y-m-d H:i:s", strtotime("+2 hours"));
		//$expiry = date("Y-m-d H:i:s", strtotime("+5 minutes"));

		$custId = $emailExist['cust_Id'];

		// Save token and expiry to `customer` table
		$updated = $this->customerLoginModel->updateResetToken($custId, $token, $expiry);

		if (!$updated) {
			return $this->response->setJSON([
				"status" => 0,
				"msg" => "Unable to save reset token. Please try again later."
			]);
		}

		$name = $emailExist['cust_Name'];
		$logoUrl = base_url(ASSET_PATH . 'assets/images/logo.jpg');
		$resetLink = base_url('forgotPassword/check/' . $token);
		require 'vendors/src/Exception.php';
		require 'vendors/src/PHPMailer.php';
		require 'vendors/src/SMTP.php';

		$mail = new PHPMailer(true);

		try {
			$mail->isSMTP();
			$mail->Host = 'smtp.gmail.com';
			$mail->SMTPAuth = true;
			$mail->Username = 'smartloungework@gmail.com';
			$mail->Password = 'peetkiqeqbgxaxqs';
			$mail->SMTPSecure = 'tls';
			$mail->Port = 587;

			$mail->setFrom('smartloungework@gmail.com', 'Smart Lounge');
			$mail->addAddress($forgotCustEmail, $name);
			$mail->addReplyTo('smartloungework@gmail.com', 'Smart Lounge');

			$mail->isHTML(true);
			$mail->Subject = 'Password Reset Link - Zakhi Designs';
			$mail->Body = "
<div style='font-family: Arial, sans-serif; padding: 20px;'>
  <div style='text-align: center;'>
    <img src='{$logoUrl}' alt='Zakhi Designs Logo' style='height: 60px;'>
    <h2 style='margin-top: 20px;'>Forgot Password</h2>
  </div>

  <p style='font-size: 16px; color: #333;'>
    Hi {$name},
  </p>

  <p style='font-size: 16px; color: #333;'>
    We received a request to reset your password for your <strong>Zakhi Designs</strong> account.
  </p>

  <p style='font-size: 16px; color: #333;'>
    If you made this request, please click the button below to set a new password.
  </p>

  <p style='margin-top: 20px;'>
    <a href='{$resetLink}' style='padding: 12px 24px; background-color: #d81b60; color: white; text-decoration: none; border-radius: 5px; font-size: 14px;'>Reset Password</a>
  </p>

  <p style='font-size: 16px; color: #333; margin-top: 30px;'>
    This link will expire in 2 hours for security reasons. If you did not request a password reset, you can safely ignore this email.
  </p>

  <p style='font-size: 14px; color: #555; margin-top: 40px;'>
    For any queries, reach us at <a href='mailto:zakhidesigns@gmail.com' style='color: #d81b60;'>zakhidesigns@gmail.com</a>
  </p>

  <p style='margin-top: 30px;'>
    <a href='https://v4cstaging.co.in/zakhidesigns/' style='padding: 10px 20px; background-color: #d81b60; color: white; text-decoration: none; border-radius: 5px;'>Visit Our Website</a>
  </p>
</div>
";



			$mail->AltBody = "Dear $name,\n\nPlease follow the link to reset your password: $resetLink\n\n";
			$mail->send();

			return $this->response->setJSON([
				'status' => 1,
				'msg' => 'A Reset Link Has Been Sent To Your Email Address.'
			]);
		} catch (Exception $e) {
			return $this->response->setJSON([
				'status' => 0,
				'msg' => 'Mail could not be sent. Mailer Error: ' . $mail->ErrorInfo
			]);
		}
	}

	public function termsandconditions()
	{
		return view('terms_and_conditions');
	}

	public function privacypolicy()
	{
		return view('privacy_policy');
	}

	public function logout()
	{
		$this->session->destroy();

		return redirect()->to(base_url('/'));
	}
	public function checkLoginStatus()
	{
		$isLoggedIn = session()->has('cust_Id'); // adjust session key to yours
		return $this->response->setJSON(['loggedIn' => $isLoggedIn]);
	}
	public function createnew()
	{


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

		$customerModel = new CustomerLoginModel();
		if ($custname && $custemail && $password) {
			if (empty($cust_id)) {
				// INSERT
				// Check if email already exists
				if ($customerModel->getCustomerByEmail($custemail)) {
					return $this->response->setJSON(['status' => 'error', 'msg' => 'User Email Already Exists. Please Login To Continue.']);
				}
				$data = [
					'cust_Name' => $custname,
					'cust_Email' => $custemail,
					'cust_Password' => md5($password),
					'cust_Status' => 1,
					'cust_createdon' => date("Y-m-d H:i:s"),
					'cust_createdby' => $this->session->get('ad_uid'),
					'cust_modifyby' => $this->session->get('ad_uid'),
				];
				$customerModel->createcust($data);
				echo json_encode(array(
					"status" => 1,
					"msg" => "Account Created Successfully. Please Login To Your Account To Start Shopping.",
					"redirect" => base_url('/')
				));

			}
		} else {
			return $this->response->setJSON([
				'status' => 'error',
				'msg' => 'All Mandatory Fields Are Required.'
			]);
		}

	}
	private function reCaptcha($recaptcha)
	{
		$secretKey = '6Le-VXcrAAAAAKSXShzC3A8GxolszKELxQ1S-9q9';
		$url = 'https://www.google.com/recaptcha/api/siteverify';
		$response = file_get_contents($url . '?secret=' . $secretKey . '&response=' . $recaptcha);
		$result = json_decode($response, true);

		return $result['success'] ?? false;
	}
}