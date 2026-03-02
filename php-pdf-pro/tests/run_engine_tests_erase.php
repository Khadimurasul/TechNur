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

echo "Testing Whiteout...\n";
$whiteoutOut = $outputDir . 'test_whiteout.pdf';
if ($engine->overlayRect($test1, [['type' => 'whiteout', 'x' => 10, 'y' => 10, 'w' => 50, 'h' => 20]], $whiteoutOut)) {
    echo "SUCCESS: Whiteout to $whiteoutOut\n";
} else {
    echo "FAILED: Whiteout\n";
}

echo "Testing Redact...\n";
$redactOut = $outputDir . 'test_redact.pdf';
if ($engine->overlayRect($test1, [['type' => 'redact', 'x' => 10, 'y' => 40, 'w' => 50, 'h' => 20]], $redactOut)) {
    echo "SUCCESS: Redacted to $redactOut\n";
} else {
    echo "FAILED: Redact\n";
}

echo "Testing Clean Metadata...\n";
$cleanOut = $outputDir . 'test_cleaned.pdf';
if ($engine->cleanMetadata($test1, $cleanOut)) {
    echo "SUCCESS: Cleaned to $cleanOut\n";
} else {
    echo "FAILED: Clean Metadata\n";
}
