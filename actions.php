<?php
$conn = new mysqli('localhost', 'root', '', 'students');
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

$action = $_POST['action'];
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    header('Location: dashboard.php?error=1');
    exit();
}

switch ($action) {
    case 'insert':
        $stmt = $conn->prepare('INSERT INTO admin (email, password) VALUES (?, ?)');
        $stmt->bind_param('ss', $email, $password);
        $stmt->execute();
        break;
    case 'update':
        $stmt = $conn->prepare('UPDATE admin SET password = ? WHERE email = ?');
        $stmt->bind_param('ss', $password, $email);
        $stmt->execute();
        break;
    case 'delete':
        $stmt = $conn->prepare('DELETE FROM admin WHERE email = ?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        break;
    case 'search':
        $stmt = $conn->prepare('SELECT * FROM admin WHERE email = ?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);
        header('Location: dashboard.php?data=' . json_encode($data));
        exit();
    case 'show':
        $result = $conn->query('SELECT * FROM admin');
        $data = $result->fetch_all(MYSQLI_ASSOC);
        header('Location: dashboard.php?data=' . json_encode($data));
        exit();
}

header('Location: dashboard.php');
$conn->close();
?>
