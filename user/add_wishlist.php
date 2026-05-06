<?php
session_start();
header('Content-Type: application/json');
include('../admin/connect.php');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit;
}

$user_id = $_SESSION['user_id'];
$pid = intval($_POST['pid'] ?? 0);
$action = $_POST['action'] ?? 'toggle'; // 'add', 'remove', 'toggle'

if ($pid <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid product']);
    exit;
}

if ($action === 'remove') {
    // Remove from wishlist
    $stmt = mysqli_prepare($con, "DELETE FROM wishlist WHERE uid = ? AND pid = ?");
    mysqli_stmt_bind_param($stmt, "ii", $user_id, $pid);
    $success = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    $message = $success ? 'Removed from wishlist' : 'Failed to remove';
} else {
    // Add or toggle
    $check_stmt = mysqli_prepare($con, "SELECT wid FROM wishlist WHERE uid = ? AND pid = ?");
    mysqli_stmt_bind_param($check_stmt, "ii", $user_id, $pid);
    mysqli_stmt_execute($check_stmt);
    $exists = mysqli_stmt_get_result($check_stmt)->num_rows > 0;
    mysqli_stmt_close($check_stmt);

    if ($exists) {
        // Already exists, remove
        $stmt = mysqli_prepare($con, "DELETE FROM wishlist WHERE uid = ? AND pid = ?");
        mysqli_stmt_bind_param($stmt, "ii", $user_id, $pid);
        $success = mysqli_stmt_execute($stmt);
        $message = $success ? 'Removed from wishlist' : 'Failed to remove';
    } else {
        // Add to wishlist
        $stmt = mysqli_prepare($con, "INSERT INTO wishlist (uid, pid) VALUES (?, ?)");
        mysqli_stmt_bind_param($stmt, "ii", $user_id, $pid);
        $success = mysqli_stmt_execute($stmt);
        $message = $success ? 'Added to wishlist' : 'Failed to add';
    }
    mysqli_stmt_close($stmt);
}

echo json_encode([
    'success' => $success,
    'message' => $message,
    'in_wishlist' => !$exists,
    'action' => $exists ? 'remove' : 'add'
]);
?>

