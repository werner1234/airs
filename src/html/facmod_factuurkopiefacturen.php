<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2019/07/22 09:11:22 $
 		File Versie					: $Revision: 1.1 $

 		$Log: facmod_factuurkopiefacturen.php,v $
 		Revision 1.1  2019/07/22 09:11:22  cvs
 		call 7675
 		
 		Revision 1.4  2007/01/06 12:26:08  cvs
 		*** empty log message ***
 		
 		Revision 1.3  2007/01/06 11:30:05  cvs
 		*** empty log message ***

 		Revision 1.2  2006/01/09 14:05:27  cvs
 		*** empty log message ***

 		Revision 1.1  2005/12/06 11:14:31  cvs
 		*** empty log message ***

 		Revision 1.1  2005/11/30 08:50:20  cvs
 		*** empty log message ***

 		Revision 1.1  2005/11/28 07:31:48  cvs
 		*** empty log message ***



*/

include_once("wwwvars.php");
session_start();
if (!facmodAccess())
{
  return false;
}

$cfg = new AE_config();

$_SESSION[NAV]     = "";
$_SESSION[submenu] = "";




//$__appvar["basedir"] =  realpath(dirname(__FILE__)."/..");


echo template($__appvar["templateContentHeader"],$editcontent);

$DB = new DB();

$DB->SQL("SELECT facnr FROM mFinancieel_factuurbeheer ORDER BY facnr DESC");
$facRec = $DB->lookupRecord();



?>


<style>
.formlinks{
width: 150px;
}
</style>

<form action="mfinancieel_factuurAfdrukkenPDF.php" method="GET" name="editForm">
<input type="hidden" name="action" value="copyRun">
<br>
&nbsp;&nbsp;<b>kopie facturen maken,</b>
geef de interval van de te printen facturen.
<br>
<br>

<div class="formblock">
	<div class="formlinks">eerste factuur
	</div>
	<div class="formrechts">
		<input type="text" name="startInterval" value="<?=$facRec[facnr]?>" size="15">
	</div>
</div>

<div class="formblock">
	<div class="formlinks">laatste factuur
	</div>
	<div class="formrechts">
    <input type="text" name="stopInterval" value="<?=$facRec[facnr]?>" size="15">
	</div>
</div>
<br>
<br>

<div class="formblock">
	<div class="formlinks">kopie tekst
	</div>
	<div class="formrechts">
    <input type="text" name="kopietxt" value="<?=$cfg->getData("kopietxt");?>" size="25">
	</div>
</div>


<div class="formblock">
	<div class="formlinks">&nbsp;
	</div>
	<div class="formrechts">
		<input name="submit" type="submit" value="start aktie">
	</div>
</div>

</form>
<?

// print templateFooter (met default vars)
echo template($__appvar["templateRefreshFooter"],$content);

?>