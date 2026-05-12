<?php
namespace App\Controllers;
use App\Models\BannerModel;

class Banner extends BaseController
{

    public function __construct()
    {
        $this->session = \Config\Services::session();
        $this->input = \Config\Services::request();
        $this->bannerModel = new BannerModel();
    }

    public function index()
    {
		 if (!$this->session->get('ad_uid')) {
				return redirect()->to(base_url('admin'));
			}
		$banner = $this->bannerModel->getAllBanners();
        $data['user'] = $banner;
        $template = view('common/header');
		$template.= view('common/leftmenu');
		$template.= view('banners',$data);
		$template.= view('common/footer');
		$template.= view('page_scripts/bannerjs');
        return $template;
    }
 
	public function updateStatus()
	{
		$theId = $this->request->getPost('the_Id');
        $newStatus = $this->request->getPost('the_Status');
        $bannerModel = new BannerModel();
        $theme = $bannerModel->getThemeByid($theId);   
        if (!$theme) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Theme Not Found'
            ]);
        }
        $update = $bannerModel->updateTheme($theId, ['the_Status' => $newStatus]);
        if ($update) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Status Updated Successfully!',
                'new_status' => $newStatus
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed To Update Status'
            ]);
        }
	}
     public function deleteBanner($the_id) {
		if ($the_id) {
			$modified_by = $this->session->get('ad_uid');
			$the_status = $this->bannerModel->deleteBannerById(3, $the_id, $modified_by);
			if ($the_status) {
				echo json_encode([
					'success' => true,
					'msg' => 'Banner Deleted Successfully.'
				]);
			} else {
				echo json_encode([
					'success' => false,
					'msg' => 'Failed To Delete Banner.'
				]);
			}
		} else {
			echo json_encode([
				'success' => false,
				'msg' => 'Invalid Request.'
			]);
		}
	}
	public function addbanner($the_id = null)
	{
		if (!$this->session->get('ad_uid')) 
		{
			return redirect()->to(base_url());
		}

		$data = [];
		 if ($the_id) {
			$banner = $this->bannerModel->getThemeByid($the_id);
		
			if (!$banner) {
				return redirect()->to('banner')->with('error', 'Banner not found');
			}
			 $data['banner'] = (array) $banner;
			
			// Load views
			$template = view('common/header');
			$template .= view('common/leftmenu');
			$template .= view('banner_add', $data);
			$template .= view('common/footer');
			$template .= view('page_scripts/bannerjs');
			return $template;
		}
		else
		{
			// Load views
			$template = view('common/header');
			$template .= view('common/leftmenu');
			$template .= view('banner_add');
			$template .= view('common/footer');
			$template .= view('page_scripts/bannerjs');
			return $template;
		}
		
	}
	public function createnew()
	{
		$bannerModel = new BannerModel();

		$the_id      = $this->request->getPost('the_id');
		$bannerName  = $this->request->getPost('file_name');
		$description = $this->request->getPost('description');
		$image       = $this->request->getFile('banner_image');
		$newName     = null;
		// Validate name format
		if (!preg_match('/^[a-zA-Z ]+$/', $bannerName)) {
			return $this->response->setJSON(['status' => 'error', 'msg' => 'Please Enter Name Correctly.']);
		}
		
		// If creating new banner (no ID), image is required
		if (empty($the_id) && (!$image || $image->getError() !== UPLOAD_ERR_OK)) {
			return $this->response->setJSON([
				'status' => 'error',
				'msg'    => 'Please Upload The Image.'
			]);
		}

		// Check if image is uploaded
		if ($image && $image->getError() == UPLOAD_ERR_OK && !$image->hasMoved()) {
			$newName = $image->getRandomName();
			$image->move(ROOTPATH . 'public/uploads', $newName);
		}
		if($bannerName && $image) {
		// Create
		if (empty($the_id)) {
			// $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
			$data = [
				'the_Name'         => $bannerName,
				'the_Description'  => $description,
				'the_Home_Banner'  => $newName ?? '',
				'the_Status'       => 1,
				'the_createdon'    => date("Y-m-d H:i:s"),
				'the_createdby'    => $this->session->get('ad_uid'),
				'the_modifyby'     => $this->session->get('ad_uid'),
			];

			$bannerModel->createBanner($data);

			return $this->response->setJSON([
				'status' => 1,
				'msg'    => 'Banner Uploaded Successfully.'
			]);
		} 
		
		// Update
		else {
			$existing = $bannerModel->getThemesByid($the_id);
			
				if (!$existing) {
				return $this->response->setJSON([
					'status' => 0,
					'msg'    => 'Banner Not Found For Update.'
				]);
			}
			
			
			if ($newName && !empty($existing->the_Home_Banner)) {
        $oldPath = ROOTPATH . 'public/uploads/' . $existing->the_Home_Banner;
        if (file_exists($oldPath)) {
            unlink($oldPath);
        }
    }


		

			$data = [
				'the_Name'         => $bannerName,
				'the_Description'  => $description,
				'the_modifyby'     => $this->session->get('ad_uid'),
				//'the_Home_Banner'  => $newName ?? $existing['the_Home_Banner']  // retain old name if no new image
			   	'the_Home_Banner' => $newName ?? $existing->the_Home_Banner

			];

			$bannerModel->modifyBanner($the_id, $data);

			return $this->response->setJSON([
				'status'   => 1,
				'msg'      => 'Banner Updated Successfully.',
				'redirect' => base_url('banner')
			]);
		}
		
	}
		else {
			return $this->response->setJSON([
				'status' => 'error',
				'msg' => 'All Mandatory Fields Are Required.'
			]);
		}
	}

/***************************************************************************************************/

public function ajaxList()
{
    $model = new BannerModel();
    $data = $model->getDatatables();
    $total = $model->countAll();
    $filtered = $model->countFiltered();

    $start = $this->request->getPost('start'); // DataTables offset

    foreach ($data as $key => &$row) {
        // Add serial number (DT_RowIndex)
        $row['DT_RowIndex'] = $start + $key + 1;

        // Default fallback
        $row['the_Name'] = $row['the_Name'] ?? 'N/A';

        // Image thumbnail
        if (!empty($row['the_Home_Banner'])) {
            $imgUrl = base_url('public/uploads/' . $row['the_Home_Banner']);
            $row['the_Home_Banner'] = '<img src="' . $imgUrl . '" alt="Banner" style="height: 40px;">';
        } else {
            $row['the_Home_Banner'] = '<span>No image</span>';
        }

        // Status toggle switch (checkbox using the_Status)
        $row['status_switch'] = '<div class="form-check form-switch">
            <input class="form-check-input checkactive"
                   type="checkbox"
                   id="statusSwitch-' . $row['the_Id'] . '"
                   value="' . $row['the_Status'] . '" ' . ($row['the_Status'] == 1 ? 'checked' : '') . '>
            <label class="form-check-label pl-0 label-check"
                   for="statusSwitch-' . $row['the_Id'] . '"></label>
        </div>';

        // Action buttons
        $row['actions'] = '<a href="' . base_url('banner/add/' . $row['the_Id']) . '">
                <i class="bi bi-pencil-square"></i>
            </a>&nbsp;
            <i class="bi bi-trash text-danger icon-clickable"
               onclick="confirmDelete(' . $row['the_Id'] . ')"></i>';
    }

    return $this->response->setJSON([
        'draw' => intval($this->request->getPost('draw')),
        'recordsTotal' => $total,
        'recordsFiltered' => $filtered,
        'data' => $data
    ]);
}


/***************************************************************************************************/

}

