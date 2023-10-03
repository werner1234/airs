<?php
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2018/09/23 17:14:23 $
 		File Versie					: $Revision: 1.28 $
*/
include_once("wwwvars.php");
$__vtVars["firstCap"] = false;
// set module naam voor autenticatie leeg = iedereen.
$__appvar["module"] = "";

// haal session NAV op
session_start();
$nav	= $_SESSION['NAV'];

function _echoButton($state,$page,$button,$tip,$target)
{
  global $nav;
  echo "<td width=\"30\" align=\"center\" class=\"button\" onmouseover=\"this.className='button_hover'\" onmouseout=\"this.className='button'\">";
  if ($state)
  {
    echo '<a href="javascript:parent.frames[\''.$target.'\'].location=\''.$nav->currentScript."?".$nav->getQuerystring($page).'\';document.write(\'<div style=\\\'background-color: #F7F7F7;height:25px;text-align:center;vertical-align: middle;font-family: Arial, Helvetica, sans-serif;font-size: 12px;\\\'> </div>\');" >';
    //echo '<a href="'.$nav->currentScript."?".$nav->getQuerystring($page).'" target="content">';
    ?>
    <?=maakKnop($button,array('size'=>16,'tooltip'=>$tip))?></a>
<? //drawButton($button,"",$tip)
  }
  else
    echo maakKnop($button,array('size'=>16,'tooltip'=>vt("Niet aktief"),'disabled'=>true));//drawButton($button."_gray","","Niet aktief");
  echo "</td>";
}

function _editButton($state,$form,$button,$tip,$txt="")
{
  global $nav;
  echo "<td nowrap class=\"button\" onmouseover=\"this.className='button_hover'\" onmouseout=\"this.className='button'\">";
  if ($state)
  {
?>
    <a href="#" onClick="parent.content.document.submitForm();">
    <?=maakKnop($button,array('size'=>16,'tooltip'=>$tip,'text'=>$txt))?></a>
<? //drawButton($button,$txt,$tip)
  }
  else
    echo  maakKnop($button,array('size'=>16,'tooltip'=>vt("Niet aktief"),'disabled'=>true,'text'=>$txt));//drawButton($button."_gray",$txt,"Niet aktief");

  echo "</td>";
}

$content = array();
$content[style] = '<link href="style/navigatie.css" rel="stylesheet" type="text/css" media="screen">';

echo template($__appvar["templateContentHeader"],$content);
?>

  <table height="27" cellspacing="0" cellpadding="0" border="0" width=100%>
  <tr>
  	<td width="160" align="center"></td>

<?
if($nav == true)
{
	if ($navlist = $nav->items['navlist'])
	{
		$prevpage = $navlist->currentPage-1;
		$nextpage = $navlist->currentPage+1;
		$lastpage = ($__BTR_CONFIG["REMOVE_QUERY_LIMIT"] && !$navlist->overrideQueryLimit) ? 1 : $navlist->lastPage;
    $target=$nav->target;
    _echoButton($navlist->buttonFirst,1,"navigate_beginning.png",vt("Ga naar de eerste pagina"),$target);
    _echoButton($navlist->buttonPrev,$prevpage,"navigate_left.png",vt("Ga naar de vorige pagina"),$target);
?>
		<td width="120" align="center" valign="center" data-pages>
      <b><?=vtb('pagina %s van %s', array($navlist->currentPage, $lastpage))?><b></b>
    </td>
<?php
    _echoButton($navlist->buttonNext,$nextpage,"navigate_right.png",vt("Ga naar de volgende pagina"),$target);
		_echoButton($navlist->buttonLast,$lastpage,"navigate_end.png",vt("Ga naar de laatste pagina"),$target);

		if($navlist->buttonAdd)
		{
?>
    <td width="80" align="center" valign="center" class="button" onmouseover="this.className='button_hover'" onmouseout="this.className='button'">
    	<a href="#" onClick="parent.content.addRecord()"><?=maakKnop('add.png',array('size'=>16,'tooltip'=>vt("record toevoegen"),'text'=>vt('toevoegen')))?> </a>
    </td>
<?// drawButton("record_add","toevoegen","record toevoegen")
		}
    //_echoButton($navlist->buttonAdd,'new',"record_add","Voeg nieuw item toe");
		echo "<td width=\"150\" align=\"center\">".$navlist->totalRecords." ".vt("items in selectie")."</td>";
	}

	if ($navEdit = $nav->items['navedit'])
	{
		$form = $navEdit->form;
?>
  <td width="80" align="center" class="button">
<?
    $text=vt('opslaan');
    if ($navEdit->buttonSave)
    {
?>
      <a href="#" onClick="parent.content.submitForm()">

<?
      $button = "save";
      $tip    = vt("sla de wijzigingen op");
      $end    = "</a>";
      $disabled=false;
      if(isset($nav->extraSettings['save']['tip']))
        $tip=$nav->extraSettings['save']['tip'];
      if(isset($nav->extraSettings['save']['text']))
        $text=$nav->extraSettings['save']['text'];
    }
    else
    {
      $button = "save_gray";
      $tip    = vt("Niet aktief");
      $end    = "";
      $disabled=true;
    }

    echo maakKnop('disk_blue.png',array('size'=>16,'tooltip'=>$tip,'disabled'=>$disabled,'text'=>$text)).$end;;//drawButton($button,"opslaan",$tip)
?>
  </td>
  <td width="80" align="center" class="button" onmouseover="this.className='button_hover'" onmouseout="this.className='button'">
<?
 //alert(parent.frames['content'].fromChanged);
//return confirm ('Are you certain?');
    if ($navEdit->buttonBack)
    {
?>
<script language="JavaScript" TYPE="text/javascript">

function checkChange()
{
  if(parent.frames['content'].fromChanged)
  {
    if(confirm ('<?=vt('U verlaat het scherm. Wijzigingen worden niet opgeslagen. Weet u het zeker?!');?>'))
    {
      try{parent.kaAjax('GET', 'keepalive.php', 'delete=1', 'sessionstatus');} catch(err) { }
      <?php
      if(strpos($nav->returnUrl,'resetFrame=1')!==false)
        echo "try{parent.parent.parent.frames['content'].location = '".$nav->returnUrl."';} catch(err) { }\n";
      ?>
      parent.frames['content'].location = '<?=$nav->returnUrl?>';
    }
  }
  else
  {
    try{parent.kaAjax('GET', 'keepalive.php', 'delete=1', 'sessionstatus');} catch(err) { }
      <?php
      if(strpos($nav->returnUrl,'resetFrame=1')!==false)
        echo "try{parent.parent.parent.frames['content'].location = '".$nav->returnUrl."';} catch(err) { }\n";
      ?>
    parent.frames['content'].location = '<?=$nav->returnUrl?>';
  }
}
function checkDelete()
{
  if(confirm ('<?=vt('Record wordt verwijderd. Weet u het zeker?');?>'))
  {
    parent.content.document.<?=$form?>.action.value='delete';
    parent.content.submitForm();
  }
}
</script>
    <a href="#" onClick="javascript:checkChange();">
      <?=maakKnop('undo.png',array('size'=>16,'tooltip'=>vt('Ga terug zonder opslaan'),'text'=>vt('terug')))?></a>
<?php
    }
?>
	</td>
  <td width="80" align="center" class="button" onmouseover="this.className='button_hover'" onmouseout="this.className='button'">
<?
    if ($navEdit->buttonDelete)
    {
?>
      <a href="#" onClick="checkDelete();">

<?
      $button = "delete";
      $tip    = vt("Verwijder dit record");
      $end    = "</a>";
      $disabled =false;
    }
    else
    {
      $button  = "delete_gray";
      $tip     = vt("Niet aktief");
      $end    = "";
      $disabled = true;
    }

    //echo drawButton($button,"verwijder",$tip).$end;
     echo maakKnop('delete.png',array('size'=>16,'tooltip'=>$tip,'disabled'=>$disabled,'text'=>vt('verwijder'))).$end;
?>
  </td>

  <td width="180" align="right" class="button">
<?
    if ($navEdit->buttonSave && !isset($nav->extraSettings['opslaanNietVerlaten']['hidden']))
    {
?>
      <a href="#" onClick="parent.content.document.<?=$form?>.action.value='updateStay';parent.content.submitForm();">
<?
      $button = "save";
      $tip    = vt("Opslaan en niet verlaten");
      $end    = "</a>";
      $disabled=false;
    }
    else
    {
      $button = "save_gray";
      $tip    = vt("Niet aktief");
      $end    = "";
      $disabled=true;
    }
   // echo drawButton($button,"opslaan niet verlaten",$tip).$end;
    echo maakKnop('disk_blue.png',array('size'=>16,'tooltip'=>$tip,'disabled'=>$disabled,'text'=>vt('opslaan niet verlaten'))).$end;
?>
  </td>
<?

  if($navEdit->message)
  {
    echo '
      <td align="left" class="button">&nbsp;&nbsp;&nbsp;
       '.$navEdit->message.'
      </td>';
  }

?>
  <td>&nbsp;&nbsp;</td>
<?
	}

	if ($navSearch = $nav->items['navsearch'])
	{
?>
    <form action="<?=$nav->currentScript?>" method="get" name="searchForm" target="content">
<?
// maak input boxen voor queryString
	parse_str($nav->getQueryString(1),$values);
	foreach ( $values as $key=>$val )
	{
		if($key != "selectie")
		{
			if(is_array($values[$key]))
			{
				for($a=0;$a < count($values[$key]); $a++)
				{
					echo "<input type=\"hidden\" name=\"".$key."[]\" value=\"".$values[$key][$a]."\">\n";
				}
			}
			else
				echo "<input type=\"hidden\" name=\"".$key."\" value=\"".$values[$key]."\">\n";
		}
	}
?>
    <td width="120"><input type="text" name="selectie" value="<?=$navSearch->selection?>"> </td>
    <td width="70"
        align="center"
        valign="center"
        class="button"
        onmouseover="this.className='button_hover'"
        onmouseout="this.className='button'">
      <a href="#" onClick="document.searchForm.submit();"><?=maakKnop("view.png",array('size'=>16,'tooltip'=>vt("zoeken")))." ".vt("zoeken")?></a>
    </td>
    </form>

		<Script type="text/javascript">
  		document.searchForm.selectie.select();
			document.searchForm.selectie.focus();
		</script>

<?
	}
}

?>
		<td>&nbsp;</td>
	</tr>
	</table>
<?
// print templateFooter (met default vars)
echo template($__appvar["templateContentFooter"],$content);
unset($__vtVars["firstCap"]);
?>