<?php

date_default_timezone_set('Asia/Bangkok');

$GLOBALS['host'] = 'localhost';
$GLOBALS['dbname'] = 'bon_appetit_';
$GLOBALS['user'] = '';
$GLOBALS['pass'] = 'Bon';
$GLOBALS['yswitch'] = 1;

$GLOBALS['sms_api_url'] = getenv('SMS_API_URL') ?: 'https://api.laaffic.com/v3/sendSms';
$GLOBALS['sms_api_key'] = getenv('SMS_API_KEY') ?: 'WdbYUYHujKsERekDj6W1FJWwkru3ur';
$GLOBALS['sms_api_secret'] = getenv('SMS_API_SECRET') ?: 'KS5kOZeuhESwP7M9rJa74HPGnA3zaL';
$GLOBALS['sms_sender'] = getenv('SMS_SENDER') ?: '';

?>