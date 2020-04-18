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


/**
* @param {string} $command -  команда API 
* @param {array} $params -  параметры команды 
* @param {'json'|'array'} $type -  формат ответа json или array 
*
*


/** Примеры */


/** 1. Открытие сессии безопасности */



$i=0;
while(1==1){
	$i+=1;
	//$result = $publicApiClient->sendRequest('getAuthInfo', []);
	$result = $publicApiClient->sendRequest('getPositionJson', []);
	$output= "";
	//print_r(json_decode ($result));
	$result = json_decode ($result);
	$vhod = floatval($result->result->ps->pos[7]->bal_price_a);
	$mkt = floatval($result->result->ps->pos[7]->mkt_price);
	$amount = intval($result->result->ps->pos[7]->q);
	$res_xom = $mkt*$amount-$vhod*$amount;
	echo "[".$i."]//////////////////////////////////////////////////////////\n";

	echo "XOM Цена входа:".$vhod." - ".$mkt." = ".$res_xom."\n";
	//$output .= "XOM Цена входа:".$vhod." - ".$mkt." = ".$res_xom."<br>";
	
	$vhod = floatval($result->result->ps->pos[6]->bal_price_a);
	$mkt = floatval($result->result->ps->pos[6]->mkt_price);
	$amount = intval($result->result->ps->pos[6]->q);
	$res_cvx = $mkt*$amount-$vhod*$amount;
	$comition = ($mkt*$amount+$vhod*$amount)*2/10000*2;

	echo "CVX Цена входа:".$vhod." - ".$mkt." = ".$res_cvx."\n";
	//$output .= 	"CVX Цена входа:".$vhod." - ".$mkt." = ".$res_cvx."<br>";
	$main_result = $res_xom+$res_cvx;
	echo "Комиссия:".$comition."\n";
	echo "Разница:".$main_result."\n";
	$main_result = $main_result+$comition;
	echo "Бабло:".$main_result."\n";
	
	
	$output .= date('l jS \of F Y h:i:s A').": ".$main_result."<br>";
	echo "\n\n";
	
	$file = 'data.html';
	file_put_contents($file, $output, FILE_APPEND | LOCK_EX);
	if(intval($main_result)>50)
		message_to_telegram($output);
	
	
	sleep(10);
	
}
/*$responseExample = $publicApiClient->sendRequest('getPositionJson');
print_r(json_decode ($responseExample))
*/
?>