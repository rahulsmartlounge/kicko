<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class ApiSecretFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $secret = env('API_ESTIMATE_SECRET', '');

        if (empty($secret)) {
            // Secret not configured — block all requests to be safe
            return service('response')
                ->setStatusCode(500)
                ->setJSON(['status' => 'error', 'message' => 'API secret not configured on server']);
        }

        $provided = $request->getHeaderLine('X-Api-Key');

        if (empty($provided) || !hash_equals($secret, $provided)) {
            return service('response')
                ->setStatusCode(401)
                ->setJSON(['status' => 'error', 'message' => 'Unauthorized: invalid or missing X-Api-Key header']);
        }

        // Authenticated — continue
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // nothing
    }
}
