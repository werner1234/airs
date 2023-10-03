<?php
include_once("wwwvars.php");

if(isset($_GET['koppelObject']))
  $koppelObject=$_GET['koppelObject'];

if (empty($koppelObject))
{
	fout("Geen koppelobject!");
	exit;
}


$koppelObject 	= stripslashes(urldecode($koppelObject));
$koppelObject 	= unserialize($koppelObject);
$encodedObject  = urlencode(serialize($koppelObject));

$html_page_title     = $koppelObject->description;
$html_recordfirst    = makeButton("record_first",true);
$html_recordback     = makeButton("record_back",true);
$html_recordnext     = makeButton("record_next",true);
$html_recordlast     = makeButton("record_last",true);
$html_recordadd      = makeButton("record_add",true);
$html_recordsearch   = makeButton("record_search",true);
$html_image_edit     = makeButton("edit");
$html_close_window   = makeButton("close",true);

$html_edit_height    = "400";
$html_edit_width     = "500";

$html_header       = "templates/content_kop.inc";
$html_footer       = "templates/content_voet.inc";
$filler              = makeButton("filler");

$allowAdd = false;

$pp = 10;


for($a=0;$a < count($koppelObject->field);$a++)
{
	if ($koppelObject->search[$a])
	{
	  if($koppelObject->join == '')
		  $searchArray[] = $koppelObject->table.".".$koppelObject->field[$a]." LIKE '%".$search."%'";
		else
		   $searchArray[] = $koppelObject->field[$a]." LIKE '%".$search."%'";
	}
	if($koppelObject->display[$a])
	{
		$cols++;
	}
}

$sel = " WHERE (".implode(" OR ",$searchArray).") ".$koppelObject->extraQuery;

// pagina browsen
if($koppelObject->join == '')
  $query 	= "SELECT COUNT(".$koppelObject->table.".".$koppelObject->field[0].") FROM ".$koppelObject->table." ".$koppelObject->join." ".$sel;
else
  $query 	= "SELECT COUNT(".$koppelObject->field[0].") FROM ".$koppelObject->table." ".$koppelObject->join." ".$sel;
$db=new DB();
$db->SQL($query);
$data  	= $db->LookupRecord('num');
$totaal = $data[0];

$page=$_GET['page'];

if (empty($page)) {
	$page = 0;
}
else {
	$page = $page;
}
if($page == 0) {
	$prevpage = 0;
}
else
	$prevpage = $page-1;

$selstart = $page * $pp;
$lastpage = ceil(($totaal / $pp));
if($lastpage > 0) $lastpage--;

$fields = implode(",",$koppelObject->field);
$query  = "SELECT ".$fields." FROM ".$koppelObject->table." ".$koppelObject->join." ".$sel." LIMIT ".$selstart.",".$pp;

$db->SQL($query);
$db->Query();

$vq = "&koppelObject=".$encodedObject."&search=".urlencode($search);
if ($page!=0)
  $navigatie_first = "<a href=\"$PHP_SELF?page=0$vq\" class=\"icon\">$html_recordfirst</a>";
else
  $navigatie_first   = $filler;
if ($page!=$lastpage)
  $navigatie_last  = "<a href=\"$PHP_SELF?page=".($lastpage)."$vq\" class=\"icon\">$html_recordlast</a>";
else
  $navigatie_last   = $filler;
if ($page>0)
  $navigatie_back  = "<a href=\"$PHP_SELF?page=".($prevpage)."$vq\" class=\"icon\">$html_recordback</a>";
else
  $navigatie_back   = $filler;
if ($page<$lastpage)
  $navigatie_next  = "<a href=\"$PHP_SELF?page=".($page+1)."$vq\" class=\"icon\">$html_recordnext</a>";
else
  $navigatie_next   = $filler;


$navigatie_close   = "<a href=\"javascript:parent.closeit();\" class=\"button\">$html_close_window</a>";

$navigatie = "$navigatie_first $navigatie_back $navigatie_add $navigatie_next $navigatie_last $navigatie_close ";

for($a=0;$a < count($koppelObject->form); $a++)
{
	if (!empty($koppelObject->form[$a]))
	{
		$formFields[] = $koppelObject->form[$a];
		$_tmp .= "  parent.document.".$koppelObject->formName.".".$koppelObject->form[$a].".value=".$koppelObject->form[$a].";\n";
		//$focusveld=$koppelObject->formName.".".$koppelObject->form[$a];
	}
}
$formFields = implode(",",$formFields);
$_template[javascript] .= "function itemSelect(".$formFields.") {\n";
$_template[javascript] .= $_tmp;
if(!empty($koppelObject->action))
	$_template[javascript] .= " parent.".$koppelObject->action.";\n";
if(!empty($koppelObject->focus))
	$_template[javascript] .= " parent.document.".$koppelObject->formName.".".$koppelObject->focus.".focus();\n";
//$_template[javascript] .= "	parent.document.$focusveld.focus();\n";
$_template[javascript] .= " parent.closeit();\n}";

$_template[title] = $html_page_title;
$_template[topmenu] = "";
$_template[navigatie] = "";
echo Template("$html_header",$_template);

$inforegel =  "Er zijn $totaal items gevonden. Pagina ".($page+1)." van ".($lastpage+1)."<br><br>";
?>
<table border="0" width="100%" height="100%" cellspacing="1" cellpadding="1" class="selectListTable" align="center" bgcolor="#F2F2F2">
<!--
  <tr>
    <td class="listTableInfoData" colspan="<?=$cols?>" align="center">
      <?=$navigatie?>
    </td>
  </tr>
-->
  <tr>
    <td class="listTableInfoData" colspan="<?=$cols?>" align="center">

			<form method="get" action="<?=$PHP_SELF?>" name="searchForm">
			  <input type="text" name="search" size="30" value="<?=stripslashes($search)?>">
			  <input type="hidden" name="koppelObject" size="30" value="<?=$encodedObject?>">
			  <a href="javascript:document.searchForm.submit();" class="icon"><?=$html_recordsearch?></a>
			</form>

      <?=$inforegel?>
    </td>
  </tr>
<tr>
<?php
for($a=0; $a < count($koppelObject->field);$a++)
{
	if($koppelObject->display[$a] == true)
	{
?>
		<td>
			<b><?=$koppelObject->field[$a]?></b>
		</td>
<?php
	}
}
?>
</tr>
<?php
while( $data = $db->NextRecord('both') )
{


	$selectFields = array();
	for($a=0; $a < count($koppelObject->field);$a++)
	{

		  if(strpos($koppelObject->field[$a],'.'))
		  {
		    $tmp=explode(".",$koppelObject->field[$a]);
		    $lookup=$tmp[1];
		  }
		  else
		    $lookup=$koppelObject->field[$a];

		if (!empty($koppelObject->form[$a]))
			$selectie = addslashes(($data[$lookup]));//htmlspecialchars
			$selectie = str_replace("\r","",$selectie);
			$selectie = str_replace("\n","\\n",$selectie);
			$selectFields[] = "'".$selectie."'";
	}
	$select = implode(",",$selectFields);
?>
	<tr class="list_dataregel" onmouseover="this.className='list_dataregel_hover'" onmouseout="this.className='list_dataregel'" onClick="javascript:itemSelect(<?=$select?>);">
<?php
	for($a=0; $a < count($koppelObject->field);$a++)
	{
		if($koppelObject->display[$a] == true)
		{


		  if(strpos($koppelObject->field[$a],'.'))
		  {
		    $tmp=explode(".",$koppelObject->field[$a]);
		    $lookup=$tmp[1];
		  }
		  else
		    $lookup=$koppelObject->field[$a];
?>
			<td><a href="javascript:itemSelect(<?=$select?>);"><?=$data[$lookup]?></a></td>
<?php
		}
	}
?>
	</tr>
<?php
}
?>
  <tr>
    <td class="listTableInfoData" colspan="<?=$cols?>" align="center">
      <?=$navigatie?>
    </td>
  </tr>
</table>
<script language="JavaScript" type="text/javascript">
  document.searchForm.search.focus();
 <?
 if($totaal==1)
 {
  echo "itemSelect($select);parent.closeit();";
 }
 ?>
</script>
</body>
</html>