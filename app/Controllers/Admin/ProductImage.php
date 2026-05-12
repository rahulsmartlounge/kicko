<?php
namespace App\Controllers;
use App\Models\ProductImageModel;

class ProductImage extends BaseController
{

    public function __construct()
    {
        $this->session = \Config\Services::session();
        $this->input = \Config\Services::request();
        $this->productimageModel = new ProductImageModel();
    }

    public function index()
    {
		 if (!$this->session->get('ad_uid')) {
           return redirect()->to(base_url('admin'));
    }

        $allproductimages = $this->productimageModel->getAllProductImages();
        $data['productimages'] =  $allproductimages;
        $template = view('common/header');
		$template.= view('common/leftmenu');
		$template.= view('productimage', $data );
		$template.= view('common/footer');
        return $template;

    }
    public function addProductImage($pri_id = null)
    {
        if (!$this->session->get('ad_uid')) {
            return redirect()->to(base_url());
        }
    
        $data = [];
        $data['products'] = $this->productimageModel->getAllProducts();
        
        $template = view('common/header');
        $template .= view('common/leftmenu');
        $template .= view('productimage_add',$data); 
        $template .= view('common/footer');
        $template .= view('page_scripts/productimagejs');
        return $template;
    }



    public function saveProductImage()
    {
        $pr_id = $this->request->getPost('pr_id');
        $file_type = $this->request->getPost('file_type');
        $files = $this->request->getFiles();
    
        if (!$pr_id || empty($files['media_files'])) {
            return $this->response->setJSON(['status' => 0, 'msg' => 'Product And Files Are Required.']);
        }
    
        $mediaFiles = $files['media_files'];
        $uploadData = [];
    
        if (is_array($mediaFiles)) {
            foreach ($mediaFiles as $file) {
                if ($file->isValid() && !$file->hasMoved()) {
                    $newName = $file->getRandomName();
                    $file->move(FCPATH . 'uploads/productmedia/', $newName);
    
                    $uploadData[] = [
                        'name' => $newName,
                        'type' => $file_type
                    ];
                }
            }
        }
    
        if (!empty($uploadData)) {
            $data = [
                'pr_id' => $pr_id,
                'pri_File_Type'=> $file_type,
                'pri_Thumbnail' => json_encode($uploadData),
                'pri_Status' =>1,
                'pri_createdon' => date('Y-m-d H:i:s'),
                'pri_createdby' => $this->session->get('ad_uid')
            ];
    
            $this->productimageModel->productimageInsert($data);
    
            return $this->response->setJSON(['status' => 1, 'msg' => 'Media Uploaded Successfully.']);
        } else {
            return $this->response->setJSON(['status' => 0, 'msg' => 'No Valid Files Uploaded.']);
        }
    }
    
    
}