<?php
session_start();

// ✅ Enable error reporting for debugging (Disable in production)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// ✅ Regenerate session ID to prevent fixation attacks
session_regenerate_id(true);

// ✅ Database credentials
$host = "localhost";       
$username = "root";        
$password = "";            
$dbname = "navsclubs"; 

// ✅ Establish a secure database connection
$conn = new mysqli($host, $username, $password, $dbname);

// ✅ Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// ✅ Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // ✅ Sanitize and validate email input
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
    $password = trim($_POST['password']);

    if (!$email || empty($password)) {
        echo "<script>alert('Please enter a valid email and password.'); window.location.href='LoginAccount.php';</script>";
        exit();
    }

    // ✅ Prepare secure SQL query (Prevent SQL Injection)
    $stmt = $conn->prepare("SELECT usersid, password_hash, fullname, nickname, rank, usertype, role FROM personal_information WHERE email = ?");
    
    if (!$stmt) {
        die("SQL Error: " . $conn->error);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // ✅ Check if user exists
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // ✅ Verify hashed password
        if (password_verify($password, $row['password_hash'])) {
            // ✅ Store user session data securely
            $_SESSION['authenticated'] = true;
            $_SESSION['usersid'] = $row['usersid'];
            $_SESSION['fullname'] = $row['fullname'];
            $_SESSION['nickname'] = $row['nickname'];
            $_SESSION['rank'] = $row['rank'];
            $_SESSION['usertype'] = $row['usertype'];
            $_SESSION['role'] = $row['role'];

            // ✅ Redirect based on user type
            if ($row["usertype"] == "admin") {
                header("Location: http://localhost/Repair-Shop-Locator-new/ADMIN/AppointmentNotifications.php");
                exit();
            } else {
                header("Location: http://localhost/NewNavsClubs-GIF/USER/IndexHome.php");
                exit();
            }
        } else {
            echo "<script>alert('Invalid email or password.'); window.location.href='LoginAccount.php';</script>";
        }
    } else {
        echo "<script>alert('No user found with this email.'); window.location.href='LoginAccount.php';</script>";
    }

    $stmt->close();
}

// ✅ Close database connection
$conn->close();
?>
