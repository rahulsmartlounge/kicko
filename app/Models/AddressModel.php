<?php namespace App\Models;

use CodeIgniter\Model;

class AddressModel extends Model
{
	protected $table = 'address';
    protected $primaryKey = 'add_Id';
    protected $allowedFields = [
        'add_Name', 'add_Email', 'add_Phone', 'add_BuldingNo', 'add_Street',
        'add_Landmark', 'add_City', 'add_State', 'add_Pincode',
        'add_CustId', 'add_Default','add_Status','add_createdon','add_createdby',
		'add_modifyon','add_modifyby','add_phcode'
    ];
	 public function getDefaultAddress($custId)
    {
        return $this->where('add_CustId', $custId)->where('add_Default', 1)->first();
    }
	public function insertOrder($data)
	{
		$this->db->table('order_detail')->insert($data);
		return $this->db->insertID(); // return the inserted ID
	}
    public function getAllAddresses($zd_uid)
    {
		return $this->db->table('address')
		->select('address.*')
		->where('address.add_CustId', $zd_uid)
		->where('address.add_Status', 1)
		->get()
		->getResultArray();

    }
	 public function findAddress($id)
    {
        return $this->where('add_Id', $id)->first();
    }
	public function setDefault($userId, $addressId = 0)
    {
        // Unset all current default addresses
        $this->where('add_CustId', $userId)->set(['add_Default' => 0])->update();

        // Set specific address as default (if ID given)
        if ($addressId > 0) {
            $this->update($addressId, ['add_Default' => 1]);
        }
    }
	/* public function insertAndSetDefault($zd_uid, $data)
{
    // 1. Unset all previous default addresses
    $this->builder()->where('add_CustId', $zd_uid)->update(['add_Default' => 0]);

    // 2. Prepare new address data
    $data['add_CustId']   = $zd_uid;
    $data['add_Default']  = 1;

    // 3. Insert new address
    $this->insert($data);
    $newId = $this->getInsertID();

    // 4. Return newly inserted address
    return $this->find($newId);
}
 */
}

