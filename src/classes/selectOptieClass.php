<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2020/05/20 17:07:40 $
 		File Versie					: $Revision: 1.47 $
*/

class selectOptie
{
  var $labelClass = 'col-3 col-md-3';
  var $buttonClass = 'col-9 col-md-8';
  var $einddatumFilterVerwijderen = false;

  function selectOptie($PHPSELF = '',$einddatumFilterVerwijderen=false)
  {
    //$this->DB = new DB();
    global $USR;
    $this->PHP_SELF = $PHPSELF;
    $this->AETemplate = new AE_template();
    $this->einddatumFilterVerwijderen = $einddatumFilterVerwijderen;

    if($USR=='')
	  $USR=$_SESSION['USR'];
 
    $extraJoin=array('Portefeuille'=>'','PortefeuilleClusters'=>'');
    $extraWhere=array('Portefeuille'=>'AND Portefeuilles.Portefeuille NOT IN(SELECT ModelPortefeuilles.Portefeuille FROM ModelPortefeuilles WHERE ModelPortefeuilles.Fixed=1) ','PortefeuilleClusters'=>'');
    if(!checkAccess('portefeuille'))
    {
       if(isset($_SESSION['usersession']['gebruiker']['internePortefeuilles']) && $_SESSION['usersession']['gebruiker']['internePortefeuilles'] == '1')
         $internDepotToegang="OR Portefeuilles.interndepot=1";
       else
         $internDepotToegang='';  

       if($_SESSION['usersession']['gebruiker']['Accountmanager'] <> '' && $_SESSION['usersession']['gebruiker']['overigePortefeuilles'] == 0)
	     {
	       $extraWhere['Portefeuille']  .= " AND(Portefeuilles.Accountmanager='".$_SESSION['usersession']['gebruiker']['Accountmanager']."' OR Portefeuilles.tweedeAanspreekpunt ='".$_SESSION['usersession']['gebruiker']['Accountmanager']."' $internDepotToegang) ";
	     }
	     else
	     {
         $extraJoin['Portefeuille'] .= "INNER JOIN VermogensbeheerdersPerGebruiker ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND  VermogensbeheerdersPerGebruiker.Gebruiker = '".$USR."' JOIN Gebruikers ON Gebruikers.Gebruiker = VermogensbeheerdersPerGebruiker.Gebruiker ";
         $extraJoin['PortefeuilleClusters'] .= "INNER JOIN VermogensbeheerdersPerGebruiker ON portefeuilleClusters.vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND  VermogensbeheerdersPerGebruiker.Gebruiker = '".$USR."' JOIN Gebruikers ON Gebruikers.Gebruiker = VermogensbeheerdersPerGebruiker.Gebruiker ";
         $extraWhere['Portefeuille']  .=" AND (Portefeuilles.beperktToegankelijk = '0' OR  Gebruikers.beperkingOpheffen = '1' )";
	     }
    }

    if(isset($_GET['portefeuilleIntern']))
      $_SESSION['portefeuilleIntern'] = $_GET['portefeuilleIntern'];

    if(isset($_GET['metConsolidatie']))
      $_SESSION['metConsolidatie'] = $_GET['metConsolidatie'];

    if(isset($_GET['selectieMethode']))
	    $_SESSION['selectieMethode'] = $_GET['selectieMethode'];

	  if(isset($_SESSION['portefeuilleIntern']))
	  {
      if($_SESSION['portefeuilleIntern']=='0')
	       $extraWhere['Portefeuille']  .= " AND Portefeuilles.interndepot=0 ";
      elseif($_SESSION['portefeuilleIntern'] == "1")
         $extraWhere['Portefeuille']  .= " AND Portefeuilles.interndepot=1 ";
	  }

    if(isset($_SESSION['metConsolidatie']))
    {
      if($_SESSION['metConsolidatie']=='0')
        $extraWhere['Portefeuille']  .= " AND Portefeuilles.consolidatie=0 ";
      elseif($_SESSION['metConsolidatie'] == "1")
        $extraWhere['Portefeuille']  .= " AND Portefeuilles.consolidatie=1 ";
    }

    if($einddatumFilterVerwijderen==true)
    {
      $extraWhere['PortefeuilleEindDatum'] = ' 1 ';
    }
    else
    {
      $extraWhere['PortefeuilleEindDatum'] = ' Portefeuilles.Einddatum  >=  NOW() ';
    }

    $this->queries['Vermogensbeheerder']="SELECT DISTINCT(Portefeuilles.Vermogensbeheerder) FROM Vermogensbeheerders
JOIN Portefeuilles ON Vermogensbeheerders.Vermogensbeheerder=Portefeuilles.Vermogensbeheerder ".$extraJoin['Portefeuille']." WHERE ".$extraWhere['PortefeuilleEindDatum']." AND Vermogensbeheerders.Einddatum  >=  NOW() ".$extraWhere['Portefeuille']." ORDER BY Portefeuilles.Vermogensbeheerder";
    $this->queries['Accountmanager']="SELECT DISTINCT(Portefeuilles.Accountmanager) as Accountmanager FROM Portefeuilles ".$extraJoin['Portefeuille']." WHERE 1 ".$extraWhere['Portefeuille']." ORDER BY Accountmanager";
    $this->queries['TweedeAanspreekpunt']="SELECT DISTINCT(tweedeAanspreekpunt) FROM Portefeuilles ".$extraJoin['Portefeuille']." WHERE ".$extraWhere['PortefeuilleEindDatum']." ".$extraWhere['Portefeuille']." ORDER BY tweedeAanspreekpunt";
    $this->queries['Client']="SELECT DISTINCT(Client) FROM Portefeuilles ".$extraJoin['Portefeuille']." WHERE ".$extraWhere['PortefeuilleEindDatum']." ".$extraWhere['Portefeuille']." ORDER BY Client";
    $this->queries['selectieveld1']="SELECT DISTINCT(selectieveld1) FROM Portefeuilles ".$extraJoin['Portefeuille']." WHERE ".$extraWhere['PortefeuilleEindDatum']." ".$extraWhere['Portefeuille']." ORDER BY selectieveld1";
    $this->queries['selectieveld2']="SELECT DISTINCT(selectieveld2) FROM Portefeuilles ".$extraJoin['Portefeuille']." WHERE ".$extraWhere['PortefeuilleEindDatum']." ".$extraWhere['Portefeuille']." ORDER BY selectieveld2";
    $this->queries['Portefeuille']="SELECT Portefeuille FROM Portefeuilles ".$extraJoin['Portefeuille']." WHERE ".$extraWhere['PortefeuilleEindDatum']." ".$extraWhere['Portefeuille']." ORDER BY Portefeuille";
    $this->queries['ClientPortefeuille']="SELECT Client,Portefeuille FROM Portefeuilles ".$extraJoin['Portefeuille']." WHERE ".$extraWhere['PortefeuilleEindDatum']." ".$extraWhere['Portefeuille']." ORDER BY Client,Portefeuille";
    $this->queries['Depotbank']="SELECT DISTINCT(Depotbank) FROM Portefeuilles ".$extraJoin['Portefeuille']." WHERE ".$extraWhere['PortefeuilleEindDatum']." ".$extraWhere['Portefeuille']."  ORDER BY Depotbank";
    $this->queries['Risicoklasse']="SELECT DISTINCT(Risicoklasse) AS Risicoklasse FROM Portefeuilles ".$extraJoin['Portefeuille']." WHERE 1 ".$extraWhere['Portefeuille']." ORDER BY Risicoklasse";
    $this->queries['ModelPortefeuille']="SELECT DISTINCT(ModelPortefeuille) AS ModelPortefeuille FROM Portefeuilles ".$extraJoin['Portefeuille']." WHERE 1 ".$extraWhere['Portefeuille']." ORDER BY ModelPortefeuille";
    $this->queries['AFMprofiel']="SELECT DISTINCT(AFMprofiel) AS AFMprofiel FROM Portefeuilles ".$extraJoin['Portefeuille']." WHERE 1 ".$extraWhere['Portefeuille']." ORDER BY AFMprofiel";
    $this->queries['SoortOvereenkomst']="SELECT DISTINCT(SoortOvereenkomst) AS SoortOvereenkomst  FROM Portefeuilles ".$extraJoin['Portefeuille']." WHERE 1 ".$extraWhere['Portefeuille']." ORDER BY SoortOvereenkomst";
    $this->queries['Remisier']="SELECT DISTINCT(Remisier) AS Remisier FROM Portefeuilles ".$extraJoin['Portefeuille']." WHERE 1 ".$extraWhere['Portefeuille']." ORDER BY Remisier";
    $this->queries['FondsenActief']="SELECT Fonds, Omschrijving FROM Fondsen WHERE (Fondsen.EindDatum  >=  NOW() OR Fondsen.EindDatum = '0000-00-00')  ORDER BY Omschrijving";
    $this->queries['FondsenAll']="SELECT Fonds, Omschrijving FROM Fondsen ORDER BY Omschrijving";
    $this->queries['FondsenKeyActief']="SELECT Fonds FROM Fondsen WHERE (Fondsen.EindDatum  >=  NOW() OR Fondsen.EindDatum = '0000-00-00') ORDER BY Fonds";
    $this->queries['FondsenKeyActiefVKM']="SELECT Fonds FROM Fondsen WHERE (Fondsen.EindDatum  >=  NOW() OR Fondsen.EindDatum = '0000-00-00') AND Fondsen.VKM = 1 ORDER BY Fonds";
    $this->queries['FondsenKeyAll']="SELECT Fonds FROM Fondsen ORDER BY Fonds";
    $this->queries['PortefeuilleClusters']="SELECT trim(cluster) FROM ( (SELECT ' alles' as cluster ) UNION (SELECT cluster FROM portefeuilleClusters ".$extraJoin['PortefeuilleClusters'] ." ORDER BY cluster)  order by cluster ) as clusters";
    $this->queries['Ordernummer']="SELECT id FROM OrdersV2 ORDER BY id ";
    $this->queries['Ordersoort']="SELECT DISTINCT(Ordersoort) as Ordersoort FROM OrdersV2 ORDER BY Ordersoort ";
    $this->queries['Orderstatus']="SELECT DISTINCT(Orderstatus) as Orderstatus FROM OrdersV2 ORDER BY Orderstatus ";
       
  }

  function getQuery($field)
  {
    $query="SELECT 'geen query gedefineerd' ";
    if($this->queries[$field]) {
      $query=$this->queries[$field];
    }
    return $query;
  }
  function getData($field)
  {
    $this->DB=new DB();
    $this->DB->SQL($this->getQuery($field));
    $this->DB->Query();
    $waarden=array();
    if($field=='PortefeuilleClusters' && $this->DB->records()==1)
      $waarden=array();
    while($data=$this->DB->nextRecord('num'))
    {
      if(count($data) > 1)
      {
        $waarden[$data[0]]=$data[1];
        $this->keyed=true;
      }
      else
      {
        $waarden[]=$data[0];
        $this->keyed=false;
      }
    }
    return $waarden;
  }

  function getOptions($field)
  {
    $html='';
    $data=$this->getData($field);
    foreach ($data as  $key=>$value)
    {
      if($this->keyed)
        $html .='<option value="'.$key.'">'.$value.'</option>';
      else
        $html .='<option value="'.$value.'">'.$value.'</option>';
    }
    return $html;
  }

function createSelectBlok($option,$options,$selection,$title='',$keyValue=false)
{
  if($_SESSION['selectieMethode']=='vink')
  { 
    return $this->createSelectBlokOpenSluiten($option,$options,$selection,$title);
  }
  $maxItems=15;
  $optionA=$options[0];
  if($keyValue==true)
    $optionB=end(array_keys($options));
  else
    $optionB=$options[count($options)-1];
  $optionsHtmlA='';
  $optionsHtmlB='';

  if($title=='')
    $title=$option;

  if($selection[$option.'Van'])
    $optionA=$selection[$option.'Van'];

  if($selection[$option.'Tm'])
    $optionB=$selection[$option.'Tm'];

  if(count($options) <= $maxItems)
  {
    foreach ($options as $index=>$value)
    {
      if($keyValue==false)
        $key=$value;
      else
        $key=$index;

      if($optionA==$key)
        $selected='selected';
      else
        $selected='';
      $optionsHtmlA.= "<option value=\"$key\" $selected>$value</option>";

      if($optionB==$key)
        $selected='selected';
      else
        $selected='';
      $optionsHtmlB.= "<option value=\"$key\" $selected>$value</option>";

    }
    $selectHtmlA='<select class="form-control" name="'.$option.'Van" style="width:200px">'.$optionsHtmlA.'</select>';
    $selectHtmlB='<select class="form-control" name="'.$option.'Tm" style="width:200px" >'.$optionsHtmlB.'</select>';
  }
  else
  {
    $optionA=$options[0];
    $optionB=$options[count($options)-1];

    if($title=='')
      $title=$option;

    if($selection[$option.'Van'] && $optionA <> $selection[$option.'Van'])
      $extraptionA ='<option value="'.$selection[$option.'Van'].'" selected>'.$selection[$option.'Van'].'</option>';
    if($selection[$option.'Tm'] && $optionB <> $selection[$option.'Tm'])
      $extraptionB='<option value="'.$selection[$option.'Tm'].'" selected>'.$selection[$option.'Tm'].'</option>';
    $selectHtmlA='<select  class="form-control" name="'.$option.'Van" style="width:200px" onfocus="javascript:loadOptions(\''.$option.'\');"><option value="'.$optionA.'">'.$optionA.'</option>'.$extraptionA.'</select>';
    $selectHtmlB='<select  class="form-control" name="'.$option.'Tm" style="width:200px" onfocus="javascript:loadOptions(\''.$option.'\');">'.$extraptionB.'<option value="'.$optionB.'">'.$optionB.'</option></select>';
  }
  $html='
<div class="formblock roundInputBlock">
  <div class="formlinks"><strong> ' . ucfirst(vt($title)) . '</strong></div><br />
  
  <div class="formrechts" id="div_' . $option . 'Van">
    ' . $selectHtmlA . '
  </div>
  
  <div class="formrechts"  >
    &nbsp;&nbsp;'.vt("t/m").'&nbsp;&nbsp;
  </div>
  
  <div class="formrechts" id="div_' . $option . 'Tm">
    ' . $selectHtmlB . '
  </div>
</div>
';
return $html;
}

function createSelectBlokOpenSluiten($option,$options,$selection,$title='')
{
  $maxItems=15;
  $optionA=$options[0];
  $optionB=$options[count($options)-1];

  if($title=='')
    $title=$option;

  if($selection[$option.'Van'])
    $optionA=$selection[$option.'Van'];

  if($selection[$option.'Tm'])
    $optionB=$selection[$option.'Tm'];

  if(count($options) <= $maxItems)
  {
    foreach ($options as $index=>$value)
    {
      if($optionA==$value)
        $selected='selected';
      else
        $selected='';
      $optionsHtmlA.= "<option value=\"$value\" $selected>$value</option>";

      if($optionB==$value)
        $selected='selected';
      else
        $selected='';
      $optionsHtmlB.= "<option value=\"$value\" $selected>$value</option>";
    }
    $selectHtmlA='<select name="'.$option.'Van" style="width:200px">'.$optionsHtmlA.'</select>';
    $selectHtmlB='<select name="'.$option.'Tm" style="width:200px" >'.$optionsHtmlB.'</select>';
  }
  else
  {
    $optionA=$options[0];
    $optionB=$options[count($options)-1];

    if($title=='')
      $title=$option;

    if($selection[$option.'Van'] && $optionA <> $selection[$option.'Van'])
      $extraptionA ='<option value="'.$selection[$option.'Van'].'" selected>'.$selection[$option.'Van'].'</option>';
    if($selection[$option.'Tm'] && $optionB <> $selection[$option.'Tm'])
      $extraptionB='<option value="'.$selection[$option.'Tm'].'" selected>'.$selection[$option.'Tm'].'</option>';
    $selectHtmlA='<select name="'.$option.'Van" style="width:200px" onfocus="javascript:loadOptions(\''.$option.'\');"><option value="'.$optionA.'">'.$optionA.'</option>'.$extraptionA.'</select>';
    $selectHtmlB='<select name="'.$option.'Tm" style="width:200px" onfocus="javascript:loadOptions(\''.$option.'\');">'.$extraptionB.'<option value="'.$optionB.'">'.$optionB.'</option></select>';
  }
  $html='
<div class="formblock">
<div class="formlinks"><a href="#" onclick="$(\'#div_'.$option.'Van\').toggle();$(\'#div_'.$option.'TmBlock\').toggle()"> Van '.$option.' </a></div>
<div class="formrechts" id="div_'.$option.'Van" style="display: none" >
'.$selectHtmlA.'
</div>
</div>
<div class="formblock" style="display: none" id="div_'.$option.'TmBlock" >
<div class="formlinks">  T/m '.$option.' </div>
<div class="formrechts" id="div_'.$option.'Tm" >
'.$selectHtmlB.'
</div>
</div>
';
return $html;
}

function createCheckBlok($option,$values,$selection,$title='')
{
  if($title=='')
    $title=$option;
  $html = '<div class="formblock">
  <div class="formlinks"> <a href="#" onclick="$(\'#opties_'.$option.'\').toggle()"><button onclick="return false;">+</button> '.vt($title).'</a>  </div>
  <div class="formrechts" style="display: none" id="opties_'.$option.'"> <button onclick="changeCheck(\''.$option.'\');return false;"><sub>'.vt("selectie omkeren").'</sub></button> </br>';
  foreach ($values as $value)
  {
    if($value=='')
      $value='Leeg';

    if($value <> '')
    {

      if(count($selection[$option]) > 0)
      {
        if ($selection[$option][$value] == 1)
        {
          $checked = "checked";
        }
        else
        {
          $checked = '';
        }
      }
      else
      {
        $checked = 'checked';
      }

      $html .=  '<input type="hidden" name="'.$option.'['.$value.']" value="0">'."\n" ;
      $html .=  '<input type="checkbox" name="'.$option.'['.$value.']" '.$checked.' value="1">'.$value."<br />\n" ;
    }
  }
  $html .='</div></div>';
  return $html;
}

function createEnkelvoudigeSelctie($data,$selectie)
{
  $portfeuilleOptions='';
  $portfeuilleOptions2='';
  $html='';
  foreach ($data as $portefeuile=>$pdata)
  {
    if(in_array($portefeuile,$selectie['selectedFields']))
      $portfeuilleOptions2 .= "<option value=\"".$pdata['Portefeuille']."\" >".$pdata['Client']. " - ".$pdata['Portefeuille']. "</option>\n";
    else
      $portfeuilleOptions .= "<option value=\"".$pdata['Portefeuille']."\" >".$pdata['Client']. " - ".$pdata['Portefeuille']. "</option>\n";
  }

  $html .='
<script language="Javascript">
function moveItem(from,to){
	var tmp_text = new Array();
	var tmp_value = new Array();
 	for(var i=0; i < from.options.length; i++) {
 		if(from.options[i].selected)
 		{
			var blnInList = false;
			for(j=0; j < to.options.length; j++)
			{
 				if(to.options[j].value == from.options[i].value)
				{
 					//alert("already in list");
 					blnInList = true;
 					break;
 				}
			}
			if(!blnInList)
 			{
				to.options.length++;
				to.options[to.options.length-1].text = from.options[i].text;
				to.options[to.options.length-1].value = from.options[i].value;
			}
 		}
		else
		{
			tmp_text.length++;
			tmp_value.length++;
			tmp_text[tmp_text.length-1] = from.options[i].text;
			tmp_value[tmp_text.length-1] = from.options[i].value;

		}
 	}
 	from.options.length = 0;
 	for(var i=0; i < tmp_text.length; i++) {
 		from.options.length++;
		from.options[from.options.length-1].text = tmp_text[i];
		from.options[from.options.length-1].value = tmp_value[i];
 	}
 	from.selectedIndex = -1;
}

	function selectSelected()
	{
	  if(document.selectForm[\'inFields[]\'])
	  {
	  	var inFields  			= document.selectForm[\'inFields[]\'];
	  	var selectedFields 	= document.selectForm[\'selectedFields[]\'];
  		for(j=0; j < selectedFields.options.length; j++){selectedFields.options[j].selected = true;}
      //		for(j=0; j < inFields.options.length; j++){inFields.options[j].selected = true;}
	  }
	}

</script>

  <table border="0">
<tr>
  <td>
	  <select name="inFields[]" multiple size="16" style="margin-left: 5px; min-width : 200px">
		  '.$portfeuilleOptions.'
	  </select>
  </td>
  <td width="70" >
	  <a href="javascript:moveItem(document.selectForm[\'inFields[]\'],document.selectForm[\'selectedFields[]\']);">
		  <img src="images/16/pijl_rechts.png" width="16" height="16" border="0" alt="toevoegen" align="absmiddle">
	  </a>
	  <br><br>
	  <a href="javascript:moveItem(document.selectForm[\'selectedFields[]\'],document.selectForm[\'inFields[]\']);">
		  <img src="images/16/pijl_links.png" width="16" height="16" border="0" alt="verwijderen" align="absmiddle">
	  </a>
  </td>
  <td>
	  <select name="selectedFields[]" multiple size="16" style="min-width : 200px">
      '.$portfeuilleOptions2.'
	  </select>
  </td>
  <td width="70" >
	  <a href="javascript:moveOptionUp(document.selectForm[\'selectedFields[]\'])">
		  <img src="images/16/pijl_omhoog.png" width="16" height="16" border="0" alt="omhoog" align="absmiddle">
	  </a>
	  <br><br>
	  <a href="javascript:moveOptionDown(document.selectForm[\'selectedFields[]\'])">
		  <img src="images/16/pijl_omlaag.png" width="16" height="16" border="0" alt="omlaag" align="absmiddle">
	  </a>
  </td>
</tr>
</table>';
return $html;
}

function createKwartaalSelectie($selectie)
{
  $totdatum = getLaatsteValutadatum();

$jr = substr($totdatum,0,4);
$maand = substr($totdatum,5,2);
$kwartaal=(ceil($maand/3));
$jaarOpties .="<select name=\"jaar\" onchange=\"javascript:setDate()\">";
for($i=-3;$i<2;$i++)
{
  if($i==0)
    $jaarOpties .="<option value=\"".($jr+$i)."\" SELECTED>".($jr+$i)."</option>";
  else
    $jaarOpties .="<option value=\"".($jr+$i)."\">".($jr+$i)."</option>";
}
$jaarOpties .="</select>";


for($i=1;$i<5;$i++)
  $kwartaalOpties .="<input name=\"kwartaal\" type=\"radio\" value=\"$i\" onclick=\"javascript:setDate()\"> Q$i &nbsp;";

$html='
<script>
function setDate()
{
  var jaar=document.selectForm.jaar.value;
  for (var i=0; i < document.selectForm.kwartaal.length; i++)
   {
      if (document.selectForm.kwartaal[i].checked)
      {
        var rad_val = document.selectForm.kwartaal[i].value;
      }
   }
   if(rad_val==1)
   {
      datumVan = "01-01-"+jaar;
      datumTm  = "31-03-"+jaar;
   }
   else if(rad_val==2)
   {
      datumVan = "31-03-"+jaar;
      datumTm  = "30-06-"+jaar;
   }
   else if(rad_val==3)
   {
      datumVan = "30-06-"+jaar;
      datumTm  = "30-09-"+jaar;
   }
   else if(rad_val==4)
   {
      datumVan = "30-09-"+jaar;
      datumTm  = "31-12-"+jaar;
   }
   else
   {
      datumVan = "";
      datumTm  = "";
   }
   document.selectForm.datumVan.value=datumVan;
   document.selectForm.datumTm.value=datumTm;

}
</script>
<input type="hidden" name="datumVan" value="">
<input type="hidden" name="datumTm" value="">
  <div class="formblock">
<div class="formlinks"> Jaar </div>
<div class="formrechts">
'.$jaarOpties.'
</div>
</div>
<div class="formblock">
<div class="formlinks"> Kwartaal </div>
<div class="formrechts">
'.$kwartaalOpties.'
</div>
</div>';

  return $html;
}


  function createDatumSelectie($selectie)
  {
    $totdatum = getLaatsteValutadatum();

    $jr = substr($totdatum,0,4);
    if($selectie['datumVan'])
      $datumVan=$selectie['datumVan'];
    else
      $datumVan= date("d-m-Y",mktime(0,0,0,1,1,$jr));
    $kal = new DHTML_Calendar();
    $inp = array ('name' =>"datumVan",'value' =>$datumVan,'size'  => "11");
    $vanVeld= $kal->make_input_field("",$inp,"");

    $kal = new DHTML_Calendar();
    if($selectie['datumVan'])
      $datumTm=$selectie['datumTm'];
    else
      $datumTm= date("d-m-Y",db2jul($totdatum));
    $inp = array ('name' =>"datumTm",'value' =>$datumTm,'size'  => "11");
    $totVeld= $kal->make_input_field("",$inp,"");
    $html='';

    if(!isset($selectie['geenVan']))
      $html.='
  <div class="formblock" '.$selectie['divExtraVan'].' >
<div class="formlinks"> '.vt("Van datum").' </div>
<div class="formrechts">
'.$vanVeld.'
</div>';
    if(!isset($selectie['geenTot']))
      $html.='
</div>
<div class="formblock"  '.$selectie['divExtraTot'].'>
<div class="formlinks"> '.vt("T/m datum").' </div>
<div class="formrechts">
'.$totVeld.'
</div>
</div>';

    return $html;
  }

function getSelectJava()
{
  $html='

  function changeCheck(item)
	{
    var theForm = document.selectForm.elements, z = 0;
    for(z=0; z<theForm.length;z++)
    {
     if(theForm[z].type == "checkbox")
     { 
       var test=theForm[z].name;
       if(test.search(item)==0)
       {  
         if (theForm[z].checked == true)
         {
          theForm[z].checked = false;
         }
         else
         {
           theForm[z].checked = true;
         }
       }
     }
    }
  }

var ajax = new Array();
function loadOptions(option)
{
	var vanField=option+\'Van\';
	var totField=option+\'Tm\';
  if(document.selectForm.elements[vanField].length < 3)
  {
	  if(option.length>1)
	  {
	  	var index = ajax.length;
	  	ajax[index] = new sack();
	  	ajax[index].element = \'veld\';
	  	ajax[index].requestFile = \'lookups/ajaxLookup.php?module=backOfficeSelectieOpties&query=\'+option+ \'' . ($this->einddatumFilterVerwijderen===true?'&einddatumFilterVerwijderen=1':'') . '\' ;	// Specifying which file to get
		  ajax[index].onCompletion = function(){ setVelden(ajax[index].response,vanField,totField) };	// Specify function that will be executed after file has been found
		  ajax[index].onError = function(){ alert(\''.vt("Ophalen van").' \'+option+\' '.vt("uit de database mislukt").'.\') };
		  ajax[index].runAJAX();		// Execute AJAX function
	  }
  }
}
function AddName(p_FormName,p_SelectName,p_OptionText,p_OptionValue)
{
  document.forms[p_FormName].elements[p_SelectName].options[document.forms[p_FormName].elements[p_SelectName].length] = new Option(p_OptionText,p_OptionValue);
}

function setVelden(waarden,veldA,veldB)
{
  valueA=document.selectForm.elements[veldA].value;
  valueB=document.selectForm.elements[veldB].value;
  var elements = waarden.split(\'\t\n\');
  var div_van =\'<select name="\'+veldA+\'\" style="width:200px">\';
  var div_tot =\'<select name="\'+veldB+\'\"  style="width:200px">\';
  var selectedA=\'\';
  var selectedB=\'\';
  var item=\'\';
  var addedEmpty=false;
 	for(var i=0;i<elements.length;i++)
 	{
 	  item=elements[i];
    if(item != \'\' || addedEmpty==false)
 	  {
 	    if(item==valueA){selectedA="selected";}else{selectedA=""};
 	    if(item==valueB){selectedB="selected";}else{selectedB=""};
      div_van += \'<option value="\' + elements[i] + \'" \' + selectedA + \'>\' + elements[i] + \'</option>\';
 	    div_tot += \'<option value="\' + elements[i] + \'" \' + selectedB + \'>\' + elements[i] + \'</option>\';
      
      if(item == \'\')
        addedEmpty=true;
 	  }

 	}
 	div_van += "</select>";
 	div_tot += "</select>";

 	 document.getElementById("div_"+veldA).innerHTML=div_van;
 	 document.getElementById("div_"+veldB).innerHTML=div_tot;
}
';

  return $html;
}

function getFormSelectJava()
{
  $html='

var ajax = new Array();
function loadOptions(option,query)
{
  if(query==""){query=option;)}
  if(document.selectForm.elements[vanField].length < 3)
  {
	  if(option.length>1)
	  {
	  	var index = ajax.length;
	  	ajax[index] = new sack();
	  	ajax[index].element = \'veld\';
	  	ajax[index].requestFile = \'lookups/ajaxLookup.php?module=backOfficeSelectieOpties&query=\'+query;	// Specifying which file to get
		  ajax[index].onCompletion = function(){ setVeld(ajax[index].response,option) };	// Specify function that will be executed after file has been found
		  ajax[index].onError = function(){ alert(\'Ophalen van \'+option+\' uit de database mislukt.\') };
		  ajax[index].runAJAX();		// Execute AJAX function
	  }
  }
}
function AddName(p_FormName,p_SelectName,p_OptionText,p_OptionValue)
{
  document.forms[p_FormName].elements[p_SelectName].options[document.forms[p_FormName].elements[p_SelectName].length] = new Option(p_OptionText,p_OptionValue);
}

function setVelden(waarden,veld)
{
  valueA=document.selectForm.elements[veld].value;
  var elements = waarden.split(\'\t\n\');
  var div_a =\'<select name="\'+veldA+\'\" style="width:200px">\';
  var selectedA=\'\';
  var item=\'\';
 	for(var i=0;i<elements.length;i++)
 	{
 	  item=elements[i];
    if(item != \'\')
 	  {
 	    if(item==valueA){selectedA="selected";}else{selectedA=""};
      div_a += \'<option value="\' + elements[i] + \'" \' + selectedA + \'>\' + elements[i] + \'</option>\';
 	  }

 	}
 	div_a += "</select>";
  document.getElementById("div_"+option).innerHTML=div_a;
}
';

  return $html;
}

function getInternExternActive()
{
  global $USR;
  $cfg=new AE_config();
  $settingsIntern=$cfg->getData($USR.'_portefeuilleIntern');
  $settingsConsolidatie=$cfg->getData($USR.'_metConsolidatie');
  $settingsActive=$cfg->getData($USR.'_portefeuilleActive');
  $settingsMethode=$cfg->getData($USR.'_selectieMethode');

  if(!isset($_SESSION['portefeuilleIntern']))
    $_SESSION['portefeuilleIntern']=$settingsIntern;
  elseif($_SESSION['portefeuilleIntern'] <> $settingsIntern)
    $cfg->addItem($USR.'_portefeuilleIntern',$_SESSION['portefeuilleIntern']);

  if(!isset($_SESSION['metConsolidatie']))
    $_SESSION['metConsolidatie']=$settingsConsolidatie;
  elseif($_SESSION['metConsolidatie'] <> $settingsConsolidatie)
    $cfg->addItem($USR.'metConsolidatie',$_SESSION['metConsolidatie']);

  if(!isset($_SESSION['lastGET']['actief']))
    $_SESSION['lastGET']['actief'] = $settingsActive;//"inactief";
  elseif($_SESSION['lastGET']['actief'] <> $settingsActive)
    $cfg->addItem($USR.'_portefeuilleActive',$_SESSION['lastGET']['actief']);

  if(!isset($_SESSION['selectieMethode']))
    $_SESSION['selectieMethode']=$settingsMethode;
  elseif($_SESSION['selectieMethode'] <> $settingsMethode)
    $cfg->addItem($USR.'_selectieMethode',$_SESSION['selectieMethode']);
}



  function getInternExternHTML($script)
  {
    $externChecked='';
    $internChecked='';
    $allChecked='';
    if($_SESSION['portefeuilleIntern'] == "0" )
    	$externChecked = "checked";
    elseif($_SESSION['portefeuilleIntern'] == "1")
	    $internChecked = "checked";
    else
	    $allChecked = "checked";

    $html ='<br>
<input type="radio" name="portefeuilleIntern" id="portefeuilleIntern" value="0" '.$externChecked.' onClick="parent.frames[\'content\'].document.location = \''.$script.'?portefeuilleIntern=0\'">
<label for="actief" title="actief"> '.vt("Externe portefeuilles").'</label><br/>
<input type="radio" name="portefeuilleIntern" id="portefeuilleIntern" value="1" '.$internChecked.' onClick="parent.frames[\'content\'].document.location = \''.$script.'?portefeuilleIntern=1\'">
<label for="actief" title="actief"> '.vt("Interne portefeuilles").'</label><br/>
<input type="radio" name="portefeuilleIntern" id="alles" value="10" '.$allChecked.' onClick="parent.frames[\'content\'].location = \''.$script.'?portefeuilleIntern=10\'">
<label for="inactief" title="actief"> '.vt("Alle portefeuilles").'</label>';
    return $html;
  }

  function getConsolidatieHTML($script)
  {
    $consolidatieUit='';
    $consolidatieChecked='';
    $allChecked='';
    if($_SESSION['metConsolidatie'] == "0" )
      $consolidatieUit = "checked";
    elseif($_SESSION['metConsolidatie'] == "1")
      $consolidatieChecked = "checked";
    elseif($_SESSION['metConsolidatie'] == "10")
      $allChecked = "checked";
    else
      $consolidatieUit = "checked";

    $html ='<br>
<input type="radio" name="metConsolidatie" id="metConsolidatie" value="0" '.$consolidatieUit.' onClick="parent.frames[\'content\'].document.location = \''.$script.'?metConsolidatie=0\'">
<label for="actief" title="actief">'.vt("Zonder consolidatie").'</label><br/>
<input type="radio" name="metConsolidatie" id="metConsolidatie" value="1" '.$consolidatieChecked.' onClick="parent.frames[\'content\'].document.location = \''.$script.'?metConsolidatie=1\'">
<label for="actief" title="actief">'.vt("Alleen consolidatie").'</label><br/>
<input type="radio" name="metConsolidatie" id="alles" value="10" '.$allChecked.' onClick="parent.frames[\'content\'].location = \''.$script.'?metConsolidatie=10\'">
<label for="inactief" title="actief">'.vt("Met consolidatie").'</label>';
    return $html;
  }

  function getSelectieMethodeHTML($script)
  {
    if($_SESSION['selectieMethode'] == 'portefeuille')
   	{
	    $selectieAlles = '';
	    $selectiePortefeuille = 'checked';
	    $selectieVink = '';
	  }
	  elseif($_SESSION['selectieMethode'] == 'vink')
	  {
	    $selectieAlles = '';
	    $selectiePortefeuille = '';
	    $selectieVink = 'checked';
  	}
  	else
  	{
  	  $selectieAlles = 'checked';
  	  $selectiePortefeuille = '';
  	  $selectieVink = '';
  	}

    $html = "<b>Selectie methode</b><br><table>";
    $html .= "<tr><td><input type=\"radio\" name=\"selectieMethode\" id=\"selectieall\" value=\"alles\"        $selectieAlles        onClick=\"parent.frames['content'].location = '$script?selectieMethode=alles'\"></td><td style='font-size: 12px;'><label for=\"selectieall\" title=\"".vt("multiselectie")."\"> ".vt("multiselectie")."</label></td></tr>";
    $html .= "<tr><td><input type=\"radio\" name=\"selectieMethode\" id=\"selectieport\" value=\"portefeuille\" $selectiePortefeuille onClick=\"parent.frames['content'].location = '$script?selectieMethode=portefeuille'\"></td><td style='font-size: 12px;'>  <label for=\"selectieport\" title=\"".vt("enkelvoudige")."\"> ".vt("enkelvoudige selectie")." </label> </td></tr>";
    $html .= "<tr><td><input type=\"radio\" name=\"selectieMethode\" id=\"selectieport\" value=\"vink\" $selectieVink onClick=\"parent.frames['content'].location = '$script?selectieMethode=vink'\"></td><td style='font-size: 12px;'>  <label for=\"selectievink\" title=\"".vt("vink")."\"> ".vt("aangepaste selectie")." </label> </td></tr>";
    $html .= '</table>';

    return $html;
  }

  function getPortefeuilleInternJava()
  {
    return "if(parent.frames['submenu'].document.selectForm.portefeuilleIntern && document.selectForm.portefeuilleIntern)
 	{
 	  for(var i=0; i < parent.frames['submenu'].document.selectForm.portefeuilleIntern.length; i++)
 	  {
 	  	if(parent.frames['submenu'].document.selectForm.portefeuilleIntern[i].checked == true)
 	  	{
 	  		document.selectForm.portefeuilleIntern.value = parent.frames['submenu'].document.selectForm.portefeuilleIntern[i].value;
 	  	}
 	  }
 	}";
  }
  
  function getConsolidatieJava()
  {
    return "if(parent.frames['submenu'].document.selectForm.metConsolidatie && document.selectForm.metConsolidatie)
 	{
 	  for(var i=0; i < parent.frames['submenu'].document.selectForm.metConsolidatie.length; i++)
 	  {
 	  	if(parent.frames['submenu'].document.selectForm.metConsolidatie[i].checked == true)
 	  	{
 	  		document.selectForm.metConsolidatie.value = parent.frames['submenu'].document.selectForm.metConsolidatie[i].value;
 	  	}
 	  }
 	}";
  }
  
  function getJsPortefeuilleInternJava()
  {
    return "if(selectForm.portefeuilleIntern && document.selectForm.portefeuilleIntern)
 	{
 	  for(var i=0; i < selectForm.portefeuilleIntern.length; i++)
 	  {
 	  	if(selectForm.portefeuilleIntern[i].checked == true)
 	  	{
 	  		document.selectForm.portefeuilleIntern.value = selectForm.portefeuilleIntern[i].value;
 	  	}
 	  }
 	}";
  }
  
  function getJsConsolidatieJava()
  {
    return "if(selectForm.metConsolidatie && document.selectForm.metConsolidatie)
 	{
 	  for(var i=0; i < selectForm.metConsolidatie.length; i++)
 	  {
 	  	if(selectForm.metConsolidatie[i].checked == true)
 	  	{
 	  		document.selectForm.metConsolidatie.value = selectForm.metConsolidatie[i].value;
 	  	}
 	  }
 	}";
  }
  
  
  
  function setSelectConfig ( $options = array() ) {
    
    if ( isset ($options['labelClass']) ) {
      $this->labelClass = $options['labelClass'];
    }
    
    if ( isset ($options['buttonClass']) ) {
      $this->buttonClass = $options['buttonClass'];
    }
    
    if ( isset ($options['PHP_SELF']) ) {
      $this->PHP_SELF = $options['PHP_SELF'];
    }
    
  }
  
  
  function getHtmlActiveAllPortefeuille () {
    $actiefChecked = '';
    $eActiefChecked = '';
    $inactiefChecked = '';
    
    if( $_SESSION['lastGET']['actief'] == "inactief" ) {
      $inactiefChecked = "checked";
    } elseif( $_SESSION['lastGET']['actief'] == "eActief" ) {
      $eActiefChecked = "checked";
    } else {
      $actiefChecked = "checked";
    }
    
    return $this->AETemplate->parseBlockFromFile('rapportFrontoffice/active_all_portefeuille.html', array(
      'labelClass'              => $this->labelClass,
      'buttonClass'             => $this->buttonClass,
      'PHP_SELF'                => $this->PHP_SELF,
    
      'actiefChecked'           => $actiefChecked,
      'eActiefChecked'          => $eActiefChecked,
      'inactiefChecked'         => $inactiefChecked,
    
      'actiefCheckedClass'      => ($actiefChecked == 'checked' ? 'active':''),
      'eActiefCheckedClass'     => ($eActiefChecked == 'checked' ? 'active':''),
      'inactiefCheckedClass'    => ($inactiefChecked == 'checked' ? 'active':''),
    ));
  }
  
  function getHtmlInterneExternePortefeuille () {
    $externChecked  = '';
    $internChecked  = '';
    $allChecked     = '';
  
  
    if( $_SESSION['portefeuilleIntern'] == "0" ) {
      $externChecked = "checked";
    } elseif( $_SESSION['portefeuilleIntern'] == "1") {
      $internChecked = "checked";
    } else {
      $allChecked = "checked";
    }
  
    return $this->AETemplate->parseBlockFromFile('rapportFrontoffice/interne_externe_portefeuille.html', array(
      'labelClass'              => $this->labelClass,
      'buttonClass'             => $this->buttonClass,
      'PHP_SELF'                => $this->PHP_SELF,
      
      'externChecked'           => $externChecked,
      'internChecked'           => $internChecked,
      'allChecked'              => $allChecked,
    
      'externCheckedClass'      => ($externChecked == 'checked' ? 'active':''),
      'internCheckedClass'      => ($internChecked == 'checked' ? 'active':''),
      'allCheckedClass'         => ($allChecked == 'checked' ? 'active':''),
    ));
  }


  function getHtmlConsolidatie () {
    $consolidatieUit      = '';
    $consolidatieChecked  = '';
    $allChecked           = '';
    
    if( $_SESSION['metConsolidatie'] == "0" ) {
      $consolidatieUit = "checked";
    } elseif($_SESSION['metConsolidatie'] == "1" ) {
      $consolidatieChecked = "checked";
    } elseif( $_SESSION['metConsolidatie'] == "10" ) {
      $allChecked = "checked";
    } else {
      $consolidatieUit = "checked";
    }
  
    return $this->AETemplate->parseBlockFromFile('rapportFrontoffice/selectie_consolidatie.html', array(
      'labelClass'                => $this->labelClass,
      'buttonClass'               => $this->buttonClass,
      'PHP_SELF'                  => $this->PHP_SELF,
    
      'consolidatieUit'           => $consolidatieUit,
      'consolidatieChecked'       => $consolidatieChecked,
      'allChecked'                => $allChecked,
    
      'consolidatieUitClass'      => ($consolidatieUit == 'checked' ? 'active':''),
      'consolidatieCheckedClass'  => ($consolidatieChecked == 'checked' ? 'active':''),
      'allCheckedClass'           => ($allChecked == 'checked' ? 'active':''),
    ));
    
  }
  
  function getHtmlSelectieMethode ()
  {
    $selectieAlles          = '';
    $selectieVink           = '';
    $selectiePortefeuille   = '';
    
    if( $_SESSION['selectieMethode'] == 'portefeuille') {
      $selectiePortefeuille = 'checked';
    } elseif( $_SESSION['selectieMethode'] == 'vink' ) {
      $selectieVink = 'checked';
    } else {
      $selectieAlles = 'checked';
    }
  
    return $this->AETemplate->parseBlockFromFile('rapportFrontoffice/selectie_selectie_methode.html', array(
      'labelClass'                => $this->labelClass,
      'buttonClass'               => $this->buttonClass,
      'PHP_SELF'                  => $this->PHP_SELF,
    
      'selectieAlles'             => $selectieAlles,
      'selectieVink'              => $selectieVink,
      'selectiePortefeuille'      => $selectiePortefeuille,
    
      'selectieAllesClass'        => ($selectieAlles == 'checked' ? 'active':''),
      'selectieVinkClass'         => ($selectieVink == 'checked' ? 'active':''),
      'selectiePortefeuilleClass' => ($selectiePortefeuille == 'checked' ? 'active':''),
    ));
  }
  
  
  function getHtmlDatumSelectie  () {
    $totdatum = getLaatsteValutadatum();
  
    $jr = substr($totdatum,0,4);
    if($selectie['datumVan'])
      $datumVan=$selectie['datumVan'];
    else
      $datumVan= date("d-m-Y",mktime(0,0,0,1,1,$jr));
    $kal = new DHTML_Calendar();
    $inp = array ('name' =>"datumVan",'value' =>$datumVan,'size'  => "11");
    $vanVeld= $kal->make_input_field("",$inp,"");
  
    $kal = new DHTML_Calendar();
    if($selectie['datumVan'])
      $datumTm=$selectie['datumTm'];
    else
      $datumTm= date("d-m-Y",db2jul($totdatum));
    $inp = array ('name' =>"datumTm",'value' =>$datumTm,'size'  => "11");
    $totVeld= $kal->make_input_field("",$inp,"");
    $html='';
  
    if(!isset($selectie['geenVan']))
      $html.='
  <div class="formblock" '.$selectie['divExtraVan'].' >
<div class="formlinks"> Van datum </div>
<div class="formrechts">
'.$vanVeld.'
</div>';
    if(!isset($selectie['geenTot']))
      $html.='
</div>
<div class="formblock"  '.$selectie['divExtraTot'].'>
<div class="formlinks"> T/m datum </div>
<div class="formrechts">
'.$totVeld.'
</div>
</div>';
  
    return $html;
  }
  
  
  
  function letterToolbar () {
    $selected = '';
    if ( isset ($_SESSION['lastGET']['letter']) ) {
      $selected = $_SESSION['lastGET']['letter'];
    }
    
    if ( isset ($_GET['letter']) && ! empty ($_GET['letter']) ) {
      $selected = $_GET['letter'];
    }
    $letter = '';
    for ( $a=65; $a <= 90; $a++ ) {
      $letter .= '<a class="btn btn-hover btn-default ' . ( $selected == chr($a) ? 'active':'' ) . '" href="' . $this->PHP_SELF . '?letter=' . chr($a) . '" class="letterButton">' . chr($a) . '</a>';
    }
    
    return '
      <div class="btn-toolbar" role="toolbar" >
        <div class="btn-group mr-2" role="group" aria-label="First group">
          <a class="btn btn-hover btn-default ' . ( $selected == '0-9' ? 'active':'' ) . '" href="' . $this->PHP_SELF . '?letter=0-9" class="letterButton" > 0-9 </a>
          ' . $letter . '
          <a class="btn btn-hover btn-default ' . ( $selected == 'all' || empty($selected) ? 'active':'' ) . '" href="' . $this->PHP_SELF . '?letter=" class="letterButton">'.vt("Alles").'</a>
        </div>
      </div>
    ';
  }
  
}






