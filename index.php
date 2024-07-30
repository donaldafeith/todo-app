<!DOCTYPE html>
<html>
<head>
    <title>To-Do App</title>
    <!-- Link to the CSS stylesheet for styling the app -->
    <link rel="stylesheet" type="text/css" href="css/styles.css">
</head>
<body>
    <h1>To-Do List</h1>
    <!-- Form to add a new to-do item -->
    <form action="add_todo.php" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="name">Task Name:</label>
            <!-- Input field for task name -->
            <input type="text" id="name" name="name" placeholder="To-Do Item Name" required>
        </div>
        <div class="form-group">
            <label for="notes">Notes:</label>
            <!-- Textarea for additional notes -->
            <textarea id="notes" name="notes" placeholder="Notes"></textarea>
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
            <!-- Input field for the date the task was assigned -->
            <input type="date" id="date_received" name="date_received" required>
        </div>
        <div class="form-group">
            <label for="date_to_be_delivered">Due Date:</label>
            <!-- Input field for the due date of the task -->
            <input type="date" id="date_to_be_delivered" name="date_to_be_delivered" required>
        </div>
        <!-- Submit button to add the to-do item -->
        <button type="submit">Add To-Do</button>
    </form>

    <h2>Filter</h2>
    <!-- Form to filter the to-do list based on status -->
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
        // Include the database connection file
        include 'db.php';

        // Determine the filter criteria
        $filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

        // Prepare the SQL query to fetch to-do items based on the filter
        $sql = "SELECT * FROM todos";
        if ($filter !== 'all') {
            $sql .= " WHERE status=?";
        }
        $stmt = $conn->prepare($sql);

        // Bind the filter parameter if necessary
        if ($filter !== 'all') {
            $stmt->bind_param("s", $filter);
        }

        // Execute the query
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if there are results
        if ($result->num_rows > 0) {
            // Loop through each to-do item
            while ($row = $result->fetch_assoc()) {
                echo "<div class='todo-card " . ($row['status'] == 'complete' ? 'completed' : '') . "'>";
                echo "<h3>" . htmlspecialchars($row['name']) . "</h3>";
                echo "<p>" . htmlspecialchars($row['notes']) . "</p>";

                // Fetch and display attachments for the to-do item
                $todo_id = $row['id'];
                $attachment_sql = "SELECT * FROM attachments WHERE todo_id=?";
                $attachment_stmt = $conn->prepare($attachment_sql);
                $attachment_stmt->bind_param("i", $todo_id);
                $attachment_stmt->execute();
                $attachment_result = $attachment_stmt->get_result();

                if ($attachment_result->num_rows > 0) {
                    while ($attachment = $attachment_result->fetch_assoc()) {
                        $file_path = 'uploads/' . htmlspecialchars($attachment['file_path']);
                        $file_extension = pathinfo($file_path, PATHINFO_EXTENSION);
                        $image_extensions = ['jpg', 'jpeg', 'png', 'gif'];

                        if (in_array(strtolower($file_extension), $image_extensions)) {
                            echo "<p>Attachment: <img src='$file_path' alt='Attachment' style='max-width:100px;'></p>";
                        } else {
                            echo "<p>Attachment: <a href='$file_path' target='_blank'>Show</a></p>";
                        }
                        echo "<p>Description: " . htmlspecialchars($attachment['description']) . "</p>";
                    }
                }
                $attachment_stmt->close();

                // Display task dates and action links
                echo "<p>Received: " . htmlspecialchars($row['date_received']) . "</p>";
                echo "<p>To be delivered: " . htmlspecialchars($row['date_to_be_delivered']) . "</p>";
                echo "<a href='edit_todo.php?id=" . $row['id'] . "'>Edit</a> | ";
                echo "<a href='delete_todo.php?id=" . $row['id'] . "'>Delete</a> | ";
                if ($row['status'] == 'pending') {
                    echo "<a href='complete_todo.php?id=" . $row['id'] . "'>Complete</a>";
                }
                echo "</div>";
            }
        } else {
            // Display message if no to-dos are found
            echo "<p>No tasks found.</p>";
        }

        // Close the database connection
        $stmt->close();
        $conn->close();
        ?>
    </div>
</body>
</html>