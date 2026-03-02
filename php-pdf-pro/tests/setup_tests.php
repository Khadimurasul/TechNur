<?php
require_once __DIR__ . '/../vendor/autoload.php';

use setasign\Fpdi\Fpdi;

$pdf1 = new Fpdi();
$pdf1->AddPage();
$pdf1->SetFont('Arial', 'B', 16);
$pdf1->Cell(40, 10, 'Test PDF 1 - Page 1');
$pdf1->AddPage();
$pdf1->Cell(40, 10, 'Test PDF 1 - Page 2');
$pdf1->Output('F', __DIR__ . '/test1.pdf');

$pdf2 = new Fpdi();
$pdf2->AddPage();
$pdf2->SetFont('Arial', 'B', 16);
$pdf2->Cell(40, 10, 'Test PDF 2 - Page 1');
$pdf2->Output('F', __DIR__ . '/test2.pdf');

echo "Generated test1.pdf and test2.pdf\n";
