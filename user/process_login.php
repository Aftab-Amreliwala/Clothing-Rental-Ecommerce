<?php
session_start();
include('../admin/connect.php');

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validation
    if (empty($email) || empty($password)) {
        $response['message'] = 'Please enter your email and password.';
        echo json_encode($response);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = 'Please enter a valid email address.';
        echo json_encode($response);
        exit;
    }

    // Exact match with plaintext password (matching registration)
    $stmt = mysqli_prepare($con, "SELECT uid, fname, lname, email, pass FROM user_master WHERE email = ? AND pass = ? AND status = 1");
    mysqli_stmt_bind_param($stmt, "ss", $email, $password);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);
        // Consistent sessions
        $_SESSION['user_id'] = $user['uid'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_name'] = $user['fname'] . ' ' . $user['lname'];
        $response['success'] = true;
        $response['message'] = 'Login successful! Redirecting...';
    } else {
        $response['message'] = 'invalid email id and password';
    }

    mysqli_stmt_close($stmt);
} else {
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);
mysqli_close($con);
?>

