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
      if (!isset($request->data->bookID) || trim($request->data->bookID) == '' ||
          trim($request->data->title) == '' || trim($request->data->author) == '' ||
          trim($request->data->publishedDate) == '' || trim($request->data->description) == '') {
            http_response_code(400);
            echo json_encode(['message' => 'Missing required fields.']);
            exit;
      }

      // validate numeric id
      $bookID = (int)$request->data->bookID;

      if ($bookID <= 0) {
        http_response_code(400);
        echo json_encode(['message' => 'Invalid bookID.']);
        exit;
      }

      // Sanitize
      $title = mysqli_real_escape_string($con, trim($request->data->title));
      $author = mysqli_real_escape_string($con, trim($request->data->author));
      $publishedDate = mysqli_real_escape_string($con, trim($request->data->publishedDate));
      $description = mysqli_real_escape_string($con, trim($request->data->description));

      // Update database
      $sql = "UPDATE `books` SET `title` = '{$title}', `author` = '{$author}', `publishedDate` = '{$publishedDate}', 
      `description` = '{$description}' WHERE `bookID` = {$bookID} LIMIT 1";

      if (mysqli_query($con, $sql)) {
        http_response_code(200);
        echo json_encode([
          'data' => [
              'bookID' => $bookID,
              'title' => $title,
              'author' => $author,
              'publishedDate' => $publishedDate,
              'description' => $description
          ]
        ]);
      }
      else {
        http_response_code(422);
        echo json_encode(['message' => 'Database update failed.']);
      }

    } else {
      http_response_code(400);
      echo json_encode(['message' => 'No data received.']);
    }
?>