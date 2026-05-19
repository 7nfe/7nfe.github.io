<?php
session_start();
include "config.php";

if (!isset($_SESSION["admin"])) {
    header("Location: login.php");
    exit;
}

$id = intval($_GET["id"] ?? 0);
$action = $_GET["action"] ?? "";

if ($action === "approve") {

    $status = 1;

}
elseif ($action === "reject") {

    $status = 3;

}
else {

    die("إجراء غير صحيح");
}

$stmt = mysqli_prepare($conn, "
UPDATE taxpayers
SET is_registered = ?
WHERE taxpayer_id = ?
");

mysqli_stmt_bind_param(
    $stmt,
    "ii",
    $status,
    $id
);

mysqli_stmt_execute($stmt);

header("Location: admin_dashboard.php");
exit;
?>