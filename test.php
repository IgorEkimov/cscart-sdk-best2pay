<?php

require_once __DIR__ . '/app/addons/best2pay/sdk/sdk_autoload.php';

use B2P\Client;

$client = new Client(5236, 'test', true);

echo '<pre>'; print_r($client); echo '</pre>';