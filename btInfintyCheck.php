<?php

/*

This script uses the BT openreach where and when website (http://www.superfast-openreach.co.uk/where-and-when/) 
ajax call response to determine if you are in a BT Infinity superfast broadband enabled area and then sends an 
e-mail an alert with the response. It is designed to be run as cron job so that you don't have to check the BT
openreach website every day.

*/

//set the telephone number you want to check and the e-mail address(es) these should be the only settings you need to change
$tel = '01234567890';
$emailAddresses = array('you@domain.com');

//set timezone in case your server isn't hosted in the UK
date_default_timezone_set('Europe/London');

//set POST variables
$postFields = array(
	'input' => $tel,
	'address' => ''
);

//url-ify the data for the POST
foreach($postFields as $key => $value) {

	$fields_string .= $key.'='.$value.'&';

}
rtrim($fields_string, '&');

try {

	//create curl resource 
	$ch = curl_init(); 
	
	if (FALSE === $ch) {
    
	    throw new Exception('cURL failed to initialize');
		
	}

	//set url 
	curl_setopt($ch, CURLOPT_URL, "https://api.superfastmaps.co.uk/openreach/1.0/ajax/check.ajax.php"); 
		
	//set the post fields as a curl option
	curl_setopt($ch, CURLOPT_POST, 1);
	
	//set the post params
	curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
	
	//return the transfer as a string 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	
	//set the referer
	curl_setopt($ch, CURLOPT_REFERER, 'http://api.superfastmaps.co.uk/openreach/1.0/');
	
	//turn ssl verification off
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	
	//set the user agent
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/43.0.2357.124 Safari/537.36');
	
	//$output contains the output string 
	$output = curl_exec($ch); 

	if (FALSE === $output) {
	
			throw new Exception(curl_error($ch), curl_errno($ch));
	
	}

} catch(Exception $e) {

    trigger_error(sprintf(
        'Curl failed with error #%d: %s',
        $e->getCode(), $e->getMessage()),
        E_USER_ERROR);

}

//close curl resource to free up system resources 
curl_close($ch);  

$output = json_decode($output);

//echo output
echo '<pre>';
print_r($output);
echo '</pre>';

//set status based on the icon used on the openreach website map
switch ($output->body->icon) {
	case 'ao':
        $status = 'accepting orders';
        break;
	case 'ea':
        $status = 'enabled area';
        break;
    case 'hd':
        $status = 'high demand';
        break;
    case 'ur':
        $status = 'under review';
        break;
	case '?':
        $status = 'can\'t tell';
        break;
	case 'cs':
        $status = 'coming soon';
        break;
	case 'pa':
        $status = 'planned area';
        break;
	case 'es':
        $status = 'exploring solutions';
        break;
}

//send e-mail
$headers = "From: " . strip_tags($_POST['req-email']) . "\r\n";
$headers .= "Reply-To: ". strip_tags($_POST['req-email']) . "\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

$subject = 'BT Infinity check for telephone number '.$tel.', your current status is: '.$status.' as of '.date('d/m/Y').' at '.date('H:i');

$message = '<html><body>';

//add an extra message to the e-mail body if you are in a BT Infinity enabled area
if ($output->body->icon == 'ao') {

	$message .= '<h1>You can get BT Infinity superfast broadband in your area!</h1>';
	$message .= '<p>Upgrade you broadband here: <a href="http://www.productsandservices.bt.com/products/manage/">http://www.productsandservices.bt.com/products/manage/</a></p>';

}

$message .= '<h2>Your current order status is:</h2>';
$message .= '<p>'.$status.'</p>';
$message .= '<h2>Which means:</h2>';
$message .= '<p>'.$output->body->icon_content.'</p>';
$message .= '<h2>Your exchange is '.$output->exchange->name.' and its status is:</h2>';
$message .= '<p>'.$output->exchange->status.'</p>';
$message .= '</body></html>';

foreach ($emailAddresses as $emailAddress) {
	
	mail($emailAddress, $subject, $message, $headers);
	
}

