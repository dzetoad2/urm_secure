<?php

//contants =========================
 define("SALT","#@2xf5",TRUE);
 // the password encryption is $salt.sha1($password.$salt)) 
 define("EXPIRY_INTERVAL","P14D",TRUE);    //case insensitie = true
//==================================

//checks if user is in db.  returns TRUE if so, else FALSE.
function userInDb($un){       //username
	  $un = cleanStrForDb($un);
      $result = mysql_query("SELECT * FROM user WHERE username='$un'");           //check un/pw against db.
      if($result===false){
		  throwMyExc('userInDb(): query fail');
	  }
	  $numrows = mysql_num_rows($result);
	  if ($numrows == 0){
	  	return FALSE;
	  }else{
      	return TRUE;
	  }
}

function createNewUserAccount($un,$pw){       //username
	$un = cleanStrForDb($un);
	$pw = cleanStrForDb($pw);
	$pwhash = constant("SALT") . sha1($pw. constant("SALT"));
	$authtoken = createAuthToken($un);
	  // insert into user    values (NULL, 'admin4','123','456',NULL)             works !!!
	  //mysql_query("INSERT INTO Persons (FirstName, LastName, Age)
      //VALUES ('Peter', 'Griffin', '35')");
    $result = mysql_query("insert into user (username, pwhash, authtoken, firstname, lastname, phone) values ('".$un."','".$pwhash."','".$authtoken."', ' ', ' ', ' ')    "  );           //check un/pw against db.
      //insert : true on success, false on error.
    if($result===false){
    	$em='query false';
    	throwMyExc($em);
    }
    return TRUE; 
}

//updates timestamp of the user given.
function updateTimestamp($un){
	 //you have to change something to get the auto update timestamp to work.  or else, just set it with UNIX   TIMESTAMP
	 //LIKE :     $query="UPDATE articles SET time=UNIX_TIMESTAMP() WHERE id='$ud_id'";
	  
	$un = cleanStrForDb($un);

      if(FALSE === mysql_query("update  user  set timestamp = NULL  where username='$un'  ")){           //check un/pw against db.
		$errorMsg="UpdateTimestamp(): query failed!";
		throwMyExc($errorMsg);
      }
}
function getExpiryDate($un){
  //according to 'EXPIRY_INTERVAL' constant, determine when user login auth expires.
	$un = cleanStrForDb($un);

  $results = mysql_query("select * from user  where username='$un' "); 
  if($results === FALSE){           //check un/pw against db.
		$errorMsg="getExpiryDate: query failed";
		throwMyExc($errorMsg); 
  }
  $numrows = mysql_num_rows($results);
  if ($numrows != 1){
  	$errorMsg="getExpiryDate: rows not 1, getExpiryDate func error";
  	throwMyExc($errorMsg); 
  	
  }
  $row = mysql_fetch_assoc($results);   //associative. this is first row of the result.
  $timestamp_in_db = $row['timestamp']; //this is the timestamp of expiry
  if(!isset($timestamp_in_db)){
	$errorMsg="getExpiryDate: timestamp_in_db not set! line 60<br/>";
	throwMyExc($errorMsg);
  } 
		//echo "timestamp in db: ".$timestamp_in_db."<br/>";
	 $dateOfExpiry = new DateTime('now');
//	 echo "5<br/>";
	 $dateOfExpiry->setTimestamp(strtotime($timestamp_in_db));   //$timestamp_in_db  should be the parameter here.      example of timestamp : 1171502725
	 $dateOfExpiry->add(new DateInterval(  constant("EXPIRY_INTERVAL")));    //p14d
//	 echo "6<br/>";  
	 return $dateOfExpiry->format('Y-m-d H:i:s');
}
