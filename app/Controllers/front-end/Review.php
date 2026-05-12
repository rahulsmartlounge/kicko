<?php
namespace App\Controllers;

use App\Models\ReviewModel;
use App\Models\ProductDisplayModel;
use App\Models\UserModel;


class Review extends BaseController
{
    protected $session;
    protected $request;

    public function __construct()
    {
        $this->session = \Config\Services::session();
        $this->request = \Config\Services::request();
    }

    // Load the review form/details view
	public function index() 
    {
        $userId = session()->get('zd_uid');

        $userModel = new UserModel();
        $addressModel = new AddressProfileModel();
        $orderModel = new OrderModel();

        $user = $userModel->find($userId);

        $data = [
            'user' => $user,
            'addresses' => $addressModel->getUserAddresses($userId),
            'orders' => $orderModel->getOrdersByUser($userId),
        ];

		$template  = view('common/header',$data);
        $template .= view('review', $data); // Ensure this view file exists
        $template .= view('common/footer');
        $template .= view('pagescripts/reviewjs');
		return $template;
		

    }
   public function loaddetails($custId, $pr_Id)
	{
		$userModel     = new UserModel();
		$reviewModel   = new ReviewModel();
		$productModel  = new ProductDisplayModel();

		// Fetch required data
		$customer      = $userModel->find($custId);
		$product       = $productModel->find($pr_Id);
		$reviews       = $reviewModel->where('pr_Id', $pr_Id)->orderBy('created_at', 'DESC')->findAll();
		$categories    = $productModel->getAllCategoriesAndSub();

		// Pass data to the view
		$data = [
			'customer'   => $customer,
			'product'    => $product,
			'reviews'    => $reviews,
			'categories' => $categories,
		];

		// Load view
		$template  = view('common/header', $data);
		$template .= view('review', $data); // Make sure 'review' view handles looping through $reviews
		$template .= view('common/footer');
		$template .= view('pagescripts/reviewjs');

		return $template;
	}



    // Handle review submission via AJAX
    public function submit()
{
    $reviewModel = new ReviewModel();
    $pr_Id = $this->request->getPost('pr_Id');

    // Convert name and review to uppercase and trim
        $name   = ucwords(strtolower(trim($this->request->getPost('name'))));
        $review = ucwords(strtolower(trim($this->request->getPost('review'))));


    // Get form inputs
    $data = [
        'cust_Id'    => $this->request->getPost('cust_Id'),
        'pr_Id'      => $pr_Id,
        'name'       => $name,
        'email'      => $this->request->getPost('email'),
        'review'     => $review,
        'rating'     => $this->request->getPost('rating'),
        'created_at' => date('Y-m-d H:i:s'),
        'is_approved'=> 0
    ];

    // Validate input
    $validation = \Config\Services::validation();
    $validation->setRules([
        'name'   => 'required',
        'email'  => 'required|valid_email',
        'rating' => 'required|in_list[1,2,3,4,5]',
       // 'review' => 'required'
    ]);

    if (!$validation->run($this->request->getPost())) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => $validation->getErrors()
        ]);
    }

    // Save to database
    if ($reviewModel->insert($data)) {
        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Review submitted successfully!'
        ]);
    } else {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Failed to save review.'
        ]);
    }
}

}
