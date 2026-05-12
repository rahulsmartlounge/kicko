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

        $startX = self::MARGIN;
        $startY = $pdf->GetY();

        $rowH  = 7;       // standard row height
        $lineH = 5;       // MultiCell line height
        $addrH = $rowH * 3; // address spans 3 rows = 21 mm

        // Column widths: left label | left value | right label | right value
        // 28 + 62 + 38 + 52 = 180 mm
        $lbl  = 28;
        $val  = 62;
        $rLbl = 38;
        $rVal = 52;

        // Vertical separator X positions
        $sepL  = $startX + $lbl;              // between left label and left value
        $sepM  = $startX + $lbl + $val;       // between left half and right half
        $sepR  = $startX + $lbl + $val + $rLbl; // between right label and right value

        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetDrawColor(0, 0, 0);
        $pdf->SetLineWidth(0.3);
        $pdf->setCellPaddings(1, 1, 1, 1);

        // ── Render all cell text with border=0 ────────────────────────────

        // Row 1: Name | Project Number
        $y = $startY;
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetXY($startX, $y);
        $pdf->MultiCell($lbl,  $lineH, 'Name',           0, 'L', false, 0, $startX,        $y, true, 0, false, true, $rowH, 'M', false);
        $pdf->SetFont('helvetica', '', 9);
        $pdf->SetXY($sepL, $y);
        $pdf->MultiCell($val,  $lineH, substr(trim($cd['name'] ?? ''), 0, 70), 0, 'L', false, 0, $sepL, $y, true, 0, false, true, $rowH, 'M', false);
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetXY($sepM, $y);
        $pdf->MultiCell($rLbl, $lineH, 'Project Number', 0, 'L', false, 0, $sepM,          $y, true, 0, false, true, $rowH, 'M', false);
        $pdf->SetFont('helvetica', '', 9);
        $pdf->SetXY($sepR, $y);
        $pdf->MultiCell($rVal, $lineH, substr(trim($cd['projectNumber'] ?? ''), 0, 50), 0, 'L', false, 0, $sepR, $y, true, 0, false, true, $rowH, 'M', false);

        // Rows 2-4: Address (left, spans 3 rows) | Date / Sales Mgr / Service Support (right)
        $y = $startY + $rowH;

        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetXY($startX, $y);
        $pdf->MultiCell($lbl, $lineH, 'Address', 0, 'L', false, 0, $startX, $y, true, 0, false, true, $addrH, 'T', false);

        $pdf->SetFont('helvetica', '', 9);
        $pdf->SetXY($sepL, $y);
        $pdf->MultiCell($val, $lineH, trim($cd['address'] ?? ''), 0, 'L', false, 0, $sepL, $y, true, 0, false, true, $addrH, 'T', false);

        $rightRows = [
            ['Date of Proposal', trim($cd['dateOfProposal'] ?? date('d-m-Y'))],
            ['Sales Manager',    trim($cd['salesManager']   ?? '')],
            ['Service Support',  trim($cd['serviceSupport'] ?? '')],
        ];
        foreach ($rightRows as $i => [$rLabel, $rValue]) {
            $ry = $y + $i * $rowH;
            $pdf->SetFont('helvetica', 'B', 9);
            $pdf->SetXY($sepM, $ry);
            $pdf->MultiCell($rLbl, $lineH, $rLabel,                    0, 'L', false, 0, $sepM, $ry, true, 0, false, true, $rowH, 'M', false);
            $pdf->SetFont('helvetica', '', 9);
            $pdf->SetXY($sepR, $ry);
            $pdf->MultiCell($rVal, $lineH, substr($rValue, 0, 50),     0, 'L', false, 0, $sepR, $ry, true, 0, false, true, $rowH, 'M', false);
        }

        // Row 5: Phone | Email
        $y = $startY + $rowH + $addrH;
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetXY($startX, $y);
        $pdf->MultiCell($lbl,  $lineH, 'Phone',  0, 'L', false, 0, $startX, $y, true, 0, false, true, $rowH, 'M', false);
        $pdf->SetFont('helvetica', '', 9);
        $pdf->SetXY($sepL, $y);
        $pdf->MultiCell($val,  $lineH, substr(trim($cd['phone'] ?? ''), 0, 70), 0, 'L', false, 0, $sepL, $y, true, 0, false, true, $rowH, 'M', false);
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetXY($sepM, $y);
        $pdf->MultiCell($rLbl, $lineH, 'Email',  0, 'L', false, 0, $sepM,    $y, true, 0, false, true, $rowH, 'M', false);
        $pdf->SetFont('helvetica', '', 9);
        $pdf->SetXY($sepR, $y);
        $pdf->MultiCell($rVal, $lineH, substr(trim($cd['companyEmail'] ?? ''), 0, 50), 0, 'L', false, 0, $sepR, $y, true, 0, false, true, $rowH, 'M', false);

        // Row 6: Email | Referred by
        $y = $startY + $rowH + $addrH + $rowH;
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetXY($startX, $y);
        $pdf->MultiCell($lbl,  $lineH, 'Email',      0, 'L', false, 0, $startX, $y, true, 0, false, true, $rowH, 'M', false);
        $pdf->SetFont('helvetica', '', 9);
        $pdf->SetXY($sepL, $y);
        $pdf->MultiCell($val,  $lineH, substr(trim($cd['email'] ?? ''), 0, 70), 0, 'L', false, 0, $sepL, $y, true, 0, false, true, $rowH, 'M', false);
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetXY($sepM, $y);
        $pdf->MultiCell($rLbl, $lineH, 'Referred by', 0, 'L', false, 0, $sepM,   $y, true, 0, false, true, $rowH, 'M', false);
        $pdf->SetFont('helvetica', '', 9);
        $pdf->SetXY($sepR, $y);
        $pdf->MultiCell($rVal, $lineH, substr(trim($cd['referredBy'] ?? ''), 0, 50), 0, 'L', false, 0, $sepR, $y, true, 0, false, true, $rowH, 'M', false);

        // ── Total block height ────────────────────────────────────────────
        $blockH = $rowH + $addrH + $rowH + $rowH; // row1 + addr(3rows) + row5 + row6

        // ── Outer border for the entire block ────────────────────────────
        $pdf->Rect($startX, $startY, self::USABLE_W, $blockH);

        // ── Vertical column separators (full block height) ────────────────
        $pdf->Line($sepL, $startY, $sepL, $startY + $blockH);
        $pdf->Line($sepM, $startY, $sepM, $startY + $blockH);
        $pdf->Line($sepR, $startY, $sepR, $startY + $blockH);

        // ── Horizontal row separators ─────────────────────────────────────
        // After row 1
        $pdf->Line($startX, $startY + $rowH, $startX + self::USABLE_W, $startY + $rowH);
        // After address block (rows 2-4) — full width
        $pdf->Line($startX, $startY + $rowH + $addrH, $startX + self::USABLE_W, $startY + $rowH + $addrH);
        // Internal separators within address rows (right half only)
        $pdf->Line($sepM, $startY + $rowH + $rowH,          $startX + self::USABLE_W, $startY + $rowH + $rowH);
        $pdf->Line($sepM, $startY + $rowH + $rowH + $rowH,  $startX + self::USABLE_W, $startY + $rowH + $rowH + $rowH);
        // After row 5
        $pdf->Line($startX, $startY + $rowH + $addrH + $rowH, $startX + self::USABLE_W, $startY + $rowH + $addrH + $rowH);

        // ── Position cursor below block ───────────────────────────────────
        $pdf->SetXY($startX, $startY + $blockH);

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
    //  Sl.No  Description  Qty  Unit  Rate(Rs.)  Amount(Rs.)
    //   10       74         14   18      32          32       = 180
    // ─────────────────────────────────────────────────────────────────────────

    private const COL_SL   = 10;
    private const COL_DESC = 74;
    private const COL_QTY  = 14;
    private const COL_UNIT = 18;
    private const COL_RATE = 32;
    private const COL_AMT  = 32;
    private const LINE_H   = 5;  // per-line height inside MultiCell (mm)
    private const MIN_ROW  = 7;  // minimum row height (mm)
    private const CELL_PAD = 1;  // uniform cell padding all sides (mm)

    /**
     * Calculate exact row height using TCPDF's getStringHeight().
     * This accounts for font metrics, padding, and character-level wrapping.
     * Description column is the single source of truth for every row.
     */
    private function calcRowHeight(KicoPDF $pdf, string $desc): float
    {
        $pdf->SetFont('helvetica', '', 9);
        $pdf->setCellPaddings(self::CELL_PAD, self::CELL_PAD, self::CELL_PAD, self::CELL_PAD);
        $measured = $pdf->getStringHeight(self::COL_DESC, $desc);
        return max(self::MIN_ROW, $measured);
    }

    private function addItemsTable(KicoPDF $pdf, array $items): void
    {
        $pdf->SetMargins(self::MARGIN, self::MARGIN, self::MARGIN);
        $pdf->SetAutoPageBreak(false);

        $this->drawTableHeader($pdf);

        // Pre-compute cumulative X positions for vertical separator lines
        $colWidths = [self::COL_SL, self::COL_DESC, self::COL_QTY, self::COL_UNIT, self::COL_RATE, self::COL_AMT];
        $sepX = [];
        $cx   = self::MARGIN;
        foreach ($colWidths as $w) {
            $cx   += $w;
            $sepX[] = $cx;
        }
        array_pop($sepX); // remove rightmost — that is the outer border, not a separator

        foreach ($items as $item) {

            // ── Category header row ──────────────────────────────────────────
            if (!empty($item['isCategory'])) {
                $y = $pdf->GetY();
                if ($y + 6 > self::PAGE_H - self::MARGIN - 10) {
                    $pdf->AddPage();
                    $this->drawTableHeader($pdf);
                }
                $pdf->SetFont('helvetica', 'B', 9);
                $pdf->SetFillColor(230, 230, 230);
                $pdf->setCellPaddings(self::CELL_PAD, self::CELL_PAD, self::CELL_PAD, self::CELL_PAD);
                $pdf->Cell(self::USABLE_W, 6, '  ' . ($item['description'] ?? ''), 1, 1, 'L', true);
                $pdf->SetFillColor(255, 255, 255);
                continue;
            }

            // ── Prepare cell values ──────────────────────────────────────────
            $slNo    = (string)($item['slNo']        ?? '');
            $desc    = (string)($item['description'] ?? '');
            $qty     = isset($item['qty']) ? (string)$item['qty'] : '';
            $unit    = (string)($item['unit']        ?? '');
            $rate    = ($item['rate'] !== null && $item['rate'] !== '')
                       ? $this->formatIndian($item['rate']) : '';
            $rawAmt  = $item['amount'] ?? null;
            $amtBold = false;

            if ($rawAmt === null || $rawAmt === '') {
                $amtText = '';
            } elseif (is_numeric($rawAmt)) {
                $amtText = $this->formatIndian((float)$rawAmt);
            } else {
                $amtText = (string)$rawAmt;
                $amtBold = true;
            }

            // ── Row height: Description is the single source of truth ────────
            $rowHeight = $this->calcRowHeight($pdf, $desc);

            // ── Page-break: entire row moves to next page if needed ──────────
            $y = $pdf->GetY();
            if ($y + $rowHeight > self::PAGE_H - self::MARGIN - 10) {
                $pdf->AddPage();
                $this->drawTableHeader($pdf);
                $y = $pdf->GetY();
            }

            $x = self::MARGIN;

            $pdf->SetFont('helvetica', '', 9);
            $pdf->SetDrawColor(0, 0, 0);
            $pdf->SetLineWidth(0.3);
            $pdf->setCellPaddings(self::CELL_PAD, self::CELL_PAD, self::CELL_PAD, self::CELL_PAD);

            // ── Render all cells with border=0 (no individual cell borders) ──

            // Sl. No — middle aligned
            $pdf->SetXY($x, $y);
            $pdf->MultiCell(
                self::COL_SL, self::LINE_H, $slNo,
                0, 'C', false, 0,
                $x, $y, true, 0, false, true, $rowHeight, 'M', false
            );

            // Description — top aligned, wraps naturally
            $pdf->SetXY($x + self::COL_SL, $y);
            $pdf->MultiCell(
                self::COL_DESC, self::LINE_H, $desc,
                0, 'L', false, 0,
                $x + self::COL_SL, $y, true, 0, false, true, $rowHeight, 'T', false
            );

            // Qty — middle aligned
            $pdf->SetXY($x + self::COL_SL + self::COL_DESC, $y);
            $pdf->MultiCell(
                self::COL_QTY, self::LINE_H, $qty,
                0, 'C', false, 0,
                $x + self::COL_SL + self::COL_DESC, $y,
                true, 0, false, true, $rowHeight, 'M', false
            );

            // Unit — middle aligned
            $pdf->SetXY($x + self::COL_SL + self::COL_DESC + self::COL_QTY, $y);
            $pdf->MultiCell(
                self::COL_UNIT, self::LINE_H, $unit,
                0, 'C', false, 0,
                $x + self::COL_SL + self::COL_DESC + self::COL_QTY, $y,
                true, 0, false, true, $rowHeight, 'M', false
            );

            // Rate — middle aligned
            $pdf->SetXY($x + self::COL_SL + self::COL_DESC + self::COL_QTY + self::COL_UNIT, $y);
            $pdf->MultiCell(
                self::COL_RATE, self::LINE_H, $rate,
                0, 'R', false, 0,
                $x + self::COL_SL + self::COL_DESC + self::COL_QTY + self::COL_UNIT, $y,
                true, 0, false, true, $rowHeight, 'M', false
            );

            // Amount — middle aligned
            if ($amtBold) { $pdf->SetFont('helvetica', 'B', 9); }
            $pdf->SetXY($x + self::COL_SL + self::COL_DESC + self::COL_QTY + self::COL_UNIT + self::COL_RATE, $y);
            $pdf->MultiCell(
                self::COL_AMT, self::LINE_H, $amtText,
                0, 'R', false, 0,
                $x + self::COL_SL + self::COL_DESC + self::COL_QTY + self::COL_UNIT + self::COL_RATE, $y,
                true, 0, false, true, $rowHeight, 'M', false
            );
            if ($amtBold) { $pdf->SetFont('helvetica', '', 9); }

            // ── Single outer row border ──────────────────────────────────────
            $pdf->Rect($x, $y, self::USABLE_W, $rowHeight);

            // ── Vertical column separators only (no top/bottom per cell) ─────
            foreach ($sepX as $sx) {
                $pdf->Line($sx, $y, $sx, $y + $rowHeight);
            }

            // ── Advance cursor — no automatic TCPDF movement ─────────────────
            $pdf->SetXY($x, $y + $rowHeight);
        }

        $pdf->SetAutoPageBreak(true, self::MARGIN);
    }

    private function drawTableHeader(KicoPDF $pdf): void
    {
        $pdf->SetFont('helvetica', 'BI', 9);
        $pdf->SetFillColor(255, 204, 102);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetDrawColor(0, 0, 0);
        $pdf->SetLineWidth(0.4);
        $pdf->setCellPaddings(self::CELL_PAD, self::CELL_PAD, self::CELL_PAD, self::CELL_PAD);

        $pdf->Cell(self::COL_SL,   7, 'Sl. No',       1, 0, 'C', true);
        $pdf->Cell(self::COL_DESC, 7, 'Description',  1, 0, 'C', true);
        $pdf->Cell(self::COL_QTY,  7, 'Qty',          1, 0, 'C', true);
        $pdf->Cell(self::COL_UNIT, 7, 'Unit',         1, 0, 'C', true);
        $pdf->Cell(self::COL_RATE, 7, 'Rate (Rs.)',   1, 0, 'C', true);
        $pdf->Cell(self::COL_AMT,  7, 'Amount (Rs.)', 1, 1, 'C', true);

        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetLineWidth(0.3);
        $pdf->SetTextColor(0, 0, 0);
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
