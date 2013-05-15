<?php
//get params from the url
$do = $_GET["do"];

//connect to the DB
include("fragments/databaseConnection.php");

switch ($do) {
    case "reset":
        //reset the count in the db to 0
		$query = "UPDATE count1 SET count = 0";
		mysql_query($query) or die(mysql_error());
		echo "count reset to 0";
		break;	
	
	case "display":
   		//select the current count and echo it
		$countInDb = mysql_query("SELECT * FROM count1");
		$current = mysql_fetch_array($countInDb,1);
		echo $current[count];
		break;
    
	default:
        //select the current count
		$countInDb = mysql_query("SELECT * FROM count1");
		$current = mysql_fetch_array($countInDb,1);
	
		//add 1 to the current counts
		$newCount = $current[count] + 1;
	
		//echo $newCount;
	
		//Save the result back to the database
		$query = "UPDATE count1 SET count = ".$newCount;
		mysql_query($query) or die(mysql_error());
		break;
}

//close mysql connection
mysql_close(); 

//write gif out if do param is not set
if (empty($do)) {

	header("Content-type: image/gif");
	header("Content-length: 43");
	$fp = fopen("php://output","wb");
	fwrite($fp,"GIF89a\x01\x00\x01\x00\x80\x00\x00\xFF\xFF",15);
	fwrite($fp,"\xFF\x00\x00\x00\x21\xF9\x04\x01\x00\x00\x00\x00",12);
	fwrite($fp,"\x2C\x00\x00\x00\x00\x01\x00\x01\x00\x00\x02\x02",12);
	fwrite($fp,"\x44\x01\x00\x3B",4);
	fclose($fp);

}
?>