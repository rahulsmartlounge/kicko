<?php

namespace App\Controllers;
use App\Models\Admin\ProductModel;
use App\Models\Admin\CustomerModel;
use App\Models\Admin\AddressModel;

class OrderNow extends BaseController
{

	protected $session;
    protected $request;
    protected $productModel;
    protected $customerModel;
    protected $addressModel;

    public function __construct()
    {
        $this->productModel  = new ProductModel();
        $this->customerModel = new CustomerModel();
        $this->addressModel  = new AddressModel();
    }

    public function index()
    {
        // Get product ID from GET or POST (depending on your link or form)
        $productId = $this->request->getPost('pr_Id');
		$custId = $this->session->getPost('ad_uid');
        if (!$productId) {
            return redirect()->to('/'); // Redirect if no product specified
        }

        // Check if user logged in (assuming session has 'cust_id')
		if (!$this->session->get('ad_uid')) {
			// Not logged in, redirect to login and pass intended page + product id
			return redirect()->to('/login?redirect=ordernow&pr_Id=' . $productId);
		}

        // Get logged-in user data
        
        $customer = $this->customerModel->find($custId);
        $address = $this->addressModel->where('cust_id', $custId)->first();

        // Get product details for this productId
        $product = $this->productModel->find($productId);
        if (!$product) {
            return redirect()->to('/'); // If product not found, redirect or show error
        }

        // Prepare data for the view
        $data = [
            'customer' => $customer,
            'address' => $address,
            'product' => $product,
        ];

        // Load your order form view with pre-filled data
        //return view('order_now', $data);
		$template = view('common/header');
			$template.= view('order_now');
			$template.= view('top_products',$data);
			$template.= view('common/footer');      
			return $template;
    }
}

?>
   

