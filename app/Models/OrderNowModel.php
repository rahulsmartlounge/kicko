<?php namespace App\Models;

use CodeIgniter\Model;

class OrderNowModel extends Model
{
    protected $table = 'order_detail'; // Primary table
    protected $primaryKey = 'od_Id';
	protected $allowedFields = [
    'pr_Id','od_Original_Price','od_Selling_Price','od_modifyby','pr_Code','cus_Id','od_Status',
    'od_Quantity','od_Grand_Total','od_createdby','od_createdon','add_Id','od_Shipping_Address'];

	  public function getProductWithAddress($cus_Id, $pr_Id)
	{
		return $this->db->table('order_detail od')
			->select('od.*, product.*, address.*')
			->join('product', 'product.pr_Id = od.pr_Id', 'left')
			->join('address', 'address.add_CustId = od.cus_Id AND address.add_Default = 1', 'left')
			->where('od.cus_Id', $cus_Id)
			->where('od.pr_Id', $pr_Id)
			->orderBy('od.od_Id', 'DESC')
			->get()
			->getRowArray(); // returns a single row
	}

	public function getOrdersById($od_Id)
	{
		return $this->db->query("select * from order_detail where od_Id = '".$od_Id."'")->getRow();
	}
	  public function updateOrderStatus($od_Id, $data)
    {
        return $this->update($od_Id, $data);
    }

	public function getProductByid($pr_Id)
	{
		return $this->db->table('product p')
			->select('p.*, c.cat_Name, s.sub_Category_Name')
			->join('category c', 'c.cat_Id = p.cat_id', 'left')
			->join('subcategory s', 's.sub_Id = p.sub_id', 'left')  // only once
			->where('p.pr_Id', $pr_Id)
			->get()
			->getRow(); 
	}
public function getCustomerAddress($cus_Id)
{
    return $this->db->table('address')
        ->where('add_CustId', $cus_Id)
        ->where('add_Default', 1)
        ->get()
        ->getRow();
}
public function getDefaultAddress($zd_uid)
{
    return $this->where('add_CustId', $zd_uid)
                ->where('add_Status', 1)
                ->where('add_Default', 1)
                ->first();
}

    public function getAllAddresses($zd_uid)
    {
        return $this->where('add_CustId', $zd_uid)->findAll();
    } 
	public function getOrdersByUser($userId)
	{
		return $this->select('order_detail.*, product.*')
					->join('product', 'product.pr_Id = order_detail.pr_Id')
					->where('order_detail.cus_Id', $userId)
					->orderBy('order_detail.od_createdon', 'DESC')
					->findAll();
	}


    /**
     * Get orders with product details (JOIN)
     *
     * @param int $userId
     * @return array
     */
    public function getOrdersWithProducts($userId)
    {
        return $this->select('order_detail.*, product.pr_Name, product.product_image')
                    ->join('product', 'product.pr_Id = order_detail.pr_Id')
                    ->where('order_detail.cus_Id', $userId)
                    ->orderBy('order_detail.od_createdon', 'DESC')
                    ->findAll();
    }
}
