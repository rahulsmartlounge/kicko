<?php
namespace App\Controllers;

use App\Models\OrderNowModel;
use App\Models\AddressModel;
use App\Models\ProductDisplayModel;
use CodeIgniter\Controller;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
class OrderNow extends Controller
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
        if (!session()->get('zd_uid')) {
            return redirect()->to(base_url());
        }

        $this->productdisplayModel = new ProductDisplayModel();
        $this->categories = $this->productdisplayModel->getAllCategoriesAndSub();
        $data['categories'] = $this->categories;

        $data['product'] = $this->productdisplayModel->getAllProducts();

        $zd_uid = session()->get('zd_uid');
        $model = new AddressModel();
        $orderModel = new OrderNowModel();

        $od_Id = $this->request->getPost('od_Id');

        $addresses = $model->getAllAddresses($zd_uid);
        $orders = $orderModel->getOrdersById($od_Id);

        $data = [
            'details' => $model->getDefaultAddress($zd_uid),
            'addresses' => $addresses,
            'is_new_user' => empty($addresses),
            'od_Id' => $od_Id,
            'order' => $orders,
        ];

        return view('common/header', $data)
            . view('order_now', $data)
            . view('common/footer')
            . view('pagescripts/OrderNowjs');
    }

    public function getAddress($id)
    {
        $model = new AddressModel();
        $address = $model->findAddress($id);

        if (!$address) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Address Not Found']);
        }

        return $this->response->setJSON($address);
    }
    //$custname = ucwords(strtolower(trim($custname)));
    public function saveNewAddress()
    {

        $zd_uid = session()->get('zd_uid');
        $email = trim($this->request->getPost('newEmail'));
        $pincode = $this->request->getPost('newPincode');
        if (empty($email)) {
            return $this->response->setJSON([
                'success' => 0,
                'message' => 'Invalid Email Address.'
            ]);
        }
        if (!preg_match('/^[a-zA-Z0-9._%+-]+@(?:[a-zA-Z0-9-]+\.)+[a-zA-Z]{2,}$/', $email)) {
            return $this->response->setJSON([
                'success' => 0,
                'message' => 'Invalid Email Address.'
            ]);
        }
         if (empty($pincode)) {
            return $this->response->setJSON([
                'success' => '0',
                'message' => 'Pincode is Required.'
            ]);
        } else if (!is_numeric($pincode) || strlen($pincode) < 4 || strlen($pincode) > 10) {
            return $this->response->setJSON([
                'success' => '0',
                'message' => 'Enter a Valid Pincode.'
            ]);
        }
        $add_id = $this->request->getPost('$add_Id');
        $model = new AddressModel();

        $data = [
            'add_Name' => ucwords(strtolower(trim($this->request->getPost('newName')))),
            'add_Email' => $email,
            'add_Phone' => $this->request->getPost('newPhone'),
            'add_phcode' => ltrim($this->request->getPost('newphcode'), '+'),
            'add_BuldingNo' => $this->request->getPost('newBuilding'),
            'add_Street' => ucwords(strtolower(trim($this->request->getPost('newStreet')))),
            'add_Landmark' => ucwords(strtolower(trim($this->request->getPost('newLandmark')))),
            'add_City' => ucwords(strtolower(trim($this->request->getPost('newCity')))),
            'add_State' => ucwords(strtolower(trim($this->request->getPost('newState')))),
            'add_Pincode' => $this->request->getPost('newPincode'),
            'add_CustId' => $zd_uid,
            'add_createdby' => $zd_uid,
            'add_Status' => 1,
            'add_createdon' => date("Y-m-d H:i:s"),
            'add_Default' => 1
        ];

        // Reset all defaults to 0 before inserting new default
        $model->where('add_CustId', $zd_uid)->set(['add_Default' => 0])->update();

        if ($model->insert($data)) {
            $insertId = $model->getInsertID();
            $details = $model->find($insertId);
            return $this->response->setJSON([
                'success' => 1,
                'insertId' => $insertId,
                'details' => $details,
                'message' => 'Address Saved Successfully.'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => 0,
                'message' => 'Failed To Save Address.',
                'errors' => $model->errors()
            ]);
        }
    }

    public function orderproduct($od_Id)
    {
        $zd_uid = session()->get('zd_uid');
        if (empty($zd_uid)) {
            return redirect()->to(base_url());
        }
        $this->productdisplayModel = new ProductDisplayModel();
        $categories = $this->productdisplayModel->getAllCategoriesAndSub();

        //  $data['categories'] = $this->categories;


        $orderModel = new OrderNowModel();
        $addressModel = new AddressModel();

        $orders = $orderModel->getOrdersById($od_Id);

        if (empty($orders)) {
            return redirect()->to(base_url())->with('error', 'Order Not Found');
        }

        $pr_Id = $orders->pr_Id;
        $addresses = $addressModel->getAllAddresses($zd_uid);

        $data = [
            'product' => $orderModel->getProductById($pr_Id),
            'details' => $addressModel->getDefaultAddress($zd_uid),
            'addresses' => $addresses,
            'categories' => $categories,
            'is_new_user' => empty($addresses),
            'od_Id' => $od_Id,
            'order' => $orders
        ];



        return view('common/header', $data)
            . view('order_now', $data)
            . view('common/footer')
            . view('pagescripts/OrderNowjs');
    }

    public function submitfrm()
    {
        $orderModel = new OrderNowModel();
        $addressModel = new AddressModel();
        $productModel = new ProductDisplayModel();

        $zd_uid = session()->get('zd_uid');
        $od_Id = $this->request->getPost('od_Id');
        $add_Id = $this->request->getPost('add_Id') ?? $this->request->getPost('address_id');

        if (empty($zd_uid) || empty($od_Id) || empty($add_Id)) {
            return $this->response->setJSON(['status' => 0, 'msg' => 'Missing Required Information.']);
        }

        $order = $orderModel->getOrdersById($od_Id);
        if (!$order) {
            return $this->response->setJSON(['status' => 0, 'msg' => 'Order Not Found.']);
        }
        $customer = $addressModel->find($add_Id);

        if (!$customer) {
            return $this->response->setJSON(['status' => 0, 'msg' => 'Customer Address Not Found.']);
        }

        $shippingAddressParts = array_filter([
            $customer['add_Name'] ?? '',
            $customer['add_BuldingNo'] ?? '',
            $customer['add_Street'] ?? '',
            $customer['add_Landmark'] ?? '',
            $customer['add_City'] ?? '',
            $customer['add_State'] ?? '',
            $customer['add_Pincode'] ?? '',
            $customer['add_Phone'] ?? '',
            $customer['add_Email'] ?? ''
        ]);

        $shippingAddressString = implode(', ', $shippingAddressParts);
        // Update stock
        $productModel->updateStockAfterOrder($order->pr_Id, $order->od_Quantity);

        // Update order
        $orderModel->updateOrderStatus($od_Id, [
            'od_Status' => 1,
            'od_createdby' => $zd_uid,
            'od_createdon' => date('Y-m-d H:i:s'),
            'od_modifyby' => $zd_uid,
            'add_Id' => $add_Id,
            'od_Shipping_Address' => $shippingAddressString
        ]);

        // Send email
        $product = $orderModel->getProductById($order->pr_Id);


        // Load PHPMailer classes
        require 'vendors/src/Exception.php';
        require 'vendors/src/PHPMailer.php';
        require 'vendors/src/SMTP.php';
        $mail = new PHPMailer(true);
        try {
            // === SHARED CONFIGURATION ===
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'smartloungework@gmail.com';
            $mail->Password = 'peetkiqeqbgxaxqs';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            // === CUSTOMER EMAIL ===
            $mail->setFrom('smartloungework@gmail.com', 'Smart Lounge');
            $mail->addAddress($customer['add_Email'], $customer['add_Name']);
            $mail->addReplyTo('smartloungework@gmail.com', 'Smart Lounge');
            // $mail->addBCC('smartloungework@gmail.com');

            $mail->isHTML(true);
            $mail->Subject = 'Order Confirmation From Zakhi Designs';

            // $addressParts = array_filter([
            //     htmlspecialchars($customer['add_BuldingNo'] ?? ''),
            //     htmlspecialchars($customer['add_Street'] ?? ''),
            //     htmlspecialchars($customer['add_Landmark'] ?? ''),
            //     htmlspecialchars($customer['add_City'] ?? ''),
            //     htmlspecialchars($customer['add_State'] ?? ''),
            //     htmlspecialchars($customer['add_Pincode'] ?? '')
            // ]);

            // $addressDetails = implode('<br>', $addressParts);
            $line1 = implode(', ', array_filter([
                htmlspecialchars($customer['add_BuldingNo'] ?? ''),
                htmlspecialchars($customer['add_Street'] ?? ''),
                htmlspecialchars($customer['add_Landmark'] ?? '')
            ]));

            $line2 = htmlspecialchars($customer['add_City'] ?? '');

            $line3 = implode(', ', array_filter([
                htmlspecialchars($customer['add_State'] ?? ''),
                htmlspecialchars($customer['add_Pincode'] ?? '')
            ]));

            $addressDetails = implode('<br>', array_filter([$line1, $line2, $line3]));

            $discountDisplay = '';
            switch ($order->od_DiscountType) {
                case '%':
                    $discountDisplay = $order->od_DiscountValue . ' %';
                    break;
                case 'Rs':
                    $discountDisplay = 'Rs ' . $order->od_DiscountValue;
                    break;
                default:
                    $discountDisplay = $order->od_DiscountValue . ' ' . $order->od_DiscountType;
            }


            $logoUrl = base_url(ASSET_PATH . 'assets/images/logo.jpg');

            // $mail->Body = '
            // <div style="font-family:Arial,sans-serif; max-width:600px; margin:auto; border:1px solid #ccc; padding:20px; position:absolute;">
            //     <div style="text-align:center;">
            //         <img src="' . $logoUrl . '" alt="Zakhi Designs Logo" style="max-width:150px;">
            //         <h2 style="color:#d81b60;">Order Confirmation</h2>
            //         <p>Thank you for shopping with Zakhi Designs.</p>
            //     </div>
            //     <table style="position:relative;" width="100%" cellpadding="8" cellspacing="0" style="border-collapse:collapse; border:1px solid #ddd; font-size:14px;">
            //         <tr><th align="left" style="padding:6px;">Order ID</th><td style="padding:6px;">' . htmlspecialchars($od_Id) . '</td></tr>
            //         <tr><th align="left" style="padding:6px;">Product</th><td style="padding:6px;">' . htmlspecialchars($product->pr_Name) . '</td></tr>
            //         <tr><th align="left" style="padding:6px;">Product Code</th><td style="padding:6px;">' . htmlspecialchars($product->pr_Code) . '</td></tr>
            //         <tr><th align="left" style="padding:6px;">Quantity</th><td style="padding:6px;">' . htmlspecialchars($order->od_Quantity) . '</td></tr>
            //         <tr><th align="left" style="padding:6px;">Actual Price</th><td style="padding:6px;">₹' . htmlspecialchars($order->od_Original_Price) . '</td></tr>
            //         <tr><th align="left" style="padding:6px;">Discount</th><td style="padding:6px;">' . htmlspecialchars($discountDisplay) . '</td></tr>
            //         <tr><th align="left" style="padding:6px;">Total Price</th><td style="padding:6px;"><b>₹' . htmlspecialchars($order->od_Grand_Total) . '</b></td></tr>
            //         <tr><th align="left" style="padding:6px;">Customer Name</th><td style="padding:6px;">' . htmlspecialchars($customer['add_Name']) . '</td></tr>
            //         <tr><th align="left" style="padding:6px;">Email</th><td style="padding:6px;">' . htmlspecialchars($customer['add_Email']) . '</td></tr>
            //         <tr><th align="left" style="padding:6px;">Phone</th><td style="padding:6px;">' . htmlspecialchars($customer['add_Phone']) . '</td></tr>
            //         <tr><th align="left" style="padding:6px; vertical-align:top; white-space: normal; word-break: break-word;">
            //             Delivery Address
            //             </th>
            //             <td style="padding:6px; ">' . $addressDetails . '</td></tr>
            //     </table>
            //     <p style="margin-top:20px; text-align:center; font-size:15px;">
            //         <strong>Thank you for purchasing with Zakhi Designs!</strong><br>
            //         Your item will be delivered in the next 5–7 business days.
            //     </p>
            //     <p style="text-align:center; margin-top:15px;">
            //         <a href="https://v4cstaging.co.in/zakhidesigns/" style="padding:10px 20px; background-color:#d81b60; color:white; text-decoration:none; border-radius:5px;">Visit Our Website</a>
            //     </p>
            //     <p style="text-align:center; font-size:13px; color:#555; margin-top:25px;">
            //         For any queries, reach us at <a href="mailto:support@zakhidesigns.com">support@zakhidesigns.com</a>
            //     </p>
            // </div>';
            $mail->Body = '
            <h2 style="color:#d81b60;">Order Confirmed!</h2>
                <p style="font-size:15px; color:#444;">
                    Hi ' . htmlspecialchars($customer['add_Name']) . ',<br>
                    We’re excited to let you know that your order has been successfully placed with <strong>Zakhi Designs</strong>! 🎉<br>
                    Below are the details of your purchase.
                </p>

                <div style="font-family:Arial, sans-serif; max-width:600px; margin:auto; border:1px solid #ccc; padding:20px; position:relative; background:#ffffff;">
                    <div style="text-align:center;">
                        <img src="' . $logoUrl . '" alt="Zakhi Designs Logo" style="max-width:150px; margin-bottom:10px;">
                       
                        <p style="font-size:15px; color:#444;">Thank you for shopping with <strong>Zakhi Designs</strong>.<br>Your order has been received and is being processed.</p>
                    </div>

                    <table width="100%" cellpadding="8" cellspacing="0" style="border-collapse:collapse; border:1px solid #ddd; font-size:14px; margin-top:20px;">
                        <tr><th align="left" style="padding:6px; background:#f9f9f9;">Order ID</th><td style="padding:6px;">' . htmlspecialchars($od_Id) . '</td></tr>
                        <tr><th align="left" style="padding:6px;">Product</th><td style="padding:6px;">' . htmlspecialchars($product->pr_Name) . '</td></tr>
                        <tr><th align="left" style="padding:6px;">Product Code</th><td style="padding:6px;">' . htmlspecialchars($product->pr_Code) . '</td></tr>
                        <tr><th align="left" style="padding:6px;">Quantity</th><td style="padding:6px;">' . htmlspecialchars($order->od_Quantity) . '</td></tr>
                        <tr><th align="left" style="padding:6px;">Actual Price</th><td style="padding:6px;">₹' . htmlspecialchars($order->od_Original_Price) . '</td></tr>
                        <tr><th align="left" style="padding:6px;">Discount</th><td style="padding:6px;">' . htmlspecialchars($discountDisplay) . '</td></tr>
                        <tr><th align="left" style="padding:6px;">Total Price</th><td style="padding:6px;"><strong>₹' . htmlspecialchars($order->od_Grand_Total) . '</strong></td></tr>
                        <tr><th align="left" style="padding:6px;">Customer Name</th><td style="padding:6px;">' . htmlspecialchars($customer['add_Name']) . '</td></tr>
                        <tr><th align="left" style="padding:6px;">Email</th><td style="padding:6px;">' . htmlspecialchars($customer['add_Email']) . '</td></tr>
                        <tr><th align="left" style="padding:6px;">Phone</th><td style="padding:6px;">' . htmlspecialchars($customer['add_Phone']) . '</td></tr>
                        <tr>
                            <th align="left" style="padding:6px; vertical-align:top; white-space:normal; word-break:break-word;">Delivery Address</th>
                            <td style="padding:6px; vertical-align:top;">' . $addressDetails . '</td>
                        </tr>
                    </table>

                    <div style="margin-top:25px; text-align:center;">
                        <p style="font-size:15px; color:#333;">
                            🕒 <strong>Estimated Delivery:</strong> Within 5–7 business days.<br>
                            You will receive an update once your order is shipped.
                        </p>
                        <a href="https://v4cstaging.co.in/zakhidesigns/" style="display:inline-block; margin-top:15px; padding:10px 20px; background-color:#d81b60; color:#fff; text-decoration:none; border-radius:5px;">Visit Our Website</a>
                    </div>

                    <p style="margin-top:25px; text-align:center; font-size:13px; color:#555;">
                        Need help? Contact us at <a href="mailto:support@zakhidesigns.com">support@zakhidesigns.com</a><br>
                        Thank you for choosing Zakhi Designs!
                    </p>
                </div>';


            $mail->AltBody = "Thank you for your order from Zakhi Designs. Order ID: {$od_Id}. Product: {$product->pr_Name}, Quantity: {$order->od_Quantity}. Total: ₹{$order->od_Grand_Total}.";
            $mail->send();

            // === ADMIN EMAIL ===
            $adminMail = new PHPMailer(true);
            $adminMail->isSMTP();
            $adminMail->Host = 'smtp.gmail.com';
            $adminMail->SMTPAuth = true;
            $adminMail->Username = 'smartloungework@gmail.com';
            $adminMail->Password = 'peetkiqeqbgxaxqs';
            $adminMail->SMTPSecure = 'tls';
            $adminMail->Port = 587;

            $adminMail->setFrom('smartloungework@gmail.com', 'Zakhi Designs - Orders');
            $adminMail->addAddress('smartloungework@gmail.com'); // Admin recipient
            $adminMail->isHTML(true);
            $adminMail->Subject = ' New Order Received - Order ID: ' . $od_Id;

            $adminMail->Body = '
            <div style="font-family:Arial,sans-serif; max-width:600px; margin:auto; border:1px solid #ddd; padding:20px; position:absolute;">
                <div style="text-align:center; margin-bottom:20px;">
                    <img src="' . $logoUrl . '" alt="Zakhi Designs Logo" style="max-width:180px;">

                    <h2 style="color:#0055a5; margin-top:10px;">New Order Notification</h2>
                </div>

                <table style="position:relative;" width="100%" cellpadding="10" cellspacing="0" style="border-collapse:collapse; font-size:14px; border:1px solid #ccc;">
                    <tr style="background:#f9f9f9;"><th align="left">Order ID</th><td>' . htmlspecialchars($od_Id) . '</td></tr>
                    <tr><th align="left">Customer</th><td>' . htmlspecialchars($customer['add_Name']) . '</td></tr>
                    <tr><th align="left">Email</th><td>' . htmlspecialchars($customer['add_Email']) . '</td></tr>
                    <tr><th align="left">Phone</th><td>' . htmlspecialchars($customer['add_Phone']) . '</td></tr>
                    <tr><th align="left" style=" white-space: normal; word-break: break-word; vertical-align:top;">Address</th><td>' . $addressDetails . '</td></tr>
                    <tr><th align="left">Product</th><td>' . htmlspecialchars($product->pr_Name) . ' (' . htmlspecialchars($product->pr_Code) . ')</td></tr>
                    <tr><th align="left">Quantity</th><td>' . htmlspecialchars($order->od_Quantity) . '</td></tr>
                    <tr><th align="left">Total</th><td><strong>₹' . number_format($order->od_Grand_Total) . '</strong></td></tr>
                </table>

                <p style="margin-top:20px;">Please check the admin panel for full details.</p>
                <p><strong>Thank you,<br>Team Zakhi Designs</strong></p>
            </div>';

            $adminMail->AltBody = "Kindly proceed to pack the item and update the order status accordingly.";
            $adminMail->send();

            return $this->response->setJSON([
                'status' => 1,
                'msg' => 'Thank You! Your Order is Confirmed. Continue Shopping To Explore More Great Products',
                'redirect' => base_url('/')
            ]);
        } catch (Exception $e) {
            return $this->response->setJSON([
                'status' => 0,
                'msg' => 'Failed to send order email. Mailer Error: ' . $mail->ErrorInfo
            ]);
        }
    }
}
