<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Успешная оплата</title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans&display=swap" rel="stylesheet">
    <style>
        body{
            font: 400 12px/1.5em 'Open Sans';
            text-align: center;
        }
        ul{
            padding: 0;
        }
        ul > li{
            display: block;
            background: #4fbe31;
            color: #fff;
            display: inline-block;
            margin-bottom: 0;
            padding: 6px 14px;
            outline: 0px;
            border: 1px solid rgba(0,0,0,0);
            background-image: none;
            vertical-align: middle;
            text-align: center;
            line-height: 1.428571429;
            cursor: pointer;
            font-family: 'Open Sans';
            font-size: 16px;
            font-weight: normal;
            font-style: normal;
            text-decoration: none;
        }
        ul > li a{
            color: inherit;
            text-decoration: none;
            text-transform: uppercase;
        }
    </style>
</head>
<body>
<?php
$id = $_GET['orderId'];
if($id){
    $f = fopen(__DIR__.'/orders.txt', 'r');
    while($ar_str = fgetcsv($f,0,';')){
        if($ar_str[1] == $id){
            $o_id = explode("_",$ar_str[0]);
            break;
        }
    }
    fclose($f);
    if($o_id[0]){
        // Статус
        $data = [
            'status' => 'G',
            "notify_user" =>  "1" ,
            "notify_department" =>  "1"
        ];
        $url="https://sale%40sportshop24.ru:qcVi1535RM2ap3V9B39wWUl63l3K7Lhy@sportshop24.ru/api/2.0/orders/".$o_id[0];
        $ch = curl_init($url);
        $op = array(
            CURLOPT_HEADER  => false,
            CURLOPT_CUSTOMREQUEST => 'PUT',
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_HTTPHEADER  => array('Content-Type: application/json'),
            CURLOPT_POSTFIELDS    => json_encode($data),
        );
        curl_setopt_array($ch,$op);
        $c_rez = json_decode(curl_exec($ch), true);
    }
    ?>
    <!-- REZ <?print_r($c_rez);?> -->
    <h1>Оплата за заказ № <?=$o_id[0]?> получена</h1>
    <h2>Спасибо за заказ!</h2>
<?}?>
<ul>
    <li><a href="/">На главную</a></li>
    <li><a href="/orders.html">Мои заказы</a></li>
</ul>
</body>
</html>