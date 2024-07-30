<?php
// Include the database connection file
include 'db.php';

// Retrieve form input values
$name = $_POST['name'];
$notes = $_POST['notes'];
$date_received = $_POST['date_received'];
$date_to_be_delivered = $_POST['date_to_be_delivered'];

// Prepare the SQL statement to insert a new to-do item to prevent SQL injection
$stmt = $conn->prepare("INSERT INTO todos (name, notes, date_received, date_to_be_delivered) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $name, $notes, $date_received, $date_to_be_delivered);

if ($stmt->execute() === TRUE) {
    // Get the ID of the newly inserted to-do item
    $todo_id = $stmt->insert_id;

    // Handle file uploads if there are any
    if (!empty($_FILES['attachments']['name'][0])) {
        foreach ($_FILES['attachments']['name'] as $key => $attachment_name) {
            if ($attachment_name) {
                // Set the target directory for uploads
                $target_dir = "uploads/";
                $target_file = $target_dir . basename($_FILES["attachments"]["name"][$key]);

                // Move the uploaded file to the target directory
                if (move_uploaded_file($_FILES["attachments"]["tmp_name"][$key], $target_file)) {
                    $file_path = basename($_FILES["attachments"]["name"][$key]);
                    $description = $_POST['attachment_descriptions'][$key];

                    // Prepare the SQL statement to insert attachment details to prevent SQL injection
                    $attachment_stmt = $conn->prepare("INSERT INTO attachments (todo_id, file_path, description) VALUES (?, ?, ?)");
                    $attachment_stmt->bind_param("iss", $todo_id, $file_path, $description);
                    $attachment_stmt->execute();
                    $attachment_stmt->close();
                }
            }
        }
    }

    // Redirect to the index page after successful insert
    header('Location: index.php');
    exit;
} else {
    // Display error message if the insert query failed
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>