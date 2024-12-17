<?php
session_start();

// Redirect to login page if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Connect to the database
include 'db.php';

// Use the `id` parameter from the POST body, fallback to the logged-in user ID if not provided
$user_id = isset($_POST['id']) ? (int)$_POST['id'] : $_SESSION['user_id'];

// Fetch user information based on the provided `id` parameter
$query = "SELECT username, role FROM users WHERE id = $user_id";
$result = $conn->query($query);

if ($result && $result->num_rows === 1) {
    $user = $result->fetch_assoc();
    $username = htmlspecialchars($user['username']);
    $role = htmlspecialchars($user['role']);
} else {
    // If user doesn't exist, show an error
    $username = "Unknown User";
    $role = "Unknown Role";
}

// Fetch tasks for the specified user ID
$tasks_query = "SELECT * FROM todos WHERE user_id = $user_id";
$tasks_result = $conn->query($tasks_query);

// Logout functionality
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
            width: 400px;
            text-align: center;
        }
        .container h1 {
            margin-bottom: 20px;
        }
        ul {
            list-style: none;
            padding: 0;
        }
        li {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-bottom: 10px;
            text-align: left;
            background-color: #f9f9f9;
        }
        .logout {
            margin-top: 20px;
            color: red;
        }
        a {
            text-decoration: none;
            color: blue;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome, <?php echo $username; ?>!</h1>

        <h2>Your Tasks</h2>
        <ul>
            <?php
            // Display tasks
            if ($tasks_result && $tasks_result->num_rows > 0) {
                while ($task = $tasks_result->fetch_assoc()) {
                    echo "<li>" . htmlspecialchars($task['task']) . "</li>";
                }
            } else {
                echo "<li>No tasks found.</li>";
            }

            ?>
        </ul>

        <form method="POST" action="index.php" style="display: none;">
            <input type="hidden" name="id" value="<?php echo $user_id; ?>">
            <button type="submit">Refresh</button>
        </form>

        <p class="logout">
            <a href="index.php?logout=true">Logout</a>
        </p>
    </div>
</body>
</html>

