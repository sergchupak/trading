<?php
/*$client_id = "630133209517-rggalep79adp5fe3a19t8me4c5srg60o.apps.googleusercontent.com";
$client_secret = "OyA1YgmXbM3ltSBnzngCMz3I";*/

require __DIR__ . '/vendor/autoload.php';

if (php_sapi_name() != 'cli') {
    throw new Exception('This application must be run on the command line.');
}


function getClient()
{
    $client = new Google_Client();
    $client->setApplicationName('Google Sheets API PHP Quickstart');
    $client->setScopes(Google_Service_Sheets::SPREADSHEETS_READONLY);
    $client->setAuthConfig('credentials.json');
    $client->setAccessType('offline');
    $client->setPrompt('select_account consent');


    $tokenPath = 'token.json';
    if (file_exists($tokenPath)) {
        $accessToken = json_decode(file_get_contents($tokenPath), true);
        $client->setAccessToken($accessToken);
    }

    // If there is no previous token or it's expired.
    if ($client->isAccessTokenExpired()) {
        // Refresh the token if possible, else fetch a new one.
        if ($client->getRefreshToken()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
        } else {
            // Request authorization from the user.
            $authUrl = $client->createAuthUrl();
            printf("Open the following link in your browser:\n%s\n", $authUrl);
            print 'Enter verification code: ';
            $authCode = trim(fgets(STDIN));

            // Exchange authorization code for an access token.
            $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
            $client->setAccessToken($accessToken);

            // Check to see if there was an error.
            if (array_key_exists('error', $accessToken)) {
                throw new Exception(join(', ', $accessToken));
            }
        }
        // Save the token to a file.
        if (!file_exists(dirname($tokenPath))) {
            mkdir(dirname($tokenPath), 0700, true);
        }
        file_put_contents($tokenPath, json_encode($client->getAccessToken()));
    }
    return $client;
}



$client = getClient();
$service = new Google_Service_Sheets($client);

$spreadsheetId = '1v5sZ9w4XVnZCSa-CmADatOIn-FHyHMu1SNeD_DtvHFc';
$range = 'cvx_xom_min!A1470:C11231';
$response = $service->spreadsheets_values->get($spreadsheetId, $range);
$values = $response->getValues();
//print_r($values);
$log = array();



$super_global = 0;
$super_global_amount = 0;
$total_arr = array();
for($i=300;$i<=350;$i+=10)
for($k=-1000; $k>=-1800; $k-=50){
for($loss_limit=$k;$loss_limit>=$k-100;$loss_limit-=10){
for($deal_profit_limit=$i;$deal_profit_limit<$i+50;$deal_profit_limit +=10){

$long_id_mark = 1;
$short_id_mark = 2;
$profit = 0;
$comissia = 0;
$long_id_mark = 1;
$short_id_mark = 2;
$deal_count = 0;
$loss_count = 0;
$pos = array(
		'long' => array(
			'name'=>'cvx', 
			'price_in' => floatval($values[0][1]), 
			'current_price' => 0, 
			'qty' => 370, 
			'res' => 0, 
			'current_amount'=>0, 
			'start_amount'=>0
		),
		'short' => array(
			'name'=>'xom', 
			'price_in' => floatval($values[0][2]), 
			'current_price' => 0, 
			'qty' => 700, 
			'res' => 0,
			'current_amount'=>0,
			'start_amount'=>0 
			)
	);
$bank_array = array();
foreach($values as $value) {

	//echo "\nCurrent_date:".$value[0].", Current profit: ".$profit." Deal_count: ".$deal_count." Commition: ".$comissia."\n";
	//определяем результат по лонгу
	$pos['long']['current_price'] = floatval(str_replace (',', '.', $value[$long_id_mark]));
	$pos['long']['current_amount'] = $pos['long']['current_price']*$pos['long']['qty'];
	$pos['long']['start_amount'] = $pos['long']['price_in']*$pos['long']['qty'];
	
	$pos['long']['res'] = $pos['long']['current_amount']-$pos['long']['start_amount'];
	//определяем результат по шорту
	$pos['short']['current_price'] = floatval(str_replace (',', '.', $value[$short_id_mark]));
	$pos['short']['current_amount'] = $pos['short']['current_price']*$pos['short']['qty'];
	$pos['short']['start_amount'] = $pos['short']['price_in']*$pos['short']['qty'];
	
	$pos['short']['res'] = $pos['short']['start_amount']-$pos['short']['current_amount'];

	//$log[]=$pos;
	$deal_result = $pos['short']['res'] + $pos['long']['res'];
	
	$deal_result = $pos['long']['res'] + $pos['short']['res'];
	$deal_comission = ($pos['long']['current_amount'] + $pos['short']['current_amount'])*floatval(0.0005)*2;

	//echo "Deal_result:".$deal_result."\n";
	
	//проверяем лимит по прибыли
	
	if($deal_result>=$deal_profit_limit){
		//фиксируем прибыль
		$profit += $deal_result;
		$profit -= $deal_comission;
		$bank_array[] = $profit;
		$comissia += $deal_comission;
		$deal_count += 1;
		$super_global_amount += $pos['long']['current_amount']*$pos['long']['current_price'] + $pos['short']['current_amount']*$pos['short']['current_price'];
		//echo "Фиксируем прибыль:".$deal_result." Банк(".$profit.") \n";
		//echo "Переставляем позиции \n";
		//переставляем лонг в шорт
		$temp_arr = $pos['long'];
		//rfdsf
		$pos['long']['name'] = $pos['short']['name'];
		$pos['long']['price_in'] = $pos['short']['current_price'];
		$pos['long']['current_price'] = 0;
		$pos['long']['qty'] = $pos['short']['qty'];
		$pos['long']['res'] = 0;
		$pos['long']['current_amount'] = 0;
		$pos['long']['start_amount'] = 0;
		//переставляем шорт в лонг
		$pos['short']['name'] = $temp_arr['name'];
		$pos['short']['price_in'] = $temp_arr['current_price'];
		$pos['short']['current_price'] = 0;
		$pos['short']['qty'] = $temp_arr['qty'];
		$pos['short']['res'] = 0;
		$pos['short']['current_amount'] = 0;
		$pos['short']['start_amount'] = 0;
		//переставляем указатель массива
		$t_makr = $long_id_mark;
		$long_id_mark = $short_id_mark;
		$short_id_mark = $t_makr;
		$deal_result = 0;

	}
	//проверяем лимит убытка
	if($deal_result<$loss_limit){
		$profit += $deal_result;

		$bank_array[] = $profit;
		$pos['long']['price_in']=$pos['long']['current_price'];
		$pos['short']['price_in']=$pos['short']['current_price'];
		$loss_count+=1;
		//$deal_result = 0;
		$deal_normal = $loss_limit;
		
		
	}
	
	//echo "\n\n";
	
}

//if($profit>12000)
	//echo $loss_limit.":".$deal_profit_limit."||Profit: ".intval($profit)."||Min bank:".intval(min($bank_array))."||Comissia:".$comissia."||Deal_count:".$deal_count."||Loss_Deal_count:".$loss_count."\n";
$super_global+=$profit;
$bank_array = array();
$profit = 0;
$comissia = 0;
$deal_count=0;
$loss_count= 0;
}



}
$percent = $super_global/$super_global_amount;
$percent = $percent*10000000;
$km = $k-50;
$ki = $i+10;
$period =  "[".$k.":".$km."][".$i.",".$ki."]";
$total_arr[] = array($percent,$period);
echo "[".$k.":".$km."][".$i.",".$ki."] Super Global profit:".$super_global."||Global_amount".$super_global_amount."||Total_percent:".$percent."\n";
$super_global = 0;
$super_global_amount = 0;
}



