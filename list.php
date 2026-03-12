<?php
  require 'connect.php';

  $books = [];

  $sql = "SELECT bookID, title, author, publishedDate, description FROM books";

  if ($result = mysqli_query($con, $sql)) {

    $count = 0;

    while ($row = mysqli_fetch_assoc($result)) {
      
      $books[$count]['bookID'] = $row['bookID'];
      $books[$count]['title'] = $row['title'];
      $books[$count]['author'] = $row['author'];
      $books[$count]['publishedDate'] = $row['publishedDate'];
      $books[$count]['description'] = $row['description'];

      $count++;
    }

    echo json_encode(['data'=>$books]);
  }
  else {
    http_response_code(404);
  }
?>