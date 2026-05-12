<?php

namespace App\Controllers;

class ErrorWeb extends BaseController
{
    public function show404(): string
    {
        $uri = service('uri')->getSegment(1);

        if (strtolower($uri) === 'admin') {
            return view('Admin/custom_404');
        }

        return view('frontend/customweb404');
    }
}
