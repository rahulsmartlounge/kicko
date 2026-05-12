<?php

namespace App\Services;

use TCPDF;

class KicoPDF extends TCPDF
{
    public bool $printPageFooter = true;

    public function Footer(): void
    {
        if (!$this->printPageFooter) {
            return;
        }
        $this->SetY(-10);
        $this->SetFont('helvetica', '', 7);
        $this->SetTextColor(80, 80, 80);
        $this->Cell(60,  5, 'For INSIDE Design..................', 0, 0, 'L');
        $this->Cell(60,  5, 'Page ' . $this->getAliasNumPage() . ' of ' . $this->getAliasNbPages(), 0, 0, 'C');
        $this->Cell(60,  5, 'For Customer.....................', 0, 1, 'R');
    }
}

class PDFService
{
    private string $firstPageImage = '';
    private string $lastPageImage  = '';

    // A4 usable width: 210 - 15 (left) - 15 (right) = 180 mm
    private const PAGE_W    = 210;
    private const PAGE_H    = 297;
    private const MARGIN    = 15;
    private const USABLE_W  = 180;  // 210 - 15 - 15

    public function __construct()
    {
        $this->firstPageImage = FCPATH . 'public/images/50.jpg';
        $this->lastPageImage  = FCPATH . 'public/images/51.jpg';
    }

    public function setFirstPageImage(string $imagePath): self
    {
        if (file_exists($imagePath)) { $this->firstPageImage = $imagePath; }
        return $this;
    }

    public function setLastPageImage(string $imagePath): self
    {
        if (file_exists($imagePath)) { $this->lastPageImage = $imagePath; }
        return $this;
    }

    public function getFirstPageImage(): string { return $this->firstPageImage; }
    public function getLastPageImage(): string  { return $this->lastPageImage;  }

    // ─────────────────────────────────────────────────────────────────────────
    // Public entry point
    // ─────────────────────────────────────────────────────────────────────────

    public function generateKitchenExportPDF(
        array $items,
        array $customerDetails = [],
        array $totals = []
    ): array {
        if (empty($items)) {
            throw new \Exception('Items array cannot be empty');
        }

        $validatedItems = $this->validateItems($items);

        $pdf = new KicoPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator('Kitchen Export System');
        $pdf->SetAuthor('KICO');
        $pdf->SetTitle('Kitchen Export PDF');
        $pdf->SetMargins(self::MARGIN, self::MARGIN, self::MARGIN);
        $pdf->SetAutoPageBreak(true, self::MARGIN);
        $pdf->setPrintHeader(false);

        // Page 1 – cover (no footer)
        $pdf->printPageFooter = false;
        $pdf->AddPage();
        if (!empty($this->firstPageImage) && file_exists($this->firstPageImage)) {
            $this->addFullPageImage($pdf, $this->firstPageImage);
        }

        // Page 2+ – content (footer on)
        $pdf->printPageFooter = true;
        $pdf->AddPage();
        $this->addCustomerDetailsBlock($pdf, $customerDetails);
        $this->addItemsTable($pdf, $validatedItems);
        if (!empty($totals)) {
            $this->addTotalsSection($pdf, $totals);
        }

        // Last page – back cover (no footer)
        if (!empty($this->lastPageImage) && file_exists($this->lastPageImage)) {
            $pdf->printPageFooter = false;
            $pdf->AddPage();
            $this->addFullPageImage($pdf, $this->lastPageImage);
        }

        $uploadsDir = FCPATH . 'uploads/';
        if (!is_dir($uploadsDir)) {
            mkdir($uploadsDir, 0755, true);
        }

        $fileName = 'kitchen_export_' . time() . '.pdf';
        $filePath = $uploadsDir . $fileName;
        $pdf->Output($filePath, 'F');

        return [
            'success'         => true,
            'fileName'        => $fileName,
            'downloadUrl'     => base_url('uploads/' . $fileName),
            'filePath'        => $filePath,
            'itemsCount'      => count($validatedItems),
            'customerDetails' => $customerDetails,
            'generatedAt'     => date('Y-m-d H:i:s'),
        ];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Full-page cover / back-cover image
    // ─────────────────────────────────────────────────────────────────────────

    private function addFullPageImage(KicoPDF $pdf, string $imagePath): void
    {
        $pdf->SetMargins(0, 0, 0);
        $pdf->SetAutoPageBreak(false);

        $info = getimagesize($imagePath);
        if ($info === false) {
            throw new \Exception('Unable to read image: ' . $imagePath);
        }

        $pw = self::PAGE_W;
        $ph = self::PAGE_H;
        $scale = max($pw / ($info[0] / 25.4), $ph / ($info[1] / 25.4));
        $w = ($info[0] / 25.4) * $scale;
        $h = ($info[1] / 25.4) * $scale;
        $pdf->Image($imagePath, ($pw - $w) / 2, ($ph - $h) / 2, $w, $h);

        $pdf->SetMargins(self::MARGIN, self::MARGIN, self::MARGIN);
        $pdf->SetAutoPageBreak(true, self::MARGIN);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Customer details block – 2-column, address spans 3 rows
    //
    // Layout (total = 180 mm):
    //   [lbl 28] [val 62] [rLbl 38] [rVal 52]
    //
    //   Row 1 (h=7):  Name         | name_val    | Project Number | proj_val
    //   Row 2 (h=7):  Address      | addr_val    | Date of Propos.| date_val
    //   Row 3 (h=7):  (span)       | (span)      | Sales Manager  | mgr_val
    //   Row 4 (h=7):  (span)       | (span)      | Service Support| svc_val
    //   Row 5 (h=7):  Phone        | phone_val   | Email.         | co_email
    //   Row 6 (h=7):  Email        | email_val   | Refered by     | ref_val
    // ─────────────────────────────────────────────────────────────────────────

    private function addCustomerDetailsBlock(KicoPDF $pdf, array $cd): void
    {
        $pdf->SetMargins(self::MARGIN, self::MARGIN, self::MARGIN);
        $pdf->SetAutoPageBreak(true, 20);

        $startX = self::MARGIN;   // 15 mm left margin
        $startY = $pdf->GetY();

        $rowH   = 7;              // height of each standard row
        $lineH  = 5;              // line height inside MultiCell text
        $addrH  = $rowH * 3;      // address cell spans 3 rows = 21 mm

        // Column widths (28 + 62 + 38 + 52 = 180 mm)
        $lbl  = 28;
        $val  = 62;
        $rLbl = 38;
        $rVal = 52;

        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetDrawColor(0, 0, 0);
        $pdf->SetLineWidth(0.3);

        // ── Row 1: Name | Project Number ──────────────────────────────────
        $y = $startY;
        $pdf->SetXY($startX, $y);
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->Cell($lbl,  $rowH, 'Name',           'LTB',  0, 'L');
        $pdf->SetFont('helvetica', '', 9);
        $pdf->Cell($val,  $rowH, substr(trim($cd['name'] ?? ''), 0, 70), 'TRB', 0, 'L');
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->Cell($rLbl, $rowH, 'Project Number',  'LTB',  0, 'L');
        $pdf->SetFont('helvetica', '', 9);
        $pdf->Cell($rVal, $rowH, substr(trim($cd['projectNumber'] ?? ''), 0, 50), 'TRB', 1, 'L');

        // ── Rows 2-4: Address (spans 3 rows) left | right rows 2, 3, 4 ───
        $y = $startY + $rowH;

        // Address LABEL – tall cell (addrH = 21 mm)
        $pdf->SetXY($startX, $y);
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->Cell($lbl, $addrH, 'Address', 1, 0, 'L');

        // Address VALUE – MultiCell with maxh so it stays inside addrH
        $pdf->SetFont('helvetica', '', 9);
        $pdf->MultiCell(
            $val,           // width
            $lineH,         // line height per text line
            trim($cd['address'] ?? ''),
            1,              // all borders
            'L',            // align
            false,          // no fill
            0,              // $ln = don't move cursor down after
            $startX + $lbl, // x
            $y,             // y
            true,           // reseth
            0,              // stretch
            false,          // not HTML
            true,           // autopadding
            $addrH,         // maxh – clamp to 3-row height
            'T',            // valign top
            false           // fitcell – don't scale font
        );

        // Right column rows 2, 3, 4
        $rightRows = [
            ['Date of Proposal', trim($cd['dateOfProposal']  ?? date('d-m-Y'))],
            ['Sales Manager',    trim($cd['salesManager']    ?? '')],
            ['Service Support',  trim($cd['serviceSupport']  ?? '')],
        ];
        foreach ($rightRows as $i => [$rLabel, $rValue]) {
            $ry = $y + $i * $rowH;
            $pdf->SetXY($startX + $lbl + $val, $ry);
            $pdf->SetFont('helvetica', 'B', 9);
            $pdf->Cell($rLbl, $rowH, $rLabel,                         'LTB',  0, 'L');
            $pdf->SetFont('helvetica', '', 9);
            $pdf->Cell($rVal, $rowH, substr($rValue, 0, 50),          'TRB',  1, 'L');
        }

        // ── Row 5: Phone | Email. ──────────────────────────────────────────
        $y = $startY + $rowH + $addrH;
        $pdf->SetXY($startX, $y);
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->Cell($lbl,  $rowH, 'Phone',  'LTB',  0, 'L');
        $pdf->SetFont('helvetica', '', 9);
        $pdf->Cell($val,  $rowH, substr(trim($cd['phone'] ?? ''), 0, 70), 'TRB', 0, 'L');
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->Cell($rLbl, $rowH, 'Email.', 'LTB',  0, 'L');
        $pdf->SetFont('helvetica', '', 9);
        $pdf->Cell($rVal, $rowH, substr(trim($cd['companyEmail'] ?? ''), 0, 50), 'TRB', 1, 'L');

        // ── Row 6: Email | Referred by ─────────────────────────────────────
        $y = $startY + $rowH + $addrH + $rowH;
        $pdf->SetXY($startX, $y);
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->Cell($lbl,  $rowH, 'Email',       'LTB',  0, 'L');
        $pdf->SetFont('helvetica', '', 9);
        $pdf->Cell($val,  $rowH, substr(trim($cd['email'] ?? ''), 0, 70), 'TRB', 0, 'L');
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->Cell($rLbl, $rowH, 'Refered by',  'LTB',  0, 'L');
        $pdf->SetFont('helvetica', '', 9);
        $pdf->Cell($rVal, $rowH, substr(trim($cd['referredBy'] ?? ''), 0, 50), 'TRB', 1, 'L');

        // Position cursor below the entire block
        $blockBottom = $startY + $rowH + $addrH + $rowH + $rowH;
        $pdf->SetXY($startX, $blockBottom);

        // ── "PROPOSAL FOR INTERIOR WORKS" orange header bar ───────────────
        $pdf->Ln(2);
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFillColor(204, 102, 0);
        $pdf->Cell(self::USABLE_W, 8, 'PROPOSAL FOR INTERIOR WORKS', 0, 1, 'C', true);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFillColor(255, 255, 255);
        $pdf->Ln(3);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Items table – 6 columns, 180 mm total
    //
    //  Sl.No  Description       Qty   Unit  Rate(Rs.)  Amount(Rs.)
    //   12      86               14    17     25          26        = 180
    // ─────────────────────────────────────────────────────────────────────────

    private function addItemsTable(KicoPDF $pdf, array $items): void
    {
        $pdf->SetMargins(self::MARGIN, self::MARGIN, self::MARGIN);
        $pdf->SetAutoPageBreak(true, 18);

        $wSl   = 12;
        $wDesc = 86;
        $wQty  = 14;
        $wUnit = 17;
        $wRate = 25;
        $wAmt  = 26;
        // Total = 180 mm

        $lineH   = 5;    // text line height inside description MultiCell
        $minRowH = 7;    // minimum row height

        $this->drawTableHeader($pdf, $wSl, $wDesc, $wQty, $wUnit, $wRate, $wAmt);

        foreach ($items as $item) {
            if (!empty($item['isCategory'])) {
                // Full-width bold section header
                $pdf->SetFont('helvetica', 'B', 9);
                $pdf->SetFillColor(230, 230, 230);
                $pdf->Cell(self::USABLE_W, 6, '  ' . ($item['description'] ?? ''), 1, 1, 'L', true);
                $pdf->SetFillColor(255, 255, 255);
                continue;
            }

            $slNo   = (string)($item['slNo']        ?? '');
            $desc   = (string)($item['description'] ?? '');
            $qty    = isset($item['qty'])  ? (string)$item['qty']  : '';
            $unit   = (string)($item['unit']         ?? '');
            $rate   = ($item['rate']   !== null && $item['rate']   !== '') ? $this->formatIndian($item['rate'])   : '';
            $rawAmt = $item['amount'] ?? null;

            if ($rawAmt === null || $rawAmt === '') {
                $amtText = '';
                $amtBold = false;
            } elseif (is_numeric($rawAmt)) {
                $amtText = $this->formatIndian((float)$rawAmt);
                $amtBold = false;
            } else {
                $amtText = (string)$rawAmt;
                $amtBold = true;
            }

            // Calculate row height based on description text wrapping
            $pdf->SetFont('helvetica', '', 9);
            $descTextH = $pdf->getStringHeight($wDesc, $desc);
            $rowH      = max($minRowH, $descTextH);

            $x = self::MARGIN;
            $y = $pdf->GetY();

            // Manual page-break check: keep entire row on one page
            if ($y + $rowH > self::PAGE_H - self::MARGIN - 10) {
                $pdf->AddPage();
                $this->drawTableHeader($pdf, $wSl, $wDesc, $wQty, $wUnit, $wRate, $wAmt);
                $y = $pdf->GetY();
            }

            $pdf->SetFont('helvetica', '', 9);
            $pdf->SetDrawColor(0, 0, 0);
            $pdf->SetLineWidth(0.3);

            // Sl. No
            $pdf->SetXY($x, $y);
            $pdf->Cell($wSl, $rowH, $slNo, 1, 0, 'C');

            // Description – MultiCell with fixed max height = rowH
            $pdf->MultiCell(
                $wDesc, $lineH, $desc,
                1, 'L', false, 0,
                $x + $wSl, $y,
                true, 0, false, true, $rowH, 'T', false
            );

            // Qty
            $pdf->SetXY($x + $wSl + $wDesc, $y);
            $pdf->Cell($wQty, $rowH, $qty, 1, 0, 'C');

            // Unit
            $pdf->SetXY($x + $wSl + $wDesc + $wQty, $y);
            $pdf->Cell($wUnit, $rowH, $unit, 1, 0, 'C');

            // Rate
            $pdf->SetXY($x + $wSl + $wDesc + $wQty + $wUnit, $y);
            $pdf->Cell($wRate, $rowH, $rate, 1, 0, 'R');

            // Amount (bold for special strings like "Cancelled")
            if ($amtBold) {
                $pdf->SetFont('helvetica', 'B', 9);
            }
            $pdf->SetXY($x + $wSl + $wDesc + $wQty + $wUnit + $wRate, $y);
            $pdf->Cell($wAmt, $rowH, $amtText, 1, 0, 'R');
            if ($amtBold) {
                $pdf->SetFont('helvetica', '', 9);
            }

            // Advance cursor to next row
            $pdf->SetXY($x, $y + $rowH);
        }
    }

    private function drawTableHeader(
        KicoPDF $pdf,
        int $wSl, int $wDesc, int $wQty, int $wUnit, int $wRate, int $wAmt
    ): void {
        $pdf->SetFont('helvetica', 'BI', 9);
        $pdf->SetFillColor(255, 204, 102);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetDrawColor(0, 0, 0);
        $pdf->SetLineWidth(0.4);

        $pdf->Cell($wSl,   7, 'Sl. No',      1, 0, 'C', true);
        $pdf->Cell($wDesc, 7, 'Description', 1, 0, 'C', true);
        $pdf->Cell($wQty,  7, 'Qty',         1, 0, 'C', true);
        $pdf->Cell($wUnit, 7, 'Unit',        1, 0, 'C', true);
        $pdf->Cell($wRate, 7, 'Rate (Rs.)',  1, 0, 'C', true);
        $pdf->Cell($wAmt,  7, 'Amount(Rs.)', 1, 1, 'C', true);

        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetLineWidth(0.3);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Totals section
    // ─────────────────────────────────────────────────────────────────────────

    private function addTotalsSection(KicoPDF $pdf, array $t): void
    {
        $pdf->Ln(3);
        $pdf->SetFont('helvetica', '', 9);
        $pdf->SetDrawColor(0, 0, 0);
        $pdf->SetLineWidth(0.3);

        // Labels take left portion, values right-aligned
        $labelW = 134;
        $valW   = 46;
        // Total: 134 + 46 = 180 mm ✓

        $subTotalLabel = $t['subTotalLabel'] ?? 'TOTAL';
        $rows = [];

        if (isset($t['subTotal'])) {
            $rows[] = [$subTotalLabel, $this->formatIndian($t['subTotal']), false];
        }
        if (isset($t['gstPercent'])) {
            $gstAmt = $t['gstAmount'] ?? round(($t['subTotal'] ?? 0) * $t['gstPercent'] / 100);
            $rows[] = ['GST @' . $t['gstPercent'] . '% of Total', $this->formatIndian($gstAmt), false];
            if (isset($t['subTotal'])) {
                $rows[] = ['Total', $this->formatIndian(($t['subTotal'] ?? 0) + $gstAmt), false];
            }
        }
        if (isset($t['discount'])) {
            $rows[] = ['Discount', $this->formatIndian($t['discount']), false];
        }
        if (isset($t['grandTotal'])) {
            $rows[] = ['Grand Total', $this->formatIndian($t['grandTotal']), true];
        }

        foreach ($rows as [$label, $value, $bold]) {
            $pdf->SetFont('helvetica', $bold ? 'B' : '', $bold ? 10 : 9);
            $pdf->Cell($labelW, 7, $label, 1, 0, 'R');
            $pdf->Cell($valW,   7, $value, 1, 1, 'R');
        }

        if (!empty($t['grandTotalWords'])) {
            $pdf->Ln(1);
            $pdf->SetFont('helvetica', 'B', 9);
            $pdf->Cell(self::USABLE_W, 7, $t['grandTotalWords'], 1, 1, 'C');
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Validate & normalise items
    // ─────────────────────────────────────────────────────────────────────────

    private function validateItems(array $items): array
    {
        $validated = [];
        $errors    = [];

        foreach ($items as $index => $item) {
            if (is_object($item)) {
                $item = (array)$item;
            }
            if (!is_array($item)) {
                $errors[] = "Item at index {$index} is not a valid object or array";
                continue;
            }

            $isCategory = !empty($item['isCategory']);

            if ($isCategory) {
                if (empty($item['description'])) {
                    $errors[] = "Category item at index {$index}: 'description' is required";
                    continue;
                }
                $validated[] = ['isCategory' => true, 'description' => (string)$item['description']];
                continue;
            }

            if (!isset($item['description']) || trim((string)$item['description']) === '') {
                $errors[] = "Item at index {$index}: 'description' is required";
                continue;
            }

            $out = [
                'isCategory'  => false,
                'slNo'        => isset($item['slNo'])  ? (string)$item['slNo']  : '',
                'description' => trim((string)$item['description']),
                'qty'         => null,
                'unit'        => isset($item['unit'])  ? (string)$item['unit']  : '',
                'rate'        => null,
                'amount'      => null,
            ];

            foreach (['qty', 'quantity'] as $qKey) {
                if (isset($item[$qKey])) {
                    $q = $item[$qKey];
                    if (!is_numeric($q) || $q < 0) {
                        $errors[] = "Item at index {$index}: 'qty' must be a non-negative number";
                        continue 2;
                    }
                    $out['qty'] = (float)$q == (int)$q ? (int)$q : (float)$q;
                    break;
                }
            }

            if (isset($item['rate']) && $item['rate'] !== null && $item['rate'] !== '') {
                if (!is_numeric($item['rate'])) {
                    $errors[] = "Item at index {$index}: 'rate' must be numeric";
                    continue;
                }
                $out['rate'] = (float)$item['rate'];
            }

            if (isset($item['amount']) && $item['amount'] !== null && $item['amount'] !== '') {
                $out['amount'] = is_numeric($item['amount']) ? (float)$item['amount'] : (string)$item['amount'];
            }

            $validated[] = $out;
        }

        if (!empty($errors)) {
            throw new \Exception('Validation errors: ' . implode('; ', $errors));
        }
        if (empty($validated)) {
            throw new \Exception('No valid items found in the request');
        }

        return $validated;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Indian number formatting  e.g.  154662 → 1,54,662
    // ─────────────────────────────────────────────────────────────────────────

    private function formatIndian(float|int $number): string
    {
        $number   = (float)$number;
        $decimals = ($number != floor($number)) ? 2 : 0;
        $parts    = explode('.', number_format($number, $decimals));
        $int      = $parts[0];
        $dec      = isset($parts[1]) ? '.' . $parts[1] : '';

        if (strlen($int) <= 3) {
            return $int . $dec;
        }

        $last3 = substr($int, -3);
        $rest  = substr($int, 0, -3);
        $rest  = preg_replace('/(\d)(?=(\d{2})+$)/', '$1,', $rest);
        return $rest . ',' . $last3 . $dec;
    }
}
