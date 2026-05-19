<?php
session_start();
include "config.php";

if (!isset($_SESSION["admin"])) {
    header("Location: login.php");
    exit;
}

$id = intval($_GET["id"] ?? 0);

$stmt = mysqli_prepare($conn, "
    UPDATE edit_requests
    SET status = 'rejected'
    WHERE request_id = ?
    AND status = 'pending'
");

mysqli_stmt_bind_param($stmt, "i", $id);

mysqli_stmt_execute($stmt);

header("Location: admin_dashboard.php");
exit;
?>