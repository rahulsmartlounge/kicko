<?php
namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Models\Admin\OrdersModel;

class Orders extends BaseController
{
    public function __construct()
    {
        $this->session = \Config\Services::session();
        $this->input = \Config\Services::request();
        $this->OrdersModel = new \App\Models\Admin\OrdersModel();
    }
    public function index()
    {
        if ($this->session->get('ad_uid')) {
            $data = [];
            $orders = $this->OrdersModel->getDatatables();
            // print_r($orders);
            // exit;
            $template = view('Admin/common/header');
            $template .= view('Admin/common/leftmenu');
            $template .= view('Admin/orders', $data);
            $template .= view('Admin/common/footer');
            $template .= view('Admin/page_scripts/ordersjs');
            return $template;
        } else {
            if (!$this->session->get('ad_uid')) {
                return redirect()->to(base_url('admin'));
            }
        }

    }
    // Listing table data
    public function ajaxList()
    {
        $model = new \App\Models\Admin\OrdersModel();

        $orderColumnIndex = $this->request->getPost('order')[0]['column'] ?? 0;
        $orderDirection = $this->request->getPost('order')[0]['dir'] ?? 'desc';

        $start = $this->request->getPost('start');
        $length = $this->request->getPost('length');
        $searchValue = $this->request->getPost('search')['value'];

        $columnMap = [
            null,
            'customer.cust_Name',
            'address.add_Email',
            'address.add_Phone',
            'product.pr_Code',
            'order_detail.od_Quantity',
            'order_detail.od_createdon',
            'order_detail.od_Status',
            null
        ];
        $orderBy = $columnMap[$orderColumnIndex] ?? 'order_detail.od_Id';

        // Get paginated data
        $data = $model->getDatatables($searchValue, $start, $length, $orderBy, $orderDirection);

        $formattedData = [];
        foreach ($data['data'] as $row) {
            $address = $row->od_Shipping_Address ?? '';

            $email = '';
            if (preg_match('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', $address, $matches)) {
                $email = $matches[0];
            }

            $phone = '';
            if (preg_match('/(\+?\d[\d\s\-()]{7,15})/', $address, $matches)) {
                $phone = trim($matches[0]);
            }

            $formattedData[] = [

                'cust_Name' => $row->cust_Name ?? 'N/A', // Send raw name only, not link
                'od_Id' => $row->od_Id,
                'add_Email' => $email ?: 'N/A',   // ← Now from od_Shipping_Address
                'add_Phone' => $phone ?: 'N/A',
                'pr_Code' => $row->pr_Code ?? 'N/A',
                'od_Quantity' => $row->od_Quantity ?? 'N/A',
                'od_createdon' => !empty($row->od_createdon) ? date('d M Y, h:i A', strtotime($row->od_createdon)) : 'N/A',
                'od_Status' => $this->getStatusLabel($row->od_Status),
                'actions' => '<a href="' . base_url('admin/orders/view/' . $row->od_Id) . '">
                                    <i class="fa fa-eye"></i></a>'
            ];
        }

        return $this->response->setJSON([
            'draw' => intval($this->request->getPost('draw')),
            'recordsTotal' => $data['total'],        // total records without filtering
            'recordsFiltered' => $data['filtered'],  // total records after filtering
            'data' => $formattedData
        ]);
    }
    // for Labeling the Status

    private function getStatusLabel($status)
    {
        switch ($status) {
            case '1':
                return 'New';
            case '2':
                return 'Confirmed';
            case '3':
                return 'Packed';
            case '4':
                return 'Dispatched';
            default:
                return '';
        }
    }

    public function orderView($od_id)
    {
        $model = new \App\Models\Admin\OrdersModel();
        if (!$this->session->get('ad_uid')) {
            return redirect()->to(base_url('admin'));
        }
        if ($this->request->isAJAX()) {
            $order = $model->getOrder($od_id);
            if (!$order) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Order not found'
                ]);
            }
            $cust_Id = $order->cus_Id;
            $add_Id = $order->add_Id;
            $customer = $model->getCustomer($cust_Id);
            $address = $model->getAddress($add_Id);

            return $this->response->setJSON([
                'status' => true,
                'data' => [
                    'order' => $order,
                    'customer' => $customer,
                    'address' => $address
                ]
            ]);
        }

        $data['od_Id'] = $od_id;
        return view('Admin/common/header')
            . view('Admin/common/leftmenu')
            . view('Admin/order_view', $data)
            . view('Admin/common/footer')
            . view('Admin/page_scripts/orders_viewjs');

    }

    public function orderStatusUpdation($od_id)
    {
        $model = new \App\Models\Admin\OrdersModel();
        $tracker = $this->input->getPost('tracker');
        $status = $this->input->getPost('status');

        //echo $tracker; exit;
        if ($this->request->isAJAX()) {
            $updation = $model->updateStatus($od_id, $tracker, $status);
            if ($updation) {
                if (!$status) {
                    return $this->response->setJSON([
                        'status' => false,
                        'message' => 'Missing The Status.'
                    ]);
                } elseif ($status == '4' && empty(trim($tracker))) {
                    return $this->response->setJSON([
                        'status' => false,
                        'message' => 'Please Enter the Tracking Link.'
                    ]);
                }
                return $this->response->setJSON([
                    'status' => true,
                    'message' => 'Status Updated Successfully.'
                ]);
            }
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Status Updation Failed'
            ]);
        }
    }

}


