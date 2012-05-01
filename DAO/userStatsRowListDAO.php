<?php
namespace urm\urm_secure\DAO;

require_once('userStatsRowDAO.php');
use urm\urm_secure\DAO\userStatsRowDAO;

/*
 * gets the full set of surveycategory rows. 
 */
class userStatsRowListDAO{
	
	public
	  $list;
	  
	function __construct(){
		$this->list = array();
		
		$q1 = "select id, username from user where 1";
		$r1 = mysql_queryCustom($q1);
		while($row = mysql_fetch_array($r1)){
			$id = $row['id'];
			$username = $row['username'];
			//echo '----------'.$id.', '.$username;
			$r = new userStatsRowDAO($id,$username);
			$r->toRowHtml();
			$this->list[] = $r;
		}
	}
	function toRowsHtml(){
		
		$o = '';
		foreach($this->list as $r){
		   $o .= $r->toRowHtml();
		}
		//		$o .= 'size: '.count($this->list);
		return $o;
	}
	
	

}