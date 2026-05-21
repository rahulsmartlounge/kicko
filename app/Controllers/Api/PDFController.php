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

            // Static company fields — always overridden regardless of payload
            $customerDetails['serviceSupport'] = '0471-4064545';
            $customerDetails['companyEmail']   = 'info@insidedesignindia.com';

            // Map payload items → PDF table rows
            $pdfItems = [];
            foreach ($items as $i => $item) {
                $item = is_object($item) ? (array)$item : (array)$item;
                $pdfItems[] = [
                    'isCategory'  => false,
                    'slNo'        => (string)($i + 1),
                    'description' => trim(
                        ($item['boxModelCode'] ?? '') .
                        (!empty($item['textureCode'])  ? ' | ' . $item['textureCode']  : '') .
                        (!empty($item['handleType'])   ? ' | ' . $item['handleType']   : '')
                    ),
                    'qty'         => $item['quantity'] ?? null,
                    'unit'        => $item['unit']     ?? 'Nos',
                    'rate'        => isset($item['rate'])   && is_numeric($item['rate'])   ? (float)$item['rate']   : null,
                    'amount'      => isset($item['amount']) && $item['amount'] !== ''      ? $item['amount']        : null,
                ];
            }

            // Extract optional totals from request
            $totals = property_exists($data, 'totals') ? (array)$data->totals : [];

            // Generate PDF using PDFService
            $pdfService = new PDFService();
            $pdfResult = $pdfService->generateKitchenExportPDF($pdfItems, $customerDetails, $totals);

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
