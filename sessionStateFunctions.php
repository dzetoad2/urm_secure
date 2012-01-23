<?php 
 require_once('sessionStateBean.php');
 
	function printSessionState($userId){
		$userId = cleanStrForDb($userId);
		
		$r = mysql_query("select sessionState from user where id=".$userId);
    	$row = mysql_fetch_assoc($r);
    	$s = $row['sessionState'];
    	$o = '<br/>'.$s.'<br/>';
    	return $o;
    	
	}
	
  function savePostAndSessionVars($userId, $post, $session, $pageTitle){
    $userId = cleanStrForDb($userId);
    $pageTitle = cleanStrForDb($pageTitle);
    $o = '';
    if(defined('DEBUG')){ 
      $o .= '<br/>-----------------<br/>sessionstate before:';
  	  $o .= printSessionState($userId);
    }
  	
  //make each one a string.      name:value name:value name:value ___ name:value name:value name:value
  	$postStr = 'testName:testValue,';
  	//$postStr = '';
    foreach ($post as $k=>$v){
  		$postStr.= $k.':'.$v.',';
  		if(defined('DEBUG')){
//  		echo '<br/>preparing to save postvar: '.$k.':'.$v;
  		}
  	}
  	$sessStr='';
  	foreach ($session as $k=>$v){
  		if($k != "authtoken"){
  			$sessStr.= $k.':'.$v.',';
  			if(defined('DEBUG')){
//  			echo '<br/>preparing to save sessvar: '.$k.':'.$v;
  			}
  		}else{
  		    if(defined('DEBUG')){
//  			echo '<br/>not saving sessvar: '.$k.':'.$v;
  		    }
  		}
  	}
  	if(defined('DEBUG')){
  	  $o .= '<br/>';
  	}
  	$stateStr = $postStr . '___' . $sessStr . '___'.$pageTitle;
  	if(defined('DEBUG')){
  	   $o .=  '<br/>savepost n sess vars: '.$stateStr.'<br/>';
  	}
  	
  	$stateStr = cleanStrForDb($stateStr);
  	
  	
  	$r = mysql_query("update user set sessionState='".$stateStr."' where  id=".$userId);
  	if($r===false){
  		return -1;
  	    $errorMsg = "error: savepostandsessionvars:  update query threw a false result";
  	    $_SESSION['errorMsg'] = $errorMsg;
        header('Location: errorPage.php');
  	}
  
  	if(defined('DEBUG')){
  	  $o .= '<br/>-----------------<br/>sessionstate after:';
  	  $o .= printSessionState($userId);
     $o .= '<br/>-----------end sessionstates print------';
  	}
  	return $o;
  }

  
  //==========================================================================================
  function getPostAndSessionVarsFromDb($userId){
    
  	$userId = cleanStrForDb($userId);
  	
  	$r = mysql_query("select sessionState from user where id=".$userId);
  	if($r===false){
  		throwMyExc('query fail');
  	}
    $row = mysql_fetch_assoc($r);
    $s = $row['sessionState'];  // a single string holding all the vars.
    if($s==''){
      // blank string, probably a new user.
      return null;
    }
    $ps = explode('___',$s);        	// we will see 3 items in array:  name:val,name:val,___name:val,name:val,____  pagename
    if(count($ps)!=3){  //enforce 3 things exactly!
    	throwMyExc('getpostandsessionvars: count of ps array was not 3. it MUST be exactly 3 (post, session, pagename) always unless blank');
    }
    $_post = explode(',',$ps[0]);
    $post = array();
    foreach($_post as $v){
    	if(trim($v)!=''){
	    	$post[] = $v;
    	}
    }
    $_sess = explode(',',$ps[1]);
    $sess = array();
    foreach($_sess as $v){
		if(trim($v)!=''){
		    $sess[] = $v;
		}
    }
    $pageTitle = $ps[2];
    $sessStateBean = new SessionStateBean();
    $sessStateBean->post = $post;
    $sessStateBean->sess = $sess;
    $sessStateBean->pageTitle = $pageTitle;
	return $sessStateBean;    
  }

  function loadState($userId){
  	$userId = cleanStrForDb($userId);
  	$ret = getPostAndSessionVarsFromDb($userId);
  	if($ret==null){
  		return null;
  		//if the get didnt work, the sessionstate data was either empty , throw it away and go back to normal page flow.
  	}
  	
  	if(isset($ret->sess)){
  	  $sess = $ret->sess;
  	  if(!is_array($sess)){
  	  	return;
  	    $errorMsg='sess not an array';
  	    $_SESSION['errorMsg'] = $errorMsg;
		header('Location: errorPage.php');
		exit();
  	  }
  	  foreach($sess as $v){
//  	  	echo '<br/>pulling out of sess arr: '.$v;
  	  	$t = explode(':',$v);
  	  	if(!isset($t[0])) {
  	  	  //return;
  	  	  $errorMsg='sess loop: t 0 not set';
  	  	  $_SESSION['errorMsg'] = $errorMsg;
		  header('Location: errorPage.php');
		  exit();
  	  	}
  	  	if(!isset($t[1])) {
  	  	  $t[1] = ''; 
  	  	  $errorMsg='sess loop: t 1 not set';
  	  	  $_SESSION['errorMsg'] = $errorMsg;
		  header('Location: errorPage.php');
		  exit();
  	  	}
  	  	$_SESSION[$t[0]] = $t[1];
  	  	if(defined('DEBUG')){
  	  	 echo '<br/>added to session from session: '.$t[0].', '. $t[1];
  	  	}
  	  }
  	  if(defined('DEBUG')){
  	   echo '<br/>';
  	  }
  	}else{
  		//return;
  	    $errorMsg='error: loadstate: session was not set from getpostandsessionvarsfromdb!';
  	    $_SESSION['errorMsg'] = $errorMsg;
		header('Location: errorPage.php');
		exit();
  	}
    if(isset($ret->post)){
      $post = $ret->post;
      if(!is_array($post)){  
		//return;
        $errorMsg = 'post not an array';
        $_SESSION['errorMsg'] = $errorMsg;
		header('Location: errorPage.php');
		exit(); 
      }
      foreach($post as $v){
//      	echo '<br/>pulling out of post arr: '.$v;
      	$t = explode(':',$v);
  	  	if(!isset($t[0])){
  	  	 //return;
  	  	 $errorMsg='post loop: t 0 not set';
  	  	 $_SESSION['errorMsg'] = $errorMsg;
		 header('Location: errorPage.php');
		 exit();
  	  	}
  	  	if(!isset($t[1])){
  	  	  //return;
  	  	  $errorMsg='post loop: t 1 not set';
  	  	  $_SESSION['errorMsg'] = $errorMsg;
		  header('Location: errorPage.php');
		  exit();
  	  	}
  	  	$_SESSION[$t[0]] = $t[1];
  	  	if(defined('DEBUG')){
      	 echo '<br/>added to session from post: '.$t[0].', '. $t[1];
  	  	}
      }
    }else{
        //return;
        $errorMsg='error: loadstate: post was not set from getpostandsessionarsfromdb!';
        $_SESSION['errorMsg'] = $errorMsg;
	    header('Location: errorPage.php');
		exit();	
    }
    if(isset($ret->pageTitle)){
      $pageTitle = $ret->pageTitle;
      if($pageTitle=="" || !isValidPage($pageTitle)){
      	//   "pagetitle pulled from db was blank"
      	$errorMsg = 'pagetitle is blank or pagetitle not valid: '.$pageTitle. ', ';
      	if(!isValidPage($pageTitle)){
      		$errorMsg .= 'page not valid!: '.$pageTitle.', ';
      	}
      	$_SESSION['errorMsg'] = $errorMsg;
		header('Location: errorPage.php');
		exit();
      }
      else if($pageTitle=="home.php"){
        if(defined('DEBUG')){
      	 echo "<br/>loadstate: pagetitle is home.php, so do nothing!";
        }
      	return;  //nothing to be done, stay on this page.
      }
      else{
        if(defined('DEBUG')){
      	 echo '<br/>pagetitle: '.$pageTitle;
        }
        
      	header('Location: '.$pageTitle);
      	
       	exit();
      }
    }else{
    	//header('Location: home.php');
    	$errorMsg=' loadstate: pagetitle was not set';
    	$_SESSION['errorMsg'] = $errorMsg;
		header('Location: errorPage.php');
		exit();
    	
    	
    	
    	
    }
    	//now put sessionvars in session, then put post in session, then headerfwd to pagetitle page.
//      foreach($session as $v){
      	//$_SESSION[]
//      }	
  }
  
  function isValidPage($p){
  	$pattern = '/^[a-zA-Z]*\.php/';
    if(preg_match($pattern,$p)){  	
    	return true;
    }else{
    	return false;
    }
    
  }