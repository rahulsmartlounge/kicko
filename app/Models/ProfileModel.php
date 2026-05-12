<?php namespace App\Models;

use CodeIgniter\Model;

class ProfileModel extends Model
{
    protected $table      = 'customer';
    protected $primaryKey = 'cust_Id';

    protected $allowedFields = ['cust_Name', 'cust_Email', 'cust_Phone','cust_Password'];

    public function getUserById($id)
    {
        return $this->find($id);
    }

    public function updateUserProfile($id, $data)
    {
        if ($this->update($id, $data)) {
            return $this->getUserById($id); 
        }
        return false;
    }
	
public function changePassword($custId, $oldPassword, $newPassword)
{
    $user = $this->find($custId);

    if (!$user || md5($oldPassword) !== $user['cust_Password']) {
        return ['status' => 0, 'msg' => 'Old Password Does Not Match.'];
    }

    if (md5($newPassword) === $user['cust_Password']) {
        return ['status' => 0, 'msg' => 'Please Enter A New Password Different From The Old One.'];
    }

    $data = ['cust_Password' => md5($newPassword)];

    if ($this->update($custId, $data)) {
        return ['status' => 1, 'msg' => 'Password Updated Successfully.'];
    } else {
        return ['status' => 0, 'msg' => 'Something Went Wrong. Could Not Update Password.'];
    }
}



}
