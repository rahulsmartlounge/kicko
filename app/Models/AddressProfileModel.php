<?php
namespace App\Models;
use CodeIgniter\Model;

class AddressProfileModel extends Model
{
    protected $table = 'address';
    protected $primaryKey = 'add_Id';
    protected $allowedFields = [
        'add_Id',
        'add_Name',
        'add_BuldingNo',
        'add_Landmark',
        'add_Street',
        'add_City',
        'add_State',
        'add_Pincode',
        'add_Default',
        'add_Status',
        'add_createdon',
        'add_createdby',
        'add_modifyon',
        'add_modifyby',
        'add_Phone',
        'add_Email',
        'add_CustId',
        'add_phcode'
    ];

    public function getUserAddresses($userId)
    {
        return $this->where('add_CustId', $userId)
            ->where('add_Status', 1)
            ->findAll();
    }
    public function addAddress($userId, $data)
    {
        $saveData = [
            'add_CustId' => $userId,
            'add_Name' => ucwords(strtolower(trim($data['newName']))),
            'add_Phone' => trim($data['newPhone']),
            'add_phcode' => trim($data['add_phcode'] ?? ''),
            'add_Email' => trim($data['newEmail']),
            'add_BuldingNo' => ucwords(strtolower(trim($data['newBuilding']))),
            'add_Landmark' => ucwords(strtolower(trim($data['newLandmark']))),
            'add_Street' => ucwords(strtolower(trim($data['newStreet']))),
            'add_City' => ucwords(strtolower(trim($data['newCity']))),
            'add_State' => ucwords(strtolower(trim($data['newState']))),
            'add_Pincode' => trim($data['newPincode']),
            // 'add_Default' => 1,
            'add_Status' => 1,
            'add_createdon' => date("Y-m-d H:i:s"),
            'add_createdby' => $userId,
        ];

        return $this->save($saveData);
    }

    public function setAsDefault($userId, $add_Id)
    {
        // Reset all to non-default for the user
        $this->where('add_CustId', $userId)->set(['add_Default' => 0])->update();

        // Set the selected address as default
        return $this->update($add_Id, ['add_Default' => 1]);
    }

   public function updateAddress($data)
{
    $updateData = [
        'add_Name' => ucwords(strtolower(trim($data['add_Name']))),
        'add_Email' => trim($data['add_Email']),
        'add_Phone' => $data['add_Phone'],
        'add_phcode' => trim($data['add_phcode'] ?? ''),
        'add_BuldingNo' => trim($data['add_BuldingNo']),
        'add_Landmark' => trim($data['add_Landmark']),
        'add_Street' => trim($data['add_Street']),
        'add_City' => ucwords(strtolower(trim($data['add_City']))),
        'add_State' => ucwords(strtolower(trim($data['add_State']))),
        'add_Pincode' => trim($data['add_Pincode']),
        'add_Default' => isset($data['setAsDefault']) ? 1 : 0,
        'add_Status' => 1,
        'add_modifyby' => $data['add_CustId'],
        'add_modifyon' => date('Y-m-d H:i:s'),
    ];

    return $this->db->table('address')
                    ->where('add_Id', $data['add_Id'])
                    ->update($updateData);
}



    public function deleteAddress($add_status, $add_Id, $modified_by)
    {
        return $this->db->query("
            UPDATE address 
            SET add_Status = '$add_status', 
                add_modifyon = NOW(), 
                add_modifyby = '$modified_by' 
            WHERE add_Id = '$add_Id'
        ");
    }
}