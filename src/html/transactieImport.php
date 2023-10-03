<?php
/* 	
 		Author  						: $Author: jwellner $
 		Laatste aanpassing	: $Date: 2005/11/01 11:20:08 $
 		File Versie					: $Revision: 1.3 $
 		
 		$Log: transactieImport.php,v $
 		Revision 1.3  2005/11/01 11:20:08  jwellner
 		diverse aanpassingen
 		
 		Revision 1.2  2005/05/06 16:51:02  cvs
 		einde dag
 		
 		
*/
include_once("wwwvars.php");

session_start();
$_SESSION[NAV] = "";
session_write_close();

$content = array();
echo template($__appvar["templateContentHeader"],$content);

?>
<form action="transactiesVerwerken.php" method="POST" target="importFrame" name="controleForm">
<!-- MAX_FILE_SIZE must precede the file input field -->
<input type="hidden" name="posted" value="true" />
<input type="hidden" name="aanvullen" value="0" />
<!-- Name of input element determines name in $_FILES array -->
<b>Consistentie controle</b><br><br>
<?php
if($_error) echo "<b style=\"color:red;\">".$_error."</b>";
?>

<div class="form">
<div class="formblock">
<div class="formlinks">Importbestand </div>
<div class="formrechts">
<input type="file" name="importfile" size="50">
</div>
</div>

<div class="form">
<div class="formblock">
<div class="formlinks">Afschriftdatum &nbsp;</div>
<div class="formrechts">
<input type="text" name="datum" value="<?=date("d-m-Y")?>" size="15">
</div>
</div>

<div class="form">
<div class="formblock">
<div class="formlinks"> Depotbank: </div>
<div class="formrechts">
<br><br><br><br><br><br>
</div>
</div>

<div class="form">
<div class="formblock">
<div class="formlinks"> &nbsp;</div>
<div class="formrechts">
<input type="checkbox" name="log_error" value="1" checked> Log fouten 
</div>
</div>

<div class="form">
<div class="formblock">
<div class="formlinks"> &nbsp;</div>
<div class="formrechts">
<input type="checkbox" name="log_all" value="1"> Log alles 
</div>
</div>

<div class="formblock">
<div class="formlinks"> &nbsp;</div>
<div class="formrechts">
<input type="button" value="Verwerken" onClick="document.controleForm.submit();">
&nbsp;&nbsp;&nbsp;&nbsp;
</div>
</div>

</div>
</form>

<div class="formblock">
<div class="formlinks"> &nbsp;</div>
<div class="formrechts">
<iframe width="600" height="400" name="importFrame"></iframe>
</div>
</div>

</div>
<?
echo template($__appvar["templateRefreshFooter"],$content);
?>