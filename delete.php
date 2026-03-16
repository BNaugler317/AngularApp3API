<?php
    require 'connect.php';
    header('Content-Type: application/json');
    ini_set('display_errors', 1);
    error_reporting(E_ALL);

    // get the posted data
    $postdata = file_get_contents("php://input");

    if (isset($postdata) && !empty($postdata)) {
      $request = json_decode($postdata);

      // validate required field
      if (!isset($request->data->bookID) || trim($request->data->bookID) == '') {
          http_response_code(400);
          echo json_encode(['message' => 'Missing bookID.']);
          exit;
      }

      //validate numeric id
      $bookID = (int)$request->data->bookID;

      if ($bookID <= 0) {
        http_response_code(400);
        echo json_encode(['message' => 'Invalid bookID']);
        exit;
      }

      // delete from the database
      $sql = "DELETE FROM `books` WHERE `bookID` = {$bookID} LIMIT 1";

      if (mysqli_query($con, $sql)) {
        if (mysqli_affected_rows($con) > 0) {
            http_response_code(200);
            echo json_encode([
              'message' => 'Book deleted successfully.',
              'bookID' => $bookID
            ]);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Book not found.']);
        }
      } else {
          http_response_code(422);
          echo json_encode(['message' => 'Database delete failed.']);
      }
    } else {
        http_response_code(400);
        echo json_encode(['message' => 'No data received']);
    }
?>