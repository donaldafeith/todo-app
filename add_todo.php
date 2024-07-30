<?php
include 'db.php';

$name = $_POST['name'];
$notes = $_POST['notes'];
$date_received = $_POST['date_received'];
$date_to_be_delivered = $_POST['date_to_be_delivered'];

$sql = "INSERT INTO todos (name, notes, date_received, date_to_be_delivered)
VALUES ('$name', '$notes', '$date_received', '$date_to_be_delivered')";

if ($conn->query($sql) === TRUE) {
    $todo_id = $conn->insert_id;

    if (!empty($_FILES['attachments']['name'][0])) {
        foreach ($_FILES['attachments']['name'] as $key => $attachment_name) {
            if ($attachment_name) {
                $target_dir = "uploads/";
                $target_file = $target_dir . basename($_FILES["attachments"]["name"][$key]);
                if (move_uploaded_file($_FILES["attachments"]["tmp_name"][$key], $target_file)) {
                    $file_path = basename($_FILES["attachments"]["name"][$key]);
                    $description = $_POST['attachment_descriptions'][$key];
                    $attachment_sql = "INSERT INTO attachments (todo_id, file_path, description)
                    VALUES ('$todo_id', '$file_path', '$description')";
                    $conn->query($attachment_sql);
                }
            }
        }
    }

    echo "New record created successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();

header('Location: index.php');
?>
