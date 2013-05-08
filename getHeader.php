<?php

//function to get the header info
function getHeader($address) {
		
	//clean out http://	
	$address = str_replace('http://http://', 'http://', $address);
	$address = str_replace('http://https://', 'https://', $address);

	$header = get_headers($address, 1);
	return $header;
}

//echo url if set as a parm and not empty
if (isset($_GET["u"]) && $_GET["u"] != '') {
	$header = getHeader('http://'.$_GET["u"]);
	echo '<p>'.$_GET["u"].'</p>';
	echo '<pre>';
	print_r($header);
	echo '</pre>';
} else {
	echo '<p>You need to specify a url by using u= as a url param</p>';
}

?>