<?php
namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Models\Admin\CategoryModel;

class Category extends BaseController
{

    public function __construct()
    {
        $this->session = \Config\Services::session();
        $this->input = \Config\Services::request();
        $this->categoryModel = new \App\Models\Admin\CategoryModel();
    }

    public function index()
    {
         if (!$this->session->get('ad_uid')) {
				return redirect()->to(base_url('admin'));
			}

        $allcategory = $this->categoryModel->getAllCategory();
        $data['category'] =  $allcategory;
        // print_r($data['category']);
        // exit;
        $template = view('Admin/common/header');
		$template.= view('Admin/common/leftmenu');
		$template.= view('Admin/category', $data);
        $template.= view('Admin/common/footer');
        $template.= view('Admin/page_scripts/categoryjs');
        return $template;

        
    }
    public function addCategory($cat_id = null)
	{
		if (!$this->session->get('ad_uid')) 
		{
			return redirect()->to(base_url('admin/category'));
		}

		$data = [];
		 if ($cat_id) {
			$cate = $this->categoryModel->getCategoryByid($cat_id);
		
			if (!$cate) {
				return redirect()->to('admin/category')->with('error', 'Category Not Found');
			}
			
			 $data['category'] = (array) $cate;
			
			
			$template = view('Admin/common/header');
			$template .= view('Admin/common/leftmenu');
			$template .= view('Admin/category_add', $data);
			$template .= view('Admin/common/footer');
			$template .= view('Admin/page_scripts/categoryjs');
			return $template;
		}
		else
		{
			$template = view('Admin/common/header');
			$template .= view('Admin/common/leftmenu');
			$template .= view('Admin/category_add');
			$template .= view('Admin/common/footer');
			$template .= view('Admin/page_scripts/categoryjs');
			return $template;
		}
		
	}

    public function saveCategory() {
$cat_id = $this->input->getPost('cat_id');
$category_name = $this->input->getPost('category_name');
$category_name = ucwords(strtolower(trim($category_name)));
$discount_value = $this->input->getPost('discount_value');
$discount_type = $this->input->getPost('discount_type');



// Check if category exists
if ($category_name) {
        // Example: validate format
    if (!preg_match('/^[a-zA-Z0-9 _-]+$/', $category_name)) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Invalid Category Name.'
        ]);
    }
    $exists = $this->categoryModel->isCategoryExists($category_name, $cat_id);
    if ($exists) {
        return $this->response->setJSON([
            'status' => 'error',
            'field' => 'category_name',
            'message' => 'Category Name Already Exists.'
        ]);
    }



        $data = [
            'cat_Name' => $category_name,
            'cat_Discount_Value' => $discount_value,
            'cat_Discount_Type' => $discount_type,
            'cat_Status' => 1,
            'cat_createdon' => date("Y-m-d H:i:s"),
            'cat_createdby' => $this->session->get('ad_uid'),
            'cat_modifyby' => $this->session->get('ad_uid'),
        ];

        if (empty($cat_id)) {
            $CreateCategory = $this->categoryModel->categoryInsert($data);
            return $this->response->setJSON([
                "status" => 1,
                "msg" => "Category Created Successfully.",
                "redirect" => base_url('category')
            ]);
        } else {
            $modifyCategory = $this->categoryModel->updateCategory($cat_id, $data);
            return $this->response->setJSON([
                "status" => 1,
                "msg" => "Category Updated Successfully.",
                "redirect" => base_url('admin/category')
            ]);
        }
    } else {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'All Fields Are Required.'
        ]);
    }
}

    public function changeStatus()
    {
        $catId = $this->request->getPost('cat_Id');
        $newStatus = $this->request->getPost('cat_Status');
        
        $categoryModel =  new \App\Models\Admin\CategoryModel();
        $category = $categoryModel->getCategoryByid($catId);

        //$productModel = new \App\Models\Admin\ProductModel();
	    //$product = $productModel->getProductByid($catId);
        
        if (!$category) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Category Not Found'
            ]);
        }
    
        $update = $categoryModel->updateCategory($catId, ['cat_Status' => $newStatus]);
    
        if ($update) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Category Status Updated Successfully!',
                'new_status' => $newStatus
            ]);
        } else {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Category Status Updated Successfully!',
                'new_status' => $newStatus
            ]);
        }
    }
    
    //Category Delete

	public function deleteCategory($cat_id)
	{
		if ($cat_id) {
			$modified_by = $this->session->get('ad_uid');
			$cat_delete = $this->categoryModel->deleteCategoryById( $cat_id, $modified_by);
			if ($cat_delete) {
				return $this->response->setJSON([
					'status' => 1,
					'message' => 'Category Deleted Successfully.'
				]);
			} else {
				return $this->response->setJSON([
					'status' => 0,
					'message' => '<b>Unable To Delete!</b> <br> There Exist a Product or Subcategory Under The Category.'
				]);
			}
		} else {
			return $this->response->setJSON([
				'status' => 0,
				'message' => 'Invalid Category ID.'
			]);
		}
	}
     
	

		
	// Listing table data
	
	public function ajaxList()
	{
	$model = new \App\Models\Admin\CategoryModel();
	$data = $model->getDatatables();
	$total = $model->countAll();
	$filtered = $model->countFiltered();

	foreach ($data as &$row) {
		// Default fallbacks
		$row['cat_Name'] = $row['cat_Name'] ?? 'N/A';
		$row['cat_Discount_Value'] = $row['cat_Discount_Value'] ?? 'N/A';
		$row['cat_Discount_Type'] = $row['cat_Discount_Type'] ?? 'N/A';
		
	
		
		// Status toggle switch
		$row['status_switch'] = '<div class="form-check form-switch">
          <input class="form-check-input checkactive"
           type="checkbox"
           id="statusSwitch-' . $row['cat_Id'] . '"
           value="' . $row['cat_Id'] . '" ' . ($row['cat_Status'] == 1 ? 'checked' : '') . '>
          <label class="form-check-label pl-0 label-check"
           for="statusSwitch-' . $row['cat_Id'] . '"></label>
           </div>';


		// Action buttons
		$row['actions'] = '<a href="' . base_url('admin/category/edit/' . $row['cat_Id']) . '">
				<i class="bi bi-pencil-square"></i>
			</a>&nbsp;
			<i class="bi bi-trash text-danger icon-clickable"
			   onclick="confirmDelete(' . $row['cat_Id'] . ')"></i>';
	}
	
	return $this->response->setJSON([
		'draw' => intval($this->request->getPost('draw')),
		'recordsTotal' => $total,
		'recordsFiltered' => $filtered,
		'data' => $data
	]);
	}


}