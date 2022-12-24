<?php

// 設定請求的 URL
$url = 'https://38f6-223-136-254-194.jp.ngrok.io/linebot/linebotpush.php';

// 送出 GET 請求並取得回應
$response = file_get_contents($url);

// 顯示回應
echo $response;

?>