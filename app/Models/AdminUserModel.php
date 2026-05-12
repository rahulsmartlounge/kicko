<?php

namespace App\Models;

use CodeIgniter\Model;

class AdminUserModel extends Model
{
    protected $table = 'user';
    protected $primaryKey = 'us_Id';

    protected $returnType = 'array';
    protected $useSoftDeletes = false; // set true if you use soft deletes

    protected $allowedFields = [
        'us_email',
        'us_phone',
        'us_createdon',
        'us_createdby',
        'us_modifyon',
        'us_modifyby',
        // Add other fields like us_name, us_password, etc.
    ];

    // Don't use CI4's default timestamp fields, we'll handle manually
    protected $useTimestamps = false;

    // Optional: Validation (if needed)
    protected $validationRules = [
        'us_email' => 'required|valid_email',
        'us_phone' => 'required|min_length[10]',
    ];

    protected $validationMessages = [
        'us_email' => [
            'required' => 'Email is required.',
            'valid_email' => 'Enter a valid email address.',
        ],
        'us_phone' => [
            'required' => 'Phone number is required.',
            'min_length' => 'Phone number must be at least 10 digits.',
        ],
    ];
}
?>