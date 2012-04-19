<?php 
require_once('modProfileFunctions.php');
 

function sendPasswordResetEmail($un){
 /* Algorithm:  This is for resetting a user's password.  First, set up and send the email, it has a random passwrod in it.
  * If the email fails, do nothing (error msg is displayed). 
  * If mail succeeds, we have to get the password hash of that password and store in that user's account.
  */
 cleanStrForDb($un);
 $to =  $un;        // $un  goes here, not the test email box.
 $subject = "URM password reset";
 $pw = makeRandomPassword();
 if(defined('DEBUG')){
   echo 'DEBUG: new pw is: '.$pw.'<br/>';
 }
 $message = "This is a message from the URM password-reset system.\n  Your new password is: ".$pw."  After logging in,".
 			" please go to the Create/Modify Profile page from the home screen to set a new password.";
 $from = "URM_Password_Reset--do_not_reply@aarc.org";
 $headers = "From:" . $from;

 $e = '';
  if(!mail($to,$subject,$message,$headers)){
  	 $e .=  'Failure sending email<br/>';
  }
  if($e==''){
    //succeeded - so now get pw hash of this new pw, and set it in the user table.
    $e .= updatePasswordForUserAccount_Username($un,$pw);
  }
  return $e;
}

function makeRandomPassword() {
    $chars = "abcdefghijkmnopqrstuvwxyz023456789@%&";     //34 chars total.
    srand((double)microtime()*1000000);
    $i = 0;
    $pass = '' ;
    while ($i <= 10) {
        $num = rand() % 36;
        $tmp = substr($chars, $num, 1);
        $pass = $pass . $tmp;
        $i++;
    }
    return $pass;
}

  

//notes: 

//Any­way, assum­ing you’ve got send­mail (or an MTA which pro­vides send­mail hooks — 
//I’m actu­ally using post­fix here) installed, you can sim­ply set this in your php.ini, 
//restart Apache (using apache2ctl restart from a root account), and all should be working:
//
//sendmail_path = /usr/sbin/sendmail -i -t