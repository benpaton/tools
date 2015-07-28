<?php

	//get the vars from post
    $to = $_POST["to"];
	$from = $_POST["from"];
	$subject = $_POST["subject"];
	$message = $_POST["message"];
	$send = $_POST["send"];
   
    //send the message
	if ($send == 'send' && $message != 'message') {
		
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$headers .= 'FROM: '.$from . "\r\n";
		
		$HTMLmessage = '
			<html>
			<head>
			  <title>'.$subject.'</title>
			</head>
			<body>'.$message.'</body>
			</html>
		';
		
    	mail($to, $subject, $HTMLmessage, $headers);
	}	
?>
<!DOCTYPE html>
<head>
<meta charset="utf-8">
<title>Anonymous E-mailer</title>
<style type="text/css">
body {
	font-family:Arial, Helvetica, sans-serif;
}
ul li {
	list-style-type:none;
	margin-bottom:20px;
}
input,
textarea {
	font-family:Arial, Helvetica, sans-serif;
	font-size:1em;
	width:250px;
}
textarea {
	height:200px;
}
</style>
</head>
<body>
<?php if ($send != 'send') { ?>
<form method="post">
	<ul>
        <li><input type="text" name="to" placeholder="to" /></li>
        <li><input type="text" name="from" placeholder="from" /></li>
        <li><input type="text" name="subject" placeholder="subject" /></li>
        <li><textarea name="message" placeholder="message"></textarea></li>
        <li><input type="hidden" name="send" id="send" value="send" /><input type="submit" name="submit" value="submit" /></li>
    </ul>
</form>
<?php } else { ?>
	<p>Message sent.</p>
    <form action="" method="post">
    	<input type="hidden" name="send" id="send" value="sendAgain" />
		<input type="submit" name="submit" value="send another message" />
    </form>
<?php } ?>
</body>
</html>
