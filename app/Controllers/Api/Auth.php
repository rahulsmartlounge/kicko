<?php
namespace App\Controllers\Api;
use App\Controllers\BaseController;
use App\Models\Admin\UsModel;

class Auth extends BaseController
{
	protected $session;
	protected $input;
	protected $usModel;

	public function __construct()
	{
		$this->session = \Config\Services::session();
		$this->input   = \Config\Services::request();
		$this->usModel = new \App\Models\Admin\UsModel();
	}
	/**
	 * Read a field from JSON body first, fall back to POST form-data.
	 */
	private function input(string $field): mixed
	{
		$contentType = $this->request->getHeaderLine('Content-Type');
		if (str_contains($contentType, 'application/json')) {
			try {
				$json = $this->request->getJSON(true);
				if (is_array($json) && array_key_exists($field, $json)) {
					return $json[$field];
				}
			} catch (\Throwable $e) {
				// malformed JSON — fall through to getPost
			}
		}
		return $this->request->getPost($field);
	}

	public function appauth(): \CodeIgniter\HTTP\ResponseInterface
	{
		$email    = trim((string)($this->input('email')    ?? ''));
		$rawPass  = (string)($this->input('password') ?? '');
		$password = md5($rawPass);

		if (!$email || !$rawPass) {
			return $this->response->setStatusCode(400)->setJSON([
				'status' => 0, 'msg' => 'Email and password are mandatory',
			]);
		}

		$userLog = $this->usModel->getLoginAccount($email, $password);
		if (!$userLog) {
			return $this->response->setStatusCode(401)->setJSON([
				'status' => 0, 'msg' => 'Invalid credentials',
			]);
		}

		switch ((string)$userLog->us_Status) {
			case '1':
				return $this->response->setStatusCode(200)->setJSON([
					'status' => 1, 'msg' => 'Login successful', 'uid' => $userLog->us_Id,
				]);
			case '2':
				return $this->response->setStatusCode(403)->setJSON([
					'status' => 0, 'msg' => 'Staff access restricted. Contact admin.',
				]);
			default:
				return $this->response->setStatusCode(403)->setJSON([
					'status' => 0, 'msg' => 'No such staff member exists.',
				]);
		}
	}

	public function registerUser(): \CodeIgniter\HTTP\ResponseInterface
	{
		$email    = trim((string)($this->input('email')    ?? ''));
		$password = (string)($this->input('password') ?? '');
		$name     = trim((string)($this->input('name')     ?? ''));
		$phone    = trim((string)($this->input('phone')    ?? ''));
		$cntycode = trim((string)($this->input('cntycode') ?? ''));
		$utype    = $this->input('utype');
		$gst      = $this->input('gst');
		$company  = $this->input('company');

		if (!$email || !$password || !$name || !$phone) {
			return $this->response->setStatusCode(400)->setJSON([
				'status' => 0, 'msg' => 'Email, password, name and phone are required',
			]);
		}

		if ($this->usModel->checkEmail($email)) {
			return $this->response->setStatusCode(409)->setJSON([
				'status' => 0, 'msg' => 'Email already exists',
			]);
		}

		$this->usModel->createUser([
			'us_utype'    => $utype,
			'us_Name'     => $name,
			'us_Email'    => $email,
			'us_Password' => md5($password),
			'us_Phone'    => $cntycode . $phone,
			'us_Gst'      => $gst,
			'us_Company'  => $company,
			'us_Status'   => 1,
			'us_Role'     => 3,
		]);

		return $this->response->setStatusCode(201)->setJSON([
			'status' => 1, 'msg' => 'User created successfully',
		]);
	}
	public function logout()
	{
		$session = session();
		$session->remove(['ad_uid', 'ad_uname']); 
		return redirect()->to(base_url('admin'));
	}
}
?>