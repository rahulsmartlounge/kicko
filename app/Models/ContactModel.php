<?php
namespace App\Models;
use CodeIgniter\Model;

class ContactModel extends Model {
    protected $table = 'contact_enquiries';
    protected $primaryKey = 'id';
    protected $allowedFields = ['fullname', 'email', 'contact_no', 'message', 'submitted_at'];
    public $timestamps = false;
}