<?php
namespace urm\urm_secure\DAO;


/*
 * gets the full set of surveycategory rows. 
 */
class userStatsStartedButIncompleteDAO{
	
	public
	  $list;
	  
	function __construct(){
		$this->list = array();
		
		 
	}
	function addToList($userIncompleteDataDAO){
		$this->list[] = $userIncompleteDataDAO;
	}
	function toRowsHtml(){
		$o = '';
		foreach($this->list as $r){
		   $o .= $r->toRowHtml();
		}
		//		$o .= 'size: '.count($this->list);
		return $o;
	}
	function toDebugAllText(){
		$o = '';
		foreach($this->list as $r){
		   $o .= $r->toDebugText();
		}
		//		$o .= 'size: '.count($this->list);
		return $o;
	}
	

}


/*
 * userid, username,
 */
class userIncompleteDataDAO{
	
}
 
