<?php
    header('Content-Type: application/json');
    ini_set('display_errors', 1);
    error_reporting(E_ALL);

    $uploadDir = 'uploads/';

    // check if file was uploaded
    if (!isset($_FILES['image']) || $_FILES['image']['error'] != UPLOAD_ERR_OK) {
      http_response_code(400);
      echo json_encode(['error' => 'no file uploaded or upload error occurred.']);
      exit;
    }

    // validate file type (allow only JPG, PNG, GIF)
    $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $fileTmpPath = $_FILES['image']['tmp_name'];
    $fileName = basename($_FILES['image']['name']);
    $fileType = mime_content_type($fileTmpPath);

    if (!in_array($fileType, $allowedMimeTypes)) {
      http_response_code(400);
      echo json_encode(['error' => 'Invalid image format. Only JPG, PNG, and GIF are allowed.']);
      exit;
    }

    // move file to uploads folder
    $targetFilePath = $uploadDir . $fileName;

    if(move_uploaded_file($fileTmpPath, $targetFilePath)) {
      http_response_code(200);
      echo json_encode(['message' => 'File upload successfully.', 'fileName' => $fileName]);
    }
    else {
      http_response_code(500);
      echo json_encode(['error' => 'Failed to move uploaded file.']);
    }


?>