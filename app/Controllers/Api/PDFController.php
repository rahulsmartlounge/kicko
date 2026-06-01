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

            // Pre-load product cache for any productId references
            $productCache = [];
            $db = \Config\Database::connect();
            foreach ($items as $item) {
                $item = (array)$item;
                if (!empty($item['productId']) && is_numeric($item['productId'])) {
                    $pid = (int)$item['productId'];
                    if (!isset($productCache[$pid])) {
                        $row = $db->table('product')
                                  ->select('pr_Id, pr_Name, pr_Code, pr_Description, pr_Selling_Price, mrp')
                                  ->where('pr_Id', $pid)
                                  ->where('pr_Status !=', 3)
                                  ->get()->getRowArray();
                        $productCache[$pid] = $row ?: null;
                    }
                }
            }

            // Map payload items → PDF rows
            $pdfItems     = [];
            $autoSubTotal = 0.0;

            foreach ($items as $i => $item) {
                if (!is_object($item) && !is_array($item)) {
                    continue;
                }
                $item = (array)$item;

                // Category header row — pass through
                if (!empty($item['isCategory'])) {
                    $pdfItems[] = [
                        'isCategory'  => true,
                        'description' => trim((string)($item['description'] ?? '')),
                    ];
                    continue;
                }

                // Resolve product from DB if productId provided
                $product = null;
                if (!empty($item['productId']) && is_numeric($item['productId'])) {
                    $product = $productCache[(int)$item['productId']] ?? null;
                }

                // Description
                if ($product) {
                    $desc = trim($product['pr_Name'] ?? '');
                    if (!empty($product['pr_Code'])) {
                        $desc .= ' | ' . $product['pr_Code'];
                    }
                } else {
                    $desc = trim(
                        ($item['description'] ?? '') !== ''
                            ? ($item['description'] ?? '')
                            : ($item['boxModelCode'] ?? '')
                    );
                }
                // Append textureCode and handleType if present (regardless of product)
                if (!empty($item['textureCode'])) {
                    $desc .= ($desc !== '' ? ' | ' : '') . $item['textureCode'];
                }
                if (!empty($item['handleType'])) {
                    $desc .= ($desc !== '' ? ' | ' : '') . $item['handleType'];
                }

                // Rate — product price takes priority if not explicitly overridden
                $rate = null;
                if (isset($item['rate']) && is_numeric($item['rate'])) {
                    $rate = (float)$item['rate'];
                } elseif ($product && is_numeric($product['pr_Selling_Price'])) {
                    $rate = (float)$product['pr_Selling_Price'];
                }

                // Qty
                $qty = null;
                $rawQty = $item['qty'] ?? $item['quantity'] ?? null;
                if ($rawQty !== null && is_numeric($rawQty)) {
                    $qty = (float)$rawQty;
                }

                // Amount — explicit > auto-calculated from rate×qty
                $amount = null;
                if (isset($item['amount']) && $item['amount'] !== '') {
                    $amount = is_numeric($item['amount']) ? (float)$item['amount'] : (string)$item['amount'];
                } elseif ($rate !== null && $qty !== null) {
                    $amount = round($rate * $qty, 2);
                }

                if (is_numeric($amount)) {
                    $autoSubTotal += (float)$amount;
                }

                $pdfItems[] = [
                    'isCategory'  => false,
                    'slNo'        => isset($item['slNo']) ? (string)$item['slNo'] : (string)($i + 1),
                    'description' => $desc,
                    'qty'         => $qty,
                    'unit'        => $item['unit'] ?? 'Nos',
                    'textureCode' => isset($item['textureCode']) && $item['textureCode'] !== '' ? (string)$item['textureCode'] : null,
                    'handleType'  => isset($item['handleType'])  && $item['handleType']  !== '' ? (string)$item['handleType']  : null,
                    'rate'        => $rate,
                    'amount'      => $amount,
                ];
            }

            if (empty($pdfItems)) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status'  => 'error',
                    'message' => 'Validation failed',
                    'errors'  => ['items' => 'No valid items found after processing'],
                ]);
            }

            // Keep only non-monetary fields from payload totals (label, gst%, discount, words)
            // subTotal and grandTotal are always calculated from item amounts
            $payloadTotals = property_exists($data, 'totals') ? (array)$data->totals : [];
            $totals = [];

            if ($autoSubTotal > 0) {
                $gstPercent  = isset($payloadTotals['gstPercent'])  && is_numeric($payloadTotals['gstPercent'])  ? (float)$payloadTotals['gstPercent']  : null;
                $discount    = isset($payloadTotals['discount'])     && is_numeric($payloadTotals['discount'])    ? (float)$payloadTotals['discount']     : null;
                $gstAmount   = ($gstPercent !== null) ? round($autoSubTotal * $gstPercent / 100, 2) : 0;
                $afterGst    = $autoSubTotal + $gstAmount;
                $grandTotal  = ($discount !== null) ? $afterGst - $discount : $afterGst;

                $totals = [
                    'subTotalLabel'   => $payloadTotals['subTotalLabel']   ?? 'TOTAL',
                    'subTotal'        => $autoSubTotal,
                    'grandTotal'      => $grandTotal,
                    'grandTotalWords' => $payloadTotals['grandTotalWords'] ?? null,
                ];
                if ($gstPercent !== null) { $totals['gstPercent'] = $gstPercent; }
                if ($discount   !== null) { $totals['discount']   = $discount; }
            } elseif (!empty($payloadTotals)) {
                // No item amounts — pass payload totals as-is (manual entry)
                $totals = $payloadTotals;
            }

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
