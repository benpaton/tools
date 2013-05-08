<?php
//set maximum execution time for the script
set_time_limit(30);

//set default number of loops to run
$numberOfLoops  = 5;

//get number of loops to run from the url if it's set
if (isset($_GET["l"])) {
	$numberOfLoops = $_GET["l"];
	set_time_limit($numberOfLoops);
}

//function to get the header info
function headerInfo($ip) {
	$url = 'http://'.$ip; 
	$header = get_headers($url, 1);
	return $header;
}

//function to implode an array with the keys
function implode_with_key($assoc, $inglue = ' = ', $outglue = ', ') {
    $return = '';
    foreach ($assoc as $tk => $tv) {
        $return .= $outglue . $tk . $inglue . $tv;
    }
    return substr($return, strlen($outglue));
}

//function to check the http status code of mutiple IP's using multi curl
function checkHTTPStatusCode($ip1,$ip2,$ip3,$ip4,$ip5) {
	$agent = "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)";
	
	//create cURL resources
	$ch1 = curl_init();
	$ch2 = curl_init();
	$ch3 = curl_init();
	$ch4 = curl_init();
	$ch5 = curl_init();
	
	//set opptions
	curl_setopt ($ch1, CURLOPT_URL,$ip1);
	curl_setopt ($ch1, CURLOPT_USERAGENT, $agent);
	curl_setopt ($ch1, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt ($ch1, CURLOPT_HEADER, 1);
	//curl_setopt ($ch1, CURLOPT_VERBOSE, false);
	//curl_setopt ($ch1, CURLOPT_TIMEOUT, 1);
	curl_setopt ($ch1, CURLOPT_TIMEOUT_MS, 500);
	
	curl_setopt ($ch2, CURLOPT_URL,$ip2);
	curl_setopt ($ch2, CURLOPT_USERAGENT, $agent);
	curl_setopt ($ch2, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt ($ch2, CURLOPT_HEADER, 1);
	curl_setopt ($ch2, CURLOPT_TIMEOUT_MS, 500);
	
	curl_setopt ($ch3, CURLOPT_URL,$ip3);
	curl_setopt ($ch3, CURLOPT_USERAGENT, $agent);
	curl_setopt ($ch3, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt ($ch3, CURLOPT_HEADER, 1);
	curl_setopt ($ch3, CURLOPT_TIMEOUT_MS, 500);
	
	curl_setopt ($ch4, CURLOPT_URL,$ip4);
	curl_setopt ($ch4, CURLOPT_USERAGENT, $agent);
	curl_setopt ($ch4, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt ($ch4, CURLOPT_HEADER, 1);
	curl_setopt ($ch4, CURLOPT_TIMEOUT_MS, 500);
	
	curl_setopt ($ch5, CURLOPT_URL,$ip5);
	curl_setopt ($ch5, CURLOPT_USERAGENT, $agent);
	curl_setopt ($ch5, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt ($ch5, CURLOPT_HEADER, 1);
	curl_setopt ($ch5, CURLOPT_TIMEOUT_MS, 500);
	
	//create the multiple cURL handle
	$mh = curl_multi_init();
	
	//add the two handles
	curl_multi_add_handle($mh,$ch1);
	curl_multi_add_handle($mh,$ch2);
	curl_multi_add_handle($mh,$ch3);
	curl_multi_add_handle($mh,$ch4);
	curl_multi_add_handle($mh,$ch5);
	
	//execute the handles
	$running = null;
	do {
		curl_multi_exec($mh, $running);
	} while($running > 0);
  
	//get http status codes
	$httpcode1 = curl_getinfo($ch1, CURLINFO_HTTP_CODE);
	$httpcode2 = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
	$httpcode3 = curl_getinfo($ch3, CURLINFO_HTTP_CODE);
	$httpcode4 = curl_getinfo($ch4, CURLINFO_HTTP_CODE);
	$httpcode5 = curl_getinfo($ch5, CURLINFO_HTTP_CODE);
		
	//close the handles
	curl_multi_remove_handle($mh, $ch1);
	curl_multi_remove_handle($mh, $ch2);
	curl_multi_remove_handle($mh, $ch3);
	curl_multi_remove_handle($mh, $ch4);
	curl_multi_remove_handle($mh, $ch5);
	curl_multi_close($mh);	

	return array($httpcode1,$httpcode2,$httpcode3,$httpcode4,$httpcode5);
}

function generateIP() {
	do {
    	$q1 = mt_rand(1,223);
	} while (in_array($q1, array(10, 100, 127, 169, 172, 192, 198, 203)));
	$q2 = mt_rand(1,255);
    $q3 = mt_rand(1,255);
    $q4 = mt_rand(1,255);
    $ip = $q1.'.'.$q2.'.'.$q3.'.'.$q4;
    return $ip;
}
?>
<!DOCTYPE html>
<head>
<title>Random IP Generator</title>
<meta charset='utf-8'>
<style type="text/css">
html,
body {
	width:100%;
	height:100%;
	margin:0;
	padding:0;
	background-color:#FFFFFF;
}
.green,
.green a,
.green:hover {
	color:green;
	font-weight:bold;
}
</style>
</head>
<body>
<?php

$i = 0;
$elapsedLoopExecutionTime = 0;
while ($i < $numberOfLoops ) {

	//start microtime to reccord who long it's taking
	$startTime = microtime(true);

	//generate array of random IP's
	$ip = array(generateIP(),generateIP(),generateIP(),generateIP(),generateIP());
	
	//get the status codes from curl
	$statusCode = checkHTTPStatusCode($ip[0],$ip[1],$ip[2],$ip[3],$ip[4]);
	
	echo '<ol>';
	
	//echo out the links
	$i2 = 0;
	while ($i2 < 5) {
		if ($statusCode[$i2] != '0') {
			
			//get header info the live IP's
			$headerInfo = implode_with_key(headerInfo($ip[$i2]));
			$headerInfo = str_replace('0 =', '', $headerInfo);
						
			echo '<li class="green"><a href="http://'.$ip[$i2].'" target="_blank">'.$ip[$i2 ].'</a> '.$headerInfo.'</li>';
		} else {
			echo '<li>'.$ip[$i2].'</li>';
		}
		$i2++;
	}
	
	echo '</ol>';
	
	$loopExecutionTime = number_format(( microtime(true) - $startTime), 4);

	echo '<p>'.$loopExecutionTime.' seconds</p>';
	
	$elapsedLoopExecutionTime = $elapsedLoopExecutionTime + $loopExecutionTime;
		
	$i++;
}

echo '<p>Total '.$elapsedLoopExecutionTime.' seconds</p>';

?>
</body>
</html>