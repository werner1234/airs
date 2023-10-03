<?php
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2017/01/04 13:22:57 $
 		File Versie					: $Revision: 1.1 $

 		$Log: mainmenu_toegangsControle.php,v $
 		Revision 1.1  2017/01/04 13:22:57  cvs
 		call 5542, uitrol WWB en TGC
 		
 				
  
 * 
 */

  $mnu->addItem("toegangsControle","IP adreslijst","url=tgc_ipaccesslistList.php");
  $mnu->addItem("toegangsControle","blacklist","url=tgc_blacklistList.php");
  $mnu->addItem("toegangsControle","logboek","url=tgc_logList.php");
  $mnu->addItem("toegangsControle","");
  $mnu->addItem("toegangsControle","blacklist instellingen","url=tgc_blacklistSetup.php");


  
?>