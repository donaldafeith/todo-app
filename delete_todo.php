<?php
// Include the database connection file
include 'db.php';

// Get the ID of the to-do item from the query string
$id = $_GET['id'];

// Prepare the SQL statement to prevent SQL injection
$stmt = $conn->prepare("DELETE FROM todos WHERE id=?");

// Bind the ID parameter to the SQL query
$stmt->bind_param("i", $id);

// Execute the query and check if it was successful
if ($stmt->execute() === TRUE) {
    echo "Record deleted successfully";
} else {
    // Display an error message if the query failed
    echo "Error: " . $stmt->error;
}

// Close the prepared statement
$stmt->close();

// Close the database connection
$conn->close();

// Redirect to the index page
header('Location: index.php');
?>