<?php
namespace App\Controllers;
use App\Controllers\BaseController;
//use App\Models\DeliveryModel;
use App\Models\ContactModel;
use App\Models\ProductDisplayModel;

class Contact extends BaseController
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
		$this->productdisplayModel = new ProductDisplayModel();
        $this->categories = $this->productdisplayModel->getAllCategoriesAndSub();
		 $data['categories'] = $this->categories;
        $data['title'] = 'Contact Us';

        $data['product'] = $this->productdisplayModel->getAllProducts();
        $template = view('common/header',$data);
		$template.= view('contact');
        $template.= view('common/footer');
		$template.= view('pagescripts/contactjs');
        return $template;

        
    }
public function submit()
{
    if ($this->request->isAJAX()) {
        $data = [
            'fullname'     => $this->request->getPost('fullname'),
            'email'        => $this->request->getPost('email'),
            'contact_no'   => $this->request->getPost('contact_no'),
            'message'      => $this->request->getPost('message'),
            'submitted_at' => date('Y-m-d H:i:s'),
        ];

        // Save to DB
        $model = new \App\Models\ContactModel();
        if (!$model->insert($data)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to save contact enquiry.'
            ]);
        }

        // Load CI email service
        $email = \Config\Services::email();

        // Optional: configure here, or in app/Config/Email.php
        $email->initialize([
            'protocol'    => 'mail', // Simpler than SMTP, works on most servers
            'mailType'    => 'html',
            'charset'     => 'utf-8',
            'newline'     => "\r\n",
        ]);

        // === 1. Email to Admin ===
        $email->setFrom('no-reply@zakhidesigns.com', 'Zakhi Designs Website');
        $email->setTo('sandrakbabu23@gmail.com');
        $email->setReplyTo($data['email'], $data['fullname']);
        $email->setSubject('New Contact Enquiry Received');
        $email->setMessage("
            <p><strong>Name:</strong> {$data['fullname']}</p>
            <p><strong>Email:</strong> {$data['email']}</p>
            <p><strong>Phone:</strong> {$data['contact_no']}</p>
            <p><strong>Message:</strong><br>" . nl2br(htmlspecialchars($data['message'])) . "</p>
        ");
        $adminSent = $email->send();

        // === 2. Auto-reply to user ===
        $email->clear(); // Clear previous email
        $email->setFrom('no-reply@zakhidesigns.com', 'Zakhi Designs');
        $email->setTo($data['email']);
        $email->setSubject('Thank you for contacting Zakhi Designs');
        $email->setMessage("
            <p>Dear <strong>{$data['fullname']}</strong>,</p>
            <p>Thank you for reaching out to Zakhi Designs. We’ve received your message and will get back to you as soon as possible.</p>

            <p style='text-align: center; margin-top: 20px;'>
                <a href='https://zakhidesigns.com' style='padding: 10px 20px; background-color: #d81b60; color: white; text-decoration: none; border-radius: 5px;'>Visit Our Website</a>
            </p>

            <p style='text-align: center; font-size: 14px; color: #555; margin-top: 30px;'>
                For any queries, reach us at <a href='mailto:support@zakhidesigns.com'>support@zakhidesigns.com</a>
            </p>

            <br><br>
            <p>Warm regards,<br>Zakhi Designs Team</p>
        ");
        $userSent = $email->send();

        if ($userSent) {
            return $this->response->setJSON([
                'status' => '1',
                'message' => 'Thank you! Your enquiry has been submitted. We will get back to you shortly.'
            ]);
        } else {
            return $this->response->setJSON([
                'status' => '0',
                'message' => 'Saved successfully, but email sending failed.'
            ]);
        }
    }

    return $this->response->setJSON([
        'status' => '0',
        'message' => 'Invalid request.'
    ]);
}


}