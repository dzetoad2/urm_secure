<?php
require_once('urm_secure/constants.php');

// ---- also  has  dbfunctions at the bottom of this file. ----

 
session_start();

//connect db
$conn = mysql_connect("localhost","myroot","f");
if($conn===FALSE) 
{
	$errorMsg="Database connection failed. Check name/pw.";
	throwMyExc($errorMsg);
}
if(FALSE===mysql_select_db("urm")){
 	$errorMsg="select fail of database - db name needs to be 'urm'";
 	throwMyExc($errorMsg);
 
}

function createAuthToken($username){  // this will be a function based on username and a random salt. 
	return $username . sha1($username . '5xuap@'. uniqid());  
	//"5111115";
}
function loggedInAdmin(){
	//
  if(isset($_COOKIE['username'])){
    $username = $_COOKIE['username'];
  }
  if(isset($_SESSION['username'])){
    $username = $_SESSION['username'];
  }
  if(isset($_SESSION['userid'])){
    $userId = $_SESSION['userid'];
  }
  if($username != "dzetoad2@gmail.com"  &&  $username != "admin"  && $username != "dubbs@aarc.org"){
  	//we are NOT an admin.
  	$em="loggedInAdmin:  error - non admin attempt to access admin page";
  	throwMyExc($em);
  }
	
  return loggedin();
  
}

//login check func,  checks  username and userid and authtoken for them.
function loggedin(){
//	echo '<a href="logout.php">Log out</a> <br/>';
  $loggedin = FALSE;
  if(isset($_COOKIE['username'])){
    $username = $_COOKIE['username'];
  }
  if(isset($_SESSION['username'])){
    $username = $_SESSION['username'];
  }
  if(isset($_SESSION['userid'])){
    $userId = $_SESSION['userid'];
  }
  
  if(isset($_SESSION['authtoken'])  ){
	$authtoken = $_SESSION['authtoken'];
  }else if(isset($_COOKIE['authtoken'])    ){
    $authtoken = $_COOKIE['authtoken'];
  }
   
    if(isset($username) && isset($authtoken) && isset($userId)){
//       echo "2<br/>";
    //check if this authtoken matches the username
    	$username = cleanStrForDb($username);
    	$userId = cleanStrForDb($userId);
   	  $q1 = "SELECT * FROM user WHERE username='$username' and id=$userId";
      $result = mysql_queryCustom($q1);           //check un/pw against db.
      if($result===false){
      	$em='loggedin: query fail';
      	throwMyExc($em);
      }
      //d i e('q1 was: '.$q1);
	  if(!isset($result)){ 
	    $em = "Error checking login against database";
	    throwMyExc($em);
	  }
//	      echo "3<br/>";
	 $row = mysql_fetch_assoc($result);   //associative. this is first row of the result.
	 $authtoken_in_db = $row['authtoken'];
	 if($authtoken != $authtoken_in_db){
	 	 //authtoken doesnt match - kick them out.
	 	 //delete the authtoken cookie! wipe out authoken in db for this user
	 	 
	 	 //d i e('authtkn != authtkn in db.  authtoken: '.$authtoken.', authtkn in db: '.$authtoken_in_db);
	 	 
	     //   (" The authtoken from session or cookie does not match authtoken in db entry! you are kicked out!");
	     //echo('<br/> authtoken: '.$authtoken);
	     //echo('<br/> authtoken: '.$authtoken_in_db);
	     
	     //echo('<br/>authtoken != authtoken in db!!!');
	     sleep(3);
	     
	     header("Location: logout.php");
	     exit();
	 }
	  
	 //authtoken matches. now check expiry.
	 $timestamp_in_db = $row['timestamp']; //this is the timestamp of expiry
	 if(!isset($timestamp_in_db)){
	  	throwMyExc("Timestamp_in_db not set!");
	 }else {
		//echo "timestamp in db: ".$timestamp_in_db."<br/>";
	 }
	 //DateTimeZone object as 2nd param?
	 $dateOfExpiry = new DateTime('now',new DateTimeZone('America/Mexico_City')); //'now'
	 $timezone_str = 'America/Mexico_City';
	 $flag = date_default_timezone_set($timezone_str);
	 if($flag===false){
	    throw new Exception('date_default_timezone_set has error: timezone str invalid: '.$timezone_str);
	 }
	 $dateOfExpiry->setTimestamp(strtotime($timestamp_in_db));   //$timestamp_in_db  should be the parameter here.      example of timestamp : 1171502725
	 $dateOfExpiry->add(new DateInterval(  constant("EXPIRY_INTERVAL")));    //p14d
//	 echo "6<br/>";  
	 $expiry = $dateOfExpiry->getTimestamp();
	 $date = new DateTime("now",new DateTimeZone('America/Mexico_City'));
	 $current_timestamp = $date->getTimestamp();
//	 echo "7<br/>";
	  
	 if($current_timestamp > $expiry){
	   //we passed expiry! so d i e.
	   //d i e ('Timestamp has expired. This is possibly a system error. <br/> : <a href="logout.php">Go to the Login page to log in.</a>');
	   header('Location: logout.php');     //this was a hack todo this, may need to analyze what's really going on.
	   exit();
	 }else{
	   //echo "Time is not expired yet, so log in check is successful, loggedin==true. <br/>";
	   $loggedin = TRUE;
	   updateTimestamp($username);
	 }
	  
	 
	 //echo "current timestamp: (what time it is RIGHT NOW) ".$current_timestamp."<br/>";   
//	   echo "expiry timestamp: (the expiry date in the database plus the interval) ".$expiry."<br/>";
    }
    return $loggedin;
}

function wipeOutAuthtoken($username){
    $result = mysql_queryCustom("UPDATE user  set  authtoken='' WHERE   username='$username'  ");           //update authtoken in db.
 	if($result===FALSE){
 	   $errorMsg="wipeoutauthtoken:  Error wiping out authtoken for ".$username.", please contact the adinistrator.";
 	   throwMyExc($errorMsg);
 	   exit();
 	}
}



function closeDb(){
   mysql_close($conn);
}
 
function logLoginSuccess($un, $pw, $ip){
	$timezone_str = 'America/Mexico_City';
	$flag = date_default_timezone_set($timezone_str);
	if($flag===false){
	    throw new Exception('date_default_timezone_set has error: timezone str invalid: '.$timezone_str);

	}
	error_log('<LOGIN_SUCCESS>'.'username: '.$un.', ip: '.$ip.' </LOGIN_SUCCESS>');
	
}


function logLoginError($un, $pw, $ip){
	$timezone_str = 'America/Mexico_City';
	$flag = date_default_timezone_set($timezone_str);
	if($flag===false){
	    throw new Exception('date_default_timezone_set has error: timezone str invalid: '.$timezone_str);

	}
	error_log('<LOGIN_ERROR>'.'username: '.$un.', pw(attempted): '.$pw.', ip: '.$ip.' </LOGIN_ERROR>');
	
}

/*
 * e:  exception object
 * errormsg:  a string
 * frompage:  a string - name of the page the error was thrown on
 */
function goErrorPage($e){
	$_SESSION['errorMsg']=$e;
	header('Location: errorPage.php');
	exit();
}

function throwMyExc($em){
	 //just in case:
	 //error_reporting()
	 //error_reporting(E_ALL); 
     //ini_set("display_errors", 1);
	 //----
	 
     try{
     	if(trim(mysql_error())!=''){
     	 $mysqlErr = ', mysql_error: '. mysql_error();
     	}else{
     	 $mysqlErr = '';	
     	}
	    $em_full = $em . $mysqlErr;
     	throw new Exception($em_full);
     	
     }catch(Exception $e){
     	$un='';
     	$uid='';
     	if(isset($_SESSION['username'])){
     		$un = 'username: ' . $_SESSION['username'];
     	}
     	if(isset($_SESSION['userid'])){
     		$uid ='userid: ' . $_SESSION['userid'];
     	}
     	
	 	$msg = $e->getMessage();
	 	$code = $e->getCode();
	 	$file = $e->getFile();
	 	$line = $e->getLine();
	 	$trace = $e->getTraceAsString();
	 	$message = '<ERROR>'.$un.', '.$uid.  ', msg: '.$msg.', file: '.$file.', line: '.$line.', trace: '.$trace.'</ERROR>';
	 	if(isset($_SERVER['HTTP_USER_AGENT'])){
	 	  $message.= $_SERVER['HTTP_USER_AGENT'];
	 	}else{
	 		$message.= ", http_user_agent not set";
	 	}
        if(isset($_SERVER['HTTP_USER_AGENT'])){
	 	  $message.= $_SERVER['HTTP_USER_AGENT'];
	 	}else{
	 		$message.= ", http_user_agent not set";
	 	}
	 	$remoteAddr = getenv('REMOTE_ADDR');
	 	if(isset($remoteAddr) && $remoteAddr != false){
	 		$message .= ", remoteaddr: ".$remoteAddr;
	 	}else{
	 		$message.= ", remoteAddr not set";
	 	}
	 	error_log($message);
	 	sendEmail($_SESSION['userid'], '<ERROR>'.$message.'</ERROR>');
     }
	 	 //error_log("test error log in throwmyexc",3,'/var/log/URM-errors2.log') or d i e('could not log error');
	 throw new Exception($e);
	 //$e->getMessage()
	 /*
	  * 
	  *0 :	message is sent to PHP's system logger, using the Operating System's system logging mechanism or a file, 
	 depending on what the error_log configuration directive is set to. This is the default option.
	 3:  email
	 */
}
function throwMyExc_nonCritical($em){
	
  try{
     	if(trim(mysql_error())!=''){
     	 $mysqlErr = ', mysql_error: '. mysql_error();
     	}else{
     	 $mysqlErr = '';	
     	}
	    $em_full = $em . $mysqlErr;
     	throw new Exception($em_full);
     	
     }catch(Exception $e){
     	$un='';
     	$uid='';
     	if(isset($_SESSION['username'])){
     		$un = 'username: ' . $_SESSION['username'];
     	}
     	if(isset($_SESSION['userid'])){
     		$uid ='userid: ' . $_SESSION['userid'];
     	}
     	
	 	$msg = $e->getMessage();
	 	$code = $e->getCode();
	 	$file = $e->getFile();
	 	$line = $e->getLine();
	 	$trace = $e->getTraceAsString();     
	 	$message = '<WARNING>'.$un.', '.$uid.  ', msg: '.$msg.', file: '.$file.', line: '.$line.', trace: '.$trace.'</WARNING>';
	 	error_log( '<WARNING>'.$un.', '.$uid.  ', msg: '.$msg.', file: '.$file.', line: '.$line.', trace: '.$trace.'</WARNING>');
	 	sendEmail($_SESSION['userid'], '<WARNING NONCRITICAL LOGGED>'.$message.'</WARNING NONCRITICAL LOGGED>');
     }
	header('Location: home.php');
	exit();
}

function sendEmail($userId, $message){
	
    $to = 'dzetoad2@gmail.com';
    $from = "URM_Notifications_Errors--do_not_reply@aarc.org"; 
    $subject = "URM Notification - Error occurred"; 
	$headers  = "From: $from\r\n"; 
    $headers .= "Content-type: text/html\r\n"; 
	
	$em = '';
    if(!mail($to,$subject,$message,$headers)){
  	  $em .=  'sendEmail():  Failure sending email<br/>';
  	  //throwMyExc($em);
  	  
  	  
    }
	
}


function cleanDocString($s){
  $s = convert_smart_quotes($s);
  $s =  ($s);
  //$s = htmlentities($s);
  return $s;
}

function cleanStrForDb($s){
	return mysql_real_escape_string($s);
}


/*
 * can tweak things if necessary.
 */
function mysql_queryCustom($q){
	return mysql_query($q);
}

function convert_smart_quotes($s){

 $search = array(chr(145), 
                    chr(146), 
                    chr(147), 
                    chr(148), 
                    chr(151)); 

    $replace = array("'", 
                     "'", 
                     '"', 
                     '"', 
                     '-'); 

    return str_replace($search, $replace, $s); 
}

require_once('urm_secure/dbfunctions.php');
