<?php
namespace App\Controllers;
use App\Controllers\BaseController;
//use App\Models\DeliveryModel;
use App\Models\ContactModel;
use App\Models\ProductDisplayModel;
 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
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
// public function submit()
// {
//     if ($this->request->isAJAX()) {
//         $data = [
//             'fullname'     => $this->request->getPost('fullname'),
//             'email'        => $this->request->getPost('email'),
//             'contact_no'   => $this->request->getPost('contact_no'),
//             'message'      => $this->request->getPost('message'),
//             'submitted_at' => date('Y-m-d H:i:s'),
//         ];

//         // Save to DB
//         $model = new \App\Models\ContactModel();
//         if (!$model->insert($data)) {
//             return $this->response->setJSON([
//                 'status' => 'error',
//                 'message' => 'Failed to save contact enquiry.'
//             ]);
//         }

//         // === Admin Email (raw PHP mail) ===
//         $to      = 'sandrakbabu23@gmail.com';
//         $subject = 'New Contact Enquiry Received';

//         $adminMessage = "
//             <html><body>
//             <p><strong>Name:</strong> {$data['fullname']}</p>
//             <p><strong>Email:</strong> {$data['email']}</p>
//             <p><strong>Phone:</strong> {$data['contact_no']}</p>
//             <p><strong>Message:</strong><br>" . nl2br(htmlspecialchars($data['message'])) . "</p>
//             </body></html>
//         ";

//         $headers = "MIME-Version: 1.0\r\n";
//         $headers .= "Content-type: text/html; charset=UTF-8\r\n";
//         $headers .= "From: Zakhi Designs <no-reply@zakhidesigns.com>\r\n";
//         $headers .= "Reply-To: {$data['fullname']} <{$data['email']}>\r\n";

//         $adminSent = mail($to, $subject, $adminMessage, $headers);

//         // === Auto-reply to User ===
//         $userSubject = 'Thank you for contacting Zakhi Designs';
//         $userMessage = "
//             <html><body>
//             <p>Dear <strong>{$data['fullname']}</strong>,</p>
//             <p>Thank you for contacting Zakhi Designs. We have received your message and will get back to you shortly.</p>
//             <p>For any queries, contact us at <a href='mailto:support@zakhidesigns.com'>support@zakhidesigns.com</a></p>
//             <br>
//             <p>Warm regards,<br>Zakhi Designs Team</p>
//             </body></html>
//         ";

//         $userHeaders = "MIME-Version: 1.0\r\n";
//         $userHeaders .= "Content-type: text/html; charset=UTF-8\r\n";
//         $userHeaders .= "From: Zakhi Designs <no-reply@zakhidesigns.com>\r\n";

//         $userSent = mail($data['email'], $userSubject, $userMessage, $userHeaders);

//         if ($adminSent && $userSent) {
//             return $this->response->setJSON([
//                 'status' => '1',
//                 'message' => 'Thank you! Your enquiry has been submitted. We will get back to you shortly.'
//             ]);
//         } else {
//             return $this->response->setJSON([
//                 'status' => '0',
//                 'message' => 'Saved successfully, but email sending failed.'
//             ]);
//         }
//     }

//     return $this->response->setJSON([
//         'status' => '0',
//         'message' => 'Invalid request.'
//     ]);
// }


public function submit()
{
    $contact_no = $this->request->getPost('contact_no');
$digitsOnly = preg_replace('/[^0-9]/', '', $contact_no);

if (!preg_match('/^[0-9()+ ]+$/', $contact_no) || strlen($digitsOnly) < 7 || strlen($digitsOnly) > 20) {
    return $this->response->setJSON([
        'status' => '0',
        'message' => 'Invalid contact number. Only digits, spaces, "+", "(", ")" allowed. Must contain 7 to 15 digits.'
    ]);
}

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

        // === Load PHPMailer from vendors/src ===
        require 'vendors/src/Exception.php';
        require 'vendors/src/PHPMailer.php';
        require 'vendors/src/SMTP.php';

        $mail = new PHPMailer(true);

        try {
            // SMTP Configuration
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'smartloungework@gmail.com'; // Your Gmail
            $mail->Password   = 'peetkiqeqbgxaxqs'; // App Password
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            $mail->setFrom('smartloungework@gmail.com', 'Zakhi Designs');

            // === Send Email to Admin ===
            $mail->addAddress('smartloungework@gmail.com', 'Admin');
            $mail->addReplyTo($data['email'], $data['fullname']);
            $mail->isHTML(true);
            $mail->Subject = 'New Contact Enquiry Received';
            $mail->Body = "
                <html><body>
                <p><strong>Name:</strong> {$data['fullname']}</p>
                <p><strong>Email:</strong> {$data['email']}</p>
                <p><strong>Phone:</strong> {$data['contact_no']}</p>
                <p><strong>Message:</strong><br>" . nl2br(htmlspecialchars($data['message'])) . "</p>
                </body></html>
            ";
            $mail->AltBody = "Name: {$data['fullname']}\nEmail: {$data['email']}\nPhone: {$data['contact_no']}\nMessage: {$data['message']}";
            $mail->send(); // Send to admin

            // === Auto-reply to User ===
            $mail->clearAddresses();
            $mail->addAddress($data['email'], $data['fullname']);
            $mail->Subject = 'Thank you for contacting Zakhi Designs';
            $mail->Body = "
                <html><body>
                <p>Dear <strong>{$data['fullname']}</strong>,</p>
                <p>Thank you for contacting Zakhi Designs. We have received your message and will get back to you shortly.</p>
                <p>For any queries, contact us at <a href='mailto:support@zakhidesigns.com'>support@zakhidesigns.com</a></p>
                <br>
                <p>Warm regards,<br>Zakhi Designs Team</p>
                </body></html>
            ";
            $mail->AltBody = "Dear {$data['fullname']},\n\nThank you for contacting Zakhi Designs. We have received your message and will get back to you shortly.";
            $mail->send(); // Send to user

            return $this->response->setJSON([
                'status' => '1',
                'message' => 'Thank you! Your enquiry has been submitted. We will get back to you shortly.'
            ]);

        } catch (Exception $e) {
            return $this->response->setJSON([
                'status' => '0',
                'message' => 'Saved successfully, but email sending failed. Error: ' . $mail->ErrorInfo
            ]);
        }
    }

    return $this->response->setJSON([
        'status' => '0',
        'message' => 'Invalid request.'
    ]);
}


}
