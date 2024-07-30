<?php
include 'db.php';

$id = $_GET['id'];

$sql = "UPDATE todos SET status='complete' WHERE id=$id";

if ($conn->query($sql) === TRUE) {
    echo "Record updated successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();

header('Location: index.php');
?>
