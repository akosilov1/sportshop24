<?php
$order = (int)$_POST['order'];
$summ = floatval($_POST['summ']);
if($order > 0 && $summ > 0){
    $ar_order = Sber::GetOrder($order);
    //print_r($ar_order);
    if($ar_order){
        $id_ = explode("_",$ar_order[0]);
        $id = $id_[0]."_".($id_[1]+1);
    }else{
        $id = $order."_1";
    }
    $o = new Order;
    $o->getOrder($order);
    /*echo "<pre>";
    print_r($o->order);
die("STOP");*/

    $options = [
        "orderNumber" => $id,
        "returnUrl" => "https://sportshop24.ru/pay/success.php",
        "jsonParams" => json_encode([
            'email'     => $_POST['email'],
            'user_id'   => $_POST['user_id'],
            'phone'     => $_POST['phone']
        ]),
        'orderBundle' => json_encode(
            array(
                'cartItems' => array(
                    'items' => $o->prepData()
                )
            ),
            JSON_UNESCAPED_UNICODE
        ),
        "amount" => $summ * 100
    ];
    /*print_r($options);
    die("STOP");*/
    $rez = Sber::Request("register.do", $options);

    if($rez['errorCode'] == 0){
        $f = fopen(__DIR__."/orders.txt", "a");
        fputcsv($f, [$id,$rez['orderId'],$rez['formUrl']], ';');
        fclose($f);
        header("Location: ".$rez['formUrl']);
        /*?>
        <p>Сумма: <strong><?=$summ?></strong></p>
        <a href="<?=$rez['formUrl'];?>">Оплатить</a>
        <?*/
    }elseif($rez['errorCode'] == 1){
        echo "Заказ с таким номером уже зарегистрирован в системе";
        /*$ar_order = Sber::GetOrder($order);

        if($ar_order){
            $id_ = explode("_",$ar_order[0]);
            $options['orderNumber'] = $id_[0];
            if(count($id_) > 1){
                $options['orderNumber'] .= '_'.($id_[1] + 1);
            }else{
                $options['orderNumber'] .= '_1';
            }
            print_r($options);
            $rez = Sber::Request("register.do", $options);
            print_r($rez);
            if($rez['errorCode'] == 0){
                $f = fopen(__DIR__."/orders.txt", "a");
                fputcsv($f, [$order,$rez['orderId'],$rez['formUrl']], ';');
                fclose($f);?>
                <p>Сумма: <strong><?=$summ?></strong></p>
                <a href="<?=$rez['formUrl'];?>">Оплатить</a><?
            }*/
            /*$rez_order = Sber::Request('getOrderStatus.do',['orderId' => $ar_order[1]]);
            print_r($rez_order);
            if($rez_order['OrderStatus'] == 0){ ?>
                <p>Сумма: <strong><?=$summ?></strong></p>
                <a href="<?=$ar_order[2];?>">Оплатить</a>
            <? }*/
        //}
    }else{
        $err_f = file_put_contents(__DIR__."/order_errors_log.txt", date("d.m.y H:i:s").$rez['errorMessage'].print_r($options, true), FILE_APPEND);
        echo $rez['errorMessage'];
    }
    //print_r($rez);

}
class Sber{
    Static function Request($request, $data){
        $ch = curl_init("https://securepayments.sberbank.ru/payment/rest/".$request);
        $params = [
            "userName" => "sportshop24-api",
            "password" => "9DtGm@sJgPZ)E*8"
        ];
        $params = array_merge($params, $data);
        //print_r($params);
        $options = [
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $params,
            CURLOPT_RETURNTRANSFER => 1
        ];
        curl_setopt_array($ch, $options);
        return json_decode(curl_exec($ch), true);
    }
    Static function GetOrder($id){
        $f = fopen(__DIR__.'/orders.txt', 'r');
        $rez = array();
        while($ar_str = fgetcsv($f,0,';')){
            $ord_ = explode("_",$ar_str[0]);
            if($ord_[0] == $id) $rez = $ar_str;
        }
        fclose($f);
        return $rez;
    }
}
class Order{

    Public $order;
    function getOrder($id){
        $url="https://sale%40sportshop24.ru:qcVi1535RM2ap3V9B39wWUl63l3K7Lhy@sportshop24.ru/api/2.0/orders/".$id;
        $ch = curl_init($url);
        $op = array(
            CURLOPT_HEADER  => false,
            CURLOPT_RETURNTRANSFER  => true,
            CURLINFO_HEADER_OUT => true,
            CURLOPT_HTTPHEADER  => array('Content-Type: application/json'),
        );
        curl_setopt_array($ch,$op);
        $this->order = json_decode(curl_exec($ch), true);
    }
    function prepData(){
        $cart = array();
        $i = 1;
        foreach ($this->order['products'] as $item){
            $cart[] = array(
                'positionId' => $i,
                'name' => $item['product'],
                'quantity' => array(
                    'value' => $item['amount'],
                    'measure' => 'шт'
                ),
                'itemAmount' => $item['price'] * $item['amount'] * 100,
                'itemCode' => $item['product_code'],
                'tax' => array(
                    'taxType' => 6,
                    //'taxSum' => 0
                ),
                'itemPrice' => $item['price'] * 100,
            );
            $i++;
        }
        // Доставка
        $shipping = $this->order['shipping'][0];
        $cart[] = array(
            'positionId' => $i,
            'name' => $shipping['shipping'],
            'quantity' => array(
                'value' => 1,
                'measure' => 'шт'
            ),
            'itemAmount' => $shipping['rate'] * 100,
            'itemCode' => $shipping['shipping_id'],
            'tax' => array(
                'taxType' => 0,
                'taxSum' => 0
            ),
            'itemPrice' => $shipping['rate'] * 100,
        );
        return $cart;
    }
}