<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Googlelogincallback extends Controller
{
    public function index()
    {
        helper('url');
        helper('session');

        // Load Google API
        require_once ROOTPATH . 'vendor/autoload.php';

        $client = new Google_Client(['client_id' => '89279377857-k55fvvqvtbk9nib9mc04jfsdgb9k00gn.apps.googleusercontent.com']);

        $request = service('request');
        $credential = $request->getPost('credential');

        if (!$credential) {
            return $this->response->setJSON(['error' => 'Missing credential']);
        }

        $payload = $client->verifyIdToken($credential);

        if ($payload) {
            $google_id = $payload['sub'];
            $email     = $payload['email'];
            $name      = $payload['name'];
            $picture   = $payload['picture'];

            session()->set([
                'google_id' => $google_id,
                'email'     => $email,
                'name'      => $name,
                'picture'   => $picture,
            ]);

            return redirect()->to('/'); // Or your landing page
        } else {
            return $this->response->setJSON(['error' => 'Invalid ID token']);
        }
        // return "Callback works!";
    }
}


// require_once 'vendor/autoload.php';
 
// $client = new Google_Client(['client_id' => '89279377857-k55fvvqvtbk9nib9mc04jfsdgb9k00gn.apps.googleusercontent.com']); // verify token
// $payload = $client->verifyIdToken($_POST['credential']);
 
// if ($payload) {
//     $google_id = $payload['sub'];
//     $email = $payload['email'];
//     $name = $payload['name'];
//     $picture = $payload['picture'];
 
//     // 🔒 Check if user exists in DB, otherwise insert
//     // Example: login or register the user
//     session_start();
//     $_SESSION['user_id'] = $google_id;
//     $_SESSION['email'] = $email;
//     $_SESSION['name'] = $name;
//     $_SESSION['picture'] = $picture;
 
//     // Redirect after login
//     header('Location: /dashboard.php');
// } else {
//     echo "Invalid ID Token";
// }