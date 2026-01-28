<?php
header('Content-Type: application/json');
require_once __DIR__ . '/vendor/autoload.php';

use TechNur\PhpPdfPro\PDFEngine;

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['action'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
    exit;
}

/**
 * Validate that a file path is within the allowed uploads directory.
 */
function validatePath($path) {
    $realUploadsDir = realpath(__DIR__ . '/uploads');
    $realPath = realpath(__DIR__ . '/' . $path);

    if (!$realPath || strpos($realPath, $realUploadsDir) !== 0) {
        throw new \Exception("Invalid file path: " . $path);
    }
    return $realPath;
}

$engine = new PDFEngine();
$outputDir = 'output/';
if (!is_dir($outputDir)) {
    mkdir($outputDir, 0777, true);
}

$response = ['success' => false];

try {
    switch ($data['action']) {
        case 'merge':
            $outputName = !empty($data['outputName']) ? $data['outputName'] : 'merged.pdf';
            if (!str_ends_with($outputName, '.pdf')) $outputName .= '.pdf';
            $outputPath = $outputDir . uniqid() . '_' . basename($outputName);

            $validFiles = [];
            foreach ($data['files'] as $file) {
                $validFiles[] = validatePath($file);
            }

            if ($engine->merge($validFiles, $outputPath)) {
                $response['success'] = true;
                $response['downloadUrl'] = $outputPath;
            } else {
                $response['error'] = 'Merging failed';
            }
            break;

        case 'extract':
        case 'delete':
            $file = validatePath($data['file']);
            $rangeStr = $data['range'];
            $outputPath = $outputDir . ($data['action'] == 'delete' ? 'deleted_' : 'extracted_') . uniqid() . '.pdf';

            $targetPages = [];
            $parts = explode(',', $rangeStr);
            foreach ($parts as $part) {
                $part = trim($part);
                if (strpos($part, '-') !== false) {
                    list($start, $end) = explode('-', $part);
                    for ($i = (int)$start; $i <= (int)$end; $i++) {
                        $targetPages[] = $i;
                    }
                } else {
                    $targetPages[] = (int)$part;
                }
            }

            $pages = [];
            if ($data['action'] == 'delete') {
                $temp_pdf = new \setasign\Fpdi\Fpdi();
                $totalPageCount = $temp_pdf->setSourceFile($file);
                for ($i = 1; $i <= $totalPageCount; $i++) {
                    if (!in_array($i, $targetPages)) {
                        $pages[] = ['page' => $i, 'rotation' => 0];
                    }
                }
            } else {
                foreach ($targetPages as $p) {
                    $pages[] = ['page' => $p, 'rotation' => 0];
                }
            }

            if ($engine->organize($file, $pages, $outputPath)) {
                $response['success'] = true;
                $response['downloadUrl'] = $outputPath;
            } else {
                $response['error'] = 'Operation failed';
            }
            break;

        case 'rotate':
            $file = validatePath($data['file']);
            $rotation = (int)$data['rotation'];
            $outputPath = $outputDir . 'rotated_' . uniqid() . '.pdf';

            $engine_temp = new \setasign\Fpdi\Fpdi();
            $pageCount = $engine_temp->setSourceFile($file);
            $pages = [];
            for ($i = 1; $i <= $pageCount; $i++) {
                $pages[] = ['page' => $i, 'rotation' => $rotation];
            }

            if ($engine->organize($file, $pages, $outputPath)) {
                $response['success'] = true;
                $response['downloadUrl'] = $outputPath;
            } else {
                $response['error'] = 'Rotation failed';
            }
            break;

        case 'annotate':
            $file = validatePath($data['file']);
            $text = $data['text'];
            $x = (int)$data['x'];
            $y = (int)$data['y'];
            $page = !empty($data['page']) ? (int)$data['page'] : null;
            $outputPath = $outputDir . 'annotated_' . uniqid() . '.pdf';

            if ($engine->annotate($file, $text, $x, $y, $outputPath, $page)) {
                $response['success'] = true;
                $response['downloadUrl'] = $outputPath;
            } else {
                $response['error'] = 'Annotation failed';
            }
            break;

        case 'whiteout':
        case 'redact':
            $file = validatePath($data['file']);
            $rect = [
                'type' => $data['action'],
                'x' => (int)$data['x'],
                'y' => (int)$data['y'],
                'w' => (int)$data['w'],
                'h' => (int)$data['h']
            ];
            if (!empty($data['page'])) {
                $rect['page'] = (int)$data['page'];
            }
            $outputPath = $outputDir . $data['action'] . '_' . uniqid() . '.pdf';

            if ($engine->overlayRect($file, [$rect], $outputPath)) {
                $response['success'] = true;
                $response['downloadUrl'] = $outputPath;
            } else {
                $response['error'] = ucfirst($data['action']) . ' failed';
            }
            break;

        case 'remove_metadata':
            $file = validatePath($data['file']);
            $outputPath = $outputDir . 'cleaned_' . uniqid() . '.pdf';

            if ($engine->cleanMetadata($file, $outputPath)) {
                $response['success'] = true;
                $response['downloadUrl'] = $outputPath;
            } else {
                $response['error'] = 'Metadata removal failed';
            }
            break;

        default:
            $response['error'] = 'Unknown action';
    }
} catch (\Exception $e) {
    $response['error'] = $e->getMessage();
}

echo json_encode($response);
