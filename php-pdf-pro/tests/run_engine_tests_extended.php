<?php
require_once __DIR__ . '/../vendor/autoload.php';
use TechNur\PhpPdfPro\PDFEngine;

$engine = new PDFEngine();
$test1 = __DIR__ . '/test1.pdf';
$outputDir = __DIR__ . '/../output/';

if (!is_dir($outputDir)) mkdir($outputDir, 0777, true);

// Create test1.pdf if it doesn't exist
if (!file_exists($test1)) {
    $pdf = new \setasign\Fpdi\Fpdi();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(40, 10, 'Test PDF 1 - Page 1');
    $pdf->Output('F', $test1);
}

echo "Testing Rotate...\n";
$rotateOut = $outputDir . 'test_rotated.pdf';
if ($engine->organize($test1, [['page' => 1, 'rotation' => 90]], $rotateOut)) {
    echo "SUCCESS: Rotated to $rotateOut\n";
} else {
    echo "FAILED: Rotate\n";
}

echo "Testing Delete (Keep only page 1)...\n";
$deleteOut = $outputDir . 'test_deleted.pdf';
if ($engine->organize($test1, [['page' => 1, 'rotation' => 0]], $deleteOut)) {
    echo "SUCCESS: Deleted to $deleteOut\n";
} else {
    echo "FAILED: Delete\n";
}
