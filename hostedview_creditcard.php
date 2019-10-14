<?php

require_once 'resources/Curl.php';
use \resources\Curl;

$request = new Curl();
$settingsdata = require_once 'resources/settings.php';

$hostUrl = $settingsdata['hosturi'];

$urls = [
    "hostUrls" => [$hostUrl[0]],
    "completeUrl" => "https://example.com/payment-completed",
    "cancelUrl" => "https://example.com/payment-canceled",
    "callbackUrl" => "https://payexexamplephpcode.000webhostapp.com/resources/script_callback.php",
    "termsOfServiceUrl" => "https://payexexamplephpcode.000webhostapp.com/termsandconditions.pdf",
];

$payeeInfo = [
    "payeeId" => $settingsdata['payeeId'],
    "payeeReference" => date("Ymdhis") . rand(100, 1000),
    "orderReference" => "order-100",
    "payeeName" => "Merchant1",
    "productCategory" => "A100",
];

$prices = [
    "type" => "creditcard",
    "amount" => 2500,
    "vatAmount" => 0,
];

$metadata = [
    'key1' => 'value1',
    'key2' => 'value2',
];

$creditCard = [
    "rejectCreditCards" => false,
    "rejectDebitCards" => false,
    "rejectConsumerCards" => false,
    "rejectCorporateCards" => false,
];

$payment = [
    'operation' => 'Purchase',
    'intent' => "Authorization",
    'currency' => "SEK",
    'prices' => [$prices],
    'description' => "Test Purchase",
    'userAgent' => "Mozilla/5.0",
    'language' => "nb-NO",
    'generatePaymentToken' => "false",
    'language' => "en-US",
    'urls' => $urls,
    'payeeInfo' => $payeeInfo,
    //'metadata' => $metadata,
];

$payload = [
    'payment' => $payment,
    'creditCard' => $creditCard,
];

try {
    $response = $request->curlRequest(
        $settingsdata['AuthorizationBearer'],
        "POST",
        $settingsdata['baseuri'] . "/psp/creditcard/payments",
        json_encode($payload)
    );

    if ($response['statusCode'] == 201) {
        $operationsArray = $response['response']->{'operations'};
        $index = array_search('view-authorization', array_column($operationsArray, 'rel'));

        if (isset($index)) {
            $href = $operationsArray[$index]->{'href'};
            //$dataout = ["creditcardhref" => $href];
            //echo $twig->render('creditcard.html', $dataout);
            include 'templates/creditcard.php';
            exit;
        }
    }
} catch (Exception $e) {
    // Exception handling
}
