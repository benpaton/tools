<?php
	//get the vars from post
    $to = $_POST["to"];
	$from = $_POST["from"];
	$subject = $_POST["subject"];
	$message = $_POST["message"];
	$send = $_POST["send"];
    //send the message
	if ($send == 'send' && $message != 'message') {
    	mail($to, $subject, $message, "FROM: {$from}");
	}	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Anonymous E-mailer</title>
<style>
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
<form action="" method="post">
	<ul>
        <li><input type="text" name="to" id="to" value="to" onclick="if (this.value=='to') { this.value='' }" onblur="if (this.value=='') { this.value='to' }" /></li>
        <li><input type="text" name="from" id="from" value="from" onclick="if (this.value=='from') { this.value='' }" onblur="if (this.value=='') { this.value='from' }" /></li>
        <li><input type="text" name="subject" id="subject" value="subject" onclick="if (this.value=='subject') { this.value='' }" onblur="if (this.value=='') { this.value='subject' }" /></li>
        <li><textarea name="message" id="message" onclick="if (this.value=='message') { this.value='' }" onblur="if (this.value=='') { this.value='message' }">message</textarea></li>
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
