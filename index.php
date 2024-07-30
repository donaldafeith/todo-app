<!DOCTYPE html>
<html>
<head>
    <title>Task List</title>
    <link rel="stylesheet" type="text/css" href="css/stylesheet.css">
</head>
<body>
    <h1>To-Do List</h1>
    <form action="add_todo.php" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="name">Task Name:
            <input type="text" id="name" name="name" placeholder="To-Do Item Name" required></label>
        </div>
        <div class="form-group">
            <label for="notes">Notes:</label>
            <textarea id="notes" name="notes" placeholder="Notes"></textarea>
        </div>
        <div class="form-group">
            <label for="attachments">Attachments:</label>
            <input type="file" id="attachments" name="attachments[]" multiple>
        </div>
        <div class="form-group">
            <label for="attachment_descriptions">Attachment Description:</label>
            <textarea id="attachment_descriptions" name="attachment_descriptions[]" placeholder="Description of attachment"></textarea>
        </div>
        <div class="form-group">
            <label for="date_received">Date Assigned:</label>
            <input type="date" id="date_received" name="date_received" required>
        </div>
        <div class="form-group">
            <label for="date_to_be_delivered">Due Date:</label>
            <input type="date" id="date_to_be_delivered" name="date_to_be_delivered" required>
        </div>
        <button type="submit">Add To-Do</button>
    </form>

    <h2>Filter</h2>
    <div class="filter-form">
        <form action="" method="get">
            <select name="filter" onchange="this.form.submit()">
                <option value="all" <?php if (isset($_GET['filter']) && $_GET['filter'] == 'all') echo 'selected'; ?>>All</option>
                <option value="pending" <?php if (isset($_GET['filter']) && $_GET['filter'] == 'pending') echo 'selected'; ?>>Pending</option>
                <option value="complete" <?php if (isset($_GET['filter']) && $_GET['filter'] == 'complete') echo 'selected'; ?>>Complete</option>
            </select>
        </form>
    </div>

    <div>
        <?php
        include 'db.php';
        $filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
        $sql = "SELECT * FROM todos";
        if ($filter !== 'all') {
            $sql .= " WHERE status='$filter'";
        }
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<div class='todo-card " . ($row['status'] == 'complete' ? 'completed' : '') . "'>";
                echo "<h3>" . $row['name'] . "</h3>";
                echo "<p>" . $row['notes'] . "</p>";

                $todo_id = $row['id'];
                $attachment_sql = "SELECT * FROM attachments WHERE todo_id=$todo_id";
                $attachment_result = $conn->query($attachment_sql);

                if ($attachment_result->num_rows > 0) {
                    while ($attachment = $attachment_result->fetch_assoc()) {
                        $file_path = 'uploads/' . $attachment['file_path'];
                        $file_extension = pathinfo($file_path, PATHINFO_EXTENSION);
                        $image_extensions = ['jpg', 'jpeg', 'png', 'gif'];

                        if (in_array(strtolower($file_extension), $image_extensions)) {
                            echo "<p>Attachment: <img src='$file_path' alt='Attachment' style='max-width:100px;'></p>";
                        } else {
                            echo "<p>Attachment: <a href='$file_path' target='_blank'>Show</a></p>";
                        }
                        echo "<p>Description: " . $attachment['description'] . "</p>";
                    }
                }

                echo "<p>Received: " . $row['date_received'] . "</p>";
                echo "<p>To be delivered: " . $row['date_to_be_delivered'] . "</p>";
                echo "<a href='edit_todo.php?id=" . $row['id'] . "'>Edit</a> | ";
                echo "<a href='delete_todo.php?id=" . $row['id'] . "'>Delete</a> | ";
                if ($row['status'] == 'pending') {
                    echo "<a href='complete_todo.php?id=" . $row['id'] . "'>Complete</a>";
                }
                echo "</div>";
            }
        } else {
            echo "<p>No to-dos found.</p>";
        }

        $conn->close();
        ?>
    </div>
</body>
</html>
