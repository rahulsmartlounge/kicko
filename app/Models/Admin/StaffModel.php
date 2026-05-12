<?php
namespace App\Models\Admin;

use CodeIgniter\Model;

class StaffModel extends Model
{

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }
    public function getAllStaff()
    {
        $query = $this->db->query("select * from user where us_Role != 1 AND  us_Status != 3");
        return $query->getResult();
    }
    public function createStaff($data)
    {
        return $this->db->table('user')->insert($data);
    }
    public function modifyStaff($us_id, $data)
    {

        $this->db->table('user')->where('us_Id', $us_id)->update($data);
        return $this->db->getLastQuery();
    }
    public function getStaffByid($us_Id)
    {
        return $this->db->query("select * from user where us_Id = '" . $us_Id . "'")->getRow();
    }
    public function deleteStaffById($us_status, $us_id, $modified_by)
    {
        return $this->db->query("update user set us_Status = '" . $us_status . "', us_modifyon=NOW(), us_modifyby='" . $modified_by . "' where us_Id = '" . $us_id . "'");
    }
    public function updateStaff($us_Id, $data)
    {
        return $this->db->table('user')->where('us_Id', $us_Id)->update($data);
    }
    public function getStaffByEmail($email)
    {
        // Use query builder to check if the email exists (ignoring 'cust_Status = 3' customers)
        $builder = $this->db->table('user');
        $builder->where('us_Email', $email);
        $builder->where('us_Status !=', 3);
        $query = $builder->get();
        return $query->getRowArray(); // This will return a single record or null if not found
    }
    public function emailExistsExcept($email, $excludeId)
    {
        $builder = $this->db->table('user');
        $builder->where('us_Email', $email);
        $builder->where('us_Id !=', $excludeId);
        $builder->where('us_Status !=', 3);
        $query = $builder->get();
        return $query->getNumRows() > 0;
    }

    //**************************Data table */
    protected $table = 'user';
    protected $primaryKey = 'us_Id';
    protected $allowedFields = ['us_Name', 'us_Email', 'us_Email2', 'us_Phone', 'us_Status']; // Adjust to your table

    // For DataTables
    public function getDatatables()
    {
        $postData = service('request')->getPost();
        $searchValue = '';

        if (!empty($postData['search']['value'])) {
            $searchValue = $this->db->escapeLikeString(str_replace([" ", "\t"], '', $postData['search']['value']));
        }

        $builder = $this->db->table('user u');
        $builder->select('u.*');
        $builder->where('u.us_Status !=', 3);
        $builder->where('u.us_Role !=', 1); // Exclude admin

        if (!empty($searchValue)) {
            $builder->groupStart();
            $builder->where("REPLACE(REPLACE(u.us_Name, ' ', ''), CHAR(9), '') LIKE '%{$searchValue}%'");
            $builder->groupEnd();
        }

        // Order
        $columns = ['u.us_Id', 'u.us_Name', 'u.us_Email', 'u.us_Email2', 'u.us_Phone', 'u.us_Status'];
        if (!empty($postData['order'])) {
            $orderCol = $columns[$postData['order'][0]['column']] ?? 'u.us_Id';
            $orderDir = $postData['order'][0]['dir'] ?? 'DESC';
            $builder->orderBy($orderCol, $orderDir);
        } else {
            $builder->orderBy('u.us_Id', 'DESC');
        }

        // Pagination
        if (!empty($postData['length']) && $postData['length'] != -1) {
            $builder->limit($postData['length'], $postData['start']);
        }

        return $builder->get()->getResultArray();
    }

    public function countAll()
    {
        return $this->db->table('user')
            ->where('us_Status !=', 3)
            ->where('us_Role !=', 1) // Exclude admin
            ->countAllResults();
    }
    public function countFiltered()
    {
        $postData = service('request')->getPost();
        $searchValue = '';

        if (!empty($postData['search']['value'])) {
            $searchValue = $this->db->escapeLikeString(str_replace([" ", "\t"], '', $postData['search']['value']));
        }

        $builder = $this->db->table('user u');
        $builder->select('COUNT(*) as total');
        $builder->where('u.us_Status !=', 3);
        $builder->where('u.us_Role !=', 1); // Exclude admin

        if (!empty($searchValue)) {
            $builder->groupStart();
            $builder->where("REPLACE(REPLACE(u.us_Name, ' ', ''), CHAR(9), '') LIKE '%{$searchValue}%'");
            $builder->groupEnd();
        }

        return $builder->get()->getRow()->total;
    }


}

?>