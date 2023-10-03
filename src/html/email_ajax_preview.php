<?php
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2016/04/22 10:11:06 $
 		File Versie					: $Revision: 1.1 $

 		$Log: email_ajax_preview.php,v $
 		Revision 1.1  2016/04/22 10:11:06  cvs
 		call 4296 naar ANO
 		
 		Revision 1.2  2016/01/30 16:43:44  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2016/01/30 16:21:38  rvv
 		*** empty log message ***
 		
 		
*/


include("wwwvars.php");

$id = substr($_POST["id"],4);

$db = new DB();
$query = "SELECT * FROM `_mailbox` where `index` = '".$id."' AND `add_user` = '".$USR."'";
$rec = $db->lookupRecordByQuery($query);
?>
<div class="mailHead">
  <span class="headField">afzender:</span> <?=$rec["from"]?><br/>
  <span class="headField">onderwerp:</span> <?=$rec["subject"]?><br/>
</div>
<pre>
   <?=$rec["body"]?>
</pre>

