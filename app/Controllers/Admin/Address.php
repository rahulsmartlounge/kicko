<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AddressModel;

class Address extends BaseController
{
    protected $addressModel;

    public function __construct()
    {
        $this->addressModel = new AddressModel();
    }

    // Show all addresses for the logged-in user
    public function index()
    {
        $userId = session()->get('ad_uid');
         if (!$this->session->get('ad_uid')) {
				return redirect()->to(base_url('admin'));
			}

        $addresses = $this->addressModel
            ->where('user_id', $userId)
            ->orderBy('is_default', 'DESC')
            ->findAll();

        return view('address/manage', ['addresses' => $addresses]);
    }

    // Add new address (GET + POST)
    public function add()
    {
         $userId = session()->get('ad_uid');
         if (!$this->session->get('ad_uid')) {
				return redirect()->to(base_url('admin'));
			}
        if ($this->request->getMethod() === 'post') {
            $data = [
                'user_id'       => session()->get('user_id'),
                'cust_Name'     => $this->request->getPost('cust_Name'),
                'address_line2' => $this->request->getPost('address_line2'),
                'city'          => $this->request->getPost('city'),
                'state'         => $this->request->getPost('state'),
                'zip'           => $this->request->getPost('zip'),
                'phone'         => $this->request->getPost('phone'),
                'is_default'    => 0
            ];

            $this->addressModel->insert($data);
            return redirect()->to(base_url('address'))->with('success', 'Address Added Successfully.');
        }

        return view('address/add');
    }

    // Edit address
    public function edit($id)
    {
         $userId = session()->get('ad_uid');
         if (!$this->session->get('ad_uid')) {
				return redirect()->to(base_url('admin'));
			}
        $address = $this->addressModel->find($id);

        if (!$address || $address['user_id'] != session()->get('user_id')) {
            return redirect()->to(base_url('address'))->with('error', 'Address Not Found.');
        }

        if ($this->request->getMethod() === 'post') {
            $data = [
                'cust_Name'     => $this->request->getPost('cust_Name'),
                'address_line2' => $this->request->getPost('address_line2'),
                'city'          => $this->request->getPost('city'),
                'state'         => $this->request->getPost('state'),
                'zip'           => $this->request->getPost('zip'),
                'phone'         => $this->request->getPost('phone')
            ];

            $this->addressModel->update($id, $data);
            return redirect()->to(base_url('address'))->with('success', 'Address Updated Successfully.');
        }

        return view('address/edit', ['address' => $address]);
    }

    // Delete address
    public function delete($id)
    {
        $address = $this->addressModel->find($id);

        if (!$address || $address['user_id'] != session()->get('user_id')) {
            return redirect()->to(base_url('address'))->with('error', 'Address Not Found.');
        }

        $this->addressModel->delete($id);
        return redirect()->to(base_url('address'))->with('success', 'Address Deleted Successfully.');
    }

    // Set as default address
    public function setDefault($id)
    {
        $userId = session()->get('user_id');
        $address = $this->addressModel->find($id);

        if (!$address || $address['user_id'] != $userId) {
            return redirect()->to(base_url('address'))->with('error', 'Address Not Found.');
        }

        // Unset existing default
        $this->addressModel->where('user_id', $userId)->set(['is_default' => 0])->update();

        // Set selected as default
        $this->addressModel->update($id, ['is_default' => 1]);

        return redirect()->to(base_url('address'))->with('success', 'Default Address Set Successfully.');
    }
}
