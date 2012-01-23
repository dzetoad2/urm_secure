<!--form declaration (<form... line) must be defined before this script component is included. -->
<!-- <input class="button" type="submit" id="submitSurveyButton" name="submitSurveyButton" value="Submit survey answer" />-->
 

<!-- type="submit"-->
 
 <input type="hidden" id="hiddenSubmitId" name="hiddenSubmitName" value="abc"/>
<h4>Please input the following:</h4>


<div id="populationBoxes">
<!-- ======================= ADULTS ================================-->
<?php if(   (isset($activityId) && $activityData['isForAdult'] == "yes")  ||  isset($customActivityId)  ){?>

<fieldset class="inputBox clearer">
 <legend><span>For Adults:</span></legend>
 <div class="radio" id="doYouPerformAdult">
  <fieldset>
   <legend><span>Do you perform this procedure?</span></legend>
	<input type="radio" name="isPerformedAdult" id="isPerformedAdultYes" value="yes"   <?php if($afDao->isPerformedAdult=="yes") echo 'checked="checked"';  ?>  /><label for="isPerformedAdultYes">Yes</label>
	<input type="radio" name="isPerformedAdult" id="isPerformedAdultNo" value="no" <?php if($afDao->isPerformedAdult=="no") echo 'checked="checked"';   ?> /><label for="isPerformedAdultNo">No</label>
	<?php  echo $afDao->isPerformedAdultErrorImg; ?>
  </fieldset>
 </div>
  
 <div class="radio startHidden" id="timeStandardAdult">
  <fieldset>
   <legend><span>Do you have a time standard for this?</span></legend>
	<input type="radio" name="hasTimestandardAdult" id="hasTimestandardAdultYes" value="yes"   <?php if($afDao->hasTimestandardAdult=="yes") echo 'checked="checked"';  ?> /><label for="hasTimestandardAdultYes">Yes</label> 
	<input type="radio" name="hasTimestandardAdult" id="hasTimestandardAdultNo" value="no"    <?php if($afDao->hasTimestandardAdult=="no") echo 'checked="checked"';   ?> /><label for="hasTimestandardAdultNo">No</label>
    <?php  echo $afDao->hasTimestandardAdultErrorImg; ?>
  </fieldset>
 </div>
 <div class="textInput startHidden" id="durVolAdult">
  <fieldset>
    
   <ol>
    <li>
     <label>Duration (mins):</label><br/>
     <input type="text" class="smallfield" id="durationAdult" name="durationAdult" value="<?php if($afDao->durationAdult!=-1) echo $afDao->durationAdult;?>"/>
     <?php echo $afDao->durationAdultErrorImg;?>
    </li>
    <?php if(isset($customActivityId)){?>
    <li>
     <label>Volume (estimated annual):</label><br/>
     <input type="text" class="smallfield" id="volumeAdult" name="volumeAdult" value="<?php if($afDao->volumeAdult!=-1) echo $afDao->volumeAdult;?>"/>
     <?php echo $afDao->volumeAdultErrorImg;?>  
    </li>
    <?php }else{ 
    	throwMyExc('custom activity id not set??????');
    }
    ?>
    
   </ol>
   
  </fieldset>
 </div>
 <div class="radio startHidden" id="methodologyAdult">
  <fieldset>
   <legend><span>Methodology:</span></legend>
    <input type="radio" name="methodologyAdult" id="methodologyAdultMeasured" value="measured" <?php if(isset($afDao->methodologyAdult) && $afDao->methodologyAdult=="measured") echo 'checked="checked"';?> /><label for="methodologyAdultMeasured">Measured</label>
	<input type="radio" name="methodologyAdult" id="methodologyAdultExpert_Opinion" value="expert_opinion" <?php if(isset($afDao->methodologyAdult) && $afDao->methodologyAdult=="expert_opinion") echo 'checked="checked"';?> /><label for="methodologyAdultExpert_Opinion">Expert Opinion</label>
	<input type="radio" name="methodologyAdult" id="methodologyAdultUnknown" value="unknown" <?php if(isset($afDao->methodologyAdult) && $afDao->methodologyAdult=="unknown") echo 'checked="checked"';?> /><label for="methodologyAdultUnknown">Unknown</label>
	<?php echo $afDao->methodologyAdultErrorImg;?>
  </fieldset>
 </div>
</fieldset>
<?php } ?>


<br/>


<?php if(  (isset($activityId) && $activityData['isForPediatric'] == "yes")  ||  isset($customActivityId)  ){?>
<fieldset class="inputBox">
 <legend><span>For Pediatrics</span></legend>
  <div class="radio" id="doYouPerformPediatric">
   <fieldset> 
	<legend><span>Do you perform this procedure?</span></legend>
	<input type="radio" name="isPerformedPediatric" id="isPerformedPediatricYes" value="yes" <?php if($afDao->isPerformedPediatric=="yes") echo 'checked="checked"'; ?> /><label for="isPerformedPediatricYes" >Yes</label> 
	<input type="radio" name="isPerformedPediatric" id="isPerformedPediatricNo" value="no" <?php if($afDao->isPerformedPediatric=="no") echo 'checked="checked"';   ?> /><label for="isPerformedPediatricNo">No</label>
	<?php  echo $afDao->isPerformedPediatricErrorImg;?>
   </fieldset>
  </div>
  <div class="radio startHidden" id="timeStandardPediatric">
   <fieldset>
	<legend><span>Do you have a time standard for this?</span></legend>
	<input type="radio" name="hasTimestandardPediatric" id="hasTimestandardPediatricYes" value="yes"   <?php if($afDao->hasTimestandardPediatric=="yes") echo 'checked="checked"';  ?> />  <label for="hasTimestandardPediatricYes" >Yes</label>
	<input type="radio" name="hasTimestandardPediatric" id="hasTimestandardPediatricNo" value="no"    <?php if($afDao->hasTimestandardPediatric=="no") echo 'checked="checked"';   ?> />  <label for="hasTimestandardPediatricNo" >No</label>
    <?php echo $afDao->hasTimestandardPediatricErrorImg; ?> 
   </fieldset>
  </div>
  <div class="textInput startHidden" id="durVolPediatric">
   <fieldset>
    <ol>
     <li>
	  <label>Duration (mins):</label>  <br/>  
      <input type="text" class="smallfield" id="durationPediatric" name="durationPediatric" value="<?php if($afDao->durationPediatric!=-1) echo $afDao->durationPediatric;?>"/>
      <?php echo $afDao->durationPediatricErrorImg;?>
     </li>
     <?php if(isset($customActivityId)){?>
     <li>
      <label>Volume (estimated annual):</label><br/>
      <input type="text" class="smallfield" id="volumePediatric" name="volumePediatric" value="<?php if($afDao->volumePediatric!=-1) echo $afDao->volumePediatric;?>"/>
      <?php echo $afDao->volumePediatricErrorImg;?> 
     </li>
     <?php }?>
    </ol>
	</fieldset>
  </div>
  <div class="radio startHidden" id="methodologyPediatric">
   <fieldset  > 
    <legend><span>Methodology:</span></legend>
	<input type="radio" name="methodologyPediatric" id="methodologyPediatricMeasured" value="measured" <?php if($afDao->methodologyPediatric=="measured") echo 'checked="checked"';?> /><label for="methodologyPediatricMeasured">Measured</label>
	<input type="radio" name="methodologyPediatric" id="methodologyPediatricExpert_Opinion" value="expert_opinion" <?php if($afDao->methodologyPediatric=="expert_opinion") echo 'checked="checked"';?> /><label for="methodologyPediatricExpert_Opinion">Expert Opinion</label>
	<input type="radio" name="methodologyPediatric" id="methodologyPediatricUnknown" value="unknown" <?php  if($afDao->methodologyPediatric=="unknown") echo 'checked="checked"';?> /><label for="methodologyPediatricUnknown">Unknown</label>
	<?php echo $afDao->methodologyPediatricErrorImg;?>
   </fieldset>
  </div>
</fieldset>
<?php }?>

<br/>

<?php if( (isset($activityId) && $activityData['isForNatal'] == "yes")  ||  isset($customActivityId)){?>
<fieldset class="inputBox">
 <legend><span>For Neonatal:</span></legend>
  <div class="radio" id="doYouPerformNatal">
	<fieldset>
	 <legend><span>Do you perform this procedure?</span></legend>
	  <input type="radio" name="isPerformedNatal" id="isPerformedNatalYes" value="yes"   <?php if($afDao->isPerformedNatal=="yes") echo 'checked="checked"';  ?> /><label for="isPerformedNatalYes">Yes</label>
	  <input type="radio" name="isPerformedNatal" id="isPerformedNatalNo"  value="no" <?php if($afDao->isPerformedNatal=="no") echo 'checked="checked"';    ?> /><label for="isPerformedNatalNo">No</label>
	  <?php echo $afDao->isPerformedNatalErrorImg; ?>
	</fieldset>
  </div>
  <div class="radio startHidden" id="timeStandardNatal">
    <fieldset>
     <legend><span>Do you have a time standard for this?</span></legend>
	  <input type="radio" name="hasTimestandardNatal" id="hasTimestandardNatal1" value="yes"   <?php if($afDao->hasTimestandardNatal=="yes") echo 'checked="checked"';  ?> /><label for="hasTimestandardNatal1">Yes</label>
	  <input type="radio" name="hasTimestandardNatal" id="hasTimestandardNatal2" value="no"    <?php if($afDao->hasTimestandardNatal=="no") echo 'checked="checked"';   ?> /><label for="hasTimestandardNatal2">No</label>
     <?php  echo $afDao->hasTimestandardNatalErrorImg;?> 
    </fieldset>
  </div>
  <div class="textInput startHidden" id="durVolNatal">
   <fieldset>
   <ol>
    <li>
     <label>Duration (mins):</label> <br/>
     <input type="text" class="smallfield" id="durationNatal" name="durationNatal" value="<?php if($afDao->durationNatal!=-1) echo $afDao->durationNatal;?>"/>
     <?php echo $afDao->durationNatalErrorImg;?>
    </li>
    <?php if(isset($customActivityId)){?>
     <li>
      <label>Volume (estimated annual):</label><br/>
	  <input type="text" class="smallfield" id="volumeNatal" name="volumeNatal" value="<?php if($afDao->volumeNatal!=-1) echo $afDao->volumeNatal;?>"/>
	  <?php echo $afDao->volumeNatalErrorImg;?>
	 </li>
    <?php }?>
   </ol>
   </fieldset>
  </div>
  <div class="radio startHidden" id="methodologyNatal">
   <fieldset>
    <legend><span>Methodology:</span></legend>
	 <input type="radio" name="methodologyNatal" id="methodologyNatal1" value="measured" <?php if($afDao->methodologyNatal=="measured") echo 'checked="checked"';?> /><label for="methodologyNatal1">Measured</label>
	 <input type="radio" name="methodologyNatal" id="methodologyNatal2" value="expert_opinion" <?php if($afDao->methodologyNatal=="expert_opinion") echo 'checked="checked"';?> /><label for="methodologyNatal2">Expert Opinion</label>
	 <input type="radio" name="methodologyNatal" id="methodologyNatal3" value="unknown" <?php  if($afDao->methodologyNatal=="unknown") echo 'checked="checked"';?> /><label for="methodologyNatal3">Unknown</label>
	 <?php echo $afDao->methodologyNatalErrorImg;?>
   </fieldset>
  </div>
</fieldset>
<?php }?>

</div>

<!--<input class="floater button" type="submit" id="submitSurveyButton" name="submitSurveyButton2" value="Submit survey answer" />-->



 




<!--<input class="clearer button" type="submit" id="skipButton2" name="skipButton2" value="Don't perform - Skip this item" />-->
<!--fid:    either userFacilityId, or else customFacilityId.-->




<?php if(defined('DEBUG')){?>
<hr/>
Debug info:<br/>
fid:<input type="text" id="fid" name="fid" value="<?php echo $fid;?>"/>
<!--aid: activityID or else customActivityId.-->
aid<input type="text" id="aid" name="aid" value="<?php echo $aid;?>"/>
<!--is_cf: is custom facility? is_ca is customactivity?  both are bools-->
is_cf:<input type="text" id="is_cf" name="is_cf" value="<?php echo $is_cf;?>"/>
is_ca<input type="text" id="is_ca" name="is_ca" value="<?php echo $is_ca;?>"/>
<?php }else{?>
<input type="hidden" id="fid" name="fid" value="<?php echo $fid;?>"/>
<!--aid: activityID or else customActivityId.-->

<!--is_cf: is custom facility? is_ca is customactivity?  both are bools-->
<input type="hidden" id="is_cf" name="is_cf" value="<?php echo $is_cf;?>"/>
<input type="hidden" id="is_ca" name="is_ca" value="<?php echo $is_ca;?>"/>
<?php }?>






<?php 


// <input type="hidden" id="aid" name="aid" value="< ?  php echo $aid;? >  "/>

?>