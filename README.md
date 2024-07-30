
# To-Do App

A simple PHP-based To-Do application with a MySQL backend. This application allows users to create, edit, delete, and complete to-do items, with the ability to attach files and provide descriptions for each attachment. You can also set due dates for the tasks and mark the day the task was assigned to you.

## Features

- View a list of to-do items with filtering options for pending, complete, and all to-dos.
- Create new to-do items with attachments and descriptions.
- Edit existing to-do items and their attachments.
- Delete to-do items.
- Mark to-do items as complete.

## Requirements

- PHP 7.0+
- MySQL 5.7+
- Apache Web Server
1. Clone the repository:
```
git clone https://github.com/donaldafeith/todo-app.git
```
Navigate to the project directory:

```
cd todo-app 
```

2. Set up the database:

Create a new database in MySQL.
Import the database.sql file to set up the required tables.
```
CREATE DATABASE todo_app;
USE todo_app;
CREATE TABLE todos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    notes TEXT,
    date_received DATE,
    date_to_be_delivered DATE,
    status ENUM('pending', 'complete') DEFAULT 'pending'
);
CREATE TABLE attachments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    todo_id INT,
    file_path VARCHAR(255),
    description TEXT,
    FOREIGN KEY (todo_id) REFERENCES todos(id) ON DELETE CASCADE
);
```
Configure the database connection:

Edit the config.php file to match your database credentials:
```
<?php
$servername = "localhost";
$username = "your_username";
$password = "your_password";
$dbname = "todo_app";
?>
```
Be sure to put the config.php in a server folder without public permissions. For example putting the config.php in the var folder. 
As long as you can link to it and the directory is accesible to your own scripts. It will work.

Ensure the uploads directory exists and has the correct permissions:

```
mkdir uploads
chmod 755 uploads
```
Start the Apache server and navigate to the project URL where you uploaded it to.
