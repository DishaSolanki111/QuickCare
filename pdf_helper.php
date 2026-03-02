<?php
/**
 * Minimal PDF helper - outputs valid PDF for simple tables (no external deps).
 */
class SimplePdf {
    private $objects = [];
    private $objNum = 0;
    private $currentPageContent = '';
    private $margin = 50;
    private $fontSize = 10;
    private $titleSize = 14;
    private $y = 750;

    public function __construct() {
        $this->currentPageContent = '';
        $this->y = 750;
    }

    private function escape($s) {
        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], (string)$s);
    }

    private function addObject($content) {
        $this->objNum++;
        $this->objects[$this->objNum] = $content;
        return $this->objNum;
    }

    public function SetFont($family, $style, $size) {
        $this->fontSize = $size;
    }

    public function writeTitle($text) {
        $text = $this->escape($text);
        $this->currentPageContent .= "BT /F1 $this->titleSize Tf $this->margin $this->y Td ($text) Tj ET ";
        $this->y -= 25;
    }

    public function writeLine($text) {
        $text = $this->escape($text);
        $this->currentPageContent .= "BT /F1 $this->fontSize Tf $this->margin $this->y Td ($text) Tj ET ";
        $this->y -= 14;
    }

    public function writeRow($cols, $colWidths = null) {
        $x = $this->margin;
        if ($colWidths === null) $colWidths = array_fill(0, count($cols), 80);
        foreach ($cols as $i => $cell) {
            $cell = $this->escape((string)$cell);
            $w = isset($colWidths[$i]) ? $colWidths[$i] : 80;
            $this->currentPageContent .= "BT /F1 $this->fontSize Tf $x $this->y Td ($cell) Tj ET ";
            $x += $w;
        }
        $this->y -= 14;
    }

    public function Output($dest, $filename = 'report.pdf') {
        $fontObj = $this->addObject("<</Type/Font/Subtype/Type1/BaseFont/Helvetica>>");
        $contentStream = $this->currentPageContent;
        $contentObj = $this->addObject("<</Length " . strlen($contentStream) . ">>\nstream\n" . $contentStream . "\nendstream");
        $pageObj = $this->addObject("<</Type/Page/Parent 2 0 R/MediaBox[0 0 612 792]/Resources<</Font<</F1 $fontObj 0 R>>>>/Contents $contentObj 0 R>>");
        $pagesObj = $this->addObject("<</Type/Pages/Kids[$pageObj 0 R]/Count 1>>");
        $catalogObj = $this->addObject("<</Type/Catalog/Pages $pagesObj 0 R>>");

        $out = "%PDF-1.4\n";
        $offsets = [];
        $n = $this->objNum;
        for ($i = 1; $i <= $n; $i++) {
            $offsets[$i] = strlen($out);
            $out .= "$i 0 obj\n" . $this->objects[$i] . "\nendobj\n";
        }
        $xrefPos = strlen($out);
        $out .= "xref\n0 " . ($n + 1) . "\n0000000000 65535 f \n";
        for ($i = 1; $i <= $n; $i++) {
            $out .= sprintf("%010d 00000 n \n", $offsets[$i]);
        }
        $out .= "trailer\n<</Size " . ($n + 1) . "/Root $catalogObj 0 R>>\nstartxref\n$xrefPos\n%%EOF";

        if ($dest === 'D' || $dest === 'I') {
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Cache-Control: private, max-age=0, must-revalidate');
            header('Pragma: public');
            echo $out;
            exit;
        }
        return $out;
    }
}
