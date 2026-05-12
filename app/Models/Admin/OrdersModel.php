<?php
namespace App\Models\Admin;

use CodeIgniter\Model;

class OrdersModel extends Model
{

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }


    protected $table = 'order_detail';
    protected $primaryKey = 'od_Id';
    protected $allowedFields = ['tracker_Link', 'od_Status', 'cus_Id'];

    public function getDatatables($searchValue = null, $start = 0, $length = 10, $orderBy = 'order_detail.od_Id', $orderDir = 'DESC')
    {

        $builder = $this->db->table('order_detail')
            ->select([
                'order_detail.od_Id',
                'order_detail.od_Quantity',
                'order_detail.od_Shipping_Address',
                'order_detail.od_Status',
                'order_detail.od_createdon',
                'product.pr_Name',
                'product.pr_Code',
                'customer.cust_Name',
                // 'customer.cust_Email',
                // 'customer.cust_Phone'
                'address.add_Email',
                'address.add_Phone'
            ])
            ->where('od_Status!=', '')
            ->join('product', 'product.pr_Id = order_detail.pr_Id', 'left')
            ->join('customer', 'customer.cust_Id = order_detail.cus_Id', 'left')
            ->join('address', 'address.add_Id = order_detail.add_Id', 'left');

        // Total records before filter
        $totalBuilder = clone $builder;
        $total = $totalBuilder->countAllResults(false);

        // Apply search filter with space + tab removal
        if (!empty($searchValue)) {
            $search = str_replace([" ", "\t"], '', $searchValue); // remove normal & tab spaces from input

            $builder->groupStart()
                ->like("REPLACE(REPLACE(customer.cust_Name, ' ', ''), CHAR(9), '')", $search, false)
                ->orLike("REPLACE(REPLACE(order_detail.od_Shipping_Address, ' ', ''), CHAR(9), '')", $search, false)
                ->orLike("REPLACE(REPLACE(product.pr_Code, ' ', ''), CHAR(9), '')", $search, false)
                ->groupEnd();
        }

        // Filtered count after search
        $filteredBuilder = clone $builder;
        $filtered = $filteredBuilder->countAllResults(false);

        // Order by od_Status in custom order, then by createdon
        $builder->orderBy("FIELD(order_detail.od_Status, 1, 2, 3, 4)", 'ASC', false);
        $builder->orderBy('order_detail.od_createdon', 'DESC');

        // Pagination
        $builder->limit($length, $start);
        $query = $builder->get();
        $data = $query->getResult();

        return [
            'data' => $data,
            'total' => $total,
            'filtered' => $filtered
        ];

    }




    public function getOrder($od_id)
    {
        return $this->db->table('order_detail')
            ->select('order_detail.*, product.pr_Code, product.pr_Description, product.pr_Name')
            ->join('product', 'product.pr_Id = order_detail.pr_Id')
            ->where('order_detail.od_Id', $od_id)
            ->whereIn('order_detail.od_Status', [1, 2, 3, 4]) // include only specific statuses
            ->get()
            ->getRow();

    }


    public function getCustomer($cust_Id)
    {
        return $this->db->table('customer')
            ->where('cust_Id', $cust_Id)
            ->get()
            ->getRow();
    }

    public function getAddress($add_Id)
    {
        return $this->db->table('address')
            ->where('add_Id ', $add_Id)
            // ->where('add_default', '1')
            ->get()
            ->getRow();
    }
    public function updateStatus($od_id, $tracker, $status)
    {

        return $this->db->table('order_detail')
            ->where('od_Id', $od_id)
            ->update([
                'tracker_Link' => $tracker,
                'od_Status' => $status
            ]);
    }



}

?>