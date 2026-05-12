<?php
namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'customer';
    protected $primaryKey = 'cust_Id';

    protected $allowedFields = [
        'cust_Name', 'cust_Email', 'cust_Phone', 'cust_Password',
        'cust_Status', 'cust_createdon', 'cust_createdby',
        'cust_modifyby', 'cust_modifyon'
    ];

    // Disable automatic timestamps unless your DB has created_at and updated_at fields
    protected $useTimestamps = false;

    /**
     * Update user profile by user ID
     */
    public function updateProfile($userId, $data)
    {
        $updateData = [
            'cust_Name'     => $data['name'] ?? '',
            'cust_Email'    => $data['email'] ?? '',
            'cust_Phone'    => $data['phone'] ?? '',
            'cust_modifyby' => $userId,
            'cust_modifyon' => date('Y-m-d H:i:s'),
            'cust_Status'   => 1
        ];

        return $this->update($userId, $updateData);
    }

    /**
     * Get user by email
     */
    public function getUserByEmail($email)
    {
        return $this->where('cust_Email', $email)->first(); // FIXED FIELD NAME
    }
}
