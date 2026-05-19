<?php
session_start();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<title>المساعد الذكي</title>

<style>
*{
    box-sizing:border-box;
}

body{
    margin:0;
    font-family:"Segoe UI",Tahoma,sans-serif;
    background:#f4f6f9;
}

.chat-container{
    width:520px;
    max-width:95%;
    margin:60px auto;
    background:white;
    border:1px solid #ddd;
    border-radius:12px;
    overflow:hidden;
    box-shadow:0 8px 25px rgba(0,0,0,0.12);
}

.chat-header{
    background:royalblue;
    color:white;
    padding:18px;
    text-align:center;
    font-size:24px;
    font-weight:bold;
}

.messages{
    height:390px;
    overflow-y:auto;
    padding:18px;
    background:#fafafa;
}

.msg{
    max-width:80%;
    padding:12px 15px;
    margin-bottom:12px;
    border-radius:12px;
    font-size:16px;
    line-height:1.6;
    clear:both;
}

.user{
    background:#dbe8ff;
    float:right;
    border-bottom-right-radius:2px;
}

.bot{
    background:#eeeeee;
    float:left;
    border-bottom-left-radius:2px;
}

.input-area{
    display:flex;
    gap:10px;
    padding:15px;
    border-top:1px solid #ddd;
    background:white;
}

input{
    flex:1;
    height:44px;
    border:1px solid #ccc;
    border-radius:8px;
    padding:0 12px;
    font-size:16px;
    outline:none;
}

input:focus{
    border-color:royalblue;
}

button{
    width:95px;
    height:44px;
    background:royalblue;
    color:white;
    border:none;
    border-radius:8px;
    font-size:16px;
    font-weight:bold;
    cursor:pointer;
}

button:hover{
    background:#244fc7;
}

.back{
    display:block;
    text-align:center;
    margin:15px auto;
    color:royalblue;
    text-decoration:none;
    font-weight:bold;
}
</style>
</head>

<body>

<div class="chat-container">

<div class="chat-header">
المساعد الذكي
</div>

<div id="messages" class="messages">
    <div class="msg bot">
        مرحبًا، كيف أقدر أساعدك؟ اسألني عن الدفع، التقسيط، الإقرار، التعديل، أو طلباتي.
    </div>
</div>

<div class="input-area">
    <input type="text" id="msg" placeholder="اكتب سؤالك هنا..." onkeydown="if(event.key==='Enter') sendMsg()">
    <button onclick="sendMsg()">إرسال</button>
</div>

</div>

<a href="mains.php" class="back">الرجوع للشاشة الرئيسية</a>

<script>
function sendMsg(){
    let input = document.getElementById("msg");
    let message = input.value.trim();

    if(message === ""){
        return;
    }

    let box = document.getElementById("messages");

    box.innerHTML += `<div class="msg user">${message}</div>`;
    input.value = "";

    fetch("chatbot_api.php?message=" + encodeURIComponent(message))
    .then(response => response.text())
    .then(reply => {
        box.innerHTML += `<div class="msg bot">${reply}</div>`;
        box.scrollTop = box.scrollHeight;
    })
    .catch(() => {
        box.innerHTML += `<div class="msg bot">حدث خطأ، حاول مرة ثانية.</div>`;
    });
}
</script>

</body>
</html>