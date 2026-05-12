<?php
namespace App\Controllers\Admin;
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
    // public function login()
    // {
    //     echo  $email = $this->request->getPost('email');
	// 	echo $password = $this->request->getPost('password');
    //     exit();
    // }

public function authenticate()
{
    $email = $this->request->getPost('email');
    $password = md5($this->request->getPost('password'));
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

    // Authenticate user
    if ($email && $password) {
        $userLog = $this->usModel->getLoginAccount($email, $password);
        if ($userLog) {
            // echo "enter";exit();
            $this->session->set([
                'ad_uid' => $userLog->us_Id,
                'ad_uname' => $userLog->us_Name,
                'role' => 'admin',
            ]);

            switch ($userLog->us_Status) {
                case '1':
                    echo json_encode(['status' => 1, 'msg' => null]);
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
	private function reCaptcha($recaptcha)
    {
        $secretKey = '6Le-VXcrAAAAAKSXShzC3A8GxolszKELxQ1S-9q9';
        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $response = file_get_contents($url . '?secret=' . $secretKey . '&response=' . $recaptcha);
        $result = json_decode($response, true);

        return $result['success'] ?? false;
    }
    
	public function logout()
	{
		$session = session();
		$session->remove(['ad_uid', 'ad_uname']); 
		return redirect()->to(base_url('admin'));
	}
}
?>