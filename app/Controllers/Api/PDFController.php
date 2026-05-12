<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Services\PDFService;
use CodeIgniter\HTTP\ResponseInterface;

class PDFController extends BaseController
{
    public function generate(): ResponseInterface
    {
        try {
            // Get JSON input
            $data = $this->request->getJSON();

            // Validate that items array exists and has at least 1 record
            if (!$data || !property_exists($data, 'items') || empty($data->items)) {
                return $this->response->setStatusCode(400)
                    ->setJSON([
                        'status' => 'error',
                        'message' => 'Validation failed',
                        'errors' => [
                            'items' => 'At least 1 record is mandatory'
                        ]
                    ]);
            }

            // Validate items is an array
            $items = is_array($data->items) ? $data->items : (array)$data->items;

            if (count($items) < 1) {
                return $this->response->setStatusCode(400)
                    ->setJSON([
                        'status' => 'error',
                        'message' => 'Validation failed',
                        'errors' => [
                            'items' => 'At least 1 record is mandatory'
                        ]
                    ]);
            }

            // Extract customer details from request
            $customerDetails = property_exists($data, 'customerDetails') ? (array)$data->customerDetails : [];

            // Extract optional totals from request
            $totals = property_exists($data, 'totals') ? (array)$data->totals : [];

            // Generate PDF using PDFService
            $pdfService = new PDFService();
            $pdfResult = $pdfService->generateKitchenExportPDF($items, $customerDetails, $totals);

            // Return success response
            return $this->response->setStatusCode(200)
                ->setJSON([
                    'status' => 'success',
                    'message' => 'PDF generated successfully',
                    'data' => [
                        'fileName' => $pdfResult['fileName'],
                        'downloadUrl' => $pdfResult['downloadUrl'],
                        'itemsCount' => $pdfResult['itemsCount'],
                        'customerDetails' => $pdfResult['customerDetails'],
                        'generatedAt' => $pdfResult['generatedAt'],
                        'items' => $items
                    ]
                ]);

        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)
                ->setJSON([
                    'status' => 'error',
                    'message' => 'An error occurred: ' . $e->getMessage()
                ]);
        }
    }
}
