<?php
include_once("wwwvars.php");

session_start();
$_SESSION[submenu] = "";
$_SESSION[NAV] = "";
session_write_close();


$content = array();
echo template($__appvar["templateContentHeader"],$content);

?>
<b><?=$PRG_NAME?> <?= vt('Database informatie'); ?></b><br><br>


<div class="formblock">
	<div class="formlinks"><?= vt('Database server adres'); ?>
	</div>
	<div class="formrechts">
		<?=$_DB_resources[1]['server']?>
	</div>
</div>

<div class="formblock">
	<div class="formlinks"><?= vt('Update server adres'); ?>
	</div>
	<div class="formrechts">
		<?=$_DB_resources[2]['server']?>
	</div>
</div>

<div class="formblock">
	<div class="formlinks"><?= vt('Update server login naam'); ?>
	</div>
	<div class="formrechts">
		<?=$_DB_resources[2]['user']?>
	</div>
</div>

<?
  $db = new DB();
  $db->SQL("SHOW tables");
  $db->Query();
  while ($data = $db->nextRecord("num"))
  {
    $dbArray[] = $data[0];
  }
  sort($dbArray);
?>

<div class="formblock">
	<div class="formlinks"><?= vt('Aangemelde tabellen'); ?>
	</div>
	<div class="formrechts">
	<table border="0" cellpadding="4">
	<tr bgcolor="#CCCCCC">
	<?
	  $k=1;
	  
	  while ($data = Next($dbArray))
	  {
	    if ($k > 3)
	    {
	     if ($col == "#EEEEEE")
	       $col = "#CCCCCC";
	     else   
	       $col = "#EEEEEE";
	     echo "</tr><tr bgcolor=\"$col\">" ;
	     $k=1;
	    }
	    echo "<td align=\"left\">".$data."</td>";
	    $k++;
	  }
?>
	</tr>
	</table>
		
	</div>
</div>
<?
// print templateFooter (met default vars)
echo template($__appvar["templateRefreshFooter"],$content);
?>