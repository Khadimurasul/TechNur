<?php
require_once __DIR__ . '/../src/PDFEngine.php';
use TechNur\PhpPdfPro\PDFEngine;

$engine = new PDFEngine();
$test1 = __DIR__ . '/test1.pdf';
$test2 = __DIR__ . '/test2.pdf';
$outputDir = __DIR__ . '/../output/';

if (!is_dir($outputDir)) mkdir($outputDir, 0777, true);

echo "Testing Merge...\n";
$mergeOut = $outputDir . 'test_merged.pdf';
if ($engine->merge([$test1, $test2], $mergeOut)) {
    echo "SUCCESS: Merged to $mergeOut\n";
} else {
    echo "FAILED: Merge\n";
}

echo "Testing Extract...\n";
$extractOut = $outputDir . 'test_extracted.pdf';
if ($engine->organize($test1, [['page' => 2, 'rotation' => 0]], $extractOut)) {
    echo "SUCCESS: Extracted to $extractOut\n";
} else {
    echo "FAILED: Extract\n";
}

echo "Testing Annotate...\n";
$annoOut = $outputDir . 'test_annotated.pdf';
if ($engine->annotate($test2, 'CONFIDENTIAL', 50, 50, $annoOut)) {
    echo "SUCCESS: Annotated to $annoOut\n";
} else {
    echo "FAILED: Annotate\n";
}
