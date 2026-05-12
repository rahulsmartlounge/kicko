<?php
namespace App\Models\Admin;


use CodeIgniter\Model;

class DashboardModel extends Model
{
    protected $db;
    protected $table = 'order_detail';
    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }
    public function getLatestOrderCount()
    {
        $sevenDaysAgo = date('Y-m-d H:i:s', strtotime('-7 days'));

        return $this->db->table($this->table)
            ->where('od_Status', '1')
            ->where('od_createdon >=', $sevenDaysAgo)
            ->countAllResults();
    }

    public function getTotalOrderCount()
    {
        return $this->db->table($this->table)->where('od_Status', '1')->countAllResults();
    }

    public function getTotalCustomerCount()
    {
        return $this->db->table('customer')->where('cust_Status', '1')->countAllResults();
    }

    public function getAnnualRevenue()
    {
        $currentMonth = date('n'); // Numeric representation of month (1-12)
        $currentYear = date('Y');

        if ($currentMonth >= 4) {
            // We're in current financial year starting April 1st this year
            $startDate = date('Y-04-01 00:00:00'); // April 1st of current year
            $endDate = date(($currentYear + 1) . '-03-31 23:59:59'); // March 31st of next year
        } else {
            // We're in financial year that started last year
            $startDate = date(($currentYear - 1) . '-04-01 00:00:00');
            $endDate = date($currentYear . '-03-31 23:59:59');
        }

        $result = $this->db->table($this->table)
            ->selectSum('od_Grand_Total', 'total_revenue')
            ->where('od_Status', '4')
            ->where('od_createdon >=', $startDate)
            ->where('od_createdon <=', $endDate)
            ->get()
            ->getRow();

        return $result->total_revenue ?? 0;
    }


    public function getTodaysOrders()
    {
        $todayStart = date('Y-m-d 00:00:00');
        $todayEnd = date('Y-m-d 23:59:59');

        return $this->db->table('order_detail od')
            ->select('od.od_Id, od.od_Grand_Total, od.od_Selling_Price, od.od_DiscountValue, od.od_DiscountType, od.od_Status,
                  c.cust_Name as customer_name, p.pr_Name as product_name')
            ->join('customer c', 'c.cust_Id = od.cus_Id', 'left')
            ->join('product p', 'p.pr_Id = od.pr_Id', 'left')
            ->where('od.od_Status', '1')
            ->where('od.od_createdon >=', $todayStart)
            ->where('od.od_createdon <=', $todayEnd)
            ->orderBy('od.od_createdon', 'DESC')
            ->get()
            ->getResult();
    }

    public function getLatestProducts()
    {
        return $this->db->table('product')
            ->where('pr_Status', 1) // Only active products
            ->orderBy('pr_createdon', 'DESC') // Latest first
            ->limit(10)
            ->get()
            ->getResult();
    }





}

?>