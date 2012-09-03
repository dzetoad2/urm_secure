<?php 
//set_include_path(get_include_path().":/usr/local/www/apache22/data/u rm/");  // this for the freebsd system only
 
//define('loginOverride','1');


if(  (isset($_SESSION['username]']))    && $_SESSION['username'] == "dzetoad2@gmail.com"){
  define('debugFillOutAction',1);    
}

define('debugFillOutAction',1);




//define('DEBUG',1);  
  //define('debugActivityCategories',1);
  //define('debugSurveyCategories',1);  //this lets you try to answer activities owned by another user (in same facility).
//define('appRootDirectory','.');

define('endingYear','2012');
define('endingMonth','6');
define('endingDay','22'); //22 is normal



class completionText {

public static $completionMessage = <<<EOF
<html> 
  <body > 
  Thank you for completing the URM Hospital Inpatient survey.  We also need time standards for the following:<br/>
   <ul>
    <li>pulmonary function labs</li>
    <li>blood gas labs</li>
    <li>echo/non-invasive cardiology labs</li>
    <li>sleep labs</li>
    <li>wound care/hyperbaric medicine (first time!)</li>
    <li>pulmonary rehabilitation services (first time!). </li>
   </ul>
   
   <br/><br/>

<b>If you manage these services</b> please go to <a href="http://www.aarc.org/urm_survey/">www.aarc.org/urm_survey/</a> to continue 
with other appropriate surveys. Each additional survey completed for your facility provides a deeper discount 
on the cost of the manual.<br/>

<br/>

<b>If you don’t manage these services</b>, Please help us by reaching out to the manager/director responsible of
 any of these services and recruiting them to complete survey(s) relevant to their area(s) of responsibility. 
 Please direct them to <a href="http://www.aarc.org/urm_survey/">www.aarc.org/urm_survey</a>
 where they can obtain survey instructions and using your login
 and password, access the surveys for your facility. <br/>

<br/>
  
To assure that the reporting of these other surveys is credited to your facility, the person reporting 
will need to access the surveys using the access information you created. This is because the database design 
allows only one contact per facility to assure that all participation discounts are credited to your facility. 
The licensing agreement allows use of the URM by any department in your facility.<br/>

<br/>

Thank you for encouraging other managers to participate in this revision of the URM. 
They can contact me with any questions at <a href="mailto:dubbs@aarc.org?Subject=URM%20Question">dubbs@aarc.org</a>. 
Remember the survey is available through May 31, 2012.<br/>
   
      
  </body> 
</html> 
EOF;

}//class end



?>
