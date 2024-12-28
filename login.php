<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        header('Location: index.php?error=1');
        exit();
    }

    $conn = new mysqli('localhost', 'root', '', 'students');
    if ($conn->connect_error) {
        die('Connection failed: ' . $conn->connect_error);
    }

    $stmt = $conn->prepare('SELECT * FROM admin WHERE email = ? AND password = ?');
    $stmt->bind_param('ss', $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        header('Location: dashboard.php');
        // header('Location: test.php');
    } else {
        header('Location: index.php?error=1');
    }

    $stmt->close();
    $conn->close();
}
?>
