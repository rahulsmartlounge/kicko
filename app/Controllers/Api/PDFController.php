<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\ProposalModel;
use App\Models\ProposalItemModel;
use App\Services\PDFService;
use CodeIgniter\HTTP\ResponseInterface;

class PDFController extends BaseController
{
    public function generate(): ResponseInterface
    {
        try {
            $data = $this->request->getJSON();

            if (!$data) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status'  => 'error',
                    'message' => 'Invalid or empty JSON body',
                ]);
            }

            if (!property_exists($data, 'items') || empty($data->items)) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status'  => 'error',
                    'message' => 'Validation failed',
                    'errors'  => ['items' => 'At least 1 item is mandatory'],
                ]);
            }

            $items = is_array($data->items) ? $data->items : (array)$data->items;

            // Customer details
            $customerDetails = property_exists($data, 'customerDetails') ? (array)$data->customerDetails : [];

            // Static company fields
            $customerDetails['serviceSupport'] = '0471-4064545';
            $customerDetails['companyEmail']   = 'info@insidedesignindia.com';

            // Location fields — accept from top-level payload OR inside customerDetails
            $rawLocation = isset($data->location)
                ? trim((string)$data->location)
                : (isset($customerDetails['location']) ? trim((string)$customerDetails['location']) : '');
            $location = $rawLocation !== '' ? $rawLocation : null;

            $latitude = null;
            if (isset($data->latitude) && is_numeric($data->latitude)) {
                $latitude = (float)$data->latitude;
            } elseif (isset($customerDetails['latitude']) && is_numeric($customerDetails['latitude'])) {
                $latitude = (float)$customerDetails['latitude'];
            }

            $longitude = null;
            if (isset($data->longitude) && is_numeric($data->longitude)) {
                $longitude = (float)$data->longitude;
            } elseif (isset($customerDetails['longitude']) && is_numeric($customerDetails['longitude'])) {
                $longitude = (float)$customerDetails['longitude'];
            }

            // Embed into customerDetails so PDFService can render them
            $customerDetails['location']  = $location;
            $customerDetails['latitude']  = $latitude;
            $customerDetails['longitude'] = $longitude;

            // Map payload items → PDF rows
            $pdfItems = [];
            foreach ($items as $i => $item) {
                if (!is_object($item) && !is_array($item)) {
                    continue; // skip malformed entries silently
                }
                $item       = (array)$item;
                $pdfItems[] = [
                    'isCategory'  => !empty($item['isCategory']),
                    'slNo'        => isset($item['slNo']) ? (string)$item['slNo'] : (string)($i + 1),
                    'description' => trim(
                        ($item['description'] ?? '') !== ''
                            ? ($item['description'] ?? '')
                            : ($item['boxModelCode'] ?? '') .
                              (!empty($item['textureCode']) ? ' | ' . $item['textureCode'] : '') .
                              (!empty($item['handleType'])  ? ' | ' . $item['handleType']  : '')
                    ),
                    'qty'    => $item['qty'] ?? $item['quantity'] ?? null,
                    'unit'   => $item['unit']   ?? 'Nos',
                    'rate'   => isset($item['rate'])   && is_numeric($item['rate'])   ? (float)$item['rate']   : null,
                    'amount' => isset($item['amount']) && $item['amount'] !== ''      ? $item['amount']        : null,
                ];
            }

            if (empty($pdfItems)) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status'  => 'error',
                    'message' => 'Validation failed',
                    'errors'  => ['items' => 'No valid items found after processing'],
                ]);
            }

            $totals = property_exists($data, 'totals') ? (array)$data->totals : [];

            // Generate PDF
            $pdfService = new PDFService();
            $pdfResult  = $pdfService->generateKitchenExportPDF($pdfItems, $customerDetails, $totals);

            // Parse date d-m-Y → Y-m-d
            $rawDate  = $customerDetails['dateOfProposal'] ?? date('d-m-Y');
            $propDate = \DateTime::createFromFormat('d-m-Y', $rawDate);
            $dbDate   = $propDate ? $propDate->format('Y-m-d') : date('Y-m-d');

            // Save proposal header
            $proposalModel = new ProposalModel();
            $proposalId    = $proposalModel->insert([
                'name'              => $customerDetails['name']           ?? null,
                'address'           => $customerDetails['address']        ?? null,
                'phone'             => $customerDetails['phone']          ?? null,
                'email'             => $customerDetails['email']          ?? null,
                'project_number'    => $customerDetails['projectNumber']  ?? null,
                'date_of_proposal'  => $dbDate,
                'sales_manager'     => $customerDetails['salesManager']   ?? null,
                'service_support'   => $customerDetails['serviceSupport'] ?? null,
                'referred_by'       => $customerDetails['referredBy']     ?? null,
                'location'          => $location,
                'latitude'          => $latitude,
                'longitude'         => $longitude,
                'sub_total_label'   => $totals['subTotalLabel']           ?? null,
                'sub_total'         => $totals['subTotal']                ?? null,
                'gst_percent'       => $totals['gstPercent']              ?? null,
                'discount'          => $totals['discount']                ?? null,
                'grand_total'       => $totals['grandTotal']              ?? null,
                'grand_total_words' => $totals['grandTotalWords']         ?? null,
                'pdf_file_name'     => $pdfResult['fileName'],
                'pdf_url'           => $pdfResult['downloadUrl'],
                'status'            => 1,
                'created_at'        => date('Y-m-d H:i:s'),
            ]);

            if ($proposalId === false) {
                log_message('error', 'PDFController::generate — ProposalModel::insert failed. Errors: ' . json_encode($proposalModel->errors()));
                return $this->response->setStatusCode(500)->setJSON([
                    'status'  => 'error',
                    'message' => 'PDF generated but failed to save order record',
                    'errors'  => $proposalModel->errors(),
                ]);
            }

            // Save line items
            $itemModel = new ProposalItemModel();
            if (!$itemModel->insertItems((int)$proposalId, $pdfItems)) {
                log_message('warning', "PDFController::generate — insertItems failed for proposal #{$proposalId}");
            }

            return $this->response->setStatusCode(200)->setJSON([
                'status'  => 'success',
                'message' => 'PDF generated successfully',
                'data'    => [
                    'proposalId'      => $proposalId,
                    'fileName'        => $pdfResult['fileName'],
                    'downloadUrl'     => $pdfResult['downloadUrl'],
                    'itemsCount'      => $pdfResult['itemsCount'],
                    'customerDetails' => $pdfResult['customerDetails'],
                    'generatedAt'     => $pdfResult['generatedAt'],
                    'items'           => $items,
                ],
            ]);

        } catch (\Throwable $e) {
            log_message('error', 'PDFController::generate — ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());

            $isDev = in_array(ENVIRONMENT, ['development', 'testing'], true);
            return $this->response->setStatusCode(500)->setJSON([
                'status'  => 'error',
                'message' => $isDev ? $e->getMessage() : 'An unexpected error occurred. Please try again.',
                ...($isDev ? ['trace' => $e->getFile() . ':' . $e->getLine()] : []),
            ]);
        }
    }
}
