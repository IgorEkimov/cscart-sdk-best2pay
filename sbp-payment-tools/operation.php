<?php

$action = !empty($_POST['action']) ? $_POST['action'] : null;

if($action === 'get_xml') {

	$args = [
		'sector' => FILTER_VALIDATE_INT,
		'id' => FILTER_VALIDATE_INT,
		'operation' => FILTER_VALIDATE_INT,
	];
	$data = filter_input_array(INPUT_POST, $args);
	$signature = $_POST['signature'];
	if(!is_base64($signature)) die("Signature is not base64");
	$data['signature'] = $signature;
	$host = filter_input(INPUT_POST, 'host', FILTER_VALIDATE_URL);
	$url = $host . "/webapi/Operation?" . http_build_query($data);

	echo send_request($url, $data);

} elseif($action === 'send_xml') {

	$url = filter_input(INPUT_POST, 'url', FILTER_VALIDATE_URL);
	if(!$url) die("Invalid URL!");

	$xml = !empty($_POST['xml']) ? $_POST['xml'] : null;
	error_log($xml . "\n\n\n");
	if(!$xml) die ('XML is empty!');
	$valid_xml = simplexml_load_string($xml);
	if(!$valid_xml) die ('Invalid XML!');
	$valid_xml = json_decode(json_encode($valid_xml), true);
	if(!$valid_xml) die ('Invalid XML!(2)');

	echo send_request($url, $xml, 'POST', true);
}

exit();




error_log(var_export($data, true));

$response = send_request($url, $data);
/*
$response = '<?xml version="1.0" encoding="UTF-8"?/////><operation>
<order_id>4279902</order_id>
<order_state>COMPLETED</order_state>
<reference>97</reference>
<id>1538437</id>
<date>2023.03.10 10:55:20</date>
<type>COMPLETE</type>
<state>APPROVED</state>
<reason_code>1</reason_code>
<message>Successful financial transaction</message>
<pan>220138******0013</pan>
<amount>84225</amount>
<fee>0</fee>
<currency>643</currency>
<approval_code>129719</approval_code>
<buyIdSumAmount>336900</buyIdSumAmount>
<signature>ODc4MWI4NTY0YzBlOTdiMjJhZjUxNDU0NzEyMjQwYzI=</signature>
</operation>';
*/

//error_log("\n\nloggg\n\n");
error_log($response);

echo $response;
exit();

function is_base64($s){
    // Check if there are valid base64 characters
    if (!preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $s)) return false;
    // Decode the string in strict mode and check the results
    $decoded = base64_decode($s, true);
    if(false === $decoded) return false;
    // Encode the string again
    if(base64_encode($decoded) != $s) return false;
    return true;
}

function send_request($url, $data = '', $method = 'POST', $xdebug = false){
	if($xdebug) $url .= '&XDEBUG_SESSION_START=11111';
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	
	if($method === 'POST') {
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
	}
	$response = curl_exec($curl);
	$curl_info = curl_getinfo($curl);
	$http_code = (isset($curl_info['http_code']) && is_numeric($curl_info['http_code'])) ? (int) $curl_info['http_code'] : null;
	curl_close($curl);
	if($http_code !== 200) return "ERROR CODE : " . $http_code;
	return $response;
}