<?php
    require 'connect.php';
    header('Content-Type: application/json');
    ini_set('display_errors', 1);
    error_reporting(E_ALL);

    // get the posted data
    $postdata = file_get_contents("php://input");

    if (isset($postdata) && !empty($postdata)) {
      $request = json_decode($postdata);

      // Validate required fields
      if (trim($request->data->title) == '' || trim($request->data->author) == '' ||
          trim($request->data->publishedDate) == '' || trim($request->data->description) == '') {
            http_response_code(400);
            echo json_encode(['message' => 'missing required fields.']);
            exit;
      }

      // Sanitize
      $title = mysqli_real_escape_string($con, trim($request->data->title));
      $author = mysqli_real_escape_string($con, trim($request->data->author));
      $publishedDate = mysqli_real_escape_string($con, trim($request->data->publishedDate));
      $description = mysqli_real_escape_string($con, trim($request->data->description));
      $imageName = mysqli_real_escape_string($con, trim($request->data->imageName));

      // Extract file name
      $origimg = str_replace('\\', '/', $imageName);
      $new = basename($origimg);
      if (empty($new)) {
        $new = 'placeholder_100.jpg';
      }

      // Allowed image file extensions
      $allowedExt = ['jpg', 'jpeg', 'png', 'gif'];
      $ext = strtolower(pathinfo($new, PATHINFO_EXTENSION));
      if ($new != 'placeholder_100.jpg' && !inarray($ext, $allowedExt)) {
        http_response_code(400);
        echo json_encode(['message' => 'Invalid image format. Only JPG, PNG, and GIF are allowed.']);
        exit;
      }

      // Check duplicate image name
      if ($new != 'placeholder_100.jpg') {
        $checkImageSql = "SELECT 1 FROM books WHERE imageName = '{$new}'";
        $checkImageResult = mysqli_query($con, $checkImageSql);
        if(mysqli_num_rows($checkImageResult) > 0) {
          http_response_code(409);
          echo json_encode(['message' => 'Duplicate image name.']);
          exit; 
        }
      }

      // Insert into database
      $sql = "INSERT INTO `books`(`bookID`, `title`, `author`, `publishedDate`, `description`, `imageName`)
        VALUES (null, '{$title}', '{$author}', '{$publishedDate}', '{$description}', '{$new}')";

      if (mysqli_query($con, $sql)) {
        http_response_code(201);
        echo json_encode([
          'data' => [
              'bookID' => mysqli_insert_id($con),
              'title' => $title,
              'author' => $author,
              'publishedDate' => $publishedDate,
              'description' => $description,
              'imageName' => $new
          ]
        ]);
      }
      else {
        http_response_code(422);
        echo json_encode(['message' => 'Database insert failed.']);
      }

    }


?>