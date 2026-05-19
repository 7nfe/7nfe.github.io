<?php
session_start();
include "config.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    if ($username == "" || $password == "") {

        $message = "الرجاء إدخال البيانات";
    } else {

        /* فحص الأدمن */

        $adminQuery = "
        SELECT *
        FROM admins
        WHERE username = ?
        AND password = ?
        LIMIT 1
        ";

        $adminStmt =
            mysqli_prepare($conn, $adminQuery);

        mysqli_stmt_bind_param(
            $adminStmt,
            "ss",
            $username,
            $password
        );

        mysqli_stmt_execute($adminStmt);

        $adminResult =
            mysqli_stmt_get_result($adminStmt);

        if (mysqli_num_rows($adminResult) > 0) {

            $_SESSION["admin"] = $username;

            header("Location: admin_dashboard.php");
            exit;
        }

        /* فحص المكلف */

        $query = "
        SELECT
            taxpayer_id,
            taxpayer_name,
            taxpayer_number,
            password,
            is_registered
        FROM taxpayers
        WHERE taxpayer_number = ?
        LIMIT 1
        ";

        $stmt = mysqli_prepare($conn, $query);

        mysqli_stmt_bind_param(
            $stmt,
            "s",
            $username
        );

        mysqli_stmt_execute($stmt);

        $result =
            mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) == 0) {

            $message =
                "بيانات الدخول غير صحيحة";
        } else {

            $user =
                mysqli_fetch_assoc($result);

            if (
                trim($user["password"])
                != $password
            ) {

                $message =
                    "بيانات الدخول غير صحيحة";
            } else {

                $_SESSION["taxpayer_id"] =
                    $user["taxpayer_id"];

                $_SESSION["taxpayer_name"] =
                    $user["taxpayer_name"];

                $_SESSION["taxpayer_number"] =
                    $user["taxpayer_number"];

                $status = (int)$user["is_registered"];

if ($status === 1) {

    header("Location: mains.php");
    exit;

} elseif ($status === 2) {

    $message = "طلب التسجيل قيد مراجعة الأدمن";

} elseif ($status === 3) {

    $message = "تم رفض طلب التسجيل";

} else {

    header("Location: eligibility.php");
    exit;
}
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>

    <meta charset="UTF-8">

    <title>
        تسجيل الدخول
    </title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: "Segoe UI", Tahoma, sans-serif;
            background-color: rgb(245, 247, 250);
        }

        .header {
            width: 100%;
            height: 82px;
            background: white;
            overflow: hidden;
            border-bottom: 1px solid #ddd;
        }

        .header img {
            width: 100%;
            height: 82px;
            object-fit: cover;
        }

        .main {
            min-height: calc(100vh - 82px);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card {
            width: 460px;
            min-height: 460px;
            background: white;
            border: 1px solid #999;
            padding: 35px 50px;
        }

        .title {
            text-align: center;
            color: royalblue;
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 35px;
        }

        label {
            display: block;
            font-size: 17px;
            font-weight: bold;
            color: black;
            margin-bottom: 8px;
            text-align: right;
        }

        .input-box {
            width: 100%;
            border: none;
            border-bottom: 2px solid silver;
            outline: none;
            font-size: 18px;
            padding: 8px 0;
            margin-bottom: 25px;
            text-align: right;
            font-family: "Segoe UI", Tahoma, sans-serif;
        }

        .input-box:focus {
            border-bottom-color: royalblue;
        }

        .show-password {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: -5px;
            margin-bottom: 20px;
            font-size: 16px;
        }

        .show-password label {
            margin: 0;
            font-weight: normal;
            cursor: pointer;
        }

        .btn-login {
            width: 100%;
            height: 48px;
            background: royalblue;
            color: white;
            border: none;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            font-family: "Segoe UI", Tahoma, sans-serif;
        }

        .btn-login:hover {
            background: #244fc7;
        }

        .message {
            margin-top: 15px;
            text-align: center;
            color: red;
            font-weight: bold;
        }

        @media (max-width:600px) {

            .card {
                width: 90%;
                padding: 30px 25px;
            }

            .title {
                font-size: 28px;
            }
        }
    </style>

</head>

<body>

    <div class="header">
        <img src="photo.jpeg" alt="الشعار">
    </div>

    <div class="main">

        <form class="card" method="POST">

            <div class="title">
                تسجيل الدخول
            </div>

            <label for="username">
                اسم المستخدم<br>
                (الرقم الضريبي)
            </label>

            <input
                type="text"
                id="username"
                name="username"
                class="input-box"
                required>

            <label for="password">
                كلمة المرور
            </label>

            <input
                type="password"
                id="password"
                name="password"
                class="input-box"
                required>

            <div class="show-password">

                <input
                    type="checkbox"
                    id="showPassword">

                <label for="showPassword">
                    إظهار كلمة المرور
                </label>

            </div>

            <button
                type="submit"
                class="btn-login">
                تسجيل الدخول
            </button>

            <?php if (!empty($message)): ?>

                <div class="message">
                    <?= htmlspecialchars($message) ?>
                </div>

            <?php endif; ?>

        </form>

    </div>

    <script>
        document
            .getElementById("showPassword")
            .addEventListener("change", function() {

                const passwordInput =
                    document.getElementById("password");

                passwordInput.type =
                    this.checked ? "text" : "password";
            });
    </script>

</body>

</html>