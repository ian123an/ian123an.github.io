<?php 
include("link.php");

$ngrok="https://38f6-223-136-254-194.jp.ngrok.io";
//設定Token 
$ChannelSecret = '37dc3371b860d8d23a6a12e5c14bd169'; 
$ChannelAccessToken = '+/5N813XXTcoB4UZrCh8uBHsnkIcqvUbqp4MCFdvYbUET4fFQ0fltd3yC6ny/C0Esz8BqYLUIc9H4ojMxBqKezh9Qt5OCwRnG2Y0XslUyEtYOA4mhucQAKaVQS8td72UvYVy5LTIH3Jczvp0zpCsFwdB04t89/1O/w1cDnyilFU='; 
 
//讀取資訊 
$HttpRequestBody = file_get_contents('php://input'); 
$HeaderSignature = $_SERVER['HTTP_X_LINE_SIGNATURE']; 
 
//驗證來源是否是LINE官方伺服器 
$Hash = hash_hmac('sha256', $HttpRequestBody, $ChannelSecret, true); 
$HashSignature = base64_encode($Hash); 
if($HashSignature != $HeaderSignature) 
{ 
    die('hash error!'); 
} 
 
//輸出 
file_put_contents('log.txt', $HttpRequestBody);
 
//解析 
$DataBody=json_decode($HttpRequestBody, true); 

$ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.line.me/v2/bot/message/reply');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $ChannelAccessToken
    ]);

//逐一執行事件 
foreach($DataBody['events'] as $Event) 
{ 
    $getsource = $Event['source'];
    $getMessage = $Event['message'];
    $usr_id = $getsource['userId'];

    
    

    // 傳送訊息
    

    //當bot收到訊息 

    switch ($Event['type']) {
        case 'message': //訊息觸發
            switch ($getMessage['type']) {
                case 'text': //訊息為文字
                    $message=strtolower($getMessage['text']);
                    switch($message){
                        case preg_match("/hi/", $message)!=false :
                            $Payload = [ 
                                'replyToken' => $Event['replyToken'],
                                'messages' => [
                                    [
                                        'type' => 'text',
                                        'text' => '嗨，醜醜'
                                    ],
                                    [
                                        'type' => 'text',
                                        'text' => $usr_id            
                                    ]
                                ]
                            ];
                            break;

                        
                        case preg_match("/qr/", $message)!=false :
                            $Payload = [
                                'replyToken'=>$Event['replyToken'],
                                'messages' => [[
                                    'type' => 'template', //訊息類型 (模板)
                                    'altText' => '已開啟功能選單', //替代文字
                                    'template' => [
                                        'type' => 'buttons', //類型 (按鈕)
                                        'title' => '功能選單',
                                        'text' => '選擇要開啟的功能', //文字
                                        'actions' => [
                                            [
                                                'type' => 'uri', //類型 (連結)
                                                'label' => 'QR code scaner', //標籤
                                                'uri' => "$ngrok/linebot/scanner.php?id=$usr_id" //連結網址
                                            ],
                                        ]
                                    ]
                                ]]  
                            ];
                            break;
                        // case 'test' :
                        //     require_once('test.php');
                        //     break;
                        case preg_match("/幹/", $message)!=false :
                        case 'e04':
                            $Payload = [ 
                                'replyToken' => $Event['replyToken'],
                                'messages' => [
                                    [
                                        'type' => 'text',
                                        'text' => '甘林涼，罵殺小:P'
                                    ],
                                ]
                            ];
                            break;
                            case preg_match("/padoru/", $message)!=false:
                                $Payload = [ 
                                    'replyToken' => $Event['replyToken'],
                                    'messages' => [
                                        [
                                            'type' => 'text',
                                            'text' => 'https://youtu.be/dQ_d_VKrFgM'
                                        ],
                                    ]
                                ];
                                break;
                        default:
                            break;
                    }
                    break;
                
                default:
                    break;
            }
            break;
        
        case 'follow': //加為好友觸發
            $Payload = [ 
                'replyToken' => $Event['replyToken'],
                'messages' => [
                    [
                        'type' => 'text',
                        'text' => '嗨，我是藥盒小精靈'
                    ],
                ]
            ];
            break;
        case 'join': //加入群組觸發
            $Payload = [ 
                'replyToken' => $Event['replyToken'],
                'messages' => [
                    [
                        'type' => 'text',
                        'text' => '嗨，我是藥盒小精靈'
                    ],
                ]
            ];
            break;
        default:
            
            break;
    }
   
    
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($Payload));
    curl_exec($ch);
    curl_close($ch);
    
    
    

    
}
?>