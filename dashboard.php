<?php
session_start();

$servername = "localhost"; // Your database server
$username = "root"; // Your database username
$password = ""; // Your database password
$dbname = "students"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}




// Handle actions for insert, update, delete, search
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Insert student
    if (isset($_POST['insert'])) {
        $name = $_POST['name'];
        $arid_number = $_POST['arid_number'];
        $gpa = $_POST['gpa'];

        if (empty($name) || empty($arid_number) || empty($gpa)) {
            $error = "All fields are required for insert!";
        } else {
            $sql = "INSERT INTO student (name, aridnumber, gpa) VALUES ('$name', '$arid_number', '$gpa')";
            if ($conn->query($sql) === TRUE) {
                $success = "Student added successfully!";
            } else {
                $error = "Error: " . $conn->error;
            }
        }
    }

    // Update student
    if (isset($_POST['update'])) {
        $arid_number = $_POST['arid_number'];
        $name = $_POST['name'];
        $gpa = $_POST['gpa'];

        if (empty($arid_number) || empty($name) || empty($gpa)) {
            $error = "All fields are required for update!";
        } else {
            $sql = "UPDATE student SET name='$name', gpa='$gpa' WHERE aridnumber='$arid_number'";
            if ($conn->query($sql) === TRUE) {
                $success = "Student updated successfully!";
            } else {
                $error = "Error: " . $conn->error;
            }
        }
    }

    // Delete student
    if (isset($_POST['delete'])) {
        if ($_POST['delete_option'] == "all") {
            $sql = "DELETE FROM student";
            if ($conn->query($sql) === TRUE) {
                $success = "All students deleted successfully!";
            } else {
                $error = "Error: " . $conn->error;
            }
        } elseif ($_POST['delete_option'] == "search") {
            $arid_number = $_POST['arid_number'];
            $sql = "DELETE FROM student WHERE aridnumber='$arid_number'";
            if ($conn->query($sql) === TRUE) {
                $success = "Student with ARID $arid_number deleted successfully!";
            } else {
                $error = "Error: " . $conn->error;
            }
        }
    }

    // Search student by ARID number
    if (isset($_POST['search'])) {
        $arid_number = $_POST['arid_number'];
        if (empty($arid_number)) {
            $error = "ARID number is required for search!";
        } else {
            $sql = "SELECT * FROM student WHERE aridnumber='$arid_number'";
            $result = $conn->query($sql);
            if ($result->num_rows == 0) {
                $error = "No student found with ARID number $arid_number!";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
           background-color: #3266A8;
            overflow-x: hidden;
            overflow-y: scroll;
        }
        .form-container {
          
            padding: 20px;
            border-radius: 8px;
           gap: 20px;
            width: 100%;
           justify-content: center;
            display: flex;
        }
        .form-1{
            background-color: white;
            width: 50%;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }
        .table{
            background-color: white;
            width: 50%;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }
        input[type="text"], input[type="number"], input[type="text"] {
            width: 80%;
            padding: 10px;
            margin: 10px 10%;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 80%;
            margin: 10px 10%;
        }
        button:hover {
            background-color: #45a049;
        }
        .hidden {
            display: none;
        }
        .error {
            color: red;
        }
        .success {
            color: green;
        }
    </style>
</head>
<body>

<div class="form-container">
    <div class="form-1">
    <h2>Welcome, </h2>
    
    <!-- Insert Student Form -->
    <form method="POST">
        <h3>Insert Student</h3>
        <input type="text" name="name" placeholder="Name" required>
        <input type="text" name="arid_number" placeholder="ARID Number" required>
        <input type="text" name="gpa" placeholder="GPA" required>
        <button type="submit" name="insert">Insert</button>
    </form>

    <!-- Update Student Form -->
    <form method="POST">
        <h3>Update Student</h3>
        <input type="text" name="arid_number" placeholder="ARID Number to Update" required>
        <button type="submit" name="search">Search</button>
    </form>

    <?php if (isset($result) && $result->num_rows > 0): ?>
    <form method="POST">
        <h3>Update Data for ARID <?php echo $arid_number; ?></h3>
        <?php
            $row = $result->fetch_assoc();
        ?>
        <input type="text" name="name" placeholder="Name" value="<?php echo $row['name']; ?>" required>
        <input type="text" name="gpa" placeholder="GPA" value="<?php echo $row['gpa']; ?>" required>
        <input type="text" name="arid_number" value="<?php echo $row['aridnumber']; ?>">
        <button type="submit" name="update">Update</button>
    </form>
    <?php endif; ?>

    <!-- Delete Student Form -->
    <form method="POST">
        <h3>Delete Student</h3>
        <input type="radio" id="delete_all" name="delete_option" value="all">
        <label for="delete_all">Delete All</label><br>
        <input type="radio" id="delete_search" name="delete_option" value="search">
        <label for="delete_search">Delete by ARID Number</label><br>

        <div id="delete_by_arid" class="hidden">
            <input type="text" name="arid_number" placeholder="ARID Number" required>
        </div>

        <button type="submit" name="delete">Delete</button>

    </form>
    </div>
<!-- show btn -->
 <div class="table">
<form method="POST">
    <button type="submit" name="show_data">Show All Students</button>
</form>

<!-- Displaying the data in a table -->
<?php
if (isset($_POST['show_data'])) {
    $sql = "SELECT * FROM student";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<table border='1' cellpadding='10' style='width:100%; margin-top:20px;'>";
        echo "<tr><th>Name</th><th>ARID Number</th><th>GPA</th></tr>";

        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
           
            echo "<td>" . $row['name'] . "</td>";
            echo "<td>" . $row['aridnumber'] . "</td>";
            echo "<td>" . $row['gpa'] . "</td>";
            echo "</tr>";
        }

        echo "</table>";
    } else {
        echo "<p>No students found in the database.</p>";
    }
}
?>
</div>
    <script>
        const deleteBySearch = document.getElementById('delete_search');
        const deleteByAridDiv = document.getElementById('delete_by_arid');
        deleteBySearch.addEventListener('change', function() {
            deleteByAridDiv.classList.remove('hidden');
        });

        const deleteAll = document.getElementById('delete_all');
        deleteAll.addEventListener('change', function() {
            deleteByAridDiv.classList.add('hidden');
        });
    </script>

    <!-- Show Success or Error Messages -->
    <?php if (isset($success)): ?>
        <p class="success"><?php echo $success; ?></p>
    <?php endif; ?>
    <?php if (isset($error)): ?>
        <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>
</div>

</body>
</html>
