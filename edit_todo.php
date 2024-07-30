<?php
include 'db.php';

$id = $_GET['id'];
$sql = "SELECT * FROM todos WHERE id=$id";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $notes = $_POST['notes'];
    $date_received = $_POST['date_received'];
    $date_to_be_delivered = $_POST['date_to_be_delivered'];

    $sql = "UPDATE todos SET name='$name', notes='$notes', date_received='$date_received', date_to_be_delivered='$date_to_be_delivered' WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
        if (!empty($_FILES['attachments']['name'][0])) {
            foreach ($_FILES['attachments']['name'] as $key => $attachment_name) {
                if ($attachment_name) {
                    $target_dir = "uploads/";
                    $target_file = $target_dir . basename($_FILES["attachments"]["name"][$key]);
                    if (move_uploaded_file($_FILES["attachments"]["tmp_name"][$key], $target_file)) {
                        $file_path = basename($_FILES["attachments"]["name"][$key]);
                        $description = $_POST['attachment_descriptions'][$key];
                        $attachment_sql = "INSERT INTO attachments (todo_id, file_path, description)
                        VALUES ('$id', '$file_path', '$description')";
                        $conn->query($attachment_sql);
                    }
                }
            }
        }

        echo "Record updated successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    header('Location: index.php');
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit To-Do</title>
</head>
<body>
    <h1>Edit To-Do</h1>
    <form action="" method="post" enctype="multipart/form-data">
        <input type="text" name="name" value="<?php echo $row['name']; ?>" required>
        <textarea name="notes"><?php echo $row['notes']; ?></textarea>
        <input type="file" name="attachments[]">
        <textarea name="attachment_descriptions[]" placeholder="Description of attachment"></textarea>
        <input type="date" name="date_received" value="<?php echo $row['date_received']; ?>" required>
        <input type="date" name="date_to_be_delivered" value="<?php echo $row['date_to_be_delivered']; ?>" required>
        <button type="submit">Update To-Do</button>
    </form>
</body>
</html>
