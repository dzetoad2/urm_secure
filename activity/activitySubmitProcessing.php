<?php

 $_fid = $_POST['fid'];
 $_aid = $_POST['aid'];
 $_is_cf = $_POST['is_cf'];
 $_is_ca = $_POST['is_ca'];

 	(isset($_POST['isPerformedAdult'])) ? $_isPerformedAdult = trim($_POST['isPerformedAdult']) : $_isPerformedAdult = "na";
	(isset($_POST['isPerformedPediatric'])) ? $_isPerformedPediatric = trim($_POST['isPerformedPediatric']) : $_isPerformedPediatric = "na";
	(isset($_POST['isPerformedNatal'])) ? $_isPerformedNatal = trim($_POST['isPerformedNatal']) : $_isPerformedNatal = "na";
	(isset($_POST['hasTimestandardAdult'])) ? $_hasTimestandardAdult = trim($_POST['hasTimestandardAdult']) : $_hasTimestandardAdult = "na";
	(isset($_POST['hasTimestandardPediatric'])) ? $_hasTimestandardPediatric = trim($_POST['hasTimestandardPediatric']) : $_hasTimestandardPediatric="na";
	(isset($_POST['hasTimestandardNatal'])) ? $_hasTimestandardNatal = trim($_POST['hasTimestandardNatal']) : $_hasTimestandardNatal="na";
	(isset($_POST['durationAdult'])) ? $_durationAdult = trim($_POST['durationAdult']) : $_durationAdult = -1;    // && isPosInt($_POST['durationAdult'])   //... not sure if need the isposint check here.
	(isset($_POST['durationPediatric'])) ? $_durationPediatric = trim($_POST['durationPediatric']) : $_durationPediatric = -1; //&& isPosInt($_POST['durationPediatric'])
	(isset($_POST['durationNatal'])) ? $_durationNatal = trim($_POST['durationNatal']) : $_durationNatal = -1; // && isPosInt($_POST['durationNatal'])
	(isset($_POST['volumeAdult']) ) ? $_volumeAdult =  trim($_POST['volumeAdult']) : $_volumeAdult = -1; //&& isPosInt($_POST['volumeAdult'])
	(isset($_POST['volumePediatric']) ) ? $_volumePediatric =  trim($_POST['volumePediatric']) : $_volumePediatric = -1; //&& isPosInt($_POST['volumePediatric'])
	(isset($_POST['volumeNatal']) ) ? $_volumeNatal =  trim($_POST['volumeNatal']) : $_volumeNatal = -1; //&& isPosInt($_POST['volumeNatal'])
	(isset($_POST['methodologyAdult'])) ? $_methodologyAdult = trim($_POST['methodologyAdult']) : $_methodologyAdult = "na";
	(isset($_POST['methodologyPediatric'])) ? $_methodologyPediatric = trim($_POST['methodologyPediatric']) : $_methodologyPediatric = "na";
	(isset($_POST['methodologyNatal'])) ? $_methodologyNatal = trim($_POST['methodologyNatal']) : $_methodologyNatal="na";
	//----- for digits, if blank, make em -1.
	//=================ADULT BLOCK =====================
 if(isset($activityId) && $activityData['isForAdult'] == "no"){
    $_isPerformedAdult="no";	
 }
 if($_isPerformedAdult=="na"){
 	$errorLabel .= "Error: Adult: Do you perform this procedure? Please choose yes or no<br/>";
 	$_isPerformedAdultErrorImg = $errorImg; 
 }else if($_isPerformedAdult=="no"){
	//done :  quit this code block.
	$_hasTimestandardAdult="na"; //forced
	$_durationAdult=-1; //forced
	$_volumeAdult=-1; //forced
	$_methodologyAdult="na";//forced
 }else{ //isperf is yes.
 	if ($_hasTimestandardAdult=="na"){
 		$errorLabel .= "Error: Adult: Do you have a time standard for this procedure? Please choose yes or no<br/>";
 		$_hasTimestandardAdultErrorImg = $errorImg; 
 	}else if($_hasTimestandardAdult=="no"){
 		//done
 		$_durationAdult=-1; //forced
		$_volumeAdult=-1; //forced
		$_methodologyAdult="na";//forced
 	}else{ //hastimestandard == yes
 		if(!isPosInt($_durationAdult)){
 			//durationadult is invalid, error, done.
 			$errorLabel.="Error: Adult:  Duration must be a positive integer<br/>";
 			$_durationAdultErrorImg = $errorImg;
 			
 		}
 		if($_durationAdult>999){
 				$errorLabel.="Error: Adult: Duration must be less than 1000<br/>";
 				$_durationAdultErrorImg = $errorImg;
 		}
 		if(isset($customActivityId)){
 		  if($_volumeAdult < 0 || $_volumeAdult > 20000 || !isPosInt($_volumeAdult)){
 				$errorLabel.="Error: Adult: Volume must be a positive integer between 0 and 20000 inclusive<br/>";
 				$_volumeAdultErrorImg = $errorImg;
 		  }
 		}
 		if($_methodologyAdult=="na"){
 				$errorLabel.="Error: Adult: Please choose a methodology<br/>";
 				$_methodologyAdultErrorImg = $errorImg;
 		}
 	}
 }
 //========================PEDIATRIC BLOCK=======================
 if(isset($activityId) && $activityData['isForPediatric'] == "no"){
    $_isPerformedPediatric="no";	
 }
 $_hasTimestandardPediatricErrorImg='';
 if($_isPerformedPediatric=="na"){
 	$errorLabel .= "Error: Pediatrics: Do you perform this procedure? Please choose yes or no<br/>";
 	$_isPerformedPediatricErrorImg = $errorImg; 
 }else if($_isPerformedPediatric=="no"){
	//done 	
	$_hasTimestandardPediatric="na";//forced
	$_durationPediatric=-1;//forced
	$_volumePediatric=-1;//forced
	$_methodologyPediatric="na";//forced
 }else{ //isperf is yes.
 	if ($_hasTimestandardPediatric=="na"){
 		$errorLabel .= "Error: Pediatrics: Do you have a time standard for this procedure? Please choose yes or no<br/>";
 		$_hasTimestandardPediatricErrorImg = $errorImg; 
 	}else if($_hasTimestandardPediatric=="no"){
 		//done
 		$_durationPediatric=-1;//forced
		$_volumePediatric=-1;//forced
		$_methodologyPediatric="na";//forced
 	}else{//hastimestandard == yes
 		if(!isPosInt($_durationPediatric)){
 			//durationped is invalid, error, done.
 			$errorLabel.="Error: Pediatrics:  Duration must be a positive integer<br/>";
 			$_durationPediatricErrorImg = $errorImg;
 		}
 		if($_durationPediatric>999){
 				$errorLabel.="Error: Pediatrics: Duration must be less than 1000<br/>";
 				$_durationPediatricErrorImg = $errorImg;
 		}
 		if(isset($customActivityId)){
 		  if($_volumePediatric < 0 || $_volumePediatric > 20000 || !isPosInt($_volumePediatric)){
 				$errorLabel.="Error: Pediatrics: Volume must be a positive integer between 0 and 20000 inclusive<br/>";
 				$_volumePediatricErrorImg = $errorImg;
 		  }
 		}
 		if($_methodologyPediatric=="na"){
 				$errorLabel.="Error: Pediatrics: Please choose a methodology<br/>";
 				$_methodologyPediatricErrorImg = $errorImg;
 		}
 	}
 
 }
 //=======================NATAL BLOCK======================
 if(isset($activityId) && $activityData['isForNatal'] == "no"){
    $_isPerformedNatal="no";	
 }
$_isPerformedNatalErrorImg=''; 
$_hasTimestandardNatalErrorImg='';
if($_isPerformedNatal=="na"){
 	$errorLabel .= "Error: NeoNatal: Do you perform this procedure? Please choose yes or no<br/>";
 	$_isPerformedNatalErrorImg = $errorImg; 
 }else if($_isPerformedNatal=="no"){
	//done
	$_hasTimestandardNatal="na";//forced
 	$_durationNatal=-1;//forced
	$_volumeNatal=-1;//forced
	$_methodologyNatal="na";//forced
 	 	
 }else{ //isperf is yes.
 	if ($_hasTimestandardNatal=="na"){
 		$errorLabel .= "Error: NeoNatal: Do you have a time standard for this procedure? Please choose yes or no<br/>";
 		$_hasTimestandardNatalErrorImg=$errorImg; 
 	}else if($_hasTimestandardNatal=="no"){
 		//done
 		$_durationNatal=-1;//forced
		$_volumeNatal=-1;//forced
		$_methodologyNatal="na";//forced
 	}else{//hastimestandard == yes
 		if(!isPosInt($_durationNatal)){
 			//durationadult is invalid, error, done.
 			$errorLabel.="Error: NeoNatal:  Duration must be a positive integer<br/>";
 			$_durationNatalErrorImg=$errorImg;
 		}
 		if($_durationNatal>999){
 				$errorLabel.="Error: NeoNatal: Duration must be less than 1000<br/>";
 				$_durationNatalErrorImg=$errorImg;
 		}
 		if(isset($customActivityId)){
 		  if($_volumeNatal < 0 || $_volumeNatal > 20000 || !isPosInt($_volumeNatal)){
 				$errorLabel.="Error: NeoNatal: Volume must be a positive integer between 0 and 20000 inclusive<br/>";
 				$_volumeNatalErrorImg=$errorImg;
 		  }
 		}
 		if($_methodologyNatal=="na"){
 				$errorLabel.="Error: NeoNatal: Please choose a methodology<br/>";
 				$_methodologyNatalErrorImg=$errorImg;
 		}
 	}
 }
 //debug
//     ("about to submit, check: durA:".$_durationAdult.", durP:".$_durationPediatric.", durN:".$_durationNatal.
// 	  ", volA:".$_volumeAdult.", volP:".$_volumePediatric.", volN:".$_volumeNatal);
 
 if($errorLabel==""){
//  	("want to submitsurveyanswer here: isperfadult=".$_isPerformedAdult.", hastimestadult=".$_hasTimestandardAdult.",durationadult=".$_durationAdult.",vol=".$_volumeAdult.",methodAdult=".$_methodologyAdult);
    try{
      if($is_cf==0){	
        $ownerId = getSurveyCategoryOwnerId($userFacilityId, $is_cf, $surveyCategoryId);
      }else{
      	$ownerId = '';
      }
//    if( ($userId != $ownerId)  &&  ($ownerId != ''))
//      $test=true;
//    else
//      $test=false;
    // ('debug: ownerid: '.$ownerId.', userId: '.$userId.'<br/>'.'($userId != $ownerId)  &&  ($ownerId != blank)  is : '.$test);
      if(       ($userId != $ownerId)  &&  ($ownerId != '')      ){  //ownerid is blank if this is a custom facility (anyone can answer survey q's for those).
        $em = 'It is forbidden to answer survey questions of a survey owned by someone else (for same facility in database)';
        throwMyExc($em);
      }
    }catch (Exception $e){
        $em = $e->getMessage();
        throwMyExc($em);
    }
    if(!($r = submitSurveyAnswer($userId, $_fid,$_aid,$_is_cf,$_is_ca,$_isPerformedAdult,$_isPerformedPediatric,$_isPerformedNatal,
 					 $_hasTimestandardAdult,$_hasTimestandardPediatric,$_hasTimestandardNatal,$_durationAdult,$_durationPediatric,
 					 $_durationNatal,$_volumeAdult,$_volumePediatric,$_volumeNatal,$_methodologyAdult,
 					 $_methodologyPediatric,$_methodologyNatal))){
    $errorMsg = "Error: Submission of survey data encountered an error<br/>";
    throwMyExc($errorMsg);
    }else{
	  $statusLabel = $r;   
	  // if this is a custom activity: redirect to activityCategories.  Else if this is an activity, redirect to activities.
	  if(isset($activityId)){
		//if there are any more activities undone in this activityCategory, then go directly to that next activity! 
		//  -> we set session vars with 
		// specifications of the 'next' activity, and reload same page: header(activity page).
	    // including userid,  fid, aid, is_cf, is_ca.
	    // ....   activity table: actitivitycategoryid.           surveyanswer table: userid, facilityid, activityid(not really to check here), iscustomfacil, iscustomactivity, 
	  //template:
 	    try{
 	      if(isset($activityCategoryId)){	
 	      	if(isset($activityCategoryDocId)){
 	      		$em = 'both act cat id and act cat docid were set! not allowed, it is a bug.';
 	      		throwMyExc($em);
 	      	}
 	      	//die ('act-cat-id was set!');
 	      }
 	    	
 	      if(!isset($activityCategoryId)){
 	      	//not set? then fetch it using the activityId we have.
 	      	$activityCategoryId = getActivityCategoryIdFromActivityId($activityId);
 	      	
 	      }
 	      
	      if(!isActivityCategoryComplete($userId, $_fid, $activityCategoryId, $is_cf)){  
	    	  //just the activity category is not complete, so stay within it and get another activity to do.
	    	  $_SESSION['activityId'] = getNextActivityId_FromOwnAC($userId, $_fid, $activityCategoryId, $is_cf,0); //is_ca is 0, last param.
 
	          header('Location: activity.php');
	          exit();
	      }elseif(     isActivityCategoryComplete($userId, $_fid, $activityCategoryId, $is_cf) && 
	              !isActivityCategoryGroupOtherCategoriesComplete($userId, $_fid, $activityCategoryId, $is_cf,0)){
	      	$arr = getNextActivityId_WithinACGroup($userId, $_fid, $activityCategoryId, $is_cf,0);  //0 : is custom activity.
	      	$_SESSION['activityId'] = $arr['activityId'];
	      	if(!isset($_SESSION['activityCategoryDocId'])){
	      	  $_SESSION['activityCategoryId'] = $arr['activityCategoryId'];
	      	}
	        header('Location: activity.php');
	          exit();
	      }else {
		    //else all activities in *all groups* done - so just go up to the activities page.
	       header('Location: activityCategories.php#activityCategories'); //was activities.php
	       exit();
	      }
	    }catch(Exception $e){
	    	$errorMsg='exception caught in activity: '.$e->getMessage();
	    	throwMyExc($errorMsg);
	    }
	  }
	  else if(isset($customActivityId)){
     	    //this is naturally where it returns after the customactivity is answered, as that is where they are created/edited - in activitycategories.
	  		header('Location: activityCategories.php#activityCategories');
     	    exit();
	  }
	  else{
	  	$em  = "activitysubmitprocessing page: End of successful submit : neither activityid nor customactivityid were set, so cannot auto redirect. Please contact administrator";
	  	throwMyExc($em);
	  }
	  		  
    }
 }
 //========= some initialization vars (since we have some real data here coming from above, not just blanks)====
 	$isPerformedAdult = $_isPerformedAdult;
	$isPerformedPediatric = $_isPerformedPediatric;
	$isPerformedNatal = $_isPerformedNatal;
	$hasTimestandardAdult = $_hasTimestandardAdult;
	$hasTimestandardPediatric = $_hasTimestandardPediatric;
	$hasTimestandardNatal = $_hasTimestandardNatal;
	$durationAdult = $_durationAdult;
	$durationPediatric = $_durationPediatric;
	$durationNatal = $_durationNatal;
	$volumeAdult = $_volumeAdult;
	$volumePediatric = $_volumePediatric;
	$volumeNatal = $_volumeNatal;	
	$methodologyAdult = $_methodologyAdult;
	$methodologyPediatric = $_methodologyPediatric;
	$methodologyNatal = $_methodologyNatal;