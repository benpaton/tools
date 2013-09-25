<?php
//set maximum execution time for the script
set_time_limit(30);

//set default number of loops to run
$numberOfLoops  = 5;

//get number of loops to run from the url if it's set
if (isset($_GET["l"])) {
	$numberOfLoops = $_GET["l"];
	
	$scriptTimeLimit = 30 + $numberOfLoops;
	set_time_limit($scriptTimeLimit);
}

//set default number of random IPs to generate
$numberOfIPsToGenerate = 5;

//get number of random IPs to generate from the url if it's set
if (isset($_GET["ips"])) {
	$numberOfIPsToGenerate = $_GET["ips"];
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
function checkHTTPStatusCode($ips) {

	//count the number of ips to setup multi curl for
	$numberOfCurlsToDo = count($ips);
	
	//setup the user agent string for curl
	$agent = "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)";
	
	//create the multiple cURL handle
	$mh = curl_multi_init();
	
	//loop over the ips and setup multi curl
	$curlLoop = 0;
	while ($curlLoop < $numberOfCurlsToDo) {
		
		//create curl resources
		$ch[$curlLoop] = curl_init();
		
		//set curl opptions
		curl_setopt ($ch[$curlLoop], CURLOPT_URL,$ips[$curlLoop]);
		curl_setopt ($ch[$curlLoop], CURLOPT_USERAGENT, $agent);
		curl_setopt ($ch[$curlLoop], CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ($ch[$curlLoop], CURLOPT_HEADER, 1);
		curl_setopt ($ch[$curlLoop], CURLOPT_TIMEOUT_MS, 500);
		
		//add the two handles
		curl_multi_add_handle($mh,$ch[$curlLoop]);
	
		$curlLoop++;
	}
	
	//execute the handles
	$running = null;
	do {
		curl_multi_exec($mh, $running);
	} while($running > 0);
	
	
	//get http status codes
	$curlHTTPStatusLoop = 0;
	while ($curlHTTPStatusLoop < $numberOfCurlsToDo) {
		$httpcodes[] = curl_getinfo($ch[$curlHTTPStatusLoop], CURLINFO_HTTP_CODE);
		
		$curlHTTPStatusLoop++;
	}
		
	//close the handles
	$curlCloseHandlesLoop = 0;
	while ($curlCloseHandlesLoop < $numberOfCurlsToDo) {
		curl_multi_remove_handle($mh, $ch[$curlCloseHandlesLoop]);
		
		$curlCloseHandlesLoop++;
	}
	curl_multi_close($mh);	

	return $httpcodes;
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

	//generate array of random IP's for the number of IPs specified
	$numberOfIPsGenerated = 0;
	while ($numberOfIPsGenerated < $numberOfIPsToGenerate) {
		
		$ips[] = generateIP();
		
		$numberOfIPsGenerated++;
	}
		
	//get the status codes from curl
	$statusCode = checkHTTPStatusCode($ips);
	
	//print_r($ips);
	//print_r($statusCode);
		
	echo '<ol>';
	
	//echo out the links
	$i2 = 0;
	while ($i2 < $numberOfIPsToGenerate) {
		if ($statusCode[$i2] != '0') {
			
			//get header info the live IP's
			$headerInfo = implode_with_key(headerInfo($ips[$i2]));
			$headerInfo = str_replace('0 =', '', $headerInfo);
						
			echo '<li class="green"><a href="http://'.$ips[$i2].'" target="_blank">'.$ips[$i2 ].'</a> '.$headerInfo.'</li>';
		} else {
			echo '<li>'.$ips[$i2].'</li>';
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