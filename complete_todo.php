<?php
// Include the database connection file
include 'db.php';

// Get the ID of the to-do item from the query string
$id = $_GET['id'];

// SQL query to update the status of the to-do item to 'complete'
$sql = "UPDATE todos SET status='complete' WHERE id=$id";

// Execute the query and check if it was successful
if ($conn->query($sql) === TRUE) {
    echo "Record updated successfully";
} else {
    // Display an error message if the query failed
    echo "Error: " . $sql . "<br>" . $conn->error;
}

// Close the database connection
$conn->close();

// Redirect to the index page
header('Location: index.php');
?>