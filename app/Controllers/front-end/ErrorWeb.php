<?php


namespace App\Controllers;
use App\Controllers\BaseController;

class ErrorWeb extends BaseController
{
   public function show404(): string
    {
        $uri = service('uri')->getSegment(1); // get first URL segment

        // Check if it's an admin URL
        if (strtolower($uri) === 'admin') {
            return view('Admin/custom_404');
        }

        // Otherwise, show web 404
        return view('customweb404');
    }
	
}
