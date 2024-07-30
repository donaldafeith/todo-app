<?php
include 'db.php';

// Get the ID of the to-do item from the query string
$id = $_GET['id'];

// Prepare the SQL statement to fetch the to-do item based on the given ID
$stmt = $conn->prepare("SELECT * FROM todos WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form input values
    $name = $_POST['name'];
    $notes = $_POST['notes'];
    $date_received = $_POST['date_received'];
    $date_to_be_delivered = $_POST['date_to_be_delivered'];

    // Prepare the SQL statement to update the to-do item to prevent SQL injection
    $update_stmt = $conn->prepare("UPDATE todos SET name=?, notes=?, date_received=?, date_to_be_delivered=? WHERE id=?");
    $update_stmt->bind_param("ssssi", $name, $notes, $date_received, $date_to_be_delivered, $id);

    if ($update_stmt->execute() === TRUE) {
        // Handle file uploads if there are any
        if (!empty($_FILES['attachments']['name'][0])) {
            foreach ($_FILES['attachments']['name'] as $key => $attachment_name) {
                if ($attachment_name) {
                    $target_dir = "uploads/";
                    $target_file = $target_dir . basename($_FILES["attachments"]["name"][$key]);
                    if (move_uploaded_file($_FILES["attachments"]["tmp_name"][$key], $target_file)) {
                        $file_path = basename($_FILES["attachments"]["name"][$key]);
                        $description = $_POST['attachment_descriptions'][$key];
                        // Prepare the SQL statement to insert attachment details
                        $attachment_stmt = $conn->prepare("INSERT INTO attachments (todo_id, file_path, description) VALUES (?, ?, ?)");
                        $attachment_stmt->bind_param("iss", $id, $file_path, $description);
                        $attachment_stmt->execute();
                        $attachment_stmt->close();
                    }
                }
            }
        }

        // Redirect to the index page after successful update
        header('Location: index.php');
        exit;
    } else {
        // Display error message if the update query failed
        echo "Error: " . $update_stmt->error;
    }

    $update_stmt->close();
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit To-Do</title>
    <link rel="stylesheet" type="text/css" href="css/styles.css">
</head>
<body>
    <h1>Edit To-Do</h1>
    <!-- Form to edit the to-do item -->
    <form action="" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="name">Task Name:</label>
            <!-- Input field for task name with prefilled value -->
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($row['name']); ?>" required>
        </div>
        <div class="form-group">
            <label for="notes">Notes:</label>
            <!-- Textarea for notes with prefilled value -->
            <textarea id="notes" name="notes"><?php echo htmlspecialchars($row['notes']); ?></textarea>
        </div>
        <div class="form-group">
            <label for="attachments">Attachments:</label>
            <!-- Input field for file attachments, allows multiple files -->
            <input type="file" id="attachments" name="attachments[]" multiple>
        </div>
        <div class="form-group">
            <label for="attachment_descriptions">Attachment Description:</label>
            <!-- Textarea for descriptions of attachments -->
            <textarea id="attachment_descriptions" name="attachment_descriptions[]" placeholder="Description of attachment"></textarea>
        </div>
        <div class="form-group">
            <label for="date_received">Date Assigned:</label>
            <!-- Input field for the date the task was assigned with prefilled value -->
            <input type="date" id="date_received" name="date_received" value="<?php echo $row['date_received']; ?>" required>
        </div>
        <div class="form-group">
            <label for="date_to_be_delivered">Due Date:</label>
            <!-- Input field for the due date of the task with prefilled value -->
            <input type="date" id="date_to_be_delivered" name="date_to_be_delivered" value="<?php echo $row['date_to_be_delivered']; ?>" required>
        </div>
        <!-- Submit button to update the to-do item -->
        <button type="submit">Update To-Do</button>
    </form>
</body>
</html>