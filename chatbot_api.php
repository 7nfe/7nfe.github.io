<?php
header("Content-Type: text/plain; charset=UTF-8");

$message = $_GET["message"] ?? $_POST["message"] ?? "";
$message = trim($message);

if ($message == "") {
    echo "اكتب سؤالك أولًا";
    exit;
}

if (mb_strpos($message, "دفع") !== false) {
    echo "ادخل إلى شاشة الدفع الإلكتروني لمعرفة رقم الدفع والمبالغ المستحقة.";
} elseif (mb_strpos($message, "تقسيط") !== false) {
    echo "ادخل إلى طلب تقسيط ضريبة المبيعات واختر الفترة ثم أدخل الدفعة الأولى.";
} elseif (mb_strpos($message, "إقرار") !== false || mb_strpos($message, "اقرار") !== false) {
    echo "ادخل إلى إقرار ضريبة المبيعات من الخدمات الداخلية.";
} else {
    echo "لم أفهم سؤالك، جرّب تسأل عن الدفع أو التقسيط أو الإقرار.";
}
?>