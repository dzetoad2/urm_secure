<?php 

function isValidPassword($pw){
/*# of characters btwn 8 and 30
 * no weird chars.  only lowercase,  uppercase,  digits 0-9, !@#$%^&*. no others!
 */
	$e='';
	if(strlen($pw)<8){
		$e .= 'Password must contain at least 8 characters<br/>';
	}
	if(strlen($pw)>30){
		$e .= 'Password must not contain more than 30 characters<br/>';
	}
	// 
	$p = '/^[a-zA-Z0-9!@#$%^&*]+$/';
	$r = preg_match($p,$pw);
	if(0===$r){
		$e .= 'Password must not contain characters other than alphanumerics and the following:  !@#$%^&*  <br/>';
	}
	
	
//has a number
	if( !preg_match("#[0-9]+#", $pw) ) {
		$e .= "Password must include at least one number <br />";
	}
//has a letter
	if( !preg_match("#[a-z]+#", $pw) ) {
		$e .= "Password must include at least one letter <br />";
	}
	return $e;
}


/*
 *   checks to see if is pos int.
 *   1 is ok
 *   0001 is ok
 *   9 is ok
 *   0 is not
 *   -1 is not
 *   -300 is not
 *   
 */
function isPosInt($instr){
		$p = '/^[0]*[1-9][0-9]*$/';            
		$r = preg_match($p, $instr);  //pregmatch returns 0 or 1.  0 means it was found 0 times.
		if(0===$r){
			return false;
		}elseif(1===$r){
			return true;
		}else{
		throwMyExc("isPosInt(): preg match was neither 0 nor 1!");
		}
}

function isNonNegInt($instr){
		$p = '/^[0-9]+$/';            
		$r = preg_match($p, $instr);  //pregmatch returns 0 or 1.  0 means it was found 0 times.
		if(0===$r){
			return false;
		}elseif(1===$r){
			return true;
		}else{
		throwMyExc("isnonnegInt(): preg match was neither 0 nor 1!");
		}
	
	
}


function isDouble($dur){
	$p = '/^[0-9]+((\.)[0-9]+)?$/';
	if(  preg_match($p,$dur)){
		return true;
	}else{ 
		return false;
	}
	
}

function isEmailAddress($email){

 if(!filter_var($email, FILTER_VALIDATE_EMAIL))
   {
     return false;
   }
	return true;		
}

function isValidZip($z){
 if(preg_match("/^([0-9]{5})(-[0-9]{4})?$/i",$z)){
    return true;
 }else{
    return false;
 }
}

function isValidPartialZip($z){
	 //check 3+ characters, and isposint(input_string_int).
	 if(  (strlen($z) >= 3)  &&   (isNonNegInt($z))  ){
	 	return true;
	 }
	 return false;
}


function validateUSAZip($z)
{
  if(preg_match("/^([0-9]{5})(-[0-9]{4})?$/i",$z))
    return true;
  else
    return false;
}
function validZip2($z){
    if (preg_match('/^[0-9]{5}([- ]?[0-9]{4})?$/', $z))
	  return true;
    else 
      return false;
}
?>