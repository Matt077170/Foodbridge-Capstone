<?php
session_start();
include '../includes/db.php';


if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../logout.php");
    exit();
}

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];
    
    // Check if the user is trying to delete their own account
    if ($user_id == $_SESSION['user']['id']) {
        $_SESSION['message'] = "You cannot delete your own account.";
        $_SESSION['message_type'] = "danger";
    } else {
        // First, get the user's name for the log entry
        $stmt_get_user = $conn->prepare("SELECT fullname FROM users WHERE id = ?");
        $stmt_get_user->bind_param("i", $user_id);
        $stmt_get_user->execute();
        $user_result = $stmt_get_user->get_result();
        $user_data = $user_result->fetch_assoc();
        $stmt_get_user->close();

        if ($user_data) {
            $user_fullname = $user_data['fullname'];

            $stmt_delete = $conn->prepare("DELETE FROM users WHERE id = ?");
            $stmt_delete->bind_param("i", $user_id);

            if ($stmt_delete->execute()) {
                log_action($conn, $_SESSION['user']['id'], "Deleted user account for: " . $user_fullname, $_SESSION['user']['role']);
                $_SESSION['message'] = "User account for " . htmlspecialchars($user_fullname) . " deleted successfully!";
                $_SESSION['message_type'] = "success";
            } else {
                $_SESSION['message'] = "Error deleting user account: " . $conn->error;
                $_SESSION['message_type'] = "danger";
            }
            $stmt_delete->close();
        } else {
            $_SESSION['message'] = "User not found.";
            $_SESSION['message_type'] = "danger";
        }
    }
} else {
    $_SESSION['message'] = "No user ID specified.";
    $_SESSION['message_type'] = "danger";
}

header("Location: dashboard.php");
exit();
