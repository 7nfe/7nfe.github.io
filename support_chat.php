<?php
session_start();
include "config.php";

if (!isset($_SESSION["taxpayer_id"])) {
    header("Location: login.php");
    exit;
}

$taxpayer_id = $_SESSION["taxpayer_id"];
$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $msg = trim($_POST["message"] ?? "");

    if ($msg != "") {

        $getEmail = mysqli_prepare($conn, "
            SELECT email
            FROM taxpayers
            WHERE taxpayer_id = ?
            LIMIT 1
        ");

        mysqli_stmt_bind_param($getEmail, "i", $taxpayer_id);
        mysqli_stmt_execute($getEmail);

        $emailResult = mysqli_stmt_get_result($getEmail);
        $emailRow = mysqli_fetch_assoc($emailResult);

        $email = $emailRow["email"] ?? "";

        $query = "
            INSERT INTO support_messages
            (
                taxpayer_id,
                email,
                message
            )
            VALUES
            (
                ?, ?, ?
            )
        ";

        $stmt = mysqli_prepare($conn, $query);

        mysqli_stmt_bind_param(
            $stmt,
            "iss",
            $taxpayer_id,
            $email,
            $msg
        );

        mysqli_stmt_execute($stmt);

        $message = " تم إرسال الرسالة للأدمن سوف يتم الرد عليك من خلال البيريد الإلكتروني الخاص بك ";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<title>التواصل مع الأدمن</title>

<style>

body{
    font-family:Segoe UI;
    background:#f4f4f4;
}

.box{
    width:500px;
    margin:100px auto;
    background:white;
    padding:30px;
    border:1px solid #ccc;
}

h2{
    text-align:center;
    color:royalblue;
}

textarea{
    width:100%;
    height:180px;
    resize:none;
    padding:10px;
    font-size:16px;
}

button{
    width:100%;
    height:45px;
    background:royalblue;
    color:white;
    border:none;
    margin-top:15px;
    font-size:18px;
    font-weight:bold;
    cursor:pointer;
}

.msg{
    text-align:center;
    color:green;
    font-weight:bold;
    margin-top:15px;
}

.back{
    display:block;
    text-align:center;
    margin-top:20px;
}
</style>
</head>

<body>

<div class="box">

<h2>التواصل مع الأدمن</h2>

<form method="POST">

<textarea
name="message"
placeholder="اكتب رسالتك هنا..."
required></textarea>

<button type="submit">
إرسال
</button>

</form>

<?php if($message != ""): ?>

<div class="msg">
<?= $message ?>
</div>

<?php endif; ?>

<a href="mains.php" class="back">
الرجوع للرئيسية
</a>

</div>

</body>
</html>