<?php 

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

$publicApiClient = new PublicApiClient($apiKey, $apiSecretKey, Nt\PublicApiClient::V2);
$result = $publicApiClient->sendRequest('getPositionJson', []);
$output= "";
	//print_r(json_decode ($result));
$result = json_decode ($result);
print_r($result);
$ticker = str_replace('.US','',$result->result->ps->pos[7]->i);

while(1==1){
//$response = $publicApiClient->sendRequest('getStockQuotesJson', ['tickers' => "XOM"]);
//$response = json_decode ($response);
//  По нескольким инструментам 
$url = "https://cloud.iexapis.com/stable/tops?token=pk_6e3949820f2045368e07dfbd20724842&symbols=".$ticker;
$json = file_get_contents($url);
$response = json_decode ($json);
print_r($response);
//echo $response->result->q[0]->x_currVal."\n";
//print_r($response);
echo $ticker.":".$response[0]->lastSalePrice."\n";
sleep(1);
}
?>