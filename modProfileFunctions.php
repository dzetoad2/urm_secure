<?php
function updatePasswordForUserAccount($userId,$pw){       //username

	$userId = cleanStrForDb($userId);
	$pw = cleanStrForDb($pw);
	
	  $pwhash = constant("SALT") . sha1($pw. constant("SALT"));
      $result = mysql_query("update user   set pwhash='".$pwhash."' where id=".$userId."  "  );           //check un/pw against db.
      if($result===false){
      	//return FALSE;
      	throwMyExc('updatePasswordForUserAccount: query fail');
      }
      return TRUE;
      
      
      //    where userid=".$userId."
}
function updatePasswordForUserAccount_Username($un,$pw){       //username

	$un = cleanStrForDb($un);
	$pw = cleanStrForDb($pw);
	$pwhash = constant("SALT") . sha1($pw. constant("SALT"));
	$pwhash = cleanStrForDb($pwhash);
	
	  //echo "DEBUG --    un: ".$un.",  pw: ".$pw.",  pwhash: ".$pwhash."<br/>";
	
      $result = mysql_query("update user   set pwhash='".$pwhash."' where username='".$un."' limit 1  "  );           //check un/pw against db.
      // update user   set pwhash='#@2xf505907640ec7425d36cb66637d8a5080ad61bae65' where username='testemailaddress@bla.com' limit 1  
      
      
      if($result===false){
      	$em='updatePasswordForUserAccount_Username: Failure updating password - Ignore the new password sent via email';
      	throwMyExc($em);
      }
      return 'Successfully updated password - Please check your email for the updated password';
      //    where userid=".$userId."
}
function updateUserProfile($un,$fn,$ln,$phone){
	$un = cleanStrForDb($un);
	$fn = cleanStrForDb($fn);
	$ln = cleanStrForDb($ln);
	$phone = cleanStrForDb($phone);	    

	if(false===mysql_query("update  user  set firstname = '".$fn."', lastname= '".$ln."', phone= '".$phone."' where username='$un'  ")){  //try to update the facility id num in the user row. if fail, return false.
		//return FALSE;
		throwMyExc('updateUserProfile: query fail');
	}
	//succeeded!
	return TRUE;
}
//this function needs to be renamed!   is it even used???
function getParam($un,$p){
	$un = cleanStrForDb($un);
	$p = cleanStrForDb($p);
	if(!isset($un)){
		return "un was not set!";
	}
	if(!isset($p)){
		return "p was not set!";
	}
	$result = mysql_query("select ".$p ." from user where user.username = '".$un."'");          //resoruce on success, false on error.
	if($result===false){
		$em="getParam: bad result";
		throwMyExc($em);
	}
	$numrows = mysql_num_rows($result);    // The number of rows in a result set on success or FALSE on failure.
	if($numrows===false){
		 //rowcount 0: error cuz its a bad username.       rowcount 1 but "": not set yet.
		throwMyExc("numrows is false");
	}
	if(($numrows > 1) || ($numrows < 0)){
		return "numrows < 0 or numrows > 1";
	}
	if ($numrows == 0){
		return "Not set";
	}
	
	if ($numrows == 1){
	//numrows must be 1 here.
		$row = mysql_fetch_assoc($result);   //associative. this is first row of the result. Returns an associative array of strings that corresponds to the fetched row, or FALSE if there are no more rows.
		$o = $row[$p];
		if(!$o)
			return "Not set";
		return $o;
	}
	return "unknown error getfirstname";
	
}
 
 

 