<pre><?php
/*$ch = curl_init("https://www.sima-land.ru/api/v5/signin");
$data = array(
			"email" => "sale@sportshop24.ru",
			"password" => "W*y8QNCfCEdyQa9",
			"regulation" => true
		);
$data_string = json_encode($data, JSON_UNESCAPED_UNICODE);
$options = array(
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_CUSTOMREQUEST => "POST",
	CURLOPT_SSL_VERIFYPEER => false,
	CURLOPT_POSTFIELDS => $data_string,
	CURLOPT_HEADER => true,
	CURLOPT_HTTPHEADER, array(
	   'Content-Type: application/json',
	   'Content-Length: ' . strlen($data_string)
	)
);
curl_setopt_array($ch, $options);
unset($options);

$rez = curl_exec($ch);
print_r(curl_getinfo($ch, CURLINFO_HEADER_OUT));
echo $rez;
curl_close($ch);

print_r($ar_pass = explode("\n",$rez));

$token = trim(str_replace("authorization: Bearer ", "", $ar_pass[1]));
$f = fopen("token.txt", "w");
fwrite($f, $token);
fclose($f);
echo "TOKEN ".$token."\n";

die("STOP");*/
/*$token = file_get_contents("token.txt");
echo "TOKEN ".$token."\n";

$ch_items = curl_init("https://www.sima-land.ru/api/v5/item");
$options = array(
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_CUSTOMREQUEST => "GET",
	CURLOPT_SSL_VERIFYPEER => false,
	CURLOPT_SSL_VERIFYHOST => false,
	CURLINFO_HEADER_OUT => true,
	//CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
	//CURLOPT_USERPWD => "sale@sportshop24.ru:W*y8QNCfCEdyQa9",
	CURLOPT_HTTPHEADER, array(
	   'Accept: application/vnd.simaland.item+json', 
	   'Authorization: Bearer '.$token
	   //'Authorization: '
	)
);
curl_setopt_array($ch_items, $options);
$rez = curl_exec($ch_items);
echo $rez;*/
$curl = curl_init('https://www.sima-land.ru/api/v3/item/?category_id=16&has_balance=1&per-page=20');
curl_setopt($curl, CURLOPT_HTTPHEADER, array('Accept: application/json'));
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
//curl_setopt($curl, CURLINFO_HEADER_OUT, true);
//curl_setopt($curl, CURLOPT_HEADER, true);

$json = curl_exec($curl); // сохранен xml

print_r(curl_getinfo($curl));
print_r(json_decode($json));
curl_close($curl);