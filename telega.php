<?php

// сюда нужно вписать токен вашего бота
define('TELEGRAM_TOKEN', '1202939733:AAG1EOk9SoO1Ldktm_olue5ZZcjM1pVZY-s');

// сюда нужно вписать ваш внутренний айдишник
define('TELEGRAM_CHATID', '140492010');

message_to_telegram('Привет!');

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
?>