<?php

namespace TechNur\PhpPdfPro;

use setasign\Fpdi\Fpdi;

if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

/**
 * Extended FPDI to support rotation and drawing
 */
class FpdiExtended extends Fpdi {
    protected $angle = 0;

    function Rotate($angle, $x = -1, $y = -1) {
        if ($x == -1) $x = $this->x;
        if ($y == -1) $y = $this->y;
        if ($this->angle != 0) $this->_out('Q');
        $this->angle = $angle;
        if ($angle != 0) {
            $angle *= M_PI / 180;
            $c = cos($angle);
            $s = sin($angle);
            $cx = $x * $this->k;
            $cy = ($this->h - $y) * $this->k;
            $this->_out(sprintf('q %.5F %.5F %.5F %.5F %.2F %.2F cm 1 0 0 1 %.2F %.2F cm', $c, $s, -$s, $c, $cx, $cy, -$cx, -$cy));
        }
    }

    function _endpage() {
        if ($this->angle != 0) {
            $this->angle = 0;
            $this->_out('Q');
        }
        parent::_endpage();
    }
}

class PDFEngine {
    /**
     * Merge multiple PDF files into one.
     */
    public function merge(array $filePaths, string $outputPath): bool {
        try {
            $pdf = new FpdiExtended();
            foreach ($filePaths as $file) {
                if (!file_exists($file)) continue;
                $pageCount = $pdf->setSourceFile($file);
                for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                    $templateId = $pdf->importPage($pageNo);
                    $size = $pdf->getTemplateSize($templateId);
                    $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                    $pdf->useTemplate($templateId);
                }
            }
            $pdf->Output('F', $outputPath);
            return true;
        } catch (\Exception $e) {
            error_log("Merge Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Extract, reorder, rotate, or delete pages.
     */
    public function organize(string $filePath, array $pageActions, string $outputPath): bool {
        try {
            $pdf = new FpdiExtended();
            $pageCount = $pdf->setSourceFile($filePath);

            foreach ($pageActions as $action) {
                $pageNo = $action['page'];
                $rotation = $action['rotation'] ?? 0;

                if ($pageNo < 1 || $pageNo > $pageCount) continue;

                $templateId = $pdf->importPage($pageNo);
                $size = $pdf->getTemplateSize($templateId);

                if ($rotation == 90 || $rotation == 270) {
                    $pdf->AddPage($size['width'] > $size['height'] ? 'P' : 'L', [$size['height'], $size['width']]);
                } else {
                    $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                }

                if ($rotation != 0) {
                    $pdf->Rotate($rotation, $pdf->GetPageWidth() / 2, $pdf->GetPageHeight() / 2);
                }

                $pdf->useTemplate($templateId, 0, 0, $size['width'], $size['height'], true);

                if ($rotation != 0) {
                    $pdf->Rotate(0);
                }
            }

            $pdf->Output('F', $outputPath);
            return true;
        } catch (\Exception $e) {
            error_log("Organize Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Add text overlay to a PDF.
     */
    public function annotate(string $filePath, string $text, int $x, int $y, string $outputPath, int $page = null): bool {
        try {
            $pdf = new FpdiExtended();
            $pageCount = $pdf->setSourceFile($filePath);

            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                $templateId = $pdf->importPage($pageNo);
                $size = $pdf->getTemplateSize($templateId);

                $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $pdf->useTemplate($templateId);

                if ($page === null || $page === $pageNo) {
                    $pdf->SetFont('Arial', 'B', 16);
                    $pdf->SetTextColor(255, 0, 0);
                    $pdf->SetXY($x, $y);
                    $pdf->Write(0, $text);
                }
            }

            $pdf->Output('F', $outputPath);
            return true;
        } catch (\Exception $e) {
            error_log("Annotate Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Overlay rectangles for Whiteout or Redaction.
     */
    public function overlayRect(string $filePath, array $rects, string $outputPath): bool {
        try {
            $pdf = new FpdiExtended();
            $pageCount = $pdf->setSourceFile($filePath);

            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                $templateId = $pdf->importPage($pageNo);
                $size = $pdf->getTemplateSize($templateId);

                $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $pdf->useTemplate($templateId);

                foreach ($rects as $rect) {
                    // Only apply to specific page if page is defined, or all pages if not
                    if (isset($rect['page']) && $rect['page'] != $pageNo) continue;

                    if ($rect['type'] === 'whiteout') {
                        $pdf->SetFillColor(255, 255, 255);
                    } else {
                        $pdf->SetFillColor(0, 0, 0);
                    }
                    $pdf->Rect($rect['x'], $rect['y'], $rect['w'], $rect['h'], 'F');
                }
            }

            $pdf->Output('F', $outputPath);
            return true;
        } catch (\Exception $e) {
            error_log("OverlayRect Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Strips metadata by re-generating the PDF without extra info.
     */
    public function cleanMetadata(string $filePath, string $outputPath): bool {
        try {
            $pdf = new FpdiExtended();
            $pageCount = $pdf->setSourceFile($filePath);

            // FPDF does not set much metadata by default unless explicitly told.
            // Re-saving with FPDI effectively strips most existing metadata.
            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                $templateId = $pdf->importPage($pageNo);
                $size = $pdf->getTemplateSize($templateId);
                $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $pdf->useTemplate($templateId);
            }

            $pdf->Output('F', $outputPath);
            return true;
        } catch (\Exception $e) {
            error_log("CleanMetadata Error: " . $e->getMessage());
            return false;
        }
    }
}
