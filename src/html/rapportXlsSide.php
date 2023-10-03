<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2009/05/10 08:52:34 $
 		File Versie					: $Revision: 1.4 $
 		
 		$Log: rapportXlsSide.php,v $
 		Revision 1.4  2009/05/10 08:52:34  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2007/09/05 08:31:43  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2007/08/30 12:03:15  rvv
 		Portefeuille parameters aangepast
 		
 	
*/

$DB= new DB();

$invoerData = array();
$invoerData['alles']=array('alles'=>'Alles');
$invoerData['H-cat']=array('alles'=>'Alles');
$invoerData['cat']=array('alles'=>'Alles');
$invoerData['H-sec']=array('alles'=>'Alles');
$invoerData['sec']=array('alles'=>'Alles');
$invoerData['regio']=array('alles'=>'Alles');

$DB->SQL("SELECT Beleggingscategorien.Beleggingscategorie,Beleggingscategorien.Omschrijving 
FROM Beleggingscategorien, BeleggingscategoriePerFonds,CategorienPerHoofdcategorie
WHERE Beleggingscategorien.Beleggingscategorie =  CategorienPerHoofdcategorie.Hoofdcategorie
GROUP BY Beleggingscategorien.Beleggingscategorie
");
$DB->Query();
while($cat = $DB->NextRecord())
  $invoerData['H-cat'][$cat['Beleggingscategorie']]=$cat['Omschrijving'];

$DB->SQL("SELECT Beleggingscategorien.Beleggingscategorie, Beleggingscategorien.Omschrijving 
FROM Beleggingscategorien, BeleggingscategoriePerFonds
WHERE Beleggingscategorien.Beleggingscategorie =  BeleggingscategoriePerFonds.Beleggingscategorie
GROUP BY Beleggingscategorien.Beleggingscategorie
");
$DB->Query();
while($cat = $DB->NextRecord())
  $invoerData['cat'][$cat['Beleggingscategorie']]=$cat['Omschrijving'];

$DB->SQL("SELECT Beleggingssectoren.Beleggingssector, Beleggingssectoren.Omschrijving 
FROM Beleggingssectoren, BeleggingssectorPerFonds
WHERE Beleggingssectoren.Beleggingssector =  BeleggingssectorPerFonds.Beleggingssector
GROUP BY Beleggingssectoren.Beleggingssector
");
$DB->Query();
while($cat = $DB->NextRecord())
  $invoerData['sec'][$cat['Beleggingssector']]=$cat['Omschrijving'];  

$DB->SQL("SELECT Beleggingssectoren.Beleggingssector, Beleggingssectoren.Omschrijving 
FROM Beleggingssectoren, SectorenPerHoofdsector
WHERE Beleggingssectoren.Beleggingssector =  SectorenPerHoofdsector.Beleggingssector
GROUP BY Beleggingssectoren.Beleggingssector
");
$DB->Query();
while($cat = $DB->NextRecord())
  $invoerData['H-sec'][$cat['Beleggingssector']]=$cat['Omschrijving'];    
  
$DB->SQL("SELECT Regios.Regio, Regios.Omschrijving 
FROM Regios, BeleggingssectorPerFonds
WHERE BeleggingssectorPerFonds.Regio =  Regios.Regio
GROUP BY Regios.Regio
");
$DB->Query();
while($cat = $DB->NextRecord())
  $invoerData['regio'][$cat['Regio']]=$cat['Omschrijving'];  
?>

<script type="text/javascript">
	function fondsChange()
	{
	  var statusDisabled = false;
	  var statusBackground = '#FBFBFB';

	  if(document.selectForm.fonds.value != '---')
	  {
	    statusDisabled = true;
      statusBackground = '#CCCCCC';
      document.selectForm.newFonds.value = '';
      document.selectForm.newFondsISIN.value = '';
      document.selectForm.newFondsValutaCode.value = '';
      document.selectForm.newFondsEenheid.value = '';
	  }

	  document.selectForm.newFonds.disabled = statusDisabled;
    document.selectForm.newFonds.style.backgroundColor = statusBackground ;
	    
	  document.selectForm.newFondsISIN.disabled = statusDisabled;
	  document.selectForm.newFondsISIN.style.backgroundColor = statusBackground ;
	    
	  document.selectForm.newFondsValutaCode.disabled = statusDisabled;
	  document.selectForm.newFondsValutaCode.style.backgroundColor = statusBackground ;
	    
	  document.selectForm.newFondsEenheid.disabled = statusDisabled;
	  document.selectForm.newFondsEenheid.style.backgroundColor = statusBackground ;
	}	

	function generate()
	{
		document.selectForm.target = "generateFrame";
 	  document.selectForm.action.value = "generate";
 	  if(checkfield())
		document.selectForm.submit();
	}
	
	function add()
	{
	document.selectForm.target = "_self";  
 	document.selectForm.save.value = "0";
 	document.selectForm.action.value = "add";
	document.selectForm.submit();
	}
	
  function opslaan()
  {
	document.selectForm.target = "_self";  
 	document.selectForm.save.value = "0";
 	document.selectForm.action.value = "opslaan";
	document.selectForm.submit();
  }
  
  function checkfield()
	{

	  //check of velden gevuld
	  if(document.selectForm.rapport.value ==  'fonds__MutatievoorstelPortefeuille' && document.selectForm.fonds.value == '---')
	  {
      if (document.selectForm.newFonds.value == '' ||
          document.selectForm.newFondsISIN.value == '' ||
          document.selectForm.newFondsValutaCode.value == '' ||
          document.selectForm.newFondsEenheid.value == '' ||
          document.selectForm.newFondsKoers.value == '' ||
          document.selectForm.newFondsValutaKoers.value == '' )
          {     
	          alert('Niet alle vereiste velden zijn gevuld.');
	          return false; 
          }
	  }
	  return true;
	}
	

	function selectTab()
	{
	  			
	  if(document.selectForm.rapport.value ==  'fonds__Fondsoverzicht' || document.selectForm.rapport.value == 'fonds__Mutatievoorstel Fondsen')
		{
		  document.getElementById('Mutatievoorstel').style.visibility="visible";
		}
		else
		{
		  document.getElementById('Mutatievoorstel').style.visibility="hidden";
		}
	  
		if(document.selectForm.rapport.value == 'fonds__Mutatievoorstel Fondsen')
		{
			document.getElementById('sm').style.visibility="visible";
		}
		else 
		{
		  document.getElementById('sm').style.visibility="hidden";
		}

		if(document.selectForm.rapport.value ==  'fonds__Modelcontrole')
		{
		  document.getElementById('Modelcontrole').style.visibility="visible";
		}
		else
		{
		  document.getElementById('Modelcontrole').style.visibility="hidden";
		}
		
		if(document.selectForm.rapport.value ==  'fonds__MutatievoorstelPortefeuille')
		{
		  document.getElementById('MutatievoorstelPortefeuille').style.visibility="visible";
		  document.getElementById('Selectie').style.visibility="hidden";
		  
		}
		else
		{
		  document.getElementById('MutatievoorstelPortefeuille').style.visibility="hidden";
		  document.getElementById('Selectie').style.visibility="visible";
		}
		
/////////////////////////

		if(document.selectForm.rapport.value ==  'management__Managementoverzicht')
		{
		  document.getElementById('Managementinfo').style.visibility="visible";
		}
		else
		{
		  document.getElementById('Managementinfo').style.visibility="hidden";
		}		
		
		if(document.selectForm.rapport.value ==  'management__Risicometing')
		{
		  document.getElementById('Risicometing').style.visibility="visible";
		}
		else
		{
		  document.getElementById('Risicometing').style.visibility="hidden";
		}

		if(document.selectForm.rapport.value ==  'management__Risicometing' || document.selectForm.rapport.value ==  'management__Risicoanalyse')
		{
		  document.getElementById('Risicometing').style.visibility="visible";
		}
		else
		{
		  document.getElementById('Risicometing').style.visibility="hidden";
		}		
		
		if(document.selectForm.rapport.value ==  'management__PortefeuilleParameters')
		{
		  document.getElementById('portPar').style.visibility="visible";
		}
		else
		{
		  document.getElementById('portPar').style.visibility="hidden";
		}		
		
//////////////////////
		
		if(document.selectForm.rapport.value ==  'optietools__OptieExpiratieLijst')
		{
		  document.getElementById('ExpiratieDatum').style.visibility="visible";
		}
		else
		{
		  document.getElementById('ExpiratieDatum').style.visibility="hidden";
		}		
		if(document.selectForm.rapport.value ==  'optietools__OptieOngedektePositie')
		{
		  document.getElementById('ongedektePositie').style.visibility="visible";
		}
		else
		{
		  document.getElementById('ongedektePositie').style.visibility="hidden";
		}	
		if(document.selectForm.rapport.value ==  'optietools__OptieVrijePositie')
		{
		  document.getElementById('vrijePositie').style.visibility="visible";
		}
		else
		{
		  document.getElementById('vrijePositie').style.visibility="hidden";
		}
///////////////
		if(document.selectForm.rapport.value ==  '')
		{
		  document.getElementById('addButton').style.visibility="hidden";
		}
		else
		{
		  document.getElementById('addButton').style.visibility="visible";
		}			
			
	}

function loadField(field)
{
  inputBox = document.selectForm['invoer'];
  var Waarden = new Array(); 

<?
  while(list($categorie,$data)= each($invoerData))
  {
  echo "Waarden['$categorie']	= new Array(); \n";
    while(list($waarde,$omschrijving)= each($data))
    {
    echo "Waarden['$categorie']['$waarde']	= '".addslashes($omschrijving)."'; \n"; 
    }
  }
  reset($invoerData);
?>  

  for(var count = inputBox.options.length - 1; count >= 0; count--)
  {
    inputBox.options[count] = null;
  }

  if (field == 'alles1')
  {
    for (keyVar in Waarden ) 
    {
      LoadWaarde(Waarden[keyVar]);
    }
  }
  LoadWaarde(Waarden[field]);
}

function LoadWaarde(waarde)
{
  inputBox = document.selectForm['invoer'];
  for (keyVar in waarde ) 
  {
 		inputBox.options.length++;
		inputBox.options[inputBox.options.length-1].text = waarde[keyVar];
		inputBox.options[inputBox.options.length-1].value = keyVar; 
  }  
}
	
</script>


<table>
  <tr>
    <td>
    <fieldset>
      <legend accesskey="A"><u>A</u>lternatieve selectie</legend>
      <div class="formblock">
        <div> <input type="checkbox" name="overRuleDatum" value="1" checked>  Datum voor alle rapporten </div>
        <div> <input type="checkbox" name="overRuleSelectie" value="1" >  Selectie voor alle rapporten </div>
        <div> <input type="checkbox" value="1" name="geconsolideerd">	Geconsolideerde portefeuilles opnemen (managementrapportages) </div>
        <br>
      </div>
     </fieldset> 
    </td>
   </tr>
  <tr>
    <td>
<div id="Mutatievoorstel" style="visibility : hidden; position:absolute;">

<fieldset id="Selectie">
<legend accesskey="e">S<u>e</u>lectie</legend>

<?

if($_GET[actief] == "inactief" )
{
	$inactiefChecked = "checked";
	$actief = "inactief";
	$fondsActief = " ";
}
else
{
	$actiefChecked = "checked";
	$actief = "actief";
	$fondsActief = " AND (Fondsen.EindDatum  >=  NOW() OR Fondsen.EindDatum = '0000-00-00') ";
}

$DB = new DB();
$DB->SQL("SELECT Fonds, Omschrijving FROM Fondsen WHERE 1=1 ".$fondsActief." ORDER BY Omschrijving");
$DB->Query();
$aantal = $DB->records();
$t=0;
$Fondsen .= "<option value=\"---\" >---</option>\n";
while($gb = $DB->NextRecord())
{
	$Fondsen .= "<option value=\"".$gb[Fonds]."\" >".$gb[Omschrijving]."</option>\n";
}

$DB->SQL("SELECT DISTINCT Fondseenheid FROM Fondsen ");
$DB->Query();
while($gb = $DB->NextRecord())
{
  $fondseenheid .= "<option value=\"".$gb[Fondseenheid]."\" >".$gb[Fondseenheid]."</option>\n";
}

$DB->SQL("SELECT DISTINCT Valuta  FROM Fondsen");
$DB->Query();
while($gb = $DB->NextRecord())
{
   $valutaCode.= "<option value=\"".$gb[Valuta]."\" >".$gb[Valuta]."</option>\n";
}
?>
<div class="formblock">
<div class="formlinks"> Fonds </div>
<div class="formrechts">
<select name="fonds" id='fonds' style="width:200px" onchange="javascript:fondsChange();">
<?=$Fondsen?>
</select>

</div>
</div>

<div class="formblock">
<div class="formlinks"> Nieuw fonds naam </div>
<div class="formrechts">
<input type="text" name="newFonds" id="newFonds">
</div>
</div>

<div class="formblock">
<div class="formlinks"> Fonds ISIN code </div>
<div class="formrechts">
<input type="text" name="newFondsISIN" id="newFondsISIN">
</div>
</div>

<div class="formblock">
<div class="formlinks"> Fonds koers </div>
<div class="formrechts">
<input type="text" name="newFondsKoers" id="newFondsKoers" size="5">
</div>
</div>

<div class="formblock">
<div class="formlinks"> Fonds valuta koers </div>
<div class="formrechts">
<input type="text" name="newFondsValutaKoers" id="newFondsValutaKoers" size="5" >
</div>
</div>

<div class="formblock">
<div class="formlinks"> Fonds valuta code </div>
<div class="formrechts">

<select name="newFondsValutaCode" id="newFondsValutaCode" style="width:200px"">
<?=$valutaCode?>
</select>
</div>
</div>

<div class="formblock">
<div class="formlinks"> Fonds eenheid </div>
<div class="formrechts">
<select name="newFondsEenheid" id="newFondsEenheid" style="width:200px"">
<?=$fondseenheid?>
</select>
</div>
</div>

<div class="formblock">
<div class="formlinks"> &nbsp; </div>
<div class="formrechts">
<input type="radio" name="actief" id="actief" value="actief" <?=$actiefChecked?> onClick="document.location = '<?=$PHP_SELF?>?actief=actief&selectRapport=Fondsoverzicht'">
<label for="actief" title="actief"> Actieve fondsen  </label>

<input type="radio" name="actief" id="inactief" value="inactief" <?=$inactiefChecked?> onClick="document.location = '<?=$PHP_SELF?>?actief=inactief&selectRapport=Fondsoverzicht'">
<label for="inactief" title="actief"> Alle fondsen </label>
</div>
</div>


<div class="formblock">
<div class="formlinks"> Depotbanken </div>
<div class="formrechts">
<select name="depotbank" style="width:200px">
<option value="" selected>--</option>
<?=$depotbankOptions?>
</select>
</div>
</div>


<div class="formblock">
<div class="formlinks"> Afronding </div>
<div class="formrechts">
<input type="text" name="afronding" value="1" size="5">
</div>
</div>

<div class="formblock">
<div class="formlinks"> Berekeningswijze </div>
<div class="formrechts">
<select name="berekeningswijze">
	<option value="Totaal vermogen">Totaal vermogen</option>
	<option value="Totaal belegd vermogen">Totaal belegd vermogen</option>
	<option value="Belegd vermogen per beleggingscategorie">Belegd vermogen per beleggingscategorie</option>
</select>
</div>
</div>

<div class="formblock">
<div class="formlinks"> Opties weergeven </div>
<div class="formrechts">
	<input type="checkbox" value="1" name="optiesWeergeven"> 
</div>
</div>

</fieldset>


<div id="sm" style="visibility : hidden; position:absolute;">

<fieldset id="Smash" >
<legend accesskey="m">S<u>m</u>ash</legend>
<?

$DB = new DB();
$query = "SELECT ModelPortefeuilles.Portefeuille, 
				 ModelPortefeuilles.Omschrijving
		  FROM ModelPortefeuilles
		  LEFT JOIN Portefeuilles on Portefeuilles.Portefeuille = ModelPortefeuilles.Portefeuille ".$join ;

$DB->SQL($query);
$DB->Query();
$aantal = $DB->records();
$t=0;

while($gb = $DB->NextRecord())
{
	$t++;
	$Modelportefeuilles .= "<option value=\"".$gb['Portefeuille']."\" >".$gb['Omschrijving']."</option>\n";
}
?>

<div class="formblock">
	<input type="radio" name="type" value="Handmatig" checked> Handmatig &nbsp;	Percentage: <input type="text" name="percentage" value="0.0" size="4"><br><br>
	<input type="radio" name="type" value="Model"> Via model &nbsp;
	Modelportefeuille:
	<select name="modelportefeuille">
	<option value="">-</option>
<?
  if ($t <> 0)
    echo "<option value=\"Allemaal\">Allemaal</option>";
?>

	<?=$Modelportefeuilles?>
	</select>
</div>
</fieldset>
</div>

</div>

<div id="KostprijsMutatieverloop" style="visibility : hidden; position:absolute;">
<fieldset id="Selectie1">
<legend accesskey="e">S<u>e</u>lectie</legend>
<?
$DB = new DB();
$DB->SQL("SELECT Fonds, Omschrijving FROM Fondsen WHERE 1=1 ".$fondsActief." ORDER BY Omschrijving");
$DB->Query();
$aantal = $DB->records();
$t=0;
while($gb = $DB->NextRecord())
{
	$Fondsen .= "<option value=\"".$gb[Fonds]."\" >".$gb[Omschrijving]."</option>\n";
}
?>
<div class="formblock">
<div class="formlinks"> Fonds </div>
<div class="formrechts">
<select name="kostprijsFonds" style="width:200px">
<?=$Fondsen?>
</select>
</div>
</div>

<div class="formblock">
<div class="formlinks"> Vanaf beginpositie </div>
<div class="formrechts">
	<input type="checkbox" value="1" name="FondsBeginpositie" checked> 
</div>
</div>
<div class="formblock">
<div class="formlinks"> Opties opnemen </div>
<div class="formrechts">
	<input type="checkbox" value="1" name="FondsOpties" checked> 
</div>
</div>
<div class="formblock">
<div class="formlinks"> Kosten opnemen </div>
<div class="formrechts">
	<input type="checkbox" value="1" name="FondsKosten" checked> 
</div>
</div>

</fieldset>
</div>

<div id="Modelcontrole" style="visibility : hidden; position:absolute;">

<fieldset id="Modelportefeuille" >
<legend accesskey="m">M<u>o</u>delcontrole</legend>
<div class="formblock">
	Modelportefeuille
	<select name="modelcontrole_portefeuille">
	<option value="">-</option>
	<?=$Modelportefeuilles?>
	</select>
</div>

<div class="formblock">
	Afwijkingspercentage <input type="text" name="modelcontrole_percentage" value="0.0" size="4"> <br><br>
</div>


<div class="formblock">
	<u>Rapportsoort</u><br>
	<input type="radio" name="modelcontrole_rapport" value="percentage" checked> Modelcontrole in percentage<br>
	<input type="radio" name="modelcontrole_rapport" value="vastbedrag"> Mutatievoorstel Portefeuille<br>
	Vast bedrag: <input type="text" name="modelcontrole_vastbedrag" value="" size="4">
</div>

<div class="formblock">
	<u>Uitvoer soort</u><br>
	<input type="radio" name="modelcontrole_uitvoer" value="alles" checked> Alles<br>
	<input type="radio" name="modelcontrole_uitvoer" value="afwijkingen"> Alleen afwijkingen<br>
</div>

<div class="formblock">
	<u>Filter</u><br>
	<input type="radio" name="modelcontrole_filter" value="alles"> Alles<br>
	<input type="radio" name="modelcontrole_filter" value="gekoppeld" checked> Alleen gekoppelde depots<br>
</div>
</fieldset>

</div>

<div id="MutatievoorstelPortefeuille" style="visibility : hidden; position:absolute;">

<fieldset id="MutatievoorstelPortefeuille" >
<legend accesskey="m">M<u>u</u>tatievoorstel Portefeuille</legend>
<div class="formblock">
<div class="formlinks"> Modelportefeuille </div>
<div class="formrechts"> <select name="mutatieportefeuille_portefeuille"><option value="">-</option><?=$Modelportefeuilles?></select>  </div>
</div>

<div class="formblock">
<div class="formlinks"> Vast bedrag </div>
<div class="formrechts"> <input type="text" name="mutatieportefeuille_vastbedrag" value="" size="15"> </div>
</div>

<div class="formblock">
<div class="formlinks"> Naam </div>
<div class="formrechts"> <input type="text" name="mutatieportefeuille_customNaam" value="" size="25"> </div>
</div>

</fieldset>
</div>

<!--  -->


<fieldset id="Risicometing" style="visibility : hidden">
<legend accesskey="R"><u>R</u>isico</legend>

<div class="formblock">
<div class="formlinks"> Risico methode</div>
<div class="formrechts">
<select name="risicoMethode">
	<option value="perBeleggingscategorie">obv % per beleggingscategorie</option>
	<option value="perFonds">obv % per fonds</option>
</select>
</div>
</div>

</fieldset>

<fieldset id="Managementinfo" style="visibility : hidden">
<legend accesskey="A">M<u>a</u>nagementinfo</legend>

<div class="formblock">
<div class="formlinks"> </div>
<div class="formrechts">
	<input type="checkbox" value="1" name="orderbyVermogensbeheerder"> Subtotaal per Vermogensbeheerder
</div>
</div>

<div class="formblock">
<div class="formlinks"> </div>
<div class="formrechts">
	<input type="checkbox" value="1" name="orderbyAccountmanager">	Subtotaal per Accountmanager
</div>
</div>

</fieldset>

<?


$DB->SQL("SELECT 
Beleggingscategorien.Beleggingscategorie,
Beleggingscategorien.Omschrijving 
FROM Beleggingscategorien, BeleggingscategoriePerFonds
WHERE Beleggingscategorien.Beleggingscategorie =  BeleggingscategoriePerFonds.Beleggingscategorie
GROUP BY Beleggingscategorien.Beleggingscategorie
");
$DB->Query();
while($cat = $DB->NextRecord())
{
	$categorienOptions .= "<option value=\"".$cat['Beleggingscategorie']."\">".$cat['Omschrijving']."</option>\n";
}

$DB->SQL("SELECT 
Beleggingscategorien.Beleggingscategorie,
Beleggingscategorien.Omschrijving 
FROM Beleggingscategorien, BeleggingscategoriePerFonds,CategorienPerHoofdcategorie
WHERE Beleggingscategorien.Beleggingscategorie =  CategorienPerHoofdcategorie.Hoofdcategorie
GROUP BY Beleggingscategorien.Beleggingscategorie
");
$DB->Query();
while($cat = $DB->NextRecord())
{
	$categorienOptions .= "<option value=\"".$cat['Beleggingscategorie']."\">".$cat['Omschrijving']."</option>\n";
}
?>

<fieldset id="portPar" style="visibility : hidden ">
<legend accesskey="A"><u>P</u>ortefeuille parameters</legend>
<table>
<tr><td><b>Invoer </b></td><td><b>Uitvoer</b> </td> </tr>
<tr>
 <td width="120">
 <input type="radio" name="typeInvoer" value="alles" checked onclick="javascript:loadField('alles')"> Alles <br>
 <input type="radio" name="typeInvoer" value="H-cat" onclick="javascript:loadField('H-cat')"> Hoofd categorien <br>
 <input type="radio" name="typeInvoer" value="cat" onclick="javascript:loadField('cat')"> Categorien <br>
 <input type="radio" name="typeInvoer" value="H-sec"  onclick="javascript:loadField('H-sec')"> Hoofd sectoren <br>
 <input type="radio" name="typeInvoer" value="sec"  onclick="javascript:loadField('sec')"> Sectoren <br>
 <input type="radio" name="typeInvoer" value="regio"  onclick="javascript:loadField('regio')"> Regios <br><br>
 </td>
 <td width="210">
 <input type="radio" name="uitvoer"  value="alles" checked>Alles <br>
 <input type="radio" name="uitvoer"  value="categorien">Categorien <br>
 <input type="radio" name="uitvoer"  value="hoofdCategorien">Hoofd Categorien <br>
 <input type="radio" name="uitvoer"  value="hoofdSectoren">Hoofd Sectoren <br>
 <input type="radio" name="uitvoer"  value="sectoren">Sectoren <br>
 <input type="radio" name="uitvoer"  value="regios">Regio's <br>
 <input type="radio" name="uitvoer"  value="instrumenten">Instrumenten <br>
 </td>
</tr>
<tr><td>&nbsp;</td><td>&nbsp;</td> </tr>
</table>
<table>
<tr>
 <td width="210">
  Invoer waarde <br>
	<select name="invoer" style="width:200px">
	<option value="alles">Alles</option>
	
	</select>
 </td>
 <td width="210">
 Null velden weergeven <br> 
 <input type="checkbox" value="1" name="nullenWeergeven"> 
 </td>
 
</tr>
</table>
</fieldset>

<!-- -->



<fieldset id="ExpiratieDatum" style="visibility : hidden">
<legend accesskey="X">E<u>x</u>piratie Datum</legend>

<div class="formblock">
<div class="formlinks"> Expiratie Maand </div>
<div class="formrechts">

<select class="" type="select"  name="expiratieMaand" >
<option value=""> --- </option>
<?
$huidigeMaand= date(n);
for($i=1; $i<13; $i++)
{
  if ($huidigeMaand == $i)
    echo "<option value=\"$i\" SELECTED>".$__appvar["Maanden"][$i]." </option>";
  else 
    echo "<option value=\"$i\" >".$__appvar["Maanden"][$i]." </option>";	
}
?>
</select>
</div>
</div>

<div class="formblock">
<div class="formlinks"> Expiratie Jaar </div>
<div class="formrechts">
<select class="" type="select"  name="expiratieJaar" >
<option value=""> --- </option>
<?
$huidigeJaar = date(Y);
for ($i=-5;$i<10;$i++)
{
$expJaar = $huidigeJaar + $i;	
if ($i == 0)
  echo "<option value=\"".$expJaar."\" SELECTED>".$expJaar."</option>";
else
  echo "<option value=\"".$expJaar."\" >".$expJaar."</option>";
}
?>
</select>
</div>
</div>
</fieldset>

<!-- Fonds selectie -->
<div id="vrijePositie" style="visibility : hidden; position:absolute;">

<fieldset id="Selectie">
<legend accesskey="e">S<u>e</u>lectie</legend>


<?

$DB->SQL("SELECT Fonds, Omschrijving FROM Fondsen WHERE (Fondsen.EindDatum  >=  NOW() OR Fondsen.EindDatum = '0000-00-00')  ORDER BY Omschrijving");
$DB->Query();
$aantal = $DB->records();
$t=0;
while($gb = $DB->NextRecord())
{
	$Fondsen .= "<option value=\"".$gb[Fonds]."\" >".$gb[Omschrijving]."</option>\n";
}
?>
<div class="formblock">
<div class="formlinks"> Fonds </div>
<div class="formrechts">
<select name="fonds1" style="width:200px">
<?=$Fondsen?>
</select>

</div>
</div>

</fieldset>
<!-- end Fonds selectie -->

<!-- Fonds selectie -->
<div id="ongedektePositie" style="visibility : hidden; position:absolute;">

<fieldset id="Selectie">
<legend accesskey="e">S<u>e</u>lectie</legend>

<div class="formblock">
<div class="formlinks"> Tonen boven </div>
<div class="formrechts">
<input type="text" name="ongedektePositiePercentage" value="100" size="5"> % geschreven.

</div>
</div>

</fieldset>
</td>
</tr>
</table>

<!-- end Fonds selectie -->