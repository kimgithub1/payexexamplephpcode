<?php

include 'resources/Curl.php';
$request = new Curl();
$settingsdata = include 'resources/settings.php';

// HTTP GET call(jQuery) from onConsumerIdentifiedEvent - see templates/checkout.php in templates
if (isset($_GET["consumerProfileRef"]) == true) {
    $urls = [
    "hostUrls" => ['https://example.com', 'https://example.net'],
    "completeUrl" => "https://example.com/payment-completed",
    "cancelUrl" => "https://example.com/payment-canceled",
    "callbackUrl" => "https://api.example.com/payment-callback",
    "termsOfServiceUrl" => "https://example.com/termsandconditoons.pdf",
    "logoUrl" => "https://example.com/logo.png",
];

    $payeeInfo = [
    "payeeId" => $settingsdata['payeeId'],
    "payeeReference" => date("Ymdhis") . rand(100, 1000),
    "orderReference" => "order-100",
    "payeeName" => "Merchant1",
    "productCategory" => "A100",
    ];

    $metadata = [
    'key1' => 'value1',
    'key2' => 'value2',
    ];

    $creditCard = [
    "no3DSecure" => false,
    "no3DSecureForStoredCard" => false,
    "rejectCardNot3DSecureEnrolled" => false,
    "rejectCreditCards" => false,
    "rejectDebitCards" => false,
    "rejectConsumerCards" => false,
    "rejectCorporateCards" => false,
    "rejectAuthenticationStatusA" => false,
    "rejectAuthenticationStatusU" => false,
    "noCvc" => false,
    ];

    $invoice = [
    "feeAmount" => 1000,
    "invoiceType" => "PayExFinancingSe",
    ];

    $swish = [
    "enableEcomOnly" => false,
    ];

    $items = [ ['creditCard' => $creditCard], ['invoice' => $invoice], ['swish' => $swish] ];

    $consumerProfileRef = filter_input(INPUT_GET, 'consumerProfileRef', FILTER_SANITIZE_STRING);

    $payer = ['consumerProfileRef' => $consumerProfileRef];

    $paymentorder = [
    'operation' => 'Purchase',
    'intent' => "Authorization",
    'currency' => "SEK",
    'amount' => 25000,
    'vatAmount' => 0,
    'description' => "Test Purchase",
    'userAgent' => "Mozilla/5.0",
    'language' => "en-US",
    'generatePaymentToken' => "false",
    'disablePaymentMenu' => "false",
    'urls' => $urls,
    'payeeInfo' => $payeeInfo,
    'payer' => $payer,
    //'metadata' => $metadata,
    'items' => $items,
    ];

    $payloadPaymentmenu = [
    'paymentorder' => $paymentorder,
    ];

    $responsePaymentmenu = $request->curlRequest(
        $settingsdata['AuthorizationBearer'],
        "POST",
        $settingsdata['baseuri'] . "/psp/paymentorders",
        json_encode($payloadPaymentmenu)
);

    if ($responsePaymentmenu['statusCode'] == 201) {
        $operationsArray = $responsePaymentmenu['response']->{'operations'};
        $index = array_search('view-paymentorder', array_column($operationsArray, 'rel'));

        if ($index == true) {
            $href = $operationsArray[$index]->{'href'};
            echo '<p class="paymentmenu-token">' . $href . '</p>';
            exit;
        }
    }
}