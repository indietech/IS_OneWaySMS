<?php

// Here are the instructions:
// This service uses OneWaySMS (see line 14). 
// You must create an API key, create a text type custom field to house the sms message(see line 59), mass update the custom field for each of the contacts you want to send the SMS to by entering the message into this custom field, and then you can run HTTP Posts to this php file using the campaign manager for each target contact (or via action sets). Easiest way to trouble shoot is to use action sets. When this script receives the HTTP post from Infusionsoft, the script parses the phone number and custom field data and posts the message contained in the custom field via the SMS gateway. Simple! You must enter your application ID into line 52 where my comment is. 

function gw_send_sms($user,$pass,$sms_from,$sms_to,$sms_msg)  

            {           

                        $query_string = "api.aspx?apiusername=".$user."&apipassword=".$pass;
                        $query_string .= "&senderid=".rawurlencode($sms_from)."&mobileno=".rawurlencode($sms_to);
                        $query_string .= "&message=".rawurlencode(stripslashes($sms_msg)) . "&languagetype=1";        
                        $url = "http://gateway.onewaysms.com.au:10001/".$query_string;       

                        $fd = @implode ('', file ($url)); 
                        //echo $url;    

                        if ($fd)  
                        {                       
				    		if ($fd > 0) 
				    		{
						    	print("MT ID : " . $fd);
					 	    	$ok = "success";
				   		 	}        
				    		else 
				    		{
					     		print("Please refer to API on Error : " . $fd);
					    		echo "Fail code :" . $fd;
					     		$ok = "fail - Code :" . $fd;
				   			 }
                        }           
                        else      
                        {                       
                             // no contact with gateway                      
                             $ok = "fail - no contact with gateway";       
                        }           
                        return $ok;  
            }  

//get stuff off the URL
$ContactId = $_POST["Id"];
$Email = $_POST["Email"];
$mobno = $_POST["Phone1"];

//include the SDK
include_once("iSDK/isdk.php");
include_once("iSDK/xmlrpc-3.0/lib/xmlrpc.inc");
//build our application object
$app = new iSDK;
//connect to the API
if ($app->cfgCon("<enter your application string without the .infusionsoft.com>")) {
    echo "Connected...";
} else {
    die("Failed to connect to Infusionsoft!");
}

//_SMSMessage is the custom field that needs to be created in Infusionsoft
$returnFields = array( '_SMSMessage');
$conDat = $app->loadCon( (int) $ContactId, $returnFields);

$user = "APIUNRN43FFBM";
$pass = "APIUNRN43FFBMUNRN4";
$sms_from = "MAP";
$sms_to = $mobno;
$sms_msg = $conDat["_SMSMessage"];
echo $sms_msg;
echo $mobno;

$return = gw_send_sms($user,$pass,$sms_from,$sms_to,$sms_msg);
echo "Return code: " . $return;

?>