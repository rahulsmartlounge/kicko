<?php
namespace App\Models;


use CodeIgniter\Model;

class CustomerLoginModel extends Model
{
    protected $table = 'customer';
    protected $primaryKey = 'cust_Id';
    protected $allowedFields = [
        'cust_Name',
        'cust_Email',
        'cust_Phone',
        'cust_Password',
        'cust_Status',
        'cust_createdon',
        'cust_createdby',
        'cust_modifyby',
        'reset_token',
        'reset_token_expiry',
        'auth_type'
    ];


    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function getLoginAccount($email, $password)
    {
        // echo "select * from customer where cust_Email = '".$email."' and cust_Password = '".$password."'" ; exit();
        return $this->db->query("select * from customer where cust_Email = '" . $email . "' and cust_Password = '" . $password . "'")->getRow();
    }
    public function getEmailExist($forgotCustEmail)
    {
        $email = strtolower(trim($forgotCustEmail));

        $query = $this->db->query(
            "SELECT cust_Id, cust_Email, cust_Name FROM customer WHERE LOWER(cust_Email) = ?",
            [$email]
        );
        return $query->getRowArray(); // or getRowObject() based on controller
    }


    public function resetPasswordNow($pass, $email)
    {
        return $this->db->table('customer')
            ->where('cust_Email', $email)
            ->update(['cust_Password' => $pass]);
    }
    public function createcust($data)
    {
        return $this->insert($data);
    }
    public function updateResetToken($custId, $token, $expiry)
    {
        return $this->db->table('customer')->update([
            'reset_token' => $token,
            'reset_token_expiry' => $expiry
        ], ['cust_Id' => $custId]);
    }
 public function getCustomerByValidToken($token)
    {
        date_default_timezone_set('Asia/Kolkata');

        $now = date("Y-m-d H:i:s");
        $value_token = trim($token);


        $query = $this->db->query("select * from customer where reset_token = '".$token."' and reset_token_expiry >= '".$now."'")->getRow();
        return $query;
    }

    public function getCustomerByEmail($email)
    {
        // Use query builder to check if the email exists (ignoring 'cust_Status = 3' customers)
        $builder = $this->db->table('customer');
        $builder->where('cust_Email', $email);
        $builder->where('cust_Status !=', 3);
        $query = $builder->get();

        return $query->getRowArray(); // This will return a single record or null if not found
    }

}

?>