<?php

namespace App\Controllers;

use App\Models\ProposalImageModel;
use CodeIgniter\HTTP\ResponseInterface;

class View360Controller extends BaseController
{
    public function index(int $id = 0): ResponseInterface|string
    {
        if ($id <= 0) {
            return $this->response->setStatusCode(404)->setBody('Image not found');
        }

        $image = (new ProposalImageModel())->find($id);

        if (!$image || (int)$image['status'] !== 1 || (int)$image['is_360'] !== 1) {
            return $this->response->setStatusCode(404)->setBody('360° image not found');
        }

        return view('view360', [
            'imageUrl' => base_url($image['file_path']),
        ]);
    }
}
