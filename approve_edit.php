<?php
session_start();
include "config.php";

if (!isset($_SESSION["admin"])) {
    header("Location: login.php");
    exit;
}

$id = intval($_GET["id"] ?? 0);

$stmt = mysqli_prepare($conn, "
    SELECT *
    FROM edit_requests
    WHERE request_id = ?
    AND status = 'pending'
    LIMIT 1
");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$request = mysqli_fetch_assoc($result)) {
    die("الطلب غير موجود أو تمت معالجته مسبقًا");
}

mysqli_begin_transaction($conn);

try {
    $update = mysqli_prepare($conn, "
        UPDATE taxpayers
        SET
            taxpayer_name = ?,
            phone_number = ?,
            email = ?,
            trade_name = ?,
            address = ?
        WHERE taxpayer_id = ?
    ");

    mysqli_stmt_bind_param(
        $update,
        "sssssi",
        $request["taxpayer_name"],
        $request["phone_number"],
        $request["email"],
        $request["trade_name"],
        $request["address"],
        $request["taxpayer_id"]
    );

    mysqli_stmt_execute($update);

    $done = mysqli_prepare($conn, "
        UPDATE edit_requests
        SET status = 'approved'
        WHERE request_id = ?
    ");

    mysqli_stmt_bind_param($done, "i", $id);
    mysqli_stmt_execute($done);

    mysqli_commit($conn);

    header("Location: admin_dashboard.php");
    exit;

} catch (Exception $e) {
    mysqli_rollback($conn);
    die("حدث خطأ أثناء الموافقة");
}
?>