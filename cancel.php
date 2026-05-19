<?php
session_start();
include "config.php";

if (!isset($_SESSION["taxpayer_id"])) {
    header("Location: login.php");
    exit;
}

$taxpayer_id = $_SESSION["taxpayer_id"];
$message = "";

$stmt = mysqli_prepare($conn, "
    SELECT taxpayer_number, taxpayer_name
    FROM taxpayers
    WHERE taxpayer_id = ?
    LIMIT 1
");
mysqli_stmt_bind_param($stmt, "i", $taxpayer_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$taxpayer = mysqli_fetch_assoc($result)) {
    die("لم يتم العثور على بيانات المكلف");
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["send"])) {

    $reason = trim($_POST["cancel_reason"] ?? "");

    if ($reason === "") {
        $message = "الرجاء إدخال سبب إلغاء التسجيل";
    } else {

        mysqli_begin_transaction($conn);

        try {
            $update = mysqli_prepare($conn, "
                UPDATE taxpayers
                SET is_registered = 0
                WHERE taxpayer_id = ?
            ");
            mysqli_stmt_bind_param($update, "i", $taxpayer_id);
            mysqli_stmt_execute($update);

            $insert = mysqli_prepare($conn, "
                INSERT INTO registration_cancel
                (taxpayer_id, reason, cancel_date)
                VALUES (?, ?, CURDATE())
            ");
            mysqli_stmt_bind_param($insert, "is", $taxpayer_id, $reason);
            mysqli_stmt_execute($insert);

            mysqli_commit($conn);

            echo "
            <script>
                alert('تم إلغاء تسجيل المكلف بنجاح');
                window.location.href = 'login.php';
            </script>";
            exit;

        } catch (Exception $e) {
            mysqli_rollback($conn);
            $message = "حدث خطأ أثناء إلغاء التسجيل: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<title>إلغاء تسجيل مكلف</title>

<style>
body {
    margin: 0;
    background: #f4f4f4;
    font-family: "Segoe UI", Tahoma, sans-serif;
}

.container {
    width: 1180px;
    max-width: 96%;
    margin: auto;
    background: white;
    min-height: 100vh;
    border: 1px solid #ccc;
}

.header {
    height: 82px;
    border-bottom: 5px solid royalblue;
    overflow: hidden;
}

.header img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.title {
    text-align: center;
    font-size: 34px;
    font-weight: bold;
    margin: 35px 0 50px;
}

.form-box {
    width: 520px;
    margin: auto;
}

.top-row {
    display: flex;
    gap: 20px;
}

.group {
    margin-bottom: 25px;
    flex: 1;
}

label {
    display: block;
    font-size: 18px;
    font-weight: bold;
    margin-bottom: 8px;
}

input, textarea {
    width: 100%;
    border: 1px solid #999;
    font-size: 16px;
    padding: 8px;
    box-sizing: border-box;
}

input {
    height: 38px;
}

textarea {
    height: 120px;
    resize: none;
    font-family: "Segoe UI", Tahoma, sans-serif;
}

.readonly {
    background: #f3f3f3;
}

.buttons {
    text-align: center;
    margin-top: 35px;
}

.btn {
    width: 130px;
    height: 45px;
    background: royalblue;
    color: white;
    border: none;
    font-size: 18px;
    font-weight: bold;
    cursor: pointer;
    margin: 0 8px;
}

.message {
    text-align: center;
    color: red;
    font-weight: bold;
    margin-bottom: 20px;
}
</style>
</head>

<body>

<div class="container">

<div class="header">
    <img src="photo.jpeg">
</div>

<div class="title">إلغاء تسجيل مكلف</div>

<?php if ($message !== ""): ?>
<div class="message"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<form method="POST">

<div class="form-box">

<div class="top-row">
    <div class="group">
        <label>رقم المكلف</label>
        <input type="text" value="<?= htmlspecialchars($taxpayer["taxpayer_number"]) ?>" readonly class="readonly">
    </div>

    <div class="group">
        <label>اسم المكلف</label>
        <input type="text" value="<?= htmlspecialchars($taxpayer["taxpayer_name"]) ?>" readonly class="readonly">
    </div>
</div>

<div class="group">
    <label>سبب إلغاء التسجيل</label>
    <textarea name="cancel_reason" required></textarea>
</div>

<div class="buttons">
    <button type="submit" name="send" class="btn">إرسال</button>
    <button type="reset" class="btn">إلغاء</button>
    <button type="button" onclick="window.location.href='mains.php'" class="btn">رجوع</button>
</div>

</div>

</form>

</div>

</body>
</html>