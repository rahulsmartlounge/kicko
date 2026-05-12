<?php
namespace App\Models;

use CodeIgniter\Model;

class DashboardModel extends Model
{

      public function __construct() {
            $this->db = \Config\Database::connect();
        }


    

}
