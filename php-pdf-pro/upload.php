<?php
header('Content-Type: application/json');

$uploadDir = 'uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$response = ['success' => false, 'files' => []];

if (!isset($_FILES['pdfs'])) {
    $response['error'] = 'No files uploaded';
    echo json_encode($response);
    exit;
}

foreach ($_FILES['pdfs']['tmp_name'] as $key => $tmpName) {
    if ($_FILES['pdfs']['error'][$key] === UPLOAD_ERR_OK) {
        $originalName = $_FILES['pdfs']['name'][$key];
        $fileExtension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        if ($fileExtension !== 'pdf') {
            continue;
        }

        $newName = uniqid('pdf_') . '.pdf';
        $destination = $uploadDir . $newName;

        if (move_uploaded_file($tmpName, $destination)) {
            $response['files'][] = [
                'name' => $originalName,
                'path' => $destination
            ];
        }
    }
}

if (!empty($response['files'])) {
    $response['success'] = true;
} else {
    $response['error'] = 'No valid PDF files were uploaded';
}

echo json_encode($response);
