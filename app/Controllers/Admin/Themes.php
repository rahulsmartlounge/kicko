<?php
namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Models\Admin\Theme_Model;

class Themes extends BaseController
{

    public function __construct()
    {
        $this->session = \Config\Services::session();
        $this->input = \Config\Services::request();
        $this->theme_Model = new \App\Models\Admin\Theme_Model();
    }

    public function index()
    {
        if (!$this->session->get('ad_uid')) {
            return redirect()->to(base_url('admin'));
        }
        $banner = $this->theme_Model->getAllBanners();
        $data['user'] = $banner;
        $template = view('Admin/common/header');
        $template .= view('Admin/common/leftmenu');
        $template .= view('Admin/themes', $data);
        $template .= view('Admin/common/footer');
        $template .= view('Admin/page_scripts/themejs');
        return $template;
    }
    public function fetch_theme()
    {
        if (!$this->session->get('ad_uid')) {
            return redirect()->to(base_url('admin'));
        }
        $themes = $this->theme_Model->fetchTheme();
    }
    public function updateStatus()
    {

        $themeId = $this->request->getPost('theme_Id');
        $newStatus = $this->request->getPost('theme_Status');
        $theme_Model = new \App\Models\Admin\Theme_Model();
        $themes = $theme_Model->getUpdateAllStatus();
        // Validate input
        if (!$themeId || !in_array($newStatus, [1, 2])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request.'
            ]);
        }

        // Check if theme exists
        //$theme = $theme_Model->getThemeStatusByid($themeId);

        /* 		if (!$theme) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Theme not found.'
                    ]);
                }

                // If setting to active, deactivate all other themes
                if ($newStatus == 1) {
                    $theme_Model->deactivateAllThemesExcept($themeId);
                } */

        // Update current theme's status
        $update = $theme_Model->updateTheme($themeId, ['theme_Status' => $newStatus]);

        return $this->response->setJSON([
            'success' => $update,
            'message' => $update ? 'Status updated successfully.' : 'Failed to update status.',
            'new_status' => $newStatus
        ]);
    }


    public function deleteBanner($theme_id)
    {
        if ($theme_id) {
            $modified_by = $this->session->get('ad_uid');
            $theme_Status = $this->theme_Model->deleteBannerById(3, $theme_id, $modified_by);
            if ($theme_Status) {
                echo json_encode([
                    'success' => true,
                    'msg' => 'Banner deleted successfully.'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'msg' => 'Failed to delete banner.'
                ]);
            }
        } else {
            echo json_encode([
                'success' => false,
                'msg' => 'Invalid request.'
            ]);
        }
    }
    public function addbanner($theme_id = null)
    {
        if (!$this->session->get('ad_uid')) {
            return redirect()->to(base_url('admin'));
        }

        $data = [];

        if ($theme_id) {
            $banner = $this->theme_Model->getThemeByid($theme_id);

            if (!$banner) {
                return redirect()->to('admin/themes')->with('error', 'Banner not found');
            }

            // Decode section JSON data
            $banner = (array) $banner;
            $banner['theme_Section1'] = json_decode($banner['theme_Section1'], true) ?? [];
            $banner['theme_Section2'] = json_decode($banner['theme_Section2'], true) ?? [];
            $banner['theme_Section3'] = json_decode($banner['theme_Section3'], true) ?? [];

            $data['banner'] = $banner;
        }
        //print_r($data);exit;

        // Load views
        $template = view('Admin/common/header');
        $template .= view('Admin/common/leftmenu');
        $template .= view('Admin/themes_add', $data); // Pass $banner data if editing
        $template .= view('Admin/common/footer');
        $template .= view('Admin/page_scripts/themejs');
        return $template;
    }

    public function save_file()
    {
        $theme_Model = new Theme_Model();
        $theme_id = $this->request->getPost('theme_id');
        $mainData = $this->request->getPost();
        $files = $this->request->getFiles();

        //$mainData['theme_name'] = trim($mainData['theme_name'] ?? '');
        //$mainData['description'] = trim($mainData['description'] ?? '');

        $mainData['theme_name'] = ucwords(strtolower(trim($mainData['theme_name'] ?? '')));
        $mainData['description'] = ucfirst(strtolower(trim($mainData['description'] ?? '')));

        $errors = [];

        // Validation - Mandatory Fields
        if (empty($mainData['theme_name']) || empty($mainData['description'])) {
            return $this->response->setJSON([
                'status' => 0,
                'msg' => 'Mandatory Fields are Required.'
            ]);
        }
        if (empty($errors)) {
            // Validate theme_name - only letters and spaces
            if (!preg_match('/^[a-zA-Z\s]+$/', $mainData['theme_name'])) {
                return $this->response->setJSON([
                    'status' => 0,
                    'msg' => 'Theme name must contain only letters and spaces.'
                ]);
            }

            // Validate description - alphanumeric and basic punctuation
            if (!preg_match('/^[a-zA-Z0-9\s,\.\'\"\\\\;: ]+$/', $mainData['description'])) {
                return $this->response->setJSON([
                    'status' => 0,
                    'msg' => 'Please enter Description correctly.'
                ]);
            }

        }

        // Helper function to validate image dimensions
        $validateDimensions = function ($file, $minWidth, $minHeight, $maxWidth, $maxHeight, &$errors, $section, $index) {
            if ($file->isValid() && !$file->hasMoved()) {
                $tempPath = $file->getTempName();
                [$width, $height] = getimagesize($tempPath);
                if ($width < $minWidth || $width > $maxWidth || $height < $minHeight || $height > $maxHeight) {
                    $errors[] = "$section image " . ($index + 1) . " dimensions must be between {$minWidth}x{$minHeight} and {$maxWidth}x{$maxHeight} pixels. Uploaded image is {$width}x{$height}.";
                    return false;
                }
                return true;
            }
            return false;
        };

        // Helper function to move & resize image
        $uploadAndResize = function ($file, $resizeWidth, $resizeHeight, $path) {
            $newName = $file->getRandomName();
            $file->move($path, $newName);
            \Config\Services::image()
                ->withFile($path . $newName)
                ->resize($resizeWidth, $resizeHeight, false)
                ->save($path . $newName);
            return $newName;
        };

        $uploadPath = ROOTPATH . 'public/uploads/themes/';
        helper('url'); // Ensure base_url() is available

        $baseUrl = base_url(); // Get your site base URL

        function isValidBaseLink($link, $baseUrl)
        {
            return strpos($link, $baseUrl) === 0;
        }


        // SECTION 1 (1200x300 to 1300x400, resize to 1300x400)
        $section1 = [];
        if (isset($files['section1_image'])) {
            foreach ($files['section1_image'] as $i => $file) {
                // Validate and Upload Image
                if ($file->isValid() && !$file->hasMoved()) {
                    if (!$validateDimensions($file, 1200, 300, 1300, 400, $errors, 'Banner', $i)) {
                        return $this->response->setJSON([
                            'status' => 0,
                            'msg' => "Main Banner Image " . ($i + 1) . " Dimensions Must be Between 1200x300 and 1300x400 Pixels."
                        ]);
                    }
                    $section1[$i]['image'] = $uploadAndResize($file, 1300, 400, $uploadPath);
                } else {
                    $section1[$i]['image'] = $mainData['section1_image_old'][$i] ?? null;
                }

                // Validate Link
                $link = $mainData['section1_link'][$i] ?? null;
                if (!empty($link) && !isValidBaseLink($link, $baseUrl)) {
                    return $this->response->setJSON([
                        'status' => 0,
                        'msg' => 'Please Provide an Internal Link Main Banner section ' . ($i + 1) . '.'
                    ]);
                }
                $section1[$i]['link'] = $link;
            }
        }



        // SECTION 2 (300x200 to 400x300, resize to 400x300)
        $section2 = [];
        if (isset($files['section2_image'])) {
            foreach ($files['section2_image'] as $i => $file) {
                // Validate and Upload Image
                if ($file->isValid() && !$file->hasMoved()) {
                    if (!$validateDimensions($file, 300, 200, 400, 300, $errors, 'Offer', $i)) {
                        return $this->response->setJSON([
                            'status' => 0,
                            'msg' => "Offer Image " . ($i + 1) . " Dimensions Must be Between 300x200 and 400x300 Pixels."
                        ]);
                    }
                    $section2[$i]['image'] = $uploadAndResize($file, 400, 300, $uploadPath);
                } else {
                    $section2[$i]['image'] = $mainData['section2_image_old'][$i] ?? null;
                }

                // Validate Name & Link
                $section2[$i]['name'] = $mainData['section2_name'][$i] ?? null;
                $link = $mainData['section2_link'][$i] ?? null;
                if (!empty($link) && !isValidBaseLink($link, $baseUrl)) {
                    return $this->response->setJSON([
                        'status' => 0,
                        'msg' => 'Please Provide an Internal Link Offer Image section ' . ($i + 1) . '.'
                    ]);
                }
                $section2[$i]['link'] = $link;
            }
        }



        // SECTION 3 (1300x400 to 1400x500, resize to 1350x400)
        $section3 = [];
        if (isset($files['section3_image'])) {
            foreach ($files['section3_image'] as $i => $file) {
                // Validate and Upload Image
                if ($file->isValid() && !$file->hasMoved()) {
                    if (!$validateDimensions($file, 1200, 400, 1400, 500, $errors, 'Bottom Banner', $i)) {
                        return $this->response->setJSON([
                            'status' => 0,
                            'msg' => "Bottom Banner Image " . ($i + 1) . " Dimensions Must be Between 1200x400 and 1400x500 pixels."
                        ]);
                    }
                    $section3[$i]['image'] = $uploadAndResize($file, 1400, 500, $uploadPath);
                } else {
                    $section3[$i]['image'] = $mainData['section3_image_old'][$i] ?? null;
                }

                // Validate Name & Link
                $section3[$i]['name'] = $mainData['section3_name'][$i] ?? null;
                $link = $mainData['section3_link'][$i] ?? null;
                if (!empty($link) && !isValidBaseLink($link, $baseUrl)) {
                    return $this->response->setJSON([
                        'status' => 0,
                        'msg' => 'Please Provide an Internal Link Bottom Banner section ' . ($i + 1) . '.'
                    ]);
                }
                $section3[$i]['link'] = $link;
            }
        }


        // Prepare data for saving
        $data = [
            'theme_Name' => $mainData['theme_name'],
            'theme_Description' => $mainData['description'],
            'theme_Section1' => json_encode($section1),
            'theme_Section2' => json_encode($section2),
            'theme_Section3' => json_encode($section3),
            'theme_Status' => 1,
            'theme_modifyby' => $this->session->get('ad_uid'),
            'theme_modifyon' => date('Y-m-d H:i:s'),
        ];
        $themeSection1 = json_decode($mainData['theme_Section1'], true);
        $themeSection2 = json_decode($mainData['theme_Section2'], true);
        $themeSection3 = json_decode($mainData['theme_Section3'], true);


        if (empty($theme_id)) {

            if (empty($themeSection1[0]['image']) || empty($themeSection2[0]['image']) || empty($themeSection3[0]['image'])) {
                return $this->response->setJSON([
                    'status' => 0,
                    'msg' => 'Mandatory Fields are Required.'
                ]);
            }
            $section2ImageCount = 0;
            if (!empty($themeSection2[0]['image'])) {
                foreach ($themeSection2 as $item) {
                    if (!empty($item['image'])) {
                        $section2ImageCount++;
                    }
                }
            }


            if ($section2ImageCount < 3) {
                return $this->response->setJSON([
                    'status' => 0,
                    'msg' => 'Atleast Three Offer Images Required .'
                ]);
            }
            // echo $section2ImageCount;exit();

            $data['theme_createdon'] = date('Y-m-d H:i:s');
            $data['theme_createdby'] = $this->session->get('ad_uid');
            $this->theme_Model->insert_data($data);

            $themeId = $this->theme_Model->insertID();
            $this->theme_Model->deactivateAllThemesExcept($themeId);

            return $this->response->setJSON([
                'status' => 1,
                'msg' => 'Theme created successfully.'
            ]);
        } else {
            // Update existing
            $existing = $this->theme_Model->getThemeByid($theme_id);
            if (!$existing) {
                return $this->response->setJSON([
                    'status' => 0,
                    'msg' => 'Theme not found.'
                ]);
            }
            $themeSection1 = $section1;
            $themeSection2 = $section2;
            $themeSection3 = $section3;
            // ✅ Validate mandatory image presence in update
            if (empty($themeSection1[0]['image']) || empty($themeSection2[0]['image']) || empty($themeSection3[0]['image'])) {
                return $this->response->setJSON([
                    'status' => 0,
                    'msg' => 'Mandatory Fields are Required.'
                ]);
            }

            // ✅ Validate at least 3 offer images in Section 2 (for update)
            $section2ImageCount = 0;
            foreach ($themeSection2 as $item) {
                if (!empty($item['image'])) {
                    $section2ImageCount++;
                }
            }

            if ($section2ImageCount < 3) {
                return $this->response->setJSON([
                    'status' => 0,
                    'msg' => 'Atleast Three Offer Images Required.'
                ]);
            }

            $this->theme_Model->modifyThemes($theme_id, $data);
            $this->theme_Model->deactivateAllThemesExcept($theme_id);

            return $this->response->setJSON([
                'status' => 1,
                'msg' => 'Theme updated successfully.',
                'redirect' => base_url('admin/themes')
            ]);
        }
    }
    /***************************************************************************************************/

    public function ajaxList()
    {
        $model = new \App\Models\Admin\Theme_Model();
        $data = $model->getDatatables();
        $total = $model->countAll();
        $filtered = $model->countFiltered();

        $start = $this->request->getPost('start'); // DataTables offset

        foreach ($data as $key => &$row) {
            // Add serial number (DT_RowIndex)
            $row['DT_RowIndex'] = $start + $key + 1;

            // Default fallback
            $row['theme_Name'] = $row['theme_Name'] ?? 'N/A';
            $row['theme_Description'] = $row['theme_Description'] ?? 'N/A';

            // Status toggle switch (checkbox using theme_Status)
            $row['status_switch'] = '
		<div class="form-check form-switch">
			<input class="form-check-input checkactive"
				   type="checkbox"
				   id="statusSwitch-' . $row['theme_Id'] . '"
				   value="' . $row['theme_Id'] . '" ' . ($row['theme_Status'] == 1 ? 'checked' : '') . '>
			<label class="form-check-label pl-0 label-check"
				   for="statusSwitch-' . $row['theme_Id'] . '"></label>
		</div>';

            // Action buttons
            $row['actions'] = '<a href="' . base_url('admin/themes/add/' . $row['theme_Id']) . '">
					<i class="bi bi-pencil-square"></i>
				</a>&nbsp;
				<i class="bi bi-trash text-danger icon-clickable"
				   onclick="confirmDelete(' . $row['theme_Id'] . ')"></i>';
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

