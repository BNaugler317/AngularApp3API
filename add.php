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

    }


?>