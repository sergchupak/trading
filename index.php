<?php
/**
* @param {string} $apiKey -  публичный ключ API 
* @param {string} $apiSecretKey -  приватный ключ API 
* @param {Nt\PublicApiClient::V1|Nt\PublicApiClient::V2} $version -  версия API 
*/
require('PublicApiClient.php');

use Nt\PublicApiClient;

$apiKey = "409d666bdc6e415de0f309902e7628b2";
$apiSecretKey = "7d831d32c93bcb35b30b378d8dbf4f5256cdb43f";
$version = Nt\PublicApiClient::V2;


// сюда нужно вписать токен вашего бота
define('TELEGRAM_TOKEN', '1202939733:AAG1EOk9SoO1Ldktm_olue5ZZcjM1pVZY-s');

// сюда нужно вписать ваш внутренний айдишник
define('TELEGRAM_CHATID', '140492010');



function message_to_telegram($text)
{
    $ch = curl_init();
    curl_setopt_array(
        $ch,
        array(
            CURLOPT_URL => 'https://api.telegram.org/bot' . TELEGRAM_TOKEN . '/sendMessage',
            CURLOPT_POST => TRUE,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_POSTFIELDS => array(
                'chat_id' => TELEGRAM_CHATID,
                'text' => $text,
            ),
        )
    );
    curl_exec($ch);
}


$publicApiClient = new PublicApiClient($apiKey, $apiSecretKey, $version);
$result = $publicApiClient->sendRequest('getPositionJson', []);
$result = json_decode ($result);


$j=0;

$m=0;
while(1==1){
if(is_array($result->result->ps->pos)){
	//$result = $publicApiClient->sendRequest('getAuthInfo', []);
	
	
	
	$ticker6 = str_replace('.US','',$result->result->ps->pos[6]->i);
	$ticker7 = str_replace('.US','',$result->result->ps->pos[7]->i);
	
	$url = "https://cloud.iexapis.com/stable/tops?token=pk_6e3949820f2045368e07dfbd20724842&symbols=".$ticker6.",".$ticker7;
	$json = file_get_contents($url);
	$json = json_decode ($json);
	
	$output= "";
	//print_r(json_decode ($result));

	$vhod = floatval($result->result->ps->pos[7]->bal_price_a);
	$result->result->ps->pos[7]->mkt_price = $json[1]->lastSalePrice;
	$mkt = floatval($result->result->ps->pos[7]->mkt_price);
	$amount = intval($result->result->ps->pos[7]->q);
	$res_xom = $mkt*$amount-$vhod*$amount;
	echo "[".$j."]//////////////////////////////////////////////////////////\n";
	echo date('l jS \of F Y h:i:s A', $json[1]->lastSaleTime)."\n";
	echo $result->result->ps->pos[7]->i." ".$vhod." - ".$mkt." = ".$res_xom."\n";
	//$output .= "XOM Цена входа:".$vhod." - ".$mkt." = ".$res_xom."<br>";
	
	$vhod = floatval($result->result->ps->pos[6]->bal_price_a);
	$result->result->ps->pos[6]->mkt_price = $json[0]->lastSalePrice;
	$mkt = floatval($result->result->ps->pos[6]->mkt_price);
	$amount = intval($result->result->ps->pos[6]->q);
	$res_cvx = $mkt*$amount-$vhod*$amount;
	$comition = ($mkt*$amount+$vhod*$amount)*5.2/10000*2;

	echo $result->result->ps->pos[6]->i." ".$vhod." - ".$mkt." = ".$res_cvx."\n";
	//$output .= 	"CVX Цена входа:".$vhod." - ".$mkt." = ".$res_cvx."<br>";
	$main_result = $res_xom+$res_cvx;
	echo "Комиссия:".$comition."\n";
	echo "Разница:".$main_result."\n";
	$main_result = $main_result-abs($comition);
	echo "Бабло:".$main_result."\n";
	
	
	$output .= date('l jS \of F Y h:i:s A').": ".$main_result."<br>";
	echo "\n\n";
	
	$file = 'data.html';
	
	//file_put_contents($file, $output, FILE_APPEND | LOCK_EX);
	
	if($m>0)
		$m--;
	
	if(intval($main_result)<-900){
		if($m==0)
			message_to_telegram("Обнуляем точку входа, убыток:".intval($main_result));
		$main_result+=900;
		$m = 10;
		
	}
	if(intval($main_result)>100)
		if($m==0)
			message_to_telegram("Фиксируем прибыль:".$main_result);
		$m = 10;
	
	sleep(0.1);
	$j++;
	if ($j==1000){
		$j=0;
		$publicApiClient = new PublicApiClient($apiKey, $apiSecretKey, $version);
		$result = $publicApiClient->sendRequest('getPositionJson', []);
		$result = json_decode ($result);
	}
}
else{
	$publicApiClient = new PublicApiClient($apiKey, $apiSecretKey, $version);
	$result = $publicApiClient->sendRequest('getPositionJson', []);
	$result = json_decode ($result);
}
		
	
}
/*$responseExample = $publicApiClient->sendRequest('getPositionJson');
print_r(json_decode ($responseExample))
*/
?>